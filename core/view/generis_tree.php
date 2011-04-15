<?php
/**
* This class displays the tree using javascript
* @author patrick
* @package usergui
*/
error_reporting(E_ALL);
   


class TAOTree
{
	function TAOTree()
	{
	}
	function microtime_float() 
	{ 
    list($usec, $sec) = explode(" ", microtime()); 
    return ((float)$usec + (float)$sec); 
	} 
	function getFilter()
	{
		
		switch ($_SESSION["filter"]) {
			case "0":{$filter = array("instances"=>false,"properties"=>false);break;}
			case "1":{$filter = array("instances"=>false,"properties"=>true);break;}
			case "2":{$filter = array("instances"=>true,"properties"=>true);break;}
			default:$filter = array("instances"=>true,"properties"=>true);
		}
		
		if ($_SESSION["bd"]=="menfpSubjects") {$_SESSION["filter"]="1";$filter = array("instances"=>false,"properties"=>true);}
		return $filter;
	}

	function getClassPath()
	{
		
	
if (!(isset($_SESSION["lastly_shown_if_refresh"])))  {$_SESSION["lastly_shown_if_refresh"]="http://www.tao.lu/Ontologies/TAO.rdf#TAOObject";
} 
		
		$path = calltoKernel('GetClassPath',array($_SESSION["session"],$_SESSION["lastly_shown_if_refresh"]));
		//print_r($path);
					//print_r($_SESSION);
					$in="new Array(";
					if (sizeOf($path)>0)
						{
						foreach	($path as $key=>$val) {$in.='"'.urlencode($val).'",';}
						$in=substr($in,0,strlen($in)-1);
						}
					else
						{$in.='"'.urlencode("http://www.tao.lu/Ontologies/generis.rdf#generis_ressource").'"';}
					$in.=')';

					return $in;
	}
	/*
	@param boolean $forselection returns a tree with only classes (for selection purpose as domain for properties)
	@param boolean $external returns a tree with external data from module specified in $_SESSION["idsub"]
	@param boolean $replacecontentof returns a tree with only instances (copy item function replace content of this instance with selected instance)
	*/
	function getOutput($forselection,$external="deprecated param",$replacecontentof="",$baseClass="",$filter="")
	{	
		$output='';
		
		if (!(isset($_SESSION))) {session_start();}
		
		if (!(isset($_SESSION["session"]))) {return "";}
		
		if ((calltoKernel('isKnownModel',array($_SESSION["session"],$baseClass,array("")))))
				{
					
				}
				else //unknown model
				
				{	
				
				$urimodel = substr($baseClass,0,strpos($baseClass,"#"));
				$file = str_replace("www.tao.lu/Ontologies/",$_SERVER["HTTP_HOST"]."/generis/Ontologies/",$urimodel);
				
				//$file =$urimodel;
				//echo dirname($_SERVER['PHP_SELF']);
				//echo $file;
				
				$dlmodel = calltoKernel('importrdfs',array($_SESSION["session"], 
				$urimodel,$file));
				

				$_SESSION["session"]=$dlmodel["pSession"];
				
				$idsub = calltoKernel('getSubscribeesurl',array($_SESSION["session"],array($baseClass),""));
				
				
				
				//getSubscriptions("http://www.tao.lu/Ontologies/TAOResult.rdf#");
				//$urimodel
				//$idsub = array("17");
				
				foreach ($idsub as $key => $val)
					{
					
					$result = calltoKernel('getRDFfromaremotemodule',array($_SESSION["session"],array($_SESSION["datalg"]), array($val[0]),false,"1"));
					
					if (!(is_string($result))) {$_SESSION["session"]=$result["pSession"];}
					
					else
						{
							$output.="<script language=\"JavaScript\">window.alert('At least one module has not been reached (Bad login/password/url)');</script>";	
						}
						
					}
				
				}
		
		
		$output="";$setcheckbox="";
		if (isset($_SESSION["ok"])) 
		{
				
				$target="";
				
					if (isset($_SESSION["datalg"])) {$lg=$_SESSION["datalg"];} else {$lg="FR";}
					
					$init = $this->microtime_float();
	

						if (!($forselection))
						{
						
						if ($replacecontentof=="")
							{
							$start = $this->microtime_float();
							set_time_limit(200);
							$tree= calltoKernel('getHTMLTree',array($_SESSION["session"],$this->getFilter(),""));
							
							
							$theened = $this->microtime_float();
							echo "<span class=debugmsg><br>Tree generation :<br>";
							echo $theened-$start;
							
							$treeoutput="[".str_replace("\\\\","\\",$tree["pXMLTree"][0])."]";
							$treeoutput=str_replace("\r","",$treeoutput);
							$treeoutput=str_replace("\n","",$treeoutput);
							$treeoutput=str_replace("\t","",$treeoutput);
							$option="";
							
							}
						else 
							{
							/*	$tree=calltoKernel('getHTMLTree',array($_SESSION["session"],array("instances"=>true,"properties"=>true),""));
							$treeoutput="[".$tree["pXMLTree"][0]."]";
							
							$treeoutput = str_replace("show=i","show=i&replacecontentof=".$replacecontentof."&byContentof=i",$treeoutput);
							$treeoutput = str_replace("generis_UiControllerHtml.php","TAOreplacecontentofItem.php",$treeoutput);
							$target="notarget";
							*/
							}
						}
					else
						{	
						
							echo "<span class=debugmsg>";	$tree=calltoKernel('getHTMLTree',array($_SESSION["session"],array("instances"=>true,"properties"=>true),"",false,$baseClass));
							$treeoutput="[".str_replace("\\\\","\\",$tree["pXMLTree"][0])."]";
							$treeoutput=str_replace("\r","",$treeoutput);
							$treeoutput=str_replace("\n","",$treeoutput);
							$treeoutput=str_replace("\t","",$treeoutput);
							$setcheckbox="1";
							
						}
					$end = $this->microtime_float();
					echo "<br>Total server time :<br>";
					echo $end-$init;
			    	echo "</span class=debugmsg>";
					$in = $this->getClassPath();
					//echo "Génération Arbre :".microtime()."<BR>";
					$indexes=$_SESSION["session"];
					
					
					
				
				
				
				
		}
		$output.='
					<script language="JavaScript">
						var toOpen='.$in.';
						var target="'.$target.'";
						var setcheckbox="'.$setcheckbox.'";
						new tree ('.$treeoutput.', TREE_TPL);
					</script>';
		

		return $output;
	}
	   
	 


}
?>