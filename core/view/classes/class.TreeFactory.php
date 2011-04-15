<?php
/**
 * feeds the generis_tree.class.php with data nodes : generis_Node.class.php
 * method feedClass recursively feeds a node representing a class with instances properties and subclasses
 * @author patrick.plichart@tudor.lu
 *
 */
//require_once("generis_mappings.php");
//error_reporting(E_ALL);
if (!defined("HtmlViewLink")) {
	define("HtmlViewLink", "generis_UiControllerHtml.php", true);
}

require_once(dirname (__FILE__).'/../../api/generisApiPhp.php');


class TreeFactory  {

	private static $_instance ;
	private $tree; // a treeNodeInstance

	private $settings = array("properties" => true, "instances" => true);
	private $rootClass = "";
	private $sessionGeneris;
	public $debug = false; 

	
	public static function singleton(){
		if (empty(self::$_instance)) {
			self::$_instance = new TreeFactory();
		}
		return self::$_instance;
	}
	
	public function __clone(){
        trigger_error('You cannot clone a singleton', E_USER_ERROR);
    }

    public function setOptions($options){
    	$this->settings = $options;
    }
    
    public function setRootClass($root){
    	$this->rootClass = $root;
    }
    
    public function setSessions($session){
    	$this->sessionGeneris = $session;
    }
    
    
    
	/**
	 * Constructor
	 **/
	public function __construct(){
	
		if ($this->debug) 
		{
			echo "<b>toString Method </b></br>".$this->tree->__toString()."<br />";
			echo "<b>getJsonString Method </b></br>".$this->tree->getJsonString()."<br />";				
			// Ultra slow echo "<b>var_dump </b><br/>";var_dump($this->tree);echo "<br />";
			//get logger bla bla 
		}
	}

	public function getTree(){
		$this->feedTree($this->sessionGeneris,$this->settings,"",$this->rootClass);
		return $this->tree;
	}
	


	/**
	 * feeds refTreeNode, a treeNode object with instances of $uriClass, a (string) uri as children
	 * @param treeNode refTreeNode 
	 * @param uriClass
	 */
	private function feedInstances($sessionGeneris,$refTreeNode,$uriClass,$settings,$ns,$modelManager=false,$instancetype="i"){
		$instances = getInstances($sessionGeneris, array($uriClass), array($ns),$modelManager);
		if($instances["pDescription"] != ""){
			$instances_collection = $instances["pDescription"]; 
			foreach ($instances_collection as $description){

				$refTreeNode->children->add(new TreeNode(new generis_Node($description["InstanceLabel"],HtmlViewLink.'?do=show&param1='.urlencode($description["InstanceKey"]).'&type='.$instancetype,$instancetype)));
			}
		}
	}

	/**
	 * feeds $refTreeNode with generis_Node representing instances, properties and recursively subclasses of $uriClass
	 * @param sessionGeneris the session returned by the authenticate service
	 * @param treeNode refTreeNode the current node of the tree to be populated
	 * @param  uriClass the class  URI from generis
	 * @param ns deprecated
	 * @param modelmanager internal object of generis extracted from the session only once so that generis does not need to extract it from the session for each requests
	 **/
	private	function feedClasses($sessionGeneris, $refTreeNode, $uriClass,$settings,$ns,$modelManager=false,$classType="c"){

		$numClass=array($uriClass);
		$sous_classes = getsubClasses($sessionGeneris, array($uriClass),  array($ns),$modelManager);
		if($sous_classes["pDescription"] != ""){
			$sous_classes_collection = $sous_classes["pDescription"]; 
			foreach ($sous_classes_collection as $description){
				$refSubClassNode = $refTreeNode->children->add(new TreeNode(new generis_Node($description["ClassLabel"],HtmlViewLink.'?do=show&param1='.urlencode( $description["ClassKey"]).'&type='.$classType,$classType)));
				if ($settings["properties"]) {
					$rootClassUri = array(0 =>  $description["ClassKey"]);
					$pPropDescription = getProperties($sessionGeneris, $rootClassUri, 
						array(""),$modelManager);
					if($pPropDescription["pDescription"] != ""){
						$properties_collection = $pPropDescription["pDescription"]; //[0];
						foreach ($properties_collection as $propDescription){
							$refSubClassNode->children->add(new TreeNode(new generis_Node($propDescription["PropertyLabel"],HtmlViewLink.'?do=show&param1='.urlencode($propDescription["PropertyKey"]).'&type=p','p')));
						}
					}
				}
				$this->feedClasses($sessionGeneris, $refSubClassNode, $description["ClassKey"],$settings,$ns,$modelManager,$classType);
				if ($settings["instances"]) {
					if ($classType=="m") {$instancetype="im";} else {$instancetype="i";}
					$this->feedInstances($sessionGeneris, $refSubClassNode, $description["ClassKey"],$settings,$ns,$modelManager,$instancetype);
				}
			}
		}
	}


	/**
	 * feeds the generis_tree.class.php with data nodes : generis_Node.class
	 * populate first root with top classes, ie classes with no parent classes then
	 * call to method feedClass recursively feeds a node representing a class with instances properties and subclasses
	 * @param settings associative array with boolean for properties and instances, the tree will be fed accordingly
	 * @param rootClassUri a string containing the uri of the generis resource to use as root class , default is none , then all top classes will be computed
	 */
	private	function feedTree($sessionGeneris,$settings,$rootClassUri="")    {

		if (is_null($this->tree)) {
			$this->tree = new Tree();
		}

		//ppl slight optimization, modelmanager should be set as singleton
		$x = urldecode($sessionGeneris[0]);
		$modelManager = unserialize($x);
		$modelManager->bd->con=connection($modelManager->currentModuleDatabase);

		//deprecated
		$pRemoteDocKey = array(0 => "");

		if ($rootClassUri=="")
		{
			$topOClasses=array();
			$topOClasses[]="http://www.tao.lu/Ontologies/generis.rdf#generis_Ressource";
			if ($_SESSION["root"]=="0")
			{
				$topOClasses = getTopClasses($sessionGeneris,$pRemoteDocKey); 
				$topMetaClasses=getTopMetaClasses($sessionGeneris,$pRemoteDocKey);
				$topClasses= array("m"=>$topMetaClasses,"c"=>$topOClasses);
			}
			else
			{
				$key="n";
				$key =  array_search('http://www.tao.lu/datatypes/WidgetDefinitions.rdf#rangeConstraintTypes', $topOClasses);
				if ($key!="") unset($topOClasses[$key]);
				$key =  array_search('http://www.tao.lu/datatypes/WidgetDefinitions.rdf#WidgetClass', $topOClasses);
				if ($key!="") unset($topOClasses[$key]);
				$key =  array_search('http://www.w3.org/2000/01/rdf-schema#Resource', $topOClasses);
				if ($key!="") unset($topOClasses[$key]);
				$topClasses= array("c"=>$topOClasses);
			}
		}
		else
		{
			$topClasses= array("c"=>array($rootClassUri));
		}
		$refNodeTree = $this->tree->children->add(new TreeNode(new generis_Node('Model','http://www.w3.org/1999/02/22-rdf-syntax-ns#&type=root','m','http://www.w3.org/1999/02/22-rdf-syntax-ns#&type=root')));
		foreach ($topClasses as $classType=>$alltopclasses)
		{
			foreach ($alltopclasses as $key=>$uriTopClass)
			{
				$labelcomment = getLabelComment($sessionGeneris,$uriTopClass,$pRemoteDocKey,$modelManager);
				$refTopClassNode = $refNodeTree->children->add(new TreeNode(new generis_Node($labelcomment["label"],HtmlViewLink.'?do=show&param1='.urlencode($uriTopClass).'&type='.$classType,$classType,$uriTopClass)));
				if ($settings["properties"])  { 
					$pPropDescription = getProperties($sessionGeneris, array($uriTopClass), $pRemoteDocKey,$modelManager);
					if($pPropDescription["pDescription"] != ""){
						$properties_collection = $pPropDescription["pDescription"]; 
						foreach ($properties_collection as $propDescription){
							$subProperties = getsubProperties($sessionGeneris, array($propDescription["PropertyKey"]),$pRemoteDocKey,$modelManager);
							$subpropertiesjs="";
							if(sizeof($subProperties["pDescription"]) > 0){
								$subproperties_collection = $subProperties["pDescription"]; 
								//$subpropertiesjs=",";
								foreach ($subproperties_collection as $description){
									//Todo
									///$subpropertiesjs .= "['".getPrefix($description["PropertyKey"]).strtr($description["PropertyLabel"],array("'"=>"\'"))."', HtmlViewLink.'?do=show&param1=".urlencode$description["PropertyKey"])."&type=sp'],";
								}
							}
							$refTopClassNode->children->add(new TreeNode(new generis_Node($propDescription["PropertyLabel"],HtmlViewLink.'?do=show&param1='.urlencode($propDescription["PropertyKey"]).'&type=p','p',$propDescription["PropertyKey"])));
						}
					}
				}

				$this->feedClasses($sessionGeneris, $refTopClassNode, $uriTopClass,$settings,"",$modelManager,$classType);
				if ($settings["instances"])  {
					if ($classType=="m") {$instancetype="im";//Instance of metaclass (a class but icon is different)
					} else {$instancetype="i";}
					$this->feedInstances($sessionGeneris, $refTopClassNode, $uriTopClass,$settings,"",$modelManager,$instancetype);
				}
			}
		}
	}
}

if (!(isset($_SESSION)))
{
	session_start();
	if (is_null($_SESSION["session"])) die("You need a session with generis, connect first on a generis node");
}
new TreeFactory($_SESSION["session"]);

?>
