<?php

/*
 * Versioning Test Case has been wrote to test versioning features.
 * When versioning is enabled or not.
 */

require_once dirname(__FILE__) . '/../../../tao/test/TestRunner.php';
require_once INCLUDES_PATH.'/simpletest/autorun.php';

class VersioningEnabledTestCase extends UnitTestCase {
    
	private $repositoryUrl = GENERIS_VERSIONED_REPOSITORY_URL;
	private $repositoryPath = GENERIS_VERSIONED_REPOSITORY_PATH;
	private $repositoryType = 'http://www.tao.lu/Ontologies/TAOItem.rdf#VersioningRepositoryTypeSubversion';
	private $repositoryLogin = GENERIS_VERSIONED_REPOSITORY_LOGIN;
	private $repositoryPassword = GENERIS_VERSIONED_REPOSITORY_PASSWORD;
	private $repositoryLabel = GENERIS_VERSIONED_REPOSITORY_LABEL;
	private $repositoryComment = GENERIS_VERSIONED_REPOSITORY_COMMENT;
	private $envName = 'VERSIONING_TEST_CASE_ENV';
    private $envDeep = 2;
    private $envNbFiles = 12;
    
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

	// Get the default repository of the TAO instance
	protected function getDefaultRepository ()
	{
		$versioningRepositoryClass = new core_kernel_classes_Class(CLASS_GENERIS_VERSIONEDREPOSITORY);
		$repositories = $versioningRepositoryClass->getInstances();
		
		// If the default repository does not exists, create it
		if(!count($repositories)){
			$repository = $this->createRepository();
		}else{
			$repository = array_pop($repositories);
			$repository = new core_kernel_versioning_Repository($repository->uriResource);
		}
		
		return $repository;
	}
	
	// Create repository by creating triples
	protected function createRepository_byTriple()
	{
		$versioningRepositoryClass = new core_kernel_classes_Class(CLASS_GENERIS_VERSIONEDREPOSITORY);
		$repository = $versioningRepositoryClass->createInstance($this->repositoryLabel, $this->repositoryComment);
		
		$VersioningRepositoryUrlProp = new core_kernel_classes_Property(PROPERTY_GENERIS_VERSIONEDREPOSITORY_URL);
		$repository->setPropertyValue($VersioningRepositoryUrlProp, $this->repositoryUrl);
		
		$VersioningRepositoryPathProp = new core_kernel_classes_Property(PROPERTY_GENERIS_VERSIONEDREPOSITORY_PATH);
		$repository->setPropertyValue($VersioningRepositoryPathProp, $this->repositoryPath);
		
		$VersioningRepositoryTypeProp = new core_kernel_classes_Property(PROPERTY_GENERIS_VERSIONEDREPOSITORY_TYPE);
		$repository->setPropertyValue($VersioningRepositoryTypeProp, $this->repositoryType);
		
		$VersioningRepositoryTypeProp = new core_kernel_classes_Property(PROPERTY_GENERIS_VERSIONEDREPOSITORY_LOGIN);
		$repository->setPropertyValue($VersioningRepositoryTypeProp, $this->repositoryLogin);
		
		$VersioningRepositoryTypeProp = new core_kernel_classes_Property(PROPERTY_GENERIS_VERSIONEDREPOSITORY_PASSWORD);
		$repository->setPropertyValue($VersioningRepositoryTypeProp, $this->repositoryPassword);
		
		return $repository;
	}
	
	// Create repository by using generis API
	protected function createRepository()
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
	
	
	// Create version file sample by creating triples
	protected function createVersionedFile_byTriple()
	{
		$clazz = new core_kernel_classes_Class(CLASS_GENERIS_VERSIONEDFILE);
	    $instance = $clazz->createInstance('myVersionedFile');
	    
	    // Add version number
	    $versionedFileVersionProp = new core_kernel_classes_Property(PROPERTY_VERSIONEDFILE_VERSION);
	    $instance->setPropertyValue($versionedFileVersionProp, '1');
	    
	    // Add path
	    $versionedFilePathProp = new core_kernel_classes_Property(PROPERTY_FILE_FILEPATH);
	    $instance->setPropertyValue($versionedFilePathProp, $this->repositoryPath.'/');
	    
	    // Add filename
	    $versionedFilenameProp = new core_kernel_classes_Property(PROPERTY_FILE_FILENAME);
	    $instance->setPropertyValue($versionedFilenameProp, 'myFile.txt');
	    
	    // Add repository
	    $versionedFileRepositoryProp = new core_kernel_classes_Property(PROPERTY_VERSIONEDFILE_REPOSITORY);
	    $instance->setPropertyValue($versionedFileRepositoryProp, $this->getDefaultRepository());
	    
	    $instance = new core_kernel_versioning_File($instance->uriResource);
	    
	    return $instance;
	}
	
    // Create env folder with some folders and files
    protected function createEnvTest($rootPath=null, $dirName=null, $deep=null)
    {
        $rootPath = !is_null($rootPath) ? $rootPath : $this->getDefaultRepository()->getPath();
        $deep =     !is_null($deep)     ? $deep     : $this->envDeep;
        $dirName =  !is_null($dirName)  ? $dirName  : $this->envName;
        $dirPath = $rootPath.'/'.$dirName;

        //create the folder
        $relativePath = substr($dirPath, strlen($this->getDefaultRepository()->getPath()));
        $instance = core_kernel_versioning_File::create('', $relativePath, $this->getDefaultRepository());
        //if is already versioned, delete the path
        if($instance->isVersioned()){
            $instance->delete();
            $instance = core_kernel_versioning_File::create('', $relativePath, $this->getDefaultRepository());
        }
        
        //create the dir
        mkdir($dirPath);
        
        $this->assertTrue(is_dir($dirPath));
        for($i=0;$i<$this->envNbFiles;$i++){
            $tempnam = tempnam($dirPath, '');
            $this->assertTrue(is_file($tempnam));
        }
        
        //add & commit the directory
        $instance->add(true);
        $instance->commit();
        
        if($deep > 0){
            $this->createEnvTest($dirPath, 'DIR_'.$deep, $deep-1);
        }
        
        return $instance;
    }
    
	/* --------------
	 * UNIT TEST CASE - REPOSITORY
	 -------------- */

	public function testModel()
	{	
		$this->assertTrue(defined('CLASS_GENERIS_VERSIONEDFILE'));
	}
	
	public function testRepositoryModel()
	{
		$repository = $this->getDefaultRepository();
		
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
	}
	
	public function testRepositoryCreate()
	{
		$repository = $this->createRepository();
				
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
		
		$repository->delete(true);
	}
	
	// Test the repository's type
	public function testRepositoryType()
	{
	    $repository = $this->getDefaultRepository();
	    $type = $repository->getType();
	    $this->assertTrue($type->uriResource, 'http://www.tao.lu/Ontologies/TAOItem.rdf#VersioningRepositoryTypeSubversion');
	}
	
	public function testRepositoryAuthenticate()
	{
		$repository = $this->getDefaultRepository();
		
		 // @NOTE If a valid conexion has been established with a remote server during the session
		 //  => The access (login/pass) of this session can not be dropped
		$repository->editPropertyValues(new core_kernel_classes_Property(PROPERTY_GENERIS_VERSIONEDREPOSITORY_LOGIN), 'bad_login');
		$this->assertFalse($repository->authenticate());
		$repository->editPropertyValues(new core_kernel_classes_Property(PROPERTY_GENERIS_VERSIONEDREPOSITORY_LOGIN), $this->repositoryLogin);
		$this->assertTrue($repository->authenticate());
	}
	
	public function testRespositoryCheckout()
	{
		$repository = $this->getDefaultRepository();
		$path = $repository->getPath();
		$repository->checkout();
	}

	// --------------
	// UNIT TEST CASE - FILE
	// -------------- 

	public function testVersionedFileModel()
	{
		$versionedFile = $this->createVersionedFile_byTriple();
		
	    $versionedFileVersionProp = new core_kernel_classes_Property(PROPERTY_VERSIONEDFILE_VERSION);
		$this->assertEqual((string)$versionedFile->getOnePropertyValue($versionedFileVersionProp), '1');
		
	    $versionedFileRepositoryProp = new core_kernel_classes_Property(PROPERTY_VERSIONEDFILE_REPOSITORY);
		$this->assertEqual($versionedFile->getOnePropertyValue($versionedFileRepositoryProp)->uriResource, $this->getDefaultRepository()->uriResource);
		
		$versionedFile->delete();
	}
	
	// Test if a resource is a versioned file
	public function testIsVersionedFile()
	{
	    $instance = $this->createVersionedFile_byTriple();
	    $this->assertTrue(core_kernel_versioning_File::isVersionedFile($instance));
	    $instance->delete();
	}
	
	// Test versioned file factory
	public function testVersionedFileCreate()
	{
	    $instance = core_kernel_versioning_File::create('file_test_case.txt', '/', $this->getDefaultRepository());
	    $this->assertFalse($instance->isVersioned()); //the file is not yet versioned
	    $this->assertFalse($instance->fileExists()); //the file does not exist in file system
	    $this->assertFalse($instance->fileExists()); //the file is not yet versioned
	    $this->assertFalse(file_exists($instance->getAbsolutePath()));
	    $instance->delete(true);
	}
	
	// Test the versioned file proxy
	public function testVersioningProxy()
	{
        // @todo This test is dedicated to the subversion implementation, be carrefull!!!
         
	    $instance = core_kernel_versioning_File::create('file_test_case.txt', '/', $this->getDefaultRepository());
	    $implementationToDelegateTo = core_kernel_versioning_FileProxy::singleton()->getImplementationToDelegateTo($instance);
	    $this->assertTrue($implementationToDelegateTo instanceof core_kernel_versioning_subversion_File);
	    $instance->delete(true);
	}
	
	// Test versioned file function add
	public function testVersionedFileAdd()
	{
	    $instance = core_kernel_versioning_File::create('file_test_case.txt', '/', $this->getDefaultRepository());
	    $instance->setContent('test');
	    $this->assertFalse($instance->isVersioned()); //the file is not yet versioned
	    $this->assertTrue($instance->fileExists()); //the file does not exist in file system
	    $instance->add();
	    $instance->delete(true);
	}
	
	// Test versioned file function commit
	public function testVersionedFileCommit()
	{
	    $instance = core_kernel_versioning_File::create('file_test_case.txt', '/', $this->getDefaultRepository());
	    $instance->setContent('test');
	    $instance->add();
	    $instance->commit();
	    $instance->delete(true);
	}
    
	// Test if the resource has local changes
	public function testHasLocalChanges()
	{
	    $instance = core_kernel_versioning_File::create('file_test_case.txt', '/', $this->getDefaultRepository());
	    $instance->setContent('test');
	    $instance->add();
	    $instance->commit();
	    $instance->setContent('test');
	    $this->assertFalse($instance->hasLocalChanges());
	    $instance->setContent('test gna');
	    $this->assertTrue($instance->hasLocalChanges());
	    $instance->delete(true);
	}
	
	public function testIsVersioned()
	{
		$instance = core_kernel_versioning_File::create('file_test_case.txt', '/', $this->getDefaultRepository());
	    // The file is not added, he has SVN_WC_STATUS_UNVERSIONED status
	    $this->assertFalse($instance->isVersioned());
	    $instance->setContent('test');
	    
	    $instance->add();
	    // The file is just added, he has SVN_WC_STATUS_ADDED status
	    $this->assertFalse($instance->isVersioned());
	    
	    $instance->commit();
	    // The file is commited, he is considered as versioned (svn_status does not return status)
	    $this->assertTrue($instance->isVersioned());
	    
	    $instance->setContent('content');
	    // The file is updated, he has update
	    $this->assertTrue($instance->isVersioned());
	    
	    // The file is deleted, and the change is commited. The file does not exist anymore in local and in the remote repository
	    $instance->delete(true);
	}
	
	// Test versioned file function delete
	public function testVersionedFileDelete()
	{
		// The function is tested in other tests :
		// * Delete unversioned file
		// * Delete a file which has been added to the repo
		// * Delete a file which has been commited
	}
	
	// Test versioned file function update
	public function testVersionedFileUpdate()
	{
		$repository1 = $this->getDefaultRepository();
		$instance = core_kernel_versioning_File::create('file_test_case.txt', '/', $repository1);
	    $instance->setContent('test');
	    $instance->add();
	    $instance->commit();
        $this->assertTrue($instance->isVersioned());
	    
	    // Update the file from another repository
	    $repository2 = core_kernel_versioning_Repository::create(
			new core_kernel_classes_Resource('http://www.tao.lu/Ontologies/TAOItem.rdf#VersioningRepositoryTypeSubversion'),
			$this->repositoryUrl,
			$this->repositoryLogin,
			$this->repositoryPassword,
			GENERIS_FILES_PATH.'/versioning/TMP_TEST_CASE_REPOSITORY',
			'TMP Repository',
			'TMP Repository'
		);
		$this->assertTrue($repository2->checkout());
		$file = core_kernel_versioning_File::create('file_test_case.txt', '/', $repository2);
		$this->assertTrue($file->setContent('updated'));
		$this->assertTrue($file->commit());
		$this->assertTrue($repository2->delete(true));
		tao_helpers_File::remove(GENERIS_FILES_PATH.'/versioning/TMP_TEST_CASE_REPOSITORY', true);
	    
		// Test the file has been updated in the first repository
		$this->assertTrue($instance->update());
		$this->assertEqual($instance->getFileContent(), 'updated');
		
	    $instance->delete(true);
	}
	
	//test the versioning function revert without parameter (revert local change)
	public function testRevert()
	{
		$instance = core_kernel_versioning_File::create('file_test_case.txt', '/', $this->getDefaultRepository());
	    $instance->setContent('my content');
	    $instance->add();
	    $instance->commit('commit message 1');
	    
		$this->assertEqual($instance->getFileContent(), 'my content');
	    $instance->setContent('my updated content');
		$this->assertEqual($instance->getFileContent(), 'my updated content');
	    $this->assertTrue($instance->revert());
		$this->assertEqual($instance->getFileContent(), 'my content');
	    
	    $instance->delete(true);
	}
	
	public function testHistory()
	{
		$instance = core_kernel_versioning_File::create('file_test_case.txt', '/', $this->getDefaultRepository());
	    $instance->setContent('my content');
	    $instance->add();
	    $instance->commit('commit message 1');
	    
	    $instance->setContent('my updated content');
	    $instance->commit('commit message 2');
	    
	    $instance->setContent('my new updated content');
	    $instance->commit('commit message 3');
	    
	    $history = $instance->getHistory();
	    
	    $this->assertEqual($history[0]['msg'], 'commit message 3');
	    $this->assertEqual($history[1]['msg'], 'commit message 2');
	    $this->assertEqual($history[2]['msg'], 'commit message 1');
	    
	    $instance->delete(true);
	}
	
	public function testVersion()
	{
		$instance = core_kernel_versioning_File::create('file_test_case.txt', '/', $this->getDefaultRepository());
	    $instance->setContent('my content');
	    $instance->add();
	    $instance->commit('commit message 1');
	    $this->assertEqual($instance->getVersion(), 1);
	    
	    $instance->setContent('my content 2');
	    $instance->commit('commit message 2');
	    $this->assertEqual($instance->getVersion(), 2);
	    
	    $instance->delete(true);
	}
	
	public function testRevertTo()
	{
		$instance = core_kernel_versioning_File::create('file_test_case.txt', '/', $this->getDefaultRepository(), "", array(
	    	'add'		=>true,
	    	'commit'	=>true
	    ));
	    $this->assertTrue($instance->setContent('my content 1'));
	    $this->assertTrue($instance->add());
	    $this->assertTrue($instance->commit('commit message 1'));
	    $this->assertTrue($instance->isVersioned());
		$this->assertEqual($instance->getFileContent(), 'my content 1');
	    $this->assertEqual($instance->getVersion(), 1);
		
	    $this->assertTrue($instance->setContent('my content 2'));
		$this->assertTrue($this->assertEqual($instance->getFileContent(), 'my content 2'));
	    $this->assertTrue($instance->commit('commit message 2'));
	    $this->assertEqual($instance->getVersion(), 2);
	    
	    $this->assertTrue($instance->setContent('my content 3'));
		$this->assertTrue($this->assertEqual($instance->getFileContent(), 'my content 3'));
	    $this->assertTrue($instance->commit('commit message 3'));
	    $this->assertEqual($instance->getVersion(), 3);
	    
	    $history = $instance->getHistory();
	    
	    // Revert to first revision
	    $instance->revert(1);
	    $this->assertTrue($instance->fileExists());
	    $this->assertTrue($instance->isVersioned());
		$this->assertEqual($instance->getFileContent(), 'my content 1');
	    $this->assertEqual($instance->getVersion(), 4);
	    
		// Revert to second revision
	    $instance->revert(2);
	    $this->assertTrue($instance->fileExists());
	    $this->assertTrue($instance->isVersioned());
		$this->assertEqual($instance->getFileContent(), 'my content 2');
	    $this->assertEqual($instance->getVersion(), 5);
		
		$instance->delete(true);
	}
	
	// Delete the test repository
	public function testDeleteVersionedRepository()
	{
		//$this->getDefaultRepository()->delete();
	}


    ///////////////////////////////////////////////////////////////////////////
    //  MANAGE FOLDER WITH THE VERSIONING API
    ///////////////////////////////////////////////////////////////////////////
    
    //Test list content
    public function testListContentRepository()
    {
        //create the env test
        $rootFile = $this->createEnvTest();
        $filePathName = '';
        //list folder content
        $repository = $this->getDefaultRepository();
        //file path
	    $versionedFilePathProp = new core_kernel_classes_Property(PROPERTY_FILE_FILEPATH);
	    $filePath = (string)$rootFile->getOnePropertyValue($versionedFilePathProp);
	    //file name
	    $versionedFilenameProp = new core_kernel_classes_Property(PROPERTY_FILE_FILENAME);
	    $fileName= (string)$rootFile->getOnePropertyValue($versionedFilenameProp);
        //build file path
        $filePathName = $filePath.$fileName;
        //list folder content
        $list = $repository->listContent($filePathName);
        //remove the env test
        //$rootFile->delete();
    }
}
