<?php
/**
* Performs calls tu update kernel 
* @package usergui
* @author Plichart Patrick <patrick.plichart@tudor.lu>
* @version 1.1
*/
include_once("generis_utils.php");
if (!(isset($_SESSION))) {session_start();}

$output="";		

		
if (isset($_SESSION["refreshlg"])) {
	$updsession =calltoKernel('setLG',array($_SESSION["session"],$_SESSION["datalg"]));
	$_SESSION["session"]=$updsession["pSession"];
	unset($_SESSION["refrreshlg"]);
}
		
	error_reporting(0);	
		if ((isset($_SESSION["instanceCreation"])) && (isset($_SESSION["Apply_Changes"])))
		{
			switch ($_SESSION["Apply_Changes"]) 
			{

			case "Apply Changes":
				include("generis_editRessource.php");	
				$ressource=$_SESSION["instanceCreation"];
					$TAOeditRessource=new TAOeditRessource();
				$TAOeditRessourceOutput=$TAOeditRessource->getOutput($ressource);
				unset($_SESSION["instanceCreation"]);
				unset($_SESSION["Apply_Changes"]);
				unset($_SESSION["range"]);
				unset($_SESSION["widget"]);
				
				$output.=$TAOeditRessourceOutput;
				break;
			case "ApplyChanges":
				include("generis_editRessource.php");	
				$ressource=$_SESSION["instanceCreation"];
				$TAOeditRessource=new TAOeditRessource();
				$TAOeditRessourceOutput=$TAOeditRessource->getOutput($ressource);
				unset($_SESSION["instanceCreation"]);
				unset($_SESSION["Apply_Changes"]);
				unset($_SESSION["range"]);
				unset($_SESSION["widget"]);
				$output.=$TAOeditRessourceOutput;
				break;
			}
		}
error_reporting("^E_NOTICE");
		switch ($_SESSION["do"])
			{
				//This calls the kernel to duplicate the selected ressource, focus uis given to the newly created resource
				case "duplicate":
					{
						$clone = calltoKernel('cloneit',array($_SESSION["session"],$_SESSION["param1"]));
						$_SESSION["do"]="show";
						$_SESSION["param1"]=$clone;
						break;
					}
				case "remove":
					{
						include("generis_removeRessource.php");
						$UIremoveRessource=new TAOremoveRessource();
						$output.=$UIremoveRessource->getOutput($_SESSION["param1"]);
						break;
						
					}
				case "removeall":
					{	
						include("generis_removeAllRessource.php");
						$UIremoveRessource=new generisremoveallRessource();
						$output.=$UIremoveRessource->getOutput($_SESSION["param1"]);
						break;
					}
			}
	

			if (isset($_SESSION["EditUser"]))
				
			{	include("generis_editUser.php");
				$TAOEditUserOutput=generis_edituser($_SESSION["editanuser"]);
				$_SESSION["showuser"]=$_SESSION["editanuser"]["login"];
				unset($_SESSION["EditUser"]);
				unset($_SESSION["editanuser"]);
				$output.=$TAOEditUserOutput;
							
			}
			if (isset($_SESSION["EditSubscribee"]))
				
			{	include("generis_editSubscribee.php");
				$TAOEditSubscribee=new TAOEditSubscribee();
				$TAOEditSubscribeeOutput=$TAOEditSubscribee->getOutput($_SESSION["editanuser"]);
				$_SESSION["subscribee"]=$_SESSION["editanuser"]["Idsub"];
				unset($_SESSION["EditSubscribee"]);
				unset($_SESSION["editanuser"]);
				$output.=$TAOEditSubscribeeOutput;
			}
			
			if (isset($_SESSION["AddSubscribee"]))
				
			{	include("generis_addSubscribee.php");
				$TAOAddSubscribee=new TAOAddSubscribee();
				$TAOAddSubscribeeOutput=$TAOAddSubscribee->getOutput($_SESSION["editanuser"]);
				$_SESSION["subscribee"]=$TAOAddSubscribeeOutput["pOKorKO"];
				unset($_SESSION["AddSubscribee"]);
				unset($_SESSION["editanuser"]);
				$output.=$TAOAddSubscribeeOutput;
			}


			if (isset($_SESSION["AddSubscriber"]))
				
			{	include("generis_addSubscriber.php");
				$TAOAddSubscriber=new TAOAddSubscriber();
				$TAOAddSubscriberOutput=$TAOAddSubscriber->getOutput($_SESSION["editanuser"]);
				error_reporting(0);
				$_SESSION["subscribee"]=$_SESSION["editanuser"]["Idsub"];
				unset($_SESSION["AddSubscriber"]);
				unset($_SESSION["editanuser"]);
				$output.=$TAOAddSubscriberOutput;
			}
			
			if (isset($_SESSION["RemoveUser"]))
				
			{	include("generis_removeUser.php");
				$TAORemoveUser=new TAORemoveUser();
				$TAORemoveUserOutput=$TAORemoveUser->getOutput($_SESSION["login"]);
				unset($_SESSION["RemoveUser"]);
				unset($_SESSION["login"]);
				$output.=$TAORemoveUserOutput;
			}
						
			if (isset($_SESSION["RemoveSubscriber"]))
			{	include("generis_removeSubscriber.php");
				$TAORemoveSubscriber=new TAORemoveSubscriber();
				$TAORemoveSubscriberOutput=$TAORemoveSubscriber->getOutput($_SESSION["login"]);
				unset($_SESSION["RemoveSubscriber"]);
				unset($_SESSION["login"]);
				$output.=$TAORemoveSubscriberOutput;
			}
			
			if (isset($_SESSION["removesubscribersgroup"]))
			{
				include("generis_removeSubscribersGroup.php");
				$TAOremovesubscribersgroup=new TAOremovesubscribersgroup();
				$TAOremovesubscribersgroupOutput=$TAOremovesubscribersgroup->getOutput($_SESSION["removesubscribersgroup"]);
				unset($_SESSION["removesubscribersgroup"]);
				$output.=$TAOremovesubscribersgroupOutput;
			}
			
			if (isset($_SESSION["RemoveSubscribee"]))
			{
				include("generis_removeSubscribee.php");
				$TAORemoveSubscribee=new TAORemoveSubscribee();
				$TAORemoveSubscribeeOutput=$TAORemoveSubscribee->getOutput($_SESSION["login"]);
				unset($_SESSION["RemoveSubscribee"]);
				unset($_SESSION["login"]);
				$output.=$TAORemoveSubscribeeOutput;
			}
		
			if (isset($_SESSION["removegroup"]))
				
			{	
				include("generis_removeGroup.php");
				$TAOremovegroup=new TAOremovegroup();
				$TAOremovegroup=$TAOremovegroup->getOutput($_SESSION["login"]);
				unset($_SESSION["removegroup"]);
				unset($_SESSION["login"]);
				$output.=$TAOremovegroupOutput;
			}
			
				if (isset($_SESSION["addUser"]))
				
			{
				include("generis_addUser.php");
				$TAOadduser=new TAOadduser();
				$TAOadduserOutput=$TAOadduser->getOutput($_SESSION["editanuser"]);
				unset($_SESSION["addUser"]);
				unset($_SESSION["editanuser"]);
				$output.=$TAOadduserOutput;
				
				
			}
			 
			 	if (isset($_SESSION["nameofgroup"]))
				
			{
				calltoKernel('addGroup',array($_SESSION["session"],array($_SESSION["nameofgroup"])));
				unset($_SESSION["nameofgroup"]);$_SESSION["msg"]=USERGROUPCREATED;
			}
			
			if (isset($_SESSION["EditSubscriber"]))
				
			{  include("generis_editSubscriber.php");
				$TAOEditSubscriber=new TAOEditSubscriber();
				$TAOEditSubscriberOutput=$TAOEditSubscriber->getOutput($_SESSION["editanuser"]);
				//$_SESSION["showuser"]=$_SESSION["editanuser"]["login"];
				unset($_SESSION["EditSubscriber"]);
				unset($_SESSION["editanuser"]);
				$output.=$TAOEditSubscriberOutput;
				
				
			}

			if (isset($_SESSION["AddGroupSubscriber"]))
				
			{
				// ancienne version
				//$id = addSubscriberGroup($_SESSION["session"],array($_SESSION["editanuser"]["login"]));
				// appel webservices
				$id = calltoKernel('addSubscriberGroup',array($_SESSION["session"],array($_SESSION["editanuser"]["login"])));

				// ancienne version
			    //affiliateGroupGroup($_SESSION["session"],array($_SESSION["editanuser"]["group"]),array($id["pOKorKO"]));
				// appel webservices
				calltoKernel('affiliateGroupGroup',array($_SESSION["session"],array($_SESSION["editanuser"]["group"]),array($id["pOKorKO"])));

				unset($_SESSION["AddGroupSubscriber"]);
				unset($_SESSION["editanuser"]);
			
			}
			

			//to be externalized in tao widget

			if (isset($_SESSION["itemcontent"]))
				
			{	

				include($_SESSION["ext"]->physicalLocation.$_SESSION["ext"]->widgets."TAOsaveContent.php");
				$TAOsaveContent=new TAOsaveContent();
				
				$TAOsaveContent=$TAOsaveContent->getOutput($_SESSION["itemcontent"]);
				
				unset($_SESSION["saveContent"]);
				unset($_SESSION["itemcontent"]);
				
			}

			if (isset($_SESSION["xml"]))
				
			{
				
				
				include($_SESSION["ext"]->physicalLocation.$_SESSION["ext"]->widgets."TAOsaveContentfromModel.php");
				$TAOsaveContent=new TAOsaveContentfromModel();
				$TAOsaveContent=$TAOsaveContent->getOutput($_SESSION["xml"], $_SESSION["instance"],$_SESSION["property"]);
				
				unset($_SESSION["xml"]);
				unset($_SESSION["instance"]);
				unset($_SESSION["property"]);
				$output.=$TAOsaveContent;
			}


			if (isset($_SESSION["testcontent"]))
				
			{
				include($_SERVER["DOCUMENT_ROOT"].$_SESSION["ext"]->widgets."TAOTsaveContent.php");
				$TAOtsaveContent=new TAOtsaveContent();
				$TAOtsaveContent=$TAOtsaveContent->getOutput($_SESSION["testcontent"]);
				
				unset($_SESSION["saveContent"]);
				unset($_SESSION["testcontent"]);
				$output.=$TAOtsaveContent;
			}


			
				if (isset($_SESSION["viewitem"]))
				
			{
				$TAOsaveContent=new TAOsaveContent();
				$TAOsaveContent=$TAOsaveContent->getOutput($_SESSION["itemcontent"]);
				
				unset($_SESSION["saveContent"]);
				unset($_SESSION["itemcontent"]);
				$output.=$TAOsaveContent;
			}
			
			if (isset($_SESSION["rights"]))
				
			{
				include("generis_saveRights.php");
				
				$TAOsaveRights=new TAOsaveRights();
				$TAOsaveRights=$TAOsaveRights->getOutput($_SESSION["rights"]);
				
				unset($_SESSION["rights"]);
				
				$output.=$TAOsaveRights;
			}





?>