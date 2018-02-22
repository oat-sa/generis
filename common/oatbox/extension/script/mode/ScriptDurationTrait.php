<?php
/**
 * Copyright (c) 2018 Open Assessment Technologies, S.A.
 */

namespace oat\oatbox\extension\script\mode;


trait ScriptDurationTrait
{
    /**
     * @var bool
     */
    private $showDurationMode;

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
     * Returns TRUE when the script is running in show duration mode.
     *
     * @return bool
     */
    protected function showDurationMode()
    {
        return $this->showDurationMode;
    }

    /**
     * Sets the show duration mode.
     *
     * @param bool $showDuration
     *
     * @return void
     */
    protected function setShowDurationMode($showDuration)
    {
        $this->showDurationMode = $showDuration;
    }

    /**
     * Provides the possible options.
     *
     * @return array
     */
    protected function provideOptionsForShowDurationMode()
    {
        return [
            'showDuration' => [
                'longPrefix'  => 'show-duration',
                'required'    => false,
                'flag'        => true,
                'description' => 'Shows the script running duration.'
            ],
        ];
    }

    /**
     * Initializes the show duration mode.
     */
    protected function initializeTheShowDurationMode()
    {
        $this->setShowDurationMode($this->hasOption('showDuration'));
        $this->startTimer();
    }

    /**
     * Finalizes the show duration mode.
     *
     * @return \common_report_Report
     */
    protected function finalizeTheShowDurationMode()
    {
        if ($this->showDurationMode()) {
            $duration = $this->getTime() - $this->getStartTime();

            return new \common_report_Report(
                \common_report_Report::TYPE_INFO,
                'Execution time: ' . $this->secondsToDuration($duration)
            );
        }
    }

    /**
     * Seconds to Duration
     *
     * Format a given number of $seconds into a duration with format [hours]:[minutes]:[seconds].
     *
     * @param $seconds
     * @return string
     */
    private function secondsToDuration($seconds)
    {
        $seconds = intval($seconds);
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds / 60) % 60);
        $seconds = $seconds % 60;

        return "${hours}h ${minutes}m {$seconds}s";
    }
}
