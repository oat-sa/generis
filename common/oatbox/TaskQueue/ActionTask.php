<?php

namespace oat\oatbox\TaskQueue;

use oat\oatbox\action\Action;

/**
 * Pre-defined class to store Action instances (commands) in task queue for later execution
 *
 * @package oat\oatbox\TaskQueue
 */
final class ActionTask extends AbstractTask implements ActionTaskInterface
{
    private $action;
    private $enqueued = false;

    public function __invoke()
    {
        return call_user_func($this->action, $this->getParameter());
    }

    public function setAction(Action $task)
    {
        $this->action = $task;

        return $this;
    }

    public function getAction()
    {
        if (is_null($this->action) && ($actionFQCN = $this->getMetadata(self::JSON_ACTION_KEY))) {
            return $actionFQCN;
        }

        return $this->action;
    }

    public function markAsEnqueued()
    {
        $this->enqueued = true;

        return $this;
    }

    public function isEnqueued()
    {
        return $this->enqueued;
    }

    public function jsonSerialize()
    {
        $this->setMetadata(self::JSON_ACTION_KEY, get_class($this->action));

        return parent::jsonSerialize();
    }
}