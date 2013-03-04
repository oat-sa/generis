<?php

require_once dirname(__FILE__) . '/GenerisTestRunner.php';


class FileHelperTestCase extends UnitTestCase {
    
    public function setUp()
    {
        GenerisTestRunner::initTest();
    }
    
	public function testRemoveFile()
	{
		$basedir	= $this->mkdir(sys_get_temp_dir());
		$this->assertTrue(is_dir($basedir));
		$file01		= tempnam($basedir, 'testdir');
		$file02		= tempnam($basedir, 'testdir');
		
		$subDir1	= $this->mkdir($basedir);
		
		$subDir2	= $this->mkdir($basedir);
		$file21		= tempnam($subDir2, 'testdir');
		$subDir21	= $this->mkdir($subDir2);
		$file211	= tempnam($subDir21, 'testdir');
		$subDir22	= $this->mkdir($subDir2);
		helpers_File::remove($basedir);
		$this->assertFalse(is_dir($basedir));
	}
	
	private function mkdir($basePath) {
		$file = tempnam($basePath, 'dir');
		$this->assertTrue(unlink($file));
		$this->assertTrue(mkdir($file));
		return $file;
	}
	
}
	