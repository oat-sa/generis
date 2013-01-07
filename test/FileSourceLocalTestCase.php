<?php

require_once dirname(__FILE__) . '/GenerisTestRunner.php';

class FileSourceLocalTestCase extends UnitTestCase {
    
    /**
     * @var core_kernel_versioning_Repository
     */
    private static $repository = null;
    
	public function __construct()
	{
		parent::__construct();
	}
	
    public function setUp()
    {
	    GenerisTestRunner::initTest();
	    
		$versioningRepositoryClass = new core_kernel_classes_Class(CLASS_GENERIS_VERSIONEDREPOSITORY);
		$repository = $versioningRepositoryClass->createInstanceWithProperties(array(
			RDFS_LABEL => 'UnitTestRepository',
			PROPERTY_GENERIS_VERSIONEDREPOSITORY_PATH => sys_get_temp_dir().DIRECTORY_SEPARATOR."testrepo",
			PROPERTY_GENERIS_VERSIONEDREPOSITORY_TYPE => INSTANCE_GENERIS_VCS_TYPE_LOCAL,
			PROPERTY_GENERIS_VERSIONEDREPOSITORY_ENABLED => GENERIS_TRUE
		));
	
		self::$repository = new core_kernel_versioning_Repository($repository);
    }
	
    public function tearDown()
    {
	    self::$repository->delete();
	    parent::tearDown();
	}

	protected function getTestRepository () {
		return self::$repository;
	}

    public function testRepository() {
    	$this->assertIsA($this->getTestRepository(), 'core_kernel_versioning_Repository');
    }
	
}
