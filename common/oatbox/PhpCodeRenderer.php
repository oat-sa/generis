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
 * Copyright (c) 2017 (original work) Open Assessment Technologies SA;
 *
 *
 */

namespace oat\oatbox;

class PhpCodeRenderer {
    /**
     * The indentation string.
     */
    const INDENT_STRING = '    ';

    /**
     * Renders an object.
     *
     * @param string $className
     * @param array  $arguments
     * @param int    $indentNumber   The current indentation number (optional, default: 1)
     *
     * @return string
     *
     * @throws \common_exception_Error
     */
    public static function renderObject($className, array $arguments, $indentNumber = 1)
    {
        $output = static::getIndent($indentNumber === 1 ? 1 : 0) . 'new ' . $className . '(';
        $output .= static::renderArray(
            $arguments,
            $indentNumber,
            true
        );
        $output .= static::getIndent($indentNumber) . ')';

        return $output;
    }

    /**
     * Returns an array's php code version or an argument list php code version.
     *
     * @param array $parameters
     * @param int   $indentNumber   The current indentation number
     * @param bool  $isArgumentList   TRUE if we want to generate an argument list.
     *
     * @return string
     *
     * @throws \common_exception_Error
     */
    public static function renderArray(array $parameters, $indentNumber, $isArgumentList = false)
    {
        $output = static::getIndent($indentNumber);
        if ($isArgumentList === false) {
            $output = '[' . $output;
        }
        foreach ($parameters as $key => $current) {
            $output .= PHP_EOL . static::getIndent($indentNumber + 1);
            if ($isArgumentList === false) {
                $output .= static::renderElement($key, $indentNumber + 1);
                $output .= ' => ';
            }
            $output .= static::renderElement($current, $indentNumber + 1) . ',';
        }

        $output = rtrim($output, ',') . PHP_EOL;
        if ($isArgumentList === false) {
            $output .= static::getIndent($indentNumber) . ']';
        }

        return $output;
    }

    /**
     * Returns an element's php code version.
     *
     * @param mixed $element
     * @param int   $indentNumber   The current indentation number
     *
     * @return string
     *
     * @throws \common_exception_Error
     */
    protected static function renderElement($element, $indentNumber)
    {
        switch (gettype($element)) {
            case 'string' :
                return '\'' . str_replace('\'', '\\\'', str_replace('\\', '\\\\', $element)) . '\'';

            case 'boolean' :
                return $element ? 'true' : 'false';

            case 'null' :
                return 'null';

            case 'integer' :
            case 'double' :
                return $element;

            case 'array' :
                return static::renderArray($element, $indentNumber);

            case 'object' :
                if ($element instanceof PhpSerializable) {
                    return $element->__toPhpCode($indentNumber);
                } else {
                    return 'unserialize(' . static::renderElement(serialize($element), $indentNumber) . ')';
                }

            default:
                // resource and unexpected types
                throw new \common_exception_Error('Could not convert variable of type ' . gettype($element) . ' to PHP variable string');
        }
    }

    /**
     * Returns the requested indentation string.
     *
     * @param int   $indentNumber   The current indentation number (optional, default: 1)
     *
     * @return string
     */
    private static function getIndent($indentNumber = 1)
    {
        return str_repeat(static::INDENT_STRING, $indentNumber);
    }
}
