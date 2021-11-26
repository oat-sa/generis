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

class ExceptionContextExtender implements ContextExtenderInterface
{
    public function extend(array $context): array
    {
        if (isset($context[self::CONTEXT_EXCEPTION]) && $context[self::CONTEXT_EXCEPTION] instanceof Throwable) {
            $context[self::CONTEXT_EXCEPTION] = $this->buildLogMessage($context[self::CONTEXT_EXCEPTION]);
        }

        return $context;
    }

    private function buildLogMessage(Throwable $exception): string
    {
        $message = $this->createMessage($exception);

        if ($exception->getPrevious()) {
            $message = sprintf(
                '%s, previous: %s',
                $message,
                $this->buildLogMessage($exception->getPrevious())
            );
        }

        return $message;
    }

    private function createMessage(Throwable $exception): string
    {
        return sprintf(
            '"%s", code: %s, file: "%s", line: %s',
            $exception->getMessage(),
            $exception->getCode(),
            $exception->getFile(),
            $exception->getLine()
        );
    }
}
