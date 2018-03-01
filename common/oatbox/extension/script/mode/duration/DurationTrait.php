<?php
/**
 * Copyright (c) 2018 Open Assessment Technologies, S.A.
 */

namespace oat\oatbox\extension\script\mode\duration;


trait DurationTrait
{
    /**
     * @var float
     */
    private $startTime;

    /**
     * Starts the timer.
     */
    protected function startTimer()
    {
        return $this->startTime = $this->getTime();
    }

    /**
     * Returns the start time.
     *
     * @return float
     */
    protected function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * Returns the current time.
     *
     * @return float
     */
    protected function getTime()
    {
        return microtime(true);
    }

    /**
     * Returns the current duration.
     *
     * @return float
     */
    protected function getCurrentDuration()
    {
        return $this->getTime() - $this->getStartTime();
    }

    /**
     * Seconds to Duration
     *
     * Format a given number of $seconds into a duration with format [hours]:[minutes]:[seconds].
     *
     * @param $seconds
     * @return string
     */
    protected function secondsToDuration($seconds)
    {
        $seconds = intval($seconds);
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds / 60) % 60);
        $seconds = $seconds % 60;

        return "${hours}h ${minutes}m {$seconds}s";
    }
}
