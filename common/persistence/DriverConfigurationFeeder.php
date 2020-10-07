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
 * Copyright (c) 2020 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 */

declare(strict_types=1);

namespace oat\generis\persistence;

use oat\oatbox\service\ConfigurableService;

class DriverConfigurationFeeder extends ConfigurableService
{
    public const SERVICE_ID = 'generis/DriverConfigurationFeeder';
    public const OPTION_DRIVER_OPTIONS = 'driverOptions';

    public function feed(array $config): array
    {
        if (empty($config['connection']['driverClass'])) {
            return $config;
        }

        $driverClass = $config['connection']['driverClass'];

        $options = $this->getOption(self::OPTION_DRIVER_OPTIONS, []);

        if (empty($options[$driverClass]) || empty($config['connection']['driverOptions'])) {
            return $config;
        }

        foreach ($options[$driverClass]['driverOptions'] as $option) {
            $config = $this->feedConfigWithService($config, $option);
        }

        return $config;
    }

    private function feedConfigWithService(array $config, string $option): array
    {
        if (empty($config['connection']['driverOptions'][$option])) {
            return $config;
        }

        if (is_object($config['connection']['driverOptions'][$option])) {
            return $config;
        }

        $config['connection']['driverOptions'][$option] = $this->getServiceLocator()
            ->get($config['connection']['driverOptions'][$option]);

        return $config;
    }
}
