<?php
/*
	
   
   
    

    
    
    
    

    
    
    

*/
/**
* USer interfaces to add a user in knowledge base
* @author patrick
* @package usergui
*/
require_once("generis_ConstantsOfGui.php");
include("generis_utils.php");

/*
	*Private
	*@param $mask mask in the form MMGGOOSUBR ex : 1020220012
	*@return array( ["MyRights"] (["00"] => "" [01] => "SELECTED"), etc...
	*/
	

		if (!(isset($_SESSION))) {session_start();}
		loadGUIlanguage();
		$output=HEAD."<BODY class=paneIframe>";
		
		$output.="<FORM action=./index.php name=adduser target=_top method=post>".TABLEHEADER;
	
			
			// ancienne version
			//$groups = getGroups($_SESSION["session"]);
			// appel webservices
			$groups = calltoKernel('getGroups',array($_SESSION["session"]));
			
			$groups=$groups["pDescription"];
			
			
			
			$output.='<center>';
			$output.='<tr><td colspan=3><Hr></td></tr>';
			$output.='<tr><td colspan=3><div class="Title">User Description</div></td></tr>';
			$output.='<tr><td colspan=3><Hr></td></tr>';

			$output.='<tr><td><div class="AUTHINFOS">Login</div></td><td><input type=text name=editanuser[login]></td></tr>';
			
			$output.='<tr><td><div class="AUTHINFOS">Group</div></td><td><select name=editanuser[group]>';
			
			foreach ($groups as $key=>$val)

			{$output.="<option>$val";}
			
			
			$output.='</select></td></tr>';
			
			$output.='<tr><td><div class="AUTHINFOS">Password</div></td><td><input type=text name=editanuser[pass1]></td></tr>';
			
			
			//$output.='<tr><td><div class="AUTHINFOS">umask</div></td><td><input type=text name=editanuser[umask] ></td></tr>';
			

			
			$output.="<tr><td>MyRights : </td><td colspan=4>
			<input type=radio name=editanuser[myrights]  value=00>None
			<input type=radio name=editanuser[myrights]  value=10>Overview	
			<input type=radio name=editanuser[myrights]  value=20>Read&nbsp;<input type=radio name=editanuser[myrights] CHECKED value=22>Read&Write
			</td></tr>";
			
			$output.="<tr><td>MyGroupRights : </td><td>
			<input type=radio name=editanuser[mygroup]  value=00>None
			<input type=radio name=editanuser[mygroup]  value=10>Overview	
			<input type=radio name=editanuser[mygroup]  value=20>Read&nbsp;<input type=radio name=editanuser[mygroup] CHECKED value=22>Read&Write
			</td></tr>";

			$output.="<tr><td>Other : </td><td>
			<input type=radio name=editanuser[other]  value=00>None
			<input type=radio name=editanuser[other]  value=10>Overview
			<input type=radio name=editanuser[other]  value=20>Read&nbsp;<input type=radio name=editanuser[other] CHECKED value=22>Read&Write
			</td></tr>";
			
			$subscribers = calltoKernel('getGroupsSubscribersMembers',array($_SESSION["session"],array(1),true));
$subscribers="['".SUSCRIBERS."','help.php?word=subscribers',".$subscribers["pDescription"]."]";
$globaltree="[".$subscribers."]";
			error_reporting(0);
			$output.='<script language="JavaScript" src="tree.js"></script>
				<script language="JavaScript" src="tree_items.js"></script>
				<script language="JavaScript" src="tree_tpl.js"></script>
			<tr><td>'.SUSCRIBERS.'</td><td><script language="JavaScript">
			var toOpen=new Array("2");var option="";
			var setcheckbox="2";
			var checkedgroup="'.$mask["selectedsubscriber"].'";
			new tree ('.$globaltree.', TREE_TPL);
			trees[0].toggle(1);
			trees[0].toggle(2);
			</script></td><td>
			<input type=radio name=editanuser[subscribers] '.$mask["subscribers"]["0"].' value=0>None<br>
			<input type=radio name=editanuser[subscribers] '.$mask["subscribers"][1].' value=1>Overview<br>	
			<input type=radio name=editanuser[subscribers] '.$mask["subscribers"][2].' value=2>Read</td></tr>';	


			
			$output.='<tr><td><div class="AUTHINFOS">Admin</div></td><td><input type=radio name=editanuser[isadmin] value=1>Is Admin<input type=radio name=editanuser[isadmin] value=0>Is User</td></td></tr>';
			
			$output.='<tr><td><div class="AUTHINFOS">Last Name</div></td><td><input type=text name=editanuser[lastname] ></td></tr>';

			$output.='<tr><td><div class="AUTHINFOS">First Name</div></td><td><input type=text name=editanuser[firstname] ></td></tr>';
			
			$output.='<tr><td><div class="AUTHINFOS">Company</div></td><td><input type=text name=editanuser[company] ></td></tr>';

			$output.='<tr><td><div class="AUTHINFOS">Email</div></td><td><input type=text name=editanuser[email] ></td></tr>';

			$output.='<tr><td><div class="AUTHINFOS">Default Language</div></td><td><input type=text name=editanuser[deflg] ></td></tr>';
			$addUser=getButtonimage("Apply");
			$output.='<tr><td colspan=3><input type=image src='.$addUser.' name=addUser value=Apply></td></tr>';
			
			
			

		$output.=TABLEFOOTER;
		$output.="</form>";
	
		echo $output;
?>