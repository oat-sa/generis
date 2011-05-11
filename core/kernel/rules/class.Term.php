<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - core\kernel\rules\class.Term.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 23.03.2010, 15:58:21 with ArgoUML PHP module 
 * (last revised $Date: 2008-04-19 08:22:08 +0200 (Sat, 19 Apr 2008) $)
 *
 * @author firstname and lastname of author, <author@example.org>
 * @package core
 * @subpackage kernel_rules
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * Resource implements rdf:resource container identified by an uri (a string).
 * Methods enable meta data management for this resource
 *
 * @author patrick.plichart@tudor.lu
 * @see http://www.w3.org/RDF/
 * @version v1.0
 */
require_once('core/kernel/classes/class.Resource.php');

/* user defined includes */
// section 10-13-1--99-20158b09:11bfa8bc7dd:-8000:0000000000000DB7-includes begin
// section 10-13-1--99-20158b09:11bfa8bc7dd:-8000:0000000000000DB7-includes end

/* user defined constants */
// section 10-13-1--99-20158b09:11bfa8bc7dd:-8000:0000000000000DB7-constants begin
// section 10-13-1--99-20158b09:11bfa8bc7dd:-8000:0000000000000DB7-constants end

/**
 * Short description of class core_kernel_rules_Term
 *
 * @access public
 * @author firstname and lastname of author, <author@example.org>
 * @package core
 * @subpackage kernel_rules
 */
class core_kernel_rules_Term
    extends core_kernel_classes_Resource
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method evaluate
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  array variable
     * @return mixed
     */
    public function evaluate($variable = array())
    {
        // section 10-13-1--99-20158b09:11bfa8bc7dd:-8000:0000000000000DBD begin
      	$logger = new common_Logger('Generis Term', Logger::debug_level);
		$logger->info('Evaluating Term uri : '. $this->uriResource , __FILE__, __LINE__);
		$logger->info('Evaluating Term name : '. $this->getLabel() , __FILE__, __LINE__);
		$termType = $this->getUniquePropertyValue(new core_kernel_classes_Property('http://www.w3.org/1999/02/22-rdf-syntax-ns#type'));
		$logger->debug('Term s type : '. $termType->uriResource , __FILE__, __LINE__);
		switch($termType->uriResource) {
    		case CLASS_TERM : {
				throw new common_Exception("Forbidden Type of Term");
				
    			break;
    		}
    		case CLASS_TERM_SUJET_PREDICATE_X : {
    				$returnValue = $this->evaluateSPX($variable);
       			break;
    		}
		   case CLASS_TERM_X_PREDICATE_OBJECT : {
		   			$returnValue = $this->evaluateXPO();
				break;
    		}
    		case CLASS_CONSTRUCTED_SET : {
    				$returnValue = $this->evaluateSet();
    			break;
    		}
    	   	case CLASS_TERM_CONST : {
    	   			$returnValue = $this->evaluateConst();
    	   		break;
    		}
    		case CLASS_OPERATION : {
    				$returnValue = $this->evaluateOperation($variable);
      			break;
    		}
    		default :
    			var_dump($this);
    			throw new common_Exception('problem evaluating Term');
    	}
    	
		return $returnValue;
        // section 10-13-1--99-20158b09:11bfa8bc7dd:-8000:0000000000000DBD end
    }


    /**
     * Short description of method evalutateSetOperation
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  Resource setOperator
     * @param  Collection actualSet
     * @param  ContainerCollection newSet
     * @return core_kernel_classes_ContainerCollection
     */
    public function evalutateSetOperation( core_kernel_classes_Resource $setOperator,  common_Collection $actualSet,  core_kernel_classes_ContainerCollection $newSet)
    {
        $returnValue = null;

        // section 10-13-1--99--1201ed7f:11c6b266eba:-8000:0000000000000EBA begin
    	if($setOperator->uriResource == INSTANCE_OPERATOR_UNION) {
			$returnValue = $actualSet->union($newSet);
    	}
    	else if($setOperator->uriResource == INSTANCE_OPERATOR_INTERSECT) {
    		$returnValue =  $actualSet->intersect($newSet);
    	}
    	else {
    		throw new common_Exception('unknow set operator');
		}
        // section 10-13-1--99--1201ed7f:11c6b266eba:-8000:0000000000000EBA end

        return $returnValue;
    }

    /**
     * Short description of method evaluateSPX
     *
     * @access protected
     * @author firstname and lastname of author, <author@example.org>
     * @param  array variable
     * @return mixed
     */
    protected function evaluateSPX($variable = array())
    {
        // section 10-13-1-85-7aec1e58:1201f62f271:-8000:00000000000015BC begin
    	$logger = new common_Logger('Generis Term evaluateSPX');
		$logger->debug('SPX TYPE' , __FILE__, __LINE__);
    	$resource = $this->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_TERM_SPX_SUBJET));
    	if($resource instanceof core_kernel_classes_Resource){
    		if(array_key_exists($resource->uriResource,$variable)) {
    			$logger->debug('Variable uri : ' .  $resource->uriResource . ' found' , __FILE__, __LINE__);
    			$logger->debug('Variable name : ' .  $resource->getLabel() . ' found' , __FILE__, __LINE__);
    			$resource = new core_kernel_classes_Resource($variable[$resource->uriResource]);
    			$logger->debug('Variable repaced uri : ' .  $resource->uriResource , __FILE__, __LINE__);
				$logger->debug('Variable repaced name : ' .  $resource->getLabel() , __FILE__, __LINE__);
    		}
    		
    		try
    		{
    			$propertyInstance = $this->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_TERM_SPX_PREDICATE));
    		}
    		catch (common_Exception $e)
    		{
    			echo $e;
    			var_dump($this);
    			die('unable to get property value in Term');
    		}
//    		if(array_key_exists($propertyInstance->uriResource,$variable)) {
//				$logger->debug('Variable uri : ' .  $propertyInstance->uriResource . ' found' , __FILE__, __LINE__);
//    			$logger->debug('Variable name : ' .  $propertyInstance->getLabel() . ' found' , __FILE__, __LINE__);
//				$propertyInstance = new core_kernel_classes_Resource($variable[$resource->uriResource]);
//    			$logger->debug('Variable repaced uri : ' .  $propertyInstance->uriResource , __FILE__, __LINE__);
//    			$logger->debug('Variable repaced name : ' .  $propertyInstance->getLabel() , __FILE__, __LINE__);
//    	    }
    		$property = new core_kernel_classes_Property($propertyInstance->uriResource);
    		$logger->debug('Property uri ' . $property->uriResource, __FILE__, __LINE__);
    		$logger->debug('Property name ' . $property->getLabel(), __FILE__, __LINE__);
       		$returnValue = $resource->getPropertyValuesCollection($property);
       		$returnValue->debug = __METHOD__;
       		$logger->debug( $returnValue->count() . ' values returned ', __FILE__, __LINE__);

       		if($returnValue->isEmpty()) {
       			$newEmptyTerm = new core_kernel_rules_Term(INSTANCE_TERM_IS_NULL,__METHOD__);
       			$logger->warning('Empty Term Created',__FILE__,__LINE__);
				$property = new core_kernel_classes_Property(PROPERTY_TERM_VALUE);
       			$returnValue = $newEmptyTerm->getUniquePropertyValue($property);	
       		}
       		else {
				if($returnValue->count() == 1 ) {
       					$returnValue = $returnValue->get(0);
       			}
       		}

       		
    	}
    	return $returnValue;
        // section 10-13-1-85-7aec1e58:1201f62f271:-8000:00000000000015BC end
    }

    /**
     * Short description of method evaluateXPO
     *
     * @access protected
     * @author firstname and lastname of author, <author@example.org>
     * @return mixed
     */
    protected function evaluateXPO()
    {
        // section 10-13-1-85-7aec1e58:1201f62f271:-8000:00000000000015BF begin
        $logger = new common_Logger('Generis Term evaluateXPO');
		$logger->debug('XPO TYPE' , __FILE__, __LINE__);
		$classTerm = new core_kernel_classes_Class(CLASS_TERM_X_PREDICATE_OBJECT);
		$obj = $this->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_TERM_XPO_OBJECT));
		$pred = $this->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_TERM_XPO_PREDICATE));
		if($obj instanceof core_kernel_classes_Literal) {
			$objValue = $obj->literal;
		}
   		if($obj instanceof core_kernel_classes_Resource) {
			$objValue = $pred->uriResource;
		}
		
		$returnValue = new core_kernel_classes_ContainerCollection(new common_Object(__METHOD__));
		$terms = $classTerm->searchInstances(array($pred->uriResource => $objValue), array('like' => false));
		foreach($terms as $term){
			$returnValue->add($term);
		}
    	
		$returnValue->debug = __METHOD__;
    	return $returnValue;
        // section 10-13-1-85-7aec1e58:1201f62f271:-8000:00000000000015BF end
    }

    /**
     * Short description of method evaluateSet
     *
     * @access protected
     * @author firstname and lastname of author, <author@example.org>
     * @return mixed
     */
    protected function evaluateSet()
    {
        // section 10-13-1-85-7aec1e58:1201f62f271:-8000:00000000000015C1 begin
        $logger = new common_Logger('Generis Term evaluateSet');
		$logger->debug('Constructed Set TYPE' , __FILE__, __LINE__);
    	$operator = $this->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_SET_OPERATOR));
    	$subSets = $this->getPropertyValuesCollection(new core_kernel_classes_Property(PROPERTY_SUBSET));
    	$returnValue = new core_kernel_classes_ContainerCollection($this);
		$returnValue->debug = __METHOD__;
		
		foreach ($subSets->getIterator() as $aSet) {
    		
    		if ($aSet instanceof core_kernel_classes_Resource ) {
    			$newSet = new core_kernel_rules_Term($aSet->uriResource);
    			$resultSet = $newSet->evaluate();
    			if ($resultSet instanceof core_kernel_classes_ContainerCollection  ) {
    				$returnValue = $this->evalutateSetOperation($operator,$returnValue,$resultSet);
				}
    			else {
					$collection = new core_kernel_classes_ContainerCollection($this);
    				$collection->add($resultSet);
    				$returnValue = $this->evalutateSetOperation($operator,$returnValue,$collection);
    			}	
    		}
    		else {
    			throw new common_Exception('Bad Type , waiting for a Resource ');
    		}
    	   
    	}    		
    	
    	return $returnValue;
        // section 10-13-1-85-7aec1e58:1201f62f271:-8000:00000000000015C1 end
    }

    /**
     * Short description of method evaluateConst
     *
     * @access protected
     * @author firstname and lastname of author, <author@example.org>
     * @return mixed
     */
    protected function evaluateConst()
    {
        // section 10-13-1-85-7aec1e58:1201f62f271:-8000:00000000000015C3 begin
        $logger = new common_Logger('Generis Term evaluateConst');
		$logger->debug('CONSTANTE TYPE' , __FILE__, __LINE__);
	    $property = new core_kernel_classes_Property(PROPERTY_TERM_VALUE);
	    return $this->getUniquePropertyValue($property); 
        // section 10-13-1-85-7aec1e58:1201f62f271:-8000:00000000000015C3 end
    }

    /**
     * Short description of method evaluateOperation
     *
     * @access protected
     * @author firstname and lastname of author, <author@example.org>
     * @param  array variable
     * @return mixed
     */
    protected function evaluateOperation($variable = array())
    {
        // section 10-13-1-85-7aec1e58:1201f62f271:-8000:00000000000015C5 begin
        $logger = new common_Logger('Generis Term evaluateOperation');
		$logger->debug('OPERATION TYPE' , __FILE__, __LINE__);
    	return $this->evaluateArithmOperation($variable);
        // section 10-13-1-85-7aec1e58:1201f62f271:-8000:00000000000015C5 end
    }

    /**
     * Short description of method evaluateArtihmOperation
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  array variable
     * @return mixed
     */
    public function evaluateArithmOperation($variable = array())
    {
        // section 10-13-1-85-7aec1e58:1201f62f271:-8000:00000000000015CA begin
        $operation = new core_kernel_rules_Operation($this->uriResource, __METHOD__);
    	return  $operation->evaluate($variable);
        // section 10-13-1-85-7aec1e58:1201f62f271:-8000:00000000000015CA end
    }

} /* end of class core_kernel_rules_Term */

?>