<?php

require_once dirname(__FILE__).'/../common/common.php';
require_once INCLUDES_PATH.'/simpletest/autorun.php';




class FileTestCase extends UnitTestCase {
    
    public function setUp(){
	    TestRunner::initTest();
	}
	
	public function testIsFile(){
	    $clazz = new core_kernel_classes_Class(CLASS_GENERIS_FILE);
	    $instance = $clazz->createInstance('toto.txt','toto');
	    $filePathProp = new core_kernel_classes_Property(PROPERTY_FILE_FILEPATH);
	    $fileNameProp = new core_kernel_classes_Property(PROPERTY_FILE_FILENAME);
	    $instance->setPropertyValue($fileNameProp,'file://toto.txt');
	    $this->assertTrue(core_kernel_classes_File::isFile($instance));
	    $this->assertFalse(core_kernel_classes_File::isFile($clazz));
	    $instance->delete();

	}
	
	public function testCreate(){

	    $file = core_kernel_classes_File::create('toto.txt');
	    $this->assertTrue($file instanceof core_kernel_classes_File);
	    $filePathProp = new core_kernel_classes_Property(PROPERTY_FILE_FILEPATH);
	    $fileNameProp = new core_kernel_classes_Property(PROPERTY_FILE_FILENAME);
	    $filePath = $file->getOnePropertyValue($filePathProp);
	    $fileName = $file->getOnePropertyValue($fileNameProp);
	    $this->assertTrue($filePath == GENERIS_FILES_PATH);
	    $this->assertTrue($fileName == 'toto.txt');
	    $file->delete();

	    $file = core_kernel_classes_File::create('toto.txt','/tmp/');
	    $filePath = $file->getOnePropertyValue($filePathProp);
	    $this->assertTrue($filePath == '/tmp/');
	    $file->delete();
	}
	
	public function testGetAbsolutePath(){
	    $file = core_kernel_classes_File::create('toto.txt');
	    $absolutePath = $file->getAbsolutePath();
	    $this->assertTrue($absolutePath == GENERIS_FILES_PATH . 'toto.txt');
	    $file->delete();
	    $file = core_kernel_classes_File::create('toto.txt','/tmp/');	    
	    $absolutePath = $file->getAbsolutePath();
	    $this->assertTrue($absolutePath == '/tmp/toto.txt');
	    $file->delete();

	}
	
	public function testGetFileContent(){
		 $this->assertTrue(file_put_contents(GENERIS_FILES_PATH . 'toto.txt', 'toto')>0);
		 $file = core_kernel_classes_File::create('toto.txt');
	     $fileContent = $file->getFileContent();
	     $this->assertIsA($fileContent,'SplFileInfo');

	     $file->delete();
	     unlink(GENERIS_FILES_PATH . 'toto.txt');
	     $file = core_kernel_classes_File::create('','/tmp/');
	     $fileContent = $file->getFileContent();
	     $this->assertIsA($fileContent,'SplFileInfo');
	     $this->assertTrue($fileContent->isDir());
	     $file->delete();


	}
    
}
	