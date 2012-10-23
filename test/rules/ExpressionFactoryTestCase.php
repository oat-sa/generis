<?php
error_reporting(E_ALL);
require_once dirname(__FILE__) . '/../../../tao/test/TestRunner.php';
require_once INCLUDES_PATH.'/simpletest/autorun.php';


/**
 *
 */
class ExpressionFactoryTestCase extends UnitTestCase {

    /**
     *
     */
    public function testCreateTerminalExpression(){
		$constantResource = core_kernel_rules_TermFactory::createConst('test1');
		$terminalExpression = core_kernel_rules_ExpressionFactory::createTerminalExpression($constantResource);
		$terminalExpressionProperty = new core_kernel_classes_Property(PROPERTY_TERMINAL_EXPRESSION,__METHOD__);
		$terminalExpressionVal = $terminalExpression->getOnePropertyValue($terminalExpressionProperty);
        $this->assertIsA($terminalExpressionVal,'core_kernel_classes_Resource');
        $this->assertEqual($terminalExpressionVal->getUri(),$constantResource->getUri());

		$constantResource->delete();
		$terminalExpression->delete();
		
	}

    /**
     *
     */
    public function testCreateRecursiveExpression(){

        $constantResource1 = core_kernel_rules_TermFactory::createConst('test1');
        $constantResource2 = core_kernel_rules_TermFactory::createConst('test2');

        $terminalExpression1 = core_kernel_rules_ExpressionFactory::createTerminalExpression($constantResource1);
        $terminalExpression2 = core_kernel_rules_ExpressionFactory::createTerminalExpression($constantResource2);

        $equalsOperator = new core_kernel_classes_Resource(INSTANCE_EQUALS_OPERATOR_URI);
        $finalExpression = core_kernel_rules_ExpressionFactory::createRecursiveExpression($terminalExpression1,$terminalExpression2,$equalsOperator);

        //prop
        $terminalExpressionProperty = new core_kernel_classes_Property(PROPERTY_TERMINAL_EXPRESSION,__METHOD__);
        $logicalOperatorProperty = new core_kernel_classes_Property(PROPERTY_HASLOGICALOPERATOR,__METHOD__);
        $firstExpressionProperty = new core_kernel_classes_Property(PROPERTY_FIRST_EXPRESSION,__METHOD__);
        $secondExpressionProperty = new core_kernel_classes_Property(PROPERTY_SECOND_EXPRESSION,__METHOD__);

        //final expr
        $finalExpressionVal = $finalExpression->getOnePropertyValue($terminalExpressionProperty);
        $this->assertIsA($finalExpressionVal,'core_kernel_classes_Resource');
        $this->assertEqual($finalExpressionVal->getUri(),INSTANCE_EMPTY_TERM_URI);

        //operator
        $logicalOperatorVal = $finalExpression->getOnePropertyValue($logicalOperatorProperty);
        $this->assertIsA($logicalOperatorVal,'core_kernel_classes_Resource');
        $this->assertEqual($logicalOperatorVal->getUri(),INSTANCE_EQUALS_OPERATOR_URI);

        //first expr
        $firstExpressionVal = $finalExpression->getOnePropertyValue($firstExpressionProperty);
        $this->assertIsA($firstExpressionVal,'core_kernel_classes_Resource');
        $this->assertEqual($firstExpressionVal->getUri(),$terminalExpression1->getUri());

        //Second expr
        $secondExpressionVal = $finalExpression->getOnePropertyValue($secondExpressionProperty);
        $this->assertIsA($secondExpressionVal,'core_kernel_classes_Resource');
        $this->assertEqual($secondExpressionVal->getUri(),$terminalExpression2->getUri());

        $constantResource1->delete();
        $constantResource2->delete();
        $terminalExpression1->delete();
        $terminalExpression2->delete();
        $finalExpression->delete();

    }
	
}