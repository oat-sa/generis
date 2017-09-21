<?php

namespace oat\oatbox\TaskQueue;

use Psr\Log\LoggerAwareInterface;

/**
 * Interface WorkerInterface
 *
 * @author Gyula Szucs <gyula@taotesting.com>
 */
interface WorkerInterface extends LoggerAwareInterface
{
    /**
     * @param QueueInterface             $queue
     * @param MessageLogManagerInterface $resultManager
     * @param bool $handleSignals
     */
    public function __construct(QueueInterface $queue, MessageLogManagerInterface $resultManager, $handleSignals);

    /**
     * Start processing tasks from the given queue.
     */
    public function processQueue();

    /**
     * Process a job that comes from the given queue
     *
     * @param  TaskInterface $task
     * @return string Status of the job
     */
    public function processTask(TaskInterface $task);

    /**
     * Set the maximum iterations for the worker. If nothing is set, the worker runs infinitely.
     *
     * @param int $maxIterations
     * @return mixed
     */
    public function setMaxIterations($maxIterations);
}