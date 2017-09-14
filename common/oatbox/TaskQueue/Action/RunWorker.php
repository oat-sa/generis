<?php

namespace oat\oatbox\TaskQueue\Action;

use oat\oatbox\action\Action;
use oat\oatbox\TaskQueue\MessageBroker\InMemoryBroker;
use oat\oatbox\TaskQueue\MessageLogManager;
use oat\oatbox\TaskQueue\Queue;
use oat\oatbox\TaskQueue\MessageLogManagerInterface;
use oat\oatbox\TaskQueue\QueueInterface;
use oat\oatbox\TaskQueue\Worker;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Start a new worker.
 *
 * ```
 * $ sudo -u www-data php index.php 'oat\oatbox\TaskQueue\Action\RunWorker'
 * $ sudo -u www-data php index.php 'oat\oatbox\TaskQueue\Action\RunWorker' 10
 * ```
 *
 * @author Gyula Szucs <gyula@taotesting.com>
 */
class RunWorker implements Action, ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    public function __invoke($params)
    {
        $limit = isset($params[0]) ? (int) $params[0] : 0;

        /** @var QueueInterface $queue */
        $queue = $this->getServiceLocator()->get(Queue::SERVICE_ID);

        if ($queue->getBroker() instanceof InMemoryBroker) {
            return \common_report_Report::createInfo('No worker needed because Sync Queue is used.');
        }

        /** @var MessageLogManagerInterface $messageLogManager */
        $messageLogManager = $this->getServiceLocator()->get(MessageLogManager::SERVICE_ID);

        (new Worker($queue, $messageLogManager))
            ->setMaxIterations($limit)
            ->processQueue();

        return \common_report_Report::createSuccess('Worker finished');
    }
}

