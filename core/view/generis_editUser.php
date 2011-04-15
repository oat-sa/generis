<?php
/*
	
   
   
calls generis kernel to update user settings and mask
  
    

*/

	
	function generis_edituser($ressource)
	{
		//print_r($ressource["resrights"]["methods"]);die();
		if ($_SESSION["EditUser"]=="Create")
			{
				
				calltoKernel('addPattern',array($_SESSION["session"], $ressource["newresrights"]));
			}
		if ($_SESSION["EditUser"]=="CreateMethod")
			{
				
				calltoKernel('addPattern',array($_SESSION["session"], $ressource["newresmethrights"],"setsubClass"));
			}
		
		$output="";
		foreach ($ressource["pass1"] as $key=>$val)
		{
		if ($val=="CRYPTED") 
			{$pass=$key;} else {$pass=md5($val);}
		}
		
		$groups ="";	
		error_reporting(0);
		foreach ($ressource["group"] as $k=>$agroup) {$groups.=$agroup.",";} $groups = substr($groups,0,strlen($groups)-1);
		//if (isset($ressource["subscribers"])) {$mask.=$ressource["subscribers"];} else {$mask="";}
		
		
		if (isset($ressource["rights"]))
		{
				$reqmask = $ressource["rights"];
				$readmask = $reqmask["author"][0].$reqmask["users"][0].$reqmask["anonymous"][0];
				$editmask = $reqmask["author"][1].$reqmask["users"][1].$reqmask["anonymous"][1];
				$deletemask = $reqmask["author"][2].$reqmask["users"][2].$reqmask["anonymous"][2];
				$readgroups="[";$Editgroups="[";$Delgroups="[";
				foreach ($reqmask["groups"] as $groupid => $privilege)
					{
						if ($privilege == "r")
						$readgroups.=$groupid.",";
						if ($privilege == "re")
						{
						$readgroups.=$groupid.",";	$Editgroups.=$groupid.",";
						}
						if ($privilege == "red")
						{
						$readgroups.=$groupid.",";	$Editgroups.=$groupid.",";	$Delgroups.=$groupid.",";
						}
					}
				
				if ($readgroups!="[") {$readgroups = substr($readgroups,0,strlen($readgroups)-1);} 
				if ($Editgroups!="[") {$Editgroups = substr($Editgroups,0,strlen($Editgroups)-1);} 
				if ($Delgroups!="[") {$Delgroups = substr($Delgroups,0,strlen($Delgroups)-1);} 
				$readgroups.="]";$Editgroups.="]";$Delgroups.="]";
				$r = $readmask.$readgroups;
				$e = $editmask.$Editgroups;
				$d = $deletemask.$Delgroups;
	
		}
		$statementmask = array($r,$e,$d);
		$maskprivileges=array();
		
		//print_r($ressource["resrights"]);
		$accessprivilegesformethods=array();
		if (isset($ressource["resrights"]))
		{
				foreach($ressource["resrights"] as $maskid => $privsOnStatements)
				{
				if ($maskid!="methods")
				{
				$onepattern="";
				foreach ($privsOnStatements as $statementprivID=> $aprivonstatement)
					{
							
							$readmask = $aprivonstatement["author"][0].$aprivonstatement["users"][0].$aprivonstatement["anonymous"][0];
							$editmask = $aprivonstatement["author"][1].$aprivonstatement["users"][1].$aprivonstatement["anonymous"][1];
							$assertmask = $aprivonstatement["author"][2].$aprivonstatement["users"][2].$aprivonstatement["anonymous"][2];
							$deletemask = $aprivonstatement["author"][3].$aprivonstatement["users"][3].$aprivonstatement["anonymous"][3];
							$readgroups="[";$Editgroups="[";$Delgroups="[";$Asgroups="[";
							
							foreach ($aprivonstatement["groups"] as $groupid => $privilege)
								{
									if ($privilege == "r")
									$readgroups.=$groupid.",";
									if ($privilege == "re")
									{
									$readgroups.=$groupid.",";	$Editgroups.=$groupid.",";
									}
									if ($privilege == "rea")
									{
									$readgroups.=$groupid.",";	$Editgroups.=$groupid.",";	$Asgroups.=$groupid.",";
									}
									if ($privilege == "red")
									{
									$readgroups.=$groupid.",";	$Editgroups.=$groupid.",";	$Asgroups.=$groupid.",";$Delgroups.=$groupid.",";
									}
								}
							
							if ($readgroups!="[") {$readgroups = substr($readgroups,0,strlen($readgroups)-1);} 
							if ($Editgroups!="[") {$Editgroups = substr($Editgroups,0,strlen($Editgroups)-1);} 
							if ($Asgroups!="[") {$Asgroups = substr($Asgroups,0,strlen($Asgroups)-1);} 
							if ($Delgroups!="[") {$Delgroups = substr($Delgroups,0,strlen($Delgroups)-1);} 
							$readgroups.="]";$Editgroups.="]";$Delgroups.="]";$Asgroups.="]";
							$r = $readmask.$readgroups;
							$e = $editmask.$Editgroups;
							$a = $assertmask.$Asgroups;
							$d = $deletemask.$Delgroups;
							
							$maskprivileges[$maskid][]=array($maskid,$aprivonstatement["Scope"],$aprivonstatement["Predicate"],$aprivonstatement["Object_R"],array($r,$e,$a,$d));
							

					}
				}//endif

				else
					//privileges on methods 
					{
						foreach ($privsOnStatements as $idmethodpriv => $methodpriv)
							{
								$scopemp=$methodpriv["Scope"];
								$methodmp=$methodpriv["Method"];
								$id=$idmethodpriv;
								$privileges = $methodpriv["author"].$methodpriv["users"].$methodpriv["anonymous"];
								$methodgroups ="[";
								foreach ($methodpriv["groups"] as $groupk => $groupv) 
									{
										if ($groupv!="-") {$methodgroups.=$groupv.",";} 
									}
								if ($methodgroups!="[") {$methodgroups = substr($methodgroups,0,strlen($methodgroups)-1);} 
								$methodgroups .="]";
								$privileges.=$methodgroups;
								$accessprivilegesformethods[] =array($scopemp,$methodmp,$id,$privileges);
							}
					}
				}//endforeach
	
		}
		
		
		
		
	$statementmask[]=$maskprivileges;
	$statementmask[]=$accessprivilegesformethods;
	//print_r($statementmask);die();

	
	//print_r(array($r,$e,$d));

	//die();
	calltoKernel('editUser',array($_SESSION["session"],array($ressource["login"]),array($pass),array($statementmask),array($ressource["isadmin"]),array($ressource["lastname"]),array($ressource["firstname"]),array($ressource["email"]),array($ressource["company"]),array($ressource["deflg"]),array(1),array($groups)));

		error_reporting(0);
		$_SESSION["msg"]=USEREDITED;
		
		return $output;
		
	
	}
	   
	

?>