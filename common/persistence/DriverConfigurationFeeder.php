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
    private const DRIVER_OPTIONS_REPLACE = [
        'OAT\Library\DBALSpanner\SpannerDriver' => [
            'driverOptions' => [
                'driver-option-auth-pool',
                'driver-option-session-pool'
            ]
        ]
    ];

    public function feed(array $config): array
    {
        if (empty($config['connection']['driverClass'])) {
            return $config;
        }

        $driverClass = $config['connection']['driverClass'];

        if (empty(self::DRIVER_OPTIONS_REPLACE[$driverClass]) || empty($config['connection']['driverOptions'])) {
            return $config;
        }

        foreach (self::DRIVER_OPTIONS_REPLACE[$driverClass]['driverOptions'] as $option) {
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
