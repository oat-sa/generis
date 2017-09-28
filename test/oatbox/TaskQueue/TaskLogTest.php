<?php

namespace oat\generis\test\oatbox\TaskQueue;

use oat\oatbox\task\Task;
use oat\oatbox\TaskQueue\TaskLog;

/**
 * @backupGlobals disabled
 * @backupStaticAttributes disabled
 */
class TaskLogTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TaskLog
     */
    private $taskLog;


    protected function setup()
    {
        $this->taskLog = new TaskLog([
            TaskLog::CONFIG_CONTAINER_NAME => 'example_container',
            TaskLog::CONFIG_PERSISTENCE => 'default'
        ]);
    }

    protected function tearDown()
    {
        $this->taskLog = null;
    }

    public function provideInsertResult()
    {
        return [
            'ForTrue' => [1, true],
            'ForFalse' => [0, false]
        ];
    }

    /**
     * @dataProvider provideInsertResult
     */
    public function testAddNewTask($insertFixture, $expected)
    {
        $nowFixture = '2017-09-22 18:00:15';

        $platformMock = $this->getMockBuilder(\common_persistence_sql_Platform::class)
            ->disableOriginalConstructor()
            ->setMethods(['getNowExpression'])
            ->getMock();

        $platformMock->expects($this->once())
            ->method('getNowExpression')
            ->willReturn($nowFixture);

        $persistenceMock = $this->getMockBuilder(\common_persistence_SqlPersistence::class)
            ->disableOriginalConstructor()
            ->setMethods([
                'getPlatForm', 'insert'
            ])
            ->getMock();

        $persistenceMock->expects($this->once())
            ->method('getPlatForm')
            ->willReturn($platformMock);

        $persistenceMock->expects($this->once())
            ->method('insert')
            ->willReturn($insertFixture);

        $instanceMock = $this->getMockBuilder(TaskLog::class)
            ->disableOriginalConstructor()
            ->setMethods(['getPersistence', 'getTableName'])
            ->getMock();

        $instanceMock->expects($this->exactly(2))
            ->method('getPersistence')
            ->willReturn($persistenceMock);

        $instanceMock->expects($this->once())
            ->method('getTableName')
            ->willReturn('table_example');


        $this->assertEquals($expected, $instanceMock->add($this->getTask()));
    }

    public function testAddNewTaskForException()
    {
        $nowFixture = '2017-09-22 18:00:15';

        $platformMock = $this->getMockBuilder(\common_persistence_sql_Platform::class)
            ->disableOriginalConstructor()
            ->setMethods(['getNowExpression'])
            ->getMock();

        $platformMock->expects($this->once())
            ->method('getNowExpression')
            ->willReturn($nowFixture);

        $persistenceMock = $this->getMockBuilder(\common_persistence_SqlPersistence::class)
            ->disableOriginalConstructor()
            ->setMethods([
                'getPlatForm', 'insert'
            ])
            ->getMock();

        $persistenceMock->expects($this->once())
            ->method('getPlatForm')
            ->willReturn($platformMock);

        $persistenceMock->expects($this->once())
            ->method('insert')
            ->willThrowException(new \RuntimeException());

        $instanceMock = $this->getMockBuilder(TaskLog::class)
            ->disableOriginalConstructor()
            ->setMethods(['getPersistence', 'getTableName'])
            ->getMock();

        $instanceMock->expects($this->exactly(2))
            ->method('getPersistence')
            ->willReturn($persistenceMock);

        $instanceMock->expects($this->once())
            ->method('getTableName')
            ->willReturn('table_example');

        $this->assertFalse($instanceMock->add($this->getTask()));
    }

    private function getTask()
    {
        $taskMock = $this->getMockForAbstractClass(Task::class);

        $taskMock->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn('1111');

        $taskMock->expects($this->exactly(2))
            ->method('getInvocable')
            ->willReturn(\stdClass::class);

        $taskMock->expects($this->once())
            ->method('getStatus')
            ->willReturn(Task::STATUS_CREATED);

        $taskMock->expects($this->once())
            ->method('getOwner')
            ->willReturn('ME');

        $taskMock->expects($this->once())
            ->method('getCreationDate')
            ->willReturn('2017-09-23 18:11:18');

        return $taskMock;
    }
}