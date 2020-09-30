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

use OAT\Library\DBALSpanner\SpannerDriver;
use oat\oatbox\service\ConfigurableService;

/**
 * @FIXME This will be refactored before merge
 *
 * @TODO The configuration mapping will be added by .conf.php file, so we do not need to use specific driver decisions here
 */
class DriverConfigFeeder extends ConfigurableService
{
    private const SPANNER_DRIVER = 'OAT\Library\DBALSpanner\SpannerDriver';

    public function feed(array $config): array
    {
        $driverClass = $config['connection']['driverClass'] ?? null;

        if ($driverClass === self::SPANNER_DRIVER && !empty($config['connection']['driverOptions'])) {
            $config = $this->feedConfigWithService($config, SpannerDriver::DRIVER_OPTION_AUTH_POOL);
            $config = $this->feedConfigWithService($config, SpannerDriver::DRIVER_OPTION_SESSION_POOL);
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
