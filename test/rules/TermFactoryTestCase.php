<?php
error_reporting(E_ALL);
require_once dirname(__FILE__) . '/../../../tao/test/TestRunner.php';
require_once INCLUDES_PATH.'/simpletest/autorun.php';


class TermFactoryTestCase extends UnitTestCase {


	public function setUp(){
		TestRunner::initTest();
	}
	
	public function testCreateConst(){
		$constantResource = core_kernel_rules_TermFactory::createConst('test1');
		$this->assertIsA($constantResource,'core_kernel_rules_Term');
		$typeUri = array_keys($constantResource->getTypes());
		$this->assertEqual($typeUri[0],CLASS_TERM_CONST);
		$this->assertTrue(count($typeUri) == 1);
			
		$termValueProperty = new core_kernel_classes_Property(PROPERTY_TERM_VALUE);
		$logicalOperatorProperty = new core_kernel_classes_Property(PROPERTY_HASLOGICALOPERATOR);
		$terminalExpressionProperty = new core_kernel_classes_Property(PROPERTY_TERMINAL_EXPRESSION);
		
		$term = $constantResource->getUniquePropertyValue($termValueProperty);
		$this->assertIsA($term,'core_kernel_classes_Literal');
		$this->assertEqual($term,'test1');
		
		$operator = $constantResource->getUniquePropertyValue($logicalOperatorProperty);
		$this->assertIsA($operator,'core_kernel_classes_Resource');
		$this->assertEqual($operator->getUri(),INSTANCE_EXISTS_OPERATOR_URI);
	
		$terminalExpression = $constantResource->getUniquePropertyValue($terminalExpressionProperty);
		$this->assertIsA($terminalExpression,'core_kernel_classes_Resource');
		$this->assertEqual($terminalExpression->getUri(),$constantResource->getUri());

		$constantResource->delete();
	}
	
	public function testCreateSPX(){
		$booleanClass = new core_kernel_classes_Class(GENERIS_BOOLEAN);
		$maybe = core_kernel_classes_ResourceFactory::create($booleanClass, 'testCase testCreateSPX',__METHOD__);
		
		$SPXResource = core_kernel_rules_TermFactory::createSPX($maybe,new core_kernel_classes_Property(RDFS_COMMENT));
		$this->assertIsA($SPXResource,'core_kernel_rules_Term');
		
		$subjectProperty = new core_kernel_classes_Property(PROPERTY_TERM_SPX_SUBJET);
		$predicateProperty = new core_kernel_classes_Property(PROPERTY_TERM_SPX_PREDICATE);
     	
		$subject = $SPXResource->getUniquePropertyValue($subjectProperty);
     	$this->assertIsA($subject,'core_kernel_classes_Resource');
		$this->assertEqual($subject->getUri(),$maybe->getUri());
     	
     	$predicate = $SPXResource->getUniquePropertyValue($predicateProperty);
		$this->assertIsA($predicate,'core_kernel_classes_Resource');
		$this->assertEqual($predicate->getUri(),RDFS_COMMENT);
		
		$SPXResource->delete();
		$maybe->delete();
	}
	

}