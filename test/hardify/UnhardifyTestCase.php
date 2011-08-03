<?php
require_once dirname(__FILE__) . '/../../../tao/test/TestRunner.php';
require_once INCLUDES_PATH.'/simpletest/autorun.php';


class UnhardifyTestCase extends UnitTestCase {
	
	public function setUp(){
	    TestRunner::initTest();
	}
	
	public function testHardify(){
		$wfEngineHardifier = new wfEngine_scripts_HardifyWfEngine (array(
			'min'		=> 1,
			'required'	=> array(
				array('compile'),
				array('decompile')
			),
			'parameters' => array(
				array(
					'name' 			=> 'compile',
					'type' 			=> 'boolean',
					'shortcut'		=> 'c',
					'required'		=> true,
					'description'	=> 'Compile the workflow triple store to relational database'
				),
				array(
					'name' 			=> 'decompile',
					'type' 			=> 'boolean',
					'shortcut'		=> 'd',
					'required'		=> true,
					'description'	=> 'Get the data from the workflow relational database to the triple store (if previously compiled)'
				),
				array(
					'name'			=> 'indexes',
					'type' 			=> 'boolean',
					'shortcut'		=> 'i',
					'description'	=> 'Create extra indexes on compiled tables and rebuild exisiting indexes databases'
				)
			)
		), array ('argv'=>array('-d -i', '-d', '-i')));
	}
}

?>