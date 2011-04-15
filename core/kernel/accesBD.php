<?php
/*














*/
/**
 * Implements all modification requests to the knowledge base (SQL layer)
 * @package kernel
 * @author Plichart Patrick <patrick.plichart@tudor.lu>
 * @version 1.1
 */
//include(dirname (__FILE__).'/../adodb/adodb.inc.php');


class accesBD
{
	var $con;

	/**
	 * Constructor
	 */
	function accesBD()
	{
	}

	/**
	 * XML RDF Generation from database
	 * For a user or an admin
	 * @access public
	 */
	function microtime_float()
	{
		list($usec, $sec) = explode(" ", microtime());
		return (str_replace(".","",$sec.$usec));
	}
	function getNamespace()
	{


		$query="SELECT value FROM `settings` WHERE `key` = 'NameSpace'  ";
		$result =  $this->con->Execute($query);
		return $result->fields[0];
	}
	function getModelID($modelURI)
	{
		 
		$sqlResult = $this->con->Execute("SELECT modelID FROM models where modelURI='".$modelURI."'");
		return $sqlResult->fields[0];
	}


	/*Rights, Informations about users*/

	function getUmask($bol)
	{
		$mask=array("read"=>"yyy[admin,administrators,authors]","edit"=>"yyy[admin,administrators,authors]","delete"=>"yyy[admin,administrators,authors]");
		$query_priv = "select onAssertPrivileges from _mask where user='".$bol."' and Scope='http://www.w3.org/1999/02/22-rdf-syntax-ns#Statement' and Method='read'";
		$row2= $this->con->Execute($query_priv);
		while (!($row2->EOF))
		{
			$mask["read"]=$row2->fields[0];$row2-> MoveNext();
		}
		$query_priv = "select onAssertPrivileges from _mask where user='".$bol."' and Scope='http://www.w3.org/1999/02/22-rdf-syntax-ns#Statement' and Method='edit'";
		$row2= $this->con->Execute($query_priv);
		while (!($row2->EOF))
		{
			$mask["edit"]=$row2->fields[0];$row2-> MoveNext();
		}
		$query_priv = "select onAssertPrivileges from _mask where user='".$bol."' and Scope='http://www.w3.org/1999/02/22-rdf-syntax-ns#Statement' and Method='delete'";
		$row2= $this->con->Execute($query_priv);
		while (!($row2->EOF))
		{
			$mask["delete"]=$row2->fields[0];$row2-> MoveNext();
		}
		return $mask;

			
	}
	function isAdmin($bol)
	{
		$query="Select admin from ".USERAPPLI." where login='".$bol."'";
		$result =  $this->con->Execute($query);
		return ($result->fields[cvrtFields('ADMIN')]=="1");
	}





	function RemovePropertyValuesforInstance($idinstance,$Idproperty)
	{
		$query="Delete from classinstance WHERE Idinstance='".$idinstance."' AND IdProperty='".$Idproperty."'";
		$this->con->Execute($query);

	}
	function getGroupsSubscribersRightsonResource($parent,$ressource)
	{

		//Short TAO rdf ID
		//error_reporting("^E_NOTICE");
		$shortrdfID =substr($ressource,2);
		$type= substr($ressource,1,1);

		$query="SELECT ID, Name from subscribersgroup where subgroupof ='".$parent."'";
		$row=$this->con->Execute($query);
		$groups =array();

		$subscribers="";
		while (!$row-> EOF)
		{
			switch ($type)
			{
				case "i": {$rights = $this->getGroupsRightsInstance($shortrdfID,$row->fields[0]);break;}
				case "c": {$rights = $this->getGroupsRightsClass($shortrdfID,$row->fields[0]);break;}
				case "p":{$rights = $this->getGroupsRightsProperty($shortrdfID,$row->fields[0]);break;}
			}	$subscribers.="['".$row->fields[1]."','generis_UiControllerHtml.php?showgroupsubscriber=".$row->fields[0]."&rights=".$rights."',";
			$subgroups = $this->getGroupsSubscribersRightsonResource($row->fields[0],$ressource);
			$subscribers.=$subgroups."],";
			$row-> MoveNext();
		}

		return $subscribers;
	}
	/****************************************************************************************
	 *
	 *				PART 3 :
	 *		Business
	 *		Subscribee Management only for administrator
	 *
	 *****************************************************************************************/


	function addSubscribee($url,$login,$password,$type,$md)
	{
		$query="INSERT INTO subscribee (login, password, url,type,DatabaseName) VALUES ('".$login."','".$password."','".$url."','".$type."','".$md."')";
		$a = $this->con->Execute($query);

		return $this->con->Insert_ID();
	}

	function editSubscribee($idsubscribee,$url,$login,$password,$type,$md)
	{
		$query="UPDATE subscribee set login='".$login."', password='".$password."',URL='".$url."',type='".$type."',DatabaseName='".$md."' WHERE IdSub='".$idsubscribee."'";
		$this->con->Execute($query);
	}

	function removeSubscribee($idsubscribee)
	{
		$query="Delete from subscribee where IdSub='".$idsubscribee."'";
		$this->con->Execute($query);
	}

	function getSubscribee()
	{
		$arraygroups=array();
		$query="Select Idsub, login, password,url, type from subscribee";
		$row= $this->con->Execute($query);
		while (!$row-> EOF) {
			$asubscribee =array("Idsub" => $row-> fields[0],"login" => $row-> fields[1], "password" => $row-> fields[2], "url" => $row-> fields[3], "type" => $row-> fields[4]);
			array_push ($arraygroups,$asubscribee);
			$row-> MoveNext();
		}
		return $arraygroups;
	}

	function getSubscribeeaslist()
	{
		$subscribees="['".SUBSCRIBEE."','generis_UiControllerHtml.php?do=addsubscribee',";
		$query="Select Idsub, login, password,url,DatabaseName,type from subscribee";
		$row= $this->con->Execute($query);
		$subscribee="";
		while (!$row-> EOF)
		{
			$subscribee="['".$row-> fields[4]."','generis_UiControllerHtml.php?do=subscribee&param1=".$row->fields[0]."',null],";
			$subscribees.=$subscribee;
			$row-> MoveNext();
		}
		return $subscribees."]";;
	}

	function getSubscribeeasUser()
	{
		$arraygroups=array();
		$query="Select Idsub, login, password,url,DatabaseName,type from subscribee";
		$row= $this->con->Execute($query);
		//return $query;
		while (!$row-> EOF)
		{

			$asubscribee =array("Idsub" => $row-> fields[0], "type" => $row-> fields[5], "url" => $row-> fields[3], "dataBaseName" => $row-> fields[4],"login" => $row-> fields[1],"password" => $row-> fields[2]);
			$i=0;$arraygroups[$row-> fields[0]]=$asubscribee;
			$row-> MoveNext();
		}
		return $arraygroups;
	}


	function getSubscribees($type)
	{
		$arraygroups=array();
		$query="Select Idsub from subscribee where Type='".$type."'";
		$row= $this->con->Execute($query);
		while (!$row-> EOF)
		{array_push ($arraygroups,$row-> fields['Idsub']);
		$row-> MoveNext();}
		return $arraygroups;
	}

	function getSubscribeeURL($value)
	{

		$query="Select url from subscribee where idsub='".$value."'";
		$result= $this->con->Execute($query);
		return $result->fields['url'];
	}

	function getSubscribeemodulename($value)
	{
		$query="Select DatabaseName from subscribee where idsub='".$value."'";
		$result= $this->con->Execute($query);
		return $result->fields['DatabaseName'];

	}


	function getSubscribeeLogin($value)
	{
		$query="Select Login from subscribee where idsub='".$value."'";
		$result= $this->con->Execute($query);
		return $result->fields['Login'];
	}

	function getSubscribeePassword($value)

	{
		$query="Select Password from subscribee where idsub='".$value."'";
		$result= $this->con->Execute($query);
		return $result->fields['Password'];
			
	}

	function getSubscribeesurl($type,$url)
	{

		$domain = substr($url,0,strpos($url,"/middleware/"));

		$modulename = substr($url,strpos($url,"/middleware/")+12);

		$modulename=substr($modulename,0,strpos($modulename,".rdf#"));

		$arraygroups=array();
		if ($type!="any")
		{
			$query="Select Idsub,Login,Password,URL,DatabaseName from subscribee where Type='".$type."' and url LIKE '".$domain."%' and DatabaseName LIKE '%".$modulename."%'";
		}
		else
		{
			$query="Select Idsub,Login,Password,URL,DatabaseName from subscribee where (url LIKE '".$domain."%') OR (url LIKE '".str_replace("localhost","127.0.0.1",$domain)."%') and DatabaseName LIKE '%".$modulename."%'";
		}

			
		$row= $this->con->Execute($query);
		while (!$row-> EOF)
		{
			array_push ($arraygroups,array($row->fields[0],$row->fields[1],$row->fields[2],$row->fields[3],$row->fields[4]));
			$row-> MoveNext();
		}
		//print_r($arraygroups);
		return $arraygroups;
	}
	/****************************************************************************************
	 *
	 *				PART 4 :
	 *		Business
	 *		User Management only for administrator
	 *
	 *****************************************************************************************/
	/*User management
	 *addUser
	 *@param $login user login
	 *@param $password encrypted with md5
	 *@param $umask default u-mask for this user
	 *@param $admin 0 or 1 if admin
	 */

	function getGroup($login)
	{
		$query="Select usergroup from ".USERAPPLI." where login='".$login."'";
		$result= $this->con->Execute($query);
		$groups = explode(",",$result->fields[0]);
			
		return $groups;
	}

	function addUser($login,$password,$umask,$admin,$lastname,$firstname,$email,$company,$deflg,$enabled,$usergroup)
	{
		$query="INSERT INTO ".USERAPPLI."  (login,password,admin,LastName, FirstName,e_mail,company,deflg,enabled,usergroup) VALUES  ('".$login."','".$password."','".$admin."','".$lastname."','".$firstname."','".$email."','".$company."','".$deflg."','".$enabled."','".$usergroup."')";
		$this->con->Execute($query);
	}
	function checkIfLoginalreadyexists($login)
	{
		$query="SELECT * from ".USERAPPLI." where login='".$login."'";
		$result= $this->con->Execute($query);
		$aResult = $result-> GetArray();
		if(count($aResult)>0)
		return"1";
		else
		return "0";
	}

	function editUser($login,$password,$umask,$admin,$lastname,$firstname,$email,$company,$deflg,$enabled,$usergroup)
	{

		//$this->con->debug=true;
		$query="UPDATE ".USERAPPLI." set password='".$password."', admin='".$admin."', LastName='".$lastname."', FirstName='".$firstname."',e_mail='".$email."',company='".$company."',deflg='".$deflg."',enabled='".$enabled."',usergroup='".$usergroup."' WHERE login='".$login."'";

		$this->con->Execute($query);

		$query_priv = "update _mask set onAssertPrivileges='".$umask[0]."' where user='".$login."' and Scope='http://www.w3.org/1999/02/22-rdf-syntax-ns#Statement' and Method='read'";
		$row2= $this->con->Execute($query_priv);

		$query_priv = "update _mask set onAssertPrivileges='".$umask[1]."' where user='".$login."' and Scope='http://www.w3.org/1999/02/22-rdf-syntax-ns#Statement' and Method='edit'";
		$row2= $this->con->Execute($query_priv);

		$query_priv = "update _mask set onAssertPrivileges='".$umask[2]."' where user='".$login."' and Scope='http://www.w3.org/1999/02/22-rdf-syntax-ns#Statement' and Method='delete'";
		$row2= $this->con->Execute($query_priv);
		//$this->con->debug=false;
		//print_r($umask[3]); die();
		foreach ($umask[3] as $scop=>$maskprivileges)
		{
			$pattern_sql="";
			foreach ($maskprivileges as $key =>$maskprivilege)
			{
				if ($maskprivilege[2]=="") $maskprivilege[2]="#117129078458820";
				if ($maskprivilege[3]=="") $maskprivilege[3]="#117129078458820";
				$pattern_sql .= $maskprivilege[2]." ".$maskprivilege[3]." -> read:".$maskprivilege[4][0]." edit:".$maskprivilege[4][1]." delete:".$maskprivilege[4][3]." assert:".$maskprivilege[4][2].",\r\n";
				$scope = $maskprivilege[1];
			}
			if ($pattern_sql!="") $pattern_sql = substr($pattern_sql,0,strlen($pattern_sql)-3);

			$sql = "update _mask set Scope='".$scope."', Method='-', onAssertPrivileges='".$pattern_sql."' where id='".$scop."' and user='".$login."'";

			//$this->con->debug=true;
			$row2= $this->con->Execute($sql);
			//$this->con->debug=false;
		}
		foreach ($umask[4] as $k=>$privilegesonmethod)
		{
			$sql = "update _mask set Scope='".$privilegesonmethod[0]."',Method='".$privilegesonmethod[1]."',onAssertPrivileges='".$privilegesonmethod[3]."' where id='".$privilegesonmethod[2]."'";
			//$this->con->debug=true;
			$row2= $this->con->Execute($sql);

		}
	}
	function addPattern($scope,$user,$method='-')
	{

		$query="select id,onAssertPrivileges from _mask where scope ='".$scope."' and method = '".$method."' and user='".$user."'";
//		$this->con->debug=true;
		$sqlid= $this->con->Execute($query);
		$existsID=false;
		while (isset($sqlid) and (!($sqlid->EOF)))
		{
			$existsID=true;
			$id=$sqlid->fields[0];
			$onAssertPrivileges = $sqlid->fields[1];
			$sqlid->MoveNext();
		}
		if ($existsID)
		{
			if ($method =="-")
			{
				$onAssertPrivileges.=",\r\n# # -> read:y--[] edit:y--[] delete:y--[] assert:y--[]";
			}
			else {$onAssertPrivileges="y--[]";}
			$query = "update _mask set onAssertPrivileges='".$onAssertPrivileges."' where id='".$id."'";

			$this->con->Execute($query);
		}
		else
		{	if ($method =="-")
		{
			$onAssertPrivileges="# # -> read:y--[] edit:y--[] delete:y--[] assert:y--[]";
		}
		else {$onAssertPrivileges="y--[]";}
		$query = "insert into _mask values ('".$user."','".$scope."','".$method."','".$onAssertPrivileges."','','')";
		$this->con->Execute($query);
		}
		return "";
		//$this->con->debug=false;
	}

	function removeUser($login)
	{
		$query="Delete from ".USERAPPLI." where login='".$login."'";
		$this->con->Execute($query);
	}

	function getgroups()
	{
		$query="Select name from grouplocaluser";
		$result= $this->con->Execute($query);
		$groups=array();
		while (!$result-> EOF)
		{	array_push($groups,$result->fields[cvrtFields("NAME")]);
		$result-> MoveNext();
		}

		return $groups;
	}




	function getUserdescription($user)
	{
		$query2="Select login,password, admin, usergroup, lastname, firstname, e_mail, company, deflg, enabled from ".USERAPPLI." where login='".$user."'";
		$row2= $this->con->Execute($query2);
		while (!$row2-> EOF)
		{
			$auser =array(	"login" => $row2->fields['login'],
							"password" => $row2->fields['password'], 
							"admin" => $row2->fields['admin'], 
							"usergroup" => $row2->fields['usergroup'], 
							"group" => $row2->fields['usergroup'], 
							"lastname" => $row2->fields['lastname'],
							"firstname" => $row2->fields['firstname'], 
							"e_mail" => $row2->fields['e_mail'], 
							"company" => $row2->fields['company'], 
							"deflg" => $row2->fields['deflg'], 
							"enabled" => $row2->fields['enabled']
			
			);
			$row2-> MoveNext();
		}
		$mask=array("read"=>"yy-[A,administrators,authors]","edit"=>"yy-[A,administrators,authors]","delete"=>"yy-[A,administrators,authors]");
		$query_priv = "select onAssertPrivileges from _mask where user='".$user."' and Scope='http://www.w3.org/1999/02/22-rdf-syntax-ns#Statement' and Method='read'";
		$row2= $this->con->Execute($query_priv);
		while (!($row2->EOF))
		{
			$mask["read"]=$row2->fields[0];$row2-> MoveNext();
		}
		$query_priv = "select onAssertPrivileges from _mask where user='".$user."' and Scope='http://www.w3.org/1999/02/22-rdf-syntax-ns#Statement' and Method='edit'";
		$row2= $this->con->Execute($query_priv);
		while (!($row2->EOF))
		{
			$mask["edit"]=$row2->fields[0];$row2-> MoveNext();
		}
		$query_priv = "select onAssertPrivileges from _mask where user='".$user."' and Scope='http://www.w3.org/1999/02/22-rdf-syntax-ns#Statement' and Method='delete'";
		$row2= $this->con->Execute($query_priv);
		while (!($row2->EOF))
		{
			$mask["delete"]=$row2->fields[0];$row2-> MoveNext();
		}
		$query_priv = "select * from _mask where user='".$user."' and Scope <> 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Statement'";
		$row2= $this->con->Execute($query_priv);
		while (!($row2->EOF))
		{
			//echo $row2->fields[2];
			if ($row2->fields[2]=="-")
			{
				$privilegesstr = $row2->fields[3];

				$statementpatterns = split(",
", $privilegesstr);
					
				$privileges=array();
				foreach ($statementpatterns as $statmentpattern)
				{
					if ($statmentpattern!="")
					{

						$elements = split(" ",$statmentpattern);
						//error_reporting("^E_NOTICE");
						$privileges[] = array(
															"predicate" => trim($elements[0]),
															"object" => $elements[1],
															"read" => substr($elements[3],5),
															"edit" => substr($elements[4],5),
															"delete" => substr($elements[5],7),
															"assert" => substr($elements[6],7),
															"ID" => $row2->fields[5]
						);
						//error_reporting(E_ALL);
					}
				}
			}
			else
			{
				$privileges =
				array("ID"=> $row2->fields[5],"privileges" =>$row2->fields[3]);
					
			}
			$mask["scopes"][$row2->fields[1]][$row2->fields[2]]=$privileges;

			$row2->MoveNext();
		}

		//print_r($mask);
		$auser["umask"]=$mask;

		return $auser;
	}

	function getgroupsandusers()
	{

		$arraygroups=array();
		$query="Select name from grouplocaluser";
		$row= $this->con->Execute($query);
		//return $query;
		$groups="['".USERS."','generis_UiControllerHtml.php?do=showglobaluser&param1=1',";
		while (!$row-> EOF)
		{
			//$members=array();
			$query2="Select login,password, admin, usergroup, lastname, firstname, e_mail, company, deflg, enabled from ".USERAPPLI." where usergroup LIKE '%".$row->fields[0]."%'";
			//return $query2;
			$row2 =$this->con->Execute($query2);
			$memberstr="";
			while (!$row2-> EOF)
			{
				//$auser =array("login" => $row2[0],"password" => $row2[1], "umask" => $row2[2], "admin" => $row2[3], "usergroup" => $row2[4], "lastname" => $row[5],"firstname" => $row2[6], "e_mail" => $row2[7], "company" => $row2[8], "deflg" => $row2[9], "enabled" => $row2[10]);
				//array_push ($members,$auser);
				$memberstr.="['".$row2->fields[0]."','generis_UiControllerHtml.php?do=showuser&param1=".$row2->fields[0]."',null],";
				$row2-> MoveNext();
			}
			$groups.="['".$row->fields[0]."','generis_UiControllerHtml.php?do=showgroupuser&param1=".$row->fields[0]."',".$memberstr."],";

			//$asubscribee =array("GroupName" => $row[0],"Members" => $members);

			$row-> MoveNext();	//array_push ($arraygroups,$asubscribee);
		}
		return $groups."]";
	}


	function addGroup($name)
	{
		$query="INSERT INTO grouplocaluser (Name) VALUES ('".$name."')";
		$this->con->Execute($query);
	}

	function editGroup($name,$newname)
	{
		$query="UPDATE grouplocaluser set Name='".$newname."' WHERE Name='".$name."'";
		$this->con->Execute($query);
	}
	function removeGroup($name)
	{
		$query="Delete from grouplocaluser WHERE Name='".$name."'";
		$this->con->Execute($query);
	}


	function affiliateUserGroup($login,$idGroup)
	{
		$query="UPDATE user set usergroup='".$idGroup."' WHERE login='".$login."'";
		$this->con->Execute($query);
	}


	/****************************************************************************************
	 *
	 *				PART 5 :
	 *		Business
	 *		Subscriber Management only for administrator
	 *
	 *****************************************************************************************/


	function getGroupsSubscribersMembers($parent,$onlygroups=false)
	{
		$query="SELECT ID, Name from subscribersgroup where subgroupof ='".$parent."'";
		$row=$this->con->Execute($query);
		$groups =array();

		$subscribers="";
		while (!$row-> EOF)
		{	//print_r($onlygroups);
			$x='';
			if (!($onlygroups))
			{
				$x = $this->getAllMembersofSubscribergroup($row->fields[0]);
			}
			//print_r($row);
			$subscribers.="['".$row->fields[1]."','generis_UiControllerHtml.php?do=showgroupsubscriber&param1=".$row->fields[0]."',$x";
			$subgroups = $this->getGroupsSubscribersMembers($row->fields[0]);
			$subscribers.=$subgroups."],";



			$row-> MoveNext();
		}

		return $subscribers;
	}



	/*
	 *Not used
	 */
	function getPHPGroupsSubscribersMembers($parent,$onlygroups=false)
	{
		$query="SELECT ID, Name from subscribersgroup where subgroupof ='".$parent."'";
		$row=$this->con->Execute($query);
		$groups =array();

		$subscribers="";
		while (!$row-> EOF)
		{	//print_r($onlygroups);
			if (!($onlygroups))
			{
				$x = $this->getAllMembersofSubscribergroup($row->fields[0]);
			}
			//print_r($row);

			$subgroups = $this->getGroupsSubscribersMembers($row->fields[0]);

			$agroup =array("Id" => $row->fields[0], "Name" => $row->fields[1],
			"Members" => $x,"Subgroups" => $this->getPHPGroupsSubscribersMembers($row->fields[0])
			);
			array_push($groups, $agroup);
			$row-> MoveNext();
		}

		return $groups;
	}

	function getRecursivesubgroups($idgroup)
	{
		$arraygroups[] = $idgroup;
		$query="SELECT ID from subscribersgroup where subgroupof='".$idgroup."'";
		$row=$this->con->Execute($query);
		while (!$row-> EOF)
		{
			$temp =  $this->getRecursivesubgroups($row->fields[0]);
			while(list($x,$gr)=each($temp))
			{array_push ($arraygroups,$gr);}
			$row-> MoveNext();
		}
		return $arraygroups;
	}
	/*
	 * Affiliate a group of subscribers to anothers, to edit(change) it, just call affiliate
	 */
	function affiliateGroupGroup($idgroupFather,$idGroupSon)

	{
		$query="UPDATE subscribersgroup set subgroupof='".$idgroupFather."' WHERE id='".$idGroupSon."'";
		$this->con->Execute($query);
	}
	function addSubscriber($login,$password,$enabled)
	{

		$query="INSERT INTO subscriber (Login,Password,enabled) VALUES ('".$login."','".$password."','".$enabled."')";
			
		$this->con->Execute($query);

		$query="Select Id from subscriber where Login='".$login."'";
		$result = $this->con->Execute($query);
		$class_id = $result->fields[0];

		return $class_id ;
	}

	function editSubscriber($idsubscriber, $login,$password,$enabled)
	{
		$query="UPDATE subscriber set Login='".$login."', Password ='".$password."' , Enabled ='".$enabled."'  WHERE id='".$idsubscriber."'";
		$this->con->Execute($query);
	}

	function removeSubscriber($idsubscriber)
	{

		$query="Delete from subscriber where ID='".$idsubscriber."'";
		$this->con->Execute($query);

	}
	function addSubscriberGroup($name)
	{
		$query="INSERT INTO subscribersgroup (Name) VALUES ('".$name."')";
		$this->con->Execute($query);

		$query="Select Id from subscribersgroup where Name='".$name."'";
		$result = $this->con->Execute($query);
		$class_id = $result->fields[0];


		return $class_id;
	}

	function editSubscriberGroup($idGroup,$name)
	{
		$query="UPDATE subscribersgroup set Name='".$name."' WHERE id='".$idGroup."'";
		$this->con->Execute($query);
	}

	function removeSubscriberGroup($idGroup)
	{
		if ($idGroup!=1)
		{
		 $query="Delete from subscribersgroup where ID='".$idGroup."'";
		 $this->con->Execute($query);
		}
	}

	function affiliateSubscriberGroup($idsubscriber,$idGroup)
	{
		$query="UPDATE subscriber set ismember='".$idGroup."' WHERE id='".$idsubscriber."'";
		$this->con->Execute($query);
	}


	function getAllMembersofSubscribergroup($idGroup)
	{

		$arraygroups = array();
		$query="SELECT ID, login from subscriber where ismember ='".$idGroup."'";
		$row=$this->con->Execute($query);

		$members="";
		while (!$row-> EOF)
		{
			array_push ($arraygroups,
			array("SubscriberId" => $row->fields[0], "SubscriberLogin" => $row->fields[1]));
			$members.="['".$row->fields[1]."','generis_UiControllerHtml.php?do=showsubscriber&param1=".$row->fields[0]."',null],";

			$row-> MoveNext();
		}
		return $members;
	}
	function getSubscriberDescription($idsubscriber)
	{
		$return=Array();

		$query="SELECT ID, login, Password, LastVisit,enabled, ismember from subscriber where ID ='".$idsubscriber."'";
		$row=$this->con->Execute($query);
		$return["description"]=array($row->fields[0],$row->fields[1],$row->fields[2],$row->fields[3],$row->fields[4],$row->fields[5]);

		$query="SELECT ID, Name from subscribersgroup";
		$row=$this->con->Execute($query);

		while (!$row-> EOF)
		{	$return["groups"][]=array($row->fields[0],$row->fields[1]);
		$row-> MoveNext();
		}


		return $return;
	}


	/****************************************************************************************
	 *
	 *				PART 5 :
	 *		Parameters
	 *		Timeout : only administrator
	 *		Def lg : for a user : see user management
	 *				 global : only administrator
	 *
	 *****************************************************************************************/

	function setTimeout($timeout)
	{
		$query="UPDATE `settings` set value='".$timeout."' where key = 'Timeout' ";
		$this->con->Execute($query);
	}
	function setMyDeflg($user,$lg)
	{
		$query="UPDATE ".USERAPPLI." set Deflg='".$lg."'  where login='".$user."'";
		$result=$this->con->Execute($query);
		return $result->fields[0];
	}
	function getMyDeflg($user)
	{
		$query="Select Deflg from ".USERAPPLI." where login='".$user."'";
		$result=$this->con->Execute($query);
		return $result->fields[0];
	}

	function getTimeout()
	{
		$query="SELECT value FROM `settings` WHERE `key` = 'Timeout'  ";
		$result=$this->con->Execute($query);
		return $result->fields[0];

	}

	function getTypeModule()
	{

		$query="SELECT value FROM `settings` WHERE `key` = 'Moduletype'  ";
		$result=$this->con->Execute($query);
		return $result->fields[0];
			
	}


	function getModuleDeflg()
	{
		$query="SELECT value FROM `settings` WHERE `key` = 'Deflg'  ";
		$result=$this->con->Execute($query);
		return $result->fields[0];
	}
	function setModuleDeflg($lg)
	{
		$query="UPDATE `settings` SET `value`='".$lg."' WHERE `key` = 'Deflg' ";
		$this->con->Execute($query);
	}
} //end of class
?>
