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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */

use oat\generis\model\GenerisRdf;

class helpers_PropertyLgCacheHelper
{

    private static function getSerial($uri){
        return 'isPropertyLg' . md5($uri);
    }

    /**
     * @param string $uri property uri
     * @return bool
     * @throws core_kernel_persistence_Exception
     */
    public static function getLgDependencyCache($uri)
    {
        try {
            $lgDependencyCache = common_cache_FileCache::singleton()->get(self::getSerial($uri));
        } catch (common_cache_NotFoundException $e) {
            $prop = new \core_kernel_classes_Property($uri);
            $lgDependentProperty = new \core_kernel_classes_Property(GenerisRdf::PROPERTY_IS_LG_DEPENDENT);
            $lgDependent = $prop->getOnePropertyValue($lgDependentProperty);

            if (is_null($lgDependent) || !$lgDependent instanceof \core_kernel_classes_Resource){
                $lgDependencyCache = false;
            } else {
                $lgDependencyCache = ($lgDependent->getUri() == GenerisRdf::GENERIS_TRUE);
            }
            self::setLgDependencyCache($uri, $lgDependencyCache);
        }
        return $lgDependencyCache;
    }
    
    public static function setLgDependencyCache($uri,$bool){
   
        $lgDependencyCache = common_cache_FileCache::singleton()->put($bool, self::getSerial($uri));
    }
    
}
