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
 */

declare(strict_types=1);

namespace oat\generis\model\Middleware;

use GuzzleHttp\Psr7\Response;
use LogicException;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Relay\Relay;

class MiddlewareRequestHandler implements RequestHandlerInterface
{
    /** @var ResponseInterface|null */
    private $originalResponse;

    /** @var array[] */
    private $map;

    /** @var ContainerInterface */
    private $container;

    public function __construct(ContainerInterface $container, array $map)
    {
        $this->container = $container;
        $this->map = $map;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $queue = $this->build($request);
        return (new Relay($queue))->handle($request);
    }

    /**
     * @return MiddlewareInterface[]
     */
    private function build(RequestInterface $request): array
    {
        $mapping = [];

        foreach ($this->maps($request) as $middlewareClass) {
            $mapping[] = $this->getMiddleware($middlewareClass);
        }

        return array_merge(
            [
                function ($request, $next) {
                    return $this->originalResponse ?? new Response();
                }
            ],
            $mapping,
            [
                function ($request, $next) {
                    return new Response();
                }
            ]
        );
    }

    public function withOriginalResponse(ResponseInterface $response): self
    {
        $this->originalResponse = $response;
        return $this;
    }

    /**
     * @return string[]
     */
    private function maps(RequestInterface $request): array
    {
        $filteredMap = [];

        $maps = $this->map[$request->getUri()->getPath()] ?? [];

        foreach ($maps as $map) {
            $middlewareMap = MiddlewareMap::fromJson($map);
            if (in_array($request->getMethod(), $middlewareMap->getHttpMethods(), true)) {
                $filteredMap = array_merge(
                    $filteredMap,
                    $middlewareMap->getMiddlewaresIds()
                );
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
