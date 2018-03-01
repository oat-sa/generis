<?php
/**
 * Copyright (c) 2018 Open Assessment Technologies, S.A.
 */

namespace oat\oatbox\extension\script\mode\duration;


trait ShowDurationModeTrait
{
    /**
     * Includes the necessary shared duration functionality.
     */
    use DurationTrait;

    /**
     * @var bool
     */
    private $showDurationMode;

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
            $duration = $this->getCurrentDuration();

            return new \common_report_Report(
                \common_report_Report::TYPE_INFO,
                'Execution time: ' . $this->secondsToDuration($duration)
            );
        }
    }
}
