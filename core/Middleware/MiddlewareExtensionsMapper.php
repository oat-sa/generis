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
 * Copyright (c) 2022 (original work) Open Assessment Technologies SA;
 *
 * @author Gabriel Felipe Soares <gabriel.felipe.soares@taotesting.com>
 */

declare(strict_types=1);

namespace oat\generis\model\Middleware;

use common_ext_Extension;

class MiddlewareExtensionsMapper
{
    public const MAP_KEY = 'middlewaresMap';

    /**
     * @param common_ext_Extension[] $extensions
     */
    public function map(array $extensions): array
    {
        $middlewareMap = [];

        foreach ($extensions as $extension) {
            foreach ($extension->getManifest()->getMiddlewares() as $map) {
                if (!in_array(MiddlewareConfigInterface::class, class_implements($map), true)) {
                    continue;
                }

                $middlewareConfigs = (new $map)();

                /** @var MiddlewareMapInterface $middlewareConfig */
                foreach ($middlewareConfigs as $middlewareConfig) {
                    foreach ($middlewareConfig->getRoutes() as $route) {
                        $methods = empty($middlewareConfig->getHttpMethods())
                            ? '(.*)'
                            : ('(' . implode('|', (array)$middlewareConfig->getHttpMethods()) . ')');

                        $route = strpos($route, '/') > 0 ? ('/' . $route) : $route;
                        $route = addslashes($route);
                        $route = str_replace('/', '\/', $route);
                        $route = '/^' . $methods . $route . '$/';

                        $middlewareMap[$route] = $middlewareMap[$route] ?? [];
                        $middlewareMap[$route][] = $middlewareConfig->jsonSerialize();
                    }
                }
            }
        }

        return $middlewareMap;
    }
}
