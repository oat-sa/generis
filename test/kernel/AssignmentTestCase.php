<?php

error_reporting(E_ALL);
require_once dirname(__FILE__).'/../../common/common.php';
require_once INCLUDES_PATH.'/simpletest/autorun.php';


class AssignmentTestCase extends UnitTestCase {
	

	
    /**
     * Setting the collection to test
     *
     */
    public function setUp(){
		core_kernel_impl_ApiModelOO::singleton()->logIn(LOGIN,md5(PASS),MODULE,true);

	}
	
	public function testEvaluate(){
		$assignTest = new core_kernel_classes_Assignment('http://127.0.0.1/middleware/Interview.rdf#i1234777168083356900');
		$toto = array();
		$toto['http://127.0.0.1/middleware/Interview.rdf#i121939168717368'] = 'http://127.0.0.1/middleware/Interview.rdf#i123322652356188';
//		var_dump($assignTest);
		var_dump($assignTest->evaluate($toto));
		$this->fail('not implemented yet');
		
	}
	

}
?>