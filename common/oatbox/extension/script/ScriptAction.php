<?php
/**  
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * 
 * Copyright (c) 2017 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 * 
 */

namespace oat\oatbox\extension\script;

use oat\oatbox\extension\AbstractAction;
use common_report_Report as Report;
use oat\oatbox\extension\script\exception\ShowUsageException;

/**
 * abstract base for extension scripts.
 *
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 */
abstract class ScriptAction extends AbstractAction
{
    /**
     * @var array
     */
    private $options;

    /**
     * @var array
     */
    private $optionsDescription;
    
    /**
     * Provides the title of the script.
     *
     * @return string
     */
    protected abstract function provideDescription();

    /**
     * Provides the possible options.
     *
     * @return array
     */
    protected abstract function provideOptions();

    /**
     * Run Script.
     * 
     * Run the userland script. Implementers will use this method
     * to implement the main logic of the script.
     *
     * @return \common_report_Report
     */
    protected abstract function run();
    
    /**
     * Invoke
     * 
     * This method makes the script invokable programatically.
     *
     * @param array $params
     *
     * @throws \common_exception_Error
     *
     * @return \common_report_Report
     */
    public function __invoke($params)
    {
        $report = new Report(
            Report::TYPE_INFO,
            $this->provideDescription() . "\n"
        );

        // Legacy start time.
        $beginScript = microtime(true);

        try {
            $this->optionsDescription = $this->provideOptions();

            // Display help (old deprecated way)?
            if ($this->displayUsage($params)) {
                return $this->usage();
            }

            // Collecting possible options.
            $this->optionsDescription = array_merge(
                $this->provideTraitOptions(),
                $this->optionsDescription
            );

            // Build option container.
            $this->options = new OptionContainer(
                $this->optionsDescription,
                $params
            );

            // Initializes the trait options.
            $this->initializeTraitOptions();

            // Run the userland script.
            $report = $this->run();

            // Initializes the trait options.
            $this->finalizeTraitOptions($report);

            $endScript = microtime(true);

            if ($this->showTime()) {
                $report->add(
                    new Report(
                        Report::TYPE_INFO,
                        'Execution time: ' . self::secondsToDuration($endScript - $beginScript)
                    )
                );
            }
        }
        catch (ShowUsageException $e) {
            return $this->usage();
        }
        catch (\Exception $e) {
            $report->add(
                new Report(
                    Report::TYPE_ERROR,
                    $e->getMessage()
                )
            );
        }

        return $report;
    }

    /**
     * Returns the possible options from traits.
     *
     * @return array
     *
     */
    private function provideTraitOptions()
    {
        $options = [];
        $methods = $this->getClassMethods();
        /** @var \ReflectionMethod $method */
        foreach ($methods as $method) {
            if (strpos($method->getName(), 'provideOptionsFor') === 0) {
                $options = array_merge(
                    $options,
                    call_user_func(
                        [
                            $this,
                            $method->getName()
                        ]
                    )
                );
            }
        }

        return $options;
    }

    /**
     * Runs the trait option initialization methods.
     *
     * @return Report
     *
     * @throws \common_exception_Error
     */
    private function initializeTraitOptions()
    {
        $methods = $this->getClassMethods();
        /** @var \ReflectionMethod $method */
        foreach ($methods as $method) {
            if (strpos($method->getName(), 'initializeThe') === 0) {
                $this->callTraitMethod($method);
            }
        }
    }

    /**
     * Runs the trait option finalization methods.
     *
     * @throws \common_exception_Error
     */
    private function finalizeTraitOptions(Report $report)
    {
        $methods = $this->getClassMethods();
        /** @var \ReflectionMethod $method */
        foreach ($methods as $method) {
            if (strpos($method->getName(), 'finalizeThe') === 0) {
                $this->callTraitMethod($method, $report);
            }
        }
    }

    /**
     * Calls the given method.
     *
     * @param \ReflectionMethod $method
     * @param Report            $report
     *
     * @throws \common_exception_Error
     */
    private function callTraitMethod(\ReflectionMethod $method, Report $report = null)
    {
        $result = call_user_func(
            [
                $this,
                $method->getName()
            ]
        );

        if ($report && $result instanceof Report) {
            $report->add($result);
        }
    }

    /**
     * Returns the class methods.
     *
     * @return \ReflectionMethod[]
     *
     */
    private function getClassMethods()
    {
        $class = new \ReflectionClass($this);

        return $class->getMethods();
    }

    /**
     * Has the requested option set.
     *
     * @param string $optionName
     *
     * @return bool
     */
    protected function hasOption($optionName)
    {
        return $this->options->has($optionName);
    }

    /**
     * Returns the requested option. If it does not exist then null.
     *
     * @param string $optionName
     *
     * @return mixed
     */
    protected function getOption($optionName)
    {
        return $this->options->get($optionName);
    }

    /**
     * Returns the usage as report.
     *
     * @return Report
     *
     * @throws \common_exception_Error
     */
    private function usage()
    {
        $report = new Report(
            Report::TYPE_INFO,
            $this->provideDescription() . "\n"
        );

        $required = new Report(Report::TYPE_INFO, 'Required Arguments:');
        $optional = new Report(Report::TYPE_INFO, 'Optional Arguments:');

        $optionsDescription = $this->optionsDescription;
        $legacyUsageDescription = $this->provideUsage();

        if (!empty($legacyUsageDescription)) {
            $optionsDescription[$this->provideUsageOptionName()] = $legacyUsageDescription;
        }

        foreach ($optionsDescription as $optionName => $optionParams) {
            // Deal with prefixes.
            $prefixes = [];
            $optionDisplay = (!empty($optionParams['flag'])) ? '' : " ${optionName}";

            if (!empty($optionParams['prefix'])) {
                $prefixes[] = '-' . $optionParams['prefix'] . "${optionDisplay}";
            }

            if (!empty($optionParams['longPrefix'])) {
                $prefixes[] = '--' . $optionParams['longPrefix'] . "${optionDisplay}";
            }

            $optionMsg = implode(', ', $prefixes);
            if (isset($optionParams['defaultValue'])) {
                $optionMsg .= ' (default: ' . $this->valueToString($optionParams['defaultValue']) . ')';
            }

            $optionReport = new Report(Report::TYPE_INFO, $optionMsg);

            if (!empty($optionParams['description'])) {
                $optionReport->add(
                    new Report(Report::TYPE_INFO, $optionParams['description'])
                );
            }

            $targetReport = (empty($optionParams['required'])) ? $optional : $required;
            $targetReport->add($optionReport);
        }

        if (count($required) > 0) {
            $report->add($required);
        }

        if (count($optional) > 0) {
            $report->add($optional);
        }

        // A little bit of formatting...
        if (count($required) > 0 && count($optional) > 0) {
            $required->add(new Report(Report::TYPE_INFO, ""));
        }

        return $report;
    }

    private function valueToString($value)
    {
        $string = "\"${value}\"";

        switch (gettype($value)) {

            case 'boolean':
                $string = ($value === true) ? 'true' : 'false';
                break;

            case 'integer':
            case 'double':
                $string = $value;
        }

        return $string;
    }

    /**
     * @param array $params
     * @return bool
     * @deprecated
     */
    private function displayUsage(array $params)
    {
        $usageDescription = $this->provideUsage();

        if (!empty($usageDescription) && is_array($usageDescription)) {
            if (!empty($usageDescription['prefix']) && in_array('-' . $usageDescription['prefix'], $params)) {
                return true;
            } elseif (!empty($usageDescription['longPrefix']) && in_array('--' . $usageDescription['longPrefix'], $params)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array
     * @deprecated
     */
    protected function provideUsage()
    {
        return [];
    }

    /**
     * @return string
     * @deprecated
     */
    protected function provideUsageOptionName()
    {
        return 'help';
    }

    /**
     * @return bool
     * @deprecated
     */
    protected function showTime()
    {
        return false;
    }

    /**
     * Seconds to Duration
     *
     * Format a given number of $seconds into a duration with format [hours]:[minutes]:[seconds].
     *
     * @param $seconds
     * @return string
     * @deprecated
     */
    private static function secondsToDuration($seconds)
    {
        $seconds = intval($seconds);
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds / 60) % 60);
        $seconds = $seconds % 60;

        return "${hours}h ${minutes}m {$seconds}s";
    }
}
