<?php
error_reporting(E_ALL);

	require_once dirname(__FILE__) . '/common/inc.extension.php';
	require_once dirname(__FILE__). '/common/common.php';
	

	// helpers
	// Here are imported all core helpers
	require_once DIR_CORE_HELPERS . 'Core.php';
	
	try {
		$re		= new HttpRequest();
		$fc		= new AdvancedFC($re);
		$fc->loadModule();
		
		
	} catch (Exception $e) {
		$message	= $e->getMessage();
		header("Location: ./portal/generisPortal.php");
	}

