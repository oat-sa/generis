<?php

require_once dirname(__FILE__) . '/../../tao/test/TestRunner.php';
require_once INCLUDES_PATH.'/simpletest/autorun.php';


class LogTestCase extends TaoTestCase {
    
    public function setUp()
    {
        TestRunner::initTest();
    }
    
    public function testPHPIniValues(){
        // core tests.
        $ini = new common_configuration_PHPINIValue('text/html', 'default_mimetype');
        $this->assertEqual($ini->getName(), 'default_mimetype');
        $this->assertEqual($ini->isOptional(), false);
        $this->assertEqual($ini->getExpectedValue(), 'text/html');
        $ini->setOptional(true);
        $this->assertEqual($ini->isOptional(), true);
        $ini->setName('foobar');
        $this->assertEqual($ini->getName(), 'foobar');
        $ini->setExpectedValue('text/xml');
        $this->assertEqual($ini->getExpectedValue(), 'text/xml');
        
        // String INI Option test.
        $ini->setName('default_mimetype');
        $ini->setExpectedValue('text/html');
        $oldIniValue = ini_get($ini->getName());
        ini_set($ini->getName(), 'text/html');
        $report = $ini->check();
        $this->assertEqual($report->getStatus(), common_configuration_Report::VALID);
        
        ini_set($ini->getName(), 'text/xml');
        $report = $ini->check();
        $this->assertEqual($report->getStatus(), common_configuration_Report::INVALID);
        
        $ini->setName('foobar');
        $report = $ini->check();
        $this->assertEqual($report->getStatus(), common_configuration_Report::UNKNOWN);
        
        ini_set($ini->getName(), $oldIniValue);
    }  
}