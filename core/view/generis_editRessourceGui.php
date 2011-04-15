<?php
/*
*/
/**
* Class TAOeditRessourceGUI generates html output to edit a ressource
* @author patrick
* @package usergui
*/

/**
*Includes Static html fragments like Head, CSS integration, etc...
*/
require_once("generis_ConstantsOfGui.php");		   
require_once("generis_tree.php");
/**
*Includes tao kernel
*/

/**
*Includes tao specialisation services
*/


include("generis_extendKernel.php");

class TAOeditRessourceGUI
{
	function TAOeditRessourceGUI()
	{
	}
	

	

	/**
	*Returns Html output, with form pre-filled to edit a resource 
	*@param String $ressource without #
	*/
	function getOutput($ressource,$createRessourceoftype="",$useruri="")
	{
	
		//$ns is deprecated , is set to empty string. Kernel services still receives this parameter but this is no longer considered
		$ns="";
		
		$instanceOf="";
		
		//default html output for resource creation
		$output="";
		if (isset($_SESSION["type"])) {$type = $_SESSION["type"];$id=$ressource; } else
		{$type = substr($ressource,0,1);$id=substr($ressource,1);}
		
		if ($useruri=="") 
			{
				//in case of creation we select a random number (ASC number) as id
				$x ="#".time().rand(0,65535);
			}
		else 
			{	//case of "about", the user has specified himselfs the uri of resource
				$x="absoluteURI".$useruri;
			}
		
		error_reporting(0);
		include_once("../../include/sphpforum-0.4/add_topic.php");
		
		

		switch ($createRessourceoftype)
		{
			case "i": 
				$errormessage = calltoKernel('setStatement',array($_SESSION["session"],$x,"http://www.w3.org/1999/02/22-rdf-syntax-ns#type",$id,"r","","","r"));
				$instanceOf=$id;
				
				
			break;
			case "p": $errormessage = calltoKernel('setStatement',array($_SESSION["session"],$x,"http://www.w3.org/1999/02/22-rdf-syntax-ns#type","http://www.w3.org/1999/02/22-rdf-syntax-ns#Property","r","","","r"));
			$errormessage = calltoKernel('setStatement',array($_SESSION["session"],$x,"http://www.w3.org/2000/01/rdf-schema#domain",$id,"r","","","r"));
			
			break;
			case "c": 
			
			$errormessage = calltoKernel('setStatement',array($_SESSION["session"],$x,"http://www.w3.org/2000/01/rdf-schema#subClassOf",$id,"r","","","r"));
			
			break;
					
		}
		
		if (isset($errormessage[0]) && ($errormessage[0]=="error") )
				{$_SESSION["show"]=$_SESSION["edit"];unset($_SESSION["edit"]); unset($_SESSION["what"]);die($errormessage[2]);}

		$_SESSION["lastly_shown_if_refresh"]=$id;
		
		//in case of a resource creation, we retrieve user's mask from database, todo : retrive onlyumask from $user->mask
		if ($createRessourceoftype!="") 
		{
			
			$result = calltoKernel('getUserdescription',array($_SESSION["session"],array($_SESSION["cuser"])));
			$user=$result["pDescription"];

			//in case of creation we select $x (a random number (ASC number) as id) as the id of currently edited resource
			$id=$x;
			
			//retrieve user's statement pattern for this kind of resource
			error_reporting(0);
			$resourcepattern = $user["umask"]["scopes"][$instanceOf]["-"];

			//print_r($resourcepattern);
			
		}
		
		$id=str_replace("absoluteURI","",$id);
		$iid=$id;
		$url = "./index.php?remove=".urlencode(substr($iid,strpos($iid,"#")+1));
		$output.='
		<script language="JavaScript" src="./JS/tree.js"></script>
		<script language="JavaScript" src="./JS/tree_items.js"></script>
		<script language="JavaScript" src="./JS/tree_tpl.js"></script>
		<FORM enctype="multipart/form-data" action=./index.php?show='.$ressource.'&ApplyChanges=ApplyChanges name=newressource target=_top method=post>';
	
		$output.=TABLEHEADER;
	
		$ressourceDescription = calltoKernel('getRessourceDescription',array($_SESSION["session"],$id,array($ns)));
	
		
	
			$labelComment = getLabelComment($_SESSION["session"],$id,array($ns));
		$overdivlink=getOverDivLink("./generis_UiControllerHtml.php?do=show&param1=$id&type=".$type,$labelComment["label"],$labelComment["comment"]);
			$output.='<tr><td colspan=4><input type=hidden name=instanceCreation[id] value='.$id.'><div class="Title">'.RESSOURCEDESCRIPTION.'&nbsp;'.$overdivlink.'</div></td><td>'.getDiscussionLink($id).'</td></tr>';
			$output.='<tr><td colspan=3><div class="PropertiesTitle">'.ISINSTANCEOF.':&nbsp;&nbsp;';
			
			foreach ($ressourceDescription["type"] as $key=>$val)
			{
				error_reporting(E_ALL);
			$labelComment = getLabelComment($_SESSION["session"],$val,array($ns));
			error_reporting(E_ALL);
			$val = urlencode($val);
			$val =getOverDivLink("./generis_UiControllerHtml.php?do=show&param1=".$val."&type=c",$labelComment["label"],$labelComment["comment"]);
			$output.=''.$val.'&nbsp;';
			}

			$output.='</div></td></tr>';
			if (sizeof($ressourceDescription["properties"])>0) {
			$output.='<tr><td colspan=3><div class="PropertiesTitle">'.PROPERTIES.'</div></td></tr>';
			}
			
			$ressourceDescription["properties"] =groupByPropertyKey($ressourceDescription["properties"]);
			
			foreach ($ressourceDescription["properties"] as $key=>$val)
			{
			$id= $key;	$comment=getOverDivLink("./generis_UiControllerHtml.php?do=show&param1=".urlencode($id)."&type=p",$val["PropertyLabel"],$val["PropertyComment"]);
			$widget="";
			
			if 
				(
					(is_file("../widgets/".urlencode($val["PropertyWidget"]).".php"))
					or (is_file($_SESSION["ext"]->physicalLocation.$_SESSION["ext"]->widgets.urlencode($val["PropertyWidget"]).".php"))	
				)
				{	
					if (
							($_SESSION["param2"]==$id)
							or (!isset($_SESSION["param2"]))
							or ($_SESSION["param2"]=="")
						)
					{
					$val["PropertyKey"]=$key;
					
					if (is_file("../widgets/".urlencode($val["PropertyWidget"]).".php")) 							{include("../widgets/".urlencode($val["PropertyWidget"]).".php");}
					else
						{
						if (is_file($_SESSION["ext"]->physicalLocation.$_SESSION["ext"]->widgets.urlencode($val["PropertyWidget"]).".php"))
							{	
							include($_SESSION["ext"]->physicalLocation.$_SESSION["ext"]->widgets.urlencode($val["PropertyWidget"]).".php");
							}
						}
					}
					
				}
				else
				{
					$widget=$val["PropertyWidget"]." widget is unknown";
				}
			
			$privileges = ""; 
			$inputprivileges ="";
			error_reporting("^E_NOTICE");
			$shortid = substr($id,strpos($id,"#"));	
			foreach ($resourcepattern as $privilege)
				{
					if ($shortid==substr($privilege["predicate"],strpos($privilege["predicate"],"#")))
					{
						$privileges.="read(".$privilege["read"].") <br />";
						$privileges.="edit(".$privilege["edit"].") <br />";
						$privileges.="delete(".$privilege["delete"].") <br />";
						$privileges.="assert(".$privilege["assert"].") <br />";
						$inputprivileges.="<input type=hidden name=instanceCreation[privileges][".$val["PropertyKey"]."] value=".urlencode(serialize($privilege)).">";
					}
				}

			$output.='<tr><td><div class="PropertyLabel">'.$comment.'<br/ ></div></td><td></td><td colspan=2 >'.$widget.'</td><td><span class=smallText>'.$privileges.'</span></td></tr>'.$inputprivileges;
			
			}
			
			if (sizeof($ressourceDescription["relatedproperties"])>0) {
			$output.='<tr><td colspan=3><div class="PropertiesTitle">'.RELATEDPROPERTIES.'</div></td></tr>';
			}
			foreach ($ressourceDescription["relatedproperties"] as $key=>$val)
			{
			$id= urlencode($val["PropertyKey"]);
			$comment=getOverDivLink("./generis_UiControllerHtml.php?do=show&param1=$id&type=p",$val["PropertyLabel"],$val["PropertyComment"]);
			$output.='<tr><td><div class="PropertyLabel">'.$comment.'</div></td><td></td></tr>';
			}
		
			$applychanges=getButtonimage(APPLY);
			
			$output.='
			
			
			<tr><td align=middle colspan=3>
			<input type=submit src='.$applychanges.' name=Apply_Changes style="border: 1px solid silver;" value="Apply Changes">
				</td></tr>';

						
		return $output.'';
	
	}
	

}
?>