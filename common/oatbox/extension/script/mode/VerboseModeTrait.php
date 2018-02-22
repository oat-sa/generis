<?php
/**
 * Copyright (c) 2018 Open Assessment Technologies, S.A.
 */

namespace oat\oatbox\extension\script\mode;


trait VerboseModeTrait
{
    /**
     * @var bool
     */
    private $verboseMode;

    /**
     * Returns TRUE when the script is running in verbose mode.
     *
     * @return bool
     */
    protected function isVerboseMode()
    {
        return $this->verboseMode;
    }

    /**
     * Sets the verbose mode.
     *
     * @param bool $isVerboseMode
     *
     * @return void
     */
    protected function setVerboseMode($isVerboseMode)
    {
        $this->verboseMode = $isVerboseMode;
    }

    /**
     * Provides the possible options.
     *
     * @return array
     */
    protected function provideOptionsForVerboseMode()
    {
        return [
            'verbose' => [
                'prefix'      => 'v',
                'longPrefix'  => 'verbose',
                'required'    => false,
                'flag'        => true,
                'description' => 'Show verbose output.'
            ],
        ];
    }

    /**
     * Initializes the verbose mode.
     */
    protected function initializeTheVerboseMode()
    {
        $this->setVerboseMode($this->hasOption('verbose'));
    }
}
