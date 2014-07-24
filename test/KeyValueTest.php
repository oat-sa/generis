<?php
require_once dirname(__FILE__) . '/GenerisPhpUnitTestRunner.php';
include_once dirname(__FILE__) . '/../../tao/includes/raw_start.php';

class KeyValueTest extends GenerisPhpUnitTestRunner {
    
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
                if ( $persistance['driver'] instanceof common_persistence_KeyValuePersistence ) {
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
    public function testSetStr($persistance, $driverName) {
        $this->assertTrue( $persistance->set('phpUnitTestKey', 'asdf') );
    }
    
    /**
     * @dataProvider persistanceProvider
     */
    public function testSetInt($persistance, $driverName) {
        $this->assertTrue( $persistance->set('phpUnitTestKey', 1) );
    }
    
    /**
     * @dataProvider persistanceProvider
     */
    public function testSetEmptyStr($persistance, $driverName) {
        if ($driverName === 'oat\kvDynamoDb\model\DynamoDbDriver') {
            $this->setExpectedException('Aws\DynamoDb\Exception\ValidationException');
        }
        $this->assertTrue( $persistance->set('phpUnitTestKey', '') );
    }

    /**
     * @dataProvider persistanceProvider
     */
    public function testSetNull($persistance, $driverName) {
        if ($driverName === 'oat\kvDynamoDb\model\DynamoDbDriver') {
            $this->setExpectedException('Aws\DynamoDb\Exception\ValidationException');
        }
        $this->assertTrue( $persistance->set('phpUnitTestKey', NULL) );
    }
    
    /**
     * @dataProvider persistanceProvider
     */
    public function testExists($persistance, $driverName) {
        $this->assertTrue( $persistance->exists('phpUnitTestKey') );
    }
    
    /**
     * @dataProvider persistanceProvider
     */
    public function testGetStr($persistance, $driverName) {
        $persistance->set('phpUnitTestKey', 'asdf');
        $this->assertSame( $persistance->get('phpUnitTestKey'), 'asdf');
    }

    /**
     * @dataProvider persistanceProvider
     */
    public function testGetInt($persistance, $driverName) {
        $persistance->set('phpUnitTestKey', 1);
        $this->assertSame( $persistance->get('phpUnitTestKey'), 1);
    }
    
    /**
     * @dataProvider persistanceProvider
     */
    public function testDel($persistance, $driverName) {
        $this->assertTrue( $persistance->del('phpUnitTestKey') );
    }

    /**
     * @dataProvider persistanceProvider
     */
    public function testGetNonExisting($persistance, $driverName) {
        $this->assertFalse( $persistance->get('phpUnitTestKey'));
    }
    
    /**
     * @dataProvider persistanceProvider
     */
    public function testDelNonExisting($persistance, $driverName) {
        $this->assertTrue( $persistance->del('phpUnitTestKey') );
    }
    
    /**
     * @dataProvider persistanceProvider
     */
    public function testExistsNot($persistance, $driverName) {
        $this->assertFalse( $persistance->exists('phpUnitTestKey') );
    }
    
    /**
     * @dataProvider persistanceProvider
     */
    public function testBinary($persistance, $driverName) {
        $testBinaryData = base64_decode('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');
        $persistance->set('binaryDataTest', $testBinaryData);
        $this->assertTrue( $persistance->get('binaryDataTest') === $testBinaryData );
        $persistance->del('binaryDataTest');
    }
    
}
