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

class RequestContextExtender implements ContextExtenderInterface
{
    /** @var array */
    private $requestData = [];

    /** @var array */
    private $serverData;

    public function withServerData(array $serverData): self
    {
        $this->serverData = $serverData;

        return $this;
    }

    public function extend(array $context): array
    {
        if (isset($context[self::CONTEXT_REQUEST_DATA]) && is_array($context[self::CONTEXT_REQUEST_DATA])) {
            $context[self::CONTEXT_REQUEST_DATA] = array_merge(
                $context[self::CONTEXT_REQUEST_DATA],
                $this->getContextRequestData()
            );

            return $context;
        }

        $context[self::CONTEXT_REQUEST_DATA] = $this->getContextRequestData();

        return $context;
    }

    private function getContextRequestData(): array
    {
        if (!empty($this->requestData)) {
            return $this->requestData;
        }

        $serverData = $this->serverData ?? $_SERVER;

        $this->requestData = [];

        if (isset($serverData['SERVER_ADDR'])) {
            $this->requestData['serverIp'] = $serverData['SERVER_ADDR'];
        }

        if (isset($serverData['SERVER_NAME'])) {
            $this->requestData['serverName'] = $serverData['SERVER_NAME'];
        }

        if (isset($serverData['REQUEST_URI'])) {
            $this->requestData['requestUri'] = substr($serverData['REQUEST_URI'], 0, 500);
        }

        if (isset($serverData['REQUEST_METHOD'])) {
            $this->requestData['requestMethod'] = $serverData['REQUEST_METHOD'];
        }

        if (isset($serverData['argv']) && !isset($this->requestData['requestUri'])) {
            $this->requestData['argv'] = $serverData['argv'];
        }

        return $this->requestData;
    }
}
