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

class OptionExtractor
{
    public static function extract(array $options, array $values)
    {
        $returnValue = [];
        
        foreach ($options as $optionName => $optionParams) {
            
            // Ignore non string-indexed options.
            if (is_string($optionName)) {
                
                if (!empty($optionParams['flag'])) {
                    // It's a flag!
                    $returnValue[$optionName] = true;
                } else {
                    // It's a regular option!
                    $prefix = empty($options['prefix']) ? '' : $options['prefix'];
                    $longPrefix = empty($options['longPrefix']) ? '' : $options['longPrefix'];
                    
                    if (empty($prefix) && empty($longPrefix)) {
                        throw new \InvalidArgumentException("Option with name '${optionName}' has no prefix, nor long prefix.");
                    }
                    
                    $required = empty($options['required']) ? false : true;
                    $optionIndex = self::searchOptionIndex($prefix, $longPrefix, $values);
                    
                    if ($required && $optionIndex === false) {
                        throw new MissingOptionException("Required option with name '${optionName}' is missing.");
                    }
                    
                    if ($optionIndex === false && isset($options[$optionName]['defaultValue'])) {
                        $returnValue[$optionName] = $options[$optionName]['defaultValue'];
                    } else {
                        // Option found by prefix or long prefix. Let's get it's value.
                        $valueIndex = $optionIndex + 1;
                        if (isset($values[$valueIndex])) {
                            $castTo = empty($options[$optionName]['cast']) ? null : $options[$optionName]['cast'];
                            $returnValue[$optionName] = self::cast($values[$valueIndex], $to);
                        } else {
                            // Edge case. Option found, but it is the last value of the $value array.
                            if ($required) {
                                throw new MissingOptionException("No value given for option with name '${optionName}'.");
                            } elseif (isset($options[$optionName]['defaultValue']) {
                                $returnValue[$optionName] = $options[$optionName]['defaultValue'];
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
            
            if (($search = array_search("${dashes}${p}", $values)) !== false) {
                $optionIndex = $search;
                break;
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
