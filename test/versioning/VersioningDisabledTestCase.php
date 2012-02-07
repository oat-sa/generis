<?php

/*
 * Versioning Test Case has been wrote to test versioning features.
 * When versioning is enabled or not.
 */

require_once dirname(__FILE__) . '/../../../tao/test/TestRunner.php';
require_once INCLUDES_PATH.'/simpletest/autorun.php';

class VersioningDisabledTestCase extends UnitTestCase {
    
	private $repositoryUrl = GENERIS_VERSIONED_REPOSITORY_URL;
	private $repositoryPath = GENERIS_VERSIONED_REPOSITORY_PATH;
	private $repositoryType = GENERIS_VERSIONED_REPOSITORY_TYPE;
	private $repositoryLogin = GENERIS_VERSIONED_REPOSITORY_LOGIN;
	private $repositoryPassword = GENERIS_VERSIONED_REPOSITORY_PASSWORD;
	private $repositoryLabel = GENERIS_VERSIONED_REPOSITORY_LABEL;
	private $repositoryComment = GENERIS_VERSIONED_REPOSITORY_COMMENT;
	
	public function __construct()
	{
		parent::__construct();
        if(GENERIS_VERSIONED_REPOSITORY_TYPE == ''){
            $this->repositoryType = 'http://tao.local#VersioningTypeBidon';
        }
	}
	
    public function setUp()
    {
	    TestRunner::initTest();
	}
	
	/* --------------
	 * UNIT TEST CASE TOOLS
	 -------------- */
	
	// Create repository by using generis API
	public function createRepository()
	{
		return core_kernel_versioning_Repository::create(
			new core_kernel_classes_Resource($this->repositoryType),
			$this->repositoryUrl,
			$this->repositoryLogin,
			$this->repositoryPassword,
			$this->repositoryPath,
			$this->repositoryLabel,
			$this->repositoryComment
		);
	}
	
	/* --------------
	 * REPOSITORY
	 -------------- */
	
	// Create repository by using generis API
	public function testCreateRepository()
	{
		$repository = core_kernel_versioning_Repository::create(
			new core_kernel_classes_Resource($this->repositoryType),
			$this->repositoryUrl,
			$this->repositoryLogin,
			$this->repositoryPassword,
			$this->repositoryPath,
			$this->repositoryLabel,
			$this->repositoryComment
		);
		
		$VersioningRepositoryUrlProp = new core_kernel_classes_Property(PROPERTY_GENERIS_VERSIONEDREPOSITORY_URL);
		$VersioningRepositoryPathProp = new core_kernel_classes_Property(PROPERTY_GENERIS_VERSIONEDREPOSITORY_PATH);
		$VersioningRepositoryTypeProp = new core_kernel_classes_Property(PROPERTY_GENERIS_VERSIONEDREPOSITORY_TYPE);
		$VersioningRepositoryLoginProp = new core_kernel_classes_Property(PROPERTY_GENERIS_VERSIONEDREPOSITORY_LOGIN);
		$VersioningRepositoryPasswordProp = new core_kernel_classes_Property(PROPERTY_GENERIS_VERSIONEDREPOSITORY_PASSWORD);
		
		$this->assertEqual((string)$repository->getOnePropertyValue($VersioningRepositoryUrlProp), $this->repositoryUrl);
		$this->assertEqual((string)$repository->getOnePropertyValue($VersioningRepositoryPathProp), $this->repositoryPath);
		$this->assertEqual($repository->getOnePropertyValue($VersioningRepositoryTypeProp)->uriResource, $this->repositoryType);
		$this->assertEqual((string)$repository->getOnePropertyValue($VersioningRepositoryLoginProp), $this->repositoryLogin);
		$this->assertEqual((string)$repository->getOnePropertyValue($VersioningRepositoryPasswordProp), $this->repositoryPassword);
		
		$repository->delete();
	}
	
	public function testRespositoryCheckout()
	{
		$repository = core_kernel_versioning_Repository::create(
			new core_kernel_classes_Resource('http://www.tao.lu/Ontologies/TAOItem.rdf#VersioningRepositoryTypeSubversion'),
			$this->repositoryUrl,
			$this->repositoryLogin,
			$this->repositoryPassword,
			$this->repositoryPath,
			$this->repositoryLabel,
			$this->repositoryComment
		);
		
		try{
			$repository->checkout();
			$this->assertTrue(false);
		}
        catch(core_kernel_versioning_exception_VersioningDisabledException $e){
			$this->assertTrue(true);
		}
		
	    $this->assertTrue($repository->delete(true));
	}

	/* --------------
	 * FILE
	 -------------- */
	
	// Test versioned file factory
	public function testVersionedFileCreate()
	{
		$repository = $this->createRepository();
	    $instance = core_kernel_versioning_File::create('file_test_case.txt', '/', $repository);
        $this->assertTrue($instance->delete(true));
	    $this->assertTrue($repository->delete(true));
	}
	
	// Test versioned file function add
	public function testVersionedFileAdd()
	{
        $repository = $this->createRepository();
	    $instance = core_kernel_versioning_File::create('file_test_case.txt', '/', $repository);
	    $instance->setContent(__CLASS__.':'.__METHOD__.'()');
        
        //try to add the versioned file to the repository
        try{
            $this->assertFalse($instance->add());
            //the following code should not be executed
            $this->assertFalse(true);
            $this->assertFalse($instance->commit());
        }
        catch(core_kernel_versioning_exception_VersioningDisabledException $e){
            //expected behavior
            $this->assertTrue(true);
        }
        
        //the file should not be versioned
        try{
            $this->assertFalse($instance->isVersioned());
            //the following code should not be executed
            $this->assertFalse(true);
        }
        catch(core_kernel_versioning_exception_VersioningDisabledException $e){
            //expected behavior
            $this->assertTrue(true);
        }
        
        //delete the file and the tao resource
        $filePath = $instance->getAbsolutePath();
        //delete the versioned resource with GENERIS_VERSIONING_ENABLED constant set to true
        // => the resource will be deleted but the file will exist anymore
        $this->assertTrue($instance->delete(true));
        $this->assertFalse(helpers_File::resourceExists($filePath));
        $this->assertTrue(file_exists($filePath));
        //remove the file manually
        $this->assertTrue(unlink($filePath));
        
        //delete the repository
	    $repository->delete(true);
	}
	
	// Test versioned file function commit
	public function testVersionedFileCommit()
	{
		$repository = $this->createRepository();
	    $instance = core_kernel_versioning_File::create('file_test_case.txt', '/', $repository);
	    $instance->setContent(__CLASS__.':'.__METHOD__.'()');
        
        //try to add the versioned file to the repository
        try{
            //commit without add, the system should throw an exception anymore
            $this->assertFalse($instance->commit());
            //the following code should not be executed
            $this->assertFalse(true);
        }
        catch(core_kernel_versioning_exception_VersioningDisabledException $e){
            //expected behavior
            $this->assertTrue(true);
        }
        
        //the file should not be versioned
        try{
            $this->assertFalse($instance->isVersioned());
            //the following code should not be executed
            $this->assertFalse(true);
        }
        catch(core_kernel_versioning_exception_VersioningDisabledException $e){
            //expected behavior
            $this->assertTrue(true);
        }
        
        //delete the file and the tao resource
        $filePath = $instance->getAbsolutePath();
        //delete the versioned resource with GENERIS_VERSIONING_ENABLED constant set to true
        // => the resource will be deleted but the file will exist anymore
        $this->assertTrue($instance->delete(true));
        $this->assertFalse(helpers_File::resourceExists($filePath));
        $this->assertTrue(file_exists($filePath));
        //remove the file manually
        $this->assertTrue(unlink($filePath));
        
        //delete the repository
	    $repository->delete(true);
	}
	
	// Test versioned file test history
	public function testHistory()
	{
		$repository = $this->createRepository();
	    $instance = core_kernel_versioning_File::create('file_test_case.txt', '/', $repository);
	    $instance->setContent(__CLASS__.':'.__METHOD__.'()');
		
        //try to get the history of a versioned file
        try{
            $this->assertFalse($instance->getHistory());
            //the following code should not be executed
            $this->assertFalse(true);
        }
        catch(core_kernel_versioning_exception_VersioningDisabledException $e){
            //expected behavior
            $this->assertTrue(true);
        }
        
		//delete the file and the tao resource
        $filePath = $instance->getAbsolutePath();
        //delete the versioned resource with GENERIS_VERSIONING_ENABLED constant set to true
        // => the resource will be deleted but the file will exist anymore
        $this->assertTrue($instance->delete(true));
        $this->assertFalse(helpers_File::resourceExists($filePath));
        $this->assertTrue(file_exists($filePath));
        //remove the file manually
        $this->assertTrue(unlink($filePath));
        
        //delete the repository
	    $repository->delete(true);
	}
	
	// Test versioned file revert
	public function testRevertTo()
	{
		$repository = $this->createRepository();
	    $instance = core_kernel_versioning_File::create('file_test_case.txt', '/', $repository);
	    $instance->setContent(__CLASS__.':'.__METHOD__.'()');
		
        //try to get the history of a versioned file
        try{
            $this->assertFalse($instance->revert(0));
            //the following code should not be executed
            $this->assertFalse(true);
        }
        catch(core_kernel_versioning_exception_VersioningDisabledException $e){
            //expected behavior
            $this->assertTrue(true);
        }
        
		//delete the file and the tao resource
        $filePath = $instance->getAbsolutePath();
        //delete the versioned resource with GENERIS_VERSIONING_ENABLED constant set to true
        // => the resource will be deleted but the file will exist anymore
        $this->assertTrue($instance->delete(true));
        $this->assertFalse(helpers_File::resourceExists($filePath));
        $this->assertTrue(file_exists($filePath));
        //remove the file manually
        $this->assertTrue(unlink($filePath));
        
        //delete the repository
	    $repository->delete(true);
	}
}
