<?php

/**
* getxHtmlResourceDescription returns HTML describing the ressource ($ressource) (the short or long uri of ressource)
* @param $ns deprecated ($resource may be both short or long uri )
* @author patrick
* @package usergui
*/

require_once("generis_ConstantsOfGui.php");	
//hack
/**
* Retrieve itemModel and related swf file according to items definition in www.tao.lu/ontologies/TAOItem.rdf
*@param item URI of item
**/
function getItemModel($item)
	{
		$itemmodelURI = "";
		
		
		$modelitem =						calltoKernel('GetInstancePropertyValues',array($_SESSION["session"],array($item),array("http://www.tao.lu/Ontologies/TAOItem.rdf#ItemModel"),array("")));
						
		$executableswf =			calltoKernel('GetInstancePropertyValues',array($_SESSION["session"],$modelitem,array("http://www.tao.lu/Ontologies/TAOItem.rdf#SWFFile"),array("")));
		
		//*Old ditems made in tao v1 compatibility hack*/
		
		//if (strpos($modelitem[0],"#i20")!=false) return array("#i20","tao_item.swf");
		
		return array($modelitem[0],$executableswf[0]);
		

	}

//this include is only restricted to tao todo : improve extension
error_reporting("0");
include_once($_SESSION["ext"]->physicalLocation.$_SESSION["ext"]->widgets."/itemAuthoring/ItemModelFunction.php");
error_reporting(E_ALL);
function getxHtmlResourceDescription($ressource,$ns="")
	{
		$xHtmlOutput="";
		$_SESSION["ClassInd"]="#".$ressource;	
		
		if (isset($_SESSION["type"]))  {$type = $_SESSION["type"];$resourceURI=$ressource;}
		else //compatiblity with tao v1.1
		{$type = substr($ressource,0,1);$resourceURI=substr($ressource,1);}		
				
		/*URLs are base64 encoded because of problems occuring in javascript with the "#" in alert notifications, url encoding does not work too*/
		$urlResourceRemoval = "./index.php?do=remove&param1=".base64_encode($resourceURI);
		$urlRemoveAllInstances = "./index.php?do=removeall&param1=".base64_encode($resourceURI);
		//echo $resourceURI;
		$xHtmlOutput.='';

		 $discussionlink= getDiscussionLink($resourceURI);
			
			$xHtmlOutput.="<span class=resourcedescription>".TABLEHEADER;
			
			/*Retrieve data from generis kernel*/
			$ressourceDescription = calltoKernel('getressourceDescription',array($_SESSION["session"],$resourceURI,array($ns)));
			//get privileges on each tripels describing the resource
			$triplerights= getrdfStatements($_SESSION["session"],$resourceURI,array(""));
			$labelCommentRes = calltoKernel('getLabelComment',array($_SESSION["session"],$resourceURI,array($ns)));
			
			//Display resource Label  with a link to the uri (useful for description amde about real uri, using about predicate)
			$overdivlink="<a target=_BLANK href=".$resourceURI."><div class=Title>".$labelCommentRes["label"]."</div></a>";
			$xHtmlOutput.='<tr><td colspan=10><div class="Title">'.$overdivlink.' </div></td></tr>';
			$xHtmlOutput.='<tr><td rowspan=200 width=25px></td></tr>';
			$xHtmlOutput.='<tr><td colspan=5><span class="PropertiesTitle">'.ISINSTANCEOF.' :</span> &nbsp;&nbsp;<i>';
			
			$typeOfxHtml = "";
			foreach ($ressourceDescription["type"] as $key=>$val)
			{
				$labelComment = calltoKernel('getLabelComment',array($_SESSION["session"],$val,array($ns)));
				$typeOfXhtmlLink = getOverDivLink("./generis_UiControllerHtml.php?do=show&param1=".urlencode($val)."&type=c",$labelComment["label"],$labelComment["comment"]);
				$typeOfxHtml.=$typeOfXhtmlLink.', ';
			}
			$typeOfxHtml = substr($typeOfxHtml,0,strlen($typeOfxHtml)-2);

			$xHtmlOutput.=$typeOfxHtml.'</i></td></tr>';
			
			if (sizeof($ressourceDescription["properties"])>0) 
			$xHtmlOutput.='<tr><td >&nbsp;</td></tr><tr><td colspan=3><div class="PropertiesTitle">'.PROPERTIES.'</div></td></tr><tr><td >&nbsp;</td></tr>';
			
			$unAssignedProperties="";
			foreach ($ressourceDescription["properties"] as $key=>$val)
			{
				
				$propertyid= urlencode($val["PropertyKey"]);
				if (
					(isset($val["PropertyValue"]))
					and
					($val["PropertyValue"]!="")
					)
				{
					$propertyLabel=getOverDivLink("./generis_UiControllerHtml.php?do=show&param1=$propertyid&type=p",$val["PropertyLabel"],$val["PropertyComment"]);
				//if (!(isset($val["PropertyValue"]))) {$val["PropertyValue"]="";}
				$raw_value=$val["PropertyValue"];
				if ((strpos($val["PropertyValue"],"http")===0) OR (strpos($val["PropertyValue"],"#")===0))
					{
						
						$labelComment = calltoKernel('getLabelComment',array($_SESSION["session"],$val["PropertyValue"],array($ns)));
						$val["PropertyValue"] = getOverDivLink("./generis_UiControllerHtml.php?do=show&param1=".urlencode($val["PropertyValue"])."&type=i",$labelComment["label"],$labelComment["comment"]);
					}
				
				
									/******************************* T. A . O specific part (Authoring widget) **************************/
									if (($val["PropertyWidget"])== "http://www.tao.lu/datatypes/WidgetDefinitions.rdf#Authoring")
									{
									$_SESSION["ITEMpreview"]=$val["PropertyValue"];
									if (((trim($val["PropertyValue"])) != "") and isset($val["PropertyValue"]))
										{
											$itemmodel = getItemModel($resourceURI);
											
									
											$val["PropertyValue"]='</td></tr><tr><td colspan=10><object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0" width="800" height="600" id="tao_item" align="middle">
											<param name="allowScriptAccess" value="sameDomain" />
											<param name="movie" value="'.$_SESSION["ext"]->httpLocation.$_SESSION["ext"]->widgets.'itemRuntime/'.$itemmodel[1].'?localXmlFile=TAOgetItemPreview.php&xml=TAOgetItemPreview.php" />
											<param name="quality" value="high" />
											<param name="bgcolor" value="#e7e5d3" />
											<embed src="'.$_SESSION["ext"]->httpLocation.$_SESSION["ext"]->widgets.'itemRuntime/'.$itemmodel[1].'?localXmlFile=TAOgetItemPreview.php&xml=TAOgetItemPreview.php" quality="high" bgcolor="#e7e5d3" width="800" height="600" name="tao_item" align="middle" allowScriptAccess="sameDomain" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
											</object>';
										}
									}
									//specific to results
									if (($val["PropertyWidget"])== "http://www.tao.lu/datatypes/WidgetDefinitions.rdf#resultauthoring")
									{
									$val["PropertyValue"].='
										<a href=../../widgets/resultauthoring.php?instance='.urlencode($resourceURI).'>View results</a>
										<br />';
									}
									/************************************************************************************************/
					
					
					$editable='';$deletable='';
					if ($val["TripleID"]!="0") 
						$tripleEditable = getMethods($_SESSION["session"],$val["TripleID"],array(""));
					else 
						$tripleEditable=array("edit"=>false,"delete"=>false);
					
					if ($tripleEditable["edit"]) {$editable='<a href=./generis_UiControllerHtml.php?do=edit&param1='.urlencode($resourceURI).'&param2='.$propertyid.' target=pane><img border=0 src=./icons/edit.png></a>';} else {$editable='';}

					if ($tripleEditable["delete"]) {$deletable='<a href="javascript:if (confirm( \'Are you sure you want to remove this statement ?\'))   window.location.replace(\''.$urlResourceRemoval.'&param2='.$val["TripleID"].'\');" target=_top><img border=0 src=./icons/erase.png></a>';} else {$deletable='';}
					
					$privilegesinfos ="";

					if (isset($triplerights[$val["TripleID"]])) {$privilegesinfos="<span class=privileges>read:".$triplerights[$val["TripleID"]]["stread"]."<br />edit:".$triplerights[$val["TripleID"]]["stedit"]."<br />delete:".$triplerights[$val["TripleID"]]["stdelete"]."</span>";}

					$xHtmlOutput.='<tr><td>'.$editable.$deletable.'</td><td><div class="PropertyLabel">'.$propertyLabel.'</div></td><td></td><td>'.$val["PropertyValue"].'</td><td><i>'.$privilegesinfos.'</i></td></tr>';

				}
				else
				{

					$unAssignedProperties.=getOverDivLink("./generis_UiControllerHtml.php?do=show&param1=$propertyid&type=p",$val["PropertyLabel"],$val["PropertyComment"]).", ";
				}
			}
			error_reporting("^E_NOTICE");
			if ($unAssignedProperties !="")
				{
					$unAssignedProperties = substr($unAssignedProperties,0,strlen($unAssignedProperties)-2);
					$xHtmlOutput.='<tr height=5%>';
					$xHtmlOutput.='<tr><td colspan=6><span class="PropertiesTitle">'.UndefinedProperties.' :</span> <i>';
					$xHtmlOutput.=$unAssignedProperties;
					$xHtmlOutput.='</td></tr>';
				}
			$xHtmlOutput.='<tr><td >&nbsp;</td></tr>';
			if (sizeof($ressourceDescription["relatedproperties"])>0) {
			$xHtmlOutput.='<tr><td colspan=6><span class="PropertiesTitle">'.RELATEDPROPERTIES.' :</span> <i>';
			}
			$relatedproperties ="";
			foreach ($ressourceDescription["relatedproperties"] as $key=>$val)
			{
				$propertyid= urlencode($val["PropertyKey"]);
				$propertyLabel=getOverDivLink("./generis_UiControllerHtml.php?do=show&param1=$propertyid&type=p",$val["PropertyLabel"],$val["PropertyComment"]);
				$relatedproperties.='<span class="PropertyLabel">'.$propertyLabel.'</span>, ';
			}
			$relatedproperties = substr($relatedproperties,0,strlen($relatedproperties)-2);
			$xHtmlOutput.=$relatedproperties.'</i></td></tr>';

			$xHtmlOutput.="</table>
			
			";
			
			if (($_SESSION["filter"]<2) or ($_SESSION["bd"]=="menfpSubjects"))
				{
					$xHtmlOutput.=TABLEHEADER;
					$ressourceDescription = calltoKernel('getinstances',array($_SESSION["session"],array($resourceURI),array($ns)));
					if (sizeof($ressourceDescription["pDescription"])>0) $xHtmlOutput.='<tr><td >&nbsp;</td></tr><tr><td colspan=3><div class="PropertiesTitle">'.INSTANCES.' ('.sizeof($ressourceDescription["pDescription"]).')</div></td></tr><tr><td >&nbsp;</td></tr>';
					error_reporting(0);
					$instances = $ressourceDescription["pDescription"];
					foreach ($instances as $key=>$val)
					{
						$key = $val["InstanceLabel"].$key;//prevents homonym
						$orderedinstances[$key] = $val;
					}
					ksort($orderedinstances);
					foreach ($orderedinstances as $key=>$val)
					{
					$instanceid= urlencode($val["InstanceKey"]);
					$propertyLabel=getOverDivLink("./generis_UiControllerHtml.php?do=show&param1=$instanceid&type=p",$val["InstanceLabel"],$val["InstanceComment"]);
					$xHtmlOutput.='
					<tr><td></td><td><div class="PropertyLabel"><li>'.$propertyLabel.'</div></td></tr>
						
					';
					}
					$xHtmlOutput.="</table>";

				}
			$xHtmlOutput.="</span>";
			


			$editClass=getButtonimage(EDIT);
			$addInstance=getButtonimage(ADDINSTANCE);
			$addsubClass=getButtonimage(ADDSUBCLASS);
			$addProperty=getButtonimage(ADDPROPERTY);
			$removeclass=getButtonimage(REMOVE);
			$rights=getButtonimage(SPECIFY);
			$search=getButtonimage(SEARCH);
			$ressource = str_replace("\\\\","\\",$ressource);
			$ressource = urlencode($ressource);
			
			$xHtmlOutput.="<table cellpadding=1 cellspacing=1 border=0 valign=top class=topMenu>
			";
			
			if ($ns=="")
			{

			if (($type=="c") or ($type=="m"))
				{$xHtmlOutput.='
			
			<tr><td><a href=./generis_UiControllerHtml.php?do=search&param1='.$ressource.' target=pane><img border=0 src=./icons/b_search.png><span class=lg>'.SEARCH.'</span></a>
			</td></tr>';}
			
			$xHtmlOutput.='
			<tr><td><a href="javascript:window.print()"><img border=0 src=./icons/b_print.png>
			
			<span class=lg>'.PRINTR.'</span></a>
			</td></tr>
			<tr><td><img border=0 src=./icons/b_usredit.png>
			'.$discussionlink.'
			</td></tr>	
			
			
			';
		
			//$xHtmlOutput.="<table cellpadding=1 cellspacing=1 border=0 valign=top class=bottomMenu>";
			$xHtmlOutput.='<tr><td><a href=./generis_UiControllerHtml.php?do=edit&param1='.$ressource.' target=pane><img border=0 src=./icons/edit.png><span class=lg>'.EDIT.'</span></a>
			</td></tr>';
			$xHtmlOutput.='
			<tr><td><a href=./generis_rightsedition.php?do=rights&param1='.$ressource.' target=pane><img border=0 src=./icons/s_rights.png><span class=lg>'.SPECIFY.'</span></a>
			</td></tr>
			
				
			<tr><td><a href=./index.php?do=duplicate&param1='.$ressource.' target=_top><img border=0 src=./icons/CopyResource.png><span class=lg>'.DUPLICATE.'</span></a>
			</td></tr>';
			
			
			if (($type=="c") or ($type=="m"))
				{
			
			$isInstanciatiable = check_SetStatement($_SESSION["session"],"", "http://www.w3.org/1999/02/22-rdf-syntax-ns#type", urldecode($ressource),array(""));
			
			if ($isInstanciatiable[0]=="ok") {
				
				$editable = 'href=./generis_UiControllerHtml.php?do=addInstance&param1='.$ressource;
				$annotatelink = 'href="javascript:popUp(\'\')"href="javascript:popUp(\'\')"';
				}
				
				else{$editable='READONLY class="grey"';$annotatelink = 'READONLY class="grey"';
				
				}
			
			$xHtmlOutput.='
			<tr><td><a '.$editable.'  target=pane><img border=0 src=./icons/b_insrow.png><span class=lg>'.ADDINSTANCE.$labelCommentRes["label"].'</span></a>
			</td></tr>
			<script>
			function popUp(URL) {
				
				popupw = window.open(URL, \'zzz\', \'toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=0,width=600,height=140,left = 540,top = 487\');
				
				popupw.document.write(\'<body style=background-color:rgb(231,229,211)><div style="COLOR:rgb(122,0,59);	font-size:14px;font-family:Verdana;font-weight: bold;"><br /><br />Please, enter the document URI you would like to annotate (recommended) or give it a unique name</div><br /><input type=text size=50 id=a><input type=submit onclick=window.opener.document.location.replace(\\\'./generis_UiControllerHtml.php?do=addInstance&param1='.$ressource.'&anottate=\\\'+document.getElementById(\\\'a\\\').value)></body>\');


			}
			</script>		
			<tr><td><a '.$annotatelink.'><img border=0 src=./icons/b_insrow.png><span class=lg>'.ANNOTATE.'</span></a>
			</td></tr>

			<tr><td><a href="javascript:if (confirm( \'Are you sure you want to remove all instances of this class ?\'))   window.location.replace(\''.$urlRemoveAllInstances.'\');"
			target=_top><img border=0 src=./icons/erase.png>
			
			<span class=lg>	
			
			'.REMOVEALL.'
			
			</span>	
			</a>
			</td></tr>
			

			
			';
			
			
			
			$isSpecializable = check_SetStatement($_SESSION["session"],"", "http://www.w3.org/2000/01/rdf-schema#subClassOf", urldecode($ressource),array(""));
			//print_r($isSpecializable);
			if ($isSpecializable[0]=="ok") {$editable = 'href=./generis_UiControllerHtml.php?do=addSubClass&param1='.$ressource;}else{$editable='READONLY class="grey"';}
			$xHtmlOutput.='
			
			<tr><td><a '.$editable.' target=pane><img border=0 src=./icons/b_newdb.png><span class=lg>'.ADDSUBCLASS.'</span></a>
			</td></tr>';
			$isPropertyallowed = check_SetStatement($_SESSION["session"],"", "http://www.w3.org/2000/01/rdf-schema#domain", urldecode($ressource),array(""));
			if ($isPropertyallowed[0]=="ok") {$editable = 'href=./generis_UiControllerHtml.php?do=addProperty&param1='.$ressource;}else{$editable='READONLY class="grey"';}
			$xHtmlOutput.='
			<tr><td><a  '.$editable.' target=pane><img border=0 src=./icons/b_newtbl.png><span class=lg>'.ADDPROPERTY.'</span></a>
			</td></tr>

			';
				}
			
			
			$xHtmlOutput.='
			
			<!--
			<tr><td><img border=0 src=./icons/laszlo.bmp><a  href=./getswffile.php?canvas='.$ressource.' target=_blank><span class=lg>get swf file</span></a>
			</td></tr>
			-->


			<tr><td><a href="javascript:if (confirm( \'Are you sure you want to remove this ressource : '.$labelCommentRes["label"].' ?\'))   window.location.replace(\''.$urlResourceRemoval.'\');"
			target=_top><img border=0 src=./icons/erase.png>
			
			<span class=lg>	
			'.REMOVE.'
			</span>	
			</a>
			</td></tr>
				
			
			';
			
			
			
			}
			
		$xHtmlOutput.=TABLEFOOTER."</div>";
		
		if (is_file("../../include/sphpforum-0.4/add_topic.php"))
		{
		$xHtmlOutput.="<div class=topicOfDiscussion style=width:90%>";
		//if the topis is not yet created , it s created 
		$x = $resourceURI;
		include("../../include/sphpforum-0.4/add_topic.php");


			$_SESSION['username'] = $_SESSION['cuser'];
			
		$xHtmlOutput.="<br /><span class=resourcedescription style=padding:2%><div class=Title>".DISCUSS."".$labelCommentRes["label"]." </div><iframe  class=topic width=100% frameborder=0 height=100% src=\"http://".$_SERVER["HTTP_HOST"].dirname($_SERVER['PHP_SELF'])."../../.."."/include/sphpforum-0.4/view_topic.php?id=".urlencode($resourceURI)."\"></iframe></span>";
				
		
		$xHtmlOutput.="</div>";		
		}

		return $xHtmlOutput;
	
	}
	   
	 
	 
	

?>