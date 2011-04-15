<?php

require_once ("../../common/common.php");

error_reporting(E_ALL);

//$GLOBALS["EVENTLOG"]["DATABASE"] = "webservice";

$sender  		= @$_REQUEST["sender"];
$comment 		= @$_REQUEST["comment"];
$coordinates 	= @$_REQUEST['coordinates'];
$domElement		= @$_REQUEST['domElement'];
$key			= @$_REQUEST['key'];

$logger = core_kernel_events_EventLogger::getInstance();

if (!isset($sender) && !isset($comment)) {
  // Minimal parameters count not received.
  exit();
}

if (!empty($coordinates) && !empty($domElement))
{
	// Register a mouse event.
	$event = new core_kernel_classes_JSMouseEvent($sender, $comment, 
												  explode(',', $coordinates),
												  $domElement);	
	$logger->trigEvent($event);
}
else if (!empty($key))
{
	// Register a keyboard event.
	$event = new core_kernel_classes_JSKeyboardEvent($sender, $comment, $key);
	$logger->trigEvent($event);
}
else
{
	$event = new core_kernel_events_EventFromJS($sender, $comment);
	$logger->trigEvent($event);
}

//TODO: Verify the user is authenticated!
//TODO: get the current database used by generis!!
//TODO: get informations from the JS and translate to fit the new Event class.
//      Now, I just keep $sender and $comment. (I'm ON !!!)


// $eventTranslator = new core_kernel_events_EventTranslator($logger);
// $eventTranslator->getCSV("/tmp/log");

?>