<?php
/**
 * Copyright (c) 2018 Open Assessment Technologies, S.A.
 */

namespace oat\oatbox\extension\script\mode;


trait DryRunModeTrait
{
    /**
     * @var bool
     */
    private $dryRunMode;

    /**
     * Returns TRUE when the script is running in dry run mode.
     *
     * @return bool
     */
    protected function isDryRunMode()
    {
        return $this->dryRunMode;
    }

    /**
     * Sets the dry run mode.
     *
     * @param bool $isDryRun
     *
     * @return void
     */
    protected function setDryRunMode($isDryRun)
    {
        $this->dryRunMode = $isDryRun;
    }

    /**
     * Provides the possible options.
     *
     * @return array
     */
    protected function provideOptionsForDryWetRunMode()
    {
        return [
            'dryRun' => [
                'longPrefix'  => 'dryRun',
                'required'    => false,
                'flag'        => true,
                'description' => 'Runs the script in dry run mode.'
            ],
            'wetRun' => [
                'longPrefix'  => 'wetRun',
                'required'    => false,
                'flag'        => true,
                'description' => 'Runs the script in wet run mode.'
            ]
        ];
    }

    /**
     * Initializes the dry run mode.
     *
     * @return \common_report_Report
     *
     * @throws \ErrorException
     */
    protected function initializeTheDryWetRunMode()
    {
        if ($this->hasOption('dryRun')) {
            $this->setDryRunMode(true);
        } elseif ($this->hasOption('wetRun')) {
            $this->setDryRunMode(false);
        } else {
            throw new \ErrorException('Define script run mode with --dry-run or --wet-run parameters!');
        }

        return new \common_report_Report(
            \common_report_Report::TYPE_INFO,
            $this->isDryRunMode()
                ? 'Script runs in DRY RUN mode!' . PHP_EOL
                : 'Script runs in WET RUN mode!' . PHP_EOL
        );
    }
}
