<?php

namespace oat\oatbox\TaskQueue;

use oat\oatbox\action\Action;

/**
 * ActionTaskInterface
 *
 * @author Gyula Szucs <gyula@taotesting.com>
 */
interface ActionTaskInterface
{
    const JSON_ACTION_KEY = '_action_fqcn_';

    /**
     * @param Action $task
     */
    public function setAction(Action $task);

    /**
     * @return Action|string
     */
    public function getAction();

    /**
     * Mark the task as successfully enqueued.
     */
    public function markAsEnqueued();

    /**
     * @return bool
     */
    public function isEnqueued();
}