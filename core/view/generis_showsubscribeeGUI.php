<?php
/*
	
   
   
    

    
    
    
    

    
    
    

*/
/**
* 
* @author patrick
* @package usergui
*/
require_once("generis_ConstantsOfGui.php");	   

class TAOshowsubscribeeGUI
{
	function TAOshowsubscribeeGUI()
	{
	}
	function getOutput($ressource)
	{
	
		$output='';
		

		
		
		$output.="<BR>".TABLEHEADER."<tr><td rowspan=100 width=14%></td></tr>";
	
			
			// ancienne version
			//$result = getSubscribeeasUser($_SESSION["session"]);
			// appel webservices
			$result = calltoKernel('getSubscribeeasUser',array($_SESSION["session"]));
						
			$user = $result["pDescription"][$ressource];
			
			$output.='<center><FORM action=./index.php?subscribee='.$ressource.' name=editsubscribee target=_top method=post>';
			$output.='<tr><td colspan=3></td></tr>';
			$output.='<tr><td colspan=3><div class="Title">'.EDITSUBSCRIBEE.'</div></td></tr>';
			$output.='<tr><td colspan=3></td></tr>';
			$output.='<tr><td colspan=3></td></tr>';

			$output.='<tr><td><input type=hidden name=editanuser[Idsub] value='.$user["Idsub"].'></td></tr>';
			$output.='<tr><td><div class="AUTHINFOS">Group</div></td><td><select name=editanuser[type]>';
		$groups=array("http://www.tao.lu/Ontologies/TAOSubject.rdf#Subject","http://www.tao.lu/Ontologies/TAOGroup.rdf#Group","http://www.tao.lu/Ontologies/TAOTest.rdf#Test","http://www.tao.lu/Ontologies/TAOItem.rdf#Item");
			foreach ($groups as $key=>$val)

			{if ($val==$user["type"]) {$output.="<option selected>$val";} else {$output.="<option>$val";}}
			
			
			$output.='</select></td></tr>';
			
			$output.='<tr><td><div class="AUTHINFOS">'.LOGIN.'</div></td><td><input type=text name=editanuser[login] value='.$user["login"].'></td></tr>';
			$output.='<tr><td><div class="AUTHINFOS">'.PASSWORD.'</div></td><td><input type=text name=editanuser[pass1]['.$user["password"].'] value=CRYPTED></td></tr>';

			$output.='<tr><td><div class="AUTHINFOS">'.DATABASENAME.'</div></td><td><input type=text name=editanuser[dataBaseName] value='.$user["dataBaseName"].'></td></tr>';

			$output.='<tr><td><div class="AUTHINFOS">URL</div></td><td><input type=text name=editanuser[url] value="'.$user["url"].'" size=50></td></tr>';
			
			
			$apply=getButtonimage(APPLY);
			$output.='<tr><td align=right><input type=image src='.$apply.' name=EditSubscribee value=Apply>';
			$output.="</form></td>";
			
			$output.="<td align=left><FORM action=./index.php name=edituser target=_top method=post>";
			$RemoveSubscribee=getButtonimage(REMOVE,true);
			$output.='<input type=hidden name=login value='.$user["Idsub"].'>
			<input type=image src='.$RemoveSubscribee.' name=RemoveSubscribee value="Remove Subscribee"></form></td></tr>';
			$output.="";

		$output.=TABLEFOOTER;
			
		return $output;
	
	}
	   
	 
	 
	


}
?>