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
 */

declare(strict_types=1);

namespace oat\oatbox\log\ServiceProvider;

use oat\oatbox\log\LoggerService;
use oat\oatbox\session\SessionService;
use oat\oatbox\log\logger\AdvancedLogger;
use oat\oatbox\log\logger\extender\UserContextExtender;
use oat\oatbox\log\logger\extender\RequestContextExtender;
use oat\oatbox\log\logger\extender\ExceptionContextExtender;
use oat\generis\model\DependencyInjection\ContainerServiceProviderInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\param;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

class LogServiceProvider implements ContainerServiceProviderInterface
{
    public function __invoke(ContainerConfigurator $configurator): void
    {
        $services = $configurator->services();

        $services->set(ExceptionContextExtender::class, ExceptionContextExtender::class);
        $services->set(RequestContextExtender::class, RequestContextExtender::class);
        $services
            ->set(UserContextExtender::class, UserContextExtender::class)
            ->args(
                [
                    service(SessionService::SERVICE_ID),
                ]
            );

        $services
            ->set(UserContextExtender::ACL_SERVICE_ID, UserContextExtender::class)
            ->args(
                [
                    service(SessionService::SERVICE_ID),
                    true,
                ]
            );

        $services
            ->set(AdvancedLogger::class, AdvancedLogger::class)
            ->public()
            ->call(
                'addContextExtender',
                [
                    service(ExceptionContextExtender::class),
                ]
            )
            ->call(
                'addContextExtender',
                [
                    service(RequestContextExtender::class),
                ]
            )
            ->call(
                'addContextExtender',
                [
                    service(UserContextExtender::class),
                ]
            )
            ->args(
                [
                    service(LoggerService::SERVICE_ID),
                ]
            );

        $services
            ->set(AdvancedLogger::ACL_SERVICE_ID, AdvancedLogger::class)
            ->public()
            ->args(
                [
                    service(LoggerService::SERVICE_ID),
                ]
            )
            ->call(
                'addContextExtender',
                [
                    service(ExceptionContextExtender::class),
                ]
            )
            ->call(
                'addContextExtender',
                [
                    service(RequestContextExtender::class),
                ]
            )
            ->call(
                'addContextExtender',
                [
                    service(UserContextExtender::ACL_SERVICE_ID),
                ]
            );
    }
}
