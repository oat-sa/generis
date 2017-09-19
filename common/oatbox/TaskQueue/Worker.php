<?php

namespace oat\oatbox\TaskQueue;

use oat\oatbox\log\LoggerAwareTrait;
use common_report_Report as Report;

/**
 * Processes tasks from the queue.
 *
 * @author Gyula Szucs <gyula@taotesting.com>
 */
final class Worker implements WorkerInterface
{
    use LoggerAwareTrait;

    private $queue;
    private $maxIterations = 0; //0 means infinite iteration
    private $iterations;
    private $shutdown;
    private $paused;
    private $waitInterval = 1; //sec
    private $processId;
    private $logContext;
    private $resultManager;

    /**
     * @param QueueInterface             $queue
     * @param MessageLogManagerInterface $resultManager
     * @param bool                       $handleSignals
     */
    public function __construct(QueueInterface $queue, MessageLogManagerInterface $resultManager, $handleSignals = true)
    {
        $this->queue = $queue;
        $this->resultManager = $resultManager;
        $this->processId = getmypid();

        $this->logContext = [
            'QueueName' => $this->queue->getName(),
            'PID'       => $this->processId
        ];

        if ($handleSignals) {
            $this->registerSigHandlers();
        }
    }

    /**
     * Start processing tasks from the queue.
     */
    public function processQueue()
    {
        $this->logInfo('Starting worker.', $this->logContext);

        while ($this->isRunning()) {

            if($this->paused) {
                $this->logDebug('Paused... ', array_merge($this->logContext, [
                    'Iteration' => $this->iterations
                ]));
                usleep($this->waitInterval * 1000000);
                continue;
            }

            ++$this->iterations;

            $this->logContext = array_merge($this->logContext, [
                'Iteration' => $this->iterations
            ]);

            try{
                $this->logDebug('Fetching tasks from queue ', $this->logContext);

                $task = $this->queue->dequeue();

                // if no task to process, sleep for the specified time and continue.
                if (!$task) {
                    $this->logDebug('No task to work on. Sleeping for '. $this->waitInterval .' sec', $this->logContext);
                    usleep($this->waitInterval * 1000000);
                    continue;
                }

                if (!$task instanceof TaskInterface) {
                    $this->logDebug('The received queue item ('. $task .') not processable.', $this->logContext);
                    continue;
                }

                $this->processTask($task);

                unset($task);
            } catch (\Exception $e) {
                $this->logError('Fetching data from queue failed with MSG: '. $e->getMessage(), $this->logContext);
                continue;
            }
        }

        $this->logInfo('Worker finished.', $this->logContext);
    }

    /**
     * Process a task.
     *
     * @param TaskInterface $task
     * @return string
     */
    public function processTask(TaskInterface $task)
    {
        $report = Report::createInfo(__('Running task %s', $task->getId()));

        try {
            $this->logDebug('Processing task '. $task->getId(), $this->logContext);

            $rowsTouched = $this->resultManager->saveRunningStatus($task->getId());

            // if the task is being executed by another worker, just return, no report needs to be saved
            if (!$rowsTouched) {
                $this->logDebug('Task '. $task->getId() .' seems to be running by another worker.', $this->logContext);
                return '';
            }

            // execute the task
            $taskReport = $task();

            if (!$taskReport instanceof Report) {
                $this->logWarning('Task should return a report object.', $this->logContext);
                $taskReport = Report::createInfo(__('Task not returned any report.'));
            }

            $report->add($taskReport);
            unset($taskReport, $rowsTouched);
        } catch (\Exception $e) {
            $this->logError('Executing task '. $task->getId() .' failed with MSG: '. $e->getMessage(), $this->logContext);
            $report = Report::createFailure(__('Executing task %s failed', $task->getId()));
        }

        $status = $report->getType() == Report::TYPE_ERROR || $report->containsError()
            ? MessageLogManagerInterface::MESSAGE_STATUS_FAILED
            : MessageLogManagerInterface::MESSAGE_STATUS_COMPLETED;

        $this->resultManager->setReport($task->getId(), $report, $status);

        unset($report);

        // delete message from queue
        $this->queue->acknowledge($task);

        return $status;
    }

    /**
     * @param int $maxIterations
     * @return $this
     */
    public function setMaxIterations($maxIterations)
    {
        $this->maxIterations = (int) $maxIterations * $this->queue->getBroker()->getMessagesToReceive();

        return $this;
    }

    /**
     * @return bool
     */
    private function isRunning()
    {
        if ($this->shutdown) {
            return false;
        }

        if ($this->maxIterations > 0) {
            return $this->iterations < $this->maxIterations;
        }

        return true;
    }

    /**
     * Register signal handlers that a worker should respond to.
     *
     * TERM/INT/QUIT: Shutdown after the current job is finished then exit.
     * USR2: Pause worker, no new jobs will be processed but the current one will be finished.
     * CONT: Resume worker.
     */
    private function registerSigHandlers()
    {
        if (!function_exists('pcntl_signal')) {
            $this->logError('Please make sure that "pcntl" is enabled.', $this->logContext);
            throw new \RuntimeException('Please make sure that "pcntl" is enabled.');
        }

        declare(ticks = 1);

        pcntl_signal(SIGTERM, array($this, 'shutdown'));
        pcntl_signal(SIGINT, array($this, 'shutdown'));
        pcntl_signal(SIGQUIT, array($this, 'shutdown'));
        pcntl_signal(SIGUSR2, array($this, 'pauseProcessing'));
        pcntl_signal(SIGCONT, array($this, 'unPauseProcessing'));

        $this->logInfo('Finished setting up signal handlers', $this->logContext);
    }

    public function shutdown()
    {
        $this->logInfo('TERM/INT/QUIT received; shutting down gracefully...', $this->logContext);
        $this->shutdown = true;
    }

    public function pauseProcessing()
    {
        $this->logInfo('USR2 received; pausing task processing...', $this->logContext);
        $this->paused = true;
    }

    public function unPauseProcessing()
    {
        $this->logInfo('CONT received; resuming task processing...', $this->logContext);
        $this->paused = false;
    }
}