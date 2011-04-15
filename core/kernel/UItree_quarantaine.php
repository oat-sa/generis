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


function getXMLinstances($pSession,$v_key,$pType,$ns,$modelManager=false,$asGraphXML=false){
// pour les Instances
//    global $client;
    
    $ret_xmlTree = "";
    if(substr($v_key,0,1)=="#"){
        $v_key = substr($v_key,2);
    }
    $pClassId = array(0 => $v_key);
    $pRemoteDocKey = array(0 => $ns);
    
	$instances = getInstances($pSession, $pClassId, $pRemoteDocKey,$modelManager);
     $end= microtime_float();
	
	if($instances["pDescription"] != ""){
        $instances_collection = $instances["pDescription"]; //[0];
        
		foreach ($instances_collection as $description){
            $v_s_key = $description["InstanceKey"];
            $v_s_label = $description["InstanceLabel"];
            
            $v_s_comment = $description["InstanceComment"];
           
			if (!($asGraphXML))
			{
				$v_s_label="<span onmouseover=\"return overlib('<div class=opaque><div class=calprio5>".$v_s_label."</div><div class=box-data>".$v_s_comment."</div></div>',FULLHTML);\" onmouseout=\"return nd();\">".$v_s_label."</span>";
			$ret_xmlTree .= "['".strtr($v_s_label,array("'"=>"\'"))."', 'generis_UiControllerHtml.php?ns=".$ns."&show=".substr($v_s_key,1)."'],";
            }
			else
			{
		$ret_xmlTree .= '
				<node name="'.$v_s_key.'">
					<label>'.strtr($v_s_label,array("'"=>"'")).'</label>
					<dataref>
						<ref xlink:show="new" xlink:href="http://'.$_SERVER["HTTP_HOST"].'/middleware/generis_UiControllerHtml.php?show='.substr($v_s_key,1).'"/>
					</dataref>
					<fill xlink:href="./icons/actor.gif"/>
					
				</node>
				<edge source="#c'.$pClassId[0].'" target="'.$v_s_key.'" >
					</edge>';


				$iDescription=getInstanceDescription($pSession,substr($v_s_key,2),"",$modelManager);
				
				$iDescription=$iDescription["pDescription"]["PropertiesValues"];
				
				foreach ($iDescription as $kc=>$vc)
				{
					if ( strpos($vc["range"],"#c") === 0)
					{
					
					$ret_xmlTree .='<edge source="'.$v_s_key.'" target="'.$vc["PropertyValue"].'">
					</edge>';
					}
					

				}
			}
			
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


function getXMLclasses($pSession,$v_key,$pType,$ns,$modelManager=false,$asGraphXML=false){
// pour les Classes
//    global $client;
   
    $ret_xmlTree = "";
    if(substr($v_key,0,1)=="#"){
        $v_key = substr($v_key,2);
    }
    $pClassId = array(0 => $v_key);
	$numClass=$pClassId;

    $pRemoteDocKey = array(0 => $ns);
   $init = microtime_float();
	$sous_classes = getsubClasses($pSession, $pClassId, $pRemoteDocKey,$modelManager);
   $end= microtime_float();
  
	if($sous_classes["pDescription"] != ""){
        $sous_classes_collection = $sous_classes["pDescription"]; //[0];
        foreach ($sous_classes_collection as $description){
            $v_s_key = $description["ClassKey"];
            $v_s_label = $description["ClassLabel"];
          
            $v_s_comment = $description["ClassComment"];
           
			
			if (!($asGraphXML)) {

							$v_s_label="<span onmouseover=\"return overlib('<div class=opaque><div class=calprio5>".$v_s_label."</div><div class=box-data>".$v_s_comment."</div></div>',FULLHTML);\" onmouseout=\"return nd();\">".$v_s_label."</span>";

						$ret_xmlTree .= "['".strtr($v_s_label,array("'"=>"\'"))."', 'generis_UiControllerHtml.php?ns=".$ns."&show=".substr($v_s_key,1)."',";
						}
			else
				{
			$ret_xmlTree .= '
					<node name="'.$v_s_key.'">
						<label>'.strtr($v_s_label,array("'"=>"'")).'</label>
						<style>
						<line colour="black"/>
						<fill xlink:href="./icons/folder.gif"/>
						<dataref>
						<ref xlink:show="new" xlink:href="http://'.$_SERVER["HTTP_HOST"].'/middleware/generis_UiControllerHtml.php?show='.substr($v_s_key,1).'"/>
					 </dataref>

						</style>
					</node>
					<edge source="#c'.$numClass[0].'"  target="'.$v_s_key.'" ></edge>
						';
				}
            

			
            if(substr($v_s_key,0,1)=="#"){
                $v_s_key = substr($v_s_key,2);
			}

            if ($pType["properties"]) {
    			$pClassId = array(0 => $v_s_key);
                $pPropDescription = getProperties($pSession, $pClassId, $pRemoteDocKey,$modelManager);
                
				if($pPropDescription["pDescription"] != ""){
                    $properties_collection = $pPropDescription["pDescription"]; //[0];
                    foreach ($properties_collection as $propDescription){
                        $v_p_key = $propDescription["PropertyKey"];
                        $v_p_label = $propDescription["PropertyLabel"];
                        
                        $v_p_comment = $propDescription["PropertyComment"];
                        
                         
						if (!($asGraphXML))
						{
						$v_p_label="<span onmouseover=\"return overlib('<div class=opaque><div class=calprio5>".$v_p_label."</div><div class=box-data>".$v_p_comment."</div></div>',FULLHTML);\" onmouseout=\"return nd();\">".$v_p_label."</span>";

						$ret_xmlTree .= "['".strtr($v_p_label,array("'"=>"\'"))."', 'generis_UiControllerHtml.php?ns=".$ns."&show=".substr($v_p_key,1)."'],";
						}
						else
						{
					$ret_xmlTree .= '
							<node name="'.$v_p_key.'">
								<label>'.strtr($v_p_label,array("'"=>"'")).'</label>
								<style>
								<line colour="black"/>
								<fill xlink:href="./icons/property.gif"/>
								</style>
								<dataref>
						<ref xlink:show="new" xlink:href="http://'.$_SERVER["HTTP_HOST"].'/middleware/generis_UiControllerHtml.php?show='.substr($v_p_key,1).'"/>
					</dataref>
							</node>
							<edge source="#c'.$pClassId[0].'" isdirected="true" target="'.$v_p_key.'" >
									<label>rdfs:domain</label>
									<line linewidth="1" linestyle="dotted" colour="blue"/>
							</edge>';
						}
						
                    }
                }
            }
			
            $ret_xmlTree .= getXMLclasses($pSession,$v_s_key,$pType,$ns,$modelManager,$asGraphXML);
             
			if ($pType["instances"]) {
                $ret_xmlTree .= getXMLinstances($pSession,$v_s_key,$pType,$ns,$modelManager,$asGraphXML);
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

function getTree($pSession,$pType,$ns,$asGraphXML=false,$pClassId = array(0 => "1"))    {

	
	$x = urldecode($pSession[0]);$modelManager = unserialize($x);
	$modelManager->bd->con=connection($modelManager->currentModuleDatabase);

    $ret_xmlTree = "";
    $pRemoteDocKey = array(0 => $ns);
	$topClasses = getTopClasses($pSession,$pRemoteDocKey); 
	
	$topClass = substr($topClasses[0],strpos($topClasses[0],"#")+2);
	$pDescription = getClassDescription($pSession, $topClass, $pRemoteDocKey,$modelManager);
	
	$description = $pDescription["pDescription"];
    $v_key = $description["InstanceKey"];
    $v_label = $description["InstanceLabel"];
    $v_comment = $description["InstanceComment"];

if (!($asGraphXML))
	{
$v_label="<span onmouseover=\"return overlib('<div class=opaque><div class=calprio5>".$v_label."</div><div class=box-data>".$v_comment."</div></div>',FULLHTML);\" onmouseout=\"return nd();\">".$v_label."</span>";

$ret_xmlTree .= "['".strtr($v_label,array("'"=>"\'"))."', 'generis_UiControllerHtml.php?ns=".$ns."&show=".substr($v_key,1)."',";
	}
else
    {
$ret_xmlTree .= '
		<node name="'.$v_key.'">
			<label>'.strtr($v_label,array("'"=>"'")).'</label>
			<style>
			<line colour="black"/>
			<fill xlink:href="./icons/folder.gif"/>
			<dataref>
						<ref xlink:show="new" xlink:href="http://'.$_SERVER["HTTP_HOST"].'/middleware/generis_UiControllerHtml.php?show=c1"/>
					</dataref>
			</style>
		</node>
			
		';
	}


    if ($pType["properties"])  { 
        
		$pPropDescription = getProperties($pSession, $pClassId, $pRemoteDocKey,$modelManager);
		
		
		if($pPropDescription["pDescription"] != ""){
            $properties_collection = $pPropDescription["pDescription"]; //[0];
            foreach ($properties_collection as $propDescription){
                $v_p_key = $propDescription["PropertyKey"];
                $v_p_label = $propDescription["PropertyLabel"];
                
                $v_p_comment = $propDescription["PropertyComment"];
               
			   
                
				if (!($asGraphXML))
				{
				$v_p_label="<span onmouseover=\"return overlib('<div class=opaque><div class=calprio5>".$v_p_label."</div><div class=box-data>".$v_p_comment."</div></div>',FULLHTML);\" onmouseout=\"return nd();\">".$v_p_label."</span>";
				$ret_xmlTree .= "['".strtr($v_p_label,array("'"=>"\'"))."', 'generis_UiControllerHtml.php?ns=".$ns."&show=".substr($v_p_key,1)."'],";
				}
				else
				{
					
				$ret_xmlTree .= '
				<node name="'.$v_p_key.'">
				<label>'.strtr($v_p_label,array("'"=>"'")).'</label>
				<style>
								<line colour="black"/>
								<fill xlink:href="./icons/property.gif"/>
					<dataref>
						<ref xlink:show="new" xlink:href="http://'.$_SERVER["HTTP_HOST"].'/middleware/generis_UiControllerHtml.php?show='.substr($v_p_key,1).'"/>
					</dataref>
								</style>
				</node>
				<edge source="#c'.$pClassId[0].'" target="'.$v_p_key.'" >
					<label>rdfs:domain</label>
				<line linewidth="1" linestyle="dash-dotted" colour="blue"/>	
				</edge>
					';
				}
				
				

            }
        }
    }
	//print_r(array($pSession,$v_key,$pType,$ns,$modelManager,$asGraphXML));
    $ret_xmlTree .= getXMLclasses($pSession,$v_key,$pType,$ns,$modelManager,$asGraphXML);
 
	
	if ($pType["instances"])  {
        $ret_xmlTree .= getXMLinstances($pSession,$v_key,$pType,$ns,$modelManager,$asGraphXML);
    }

	
	 if (!($asGraphXML))
	{
    $ret_xmlTree .= "]";
    }
	
    return array("pXMLTree" => array($ret_xmlTree));
}
?>