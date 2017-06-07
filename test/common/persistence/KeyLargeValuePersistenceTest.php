<?php

namespace oat\generis\test\common\persistence;

class KeyLargeValuePersistenceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \common_persistence_KeyLargeValuePersistence
     */
    protected $largeValuePersistence;

    public function setUp()
    {
        $this->largeValuePersistence = new \common_persistence_KeyLargeValuePersistence(
            array(),
            new \common_persistence_InMemoryKvDriver(),
            100
        );
    }

    public function tearDown()
    {
        unset($this->largeValuePersistence);
    }

    protected function get100000bytesValue()
    {
        return str_repeat('a', 100000);
    }

    public function testSetGetLargeValue()
    {
        $bigValue = $this->get100000bytesValue();
        $this->largeValuePersistence->set('test', $bigValue);
        $this->assertEquals($bigValue, $this->largeValuePersistence->get('test'));
    }

    public function testDelExistsLarge()
    {
        $bigValue = $this->get100000bytesValue();
        $this->assertFalse($this->largeValuePersistence->exists('test'));
        $this->largeValuePersistence->set('test', $bigValue);
        $this->assertTrue($this->largeValuePersistence->exists('test'));
        $this->assertTrue($this->largeValuePersistence->del('test'));
        $this->assertFalse($this->largeValuePersistence->exists('test'));
        $this->assertEmpty($this->largeValuePersistence->get('test'));
    }

    public function testSetGet()
    {
        $this->largeValuePersistence->set('test', 'fixture');
        $this->assertEquals('fixture', $this->largeValuePersistence->get('test'));
    }

    public function testDelExists()
    {
        $this->assertFalse($this->largeValuePersistence->exists('test'));
        $this->largeValuePersistence->set('test', 'fixture');
        $this->assertTrue($this->largeValuePersistence->exists('test'));
        $this->assertTrue($this->largeValuePersistence->del('test'));
        $this->assertFalse($this->largeValuePersistence->exists('test'));
        $this->assertEmpty($this->largeValuePersistence->get('test'));
    }

}