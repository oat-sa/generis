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
 * Copyright (c) 2014-2021 (original work) Open Assessment Technologies SA;
 */

namespace oat\oatbox;

use common_exception_Error;
use common_Utils;
use oat\oatbox\log\LoggerAwareTrait;
use oat\oatbox\log\TaoLoggerAwareInterface;

/**
 * Configurable base class
 *
 * inspired by Solarium\Core\Configurable by Bas de Nooijer
 * https://github.com/basdenooijer/solarium/blob/master/library/Solarium/Core/Configurable.php
 *
 * @author Joel Bout <joel@taotesting.com>
 *
 * @deprecated New services must be registered using Dependency Injection Container
 */
abstract class Configurable implements PhpSerializable, TaoLoggerAwareInterface
{
    use LoggerAwareTrait;

    /** @var array */
    private $options = [];

    /**
     * public constructor to allow the object to be recreated from php code
     *
     * @param array $options
     *
     * @deprecated New services must be registered using Dependency Injection Container
     */
    public function __construct($options = [])
    {
        $this->setOptions($options);
    }

    /**
     * (non-PHPdoc)
     *
     * @see \oat\oatbox\PhpSerializable::__toPhpCode()
     * @deprecated Use \oat\generis\model\DependencyInjection\ServiceOptions instead
     */
    public function __toPhpCode()
    {
        $options = $this->getOptions();
        $params = empty($options) ? '' : common_Utils::toHumanReadablePhpString($options);

        return 'new ' . get_class($this) . '(' . $params . ')';
    }

    /**
     * @deprecated Use \oat\generis\model\DependencyInjection\ServiceOptions instead
     *
     * @param mixed $name
     * @param mixed $value
     */
    public function setOption($name, $value)
    {
        $this->options[$name] = $value;
    }

    /**
     * Set options
     *
     * @param array $options
     *
     * @throws common_exception_Error
     *
     * @return void
     *
     * @deprecated Use \oat\generis\model\DependencyInjection\ServiceOptions instead
     */
    public function setOptions(array $options)
    {
        if (!is_array($options)) {
            if (is_object($options) && method_exists($options, 'toArray')) {
                $options = $options->toArray();
            } else {
                throw new common_exception_Error(
                    'Options submitted to ' . get_called_class() . ' must be an array or implement toArray'
                );
            }
        }
        $this->options = $options;
    }

    /**
     * Returns whenever or not the option is defined
     *
     * @param string $name
     *
     * @return boolean
     *
     * @deprecated Use \oat\generis\model\DependencyInjection\ServiceOptions instead
     */
    public function hasOption($name)
    {
        return isset($this->options[$name]);
    }

    /**
     * Get an option value by name
     *
     * If the option is not set a default value will be returned.
     *
     * @param string $name
     * @param mixed|null $default default value to return if option is not set
     *
     * @return mixed
     *
     * @deprecated Use \oat\generis\model\DependencyInjection\ServiceOptions instead
     */
    public function getOption($name, $default = null)
    {
        return $this->options[$name] ?? $default;
    }

    /**
     * Get all options
     *
     * @return array
     *
     * @deprecated Use \oat\generis\model\DependencyInjection\ServiceOptions instead
     */
    public function getOptions()
    {
        return $this->options;
    }
}
