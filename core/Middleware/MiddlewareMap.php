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
    private const OPTION_MIDDLEWARES = 'middlewares';
    private const OPTION_ROUTES = 'routes';
    private const OPTION_HTTP_METHODS = 'httpMethods';

    /** @var string[] */
    private $routes = [];

    /** @var string[] */
    private $middlewaresIds = [];

    /** @var string[]|null */
    private $httpMethods = [];

    public static function byRoute(string $route): self
    {
        return new self([$route]);
    }

    public static function byRoutes(string ...$route): self
    {
        return new self($route);
    }

    public static function byMiddlewareId(string $middlewareId): self
    {
        return new self([], [$middlewareId]);
    }

    public static function byMiddlewareIds(string ...$middlewareIds): self
    {
        return new self([], $middlewareIds);
    }

    public function andMiddlewareId(string $middlewareId): self
    {
        $this->middlewaresIds[] = $middlewareId;

        return $this;
    }

    public function andHttpMethod(string $httpMethod): self
    {
        $this->httpMethods[] = $httpMethod;

        return $this;
    }

    public function andRoute(string $route): self
    {
        $this->routes[] = $route;

        return $this;
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

    private function __construct(array $routes = [], array $middlewaresIds = [], array $httpMethods = [])
    {
        $this->routes = $routes;
        $this->middlewaresIds = $middlewaresIds;
        $this->httpMethods = $httpMethods;
    }
}
