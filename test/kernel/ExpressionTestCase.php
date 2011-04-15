<?php

error_reporting(E_ALL);
require_once dirname(__FILE__).'/../../common/common.php';
require_once INCLUDES_PATH.'/simpletest/autorun.php';

/**
 * Test class for Expression.
*/

class ExpressionTestCase extends UnitTestCase {
	
	protected $object;
	
    /**
     * Setting the collection to test
     *
     */
    public function setUp(){
		core_kernel_impl_ApiModelOO::singleton()->logIn(LOGIN,md5(PASS),MODULE,true);
//		$this->object = new core_kernel_rules_Expression('http://localhost/middleware/Rules.rdf#i121982992121794',__METHOD__);
	}
	
	public function testEvaluate(){
//		$existExpression =  new core_kernel_rules_Expression('http://localhost/middleware/Rules.rdf#i121924593527220',__METHOD__);
//		$equalsExpression = new core_kernel_rules_Expression('http://localhost/middleware/Rules.rdf#i121982992121794',__METHOD__);
//		$differentExpression = new core_kernel_rules_Expression('http://localhost/middleware/Rules.rdf#i1220022251038775700',__METHOD__);
//		$orExpression =  new core_kernel_rules_Expression('http://localhost/middleware/Rules.rdf#i122044891255360',__METHOD__);
//		$andExpression = new core_kernel_rules_Expression('http://localhost/middleware/Rules.rdf#i122044896153524',__METHOD__);  
//		$supExpresion = new core_kernel_rules_Expression('http://localhost/middleware/Rules.rdf#i122045150826772',__METHOD__);
//		$supEqualsExpresion = new core_kernel_rules_Expression('http://localhost/middleware/Rules.rdf#i122045155047080',__METHOD__);
//		$operationAddExpresion = new core_kernel_rules_Expression('http://localhost/middleware/Rules.rdf#i122088519323530',__METHOD__);
//		$operationMinusExpresion = new core_kernel_rules_Expression('http://localhost/middleware/Rules.rdf#i1221149247008773400',__METHOD__);
//		$operationRecurssion = new core_kernel_rules_Expression('http://localhost/middleware/Rules.rdf#i1221152334095640900',__METHOD__);
//		$xpoExpression = new core_kernel_rules_Expression('http://localhost/middleware/Rules.rdf#i122165683620330',__METHOD__);
//		$transitionExpression = new core_kernel_rules_Expression('http://127.0.0.1/middleware/Interview.rdf#i122208993757374',__METHOD__);
//		$situationExpression = new core_kernel_rules_Expression('http://127.0.0.1/middleware/Interview.rdf#i122243728852014',__METHOD__);
//		$plopExpression = new core_kernel_rules_Expression('http://127.0.0.1/middleware/Interview.rdf#i1223310341067563900',__METHOD__);
//		

		
		
//		var_dump($existExpression->evaluate());
		
//		echo '$equalsExpression '; var_dump($equalsExpression->evaluate());
//		echo '$differentExpression ';var_dump($differentExpression->evaluate());
//		echo '$orExpression ';var_dump($orExpression->evaluate());
//		echo '$andExpression ';var_dump($andExpression->evaluate());
//		echo '$SupExpresion ';var_dump($supExpresion->evaluate());
//		echo '$SupEqualsExpresion ';var_dump($supEqualsExpresion->evaluate());
//		echo '$OperationExpresion '; var_dump($operationAddExpresion->evaluate());
//		echo '$$OperationMinusExpresion ';var_dump($operationMinusExpresion->evaluate());
////		echo '$operationRecurssion' ; var_dump($operationRecurssion->evaluate());

		//echo '$xpoExpression' ; var_dump($xpoExpression->evaluate());

//		echo '$xpoExpression' ; var_dump($xpoExpression->evaluate());
//		echo '$transitionExpression' ; var_dump($xpoExpression->evaluate());
//		echo '$situationExpression' ; var_dump($situationExpression->evaluate());
		


//
//		$toto = array();
//		$toto['http://127.0.0.1/middleware/Interview.rdf#i121939168717368'] = 'http://127.0.0.1/middleware/Interview.rdf#i123322652356188' ;
//		$PIAACExpression = new core_kernel_rules_Expression('#i1233591112021784600',__METHOD__);
//		var_dump($PIAACExpression->evaluate($toto));
		
		$term = core_kernel_rules_TermFactory::createConst('4');
		$terminalExpression = core_kernel_rules_ExpressionFactory::createTerminalExpression($term);

		$term2 = core_kernel_rules_TermFactory::createConst('6');
		$terminalExpression2 = core_kernel_rules_ExpressionFactory::createTerminalExpression($term2);
		
		$term3 = core_kernel_rules_TermFactory::createConst('4');
		$terminalExpression3 = core_kernel_rules_ExpressionFactory::createTerminalExpression($term3);
	
		
		$operatorEq = new core_kernel_classes_Resource(INSTANCE_EQUALS_OPERATOR_URI,__METHOD__);
		$operatorDif = new core_kernel_classes_Resource(INSTANCE_DIFFERENT_OPERATOR_URI,__METHOD__);
		$operatorSupEq = new core_kernel_classes_Resource(INSTANCE_SUP_EQ_OPERATOR_URI,__METHOD__);
		$operatorInfEq = new core_kernel_classes_Resource(INSTANCE_INF_EQ_OPERATOR_URI,__METHOD__);
		$operatorSup = new core_kernel_classes_Resource(INSTANCE_SUP_OPERATOR_URI,__METHOD__);
		$operatorInf = new core_kernel_classes_Resource(INSTANCE_INF_OPERATOR_URI,__METHOD__);
			
		$operatorOr = new core_kernel_classes_Resource(INSTANCE_OR_OPERATOR,__METHOD__);
		$operatorAnd = new core_kernel_classes_Resource(INSTANCE_AND_OPERATOR,__METHOD__);

		// 4 == 6
		$finalExpression = core_kernel_rules_ExpressionFactory::createRecursiveExpression($terminalExpression,$terminalExpression2,$operatorEq);
		$this->assertFalse($finalExpression->evaluate());

		// 4 != 6
		$finalExpression2 = core_kernel_rules_ExpressionFactory::createRecursiveExpression($terminalExpression,$terminalExpression2,$operatorDif);
		$this->assertTrue($finalExpression2->evaluate());
		
		// 4 == 4 
		$finalExpression3 = core_kernel_rules_ExpressionFactory::createRecursiveExpression($terminalExpression,$terminalExpression3,$operatorEq);
		$this->assertTrue($finalExpression3->evaluate());

		// 4 != 4
		$finalExpression4 = core_kernel_rules_ExpressionFactory::createRecursiveExpression($terminalExpression,$terminalExpression3,$operatorDif);
		$this->assertFalse($finalExpression4->evaluate());
		
		// 4 >= 6
		$finalExpression5 = core_kernel_rules_ExpressionFactory::createRecursiveExpression($terminalExpression,$terminalExpression2,$operatorSupEq);
		$this->assertFalse($finalExpression5->evaluate());
		
		// 4 >= 4
		$finalExpression6 = core_kernel_rules_ExpressionFactory::createRecursiveExpression($terminalExpression,$terminalExpression3,$operatorSupEq);
		$this->assertTrue($finalExpression6->evaluate());
	
		// 6 >= 4
		$finalExpression16 = core_kernel_rules_ExpressionFactory::createRecursiveExpression($terminalExpression2,$terminalExpression3,$operatorSupEq);
		$this->assertTrue($finalExpression16->evaluate());
		
		// 4 > 6
		$finalExpression7 = core_kernel_rules_ExpressionFactory::createRecursiveExpression($terminalExpression,$terminalExpression2,$operatorSup);
		$this->assertFalse($finalExpression7->evaluate());
			
		// 4 > 4 
		$finalExpression8 = core_kernel_rules_ExpressionFactory::createRecursiveExpression($terminalExpression,$terminalExpression3,$operatorSup);
		$this->assertFalse($finalExpression8->evaluate());
		
		// 6 > 4
		$finalExpression9 = core_kernel_rules_ExpressionFactory::createRecursiveExpression($terminalExpression2,$terminalExpression3,$operatorSup);
		$this->assertTrue($finalExpression9->evaluate());
		
		// 6 < 4
		$finalExpression10 = core_kernel_rules_ExpressionFactory::createRecursiveExpression($terminalExpression2,$terminalExpression3,$operatorInf);
		$this->assertFalse($finalExpression10->evaluate());
				
		// 4 < 4
		$finalExpression11 = core_kernel_rules_ExpressionFactory::createRecursiveExpression($terminalExpression,$terminalExpression3,$operatorInf);
		$this->assertFalse($finalExpression11->evaluate());
		
		// 4 < 6
		$finalExpression12 = core_kernel_rules_ExpressionFactory::createRecursiveExpression($terminalExpression,$terminalExpression2,$operatorInf);
		$this->assertTrue($finalExpression12->evaluate());
		
		// 6 <= 4
		$finalExpression13 = core_kernel_rules_ExpressionFactory::createRecursiveExpression($terminalExpression2,$terminalExpression3,$operatorInfEq);
		$this->assertFalse($finalExpression13->evaluate());
				
		// 4 <= 4
		$finalExpression14 = core_kernel_rules_ExpressionFactory::createRecursiveExpression($terminalExpression,$terminalExpression3,$operatorInfEq);
		$this->assertTrue($finalExpression14->evaluate());
		
		// 4 <= 6
		$finalExpression15 = core_kernel_rules_ExpressionFactory::createRecursiveExpression($terminalExpression,$terminalExpression2,$operatorInfEq);
		$this->assertTrue($finalExpression15->evaluate());
		
		// 4 =! 6 or  4 == 4 // True
		$finalExpression17 = core_kernel_rules_ExpressionFactory::createRecursiveExpression($finalExpression2,$finalExpression3,$operatorOr);
		$this->assertTrue($finalExpression17->evaluate());
		
		
		// 4 =! 6 and 4 != 4 // False
		$finalExpression19 = core_kernel_rules_ExpressionFactory::createRecursiveExpression($finalExpression2,$finalExpression4,$operatorAnd);
		$this->assertFalse($finalExpression19->evaluate());
		
		
		// 4 == 6 or 4 =! 4 // False
		$finalExpression18 = core_kernel_rules_ExpressionFactory::createRecursiveExpression($finalExpression,$finalExpression4,$operatorOr);
		$this->assertFalse($finalExpression18->evaluate());
		
		// 4 != 6 and 4 >= 4 // True
		$finalExpression20 = core_kernel_rules_ExpressionFactory::createRecursiveExpression($finalExpression2,$finalExpression6,$operatorAnd);
		$this->assertTrue($finalExpression20->evaluate());
		
		
		$this->assertTrue($term->delete());
		$this->assertTrue($term2->delete());
		$this->assertTrue($term3->delete());
		$this->assertTrue($terminalExpression->delete());
		$this->assertTrue($terminalExpression2->delete());
		$this->assertTrue($terminalExpression3->delete());
		$this->assertTrue($finalExpression->delete());
		$this->assertTrue($finalExpression2->delete());
		$this->assertTrue($finalExpression3->delete());
		$this->assertTrue($finalExpression4->delete());
		$this->assertTrue($finalExpression5->delete());
		$this->assertTrue($finalExpression6->delete());
		$this->assertTrue($finalExpression7->delete());
		$this->assertTrue($finalExpression8->delete());
		$this->assertTrue($finalExpression9->delete());
		$this->assertTrue($finalExpression10->delete());
		$this->assertTrue($finalExpression11->delete());
		$this->assertTrue($finalExpression12->delete());
		$this->assertTrue($finalExpression13->delete());
		$this->assertTrue($finalExpression14->delete());	
		$this->assertTrue($finalExpression15->delete());
		$this->assertTrue($finalExpression16->delete());
		$this->assertTrue($finalExpression17->delete());
		$this->assertTrue($finalExpression18->delete());
		$this->assertTrue($finalExpression19->delete());
		$this->assertTrue($finalExpression20->delete());

	}
	

}
?>