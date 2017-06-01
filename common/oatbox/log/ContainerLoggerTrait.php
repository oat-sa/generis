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
 * Copyright (c) 2017 (original work) Open Assessment Technologies SA
 *
 */

namespace oat\oatbox\log;


use oat\oatbox\PimpleContainerTrait;
use Pimple\Container;

trait ContainerLoggerTrait
{
    // Adding container.
    use PimpleContainerTrait;

    // Adding logger.
    use LoggerAwareTrait;

    /**
     * Initialize the container and the logger.
     *
     * @param Container $container   The dependency container instance.
     * @param string    $key         The returning dependency key.
     *
     * @return mixed
     */
    public function initContainer($container, $key = '')
    {
        if ($container instanceof Container) {
            $this->setContainer($container);
            $this->setLogger(
                $this->getContainer()->offsetGet(LoggerService::SERVICE_ID)->getLogger()
            );

            // @TODO: implemented because of the legacy, we will don't need it when we're using the container in every case.
            if (!empty($key)) {
                try {
                    return $this->getContainer()->offsetGet($key);
                } catch (\InvalidArgumentException $e) {
                }
            }

            return null;
        }

        return $container;
    }
}