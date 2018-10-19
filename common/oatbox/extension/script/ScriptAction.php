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

/**
 * abstract base for extension scripts.
 *
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 */
abstract class ScriptAction extends AbstractAction
{
    private $options;
    private $optionsDescription;
    
    protected abstract function provideOptions();
    
    protected abstract function provideDescription();
    
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
     * @return \common_report_Report
     */
    public function __invoke($params)
    {
        $this->optionsDescription = $this->provideOptions();
        $beginScript = microtime(true);
        
        // Display help?
        if ($this->displayUsage($params)) {
            return $this->usage();
        }
        
        // Build option container.
        try {
            $this->options = new OptionContainer(
                $this->optionsDescription, 
                $params
            );
        } catch (\Exception $e) {
            return new Report(
                Report::TYPE_ERROR,
                $e->getMessage()
            );
        }

        // Run the userland script.
        $report = $this->run();

        $endScript = microtime(true);
        if ($this->showTime()) {
            $report->add(
                new Report(
                    Report::TYPE_INFO,
                    'Execution time: ' . self::secondsToDuration($endScript - $beginScript)
                )
            );
        }

        return $report;
    }

    protected function hasOption($optionName)
    {
        return $this->options->has($optionName);
    }
    
    protected function getOption($optionName)
    {
        return $this->options->get($optionName);
    }
    
    protected function provideUsage()
    {
        return [];
	}
	
    protected function provideUsageOptionName()
    {
        return 'help';
    }

    protected function showTime()
    {
        return false;
    }
	
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
    
    private function usage()
    {
        $report = new Report(
            Report::TYPE_INFO,
            $this->provideDescription() . "\n"
        );
        
        $optionsDescription = $this->optionsDescription;
        $optionsDescription[$this->provideUsageOptionName()] = $this->provideUsage();
        
        $required = new Report(Report::TYPE_INFO, 'Required Arguments:');
        $optional = new Report(Report::TYPE_INFO, 'Optional Arguments:');
        
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
                $optionMsg .= ' (default: ' . self::valueToString($optionParams['defaultValue']) . ')';
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

        if ($required->hasChildren()) {
            $report->add($required);
        }

        if ($optional->hasChildren()) {
            $report->add($optional);
        }

        // A little bit of formatting...
        if ($required->hasChildren() && $optional->hasChildren()) {
            $required->add(new Report(Report::TYPE_INFO, ""));
        }


        return $report;
    }
    
    private static function valueToString($value)
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
     * Seconds to Duration
     *
     * Format a given number of $seconds into a duration with format [hours]:[minutes]:[seconds].
     *
     * @param $seconds
     * @return string
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
