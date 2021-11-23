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

use oat\oatbox\session\SessionService;
use Throwable;

class UserContextExtender implements ContextExtenderInterface
{
    /** @var SessionService */
    private $sessionService;

    /** @var array|null */
    private $userData;

    public function __construct(SessionService $sessionService)
    {
        $this->sessionService = $sessionService;
    }

    public function extend(array $context): array
    {
        $context[self::CONTEXT_USER_DATA] = isset($context[self::CONTEXT_USER_DATA])
            && is_array($context[self::CONTEXT_USER_DATA])
            ? array_merge($context[self::CONTEXT_USER_DATA], $this->getContextUserData())
            : $this->getContextUserData();

        return $context;
    }

    private function getContextUserData(): array
    {
        if ($this->userData === null) {
            $this->userData = [
                'id' => $this->getUserIdentifier(),
            ];
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
}
