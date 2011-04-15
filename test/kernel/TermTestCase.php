<?php

error_reporting(E_ALL);
require_once dirname(__FILE__).'/../../common/common.php';
require_once INCLUDES_PATH.'/simpletest/autorun.php';

/**
 * Test class for Expression.
*/

class TermTestCase extends UnitTestCase {
	

	
    /**
     * Setting the collection to test
     *
     */
    public function setUp(){
		core_kernel_impl_ApiModelOO::singleton()->logIn(LOGIN,md5(PASS),MODULE,true);

	}
	

	public function testEvaluate(){
		//constant
		$constant = core_kernel_rules_TermFactory::createConst('Plop');
		$result = $constant->evaluate();
		$this->assertIsA($result,'core_kernel_classes_Literal');
		$this->assertEqual($result->literal,'Plop'); 
		
		
		//SPX
		$subject = new core_kernel_classes_Resource(GENERIS_TRUE);
		$predicate = new core_kernel_classes_Property(RDF_TYPE);
		
		$spx = core_kernel_rules_TermFactory::createSPX($subject,$predicate);
		$result = $spx->evaluate();
		$this->assertIsA($result,'core_kernel_classes_Resource');
		$this->assertEqual($result->uriResource,'http://www.tao.lu/Ontologies/generis.rdf#Boolean');
		
		//Variable replacement
		$spx2 = core_kernel_rules_TermFactory::createSPX($spx,$predicate);

		$result = $spx2->evaluate();
		$this->assertIsA($result,'core_kernel_classes_Resource');
		$this->assertEqual($result->uriResource,CLASS_TERM_SUJET_PREDICATE_X);
		
		$variable = array();
		$variable[$spx->uriResource] = $subject->uriResource;
		$result = $spx2->evaluate($variable);
		$this->assertIsA($result,'core_kernel_classes_Resource');
		$this->assertEqual($result->uriResource,'http://www.tao.lu/Ontologies/generis.rdf#Boolean');
		
				
		$this->assertTrue($spx->delete());
		$this->assertTrue($constant->delete());
		$this->assertTrue($spx2->delete());
		

		

	}

}
?>