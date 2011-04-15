<?php

function getXMLinstances($pSession,$v_key,$pType,$ns,$modelManager=false,$instancetype="i"){

    $ret_xmlTree = "";
    $pRemoteDocKey = array(0 => $ns);
	$instances = getInstances($pSession, array($v_key), $pRemoteDocKey,$modelManager);
	if($instances["pDescription"] != ""){
        $instances_collection = $instances["pDescription"]; //[0];
		foreach ($instances_collection as $description){
            $v_s_key = $description["InstanceKey"];
            $v_s_label = $description["InstanceLabel"];
            $v_s_comment = $description["InstanceComment"];
           		 
				

			
				$v_s_label=getOverDiv($v_s_label,$v_s_comment);
			$ret_xmlTree .= "['".strtr($v_s_label,array("'"=>"\'"))."', 'generis_UiControllerHtml.php?ns=&show=".urlencode($v_s_key)."&type=".$instancetype."'],";
            
			
			
			
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


function getXMLclasses($pSession,$v_key,$pType,$ns,$modelManager=false,$classtype="c"){
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
           
			
			

			$v_s_label=getOverDiv($v_s_label,$v_s_comment);

			$ret_xmlTree .= "['".strtr($v_s_label,array("'"=>"\'"))."', 'generis_UiControllerHtml.php?ns=&show=".urlencode( $class_key)."&type=".$classtype."',";
						
			

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
                        $v_p_key = $propDescription["PropertyKey"];
                      	$v_p_label = $propDescription["PropertyLabel"];
                        
                        $v_p_comment = $propDescription["PropertyComment"];
                        
                         
						
						$v_p_label=getOverDiv($v_p_label,$v_p_comment);

						$ret_xmlTree .= "['".strtr($v_p_label,array("'"=>"\'"))."', 'generis_UiControllerHtml.php?ns=&show=".urlencode($v_p_key)."&type=p'],";
						
						
						
                    }
                }
            }
			
            $ret_xmlTree .= getXMLclasses($pSession,$class_key,$pType,$ns,$modelManager,$classtype);
             
			if ($pType["instances"]) {
				if ($classtype=="m") {$instancetype="im";//Instance of metaclass (a class but icon is different)
				} else {$instancetype="i";}
                $ret_xmlTree .= getXMLinstances($pSession,$class_key,$pType,$ns,$modelManager,$instancetype);
            }
			
            $ret_xmlTree .= "],";
			
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

function getTree($pSession,$pType,$ns,$pClassId="")    {

	

	$x = urldecode($pSession[0]);$modelManager = unserialize($x);
	$modelManager->bd->con=connection($modelManager->currentModuleDatabase);
    $ret_xmlTree = "";
    $pRemoteDocKey = array(0 => $ns);
	set_time_limit(9999);
	if ($pClassId=="")
	{
	error_reporting(0);
	if ($_SESSION["root"]=="0")
		{
	$topMetaClasses=getTopMetaClasses($pSession,$pRemoteDocKey);
	$topOClasses = getTopClasses($pSession,$pRemoteDocKey); 
	$topClasses= array("m"=>$topMetaClasses,"c"=>$topOClasses);
		}
	else
		{
	$topClasses= array("c"=>array("http://www.tao.lu/Ontologies/generis.rdf#generis_Ressource"));
		}
	}
	else
	{
		$topClasses= array("c"=>array($pClassId));
	}
	$ret_xmlTree="";
	
	$ret_xmlTree .= "['Model','Model',";
			
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
	
	$v_label=getOverDiv($v_label,$v_comment);
		
	$ret_xmlTree .= "['".strtr($v_label,array("'"=>"\'"))."', 'generis_UiControllerHtml.php?ns=&show=".urlencode($v_key)."&type=".$classtype."',";
			
		


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
				   
					
					
					$v_s_label=getOverDiv($v_s_label,$v_s_comment);

					$subpropertiesjs .= "['".strtr($v_s_label,array("'"=>"\'"))."', 'generis_UiControllerHtml.php?ns=&show=".urlencode($v_s_key)."&type=sp'],";
										
					}
				}


				$v_p_label = $propDescription["PropertyLabel"];
                $v_p_comment = $propDescription["PropertyComment"];
                
                
				$v_p_label=getOverDiv($v_p_label,$v_p_comment);
				$ret_xmlTree .= "['".strtr($v_p_label,array("'"=>"\'"))."', 'generis_UiControllerHtml.php?ns=&show=".urlencode($v_p_key)."&type=p'".$subpropertiesjs."],";
				
				
				

			
				

            }
        }
    }
	
    $ret_xmlTree .= getXMLclasses($pSession,$val,$pType,$ns,$modelManager,$classtype);
 
	
	if ($pType["instances"])  {
        if ($classtype=="m") {$instancetype="im";//Instance of metaclass (a class but icon is different)
		} else {$instancetype="i";}
		
		$ret_xmlTree .= getXMLinstances($pSession,$val,$pType,$ns,$modelManager,$instancetype);
    }

	
	
    $ret_xmlTree .= "],";
    
	}//Foreach
	}//Foreach
	$ret_xmlTree .= "]";
	
    return array("pXMLTree" => array($ret_xmlTree));
}
?>