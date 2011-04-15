<?php
/*
	
   
   
    

    
    
    
    

    
    
    

*/
/**
* User GUI to add a subscibee implementation
* @author patrick
* @package usergui
*/
require_once("generis_ConstantsOfGui.php");   

class TAOaddsubscribeeGUI
{
	function TAOaddsubscribeeGUI()
	{
	}
	function getOutput()
	{
	
		$output='';
		

		
		
		$output.="<BR>".TABLEHEADER."<tr><td rowspan=100 width=14%></td></tr>";
	
			
			$output.='<center><FORM action=./index.php name=addsubscribee target=_top method=post>';
			$output.='<tr><td colspan=3></td></tr>';
			$output.='<tr><td colspan=3><div class="Title">'.SUBSCRIBEEDESCRIPTION.'</div></td></tr>';
			$output.='<tr><td colspan=3></td></tr>';
			$output.='<tr><td colspan=3></td></tr>';
			$output.='<tr><td><input type=hidden name=editanuser[Idsub] value=""></td><td></td></tr>';
			$output.='<tr><td><div class="AUTHINFOS">'.MTYPE.'</div></td><td><select name=editanuser[type]>';
			
				$groups=array("http://www.tao.lu/Ontologies/TAOSubject.rdf#Subject","http://www.tao.lu/Ontologies/TAOGroup.rdf#Group","http://www.tao.lu/Ontologies/TAOTest.rdf#Test","http://www.tao.lu/Ontologies/TAOItem.rdf#Item");
			foreach ($groups as $key=>$val)

			{if ($val=="subject") {$output.="<option selected>$val";} else {$output.="<option>$val";}}
			
			
			$output.='</select></td></tr>';
			
			$output.='<tr><td><div class="AUTHINFOS">'.LOGIN.'</div></td><td><input type=text name=editanuser[login] value=""></td></tr>';
			$output.='<tr><td><div class="AUTHINFOS">'.PASSWORD.'</div></td><td><input type=text name=editanuser[pass1][x]></td></tr>';

			$output.='<tr><td><div class="AUTHINFOS">'.DATABASENAME.'</div></td><td><input type=text name=editanuser[dataBaseName] value=""></td></tr>';

			$output.='<tr><td><div class="AUTHINFOS">URL</div></td><td><input type=text name=editanuser[url] value="http://XXX.XXX.XXX.XXX/middleware/businessmodulesubscriber.php" size=50></td></tr>';
			
			
			$addSubscribee=getButtonimage(APPLY);
			$output.='<tr><td></td><td colspan=3><center><input type=image src='.$addSubscribee.' name=AddSubscribee value=Apply>&nbsp;&nbsp;&nbsp;';
			$output.="</form>";
			
			

		$output.=TABLEFOOTER;
			
		return $output;
	
	}
	   
	 
	 
	


}
?>