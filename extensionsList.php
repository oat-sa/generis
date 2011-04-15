<?php


//simulate a service that retrieve list of availlable extension
return array( 
		'testExtension' => array ( 
				'zip' => dirname(__FILE__).'/test/common/testExtension.zip',
				'author' => 'CRP Henri Tudor',
				'name' => 'testExtensionZip',
				'description' => 'Sample Test Extension to test Ext Mechanism',
				'version' => '0.25'
				)
		);
?>
