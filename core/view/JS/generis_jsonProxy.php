<?php
error_reporting(E_ALL);
include_once(dirname(__FILE__).'/../../../common/common.php');

if (!(isset($_SESSION))) {
	session_start();
}

$builder = TreeJsonBuilder::singleton();
$builder->setSessions($_SESSION["session"]);
$query = $_GET["param1"];

echo $builder->getJson($query);;

?>
