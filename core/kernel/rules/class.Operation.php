<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - core\kernel\rules\class.Operation.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 04.01.2013, 17:05:09 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package core
 * @subpackage kernel_rules
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include core_kernel_rules_Term
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 */
require_once('core/kernel/rules/class.Term.php');

/* user defined includes */
// section 10-13-1--99-20158b09:11bfa8bc7dd:-8000:0000000000000DFF-includes begin
// section 10-13-1--99-20158b09:11bfa8bc7dd:-8000:0000000000000DFF-includes end

/* user defined constants */
// section 10-13-1--99-20158b09:11bfa8bc7dd:-8000:0000000000000DFF-constants begin
// section 10-13-1--99-20158b09:11bfa8bc7dd:-8000:0000000000000DFF-constants end

/**
 * Short description of class core_kernel_rules_Operation
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package core
 * @subpackage kernel_rules
 */
class core_kernel_rules_Operation
    extends core_kernel_rules_Term
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute firstOperation
     *
     * @access private
     * @var Term
     */
    private $firstOperation = null;

    /**
     * Short description of attribute secondOperation
     *
     * @access private
     * @var Term
     */
    private $secondOperation = null;

    /**
     * Short description of attribute arithmeticOperator
     *
     * @access private
     * @var Resource
     */
    private $arithmeticOperator = null;

    // --- OPERATIONS ---

    /**
     * Short description of method getFirstOperation
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return core_kernel_rules_Term
     */
    public function getFirstOperation()
    {
        $returnValue = null;

        // section 10-13-1--99-20158b09:11bfa8bc7dd:-8000:0000000000000E23 begin
        if(empty($this->firstOperation)){
        	$property = new core_kernel_classes_Property(PROPERTY_OPERATION_FIRST_OP);
        	$resource = $this->getUniquePropertyValue($property);
        	$this->firstOperation = new core_kernel_rules_Term($resource->uriResource);
        }
        $returnValue = $this->firstOperation;
        // section 10-13-1--99-20158b09:11bfa8bc7dd:-8000:0000000000000E23 end

        return $returnValue;
    }

    /**
     * Short description of method getSecondOperation
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return core_kernel_rules_Term
     */
    public function getSecondOperation()
    {
        $returnValue = null;

        // section 10-13-1--99-20158b09:11bfa8bc7dd:-8000:0000000000000E25 begin
        if(empty($this->secondOperation)){
        	$property = new core_kernel_classes_Property(PROPERTY_OPERATION_SECND_OP);
        	$resource = $this->getUniquePropertyValue($property);
        	$this->secondOperation = new core_kernel_rules_Term($resource->uriResource);
        }
        $returnValue = $this->secondOperation;
        // section 10-13-1--99-20158b09:11bfa8bc7dd:-8000:0000000000000E25 end

        return $returnValue;
    }

    /**
     * Short description of method getOperator
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return core_kernel_classes_Resource
     */
    public function getOperator()
    {
        $returnValue = null;

        // section 10-13-1--99-20158b09:11bfa8bc7dd:-8000:0000000000000E27 begin
        if(empty($this->arithmeticOperator)){
        	$property = new core_kernel_classes_Property(PROPERTY_OPERATION_OPERATOR);
        	$this->arithmeticOperator = $this->getUniquePropertyValue($property);
        }
        $returnValue = $this->arithmeticOperator;
        // section 10-13-1--99-20158b09:11bfa8bc7dd:-8000:0000000000000E27 end

        return $returnValue;
    }

    /**
     * Short description of method evaluate
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  array variable
     * @return mixed
     */
    public function evaluate($variable = array())
    {
        // section 10-13-1--99-1a35566c:11edfbb4d4b:-8000:0000000000000F3A begin
        common_Logger::i('Evaluating Operation uri : '. $this->uriResource, array('Generis Operation'));
        common_Logger::i('Evaluating Operation name : '. $this->getLabel(), array('Generis Operation'));
        
        $operator = $this->getOperator();
        common_Logger::d('Operator uri: '. $operator->uriResource, array('Generis Operation'));
        common_Logger::d('Operator name: '. $operator->getLabel(), array('Generis Operation'));         

	    $firstPart = $this->getFirstOperation()->evaluate($variable);
	    $secondPart = $this->getSecondOperation()->evaluate($variable);


		if($firstPart instanceof core_kernel_classes_ContainerCollection ) {
			//if we have more than one result we only take the Literal label
			$nbLiteral = 0;
			$iterator = $firstPart->getIterator();
			foreach ($iterator as $first) {
				if ($first instanceof core_kernel_classes_Literal ) {
					$firstPart = $first;
					$nbLiteral++;
				}
				
			}
			if ($nbLiteral != 1){
				var_dump($iterator);
				throw new common_Exception('more than one Literal Retreive during  evaluation');
			}
    	}


   		
    	if($secondPart instanceof core_kernel_classes_ContainerCollection ) {
    		//if we have more than one result we only take the resource label
			$nbLiteral = 0;
			$iterator = $secondPart->getIterator();
			foreach ($secondPart->getIterator() as $second) {
				if ($second instanceof core_kernel_classes_Literal) {
					$secondPart = $second;
					$nbLiteral++;
				}

			}
			if ($nbLiteral != 1){
				var_dump($iterator);
				throw new common_Exception('more than one Literal Retreive during evaluation');
			}
    	}

    	common_Logger::d('First Part : ', array('Generis Operation'));
    	common_Logger::d('Second Part : '. $secondPart, array('Generis Operation'));
    	$returnValue = $this->evaluateRecursiveOperation($firstPart,$secondPart,$operator);
    	common_Logger::i('Operation value: '. $returnValue, array('Generis Operation'));
    	
    	return $returnValue;
        // section 10-13-1--99-1a35566c:11edfbb4d4b:-8000:0000000000000F3A end
    }

    /**
     * Short description of method evaluateRecursiveOperation
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Literal first
     * @param  Literal second
     * @param  Resource operator
     * @return mixed
     */
    public function evaluateRecursiveOperation( core_kernel_classes_Literal $first,  core_kernel_classes_Literal $second,  core_kernel_classes_Resource $operator)
    {
        // section 10-13-1--99-1a35566c:11edfbb4d4b:-8000:0000000000000F43 begin
        
        switch ($operator->uriResource) {
        	case INSTANCE_OPERATOR_ADD: {
        		$returnValue = new core_kernel_classes_Literal($first->literal + $second->literal);
        		break;
        	}
             case INSTANCE_OPERATOR_MINUS: {
        		$returnValue = new core_kernel_classes_Literal($first->literal - $second->literal);
        		break;
        	}
            case INSTANCE_OPERATOR_MULTIPLY: {
        		$returnValue = new core_kernel_classes_Literal($first->literal * $second->literal);
        		break;
        	}
        	case INSTANCE_OPERATOR_DIVISION: {
        		$returnValue = new core_kernel_classes_Literal($first->literal / $second->literal);
        		break;
        	}
        	case INSTANCE_OPERATOR_CONCAT: {
        		// FIXME Hotfix for the concat operator. Can't find why traling spaces are not
        		// kept intact when using concat.
        		// ex: 'february ' CONCAT '2008' -> 'february2008' instead of 'february 2008'.
        		$returnValue = new core_kernel_classes_Literal($first->literal . ' ' . $second->literal);
        		break;
        	}
        	             	
        	default : {
        		throw new common_Exception('problem evaluating operation, operator do not match with operands');
        	}
        		
        }
        $returnValue->debug = __METHOD__;
        return $returnValue;
        // section 10-13-1--99-1a35566c:11edfbb4d4b:-8000:0000000000000F43 end
    }

} /* end of class core_kernel_rules_Operation */

?>