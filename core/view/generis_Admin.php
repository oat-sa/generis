<?php
/*
	
   
   
    

    
    
    
    

    
    
    

*/   
/**
* Implements user interfaces to genrate admin tree with subscribers, users and subscribeees
* @author patrick
* @package usergui
*/

class generis_Admin
{
	function generis_Admin()
	{
	}
	function getOutput($admin_Object)
	{	
		loadGUIlanguage();
		if (!(isset($_SESSION))) {session_start();}
		$output="";
		$subscribers="";
		$subscribee="";
		$result="";

		if (isset($_SESSION["ok"])) 
		{
				$output='<br><a href=index.php?generis_admin=stop target=_top>Back to ressource management</a><br><br><script language="JavaScript" src="tree.js"></script>
				<script language="JavaScript" src="tree_items.js"></script>
				<script language="JavaScript" src="tree_tpl.js"></script>';
				
				if ($admin_Object=="user")
				{	
				$y = calltoKernel('getgroupsmembers',array($_SESSION["session"]));
				$tree= $y["pDescription"];
				}

				if ($admin_Object=="subscription")
				{
				$y = calltoKernel('getSubscribeeaslist',array($_SESSION["session"]));
				
				$tree= $y["pDescription"];
				}

				if ($admin_Object=="subscriber")
				{
					
				$subscribers = calltoKernel('getGroupsSubscribersMembers',array($_SESSION["session"]));
				$tree="['".SUSCRIBERS."','generis_UiControllerHtml.php?addgroupsubscriber=1',".$subscribers["pDescription"]."]";
				}

				
$globaltree="[['".ADMIN."','www.tao.lu',".$tree."]]";
				
				


				$output.='<script language="JavaScript">
				var toOpen=new Array("");
				var setcheckbox="0";
				var target="";
				var option="";
				new tree ('.$globaltree.', TREE_TPL);</script>';

				
				
				
			
				
		}/*FIN DU SESSION OK*/
		
		

		return $output;
	}
	   
	 


}
?>