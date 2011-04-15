<?php
/*
	
   
   
    

    
    
    
    

    
    along with this program; if not, Edit to the Free Software
    

*/
/**
* Implements user interface for search purpose among resources
* @author patrick
* @package usergui
*/
require_once("generis_ConstantsOfGui.php");	   

class TAOsearchGUI
{
	function TAOsearchGUI()
	{
	}
	function getForm($id,$ns,$rootlevel=false)
		{
		$return="";
		$ressourceDescription = calltoKernel('getressourceDescription',array($_SESSION["session"],$id,array($ns)));
		$namespace = calltoKernel('getNS',array($_SESSION["session"]));
		
		foreach ($ressourceDescription["relatedproperties"] as $key=>$val)
			{
					if ($rootlevel) {$_SESSION["stackCalls"]=0;}
					$id= $val["PropertyKey"];
					if ($val["PropertyKey"]=="http://www.w3.org/1999/02/22-rdf-syntax-ns#type")
					{$val["PropertyValue"]=array("#c5");}	$comment=getOverDivLink("./generis_UiControllerHtml.php?ns=$ns&show=".urlencode($id)."&type=p",$val["PropertyLabel"],$val["PropertyComment"]);
					$widget="";
					
					switch ($val["PropertyWidget"]) {
								//Use of case to insert new widgets is deprecated, just create a new file by inserting widgets in ../../widgets/
								default:
									if ((is_file("../widgets/".urlencode($val["PropertyWidget"]).".php"))
									or (is_file($_SESSION["ext"]->physicalLocation.$_SESSION["ext"]->widgets.urlencode($val["PropertyWidget"]).".php"))	
									)
									{
										if ((($val["PropertyWidget"])== "http://www.tao.lu/datatypes/WidgetDefinitions.rdf#Authoring")
											or (($val["PropertyWidget"])== "http://www.tao.lu/datatypes/WidgetDefinitions.rdf#TAuthoring"))
										{}
										else
										{
											if ($val["PropertyWidget"]=="http://www.tao.lu/datatypes/WidgetDefinitions.rdf#HTMLArea") {$val["PropertyWidget"]="http://www.tao.lu/datatypes/WidgetDefinitions.rdf#TextBox";};
//											print_r($val);
											$key = $val["PropertyKey"];
											if ($key[0]=="#") {$val["PropertyKey"]=$namespace.$val["PropertyKey"];}
											//print_r($val);
											
											
											if (is_file("../widgets/".urlencode($val["PropertyWidget"]).".php"))
											include("../widgets/".urlencode($val["PropertyWidget"]).".php");
											if (is_file($_SESSION["ext"]->physicalLocation.$_SESSION["ext"]->widgets.urlencode($val["PropertyWidget"]).".php"))	
											include($_SESSION["ext"]->physicalLocation.$_SESSION["ext"]->widgets.urlencode($val["PropertyWidget"]).".php");
										}
									
									if (((urlencode($val["PropertyWidget"]))=="http%3A%2F%2Fwww.tao.lu%2Fdatatypes%2FWidgetDefinitions.rdf%23TreeView") and ($_SESSION["stackCalls"]<=1)) {
										$rcolor=221-$_SESSION["stackCalls"]*10;
										$gcolor=219-$_SESSION["stackCalls"]*10;
										$bcolor=201-$_SESSION["stackCalls"]*10;
										$_SESSION["stackCalls"]++; $widget.="</td>"."<td style=border-color:rgb(".$rcolor.",".$gcolor.",".$bcolor.");background-color:rgb(".$rcolor.",".$gcolor.",".$bcolor.");display:none;height:auto;><table border=1>".$this->getForm($val["PropertyRange"],$ns)."</table>";}
									}
									else
									{$widget=$val["PropertyWidget"]." widget is unknown";}
									

					}
					$return.='<tr style=height:auto;><td style=height:auto;><div class="PropertyLabel">'.$comment.'</div></td><td></td><td style:height:auto;>'.$widget.'</td></tr>';
			
			}
			return $return;
		}

	function getOutput($ressource)
	{
		
		$ns="";
		//$_SESSION["ClassInd"]="#".$ressource;
		$output="";
		
		if (isset($_SESSION["type"])) {$type = $_SESSION["type"];$id=$ressource; } else
		{$type = substr($ressource,0,1);$id=substr($ressource,1);}
		
		$iid=$id;
		
		
		$output.='
		<script language="JavaScript" src="./JS/tree.js"></script>
		<script language="JavaScript" src="./JS/tree_items.js"></script>
		<script language="JavaScript" src="./JS/tree_tpl.js"></script>
		<FORM enctype="multipart/form-data" action=generis_search.php name=newressource method=post>';
	
		$output.="<table class=generisTable cellpadding=1 cellspacing=1 border=0 valign=top >";
		$output.=$this->getForm($id,$ns,true);
			
		$applychanges=getButtonimage(SEARCH);
		$output.='
			<tr><td></td><td align=right colspan=3>
				
			<input type=submit src='.$applychanges.' name=getResults value="Search">
				</td></tr>';

		$output.=TABLEFOOTER;				
		return $output;
	
	}

	/*
	function getOutput($ressource,$ns)
	{	
		$output="";
		$type = substr($ressource,0,1);
		$id=substr($ressource,1);
		
		$output.="<FORM action=./TAOsearch.php name=newressource method=post>";
		$output.=TABLEHEADER;
		$auth = calltoKernel('getClassDescription',array($_SESSION["session"],array($id),array($ns)));
		$output.="<input type=hidden name=instanceCreation[ns] value=".$ns.">";
		$result = $auth["pDescription"];
		$output.="<tr><td><table valign=top border=0 cellpadding=4 cellspacing=4>";
		$output.='<tr><td colspan=3><Hr></td></tr>';
		$output.='<tr><td><input type=radio value=1 name="instanceCreation[exactmatch]">'.EXACT.'</td><td></td><td><input type=radio value=0 checked name="instanceCreation[exactmatch]">'.CONTAINS.'</td></tr>';
		$output.='';
		$output.='<tr><td colspan=3><Hr></td></tr>';
		$output.='<tr><td colspan=3><input type=hidden name=instanceCreation[id] value='.$result["InstanceKey"].'>'.SEARCHCRITERIA.'</td></tr>';
		$output.='<tr><td colspan=3><Hr></td></tr>';
		$output.='<tr><td ><div class="LabelTitle">'.LABEL.'</div></td><td colspan=2><input size=25 type=text name=instanceCreation[label] value="">
		</td></tr>';
		$output.='<tr><td ><div class="CommentTitle">'.COMMENT.'</div></td><td colspan=2><input size=25 type=text name=instanceCreation[comment] value=""></td></tr>';
		$output.='<tr><td><div class="TypeTitle">'.TYPE.'</div></td><td colspan=2><div class="Type"><SELECT name=instanceCreation[type][] MULTIPLE size=1>';
		$classes = calltoKernel('getAllClasses',array($_SESSION["session"],array(1),array($ns)));
		$parents = $classes;
		array_push($parents,"#".$ressource);

		foreach ($classes["pDescription"][0] as $key=>$val)
			{
					if  (!(array_search($val["PropertyKey"],$parents)===FALSE))
					{$output.='<option SELECTED value='.$val["PropertyKey"].'>'.$val["PropertyLabel"].'</option>';}
					
			}
		$output.="</select>";
		$output.="</table></td><td><table border=0>";
		$output.='<tr><td colspan=4><Hr></td></tr>';
		$output.='<tr border=1><td><div class="PropertiesTitle">'.PROPERTYLABEL.'</div></td><td><div class="PropertiesTitle">'.PROPERTYVALUE.'</div></td></tr>';
		$output.='<tr><td colspan=4><Hr></td></tr>';
		$x = $result["PropertiesValues"][0];
		foreach ($x as $key=>$val)
				{
					
					$id= substr($val["PropertyKey"],1);
					$comment="<a href=./generis_UiControllerHtml.php?ns=$ns&show=$id onmouseover=\"return overlib('<div class=PropertyComment>".$val["PropertyComment"]."</div>');\" onmouseout=\"return nd();\">".$val["PropertyLabel"]."</a>";
					$output.='<tr><td><div class="PropertyLabel">'.$comment.'</div></td><td>';

					switch ($val["PropertyWidget"]) {
						case "http://www.tao.lu/datatypes/WidgetDefinitions.rdf#TextBox":
						$output.= '<input size=25 type=text name=instanceCreation[properties]['.$val["PropertyKey"].'] value="'.$val["PropertyValue"].'">';break;
						
						case "http://www.tao.lu/datatypes/WidgetDefinitions.rdf#TextArea":
							//print_r($val);
							
							$output.= '<textarea cols=25 name=instanceCreation[properties]['.$val["PropertyKey"].']></textarea><BR>';
								
								break;

						case "http://localhost/datatypes/WidgetDefinitions.rdf#Textbox":
						$output.= '<input size=25 type=text name=instanceCreation[properties]['.$val["PropertyKey"].'] value="'.$val["PropertyValue"].'">';break;
						
						case "http://www.tao.lu/datatypes/WidgetDefinitions.rdf#http://localhost/datatypes/WidgetDefinitions.rdf#Textbox":
						$output.= '<input size=25 type=text name=instanceCreation[properties]['.$val["PropertyKey"].'] value="'.$val["PropertyValue"].'">';
												
						break;

						case "http://www.tao.lu/datatypes/WidgetDefinitions.rdf#Combobox":
										$output.= '<SELECT name=instanceCreation[properties]['.$val["PropertyKey"].']>
										';
										
										$result = calltoKernel('getInstances',array($_SESSION["session"],array(substr($val["PropertyRange"],2)),array($ns)));
										$listinstanceslabels=$result["pDescription"][0];
										$output.='<option value=???>???</option>';

										foreach ($listinstanceslabels as $keyi=>$vali)
											{$output.='<option value='.$vali["InstanceKey"].'>'.$vali["InstanceLabel"].'</option>';
											}
										
										$output.= '</SELECT>';						
										break;
						
						case "http://www.tao.lu/datatypes/WidgetDefinitions.rdf#RadioButton":
										
						                
										$result = calltoKernel('getInstances',array($_SESSION["session"],array(substr($val["PropertyRange"],2)),array($ns)));
										$listinstanceslabels=$result["pDescription"][0];
										
										foreach ($listinstanceslabels as $keyi=>$vali)
											{
											$output.='<input type=radio value='.$vali["InstanceKey"].' name="instanceCreation[properties]['.$val["PropertyKey"].']">'.$vali["InstanceLabel"].'<BR>';
											}
						break;

						case "http://www.tao.lu/datatypes/WidgetDefinitions.rdf#ListBox":
												
											$output.= '<SELECT name="instanceCreation[properties]['.$val["PropertyKey"].'][]" MULTIPLE>
											';
											$result = calltoKernel('getInstances',array($_SESSION["session"],array(substr($val["PropertyRange"],2)),array($ns)));
											$listinstanceslabels=$result["pDescription"][0];
											foreach ($listinstanceslabels as $keyi=>$vali)
												{$output.='<option value='.$vali["InstanceKey"].'>'.$vali["InstanceLabel"].'</option>';
												}
											$output.= '</SELECT>';
						break;
						case "http://www.tao.lu/datatypes/WidgetDefinitions.rdf#CheckBox":
										
										$result = calltoKernel('getInstances',array($_SESSION["session"],array(substr($val["PropertyRange"],2)),array($ns)));

										$listinstanceslabels=$result["pDescription"][0];
										foreach ($listinstanceslabels as $keyi=>$vali)
											{
												
												$output.='<input type=checkbox value='.$vali["InstanceKey"].' name="instanceCreation[properties]['.$val["PropertyKey"].']">'.$vali["InstanceLabel"].'
												<BR>';
											}
						break;
						
						}
						$output.='</td></tr>';
				}
			$output.="</table></td></tr>";	
			$search=getButtonimage(SEARCH);
			$output.='<tr><td align=center colspan=3><input type=image src='.$search.' name=getResults value="Search"></td></tr>';
		$output.=TABLEFOOTER;
		$output.="</FORM>";		
		return $output;
	}
	*/
}
?>