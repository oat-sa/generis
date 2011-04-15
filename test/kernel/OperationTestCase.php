<?php

error_reporting(E_ALL);
require_once dirname(__FILE__).'/../../common/common.php';
require_once INCLUDES_PATH.'/simpletest/autorun.php';

/**
 * Test class for Expression.
*/

class OperationTestCase extends UnitTestCase {
	

	
    /**
     * Setting the collection to test
     *
     */
    public function setUp(){
		core_kernel_impl_ApiModelOO::singleton()->logIn(LOGIN,md5(PASS),MODULE,true);

	}
	public function testEvaluate(){
		$term1 = core_kernel_rules_TermFactory::createConst('4');
		$term2 = core_kernel_rules_TermFactory::createConst('2');
		
		$operatorAdd = new core_kernel_classes_Resource(INSTANCE_OPERATOR_ADD);
		$operationAdd = core_kernel_rules_OperationFactory::createOperation($term1,$term2,$operatorAdd);
		$result = $operationAdd->evaluate();
		$this->assertIsA($result,'core_kernel_classes_Literal');
		$this->assertEqual($result->literal,'6'); 		
		
		$operatorMinus = new core_kernel_classes_Resource(INSTANCE_OPERATOR_MINUS);
		$operationMinus = core_kernel_rules_OperationFactory::createOperation($term1,$term2,$operatorMinus);
		$result = $operationMinus->evaluate();
		$this->assertIsA($result,'core_kernel_classes_Literal');
		$this->assertEqual($result->literal,'2'); 
		
		$operatorMulti = new core_kernel_classes_Resource(INSTANCE_OPERATOR_MULTIPLY);
		$operationMulti = core_kernel_rules_OperationFactory::createOperation($term1,$term2,$operatorMulti);
		$result = $operationMulti->evaluate();
		$this->assertIsA($result,'core_kernel_classes_Literal');
		$this->assertEqual($result->literal,'8'); 
		
		$operatorDiv = new core_kernel_classes_Resource(INSTANCE_OPERATOR_DIVISION);
		$operationDiv = core_kernel_rules_OperationFactory::createOperation($term1,$term2,$operatorDiv);
		$result = $operationDiv->evaluate();
		$this->assertIsA($result,'core_kernel_classes_Literal');
		$this->assertEqual($result->literal,'2'); 
		
		$operatorConcat = new core_kernel_classes_Resource(INSTANCE_OPERATOR_CONCAT);
		$operationConcat = core_kernel_rules_OperationFactory::createOperation($term1,$term2,$operatorConcat);
		$result = $operationConcat->evaluate();
		$this->assertIsA($result,'core_kernel_classes_Literal');
		$this->assertEqual($result->literal,'4 2'); 

		$complex1 = core_kernel_rules_OperationFactory::createOperation($operationMulti,$operationAdd,$operatorMulti);
		$result = $complex1->evaluate();
		$this->assertIsA($result,'core_kernel_classes_Literal');
		$this->assertEqual($result->literal,'48'); 
		
		$complex2 = core_kernel_rules_OperationFactory::createOperation($complex1,$operationDiv,$operatorAdd);
		$result = $complex2->evaluate();
		$this->assertIsA($result,'core_kernel_classes_Literal');
		$this->assertEqual($result->literal,'50'); 
		
		
		$this->assertTrue($operationAdd->delete());
		$this->assertTrue($operationMinus->delete());
		$this->assertTrue($operationMulti->delete());
		$this->assertTrue($operationDiv->delete());
		$this->assertTrue($operationConcat->delete());
		$this->assertTrue($complex1->delete());
		$this->assertTrue($complex2->delete());
		
		$this->assertTrue($term1->delete());
		$this->assertTrue($term2->delete());
		
	}
}
?>