<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - core\kernel\classes\class.Assignment.php
 *
 * 
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatic generated with ArgoUML 0.24 on 19.01.2009, 16:51:12
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package core
 * @subpackage kernel_classes
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
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
// section 10-13-1--99--40a85dda:11eef4d8c1d:-8000:0000000000000F3E-includes begin
// section 10-13-1--99--40a85dda:11eef4d8c1d:-8000:0000000000000F3E-includes end

/* user defined constants */
// section 10-13-1--99--40a85dda:11eef4d8c1d:-8000:0000000000000F3E-constants begin
// section 10-13-1--99--40a85dda:11eef4d8c1d:-8000:0000000000000F3E-constants end

/**
 * Short description of class core_kernel_classes_Assignment
 *
 * @access public
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package core
 * @subpackage kernel_classes
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
class core_kernel_classes_Assignment
    extends core_kernel_classes_Resource
{
    // --- ATTRIBUTES ---

	public $subject;
	public $predicate;
	public $object;
	public $logger;
    // --- OPERATIONS ---

	public function __construct($uriResource , $debug = ''){
		parent::__construct($uriResource,$debug );
		
		$this->logger = new common_Logger('Generis Assignment', Logger::debug_level);
		$this->logger->info('Evaluating Assignment uri: '. $this->uriResource , __FILE__, __LINE__);
		$this->logger->info('Evaluating Assignment name: '. $this->getLabel() , __FILE__, __LINE__);
		
        $variableProperty = new core_kernel_classes_Property(PROPERTY_ASSIGNMENT_VARIABLE, __METHOD__);
        

        $variableInstance = $this->getUniquePropertyValue($variableProperty);        
        if($variableInstance instanceof core_kernel_classes_Resource ){
        	//Check if SPX
        	$this->logger->info('Evaluating Variable uri: '. $variableInstance->uriResource , __FILE__, __LINE__);
        	$this->logger->info('Evaluating Variable name: '. $variableInstance->getLabel() , __FILE__, __LINE__);
        	$termType = $variableInstance->getUniquePropertyValue(new core_kernel_classes_Property(RDF_TYPE,__METHOD__));
        	if($termType->uriResource == CLASS_TERM_SUJET_PREDICATE_X) {
        		$this->subject = $variableInstance->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_TERM_SPX_SUBJET,__METHOD__));
        		$this->logger->debug('Subject uri:'. $this->subject->uriResource , __FILE__, __LINE__);
        		$this->logger->debug('Subject name:'. $this->subject->getLabel() , __FILE__, __LINE__);
        		$predicateResource = $variableInstance->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_TERM_SPX_PREDICATE,__METHOD__));
        		$this->predicate = new core_kernel_classes_Property($predicateResource->uriResource,__METHOD__);
        	    $this->logger->debug('Predicate uri:'. $this->predicate->uriResource , __FILE__, __LINE__);
        		$this->logger->debug('Predicate name:'. $this->predicate->getLabel() , __FILE__, __LINE__);
        	}
        	
        }

	}
	
	public function getTermValue($termValueResource)
	{
		$termValue = new core_kernel_rules_Term($termValueResource->uriResource,__METHOD__);
		return $termValue;
	}
	
    /**
     * Short description of method evaluate
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param array
     * @return boolean
     */
    public function evaluate($variable = array())
    {
        $returnValue = (bool) false;

        // section 10-13-1--99--40a85dda:11eef4d8c1d:-8000:0000000000000F4A begin
		$valueProperty =  new core_kernel_classes_Property(PROPERTY_ASSIGNMENT_VALUE, __METHOD__);
        $termValueResource = $this->getUniquePropertyValue($valueProperty);
        if($termValueResource instanceof core_kernel_classes_Resource ){
        	$this->logger->info('Evaluating Value uri: '. $termValueResource->uriResource , __FILE__, __LINE__);
        	$this->logger->info('Evaluating Value name: '. $termValueResource->getLabel() , __FILE__, __LINE__);
        	$termValue = $this->getTermValue($termValueResource);
        	$this->object = $termValue->evaluate($variable);
        	
        }
        
        if(isset($this->subject)){
        	if ($this->subject instanceof core_kernel_classes_Resource ) {
        		if ( isset($this->predicate) && $this->predicate instanceof core_kernel_classes_Property ){

        			if(array_key_exists($this->subject->uriResource,$variable)) {
        				$this->logger->debug('predicate is a Property');
        				$this->logger->debug('Variable uri : ' .  $this->subject->uriResource . ' found' , __FILE__, __LINE__);
    					$this->logger->debug('Variable name : ' .  $this->subject->getLabel() . ' found' , __FILE__, __LINE__);
        				$this->subject = new core_kernel_classes_Resource($variable[$this->subject->uriResource]);
        				$this->logger->debug('Variable repaced uri : ' .  $this->subject->uriResource , __FILE__, __LINE__);
						$this->logger->debug('Variable repaced name : ' .  $this->subject->getLabel() , __FILE__, __LINE__);
    
        			}
					$this->logger->debug('Object type : ' . get_class($this->object),__FILE__,__LINE__);
        			
        			if($this->object instanceof core_kernel_classes_Literal ) {
        				$this->logger->debug('object is a Literal');
        				// Maybe a resource is there ? We have to clean before adding the value to the variable.
        				$this->subject->removePropertyValues($this->predicate);
        				$returnValue = $this->subject->setPropertyValue($this->predicate,$this->object->literal);
        				$this->logger->info($this->subject->uriResource . '->' . $this->predicate->uriResource . '<-'. $this->object->literal , __FILE__, __LINE__);
        				$this->logger->info($this->subject->getLabel() . '->' . $this->predicate->getLabel() . '<-'. $this->object->literal , __FILE__, __LINE__);
        			}
        			
        			// Particular case for robustness. The Resource case
        			if ($this->object instanceof core_kernel_classes_Resource) {
        				$this->logger->debug('object is a Resource');
        				$value = $this->object->uriResource;
        				$this->logger->debug('Value :' . $value   , __FILE__, __LINE__);
        				$this->subject->removePropertyValues($this->predicate);
        				$returnValue = $this->subject->setPropertyValue($this->predicate,$value);
        				$this->logger->info($this->subject->uriResource . '->' . $this->predicate->uriResource . '<-'. $value , __FILE__, __LINE__);
        				$this->logger->info($this->subject->getLabel() . '->' . $this->predicate->getLabel() . '<-'. $value , __FILE__, __LINE__);
        			}
        			
        			if($this->object instanceof core_kernel_classes_ContainerCollection ) {
        				$this->logger->debug('object is a ContainerCollection');
        				$this->logger->info( $this->object->count() . ' values are found ' , __FILE__, __LINE__);
        				$this->subject->removePropertyValues($this->predicate);
        				foreach ($this->object->getIterator() as $obj) {
        					$value =  $obj instanceof core_kernel_classes_Resource ? $obj->uriResource : $obj->literal;
        					$this->logger->debug('Value :' . $value   , __FILE__, __LINE__);
        					$returnValue = $this->subject->setPropertyValue($this->predicate,$value);
        					$this->logger->info($this->subject->uriResource . '->' . $this->predicate->uriResource . '<-'. $value , __FILE__, __LINE__);
        					$this->logger->info($this->subject->getLabel() . '->' . $this->predicate->getLabel() . '<-'. $value , __FILE__, __LINE__);
        				
        				}
        			}
        			
        		}
        		else {
        			$this->logger->error('Asignment Predicate not found ' . $this->getLabel(),__FILE__,__LINE__);
        			

        		}
        	}
        	else {
        		$this->logger->error('Asignment subject shouldbe instance of Resource ' . $this->getLabel(),__FILE__,__LINE__);
        	}
        }
        // section 10-13-1--99--40a85dda:11eef4d8c1d:-8000:0000000000000F4A end

        return (bool) $returnValue;
    }

} /* end of class core_kernel_classes_Assignment */

?>