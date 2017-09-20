# Task Queue

> This article describes the functioning of the new Task Queue.

## Install

Before using of the queue system, please run the following command to create the required queue and message log container:
```bash
 $ sudo -u www-data php index.php 'oat\oatbox\TaskQueue\Action\InitializeQueue'
```

## Components

The task queue system is built on three main components.

### Queue component

It is responsible for handling different types of queues and mainly for publishing and receiving messages or tasks.
This is the _**main service**_ to be used for interacting with the queue system.

The communication with the different queues is done through the Message Brokers. There are three type of message brokers currently:
- **InMemoryBroker** which accomplishes the Sync Queue mechanism. Tasks will be executing straightaway after adding them into the queue.
- **DbalBroker** which stores messages/tasks in RDS.
- **SqsBroker** which is for using AWS SQS.

The Message Broker, as its name suggests, handles Messages. 
A **Message** is the basic entity to store information in it and publish it into the queue.
It can hold a simple string in its body and many metadata.


_Note_: 
> You can store Messages in any queue but only Tasks are processed by the worker which means you will need to fetch and process your Message object manually.


A **Task** is extended from Message and it is used to hold **Action** objects to be run later.

### Worker component

Its duty is to get a **Task** from the specified queue and execute it. Multiple workers can be run at the same time.
It has built-in signal handling for the following actions:
 - Shutting down the worker gracefully: SIGTERM/SIGINT/SIGQUIT
 - Pausing task processing: SIGUSR2
 - Resuming task processing: SIGCONT
 
After processing the given task, the worker saves the generated report for the task through the Message Log Manager.

#### Running a worker

To run a worker, use the following command. It will start a worker for running infinitely.

```bash
 $ sudo -u www-data php index.php 'oat\oatbox\TaskQueue\Action\RunWorker'
```

If you want the worker running for a specified time/iteration, use this one:

```bash
 $ sudo -u www-data php index.php 'oat\oatbox\TaskQueue\Action\RunWorker' 10
```

### Message Log Manager component
It is responsible for managing the lifecycle of Messages/Tasks, can be accessed as service. It stores the statuses, the generated report and some other useful metadata. 
Its main important duty is preventing of running the same task by multiple workers at the same time. 

It can also have multiple brokers extending MessageLogManagerInterface to store the data in different type of storage system. 
Currently we have **DbalLogBroker** which uses RDS.

Usually, you won't have to interact with this service directly except if you are using InMemoryBroker and want to get the report of the given task in the same request.

## Registering the services

```php
$newQueue = new \oat\oatbox\TaskQueue\Queue([
    \oat\oatbox\TaskQueue\Queue::OPTION_QUEUE_NAME => 'queue_name',
    \oat\oatbox\TaskQueue\Queue::OPTION_MESSAGE_BROKER => \oat\oatbox\TaskQueue\MessageBroker\DbalBroker::class,
    \oat\oatbox\TaskQueue\Queue::OPTION_MESSAGE_BROKER_CONFIG => [
        \oat\oatbox\TaskQueue\MessageBroker\DbalBroker::CONFIG_PERSISTENCE => 'default',
        \oat\oatbox\TaskQueue\MessageBroker\DbalBroker::CONFIG_MESSAGES_TO_RECEIVE => '5'
    ],
    \oat\oatbox\TaskQueue\Queue::OPTION_MESSAGE_BROKER_CACHE => 'generis/cache'
]);
$this->getServiceManager()->register(Queue::SERVICE_ID, $newQueue);

$messageLogManager =  new \oat\oatbox\TaskQueue\MessageLogManager([
    \oat\oatbox\TaskQueue\MessageLogManager::OPTION_MESSAGE_LOG_BROKER => \oat\oatbox\TaskQueue\MessageLogBroker\DbalLogBroker::class,
    \oat\oatbox\TaskQueue\MessageLogManager::OPTION_MESSAGE_LOG_BROKER_CONFIG => [
        \oat\oatbox\TaskQueue\MessageLogBroker\DbalLogBroker::CONFIG_PERSISTENCE => 'default'
    ]
]);
$this->getServiceManager()->register(\oat\oatbox\TaskQueue\MessageLogManager::SERVICE_ID, $messageLogManager);
```

## Usage examples

- Getting the queue service as usual:

```php
$queue = $this->getServiceManager()->get(\oat\oatbox\TaskQueue\Queue::SERVICE_ID);
```

- Creating a Message object:

```php
$myMessage = new \oat\oatbox\TaskQueue\Message('This is my first test message');
$myMessage->setMetadata('forWhom', 'Developers');

// some useful getters
$id = $myMessage->getId();
$createdAt = $myMessage->getCreatedAt();
$owner = $myMessage->getOwner();
$body = $myMessage->getBody();
$forWhom = $myMessage->getMetadata('forWhom');
```

- Publishing a Message
```php
if ($queue->enqueue($myMessage)) {
    echo 'Successfully published.';
}
```

- Getting the next enqueued message/task from queue
```php
$dequeuedMessage = $queue->dequeue();
```

- Deleting a message from the queue
```php
$queue->acknowledge($dequeuedMessage);
```

### Working with Task

There is two ways to create and publish a task.

- **First option**: creating a task class extending \oat\oatbox\TaskQueue\AbstractTask. It's a new way, use it if you like it and you don't need the possibility to run your task as an Action from CLI.
```php
<?php

use \common_report_Report as Report;
use \oat\oatbox\TaskQueue\AbstractTask;

class MyFirstTask extends AbstractTask
{
    // constants for the param keys
    const PARAM_TEST_URI = 'test_uri';
    const PARAM_DELIVERY_URI = 'delivery_uri';

    /**
     * As usual, the magic happens here.
     * It needs to return a Report object. 
     */
    public function __invoke()
    {
        // you get the parameter using getParameter() with the required key
        if (!$this->getParameter(self::PARAM_TEST_URI) || !$this->getDeliveryUri()) {
            return Report::createFailure('Missing parameters');
        }

        $report = Report::createSuccess();
        $report->setMessage("I worked with Test ". $this->getParameter(self::PARAM_TEST_URI) ." and Delivery ". $this->getDeliveryUri());

        return $report;
    }

    /**
     * You can create a custom setter for your parameter.
     *
     * @param $uri
     */
    public function setDeliveryUri($uri)
    {
        // doing some validation
        // if it's a valid delivery
        $this->setParameter(self::PARAM_DELIVERY_URI, $uri);
    }

    /**
     * You can create a custom getter for your parameter.
     *
     * @return mixed
     */
    public function getDeliveryUri()
    {
        return $this->getParameter(self::PARAM_DELIVERY_URI);
    }
}
```

Then you can initiate your class and setting the required parameters and finally publish it:
```php
$myTask = new MyFirstTask();
$myTask->setParameter(MyFirstTask::PARAM_TEST_URI, 'http://taotesting.com/tao.rdf#i1496838551505670');
$myTask->setDeliveryUri('http://taotesting.com/tao.rdf#i1496838551505110');

if ($queue->enqueue($myTask)) {
    echo "Successfully published";
}
```

- **Second option**: Using Command/Action objects which implement \oat\oatbox\action\Action. This is the usual old way and more preferable because we can run those actions from CLI if needed.

```php
$task = $queue->createTask(new RegeneratePayload(), array($delivery->getUri()));
if ($task->isEnqueued()) {
    echo "Successfully published";
}
```

As you can see, nothing has changed here. It is the same like before. The magic is behind of the createTask() method. Look into it if you dare...

Anyway, the main thing here is that a wrapper class called \oat\oatbox\TaskQueue\ActionTask is used to wrap your Action object and make it consumable for the queue system.

#### Working with Message Log Manager

Mostly, it can be used when the queue is used as Sync Queue and you want to get the status and the report for a task:

```php
/** @var MessageLogManagerInterface $messageLogManager */
$messageLogManager = $this->getServiceManager()->get(\oat\oatbox\TaskQueue\MessageLogManager::SERVICE_ID);

// checking the status for MESSAGE_STATUS_COMPLETED can prevent working with a null report if InMemoryBroker not used anymore.
if ($task->isEnqueued() && $messageLogManager->getStatus($task->getId()) == MessageLogManagerInterface::MESSAGE_STATUS_COMPLETED) {
    $report = $messageLogManager->getReport($task->getId());
}
```

