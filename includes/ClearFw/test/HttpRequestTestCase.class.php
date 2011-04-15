<?php

/**
 * 
 */
class HttpRequestTestCase extends UnitTestCase  {
    
    /**
     * Return module name
     * @return	string		Module
     */
    function testGetModule() {
    	// test 1
    	$_SERVER["REQUEST_URI"]	= "http://www.test.com/index.php/essai/test/";
   		$httpRequest	= new HttpRequest();	
    	$this->assertTrue($httpRequest->getModule(), "essai");
    	
    	// test 2
    	$_SERVER["REQUEST_URI"]	= "http://www.test.com/index.php/";
   		$httpRequest	= new HttpRequest();	
    	$this->assertNull($httpRequest->getModule());
    	
    	// test 3
    	$_SERVER["REQUEST_URI"]	= "http://www.test.com/index.php?variable=1";
   		$httpRequest	= new HttpRequest();	
    	$this->assertNull($httpRequest->getModule());
    	
    	// test 4
    	$_SERVER["REQUEST_URI"]	= "http://www.test.com/index.php/module/essai?variable=1";
   		$httpRequest	= new HttpRequest();	
    	$this->asserttrue($httpRequest->getModule(), "module");
    }
    
    /**
     * Return action name
     * @return	string		Action
     */
    function testGetAction() {
    	// test 1
    	$_SERVER["REQUEST_URI"]	= "http://www.test.com/index.php/essai/test/";
   		$httpRequest	= new HttpRequest();	
    	$this->assertTrue($httpRequest->getAction(), "test");
    	
    	// test 2
    	$_SERVER["REQUEST_URI"]	= "http://www.test.com/index.php/";
   		$httpRequest	= new HttpRequest();
    	$this->assertNull($httpRequest->getAction());
    	
    	// test 3
    	$_SERVER["REQUEST_URI"]	= "http://www.test.com/index.php?variable=1";
   		$httpRequest	= new HttpRequest();	
    	$this->assertNull($httpRequest->getAction());
    	
    	// test 4
    	$_SERVER["REQUEST_URI"]	= "http://www.test.com/index.php/module/essai?variable=1";
   		$httpRequest	= new HttpRequest();	
    	$this->asserttrue($httpRequest->getAction(), "essai");
    }
    
    /**
     * Return arguments
     * @return	array		Arguments
     */
    function testGetArgs() {
   		$httpRequest	= new HttpRequest();
   		$args = $httpRequest->getArgs();	
    	$this->asserttrue(empty($args));
    }
    
    /**
     * Return an argument
     * @param	string		$pKey		Argument name
     * @return	string		Argument value
     */
    function testGetArgument() {
    	
    }
}
?>