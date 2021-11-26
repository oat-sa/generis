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
 * Copyright (c) 2021 (original work) Open Assessment Technologies SA
 */

declare(strict_types=1);

namespace oat\oatbox\log\logger\extender;

use Throwable;
use oat\oatbox\session\SessionService;

class UserContextExtender implements ContextExtenderInterface
{
    public const USER_ACL_CONTEXT_EXTENDER = self::class . '::USER_ACL_CONTEXT_EXTENDER';

    /** @var SessionService */
    private $sessionService;

    /** @var array|null */
    private $userData;

    /** @var bool */
    private $extendWithUserRoles;

    public function __construct(SessionService $sessionService, bool $extendWithUserRoles = false)
    {
        $this->sessionService = $sessionService;
        $this->extendWithUserRoles = $extendWithUserRoles;
    }

    public function extend(array $context): array
    {
        if (isset($context[self::CONTEXT_USER_DATA]) && is_array($context[self::CONTEXT_USER_DATA])) {
            $context[self::CONTEXT_USER_DATA] = array_merge(
                $context[self::CONTEXT_USER_DATA],
                $this->getContextUserData()
            );

            return $context;
        }

        $context[self::CONTEXT_USER_DATA] = $this->getContextUserData();

        return $context;
    }

    private function getContextUserData(): array
    {
        if ($this->userData === null) {
            $this->userData = [
                'id' => $this->getUserIdentifier(),
            ];

            if ($this->extendWithUserRoles) {
                $this->userData['roles'] = $this->getUserRoles();
            }
        }

        return $this->userData;
    }

    private function getUserIdentifier(): ?string
    {
        try {
            if ($this->sessionService->isAnonymous()) {
                return 'anonymous';
            }

            $user = $this->sessionService->getCurrentUser();

            return $user ? $user->getIdentifier() : 'system';
        } catch (Throwable $exception) {
            return 'unreachable';
        }
    }

    private function getUserRoles(): array
    {
        try {
            $user = $this->sessionService->getCurrentUser();

            return $user ? $user->getRoles() : [];
        } catch (Throwable $exception) {
            return [];
        }
    }
}
