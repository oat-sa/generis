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
 * Copyright (c) 2021-2022 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 * @author Gabriel Felipe Soares <gabriel.felipe.soares@taotesting.com>
 */

declare(strict_types=1);

namespace oat\generis\model\DependencyInjection;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use oat\generis\model\Middleware\MiddlewareExtensionsMapper;
use oat\generis\model\Middleware\MiddlewareRequestHandler;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Relay\RelayBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\param;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

class ContainerServiceProvider implements ContainerServiceProviderInterface
{
    public function __invoke(ContainerConfigurator $configurator): void
    {
        $services = $configurator->services();

        $services->set(ServerRequestInterface::class, ServerRequestInterface::class)
            ->public()
            ->factory(ServerRequest::class . '::fromGlobals');

        $services->set(ResponseInterface::class, Response::class)
            ->public();

        $services->set(RelayBuilder::class, RelayBuilder::class);

        $services->set(LegacyServiceGateway::class, LegacyServiceGateway::class);

        $services->set(MiddlewareRequestHandler::class, MiddlewareRequestHandler::class)
            ->public()
            ->args(
                [
                    service(ContainerServiceProviderInterface::CONTAINER_SERVICE_ID),
                    service(RelayBuilder::class),
                    param(MiddlewareExtensionsMapper::MAP_KEY),
                ]
            );
    }
}
