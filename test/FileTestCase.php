<?php
/*  
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * 
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
?>
<?php

require_once dirname(__FILE__) . '/GenerisTestRunner.php';


class FileTestCase extends UnitTestCase {
    
    public function setUp()
    {
        GenerisTestRunner::initTest();
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
	    $this->assertEqual($absolutePath, GENERIS_FILES_PATH . 'toto.txt');
	    $this->assertTrue($file->delete());
	    
	    $file = core_kernel_classes_File::create('toto.txt', DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR);	    
	    $absolutePath = $file->getAbsolutePath();
	    $this->assertEqual($absolutePath, DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR . 'toto.txt');
	    
	    $this->assertTrue($file->delete());
	}
	
	public function testGetFileInfo()
	{
	    $file = core_kernel_classes_File::create('toto.txt');
	    $file->setContent('toto is kite surfing !!! le ouf');
	    $fileInfo = $file->getFileInfo();
	    $this->assertIsA($fileInfo, 'SplFileInfo');
		$this->assertTrue($file->delete());
		
		$file = core_kernel_classes_File::create('',sys_get_temp_dir());
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
		
		$file = core_kernel_classes_File::create('',sys_get_temp_dir());
	    $fileContent = $file->getFileContent();
	    $this->assertTrue($file->delete());
	}
    
    //The the resource file exists function
    public function testResourceFileExists()
    {
    	// Create a correct path...
    	$file = 'FileTestCase_testResourceFileExists';
    	$dir = rtrim(sys_get_temp_dir(), "\\/") . DIRECTORY_SEPARATOR;
    	$path = $dir . $file;
    	
        $this->assertFalse(helpers_File::resourceExists($path));
        $file = core_kernel_classes_File::create($file, $dir, 'FileTestCase_testResourceFileExists_URI');
        $this->assertTrue(helpers_File::resourceExists($path));
        $this->assertFalse(helpers_File::resourceExists('test'));
        $file->delete();
    }
    
    //The the resource get resource file function
    public function testGetResourceFile()
    {
    	$file = 'FileTestCase_testResourceFileExists'; 
    	$dir = rtrim(sys_get_temp_dir(), "\\/") . DIRECTORY_SEPARATOR;
    	$path = $dir . $file;
    	
        $this->assertNull(helpers_File::getResource($path));
        $file = core_kernel_classes_File::create($file, $dir, 'FileTestCase_testResourceFileExists_URI');
        $searchedFile = helpers_File::getResource($path);
        $this->assertNotNull($searchedFile);
        $this->assertTrue($searchedFile instanceof core_kernel_classes_File);
        $file->delete();
        $this->assertNull(helpers_File::getResource($path));
    }
    
}
	