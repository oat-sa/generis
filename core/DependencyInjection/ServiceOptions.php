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
 * Copyright (c) 2021 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 * @author Gabriel Felipe Soares <gabriel.felipe.soares@taotesting.com>
 */

declare(strict_types=1);

namespace oat\generis\model\DependencyInjection;

use oat\oatbox\service\ConfigurableService;

/**
 * @notice It is NOT RECOMMENDED to use this class. New services on container should rely on ENVIRONMENT VARIABLES,
 *         but when this is really not possible and OO techniques like, Proxy, Factory, Strategy cannot solve the
 *         issue than MAYBE this class can be used.
 */
final class ServiceOptions extends ConfigurableService implements ServiceOptionsInterface
{
    public const SERVICE_ID = 'generis/ServiceOptions';

    public function save(string $serviceId, string $option, $value): ServiceOptionsInterface
    {
        $mainOption = parent::getOption($serviceId, []);
        $mainOption[$option] = $value;

        parent::setOption($serviceId, $mainOption);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function get(string $serviceId, string $option, $default = null)
    {
        return parent::getOption($serviceId, [])[$option] ?? $default;
    }

    public function remove(string $serviceId, string $option): ServiceOptionsInterface
    {
        $allOptions = parent::getOption($serviceId, []);

        if (isset($allOptions[$option])) {
            unset($allOptions[$option]);
        }

        parent::setOption($serviceId, $allOptions);

        return $this;
    }
}
