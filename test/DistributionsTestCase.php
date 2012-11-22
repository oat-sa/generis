<?php

require_once dirname(__FILE__) . '/GenerisTestRunner.php';

class DistributionsTestCasePrototype extends TestCasePrototype {
    
    public function setUp()
    {
    	parent::setUp();
        GenerisTestRunner::initTest();
    }
    
    public function tearDown(){
    	parent::tearDown();
    }
    
    public function testDistribution(){
    	$id = 'test-distrib';
    	$name = 'Test Distribution';
    	$description = 'Test Distribution Description';
    	$version = '0.1';
    	$extensions = array('fake1', 'fake2');
    	
    	// Test instanciation and getters.
    	$distrib = new common_distrib_Distribution($id, $name, $description, $version, $extensions);
    	$this->assertIsA($distrib, 'common_distrib_Distribution');
    	$this->assertEqual($id, $distrib->getId());
    	$this->assertEqual($name, $distrib->getName());
    	$this->assertEqual($description, $distrib->getDescription());
    	$this->assertEqual($version, $distrib->getVersion());
    	$this->assertEqual($extensions, $distrib->getExtensions());
    	
    	// Test setters.
    	$id = 'test-distrib-2';
    	$name = 'Test Distribution 2';
    	$description = 'Test Distribution Description 2';
    	$version = '0.2';
    	$extensions = array('fake1', 'fake2', 'fake3');
    	
    	$distrib->setId($id);
    	$distrib->setName($name);
    	$distrib->setDescription($description);
    	$distrib->setVersion($version);
    	$distrib->setExtensions($extensions);
    	
    	$this->assertEqual($id, $distrib->getId());
    	$this->assertEqual($name, $distrib->getName());
    	$this->assertEqual($description, $distrib->getDescription());
    	$this->assertEqual($version, $distrib->getVersion());
    	$this->assertEqual($extensions, $distrib->getExtensions());
    	
    	// Misc.
    	$distrib->removeExtension('fake4');
    	$this->assertEqual($extensions, $distrib->getExtensions());
    	$distrib->removeExtension('fake2');
    	$this->assertEqual(array('fake1', 'fake3'), $distrib->getExtensions());
    	$this->assertFalse($distrib->hasExtension('fake2'));
    	$this->assertTrue($distrib->hasExtension('fake1'));
    	$distrib->addExtension('fake2');
    	$this->assertEqual(array('fake1', 'fake3', 'fake2'), $distrib->getExtensions());
    	
    }
}