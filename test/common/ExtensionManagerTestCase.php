<?php

error_reporting(E_ALL);
require_once dirname(__FILE__).'/../../common/common.php';
require_once INCLUDES_PATH.'/simpletest/autorun.php';



/**
 * Test class for ExtensionManager
 * 
 * @author lionel.lecaque@tudor.lu
 * @package test
 */


class ExtensionManagerTestCase extends UnitTestCase {
	
	public function testGetInstalledExtensions(){
		$db = core_kernel_classes_DbWrapper::singleton(DATABASE_NAME);
		$fakeExtensionSql = "INSERT INTO `extensions` (`id`, `name`, `version`, `loaded`, `loadAtStartUp`) 
							VALUES ('testExtension', 'test', '0.1', 1, 1);";
		
		$this->assertTrue($db->execSql($fakeExtensionSql));
		$extensinoManager = common_ext_ExtensionsManager::singleton();
		try{
			$ext = $extensinoManager->getInstalledExtensions();
		}
		catch(common_ext_ExtensionException $ee){
			$this->assertEqual('Extension Manifest not found : <b>testExtension</b>', $ee->getMessage());
		}
		
		mkdir(EXTENSION_PATH.'/testExtension');
		file_put_contents(EXTENSION_PATH.'/testExtension/'.MANIFEST_NAME, "
			<?php
				return array(
					'name' => 'Test Extension',
					'description' => 'Test Extension',
					'additional' => array(
						'version' => '0.25',
						'author' => 'CRP Henry Tudor',
						'dependances' => array('test01'),
						'models' => array(),
						'install' => array( 
							'sql' => dirname(__FILE__). '/install/db/testExtension.sql',
							'php' => dirname(__FILE__). '/install/install.php'
						),
						'registerToClassLoader' => true,
						'configFile' => dirname(__FILE__). '/includes/common.php',
						'classLoaderPackages' => array( 
							dirname(__FILE__).'/actions/' , 
							dirname(__FILE__).'/models/',
						)
					)
				);
			?>
		");
		
		$this->assertTrue(file_exists(EXTENSION_PATH.'/testExtension/'.MANIFEST_NAME));
		
		$extensinoManager->reset();
		$ext = $extensinoManager->getInstalledExtensions();
		$this->assertTrue(isset($ext['testExtension']));
		
		$this->assertEqual($ext['testExtension']->author, 'CRP Henry Tudor');
		$this->assertEqual($ext['testExtension']->name, 'Test Extension');
		$this->assertEqual($ext['testExtension']->version, '0.25');
		
		unlink(EXTENSION_PATH.'/testExtension/'.MANIFEST_NAME);
		rmdir(EXTENSION_PATH.'/testExtension');
		
		$this->assertFalse(file_exists(EXTENSION_PATH.'/testExtension/'.MANIFEST_NAME));
		
		$fakeExtensionSql = "DELETE FROM `extensions` where  `id` = 'testExtension'";
		$this->assertTrue($db->execSql($fakeExtensionSql));
	}
	

	
	public function testGetModelsToLoad(){
		$extensinoManager = common_ext_ExtensionsManager::singleton();
		$models = $extensinoManager->getModelsToLoad();
		$this->assertTrue(is_array($models));
		$this->assertTrue(count($models) > 0);
		$this->assertTrue(in_array('http://www.w3.org/1999/02/22-rdf-syntax-ns', $models));
		$this->assertTrue(in_array('http://www.w3.org/2000/01/rdf-schema', $models));
		$this->assertTrue(in_array('http://www.tao.lu/Ontologies/generis.rdf', $models));
	}
}