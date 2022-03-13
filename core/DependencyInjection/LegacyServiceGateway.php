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
 *
 * @author Gabriel Felipe Soares <gabriel.felipe.soares@taotesting.com>
 */

declare(strict_types=1);

namespace oat\generis\model\DependencyInjection;

use oat\oatbox\service\ConfigurableService;
use oat\oatbox\service\ServiceManager;
use Psr\Container\ContainerInterface;

class LegacyServiceGateway implements ContainerInterface
{
    /** @var ServiceManager|null */
    private $serviceManager;

    public function __construct(ServiceManager $serviceManager = null)
    {
        $this->serviceManager = $serviceManager ?? ServiceManager::getServiceManager();
    }

    public function __invoke($id = null)
    {
        return $id ? $this->serviceManager->get($id) : $this->serviceManager;
    }

    /**
     * @inheritDoc
     */
    public function get($id)
    {
        return $this->serviceManager->get($id);
    }

    /**
     * @inheritDoc
     */
    public function has($id)
    {
        return $this->serviceManager->has($id) || $this->isConfigurableService($id);
    }

    private function isConfigurableService($id): bool
    {
        return class_exists($id) && is_subclass_of($id, ConfigurableService::class);
    }
}
