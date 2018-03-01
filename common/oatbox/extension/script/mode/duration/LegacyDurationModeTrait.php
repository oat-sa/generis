<?php
/**
 * Copyright (c) 2018 Open Assessment Technologies, S.A.
 */

namespace oat\oatbox\extension\script\mode\duration;

/**
 * Trait LegacyDurationModeTrait
 *
 * @deprecated Please use the Hide/ShowDurationModeTrait instead!
 *
 * @package oat\oatbox\extension\script\mode\duration
 */
trait LegacyDurationModeTrait
{
    /**
     * Includes the necessary shared duration functionality.
     */
    use DurationTrait;

    /**
     * @var bool
     */
    private $showDuration;

    /**
     * Returns TRUE when the script needs to show the duration.
     *
     * @return bool
     */
    protected function showDuration()
    {
        return $this->showDuration;
    }

    /**
     * Sets the show duration flag.
     *
     * @param bool $showDuration
     *
     * @return void
     */
    protected function setShowDuration($showDuration)
    {
        $this->showDuration = $showDuration;
    }

    /**
     * Initializes the legacy duration mode.
     */
    protected function initializeTheLegacyDurationMode()
    {
        $showTime = false;
        if (method_exists($this, 'showTime')) {
            $showTime = call_user_func(
                [
                    $this,
                    'showTime'
                ]
            );
        }
        $this->setShowDuration($showTime);
        $this->startTimer();
    }

    /**
     * Finalizes the show duration mode.
     *
     * @return \common_report_Report
     */
    protected function finalizeTheLegacyDurationMode()
    {
        if ($this->showDuration()) {
            $duration = $this->getCurrentDuration();

            return new \common_report_Report(
                \common_report_Report::TYPE_INFO,
                'Execution time: ' . $this->secondsToDuration($duration)
            );
        }
    }
}
