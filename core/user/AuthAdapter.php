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
 */

namespace oat\generis\model\user;

use core_kernel_users_Service;
use core_kernel_classes_Class;
use common_exception_InconsistentData;
use core_kernel_classes_Property;
use core_kernel_users_InvalidLoginException;
use oat\generis\Helper\UserHashForEncryption;
use oat\generis\model\GenerisRdf;
use oat\generis\model\OntologyRdfs;
use oat\oatbox\Configurable;
use oat\oatbox\service\ServiceManager;
use oat\oatbox\user\auth\LoginAdapter;

/**
 * Authentication adapter interface to be implemented by authentication methodes
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package generis
 */
class AuthAdapter extends Configurable implements LoginAdapter
{
    public const OPTION_PATTERN = 'pattern';
    public const OPTION_USERFACTORY = 'user_factory';

    /**
     * Returns the hashing algorithm defined in generis configuration
     * use core_kernel_users_Service::getPasswordHash() instead
     *
     * @return \helpers_PasswordHash
     * @deprecated
     */
    public static function getPasswordHash()
    {
        return core_kernel_users_Service::getPasswordHash();
    }

    /**
     * Username to verify
     *
     * @var string
     */
    protected $username;

    /**
     * Password to verify
     *
     * @var $password
     */
    protected $password;

    /**
     * (non-PHPdoc)
     * @see \oat\oatbox\user\auth\LoginAdapter::setCredentials()
     */
    public function setCredentials($login, $password)
    {
        $this->username = $login;
        $this->password = $password;
    }

    /**
     * (non-PHPdoc)
     * @see \common_user_auth_Adapter::authenticate()
     * @throws \Exception
     */
    public function authenticate()
    {

        if ($this->hasOption(self::OPTION_PATTERN)) {
            if (preg_match($this->getOption(self::OPTION_PATTERN), $this->username) === 0) {
                throw new core_kernel_users_InvalidLoginException(
                    "Invalid pattern for user '" . $this->username . "'."
                );
            }
        }

        $userClass = new core_kernel_classes_Class(GenerisRdf::CLASS_GENERIS_USER);
        $filters = [GenerisRdf::PROPERTY_USER_LOGIN => $this->username];
        $options = ['like' => false, 'recursive' => true];
        $users = $userClass->searchInstances($filters, $options);


        if (count($users) > 1) {
            // Multiple users matching
            throw new common_exception_InconsistentData(
                "Multiple Users found with the same login '" . $this->username . "'."
            );
        }
        if (empty($users)) {
            // fake code execution to prevent timing attacks
            $label = new core_kernel_classes_Property(OntologyRdfs::RDFS_LABEL);
            $hash = $label->getUniquePropertyValue($label);
            if (!core_kernel_users_Service::getPasswordHash()->verify($this->password, $hash)) {
                throw new core_kernel_users_InvalidLoginException('Unknown user "' . $this->username . '"');
            }
            // should never happen, added for integrity
            throw new core_kernel_users_InvalidLoginException(
                'Inexisting user did not fail password check, this should not happen'
            );
        }

        $userResource = current($users);
        $hash = $userResource->getUniquePropertyValue(
            new core_kernel_classes_Property(GenerisRdf::PROPERTY_USER_PASSWORD)
        );
        if (!core_kernel_users_Service::getPasswordHash()->verify($this->password, $hash)) {
            throw new core_kernel_users_InvalidLoginException('Invalid password for user "' . $this->username . '"');
        }

        if ($this->hasOption(self::OPTION_USERFACTORY)) {
            $userFactory = ServiceManager::getServiceManager()->get($this->getOption(self::OPTION_USERFACTORY)) ;

            if ($userFactory instanceof UserFactoryServiceInterface) {
                return $userFactory->createUser($userResource, UserHashForEncryption::hash($this->password));
            }
        }

        return (new UserFactoryService())->createUser($userResource);
    }
}
