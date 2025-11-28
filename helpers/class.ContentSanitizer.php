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
 * 31 Milk St # 960789 Boston, MA 02196 USA.
 *
 * Copyright (c) 2025 Open Assessment Technologies SA
 */

declare(strict_types=1);

use oat\generis\model\OntologyRdfs;

class helpers_ContentSanitizer
{
    private const DEFAULT_ENCODING = 'UTF-8';

    /**
     * Encode the provided string so it can safely be stored without
     * introducing executable HTML. All HTML special characters are escaped.
     *
     * @param string $value
     * @param string $encoding
     */
    public static function sanitize(core_kernel_classes_Property $property, $value, string $encoding = self::DEFAULT_ENCODING): mixed
    {
        if (!is_string($value)) {
            return $value;
        }

        if ($property->getUri() === OntologyRdfs::RDFS_LABEL) {
            $encoding = $encoding !== '' ? $encoding : self::DEFAULT_ENCODING;
            return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, $encoding, false);
        }

        return $value;
    }
}
