<?php

require_once dirname(__FILE__) . '/../../tao/test/TestRunner.php';
require_once INCLUDES_PATH.'/simpletest/autorun.php';


class ExceptionTestCase extends UnitTestCase {
    
    public function setUp()
    {
	    TestRunner::initTest();
	}
    
    // Method used in the testInvalidArgumentTypeException
    private function wrongArgumentType($object)
    {
        // the function expects a common_Object as first argument
        if(!($object instanceof common_Object) ||
                ( ($object instanceof common_Object) && is_subclass_of($object, 'common_Object'))){
            throw new common_exception_InvalidArgumentType(__CLASS__, __METHOD__, 1, 'common_Object', $object);
        }
    }
    
    public function testInvalidArgumentTypeException()
    {
        try{
            $myResource = new core_kernel_classes_Resource('NIMP');
            $this->wrongArgumentType($myResource);
            $this->assertFalse(true);
        }catch(common_exception_InvalidArgumentType $exception){
            $this->assertTrue(true);
        }
    }
}
