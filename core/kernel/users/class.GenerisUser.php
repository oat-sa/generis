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
 * Copyright (c) 2017 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 *
 */

use oat\generis\model\GenerisRdf;
use oat\generis\model\kernel\users\UserInternalInterface;
use oat\generis\model\OntologyRdf;

/**
 * Authentication adapter interface to be implemented by authentication methodes
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package generis

 */
class core_kernel_users_GenerisUser extends common_user_User implements UserInternalInterface
{
    /**
     * @var string identifier of user
     */
    private $identifier;

    private $cache;

    private $cachedProperties = [
        GenerisRdf::PROPERTY_USER_DEFLG,
        GenerisRdf::PROPERTY_USER_ROLES,
        GenerisRdf::PROPERTY_USER_UILG,
        GenerisRdf::PROPERTY_USER_FIRSTNAME,
        GenerisRdf::PROPERTY_USER_LASTNAME,
        GenerisRdf::PROPERTY_USER_LOGIN,
        GenerisRdf::PROPERTY_USER_TIMEZONE,
    ];

    public function __construct(core_kernel_classes_Resource $user)
    {
        $this->identifier = $user->getUri();
        // load datalanguage to prevent cycle later on
        $this->getPropertyValues(GenerisRdf::PROPERTY_USER_DEFLG);
    }

    public function getIdentifier()
    {
        return $this->identifier;
    }

    private function getUserResource()
    {
        return new core_kernel_classes_Resource($this->getIdentifier());
    }

    public function getPropertyValues($property)
    {
        if (!in_array($property, $this->cachedProperties)) {
            return $this->getUncached($property);
        } elseif (!isset($this->cache[$property])) {
            $this->cache[$property] = $this->getUncached($property);
        }

        return $this->cache[$property];
    }

    private function getUncached($property)
    {
        switch ($property) {
            case GenerisRdf::PROPERTY_USER_DEFLG:
                $lang = $this->findLanguage($property);

                return $lang ?: [DEFAULT_LANG];

            case GenerisRdf::PROPERTY_USER_UILG:
                return $this->findLanguage($property);

            default:
                return $this->getUserResource()->getPropertyValues(new core_kernel_classes_Property($property));
        }
    }

    /**
     * @return array|string[]
     * @throws common_Exception
     * @throws core_kernel_classes_EmptyProperty
     * @throws core_kernel_persistence_Exception
     */
    private function findLanguage(string $propertyURI): array
    {
        $language = [];
        $resource = $this->getUserResource()->getOnePropertyValue(
            new core_kernel_classes_Property($propertyURI)
        );

        if (is_null($resource)) {
            return $language;
        }

        if (!$resource instanceof core_kernel_classes_Resource) {
            common_Logger::w("Language {$resource} is not a resource");

            return $language;
        }

        return [
           (string) $resource->getUniquePropertyValue(
               new core_kernel_classes_Property(OntologyRdf::RDF_VALUE)
           )
        ];
    }

    public function refresh()
    {
        $this->roles = false;
        $this->cache = [
            GenerisRdf::PROPERTY_USER_DEFLG => $this->getUncached(GenerisRdf::PROPERTY_USER_DEFLG)
        ];
        return true;
    }
}
