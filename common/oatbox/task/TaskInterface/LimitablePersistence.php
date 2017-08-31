<?php

namespace oat\oatbox\task\TaskInterface;


interface LimitablePersistence
{
    public function setReturnTaskLimit($limit);

    public function getReturnTaskLimit();
}