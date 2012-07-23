<?php
error_reporting(E_ALL);
require_once dirname(__FILE__) . '/../../../tao/test/TestRunner.php';
require_once INCLUDES_PATH.'/simpletest/autorun.php';


class TermTestCase extends UnitTestCase {


	public function setUp(){
		TestRunner::initTest();
	}
	
	public function testEvaluate(){
		
		// eval const
		$constantResource = core_kernel_rules_TermFactory::createConst('test1');
		$term = $constantResource->evaluate();			
		$this->assertIsA($term,'core_kernel_classes_Literal');
		$this->assertEqual($term,'test1');
		$constantResource->delete();
		
		//eval SPX
		$booleanClass = new core_kernel_classes_Class(GENERIS_BOOLEAN);
		
		$maybe = core_kernel_classes_ResourceFactory::create($booleanClass, 'testCase testCreateSPX',__METHOD__);
		$SPXResource = core_kernel_rules_TermFactory::createSPX($maybe,new core_kernel_classes_Property(RDFS_COMMENT));
		$spxResult = $SPXResource->evaluate();
		$this->assertIsA($spxResult,'core_kernel_classes_Literal');
		$this->assertEqual($spxResult,__METHOD__);
		
		
		$SPXResource->delete();
		$maybe->delete();

	}
	

	

}