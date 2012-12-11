<?php
require_once dirname(__FILE__) . '/GenerisTestRunner.php';

/**
 * Test of the common_ext_Namespace and common_ext_NamesapceManager
 * 
 * @author Bertrand Chevrier <bertrand.chevrier@tudor.lu>
 * @package generis
 * @subpackage test
 */
class DbWrapperTestCase extends UnitTestCase {
	
	public function setUp(){
        GenerisTestRunner::initTest();
	}
	
	public function testGetColumnNames(){
		$dbWrapper = core_kernel_classes_DbWrapper::singleton();
		$columns = $dbWrapper->getColumnNames('statements');
		$this->assertTrue(in_array('modelID', 		$columns));
		$this->assertTrue(in_array('subject', 		$columns));
		$this->assertTrue(in_array('predicate', 	$columns));
		$this->assertTrue(in_array('object', 		$columns));
		$this->assertTrue(in_array('l_language', 	$columns));
		$this->assertTrue(in_array('id', 			$columns));
		$this->assertTrue(in_array('author', 		$columns));
		$this->assertTrue(in_array('stread', 		$columns));
		$this->assertTrue(in_array('stedit', 		$columns));
		$this->assertTrue(in_array('stdelete', 		$columns));
		$this->assertTrue(in_array('epoch', 		$columns));
	}
}
?>