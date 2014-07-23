<?php
require_once dirname(__FILE__) . '/GenerisPhpUnitTestRunner.php';
include_once dirname(__FILE__) . '/../../tao/includes/raw_start.php';

class KeyValueTest extends GenerisPhpUnitTestRunner {
    
    private static $driver;
    
	function __construct() {
    	//parent::__construct();
    }
    
    public static function setUpBeforeClass() {
        GenerisPhpUnitTestRunner::initTest();
        self::$driver = common_persistence_KeyValuePersistence::getPersistence('keyValueResult');
        $persistances = common_persistence_Manager::getAllPersistances();
        echo count($persistances);
        foreach ($persistances as $persistanceKey=>$persistance) {
            echo "\n$persistanceKey ";
            echo (int)($persistance instanceof common_persistence_KeyValuePersistence);
            echo (int)($persistance instanceof common_persistence_AdvKeyValuePersistence);
        }
    }
    
    public static function tearDownAfterClass() {
        //
    }
    
    public function setUp() {
        //
    }
    
    public function tearDown() {
        //
    }
    
    public function testSet() {
        $this->assertTrue( self::$driver->set('phpUnitTestKey', 1) );
        $this->assertTrue( self::$driver->set('phpUnitTestKey3', '') );
        $this->assertTrue( self::$driver->set('phpUnitTestKey3', NULL) );
    }
    
    public function testGet() {
        $this->assertSame( self::$driver->get('phpUnitTestKey'), 1);
    }
    
    public function testExists() {
        $this->assertTrue( self::$driver->exists('phpUnitTestKey') );
    }
    
    /**
     * @expectedException Aws\DynamoDb\Exception\ValidationException
     */
    public function testIncr() {
        $this->assertTrue( self::$driver->exists('phpUnitTestKey') );
        $this->assertInternalType( 'integer', self::$driver->incr('phpUnitTestKey') );
        $this->assertSame( self::$driver->incr('phpUnitTestKey'), 3 );
        self::$driver->set('phpUnitTestKey2', '1');
        self::$driver->incr('phpUnitTestKey2');
    }

    public function testDel() {
        $this->assertTrue( self::$driver->del('phpUnitTestKey') );
        $this->assertTrue( self::$driver->del('phpUnitTestKey2') );
        $this->assertTrue( self::$driver->del('phpUnitTestKey3') );
    }
    
    
}
