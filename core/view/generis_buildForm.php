<?php

if (0 > version_compare(PHP_VERSION, '5')) {
    trigger_error('This application requires PHP version 5', E_USER_ERROR);
}
include_once("../../common/common.php");

set_include_path("../../");


//creates the api instance
core_control_FrontController::connect();


$aClass  = new core_kernel_classes_Class($_GET["classUri"]);
$hclC = $aClass->createHyperClass();


$hcl = new core_kernel_classes_HyperClass($hclC->uriResource);
$hcl->feed();
$hcl_hyperview = new core_view_classes_HyperView($hcl);
$xul = $hcl_hyperview->getForm();




header ("Content-type: application/vnd.mozilla.xul+xml; charset=iso-8859-15");
header ("title: HyperClass");
header ("id: main");
echo $xul;
?>