<?php
require_once dirname(__FILE__) . '/GenerisPhpUnitTestRunner.php';
include_once dirname(__FILE__) . '/../../tao/includes/raw_start.php';

class AdvKeyValueTest extends GenerisPhpUnitTestRunner {
    
    private static $persistances;
    
    public function persistanceProvider() {
        if (count(self::$persistances) === 0) {
            $persistances = common_persistence_Manager::getAllPersistances();
            echo "\nList of all distinct persistances:";
            foreach ($persistances as $persistanceKey=>$persistance) {
                echo "\n  $persistanceKey [". get_class($persistance['driver']) ."] [".$persistance['driverName']."] ";
                if ( $persistance['driver'] instanceof common_persistence_AdvKeyValuePersistence ) {
                    echo "is AdvKeyValuePersistence ";
                } elseif ( $persistance['driver'] instanceof common_persistence_KeyValuePersistence ) {
                    echo "is KeyValuePersistence ";
                } else {
                    echo "is NOT KeyValuePersistence ";
                }
                // the following line depends on the driver being tested
                if ( $persistance['driver'] instanceof common_persistence_AdvKeyValuePersistence ) {
                    self::$persistances[$persistance['driverName']] = array( $persistance['driver'], $persistance['driverName'] );
                }
            }
            echo "\nSuitable persistances ".count(self::$persistances)."\n\n";
        }
        return self::$persistances;
    }

    public static function setUpBeforeClass() {
        GenerisPhpUnitTestRunner::initTest();
        echo "\n";
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
    
    /**
     * @dataProvider persistanceProvider
     */
    public function testHSetStr($persistance, $driverName) {
        $this->assertTrue( $persistance->hSet('phpUnitTestKeyAdv', 'testFieldStr', 'testValue') );
    }
    
    /**
     * @dataProvider persistanceProvider
     */
    public function testHSetInt($persistance, $driverName) {
        $this->assertTrue( $persistance->hSet('phpUnitTestKeyAdv', 'testFieldInt', 1) );
    }

    /**
     * @dataProvider persistanceProvider
     */
    public function testHGetStr($persistance, $driverName) {
        $persistance->hSet('phpUnitTestKeyAdv', 'testFieldStr', 'testValue');
        $this->assertSame( $persistance->hGet('phpUnitTestKeyAdv', 'testFieldStr'), 'testValue' );
    }
    
    
    /**
     * @dataProvider persistanceProvider
     */
    public function testHGetNonExistentHashKey($persistance, $driverName) {
        $this->assertFalse( $persistance->hGet('phpUnitTestKeyAdv', 'testFieldNonExistent') );
    }
    
    /**
     * @dataProvider persistanceProvider
     */
    public function testHGetNonExistentKey($persistance, $driverName) {
        $persistance->del('phpUnitTestKeyAdv');
        $this->assertFalse( $persistance->hGet('phpUnitTestKeyAdv', 'testFieldNonExistent') );
    }
    
    /**
     * @dataProvider persistanceProvider
     */
    public function testHExistsOnExisting($persistance, $driverName) {
        $persistance->hSet('phpUnitTestKeyAdv', 'testFieldStr', 'testValue');
        $this->assertTrue( $persistance->hExists('phpUnitTestKeyAdv', 'testFieldStr') );
    }
    
    /**
     * @dataProvider persistanceProvider
     */
    public function testHExistsOnNonExisting($persistance, $driverName) {
        $persistance->del('phpUnitTestKeyAdv');
        $this->assertfalse( $persistance->hExists('phpUnitTestKeyAdv', 'testFieldStr') );
    }

    /**
     * @dataProvider persistanceProvider
     */
    public function testHmSet($persistance, $driverName) {
        $persistance->del('phpUnitTestKeyAdvHMSET');
        $hmSetArray = array('phpUnitTestHashKey1'=>'someVal1', 'phpUnitTestHashKey2'=>'someVal2');
        $this->assertTrue( $persistance->hmSet('phpUnitTestKeyAdvHMSET', $hmSetArray) );
    }
    
    /**
     * @dataProvider persistanceProvider
     */
    public function testHGetAll($persistance, $driverName) {
        $result = $persistance->hGetAll('phpUnitTestKeyAdvHMSET');
        $this->assertTrue( (count($result)===2) && (key_exists('phpUnitTestHashKey1', $result) && key_exists('phpUnitTestHashKey2', $result)) && ($result['phpUnitTestHashKey1']==='someVal1' && $result['phpUnitTestHashKey2']==='someVal2') );
        $persistance->del('phpUnitTestKeyAdvHMSET');
    }
    
    /**
     * @dataProvider persistanceProvider
     */
    public function testHGetAllNonExistent($persistance, $driverName) {
        $result = $persistance->hGetAll('phpUnitTestKeyAdvHMSETNonExistent');
        $this->assertInternalType('array', $result);
        $this->assertTrue( count($result)===0 );
    }
    
    /**
     * @dataProvider persistanceProvider
     */
    public function testIncrOfNew($persistance, $driverName) {
        $persistance->del('phpUnitTestKey');
        $result = $persistance->incr('phpUnitTestKey');
        $this->assertSame($result, 1);
        $persistance->del('phpUnitTestKey');
    }

    /**
     * @dataProvider persistanceProvider
     */
    public function testIncrOfInt($persistance, $driverName) {
        $persistance->del('phpUnitTestKey');
        $persistance->set('phpUnitTestKey', 1);
        $result = $persistance->incr('phpUnitTestKey');
        $this->assertSame($result, 2);
        $persistance->del('phpUnitTestKey');
    }
    
    /**
     * @dataProvider persistanceProvider
     */
    public function testIncrOfString($persistance, $driverName) {
        if ($driverName === 'oat\kvDynamoDb\model\DynamoDbDriver') {
            $this->setExpectedException('Aws\DynamoDb\Exception\ValidationException');
        }
        $persistance->del('phpUnitTestKey');
        $persistance->set('phpUnitTestKey', '1');
        $result = $persistance->incr('phpUnitTestKey');
        $this->assertSame($result, 2);
        $persistance->del('phpUnitTestKey');
    }
    
    /**
     * @dataProvider persistanceProvider
     */
    public function testKeys($persistance, $driverName) {
        $persistance->set('phpUnitTestKeysKey1', 'someVal');
        $persistance->set('phpUnitTestKeysKey2', 'someVal');
        $persistance->set('phpUnitTestKeysKey3', 'someVal');
        $keys = $persistance->keys('phpUnitTestKeysKey*');
        sort($keys);
        $this->assertTrue( (count($keys)===3) && ($keys[0]==='phpUnitTestKeysKey1' && $keys[1]==='phpUnitTestKeysKey2' && $keys[2]==='phpUnitTestKeysKey3') );
        $persistance->del('phpUnitTestKeysKey1');
        $persistance->del('phpUnitTestKeysKey2');
        $persistance->del('phpUnitTestKeysKey3');
    }
    
}
