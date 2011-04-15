<?php

define("VIEWMODE", "View", true);
define("DATEDOC", "date", true);
define("SIZEDOC", "size", true);
function cvrtFields($string) {return strtolower($string);}

$dir = dirname (__FILE__);
error_Reporting(E_ALL);
include_once($dir."/../../includes/adodb5/adodb.inc.php");
include_once($dir."/../kernel/accesBD.php");
include_once($dir."/../kernel/modelManager.php");
include_once($dir."/../kernel/cache.php");
include_once($dir.'/../../common/common.php');
include_once($dir."/../kernel/serverModule.php");
include_once($dir."/../kernel/model.php");
include_once($dir."/../kernel/rdfmodel.php");
include_once($dir."/../kernel/rdfsmodel.php");



?>