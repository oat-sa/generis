<?php

/*
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
 * Copyright (c) 2021-2022 (original work) Open Assessment Technologies SA
 *
 * @author Gabriel Felipe Soares <gabriel.felipe.soares@taotesting.com>
 */

declare(strict_types=1);

namespace oat\generis\model\Middleware;

use LogicException;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Relay\RelayBuilder;

class MiddlewareRequestHandler implements RequestHandlerInterface
{
    /** @var ResponseInterface|null */
    private $originalResponse;

    /** @var array[] */
    private $middlewareMap;

    /** @var ContainerInterface */
    private $container;

    /** @var RelayBuilder */
    private $relayBuilder;

    public function __construct(ContainerInterface $container, RelayBuilder $relayBuilder, array $middlewareMap)
    {
        $this->container = $container;
        $this->middlewareMap = $middlewareMap;
        $this->relayBuilder = $relayBuilder;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->relayBuilder
            ->newInstance($this->createMiddlewareQueue($request))
            ->handle($request);
    }

    public function withOriginalResponse(ResponseInterface $response): self
    {
        $this->originalResponse = $response;

        return $this;
    }

    /**
     * @return MiddlewareInterface[]
     */
    private function createMiddlewareQueue(RequestInterface $request): array
    {
        $mapping = [];

        foreach ($this->discoverMiddlewareIds($request) as $middlewareId) {
            $mapping[] = $this->getMiddleware($middlewareId);
        }

        $originalResponse = $this->originalResponse;

        return array_merge(
            $mapping,
            [
                static function ($request, $next) use ($originalResponse): ResponseInterface {
                    return $originalResponse;
                }
            ]
        );
    }

    /**
     * @return string[]
     */
    private function discoverMiddlewareIds(RequestInterface $request): array
    {
        $filteredMap = [];
        $preparedRoute = $request->getMethod() . $request->getUri()->getPath();

        foreach ($this->middlewareMap as $routeRegex => $mapGroup) {
            if (preg_match($routeRegex, $preparedRoute) !== 1) {
                continue;
            }

            foreach ($mapGroup as $map) {
                $filteredMap = array_merge($filteredMap, MiddlewareMap::fromJson($map)->getMiddlewaresIds());
            }
        }

        return $filteredMap;
    }

    private function getMiddleware(string $middlewareId): MiddlewareInterface
    {
        $middleware = $this->container->get($middlewareId);

        if ($middleware instanceof MiddlewareInterface) {
            return $middleware;
        }

        throw new LogicException(sprintf('Incorrect middleware configuration for %s', $middlewareId));
    }
}
