<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API -
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 14.11.2012, 13:24:23 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package common
 * @subpackage configuration
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 10-13-1-85--478659bd:13afeb85455:-8000:0000000000001C91-includes begin
// section 10-13-1-85--478659bd:13afeb85455:-8000:0000000000001C91-includes end

/* user defined constants */
// section 10-13-1-85--478659bd:13afeb85455:-8000:0000000000001C91-constants begin
// section 10-13-1-85--478659bd:13afeb85455:-8000:0000000000001C91-constants end

/**
 * Short description of class common_configuration_ComponentCollection
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package common
 * @subpackage configuration
 */
class common_configuration_ComponentCollection
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * The components that have to be checked.
     *
     * @access private
     * @var array
     */
    private $components = array();

    /**
     * An array of arrays. The arrays contained in this field are associative
     * with the following keys: 'component' is the component on which other
     * have dependencies. 'depends' is an array containing components that have
     * dependency on 'component'.
     *
     * @access private
     * @var array
     */
    private $dependencies = array();

    // --- OPERATIONS ---

    /**
     * Short description of method addComponent
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Component component
     * @return void
     */
    public function addComponent( common_configuration_Component $component)
    {
        // section 10-13-1-85--478659bd:13afeb85455:-8000:0000000000001C95 begin
        $comp = $this->components[] = $component;
        // section 10-13-1-85--478659bd:13afeb85455:-8000:0000000000001C95 end
    }

    /**
     * Short description of method addDependency
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Component component
     * @param  Component dependency
     * @return void
     */
    public function addDependency( common_configuration_Component $component,  common_configuration_Component $dependency)
    {
        // section 10-13-1-85--478659bd:13afeb85455:-8000:0000000000001CA8 begin
        // Look for a similar component.
        
        // section 10-13-1-85--478659bd:13afeb85455:-8000:0000000000001CA8 end
    }

    /**
     * Short description of method reset
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return void
     */
    public function reset()
    {
        // section 10-13-1-85--478659bd:13afeb85455:-8000:0000000000001C97 begin
        $this->components = array();
        $this->dependencies = array();
        // section 10-13-1-85--478659bd:13afeb85455:-8000:0000000000001C97 end
    }

    /**
     * Returns an array of Reports.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return array
     */
    public function check()
    {
        $returnValue = array();

        // section 10-13-1-85--478659bd:13afeb85455:-8000:0000000000001C9D begin        
        
        // section 10-13-1-85--478659bd:13afeb85455:-8000:0000000000001C9D end

        return (array) $returnValue;
    }
    
    private function getDependentComponents(common_configuration_Component $component){
    	$returnValue = array();
    	
    	foreach ($this->dependencies as $d){
    		foreach ($d['dependsOn'] as $on){
    			if ($on === $component){
    				$returnValue[] = $d['component'];
    			}
    		}
    	}
    	
    	return $returnValue;
    }
    
    public function isAcyclic(){
    	$l = array(); // Empty list where elements are sorted.
    	$q = array(); // Set of nodes with no incoming edges.
    	
    	
    }

} /* end of class common_configuration_ComponentCollection */

?>