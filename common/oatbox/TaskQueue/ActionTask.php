<?php

namespace oat\oatbox\TaskQueue;

use oat\oatbox\action\Action;

/**
 * Pre-defined class to store Action instances (commands) in task queue for later execution
 *
 * @author Gyula Szucs <gyula@taotesting.com>
 */
final class ActionTask extends AbstractTask implements ActionTaskInterface
{
    private $action;
    private $enqueued = false;

    /**
     * @return \common_report_Report
     */
    public function __invoke()
    {
        return call_user_func($this->action, $this->getParameter());
    }

    /**
     * @param Action $task
     * @return $this
     */
    public function setAction(Action $task)
    {
        $this->action = $task;

        return $this;
    }

    /**
     * @return Action|string
     */
    public function getAction()
    {
        if (is_null($this->action) && ($actionFQCN = $this->getMetadata(self::JSON_ACTION_KEY))) {
            return $actionFQCN;
        }

        return $this->action;
    }

    /**
     * @return $this
     */
    public function markAsEnqueued()
    {
        $this->enqueued = true;

        return $this;
    }

    /**
     * @return bool
     */
    public function isEnqueued()
    {
        return $this->enqueued;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        $this->setMetadata(self::JSON_ACTION_KEY, get_class($this->action));

        return parent::jsonSerialize();
    }
}