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
 */

declare(strict_types=1);

namespace oat\generis\model\Middleware;

final class MiddlewareMap implements MiddlewareMapInterface
{
    /** @var string[] */
    private $routes;

    /** @var string[] */
    private $middlewaresIds;

    /** @var string[]|null */
    private $httpMethods;

    /**
     * For now, we only map middlewares per single route.
     *
     * @TODO Create mapping per multiple routes and per middlewares in the future
     */
    public static function perRoute(
        string $route,
        array $middlewaresIds,
        array $httpMethods = null
    ): MiddlewareMapInterface {
        return new self(
            [$route],
            $middlewaresIds,
            $httpMethods
        );
    }

    public function getRoutes(): array
    {
        return $this->routes;
    }

    public function getMiddlewaresIds(): array
    {
        return $this->middlewaresIds;
    }

    public function getHttpMethods(): ?array
    {
        return $this->httpMethods;
    }

    public function jsonSerialize(): array
    {
        return [
            self::OPTION_ROUTES => $this->routes,
            self::OPTION_HTTP_METHODS => $this->httpMethods,
            self::OPTION_MIDDLEWARES => $this->middlewaresIds,
        ];
    }

    public static function fromJson(array $json): MiddlewareMapInterface
    {
        return new self(
            $json[self::OPTION_ROUTES],
            $json[self::OPTION_MIDDLEWARES],
            $json[self::OPTION_HTTP_METHODS]
        );
    }

    private function __construct(array $routes, array $middlewaresIds, array $httpMethods = null)
    {
        $this->routes = $routes;
        $this->middlewaresIds = $middlewaresIds;
        $this->httpMethods = $httpMethods;
    }
}
