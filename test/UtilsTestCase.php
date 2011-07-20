<?php
error_reporting(E_ALL);

require_once dirname(__FILE__).'/../common/common.php';
require_once INCLUDES_PATH.'/simpletest/autorun.php';

/**

/**
 * Test class for Expression.
*/

class UtilsTestCase extends UnitTestCase {
	
	
	public function testIsUri(){
		$toto = 'http://localhost/middleware/Rules.rdf#i122044076930844';
		$toto2 = 'j ai super fain';
		$toto3 = 'http://localhost/middleware/Rules.rdf';
		$this->assertTrue(common_Utils::isUri($toto));
		$this->assertFalse(common_Utils::isUri($toto2));
		$this->assertFalse(common_Utils::isUri($toto3));
	}
	
	public function testGetNewUri(){
		$toto = common_Utils::getNewUri();
		$tata = common_Utils::getNewUri();
		$this->assertNotEqual($toto,$tata);
	}
	

}
?>