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
 * Copyright (c) 2025 Open Assessment Technologies SA
 */

declare(strict_types=1);

class helpers_ContentSanitizer
{
    private const DEFAULT_ENCODING = 'UTF-8';

    /**
     * Encode the provided string so it can safely be stored without
     * introducing executable HTML. All HTML special characters are escaped.
     *
     * @param string $value
     * @param string $encoding
     * @return string
     */
    public static function sanitizeString($value, string $encoding = self::DEFAULT_ENCODING): string
    {
        $encoding = $encoding !== '' ? $encoding : self::DEFAULT_ENCODING;

        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, $encoding, false);
    }

    /**
     * Sanitize only when the provided value is a string.
     *
     * @param mixed $value
     * @return string|mixed
     */
    public static function sanitize($value)
    {
        if (!is_string($value)) {
            return $value;
        }

        return self::sanitizeString($value);
    }
}
