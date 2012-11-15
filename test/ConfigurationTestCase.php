<?php

require_once dirname(__FILE__) . '/GenerisTestRunner.php';

class ConfigurationTestCasePrototype extends TestCasePrototype {
    
    public function setUp()
    {
        GenerisTestRunner::initTest();
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
        $this->assertEqual($report->getStatusAsString(), 'valid');
        
        ini_set($ini->getName(), 'text/xml');
        $report = $ini->check();
        $this->assertEqual($report->getStatus(), common_configuration_Report::INVALID);
        $this->assertEqual($report->getStatusAsString(), 'invalid');
        
        $ini->setName('foobar');
        $report = $ini->check();
        $this->assertEqual($report->getStatus(), common_configuration_Report::UNKNOWN);
        $this->assertEqual($report->getStatusAsString(), 'unknown');
        
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

    function testPHPExtension(){
        // Testing PHPExtension existence and version is quite hard to do. Indeed,
        // it depends what the installed extensions are on the computers running the tests.
        // Thus, I decided to use the DOM extension as a test. I've never seen a PHP installation
        // without the DOM extension. It comes built-in except if you compile PHP with the
        // --disable-dom directive.
        
        // core tests.
        $ext = new common_configuration_PHPExtension(null, null, 'dom');
        $this->assertEqual($ext->getMin(), null);
        $this->assertEqual($ext->getMax(), null);
        $this->assertEqual($ext->getName(), 'dom');
        $this->assertEqual($ext->isOptional(), false);
        
        $ext->setMin('1.0');
        $this->assertEqual($ext->getMin(), '1.0');
        
        $ext->setMax('2.0');
        $this->assertEqual($ext->getMax(), '2.0');
        
        $ext->setName('foobar');
        $this->assertEqual($ext->getName(), 'foobar');
        
        // test with an extension that has no version information (hash) that
        // contains hash functions such as md5().
        // We consider that if there is no version information but the extension
        // is loaded, the report is always valid even if min and/or max version(s)
        // are specified.
        $ext = new common_configuration_PHPExtension(null, null, 'hash');
        $report = $ext->check();
        $this->assertEqual($report->getStatus(), common_configuration_Report::VALID);
        
        $ext->setMin('1.0');
        $report = $ext->check();
        $this->assertEqual($report->getStatus(), common_configuration_Report::VALID);
        
        $ext->setMax('2.0');
        $report = $ext->check();
        $this->assertEqual($report->getStatus(), common_configuration_Report::VALID);
        
        $ext->setMin(null);
        $report = $ext->check();
        $this->assertEqual($report->getStatus(), common_configuration_Report::VALID);
        
        // test with the dom extension that has version information.
        $ext = new common_configuration_PHPExtension('19851127', '20090601', 'dom');
        $report = $ext->check();
        $this->assertEqual($report->getStatus(), common_configuration_Report::VALID);
        
        $ext->setMin('20050112');
        $report = $ext->check();
        $this->assertEqual($report->getStatus(), common_configuration_Report::INVALID);
        
        $ext->setMin(null);
        $ext->setMax('20020423');
        $report = $ext->check();
        $this->assertEqual($report->getStatus(), common_configuration_Report::INVALID);
        
        $ext->setMin('20010731');
        $ext->setMax(null);
        $report = $ext->check();
        $this->assertEqual($report->getStatus(), common_configuration_Report::VALID);
        
        // Unexisting extension.
        $ext = new common_configuration_PHPExtension('1.0', '1.4', 'foobar');
        $report = $ext->check();
        $this->assertEqual($report->getStatus(), common_configuration_Report::UNKNOWN);
    }  

    public function testFileSystemComponent(){
        $f = new common_configuration_FileSystemComponent(__FILE__, 'r', 'This file');
        $this->assertEqual($f->getLocation(), __FILE__);
        $this->assertEqual($f->getExpectedRights(), 'r');
        $this->assertEqual($f->getName(), 'This file');
        $this->assertFalse($f->isOptional());
        $this->assertTrue($f->isReadable());
        
        $this->expectException(new common_configuration_MalformedRightsException("Malformed rights. Expected format is r|rw|rwx."));
        $f->setExpectedRights('fail');
        
        try{
            $f->setExpectedRights('rw');
            $this->pass();
        }
        catch (common_configuration_MalformedRightsException $e){
            $this->fail();
        }
    }
    
    public function testComponentCollection(){
    	// Non acyclic simple test.
    	$collection = new common_configuration_ComponentCollection();
    	$componentA = new common_configuration_Mock(common_configuration_Report::VALID, 'componentA');
    	$componentB = new common_configuration_Mock(common_configuration_Report::VALID, 'componentB');
    	$componentC = new common_configuration_Mock(common_configuration_Report::VALID, 'componentC');
    	
    	$collection->addComponent($componentA);
    	$collection->addComponent($componentB);
    	$collection->addComponent($componentC);
    	
    	$collection->addDependency($componentC, $componentA);
    	$collection->addDependency($componentB, $componentA);
    	
    	try{
    		$reports = $collection->check();
    		$this->assertTrue(true); // The graph is acyclic. Perfect!
    		$this->assertEqual(count($collection->getCheckedComponents()), 3);
    		$this->assertEqual(count($collection->getUncheckedComponents()), 0);
    		
    		// Now change some reports validity.
    		$componentA->setExpectedStatus(common_configuration_Report::INVALID);
    		
    		$reports = $collection->check();
    		$this->assertTrue(true); // Acyclic graph, no CyclicDependencyException thrown.
    		$this->assertEqual(count($collection->getCheckedComponents()), 1);
    		$this->assertEqual(count($collection->getUncheckedComponents()), 2);
    	}
    	catch (common_configuration_CyclicDependencyException $e){
    		$this->assertTrue(false, 'The graph dependency formed by the ComponentCollection must be acyclic.');
    	}
    }
}