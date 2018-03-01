<?php
/**
 * Copyright (c) 2018 Open Assessment Technologies, S.A.
 */

namespace oat\oatbox\extension\script\mode;


use oat\oatbox\extension\script\exception\ShowUsageException;

/**
 * Trait LegacyHelpModeTrait
 *
 * @deprecated Please use the HelpModeTrait instead!
 *
 * @package oat\oatbox\extension\script\mode
 */
trait LegacyHelpModeTrait
{
    /**
     * Adds the help option.
     *
     * @return array
     */
    protected function provideOptionsForLegacyHelpMode()
    {
        $options = [];
        if (method_exists($this, 'provideUsage')) {
            $options['help'] = call_user_func(
                [
                    $this,
                    'provideUsage'
                ]
            );
            $options['help']['flag'] = true;
        }

        return $options;
    }

    /**
     * Initializes the legacy help mode.
     *
     * @throws ShowUsageException
     */
    protected function initializeTheLegacyHelpMode()
    {
        if ($this->hasOption('help')) {
            throw new ShowUsageException('');
        }
    }
}
