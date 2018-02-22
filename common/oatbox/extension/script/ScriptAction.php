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
     * @var string
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

        try {
            // Collecting possible options.
            $this->optionsDescription = array_merge(
                $this->provideTraitOptions(),
                $this->provideOptions()
            );

            // Build option container.
            $this->options = new OptionContainer(
                $this->optionsDescription,
                $params
            );

            // Initializes the trait options.
            $report = $this->initializeTraitOptions($report);

            // Run the userland script.
            $report->add($this->run());

            // Initializes the trait options.
            $report = $this->finalizeTraitOptions($report);
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
     * @throws \ReflectionException
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
     * @param Report $report
     *
     * @return Report
     *
     * @throws \ReflectionException
     * @throws \common_exception_Error
     */
    private function initializeTraitOptions(Report $report)
    {
        $methods = $this->getClassMethods();
        /** @var \ReflectionMethod $method */
        foreach ($methods as $method) {
            if (strpos($method->getName(), 'initializeThe') === 0) {
                $report = $this->callTraitMethod($method, $report);
            }
        }

        return $report;
    }

    /**
     * Runs the trait option finalization methods.
     *
     * @param Report $report
     *
     * @return Report
     *
     * @throws \ReflectionException
     * @throws \common_exception_Error
     */
    private function finalizeTraitOptions(Report $report)
    {
        $methods = $this->getClassMethods();
        /** @var \ReflectionMethod $method */
        foreach ($methods as $method) {
            if (strpos($method->getName(), 'finalizeThe') === 0) {
                $report = $this->callTraitMethod($method, $report);
            }
        }

        return $report;
    }

    /**
     * Calls the given method.
     *
     * @param \ReflectionMethod $method
     * @param Report            $report
     *
     * @return Report
     *
     * @throws \common_exception_Error
     */
    private function callTraitMethod(\ReflectionMethod $method, Report $report)
    {
        $result = call_user_func(
            [
                $this,
                $method->getName()
            ]
        );

        if ($result instanceof Report) {
            $report->add($result);
        }

        return $report;
    }

    /**
     * Returns the class methods.
     *
     * @return \ReflectionMethod[]
     *
     * @throws \ReflectionException
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

        foreach ($this->optionsDescription as $optionName => $optionParams) {
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
}
