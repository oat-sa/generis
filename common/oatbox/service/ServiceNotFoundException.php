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
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA;
 *
 */

namespace oat\oatbox\service;

use Zend\ServiceManager\Exception\ServiceNotFoundException as ZendException;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Exception thrown whenever a service is not defined and cannot be autowired
 * Compatible with the Zend Servicemanager as well as the PSR-11 container
 * @author Joel Bout <joel@taotesting.com>
 */
class ServiceNotFoundException extends ZendException implements NotFoundExceptionInterface
{
    private $serviceKey;

    public function __construct($serviceKey, $message = '')
    {
        parent::__construct('Service "' . $serviceKey . '" not found' . (empty($message) ? '' : ': ' . $message));
        $this->serviceKey = $serviceKey;
    }

    public function getServiceKey()
    {
        return $this->serviceKey;
    }
}
