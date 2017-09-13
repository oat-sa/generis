<?php

namespace oat\oatbox\TaskQueue\Action;

use oat\oatbox\action\Action;
use oat\oatbox\TaskQueue\MessageBroker\InMemoryBroker;
use oat\oatbox\TaskQueue\Queue;
use oat\oatbox\TaskQueue\MessageLogManagerInterface;
use oat\oatbox\TaskQueue\Worker;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;


/**
 * Initialize Queue:
 * - create the queue if set
 * - create the result container if set
 *
 * ```
 * $ sudo -u www-data php index.php 'oat\oatbox\TaskQueue\Action\InitializeQueue'
 * ```
 */
class InitializeQueue implements Action, ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    public function __invoke($params)
    {
        try {
            // Create queue
            /** @var Queue $queue */
            $queue = $this->getServiceLocator()->get(Queue::SERVICE_ID);

            if (!$queue->getBroker() instanceof InMemoryBroker) {
                $queue->getBroker()->createQueue();
            }

            // Create result container
            /** @var MessageLogManagerInterface $resultManager */
            $resultManager = $this->getServiceLocator()->get(MessageLogManagerInterface::SERVICE_ID);
            $resultManager->getBroker()->createContainer();

            return \common_report_Report::createSuccess('Initialization successful');
        } catch (\Exception $e) {
            return \common_report_Report::createFailure($e->getMessage());
        }
    }
}

