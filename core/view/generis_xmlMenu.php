<?php
/*
	
   
   
    

    
    
    
    

    
    
    

*/
/**
* Implements left frame allowing navigation among several modules
* @package usergui
* @author Plichart Patrick <patrick.plichart@tudor.lu>
* @version 1.1
*/

header("Content-Type: text/xml");
if (!(isset($_SESSION))) {session_start();}
include_once("generis_utils.php");
loadGUIlanguage();
//print_r($_SESSION);

foreach ($_SESSION["All_in_one_modules"] as $key => $val)

{
	
	$links[]=array($val,$key);
}


$xmlb='<?xml version=\'1.0\' ?>
<menu absolutePosition="auto" mode="classic" maxItems="50" xname="" mixedImages="yes" type="a1">
	<MenuItem name="'.MODULES.'" id="main_file" withoutImages="true">
';
foreach ($links as $k=>$v)
{

//$v[0]=str_replace('?','firstVar',$v[0]);
//$temp=str_replace('&','nextVar',$v[0]);
$xmlb.='<MenuItem name="'.$v[1].'" id="'.$v[1].'" href="'.str_replace('&','&#38;',$v[0]."&killsession=1").'" />
';


}
$xmlb.='<divider id="div_2"/>
	<MenuItem name="'.MODULESMGT.'" id="MModules"/>
	</MenuItem>
	<MenuItem name="'.SETTINGS.'" id="settings" mixedImages="yes">
	
	<MenuItem name="'.UILG.'" id="uilanguage" withoutImages="true">';

$lgfiles =getlgfiles();
foreach ($lgfiles as $h=>$v)
			{ 
				
			if ($_SESSION["guilg"]==$v)	
				{$xmlb.='<MenuItem name="&lt;b&gt;'.$v.'&lt;/b&gt;"  id="'.$v.'" href="./index.php?guilg='.$v.'" />';}
			else
			$xmlb.='<MenuItem name="'.$v.'"  id="'.$v.'" href="./index.php?guilg='.$v.'" />';
			
			
			}

	

$xmlb.='	</MenuItem>
<MenuItem name="Settings"  id="settings" href="./index.php?do=settings" />
	<MenuItem name="Tree Filter" id="uilanguage" withoutImages="true">
		<MenuItem name="View ALL"   href="./index.php?filter=2" />
		<MenuItem name="View Classes Properties"   href="./index.php?filter=1" />
		<MenuItem name="View Classes"  href="./index.php?filter=0" />
	</MenuItem>
	<MenuItem name="Tree root" id="uilanguage" withoutImages="true">
		<MenuItem name="rdf:resource"   href="./index.php?root=0" />
		<MenuItem name="generis:generis_resource"   href="./index.php?root=1" />
	</MenuItem>
	</MenuItem>';
	

if (
			calltoKernel('isAdmin',array($_SESSION["session"]))
			) 	
			
			{$xmlb.='	<MenuItem name="'.ADMIN.'" id="admin" withoutImages="true">
		

		<MenuItem name="'.USERMGT.'" id="usemgt"  href="./index.php?generis_admin=user"/>
		<MenuItem name="'.SUBSCRIBEEMGT.'" id="scrmgt"  href="./index.php?generis_admin=subscription"/>
		<MenuItem name="'.SUBSCRIBERMGT.'" id="scrmgt"  href="./index.php?generis_admin=subscriber"/>
		</MenuItem>';
			}


$xmlb.='
	<!--<MenuItem name="'.HELP.'" id="help" withoutImages="true"/>-->
 <MenuItem name="'.HELP.'" id="help" withoutImages="true">
  <MenuItem name="'.HELPTPS.'" id="helptps" />
  <MenuItem name="'.ABOUT.'" id="about"  />
 </MenuItem>
</menu>
';
echo $xmlb;

		
?>