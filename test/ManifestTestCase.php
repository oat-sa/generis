<?php
require_once dirname(__FILE__) . '/GenerisTestRunner.php';


class ModelsRightTestCase extends UnitTestCase {
	
	const SAMPLES_PATH = '/samples/manifests/';
	const MANIFEST_PATH_DOES_NOT_EXIST = 'idonotexist.php';
	const MANIFEST_PATH_LIGHTWEIGHT = 'lightweightManifest.php';
	
	public function setUp(){
        GenerisTestRunner::initTest();
	}
	
	public function testManifestLoading(){
		$currentPath = dirname(__FILE__);
		
		// try to load a manifest that does not exists.
		try{
			$manifestPath = $currentPath . self::SAMPLES_PATH . self::MANIFEST_PATH_DOES_NOT_EXIST;
			$manifest = new common_ext_Manifest($manifestPath);
			$this->assertTrue(false, "Trying to load a manifest that does not exist should raise an exception");
		}
		catch (Exception $e){
			$this->assertIsA($e, 'common_ext_ManifestNotFoundException');
		}
		
		// Load a simple lightweight manifest that exists and is well formed.
		$manifestPath = $currentPath . self::SAMPLES_PATH . self::MANIFEST_PATH_LIGHTWEIGHT;
		try{
			$manifest = new common_ext_Manifest($manifestPath);
			$this->assertIsA($manifest, 'common_ext_Manifest');
			$this->assertEqual($manifest->getName(), 'lightweight');
			$this->assertEqual($manifest->getDescription(), 'lightweight testing manifest');
			$this->assertEqual($manifest->getVersion(), '1.0');
			$this->assertEqual($manifest->getAuthor(), 'TAO Team');
		}
		catch (common_ext_ManifestNotFoundException $e){
			$this->assertTrue(false, "Trying to load a manifest that exists and well formed should not raise an exception.");
		}
	}
}