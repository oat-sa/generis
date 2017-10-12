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

/**
 * Trait to serialize dependencies.
 *
 * @access public
 * @package generis
 */
trait PhpSerializeTrait
{
    /**
     * This function should generate the php code to recreate
     * the current instance.
     *
     * @param int $indentNumber   The current indentation number (optional, default: 1)
     *
     * @return string
     *
     * @throws \common_exception_Error
     */
    public function __toPhpCode($indentNumber = 1)
    {
        return PhpCodeRenderer::renderObject(
            __CLASS__,
            $this->getAllConstructorPropertyValues(),
            $indentNumber
        );
    }

    /**
     * Returns all of the constructor property values.
     *
     * @return array
     */
    protected function getAllConstructorPropertyValues()
    {
        $reflectionMethod = new \ReflectionMethod($this, '__construct');
        $parameters = $reflectionMethod->getParameters();
        $names = [];
        foreach ($parameters as $current) {
            $name = $current->getName();
            $names[] = $this->$name;
        }

        return $names;
    }
}