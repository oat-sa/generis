<?php

 /**
* Extracts the do variable from session, and map it to the right php file implementing the requested operation together with param varaibles set in the request
* @author patrick
* @package usergui
*/
			
			
//include all files implementing all possible requests, could be optimized by dynamically loading only needed one according to the request
error_reporting(E_ALL);


include("generis_cacheRemoteDescriptions.php");

class TAOPaneController
{
	function TAOPaneController()
	{
	}
	/**
	*Extracts the do variable from session, and map it to the right php file implementing the requested operation together with param varaibles set in the request
	*@param ns deprecated
	*@external deprecated
	*
	*@author=PPL
	*/
	function getOutput($external,$ns)
	{
		if (!(isset($_SESSION))) {session_start();}
		/*
		include_once('../../common/ext/loader/extension.php');
		include_once('../../common/common.php');
		$ext = extension::getExtension();
		$log = $ext->loadExtension(EXTENSION);
		$_SESSION["ext"]=$ext;
		*/
		$output="";
		if (isset($_SESSION["ok"])) 
		{	unset($_SESSION["lastly_shown_if_refresh"]);
			switch ($_SESSION["do"])
			{
				//The selected resource is displayed
				case "show":
						{
							include("generis_getxHtmlResourceDescription.php");
							//If the user refreshes its screen this is used to display the same page again
							$_SESSION["lastly_shown_if_refresh"]=$_SESSION["param1"];
							$output.= getxHtmlResourceDescription($_SESSION["param1"]);
							break;
						}
				//A form is displayed enabling user to edit the currently selected ressource
				case "edit":
						{	
							error_reporting(E_ALL);
							include("generis_editRessourceGui.php");
							$TAOeditRessourceGUI=new TAOeditRessourceGUI();
							$output.=$TAOeditRessourceGUI->getOutput($_SESSION["param1"],"","");
							break;
						}
				//A form is displayed enabling user to create a new instance of the selected class
				case "addInstance":
						{
							include("generis_editRessourceGui.php");
							if (isset($_SESSION["anottate"])) $useruri=$_SESSION["anottate"]; else $useruri="";
							$TAOeditRessourceGUI=new TAOeditRessourceGUI();
							
							$output.=$TAOeditRessourceGUI->getOutput($_SESSION["param1"],"i",$useruri);
							//The variable anotatte must change and become a second aprameter for the request
							unset($_SESSION["anottate"]);
							break;
						}
				//A form is displayed enabling user to create a new subclass of the selected class
				case "addSubClass":
						{				
							include("generis_editRessourceGui.php");
							$TAOeditRessourceGUI=new TAOeditRessourceGUI();
							$output.=$TAOeditRessourceGUI->getOutput($_SESSION["param1"],"c","");
							break;
						}
				//A form is displayed enabling user to create a new property of the selected class
				case "addProperty":
						{				
							include("generis_editRessourceGui.php");
							$TAOeditRessourceGUI=new TAOeditRessourceGUI();
							$output.=$TAOeditRessourceGUI->getOutput($_SESSION["param1"],"p","");
							break;
						}
				//this loads the selected plugin in the right pane
				case "plugin":
						{ 
							if (is_dir("../../includes/".$_SESSION["param1"]))
							header("Location: ../../includes/".strtolower($_SESSION["param1"]));
							break;
						}
				//This display the form to edit setting of the selected user
				case "showuser":
						{
							include("generis_showUserGui.php");
							$TAOshowUserGUI=new TAOshowUserGUI();
							$output.=$TAOshowUserGUI->getOutput($_SESSION["param1"]);
							break;
						}
				//This displays the group of user and enable the connected user to add seome member of the group
				case "showgroupuser":
						{
							include("generis_showGroupUserGUI.php");
							$TAOshowGroupUserGUI=new TAOshowGroupUserGUI();
							$output.=$TAOshowGroupUserGUI->getOutput($_SESSION["param1"]);
							$output.=$TAOshowGroupUserGUIOutput;
							break;
						}
				//This displays the selected group of subscribers and enables the connected user to add new subescribers
				case "showgroupsubscriber":
						{
							include("generis_showGroupSubscriberGui.php");
							$TAOshowgroupsubscriberGUI=new TAOshowgroupsubscriberGUI();
							$output.=$TAOshowgroupsubscriberGUI->getOutput($_SESSION["param1"]);
							break;
						}
				//This displays the form describing the selected subscribee
				case "subscribee":
						{
							include("generis_showsubscribeeGUI.php");
							$TAOshowsubscribeeGUI=new TAOshowsubscribeeGUI();
							$output.=$TAOshowsubscribeeGUI->getOutput($_SESSION["param1"]);
							break;
						}
				//This displays a form to add a new subscribee
				case "addsubscribee":
						{
							include("generis_addSubscribeeGui.php");
							$TAOaddsubscribeeGUI=new TAOaddsubscribeeGUI();
							$output.=$TAOaddsubscribeeGUI->getOutput();
							break;
						}
				//This displays a form to create new groups of users
				case "showglobaluser":
						{
							include("generis_showGlobalUserGui.php");
							$TAOshowglobalUserGUI=new TAOshowglobalUserGUI();
							$output.=$TAOshowglobalUserGUI->getOutput($_SESSION["param1"]);
							break;
						}
				//This display the description of the selected subscriber
				case "showsubscriber":
						{
						include("generis_showSubscriberGUI.php");
						$TAOshowsubscriberGUI=new TAOshowsubscriberGUI();
						$output.=$TAOshowsubscriberGUI->getOutput($_SESSION["param1"]);
						break;
						}
				//This displays a form to perform some search based on the model of the selected class	
				case "search":
						{
						 include("generis_searchGui.php");
						$TAOsearchGUI=new TAOsearchGUI();
						$output.=$TAOsearchGUI->getOutput($_SESSION["param1"],"");
						break;
						}
				
				case "settings":
						{
						include("generis_settingsGui.php");
						$TAOsettingsGUI=new TAOsettingsGUI();
						$output.=$TAOsettingsGUIOutput=$TAOsettingsGUI->getOutput("");
						break;
						}
				
				//Exception for tao, this links to the authoring tool , normally widgets should link on their own to the right widget applciation and should not modify generis controller.

				/****************************** To be externalized ************************************************/
				case "AuthoringT":
						{
						include($_SERVER["DOCUMENT_ROOT"].$_SESSION["ext"]->widgets."TAOAuthoringTGUI.php");
						$TAOAuthoringTGUI=new TAOAuthoringTGUI();
						$output.=$TAOAuthoringTGUI->getOutput($_SESSION["param1"]);
						break;
						}
				case "Authoring":
						{	
							$ressource=$_SESSION["param1"];
							foreach ($ressource as $key=>$val)
							{
							$instance=$key;
							foreach ($val as $keyu=>$valu)	{$property=$keyu;}
							}
							$itemmodel="";$oldxml="";
							$iDescr = calltoKernel('getInstanceDescription',array($_SESSION["session"],array($instance),array("")));
							$x = $iDescr["pDescription"];
							error_reporting(0);				
							foreach ($x["PropertiesValues"][0] as $ii => $pvalue)
								{
									if ($pvalue["PropertyKey"] == "http://www.tao.lu/Ontologies/TAOItem.rdf#ItemModel")
										{	$itemmodel = $pvalue["PropertyValue"];
											$iDescr = calltoKernel('getInstanceDescription',array($_SESSION["session"],array($itemmodel),array("")));
											$itemmodel = $iDescr["pDescription"]["label"];
										}
									if ($pvalue["PropertyKey"] == $property)
										{	
											$oldxml = $pvalue["PropertyValue"];
										}
								}
						
							/*Switcher ici en fonction du meta-modele*/
							if ($itemmodel=="QCM")
							{
								include($_SESSION["ext"]->physicalLocation.$_SESSION["ext"]->widgets."TAOAuthoringGUI.php");
								$TAOAuthoringGUI=new TAOAuthoringGUI();
								$output.=$TAOAuthoringGUI->getOutput($ressource);
							}
							else
							{
									error_reporting(0);
									$_SESSION["ModelnsInstance"] = calltoKernel('getNamespace',array($_SESSION["session"])).$instance;
									$_SESSION["OldXml"] = $oldxml;
									$_SESSION["ITEMpreview"]=$oldxml;
									$_SESSION["ModelnsProperty"] = $property;
									$_SESSION["Identity"]="".$_SESSION["ext"]->httpLocation.$_SESSION["ext"]->widgets."itemAuthoring/".$itemmodel."authoring.php";
									include_once($_SESSION["ext"]->physicalLocation.$_SESSION["ext"]->widgets."itemAuthoring/".$itemmodel."authoring.php");
									die();
									
							}
						}

						/****************************** /To be externalized ************************************************/
			}//end of switch


			
		}//endif
		//if the session is not set (session timeout case, displays the authentication pane)
		else
		{	include("generis_authenticate.php");
			$TAOauthenticate=new TAO_Pane_Authenticate();
			$output.=$TAOauthenticate->getOutput();
		}

		unset($_SESSION["param1"]);
		unset($_SESSION["param2"]);
		unset($_SESSION["param3"]);
		unset($_SESSION["do"]);
		return $output;
	}
}
?>
