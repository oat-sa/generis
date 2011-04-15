<?
	function getModulesof($login,$password)
	{
	$con = NewADOConnection(SGBD_DRIVER);
	$con->Connect(DATABASE_URL, DATABASE_LOGIN, DATABASE_PASS, "generisportal");
    $con->debug = false;

	$query="Select generisPass from generisuser where generisLogin='".$login."'";
	  
	   $result =  $con->Execute($query);
	   while (!$result-> EOF)
		{$row=$result->fields;
	    if   ($password == $row[0])
		   {;} else {return false;}
		$result-> MoveNext();
		}
	
	$resultx=array();
	$query="Select generisModuleName,ModuleLogin,ModulePass,url,enabled,ID from generismodules where generisLogin='".$login."'";
	  
	   $result =  $con->Execute($query);
	   while (!$result-> EOF)
		{$row=$result->fields;
			$resultx[] = $row;$result-> MoveNext();
		}
	
	return $resultx;

	}
	function getModules($id)
	{
	$con = NewADOConnection(SGBD_DRIVER);
	$con->Connect(DATABASE_URL, DATABASE_LOGIN, DATABASE_PASS, "generisportal");
    $con->debug = false;
	$query="Select generisPass from generisuser where generisLogin='".$login."'";
	$result =  $con->Execute($query);
	   while (!$result-> EOF)
		{$row=$result->fields;
	    if   ($password == $row[0])
		   {;} else {return false;}
		$result-> MoveNext();
		}
	
	$resultx=array();
	$query="Select generisModuleName,ModuleLogin,ModulePass,url,enabled,ID from generismodules where ID='".$id."'";
	  
	   $result =  $con->Execute($query);
	   while (!$result-> EOF)
		{$row=$result->fields;
			$resultx[] = $row;$result-> MoveNext();
		}
	
	return $resultx;

	}
	function updatemodule($ID,$modulename,$Modulelogin,$Modulepass,$ModuleURL,$checked)
	{
		$con = NewADOConnection(SGBD_DRIVER);
		$con->Connect(DATABASE_URL, DATABASE_LOGIN, DATABASE_PASS, "generisportal");
		$con->debug = false;
		$query="update generismodules set
		generisModuleName='".$modulename."',
		ModuleLogin='".$Modulelogin."',
		ModulePass='".$Modulepass."',
		url='".$ModuleURL."',
		enabled='".$checked."'
		where ID='".$ID."'";
		
		$result =  $con->Execute($query);
	}
	function affectnewModule($login,$modulename, $log, $pass,$url="",$checked="")
	{$con = NewADOConnection(SGBD_DRIVER);
		$con->Connect(DATABASE_URL, DATABASE_LOGIN, DATABASE_PASS, "generisportal");
		$con->debug = false;
		
		$query="INSERT INTO generismodules (generisModuleName,generisLogin, ModuleLogin,ModulePass,URL,enabled) VALUES ('".$modulename."','".$login."','".$log."','".$pass."','".$url."','".$checked."')";
		$result =  $con->Execute($query);
	}
	function unaffectModule($ID,$login)
	{	$con = NewADOConnection(SGBD_DRIVER);
		$con->Connect(DATABASE_URL, DATABASE_LOGIN, DATABASE_PASS, "generisportal");
		$con->debug = false; 
		$query="Delete from generismodules where ID='".$ID."' and generisLogin='".$login."'";
		$result =  $con->Execute($query);
	}
?>