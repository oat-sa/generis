<?php
/**
 * returns a javascript array structure describing all instances of class $v_key or a subGraph structure described in xml if $asGraphXML is set to true,  related to $ns set of knowledge specified (may be remote knowledge)
 *@param TAO:session $pSession returned by authenticate service
 *@access private
 *@param Array([0] => String) $v_key class returned instances are typeOf 
 *@param Array([0] => String) $pType deprecated
 *@param String $ns is optional (if none use empty sting). It defines the namespace, set of data to use.  This namespace is got using service "getrdffromaremotemodule" 
 *@param Boolean $asGraphXML if true returns a javascript structure else a graph structure in graphml
 *@return string
 */
require_once("generis_mappings.php");
function getOverDiv($label,$comment)
{		

	$label=trim($label);
	$comment=trim($comment);
	$label=str_replace('"','',$label);
	$comment=str_replace('"','',$comment);


	return "<span onmouseover=\"return overlib('<div class=opaque><div class=calprio5>".$label."</div><div class=box-data>".$comment."</div></div>',FULLHTML);\" onmouseout=\"return nd();\">".$label."</span>";
}
function getXMLinstances($pSession,$v_key,$pType,$ns,$modelManager=false,$asGraphXML=false,$instancetype="i"){

	$ret_xmlTree = "";
	$pRemoteDocKey = array(0 => $ns);
	$instances = getInstances($pSession, array($v_key), $pRemoteDocKey,$modelManager);
	if($instances["pDescription"] != ""){
		$instances_collection = $instances["pDescription"]; //[0];
		foreach ($instances_collection as $description){
			$v_s_key = $description["InstanceKey"];
			$v_s_label = $description["InstanceLabel"];
			$v_s_comment = $description["InstanceComment"];



			if (!($asGraphXML))
			{

				$v_s_label=getOverDiv($v_s_label,$v_s_comment);
				$ret_xmlTree .= "['".getPrefix($v_s_key).strtr($v_s_label,array("'"=>"\'","<br />" => " "))."', 'generis_UiControllerHtml.php?do=show&param1=".urlencode($v_s_key)."&type=".$instancetype."'],";
			}
			else
			{
				$ret_xmlTree .= '
					<node name="'.$v_s_key.'">
					<label>'.strtr($v_s_label,array("'"=>"'")).'</label>
					<dataref>
					<ref xlink:show="new" xlink:href="http://'.$_SERVER["HTTP_HOST"].'/core/view/generis_UiControllerHtml.php?do=show&amp;param1='.substr($v_s_key,1).'"/>
					</dataref>
					<fill xlink:href="../../core/view/icons/Instance.gif"/>

					</node>
					<edge source="'.$v_key.'" target="'.$v_s_key.'" linestyle="solid" colour="black">
					</edge>';


				$iDescription=getInstanceDescription($pSession,$v_s_key,array(""),$modelManager);

				$iDescription=$iDescription["pDescription"]["PropertiesValues"];

				foreach ($iDescription as $kc=>$vc)
				{


					$ret_xmlTree .='<edge source="'.$v_s_key.'" target="'.$vc["PropertyValue"].' linestyle="dash-dotted" colour="lightGray">
						</edge>';



				}
			}

			/*Recursive definition of class and ressource */

			/*
			switch ($v_s_key){
				case "http://www.w3.org/2000/01/rdf-schema#Class":break;
				case "http://www.w3.org/2000/01/rdf-schema#Resource":break;
				default:$ret_xmlTree.= getXMLclasses($pSession,$v_s_key,$pType,$ns,$modelManager=false,$asGraphXML=false,$classtype="c");
				$ret_xmlTree.="],";
				echo "<br>
				".$ret_xmlTree."<br>
				";
				break;

				}
			 */

		}
	}
	return($ret_xmlTree);
}

/**
 * returns a javascript array structure describing all classes being a subclass of class $v_key or a subGraph structure described in xml if $asGraphXML is set to true,  related to $ns set of knowledge specified (may be remote knowledge)
 *@param TAO:session $pSession returned by authenticate service
 *@access private
 *@param Array([0] => String) $v_key the class returned classes are subClassOf 
 *@param Array([0] => String) $pType deprecated
 *@param String $ns is optional (if none use empty sting). It defines the namespace, set of data to use.  This namespace is got using service "getrdffromaremotemodule" 
 *@param Boolean $asGraphXML if true returns a javascript structure else a graph structure in graphml
 *@return string
 */


function getXMLclasses($pSession,$v_key,$pType,$ns,$modelManager=false,$asGraphXML=false,$classtype="c"){
	// pour les Classes
	//    global $client;

	$ret_xmlTree = "";


	$numClass=array($v_key);

	$pRemoteDocKey = array(0 => $ns);
	//$init= microtime_float();
	$sous_classes = getsubClasses($pSession, array($v_key),  $pRemoteDocKey,$modelManager);

	//$end= microtime_float();
	//echo "getsubCLasses";echo $end-$init;echo "<br>";

	if($sous_classes["pDescription"] != ""){
		$sous_classes_collection = $sous_classes["pDescription"]; //[0];
		foreach ($sous_classes_collection as $description){
			$class_key = $description["ClassKey"];


			$v_s_label = $description["ClassLabel"];
			$v_s_comment = $description["ClassComment"];


			if (!($asGraphXML)) {

				$v_s_label=getOverDiv($v_s_label,$v_s_comment);

				$ret_xmlTree .= "['".getPrefix($class_key).strtr($v_s_label,array("'"=>"\'"))."', 'generis_UiControllerHtml.php?do=show&param1=".urlencode( $class_key)."&type=".$classtype."',";
			}
			else
			{
				$ret_xmlTree .= '
					<node name="'.$class_key.'">
					<label>'.strtr($v_s_label,array("'"=>"'")).'</label>
					<style>
					<line colour="black"/>
					<fill xlink:href="../../core/view/icons/class.gif"/>
					<dataref>
					<ref xlink:show="new" xlink:href="http://'.$_SERVER["HTTP_HOST"].'/core/view/generis_UiControllerHtml.php?do=show&amp;param1='.$class_key.'"/>
					</dataref>

					</style>
					</node>
					<edge source="'.$numClass[0].'"  target="'.$class_key.'" colour="black"></edge>
					';
			}




			/*Deprecated cbr
				if(substr($v_s_key,0,1)=="#"){
		$v_s_key = substr($v_s_key,2);
			}*/

			if ($pType["properties"]) {
				$pClassId = array(0 =>  $class_key);
				// $init= microtime_float();
				$pPropDescription = getProperties($pSession, $pClassId, 
					$pRemoteDocKey,$modelManager);


				//$end= microtime_float();
				//echo "getProperties";echo $end-$init;echo "<br>";
				if($pPropDescription["pDescription"] != ""){
					$properties_collection = $pPropDescription["pDescription"]; //[0];
					foreach ($properties_collection as $propDescription){
						//print_r($propDescription);die();
						//echo substr($propDescription["PropertyRange"],strpos($propDescription["PropertyRange"],"#"));die();
						$v_p_key = $propDescription["PropertyKey"];
						$v_p_label = $propDescription["PropertyLabel"];

						$v_p_comment = $propDescription["PropertyComment"];


						if (!($asGraphXML))
						{
							$v_p_label=getOverDiv($v_p_label,$v_p_comment);

							$ret_xmlTree .= "['".getPrefix($v_p_key).strtr($v_p_label,array("'"=>"\'"))."', 'generis_UiControllerHtml.php?do=show&param1=".urlencode($v_p_key)."&type=p'],";
						}
						else
						{
							$range = substr($propDescription["PropertyRange"],strpos($propDescription["PropertyRange"],"#"));
							/*This implementation generates nodes for properties*/
					/*
					$ret_xmlTree .= '
							<node name="'.$v_p_key.'">
								<label>'.strtr($v_p_label,array("'"=>"'")).'</label>
								<style>
								<line colour="black"/>
								<fill xlink:href="../../core/view/icons/nproperty.gif"/>
								</style>
								<dataref>
						<ref xlink:show="new" xlink:href="http://'.$_SERVER["HTTP_HOST"].'/core/view/generis_UiControllerHtml.php?do=show&param1='.substr($v_p_key,1).'"/>
					</dataref>
							</node>
							<edge source="'.$pClassId[0].'" isdirected="true" target="'.$v_p_key.'" >
									<label></label>
									<line linewidth="1" linestyle="dotted" colour="#fcb86c"/>
							</edge>
							<edge source="'.$v_p_key.'" isdirected="true" target="'.$range.'" >
									<label></label>
									<line linewidth="1" linestyle="dotted" colour="#6cb8fc"/>
							</edge>
								';
					 */
							/*This implementation just generates 1 edges for property*/
							$ret_xmlTree .= '

								<edge source="'.$pClassId[0].'" isdirected="true" target="'.$range.'" >
								<label>'.strtr($v_p_label,array("'"=>"'")).'</label>
								<line linewidth="1" linestyle="dotted" colour="#fcb86c"/>
								</edge>

								';



						}

					}
				}
			}

			$ret_xmlTree .= getXMLclasses($pSession,$class_key,$pType,$ns,$modelManager,$asGraphXML,$classtype);

			if ($pType["instances"]) {
				if ($classtype=="m") {$instancetype="im";//Instance of metaclass (a class but icon is different)
				} else {$instancetype="i";}
				$ret_xmlTree .= getXMLinstances($pSession,$class_key,$pType,$ns,$modelManager,$asGraphXML,$instancetype);
			}
			if (!($asGraphXML))
			{
				$ret_xmlTree .= "],";
			}
		}
	}
	return($ret_xmlTree);
}
/**
 * returns a javascript array structure describing all resources or a subGraph structure described in xml if $asGraphXML is set to true,  related to $ns set of knowledge specified (may be remote knowledge)
 *@param TAO:session $pSession returned by authenticate service
 *@access public
 *@param Array([0] => String) $v_key the class returned classes are subClassOf 
 *@param Array([0] => String) $pType deprecated
 *@param String $ns is optional (if none use empty sting). It defines the namespace, set of data to use.  This namespace is got using service "getrdffromaremotemodule" 
 *@param Boolean $asGraphXML if true returns a javascript structure else a graph structure in graphml
 *@param Array([0] => String) $pClassId resource root selected
 *@return string
 */

function getTree($pSession,$pType,$ns,$asGraphXML=false,$pClassId="")    {


	//$_SESSION["RDFS:CLASS"]=false;
	//$_SESSION["RDFS:RESOURCE"]=false;
	$x = urldecode($pSession[0]);
	$modelManager = unserialize($x);
	$modelManager->bd->con=connection($modelManager->currentModuleDatabase);
	$ret_xmlTree = "";
	$pRemoteDocKey = array(0 => $ns);
	set_time_limit(9999);
	if ($pClassId=="")
	{
		$topOClasses=array();

		$topOClasses[]="http://www.tao.lu/Ontologies/generis.rdf#generis_Ressource";
		//TODO 
		error_reporting(0);
		if ($_SESSION["root"]=="0")
		{
			$topOClasses = getTopClasses($pSession,$pRemoteDocKey); 
			$topMetaClasses=getTopMetaClasses($pSession,$pRemoteDocKey);
			//print_r($topOClasses);
			$topClasses= array("m"=>$topMetaClasses,"c"=>$topOClasses);
		}
		else
		{
			$key="n";
			$key =  array_search('http://www.tao.lu/datatypes/WidgetDefinitions.rdf#rangeConstraintTypes', $topOClasses);
			if ($key!="") {
				unset($topOClasses[$key]);
			}
			$key =  array_search('http://www.tao.lu/datatypes/WidgetDefinitions.rdf#WidgetClass', $topOClasses);
			if ($key!="") unset($topOClasses[$key]);
			$key =  array_search('http://www.w3.org/2000/01/rdf-schema#Resource', $topOClasses);
			if ($key!="") unset($topOClasses[$key]);

			$topClasses= array("c"=>$topOClasses);
		}
	}
	else
	{
		$topClasses= array("c"=>array($pClassId));
	}
	$ret_xmlTree="";

	if (!($asGraphXML))
	{
		$ret_xmlTree .= "['Model','http://www.w3.org/1999/02/22-rdf-syntax-ns#&type=root',";
	}
	foreach ($topClasses as $classtype=>$alltopclasses)
	{

		foreach ($alltopclasses as $key=>$val)
		{

			//$init= microtime_float();
			$labelcomment = getLabelComment($pSession,$val,$pRemoteDocKey,$modelManager);
			//$pDescription = getClassDescription($pSession, array($val), $pRemoteDocKey,$modelManager);

			//$end= microtime_float();
			//echo "getClassDescription";echo $end-$init;echo "<br>";

			$v_key = $val;

			$v_label = $labelcomment["label"];
			$v_comment = $labelcomment["comment"];

			if (!($asGraphXML))
			{
				$v_label=getOverDiv($v_label,$v_comment);

				$ret_xmlTree .= "['".getPrefix($v_key).strtr($v_label,array("'"=>"\'"))."', 'generis_UiControllerHtml.php?do=show&param1=".urlencode($v_key)."&type=".$classtype."',";
			}
			else
			{
				$ret_xmlTree .= '
					<node name="'.$v_key.'">
					<label>'.strtr($v_label,array("'"=>"'")).'</label>
					<style>
					<line colour="black"/>
					<fill xlink:href="../../core/view/icons/class.gif"/>
					<dataref>
					<ref xlink:show="new" xlink:href="http://'.$_SERVER["HTTP_HOST"].'/core/view/generis_UiControllerHtml.php?do=show&amp;param1=c1"/>
					</dataref>
					</style>
					</node>

					';
			}


			if ($pType["properties"])  { 


				$pPropDescription = getProperties($pSession, array($val), $pRemoteDocKey,$modelManager);


				if($pPropDescription["pDescription"] != ""){
					$properties_collection = $pPropDescription["pDescription"]; //[0];
					foreach ($properties_collection as $propDescription){
						$v_p_key = $propDescription["PropertyKey"];


						$subProperties = getsubProperties($pSession, array($v_p_key),$pRemoteDocKey,$modelManager);
						$subpropertiesjs="";
						if(sizeof($subProperties["pDescription"]) > 0){
							$subproperties_collection = $subProperties["pDescription"]; 
							$subpropertiesjs=",";
							foreach ($subproperties_collection as $description){
								$v_s_key = $description["PropertyKey"];
								$v_s_label = $description["PropertyLabel"];
								$v_s_comment = $description["PropertyComment"];


								if (!($asGraphXML)) {

									$v_s_label=getOverDiv($v_s_label,$v_s_comment);

									$subpropertiesjs .= "['".getPrefix($v_s_key).strtr($v_s_label,array("'"=>"\'"))."', 'generis_UiControllerHtml.php?do=show&param1=".urlencode($v_s_key)."&type=sp'],";
								}
							}
						}


						$v_p_label = $propDescription["PropertyLabel"];
						$v_p_comment = $propDescription["PropertyComment"];


						if (!($asGraphXML))
						{
							$v_p_label=getOverDiv($v_p_label,$v_p_comment);
							$ret_xmlTree .= "['".getPrefix($v_p_key).strtr($v_p_label,array("'"=>"\'"))."', 'generis_UiControllerHtml.php?do=show&param1=".urlencode($v_p_key)."&type=p'".$subpropertiesjs."],";
						}
						else
						{
							$range = substr($propDescription["PropertyRange"],strpos($propDescription["PropertyRange"],"#"));	

				/*
				$ret_xmlTree .= '
				<node name="'.$v_p_key.'">
				<label>'.strtr($v_p_label,array("'"=>"'")).'</label>
				<style>
								<line colour="black"/>
								<fill xlink:href="../../core/view/icons/nproperty.gif"/>
					<dataref>
						<ref xlink:show="new" xlink:href="http://'.$_SERVER["HTTP_HOST"].'/core/view/generis_UiControllerHtml.php?do=show&param1='.substr($v_p_key,1).'"/>
					</dataref>
								</style>
				</node>
				<edge source="'.$val.'" target="'.$v_p_key.'" >
					<label></label>
				<line linewidth="1" linestyle="dotted" colour="#black"/>	
				</edge>
				<edge source="'.$v_p_key.'" isdirected="true" target="'.$range.'" >
				<label></label>
				<line linewidth="1" linestyle="dotted" colour="#6cb8fc"/>
				</edge>
					';
				 */

							$ret_xmlTree .= '

								<edge source="'.$v_p_key.'" isdirected="true" target="'.$range.'" >
								<label>'.strtr($v_p_label,array("'"=>"'")).'</label>
								<line linewidth="2" linestyle="dotted" colour="#fcb86c"/>
								</edge>

								';

						}





					}
				}
			}

			$ret_xmlTree .= getXMLclasses($pSession,$val,$pType,$ns,$modelManager,$asGraphXML,$classtype);


			if ($pType["instances"])  {
				if ($classtype=="m") {$instancetype="im";//Instance of metaclass (a class but icon is different)
				} else {$instancetype="i";}

				$ret_xmlTree .= getXMLinstances($pSession,$val,$pType,$ns,$modelManager,$asGraphXML,$instancetype);
			}


			if (!($asGraphXML))
			{
				$ret_xmlTree .= "],";
			}
		}//Foreach
	}//Foreach
	$ret_xmlTree .= "]";

	return array("pXMLTree" => array($ret_xmlTree));
}
?>
