<?php

require_once dirname(__FILE__) . '/../../tao/test/TestRunner.php';
require_once INCLUDES_PATH.'/simpletest/autorun.php';


class FileTestCase extends UnitTestCase {
    
    public function setUp()
    {
	    TestRunner::initTest();
	}
	
	public function testIsFile()
	{
	    $clazz = new core_kernel_classes_Class(CLASS_GENERIS_FILE);
	    $instance = $clazz->createInstance('toto.txt','toto');
	    $filePathProp = new core_kernel_classes_Property(PROPERTY_FILE_FILEPATH);
	    $fileNameProp = new core_kernel_classes_Property(PROPERTY_FILE_FILENAME);
	    $instance->setPropertyValue($fileNameProp,'file://toto.txt');
	    $this->assertTrue(core_kernel_classes_File::isFile($instance));
	    $this->assertFalse(core_kernel_classes_File::isFile($clazz));
	    $instance->delete();
	}
	
	public function testCreate()
	{
	    $file = core_kernel_classes_File::create('toto.txt');
	    $this->assertTrue($file instanceof core_kernel_classes_File);
	    $filePathProp = new core_kernel_classes_Property(PROPERTY_FILE_FILEPATH);
	    $fileNameProp = new core_kernel_classes_Property(PROPERTY_FILE_FILENAME);
	    $filePath = $file->getOnePropertyValue($filePathProp);
	    $fileName = $file->getOnePropertyValue($fileNameProp);
	    $this->assertTrue($filePath == GENERIS_FILES_PATH);
	    $this->assertTrue($fileName == 'toto.txt');
	    $this->assertTrue($file->delete());
	    
	    $file = core_kernel_classes_File::create('toto.txt','/tmp/');
	    $filePath = $file->getOnePropertyValue($filePathProp);
	    $this->assertTrue($filePath == '/tmp/');
	    $this->assertTrue($file->delete());
	    
	    // Create dir
	    $dir = core_kernel_classes_File::create('', '/tmp/myDir');
	}
	
	public function testGetAbsolutePath()
	{
	    $file = core_kernel_classes_File::create('toto.txt');
	    $absolutePath = $file->getAbsolutePath();
	    $this->assertTrue($absolutePath == GENERIS_FILES_PATH . 'toto.txt');
	    $this->assertTrue($file->delete());
	    
	    $file = core_kernel_classes_File::create('toto.txt','/tmp/');	    
	    $absolutePath = $file->getAbsolutePath();
	    $this->assertTrue($absolutePath == '/tmp/toto.txt');
	    $this->assertTrue($file->delete());
	}
	
	public function testGetFileInfo()
	{
	    $file = core_kernel_classes_File::create('toto.txt');
	    $file->setContent('toto is kite surfing !!! le ouf');
	    $fileInfo = $file->getFileInfo();
	    $this->assertIsA($fileInfo, 'SplFileInfo');
		$this->assertTrue($file->delete());
		
		$file = core_kernel_classes_File::create('','/tmp/');
	    $fileInfo = $file->getFileInfo();
	    $this->assertIsA($fileInfo,'SplFileInfo');
	    $this->assertTrue($fileInfo->isDir());
	    $this->assertTrue($file->delete());
	}
	
	public function testSetGetFileContent()
	{
	    $file = core_kernel_classes_File::create('toto.txt', null);
	    $file->setContent('toto is kite surfing !!! le ouf');
	    $fileContent = $file->getFileContent();
	    $this->assertEqual($fileContent,'toto is kite surfing !!! le ouf');
		$this->assertTrue($file->delete(true));
		
		$file = core_kernel_classes_File::create('','/tmp/');
	    $fileContent = $file->getFileContent();
	    $this->assertTrue($file->delete());
	}
    
}
	