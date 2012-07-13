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

    function testPHPRuntime() {
        // Core tests.
        $php = new common_configuration_PHPRuntime('5.3', '5.4', 'PHPRuntime');
        $this->assertEqual($php->getName(), 'PHPRuntime');
        $this->assertEqual($php->getMin(), '5.3');
        $this->assertEqual($php->getMax(), '5.4');
        $this->assertFalse($php->isOptional());
        
        $php->setMin('5.2');
        $this->assertEqual($php->getMin(), '5.2');
        
        $php->setMax('5.3');
        $this->assertEqual($php->getMax(), '5.3');
        
        $php->setName('foobar');
        $this->assertEqual($php->getName(), 'foobar');
        
        $php->setOptional(true);
        $this->assertTrue($php->isOptional());
        
        // max & min test.
        $php = new common_configuration_PHPRuntime('5.3', '5.4', 'PHPRuntime');
        $report = $php->check();
        $this->assertEqual($report->getStatus(), common_configuration_Report::VALID);
        
        $php->setMin('5.5');
        $php->setMax('5.5.6.3');
        $report = $php->check();
        $this->assertEqual($report->getStatus(), common_configuration_Report::INVALID);
        
        // min test.
        $php = new common_configuration_PHPRuntime('5.3', null, 'PHPRuntime');
        $report = $php->check();
        $this->assertEqual($report->getStatus(), common_configuration_Report::VALID);
        
        $php->setMin('5.5.3');
        $report = $php->check();
        $this->assertEqual($report->getStatus(), common_configuration_Report::INVALID);
        
        // max test.
        $php = new common_configuration_PHPRuntime(null, '5.5', 'PHPRuntime');
        $report = $php->check();
        $this->assertEqual($report->getStatus(), common_configuration_Report::VALID);
        
        $php->setMax('5.2');
        $report = $php->check();
        $this->assertEqual($report->getStatus(), common_configuration_Report::INVALID);
    }  
}