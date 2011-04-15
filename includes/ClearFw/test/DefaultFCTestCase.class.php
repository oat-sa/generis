<?php

/**
 * Default Front Controller
 * @author Luc Dehand
 */
class DefaultFC extends UnitTestCase implements FrontController {
    
    private $defaultFC	= null;
    
    function __construct() {
    	$defaultFC	= new DefaultFC();
    }
    
    /**
     * Load module
     */
    function testLoadModule() {
    	
    }
    
    /**
     * Search all plugins path
     */
    function testGetAllPaths() {
    	
    }
    
    /**
     * Return module path
     * @param	string	$pModule		Module name
     * @return	string	module path
     */
    function testGetPath() {
    	
    }
}
?>