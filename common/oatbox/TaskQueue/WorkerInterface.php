<?php

namespace oat\oatbox\TaskQueue;

use Psr\Log\LoggerAwareInterface;

interface WorkerInterface extends LoggerAwareInterface
{
    public function __construct(QueueInterface $queue, MessageLogManagerInterface $resultManager, $handleSignals);

    /**
     * Process tasks in the given queue.
     */
    public function processQueue();

    /**
     * Process a job that comes from the given queue
     *
     * @param  TaskInterface $task
     * @return int Status of the job
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