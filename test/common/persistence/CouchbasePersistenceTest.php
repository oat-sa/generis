<?php
/**
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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 * @author Konstantin Sasim <sasim@1pt.com>
 * @license GPLv2
 * @package generis
 *
 */

namespace oat\generis\test\common\persistence;

use oat\generis\test\common\persistence\stubs\FakeCouchbaseCluster;
use oat\generis\test\GenerisPhpUnitTestRunner;

/**
 * Class CouchbasePersistenceTest
 *
 * TestCase for {@link common_persistence_CouchbaseDriver} class
 *
 * @package oat\generis\test
 */
class CouchbasePersistenceTest extends GenerisPhpUnitTestRunner{

    const PERSISTENCE_NAME = 'couchbase_test';
    const CANARY_ID    = 'test_id';
    const CANARY_VALUE = 'value';

    /** @var  \PHPUnit_Framework_MockObject_MockObject */
    protected $driverMock;
    /** @var  FakeCouchbaseCluster */
    private $fakeCluster;
    /** @var int */
    private $clusterMode = FakeCouchbaseCluster::CLUSTER_MODE_NORMAL;

    /**
     * Provides default and valid Couchbase driver config
     *
     * @return array
     */
    protected function _getCouchbaseConfig()
    {
        return array(
            'driver'   => 'couchbase',
            'cluster'  => 'couchbase://localhost',
            'bucket'   => 'tao_test_bucket',
            'password' => '123456',
        );
    }

    /**
     * Stubs CouchbaseCluster class, emulates misconfiguration behaviour and applies cluster mode
     *
     * @return FakeCouchbaseCluster
     * @throws \CouchbaseException
     */
    public function createClusterCallback()
    {
        $args = func_get_args();
        $config = $this->_getCouchbaseConfig();

        // invalid cluster emulation
        if( $config['cluster'] != $args[0] ){
            throw new \CouchbaseException("Invalid cluster");
        }

        $this->fakeCluster = new FakeCouchbaseCluster($args[0], $this->clusterMode );
        $this->fakeCluster->__setValidBucket($config['bucket']);
        $this->fakeCluster->__setValidPassword($config['password']);

        return $this->fakeCluster;
    }

    /**
     * Creates Couchbase persistence driver mock object
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getDriverMock()
    {
        $mock = $this->getMockBuilder('common_persistence_CouchbaseDriver')
            ->setMethods( array('_getCluster') )
            ->getMock();

        $mock->method('_getCluster')->willReturnCallback( array($this, 'createClusterCallback') );

        return $mock;
    }

    /**
     * Positive test for {@link common_persistence_CouchbaseDriver::connect}
     *
     * @return \common_persistence_Persistence
     */
    public function testConnectPositive()
    {
        $params = $this->_getCouchbaseConfig();
        $driver  = $this->getDriverMock();

        $persistence = $driver->connect(self::PERSISTENCE_NAME, $params);
        $this->assertInstanceOf('common_persistence_KeyValuePersistence', $persistence);

        return $persistence;
    }

    /**
     * Negative test for {@link common_persistence_CouchbaseDriver::connect}: invalid cluster
     *
     * @expectedException \common_exception_PersistenceError
     * @expectedExceptionMessage Invalid cluster
     */
    public function testConnectInvalidCluster()
    {
        $params = $this->_getCouchbaseConfig();
        $params['cluster'] = 'couchbase://invalid_cluster';

        $driver  = $this->getDriverMock();

        $driver->connect(self::PERSISTENCE_NAME, $params);
    }

    /**
     * Negative test for {@link common_persistence_CouchbaseDriver::connect}: empty cluster
     *
     * @expectedException \common_exception_PersistenceError
     * @expectedExceptionMessage cluster not set
     */
    public function testConnectEmptyCluster()
    {
        $params = $this->_getCouchbaseConfig();
        unset( $params['cluster'] );

        $driver  = $this->getDriverMock();

        $driver->connect(self::PERSISTENCE_NAME, $params);
    }

    /**
     * Negative test for {@link common_persistence_CouchbaseDriver::connect}: broken cluster
     *
     * @expectedException \common_exception_PersistenceError
     * @expectedExceptionMessage General cluster error
     */
    public function testConnectBrokenCluster()
    {
        $params = $this->_getCouchbaseConfig();
        $this->clusterMode = FakeCouchbaseCluster::CLUSTER_MODE_BROKEN;
        $driver  = $this->getDriverMock();

        $driver->connect(self::PERSISTENCE_NAME, $params);
    }

    /**
     * Negative test for {@link common_persistence_CouchbaseDriver::connect}: invalid bucket
     *
     * @expectedException \common_exception_PersistenceError
     * @expectedExceptionMessage Bucket not found
     */
    public function testConnectInvalidBucket()
    {
        $params = $this->_getCouchbaseConfig();
        $params['bucket'] = 'invalid_bucket';

        $driver  = $this->getDriverMock();

        $driver->connect(self::PERSISTENCE_NAME, $params);
    }

    /**
     * Negative test for {@link common_persistence_CouchbaseDriver::connect}: invalid bucket password
     *
     * @expectedException \common_exception_PersistenceError
     * @expectedExceptionMessage Bucket auth error
     */
    public function testConnectInvalidBucketPassword()
    {
        $params = $this->_getCouchbaseConfig();
        $params['password'] = 'invalid bucket password';

        $driver  = $this->getDriverMock();

        $driver->connect(self::PERSISTENCE_NAME, $params);
    }

    /**
     * Negative test for {@link common_persistence_CouchbaseDriver::connect}: empty bucket
     *
     * @expectedException \common_exception_PersistenceError
     * @expectedExceptionMessage bucket not set
     */
    public function testConnectEmptyBucket()
    {
        $params = $this->_getCouchbaseConfig();
        unset( $params['bucket'] );

        $driver  = $this->getDriverMock();

        $driver->connect(self::PERSISTENCE_NAME, $params);
    }

    /**
     * Negative test for {@link common_persistence_CouchbaseDriver::connect}: broken bucket
     *
     * @expectedException \common_exception_PersistenceError
     * @expectedExceptionMessage Unable to get bucket due to general error
     */
    public function testConnectBrokenBucket()
    {
        $params = $this->_getCouchbaseConfig();
        $this->clusterMode = FakeCouchbaseCluster::CLUSTER_MODE_BROKEN_BUCKET;
        $driver  = $this->getDriverMock();

        $driver->connect(self::PERSISTENCE_NAME, $params);
    }

    /**
     * Valid persistence object provider for tests in connected state
     *
     * @param int $mode
     * @return \common_persistence_KeyValuePersistence
     */
    protected function _getPersistence($mode = FakeCouchbaseCluster::CLUSTER_MODE_NORMAL)
    {
        $params = $this->_getCouchbaseConfig();
        $this->clusterMode = $mode;
        $driver = $this->getDriverMock();
        return $driver->connect(self::PERSISTENCE_NAME, $params);
    }

    /**
     * Data provider for:
     * - {@link testSetPositive}
     * - {@link testGetPositive}
     *
     * @return array
     */
    public function insertedDataProvider()
    {
        $testObj = new \stdClass();
        $testObj->foo = 'bar';
        $testObj->array_val = array('foo' => 'bar');

        return array(
            'string' => array(self::CANARY_VALUE),
            'int'    => array(124),
            'float'  => array(12.4),
            'bool'   => array(true),
            'array'  => array(array('foo' => 'bar')),
            'object' => array($testObj),
            'null'   => array(null)
        );
    }

    /**
     * Positive test for {@link common_persistence_CouchbaseDriver::set}
     *
     * @param mixed $value
     * @dataProvider insertedDataProvider
     */
    public function testSetPositive($value)
    {
        $persistence = $this->_getPersistence();

        //insert
        $this->assertTrue($persistence->set(self::CANARY_ID, $value) );
        $this->assertTrue($this->fakeCluster->__bucketDocumentExists(self::CANARY_ID));

        $document = $this->fakeCluster->__getBucketDocument(self::CANARY_ID);
        $this->assertEquals($value, unserialize($document->value));

        //update
        $value = 'modified_value';
        $this->assertTrue($persistence->set(self::CANARY_ID, $value) );

        $document = $this->fakeCluster->__getBucketDocument(self::CANARY_ID);
        $this->assertEquals($value, unserialize($document->value));
    }

    /**
     * @expectedException \common_exception_NotImplemented
     * @expectedExceptionMessage TTL not implemented
     */
    public function testSetWithTtlNegative()
    {
        $persistence = $this->_getPersistence();

        $persistence->set(self::CANARY_ID, self::CANARY_VALUE, 1000);
    }

    /**
     * Positive test for {@link common_persistence_CouchbaseDriver::get}
     *
     * @param mixed $value
     * @dataProvider insertedDataProvider
     */
    public function testGetPositive($value)
    {
        $persistence = $this->_getPersistence();
        $this->fakeCluster->__createBucketDocument(self::CANARY_ID, serialize($value));

        $this->assertEquals($persistence->get(self::CANARY_ID), $value);
    }

    /**
     * Negative test for {@link common_persistence_CouchbaseDriver::get}
     */
    public function testGetNegaive()
    {
        $persistence = $this->_getPersistence();

        $this->assertFalse($persistence->get(self::CANARY_ID));
    }

    /**
     * Positive test for {@link common_persistence_CouchbaseDriver::exists}
     */
    public function testExistsPositive()
    {
        $persistence = $this->_getPersistence();
        $this->fakeCluster->__createBucketDocument(self::CANARY_ID, serialize(self::CANARY_VALUE));

        $this->assertTrue($persistence->exists(self::CANARY_ID));
    }

    /**
     * Negative test for {@link common_persistence_CouchbaseDriver::exists}
     */
    public function testExistsNegative()
    {
        $persistence = $this->_getPersistence();

        $this->assertFalse($persistence->exists(self::CANARY_ID));
    }

    /**
     * Positive test for {@link common_persistence_CouchbaseDriver::del}
     */
    public function testDelPositive()
    {
        $persistence = $this->_getPersistence();
        $this->fakeCluster->__createBucketDocument(self::CANARY_ID, serialize(self::CANARY_VALUE));

        $this->assertTrue($persistence->del(self::CANARY_ID));
    }

    /**
     * Negative test for {@link common_persistence_CouchbaseDriver::del}
     */
    public function testDelNegative()
    {
        $persistence = $this->_getPersistence();

        $this->assertFalse($persistence->del(self::CANARY_ID));
    }

    /**
     * Buggy bucket test for {@link common_persistence_CouchbaseDriver::set}
     *
     * @expectedException \common_exception_PersistenceError
     * @expectedExceptionMessage General bucket error
     */
    public function testSetBuggy()
    {
        $persistence = $this->_getPersistence(FakeCouchbaseCluster::CLUSTER_MODE_BUGGY_BUCKET);
        $persistence->set(self::CANARY_ID, self::CANARY_VALUE);
    }

    /**
     * Buggy bucket test for {@link common_persistence_CouchbaseDriver::get}
     *
     * @expectedException \common_exception_PersistenceError
     * @expectedExceptionMessage General bucket error
     */
    public function testGetBuggy()
    {
        $persistence = $this->_getPersistence(FakeCouchbaseCluster::CLUSTER_MODE_BUGGY_BUCKET);
        $this->fakeCluster->__createBucketDocument(self::CANARY_ID, serialize(self::CANARY_VALUE));

        $persistence->get(self::CANARY_ID);
    }

    /**
     * Buggy bucket test for {@link common_persistence_CouchbaseDriver::exists}
     *
     * @expectedException \common_exception_PersistenceError
     * @expectedExceptionMessage General bucket error
     */
    public function testExistsBuggy()
    {
        $persistence = $this->_getPersistence(FakeCouchbaseCluster::CLUSTER_MODE_BUGGY_BUCKET);
        $this->fakeCluster->__createBucketDocument(self::CANARY_ID, serialize(self::CANARY_VALUE));

        $persistence->exists(self::CANARY_ID);
    }

    /**
     * Buggy bucket test for {@link common_persistence_CouchbaseDriver::del}
     *
     * @expectedException \common_exception_PersistenceError
     * @expectedExceptionMessage General bucket error
     */
    public function testDelBuggy()
    {
        $persistence = $this->_getPersistence(FakeCouchbaseCluster::CLUSTER_MODE_BUGGY_BUCKET);
        $this->fakeCluster->__createBucketDocument(self::CANARY_ID, serialize(self::CANARY_VALUE));

        $persistence->del(self::CANARY_ID);
    }
}