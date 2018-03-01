<?php
/**
 * Copyright (c) 2018 Open Assessment Technologies, S.A.
 */

namespace oat\oatbox\extension\script\mode\duration;


trait HideDurationModeTrait
{
    /**
     * Includes the necessary shared duration functionality.
     */
    use DurationTrait;

    /**
     * @var bool
     */
    private $hideDurationMode;

    /**
     * Returns TRUE when the script is running in hide duration mode.
     *
     * @return bool
     */
    protected function hideDurationMode()
    {
        return $this->hideDurationMode;
    }

    /**
     * Sets the hide duration mode.
     *
     * @param bool $hideDuration
     *
     * @return void
     */
    protected function setHideDurationMode($hideDuration)
    {
        $this->hideDurationMode = $hideDuration;
    }

    /**
     * Provides the possible options.
     *
     * @return array
     */
    protected function provideOptionsForHideDurationMode()
    {
        return [
            'showDuration' => [
                'longPrefix'  => 'hideDuration',
                'required'    => false,
                'flag'        => true,
                'description' => 'Hides the script running duration.'
            ],
        ];
    }

    /**
     * Initializes the hide duration mode.
     */
    protected function initializeTheHideDurationMode()
    {
        $this->setHideDurationMode($this->hasOption('hideDuration'));
        $this->startTimer();
    }

    /**
     * Finalizes the show duration mode.
     *
     * @return \common_report_Report
     */
    protected function finalizeTheHideDurationMode()
    {
        if (!$this->hideDurationMode()) {
            $duration = $this->getCurrentDuration();

            return new \common_report_Report(
                \common_report_Report::TYPE_INFO,
                'Execution time: ' . $this->secondsToDuration($duration)
            );
        }
    }
}
