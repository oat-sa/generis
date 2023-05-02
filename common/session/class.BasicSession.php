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
 * Copyright (c) 2017-2021 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 */

/*
 * Represents a Session on Generis.
 *
 * @access private
 * @author Joel Bout, <joel@taotesting.com>
 * @package generis

 */

use oat\generis\model\GenerisRdf;
use oat\generis\model\OntologyRdfs;
use oat\oatbox\Refreshable;
use oat\oatbox\session\SessionContext;
use oat\oatbox\user\User;
use oat\oatbox\user\UserLanguageServiceInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\ServiceManager\ServiceLocatorInterface;

class common_session_BasicSession implements common_session_Session, ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait {
        setServiceLocator as protected setOriginalServiceLocator;
    }

    /**
     * @var common_user_User
     */
    private $user;

    /**
     * @var SessionContext[]
     */
    private $contexts;

    /**
     * @param SessionContext[] $context
     */
    public function __construct(User $user, array $contexts = [])
    {
        $this->user = $user;
        $this->contexts = $contexts;
    }

    public function getUser()
    {
        return $this->user;
    }

    /**
     * {@inheritDoc}
     *
     * @see common_session_Session::getUserUri()
     */
    public function getUserUri()
    {
        return $this->user->getIdentifier();
    }

    /**
     * @param string $property
     *
     * @return mixed
     */
    public function getUserPropertyValues($property)
    {
        return $this->user->getPropertyValues($property);
    }

    /**
     * (non-PHPdoc)
     *
     * @see common_session_Session::getUserLabel()
     */
    public function getUserLabel()
    {
        $label = '';
        $first = $this->user->getPropertyValues(GenerisRdf::PROPERTY_USER_FIRSTNAME);
        $label .= empty($first) ? '' : current($first);
        $last = $this->user->getPropertyValues(GenerisRdf::PROPERTY_USER_LASTNAME);
        $label .= empty($last) ? '' : ' ' . current($last);
        $label = trim($label);

        if (empty($label)) {
            $login = $this->user->getPropertyValues(GenerisRdf::PROPERTY_USER_LOGIN);

            if (!empty($login)) {
                $label = current($login);
            }
        }

        if (empty($label)) {
            $rdflabel = $this->user->getPropertyValues(OntologyRdfs::RDFS_LABEL);
            $label = empty($rdflabel) ? __('user') : current($rdflabel);
        }

        return $label;
    }

    /**
     * {@inheritDoc}
     *
     * @see common_session_Session::getUserRoles()
     */
    public function getUserRoles()
    {
        $returnValue = [];
        // We use a Depth First Search approach to flatten the Roles Graph.
        foreach ($this->user->getPropertyValues(GenerisRdf::PROPERTY_USER_ROLES) as $roleUri) {
            $returnValue[$roleUri] = $roleUri;
            $role = new core_kernel_classes_Resource($roleUri);

            foreach (core_kernel_users_Service::singleton()->getIncludedRoles($role) as $incRole) {
                $returnValue[$incRole->getUri()] = $incRole->getUri();
            }
        }

        return $returnValue;
    }

    /**
     * @return string language code (e.g. 'en-US')
     */
    public function getDataLanguage()
    {
        return $this->getServiceLocator()->get(UserLanguageServiceInterface::class)->getDataLanguage($this->getUser());
    }

    /**
     * @return string language code (e.g. 'en-US')
     */
    public function getInterfaceLanguage()
    {
        /** @var PHPSession $session */
        $session = PHPSession::singleton();

        /** @var UserLanguageServiceInterface $userLanguageService */
        $userLanguageService = $this->getServiceLocator()->get(UserLanguageServiceInterface::class);

        if ($session->hasAttribute('overrideInterfaceLanguage')) {
            $userLanguageService->setCustomInterfaceLanguage($session->getAttribute('overrideInterfaceLanguage'));
        } else {
            // Just to be sure the custom interface language is removed when the session attribute is gone
            $userLanguageService->setCustomInterfaceLanguage(null);
        }

        return $userLanguageService->getInterfaceLanguage($this->getUser());
    }

    /**
     * (non-PHPdoc)
     *
     * @see common_session_Session::getTimeZone()
     */
    public function getTimeZone()
    {
        $tzs = $this->user->getPropertyValues(GenerisRdf::PROPERTY_USER_TIMEZONE);
        $tz = empty($tzs) ? '' : (string)current($tzs);

        return empty($tz) ? TIME_ZONE : $tz;
    }

    public function refresh()
    {
        if ($this->user instanceof Refreshable) {
            $this->user->refresh();
        }
    }

    /**
     * {@inheritDoc}
     *
     * @see \Zend\ServiceManager\ServiceLocatorAwareInterface::setServiceLocator()
     * propagate to user
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        if ($this->user instanceof ServiceLocatorAwareInterface) {
            $this->user->setServiceLocator($serviceLocator);
        }

        return $this->setOriginalServiceLocator($serviceLocator);
    }

    public function getContexts(string $class = null): array
    {
        $contexts = $this->contexts;

        if ($class != null) {
            $contexts = array_filter($contexts, function ($element) use ($class) {
                return $element instanceof $class;
            });
        }

        return $contexts;
    }
}
