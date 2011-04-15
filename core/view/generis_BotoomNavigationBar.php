<?php

/**
* User Gui bottom fram : show available and selected gui/data language
* shows messages
* @author patrick
* @package usergui
*/	
	
	
	function adddatalg()
	{
		$fp = fopen("./lg/GUI.php", "r");
		$content = fread($fp,8096);
		fclose($fp);
		$tab=unserialize($content);
		$tab[]=$_POST["addlg"];$file = fopen("./lg/GUI.php", "wb");
	    fwrite($file, serialize($tab). "\n");
	    fclose($file);
	}
	
		$output="";
		
		if (!(isset($_SESSION))) {session_start();}
		

		
		$output.= '
		<table border=0 width=100% cellspacing=0 cellpadding=0>
		<tr class=darkpurple height=100%>';
		
		$output.="
		<td  width=3% ></td>
		<td width=10%><span class=msg><img src=./icons/b_search.png>&nbsp;".SEARCH."&nbsp;</span></td><td width=10%><form method=post action=generis_search.php target=pane><input type=text size=12 MAXLENGTH=20 name=instanceCreation[properties]><input type=hidden size=12  name=fulltext>
		<input type=hidden class=\"bouton\" align=top name=cft value=search>
		</form></td>";
		//print_r($_POST);

		if (!($external))
		{
		
		$output.="<td width=4%></td>";
		
		
		$output.=  "<td width=45%><center>";
		if ((isset($_SESSION["msg"])))
			{
		$output.="<span class=msg>".str_replace(" ","&nbsp;",$_SESSION["msg"])."</span>";
			
			}
		}
		$output.="</center></td><td width=9%></td>";
		
		$output.=  "<td width=50%>";
		$output.="<table width=100% border=0 class=bottomrighttable cellspacing=0 cellpadding=0><tr><td width=34px class=arightcorner>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td align=left >";
		
		$output.="<table border=0 width=100% class=bottomrighttable cellspacing=0 cellpadding=0><tr height=22px>";
		
		if (isset($_SESSION["ok"])) 
		{
		$output.="
		<td><span class=guiLabel><img src=./icons/s_lang.png>".DATALANGUAGE."</span></td>";
		$output.="<td></td>";
		
		
		
		
		if (isset($_POST["addlg"])) {adddatalg();}
		
				
		/*GETDEFAULT LANGUAGE*/
		$t = calltoKernel('getModuleDeflg',array($_SESSION["session"]));
		
		$deflg= $t["pDescription"];
		$_SESSION["deflg"]=$deflg;	
		$datalgs = calltoKernel('getLgs',array($_SESSION["session"]));
		
		foreach ($datalgs as $key => $val)
			{
				if ($val!="")
				{
					$output.="<td>";
					if ($_SESSION["datalg"]==$val)
					{$image="<span class=datalg_selected>".strtoupper($val)."</span>";}
					
					else 
					{
						if ($deflg==$val)
						{$image="<span class=datalg_default>".strtoupper($val)."</span>";}
						else {$image="<span class=datalg>".strtoupper($val)."</span>";}
					}
					
					$output.="<a href=./index.php?datalg=$val target=_top>$image</a>";
					$output.="</td><td></td>";
				}
			}
		
		}
		$output.=  "<td width= 35%><form method=post action=generis_addlanguage.php><input type=text size=1 MAXLENGTH=2 name=addlg><input type=submit class=\"bouton\" align=top name=lg value=+></form></td></tr><tr height=3px><td colspan=256 class=brown></td></tr></table>";
		$output.="</td></tr></table>";
		$output.=  "</td></tr></table>";
		
		
		echo $output;
	
	   
		
		
		


?>