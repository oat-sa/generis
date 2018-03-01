<?php
/**
 * Copyright (c) 2018 Open Assessment Technologies, S.A.
 */

namespace oat\oatbox\extension\script\mode;


use oat\oatbox\extension\script\exception\ShowUsageException;

trait HelpModeTrait
{
    /**
     * Adds the help option.
     *
     * @return array
     */
    protected function provideOptionsForHelpMode()
    {
        return [
            'help' => [
                'prefix'      => 'h',
                'longPrefix'  => 'help',
                'required'    => false,
                'flag'        => true,
                'description' => 'Shows the usage of the script.'
            ],
        ];
    }

    /**
     * Initializes the help mode.
     *
     * @throws ShowUsageException
     */
    protected function initializeTheHelpMode()
    {
        if ($this->hasOption('help')) {
            throw new ShowUsageException('');
        }
    }
}
