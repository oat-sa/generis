<?php
/*
	
   
   
    

    
    
    
    

    
    
    

*/
/**
*User interface to retrieve login and password of user
*@author Plichart Patrick <patrick.plichart@tudor.lu>
* @version 1.1
* @package usergui
*/
	   

class TAO_Pane_Authenticate
{
	function TAO_Pane_Authenticate()
	{
		
	}
	function getOutput()
	{	
		$output="";
		
		
		
		
		
		
			if (!(isset($_SESSION["ok"])))

			{

				if ( (!(isset($_SESSION["uname"]))))
				  {			
						
							
							$output.="<br><div class=\"Date\">";
							$output.= (date("l d M Y")."   &nbsp;&nbsp;| &nbsp;&nbsp;");
							$output.= (date("H : i : s"));

							$output.="</div>";
							$output.="<br> The session has expired, please connect on generis portal";
							/*
							$output.= "<br>
							<div class=\"Authenticationform\" style=\"width:50%\">
								".AUTHFORM."
							</div><br><div class=\"AuthBloc\" style=\"width:50%\">
							<FORM action=index.php target=_top method=post><table CELLSPACING=15 border=0 >
							<tr><td><div class=\"AUTHINFOS\" style=\"color:white\">".USERNAME."</div></td><td><INPUT maxLength=25 size=20 name=uname></td></tr>
							<tr><td><div class=\"AUTHINFOS\" style=\"color:white\">".PASSWORD."</div></td><td><INPUT type=password maxLength=20 size=20 name=pass></td></tr>
							<tr><td><div class=\"AUTHINFOS\" style=\"color:white\">Module</div></td><td><INPUT maxLength=25 size=20 name=bdmodule value=anaxagora-km></td></tr>
							<tr><td><div class=\"AUTHINFOS\" style=\"color:white\">".LANGUAGE."</div></td><td><select name=datalg>
							<option value=XX>".PICKLANGUAGE."
							<option value=FR>French<option value=EN>English<option value=DU>Dutch</select></td></tr><tr><td><div class=\"AUTHINFOS\" style=\"color:white\">".xFUNCTION."</div></td><td><select name=function><option value=1>".PICKROLE."<option value=1>as admin<option value=0>as user</select></td></tr><tr><td></td><td><br><INPUT type=submit style=\"border: 1px solid silver;\" value=Login></td><td></td></tr></table>			</FORM></div>";
							*/
					}
			  else
					{		
							
					}
			}
		
		return $output;
	   
}

}
?>