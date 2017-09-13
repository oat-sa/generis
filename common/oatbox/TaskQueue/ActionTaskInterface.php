<?php

namespace oat\oatbox\TaskQueue;

use oat\oatbox\action\Action;

interface ActionTaskInterface
{
    const JSON_ACTION_KEY = '_action_fqcn_';

    public function setAction(Action $task);

    public function getAction();

    public function markAsEnqueued();
    public function isEnqueued();
}