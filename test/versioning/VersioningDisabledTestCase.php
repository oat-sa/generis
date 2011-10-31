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
	private $repositoryType = 'http://www.tao.lu/Ontologies/TAOItem.rdf#VersioningRepositoryTypeSubversion';
	private $repositoryLogin = GENERIS_VERSIONED_REPOSITORY_LOGIN;
	private $repositoryPassword = GENERIS_VERSIONED_REPOSITORY_PASSWORD;
	private $repositoryLabel = GENERIS_VERSIONED_REPOSITORY_LABEL;
	private $repositoryComment = GENERIS_VERSIONED_REPOSITORY_COMMENT;
	
	public function __construct()
	{
		parent::__construct();
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
			new core_kernel_classes_Resource('http://www.tao.lu/Ontologies/TAOItem.rdf#VersioningRepositoryTypeSubversion'),
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
			new core_kernel_classes_Resource('http://www.tao.lu/Ontologies/TAOItem.rdf#VersioningRepositoryTypeSubversion'),
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
	
	public function testRespositoryAuthenticate()
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
			$repository->authenticate();
		}catch(core_kernel_versioning_VersioningDisabledException $e){
			$this->assertTrue(true);
		}
		
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
		}catch(core_kernel_versioning_VersioningDisabledException $e){
			$this->assertTrue(true);
		}
		
		$repository->delete();
	}

	/* --------------
	 * FILE
	 -------------- */
	
	// Test versioned file factory
	public function testVersionedFileCreate()
	{
		$repository = $this->createRepository();
	    $instance = core_kernel_versioning_File::create('file_test_case.txt', '/', $repository);
	    //$this->assertTrue(core_kernel_versioning_File::isVersionedFile($instance));
	    $repository->delete(true);
	}
	
	// Test versioned file function add
	public function testVersionedFileAdd()
	{
		$repository = $this->createRepository();
	    $instance = core_kernel_versioning_File::create('file_test_case.txt', '/', $repository);
	    $instance->setContent('test');
		try{
	    	$instance->add();
			$this->assertTrue(false);
		}catch(core_kernel_versioning_VersioningDisabledException $e){
			$this->assertTrue(true);
		}
		$this->assertFalse($instance->isVersioned());
	    $instance->delete(true);
	    $repository->delete(true);
	}
	
	// Test versioned file function commit
	public function testVersionedFileCommit()
	{
		$repository = $this->createRepository();
	    $instance = core_kernel_versioning_File::create('file_test_case.txt', '/', $repository);
	    $instance->setContent('test');
		try{
	    	$instance->add();
	    	$instance->commit();
			$this->assertTrue(false);
		}catch(core_kernel_versioning_VersioningDisabledException $e){
			$this->assertTrue(true);
		}
		$this->assertFalse($instance->isVersioned());
	    $instance->delete(true);
	    $repository->delete(true);
	}
	
	// Test versioned file test history
	public function testHistory()
	{
		$repository = $this->createRepository();
	    $instance = core_kernel_versioning_File::create('file_test_case.txt', '/', $repository);
	    $instance->setContent('test');
		try{
	    	$instance->getHistory();
			$this->assertTrue(false);
		}catch(core_kernel_versioning_VersioningDisabledException $e){
			$this->assertTrue(true);
		}
		$this->assertFalse($instance->isVersioned());
	    $instance->delete(true);
	    $repository->delete(true);
	}
	
	// Test versioned file revert
	public function testRevertTo()
	{
		$repository = $this->createRepository();
	    $instance = core_kernel_versioning_File::create('file_test_case.txt', '/', $repository);
	    $instance->setContent('test');
		try{
	    	$instance->revert(0);
			$this->assertTrue(false);
		}catch(core_kernel_versioning_VersioningDisabledException $e){
			$this->assertTrue(true);
		}
		$this->assertFalse($instance->isVersioned());
	    $instance->delete(true);
	    $repository->delete(true);
	}
}
