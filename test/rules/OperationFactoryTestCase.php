<?php
error_reporting(E_ALL);
require_once dirname(__FILE__) . '/../../../tao/test/TaoTestRunner.php';
require_once INCLUDES_PATH.'/simpletest/autorun.php';


class OperationFactoryTestCase extends UnitTestCase {


	public function setUp(){
		TaoTestRunner::initTest();
	}
	
	public function testCreateOperation(){
		$constant5 = core_kernel_rules_TermFactory::createConst('5');
		$constant12 = core_kernel_rules_TermFactory::createConst('12');
		$operation = core_kernel_rules_OperationFactory::createOperation(
			$constant5,
			$constant12,
			new core_kernel_classes_Resource(INSTANCE_OPERATOR_ADD)
		);
		$this->assertIsA($operation,'core_kernel_rules_Operation');
		
		$firstOperand = new core_kernel_classes_Property(PROPERTY_OPERATION_FIRST_OP);
		$secondOperand = new core_kernel_classes_Property(PROPERTY_OPERATION_SECND_OP);	
		$operatorProperty = new core_kernel_classes_Property(PROPERTY_OPERATION_OPERATOR);
		
		$operator = $operation->getUniquePropertyValue($operatorProperty);
		$this->assertIsA($operator,'core_kernel_classes_Resource');
		$this->assertEqual($operator->getUri(),INSTANCE_OPERATOR_ADD);
		
        $term1 = $operation->getUniquePropertyValue($firstOperand);
        $this->assertIsA($term1,'core_kernel_classes_Resource');
		$this->assertEqual($term1->getUri(),$constant5->getUri());
        
		$term2 = $operation->getUniquePropertyValue($secondOperand);
		$this->assertIsA($term2,'core_kernel_classes_Resource');
		$this->assertEqual($term2->getUri(),$constant12->getUri());
		
		$constant5->delete();
		$constant12->delete();
		$operation->delete();
	}

}