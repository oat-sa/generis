<?php
define("HtmlViewLink", "generis_UiControllerHtml.php", true);
require_once(dirname (__FILE__).'/../../api/generisApiPhp.php');
/**
 * This builder is used to build a json file giving all subclass, instances and properties of from an URI
 * It is a singleton to handle sessions. 
 * 
 * @author lionel.lecaque@tudor.lu
 *
 */
class TreeJsonBuilder  {
	private $json;
	private static $_instance;
	private $sessionsGeneris;
	
	/**
	 * Private Construtor
	 *
	 */
	public function __construct(){
		
	}    
	

	
	/**
	 * Singleton
	 *
	 * @return TreeJsonBuilder
	 */
	public static function singleton(){
			if (empty(self::$_instance)) {
				self::$_instance = new TreeJsonBuilder();
			}
			return self::$_instance;
	}
	
	public function __clone(){
        trigger_error('You cannot clone a singleton', E_USER_ERROR);
    }
	
	/**
	 *  Build Json List of element of Classes,Instances, Properties.
	 *
	 * @param List $collection
	 * @param String $uriKey
	 * @param String $labelKey
	 * @param String $type
	 * @param String $icon
	 * @param boolean $first
	 * @return boolean
	 */
	private function addResourceToJson($collection,$uriKey,$labelKey,$type,$icon,$first){
		
		foreach ($collection as $description){
				if($first){
					$this->json .= '{';
					$first = false;
				}
				else{
					$this->json = $this->json.',{';
				}
				$this->json .= '"link" : "'.HtmlViewLink.'?do=show&param1='.urlencode( $description[$uriKey]).'&type='.$type.'",';
				$this->json .= '"icon" : "'.$icon.'",';
				$this->json .= '"target" : "pane",';
				$this->json .= '"label" : "'.$this->getCleanLabel($description[$labelKey]).'"';
				$this->json .= '}';
		}
		return $first;
	}

	/**
	 * Provide session to builder
	 *
	 * @param String $session
	 */
	public function setSessions($session){
    	$this->sessionGeneris = $session;
    }
	
	/**
	 * Retrun Json String containning classes, instances and properties of the class : uriClass  
	 *
	 * @param String $uriClass
	 * @return String
	 */
	public function getJson($uriClass){
		$uri = urldecode($uriClass);
		$ns ="";
		$modelManager = false;
		$subClass = getsubClasses($this->sessionGeneris, array($uri),  array($ns),$modelManager);
		$instances = getInstances($this->sessionGeneris, array($uri), array($ns),$modelManager);
		$properties = getProperties($this->sessionGeneris, array($uri), array($ns),$modelManager);
		$first = true;
		$this->json ='{"ResultSet":{"Result":[';
		$first = $this->addResourceToJson($subClass["pDescription"],"ClassKey","ClassLabel","c","icon-class",true);
		$second = $this->addResourceToJson($instances["pDescription"],"InstanceKey","InstanceLabel","i","icon-instance",$first);
		$third = $this->addResourceToJson($properties["pDescription"],"PropertyKey","PropertyLabel","p","icon-property",$second);
				
		$this->json .= ']}}';
		return $this->json;
	}
	
	private function getCleanLabel($string){
		$returnValue = str_replace("\r"," ",$string);
		$returnValue = str_replace("\n"," ",$returnValue);
		return trim(strip_tags($returnValue));
		
	}
	
	public function __toString(){
		return json_encode();
	}
}
?>