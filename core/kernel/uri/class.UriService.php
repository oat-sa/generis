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

use oat\generis\model\kernel\uri\UriProvider;
use oat\oatbox\service\ServiceManager;

/**
 * Provides backward compatibility to generates a URI
 *
 * @author Joel Bout <joel@taotesting.com>
 * @deprecated
 */
class core_kernel_uri_UriService
{
    const CONFIG_KEY = 'uriProvider';
        
    private static $instance;
    
    public static function singleton()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private $uriProvider = null;
    
    /**
     * Generate a new URI with the UriProvider in force.
     *
     * @return string
     */
    public function generateUri()
    {
        return (string) $this->getUriProvider()->provide();
    }
    
    /**
     * Set the UriProvider in force.
     *
     * @param UriProvider $provider
     */
    public function setUriProvider(UriProvider $provider)
    {
        $this->uriProvider = $provider;
        ServiceManager::getServiceManager()->register(UriProvider::SERVICE_ID, $provider);
    }
    
    /**
     * Get the UriProvider in force.
     *
     * @return UriProvider
     */
    public function getUriProvider()
    {
        if (is_null($this->uriProvider)) {
            $this->uriProvider = ServiceManager::getServiceManager()->get(UriProvider::SERVICE_ID);
        }
        return $this->uriProvider;
    }
}
