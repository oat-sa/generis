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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA;
 *
 *
 */

namespace oat\generis\test\unit\common\persistence;

use \common_persistence_PhpFileDriver;
use oat\generis\test\integration\tools\InvokeMethodTrait;
use org\bovigo\vfs\vfsStream;
use oat\generis\test\TestCase;

class PhpFilePersistenceTtlModeTest extends TestCase
{
    /**
     * Adds invokeMethod() method.
     */
    use InvokeMethodTrait;

    /**
     * The used TTL value.
     */
    const TTL = 15;

    private $root;

    public function setUp()
    {
        if (!class_exists('org\bovigo\vfs\vfsStream')) {
            $this->markTestSkipped(
                'filepersistence tests require mikey179/vfsStream'
            );
        }
        $this->root = vfsStream::setup('data');
    }

    public function testConnect()
    {
        $params = array(
            'dir' => vfsStream::url('data'),
            'humanReadable' => true,
            common_persistence_PhpFileDriver::OPTION_TTL => true,
        );
        $driver = new common_persistence_PhpFileDriver();
        $persistence = $driver->connect('test', $params);
        $this->assertInstanceOf('common_persistence_KeyValuePersistence', $persistence);

        return $persistence;
    }

    /**
     * @depends testConnect
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     *
     * @param \common_persistence_KeyValuePersistence $persistence
     *
     * @throws \common_Exception
     */
    public function testSet($persistence)
    {
        $return = $persistence->set('fakeKeyName', 'value');
        $this->assertTrue($this->root->hasChild('fakeKeyName.php'));
        $content = $this->root->getChild('fakeKeyName.php')->getContent();
        $this->assertEquals(
            preg_replace('/\s+/', '', "<?php return array('value' => 'value', 'expiresAt' => null);"),
            preg_replace('/\s+/', '', $content)
        );
        $this->assertTrue($return);
    }

    /**
     * Tests the set with ttl.
     */
    public function testSetWithTtl()
    {
        $timeStamp = 1520259448;
        $ttlRaisedTimestamp = $timeStamp + static::TTL;

        $persistence = $this->getFakedGetTimePersistenceMock($timeStamp);

        $return = $persistence->set('fakeKeyName', 'value', static::TTL);
        $this->assertTrue($this->root->hasChild('fakeKeyName.php'));
        $content = $this->root->getChild('fakeKeyName.php')->getContent();

        $this->assertEquals(
            preg_replace('/\s+/', '', "<?php return array('value' => 'value', 'expiresAt' => $ttlRaisedTimestamp);"),
            preg_replace('/\s+/', '', $content)
        );
        $this->assertTrue($return);
    }

    /**
     * Returns a persistence instance with a php file driver where the getTime method is mocked.
     *
     * @param int $fakeTimeStamp
     *
     * @return mixed
     */
    private function getFakedGetTimePersistenceMock($fakeTimeStamp)
    {
        $driverMock = $this->getMockBuilder(common_persistence_PhpFileDriver::class)
            ->setMethods(['getTime'])
            ->getMock()
        ;
        $driverMock->expects($this->any())
            ->method('getTime')
            ->willReturn($fakeTimeStamp)
        ;

        $params = array(
            'dir' => vfsStream::url('data'),
            'humanReadable' => true,
            common_persistence_PhpFileDriver::OPTION_TTL => true,
        );

        return $driverMock->connect('testWithTtl', $params);
    }

    /**
     * @depends testConnect
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     *
     * @param \common_persistence_KeyValuePersistence $persistence
     *
     * @throws \common_Exception
     */
    public function testGet($persistence)
    {
        // Adding to cache and op cache.
        $this->assertTrue($persistence->set('fakeKeyName', 'value'));
        $this->assertEquals('value', $persistence->getDriver()->get('fakeKeyName'));

        $this->assertTrue($persistence->set('fakeKeyName', 'value'));
        $this->assertEquals('value', $persistence->getDriver()->get('fakeKeyName'));
    }

    /**
     * Test for the get method with ttl.
     */
    public function testGetTtl()
    {
        $fakeCurrentTime = 1520259448;

        $persistence = $this->getFakedGetTimePersistenceMock($fakeCurrentTime);
        $this->assertFalse($persistence->getDriver()->get('fakeKeyName'));

        // Adding to cache and op cache.
        $this->assertTrue($persistence->set('fakeKeyName', 'value', static::TTL));
        $this->assertEquals('value', $persistence->getDriver()->get('fakeKeyName'));

        $this->assertTrue($persistence->set('fakeKeyName', 'value', -10000));
        $this->assertFalse($persistence->getDriver()->get('fakeKeyName'));
    }

    /**
     * @depends testConnect
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     *
     * @param \common_persistence_KeyValuePersistence $persistence
     *
     * @throws \common_Exception
     */
    public function testExists($persistence)
    {
        $persistence->getDriver()->setTtlMode(true);

        // Try to get entry.
        $this->assertFalse($persistence->exists('fakeKeyNameDoesNotExist'));
        $this->assertTrue($persistence->set('fakeKeyName', 'value'));
        $this->assertTrue($persistence->exists('fakeKeyName'));

        // Try to get expired entry.
        $this->assertFalse($persistence->exists('fakeKeyNameExistTest'));
        $this->assertTrue($persistence->set('fakeKeyNameExistTest', 'value', -100000));
        $this->assertFalse($persistence->exists('fakeKeyNameExistTest'));
    }

    /**
     * @depends testConnect
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     *
     * @param \common_persistence_KeyValuePersistence $persistence
     *
     * @throws \common_Exception
     */
    public function testDel($persistence)
    {
        $this->assertTrue($persistence->set('fakeKeyName', 'value'));
        $this->assertTrue($persistence->exists('fakeKeyName'));
        $this->assertTrue($persistence->del('fakeKeyName'));
        $this->assertFalse($persistence->exists('fakeKeyName'));
    }

    /**
     * @depends testConnect
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     *
     * @param \common_persistence_KeyValuePersistence $persistence
     *
     * @throws \common_Exception
     */
    public function testIncr($persistence)
    {
        $key = 'testIncr';
        $this->assertTrue($persistence->set($key, 0));
        $this->assertTrue($persistence->incr($key));
        $this->assertEquals(1, $persistence->get($key));
        $this->assertTrue($persistence->incr($key));
        $this->assertEquals(2, $persistence->get($key));
    }

    /**
     * @depends testConnect
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     *
     * @param \common_persistence_KeyValuePersistence $persistence
     *
     * @throws \common_Exception
     */
    public function testDecr($persistence)
    {
        $key = 'testDecr';

        $this->assertTrue($persistence->set($key, 10));
        $this->assertTrue($persistence->decr($key));
        $this->assertEquals(9, $persistence->get($key));
        $this->assertTrue($persistence->decr($key));
        $this->assertEquals(8, $persistence->get($key));
    }

    /**
     * @depends testConnect
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     *
     * @param \common_persistence_KeyValuePersistence $persistence
     *
     * @throws \common_Exception
     */
    public function testPurge($persistence)
    {
        $this->assertTrue($persistence->set('fakeKeyName', 'value'));
        $this->assertTrue($persistence->set('fakeKeyName2', 'value'));
        $this->assertTrue($persistence->exists('fakeKeyName'));
        $this->assertTrue($persistence->exists('fakeKeyName2'));
        $this->assertTrue($persistence->purge());
        $this->assertFalse($this->root->hasChildren());
    }

    /**
     * @author Lionel Lecaque, lionel@taotesting.com
     *
     * @throws \common_Exception
     */
    public function testNotHumanReadable()
    {
        $vfStream = vfsStream::setup('cache');
        $params = array(
            'dir' => vfsStream::url('cache'),
        );
        $driver = new common_persistence_PhpFileDriver();
        $persistence = $driver->connect('test', $params);

        $persistence->set('fakeKeyName', 'value');
        $this->assertEquals('value', $persistence->get('fakeKeyName'));

    }

    /**
     * Tests the ttl mode.
     *
     * @param \common_persistence_KeyValuePersistence $persistence
     *
     * @depends testConnect
     */
    public function testTtlMode($persistence)
    {
        $this->assertTrue($persistence->getDriver()->isTtlMode());
    }
}
