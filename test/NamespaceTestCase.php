<?php
require_once dirname(__FILE__) . '/GenerisTestRunner.php';

/**
 * Test of the common_ext_Namespace and common_ext_NamesapceManager
 * 
 * @author Bertrand Chevrier <bertrand.chevrier@tudor.lu>
 * @package generis
 * @subpackage test
 */
class NamespaceTestCase extends UnitTestCase {
	
	public function setUp(){
        GenerisTestRunner::initTest();
	}

	/**
	 * Tes if the model is correctly loaded, especially the manager singleton
	 */
	public function testModel(){
		$namespaceManager = common_ext_NamespaceManager::singleton();
		$this->assertIsA($namespaceManager, 'common_ext_NamespaceManager');
		
		$this->assertReference($namespaceManager, common_ext_NamespaceManager::singleton());
		
		$tempNamesapce = new common_ext_Namespace();
		$this->assertIsA($tempNamesapce, 'common_ext_Namespace');
	}
	
	/**
	 * test the manager retrieving methods and the namespace setters/getters
	 */
	public function testBehaviour(){
		$namespaceManager = common_ext_NamespaceManager::singleton();
		$namespaces = $namespaceManager->getAllNamespaces();
		$this->assertTrue(count($namespaces) > 0);
		
		foreach($namespaces as $namespace){
			$this->assertIsA($namespace, 'common_ext_Namespace');
		}
		
		$localNs = $namespaceManager->getLocalNamespace();
		$this->assertIsA($localNs, 'common_ext_Namespace');

		$otherLocalNs = $namespaceManager->getNamespace($localNs->getModelId());
		$this->assertIsA($otherLocalNs, 'common_ext_Namespace');
		
		$this->assertEqual((string)$otherLocalNs, (string)$localNs);
	}
	
}
?>