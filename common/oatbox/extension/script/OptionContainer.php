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

/**
 * Option Container Class
 * 
 * This class implements a container for options provided through CLI scripts.
 */
class OptionContainer
{
    /**
     * @var array
     */
    protected $options;
    
    /**
     * @var array
     */
    protected $data;
    
    /**
     * Constructor
     * 
     * Create a new OptionContainer object.
     * 
     * @param array $options
     * @param array $values
     */
    public function __construct(array $options, array $values) {
        $this->data = self::extract($options, $values);
        $this->options = $options;
    }
    
    /**
     * Has Option
     * 
     * Wheter an option with name $optionName is extracted.
     * 
     * @param string $optionName
     */
    public function has($optionName)
    {
        return isset($this->data[$optionName]);
    }
    
    /**
     * Get Option
     * 
     * Returns the value of option with name $optionName. In case of
     * such a value does not exist, null is returned.
     * 
     * @return mixed
     */
    public function get($optionName)
    {
        return ($this->has($optionName)) ? $this->data[$optionName] : null;
    }
    
    /**
     * Get Options
     * 
     * Get all options.
     * 
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }
    
    /**
     * Is Flag
     * 
     * Wheter an option with name $optionName is a flag.
     */
    public function isFlag($optionName)
    {
        return isset($this->options[$optionName]) && !empty($this->options[$optionName]['flag']);
    }
    
    private static function extract(array $options, array $values)
    {
        $returnValue = [];
        
        foreach ($options as $optionName => $optionParams) {
            
            // Ignore non string-indexed options.
            if (is_string($optionName)) {
                
                $prefix = empty($optionParams['prefix']) ? '' : $optionParams['prefix'];
                $longPrefix = empty($optionParams['longPrefix']) ? '' : $optionParams['longPrefix'];
                
                if (empty($prefix) && empty($longPrefix)) {
                    throw new \LogicException("Argument with name '${optionName}' has no prefix, nor long prefix.");
                }
                
                if (!empty($optionParams['flag'])) {
                    // It's a flag!
                    if (is_int(self::searchOptionIndex($prefix, $longPrefix, $values))) {
                        $returnValue[$optionName] = true;
                    }
                } else {
                    // It's a regular option!
                    $required = empty($optionParams['required']) ? false : true;
                    $castTo = empty($optionParams['cast']) ? null : $optionParams['cast'];
                    $optionIndex = self::searchOptionIndex($prefix, $longPrefix, $values);
                    
                    if ($required && $optionIndex === false) {
                        throw new MissingOptionException("Required argument '${optionName}' is missing.", $optionName);
                    }
                    
                    if ($optionIndex === false && isset($optionParams['defaultValue'])) {
                        $returnValue[$optionName] = self::cast($optionParams['defaultValue'], $castTo);
                    } else {
                        $valueIndex = $optionIndex + 1;
                        if ($optionIndex !== false && isset($values[$valueIndex])) {

                            $returnValue[$optionName] = self::cast($values[$valueIndex], $castTo);
                        } else {
                            // Edge case. Option found, but it is the last value of the $value array.
                            if ($required) {
                                throw new MissingOptionException("No value given for required argument '${optionName}'.", $optionName);
                            } elseif (isset($optionParams['defaultValue'])) {
                                $returnValue[$optionName] = self::cast($optionParams['defaultValue'], $castTo);
                            }
                        }
                    }
                }
            }
        }
        
        return $returnValue;
    }
    
    private static function searchOptionIndex($prefix, $longPrefix, array $values)
    {
        $optionIndex = false;
        $prefixes = [$prefix, $longPrefix];
        
        for ($i = 0; $i < count($prefixes); $i++) {
            $dashes = str_repeat('-', $i + 1);
            $p = $prefixes[$i];
            if (!empty($p)) {
                if (($search = array_search("${dashes}${p}", $values)) !== false) {
                    $optionIndex = $search;
                    break;
                }
            }
        }
        
        return $optionIndex;
    }
    
    private static function cast($value, $to)
    {
        $casted = $value;
        
        if (is_string($to)) {
            switch (strtolower($to)) {
                case 'integer':
                case 'int':
                    $casted = @intval($value);
                    break;
                    
                case 'float':
                    $casted = @floatval($value);
                    break;
                    
                case 'string':
                    $casted = @strval($value);
                    break;
                    
                case 'boolean':
                case 'bool':
                    $casted = @boolval($value);
                    break;
            }
        }
        
        return $casted;
    }
}
