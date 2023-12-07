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
 * Copyright (c) 2023 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */

namespace oat\generis\model\kernel\persistence\starsql;

class LanguageProcessor
{
    public const LANGUAGE_TAGGED_VALUE_PATTERN = "/^(.*)@([a-zA-Z\\-]{5,6})$/";

    public function filterByLanguage($entries, $allowedLanguages): array
    {
        if (empty($entries)) {
            return [];
        }

        $filteredValues = [];
        foreach ($entries as $entry) {
            // collect all entries with matching language or without language
            $matchSuccess = preg_match(self::LANGUAGE_TAGGED_VALUE_PATTERN, $entry, $matches);
            if (!$matchSuccess) {
                $filteredValues[] = $entry;
            } elseif (isset($matches[2]) && in_array($matches[2], $allowedLanguages)) {
                $filteredValues[] = $matches[1];
            }
        }

        return $filteredValues;
    }

    public function filterByAvailableLanguage($entries, $dataLanguage, $defaultLanguage): array
    {
        if (empty($entries)) {
            return [];
        }

        $fallbackLanguage = '';

        foreach ($entries as $entry) {
            preg_match(self::LANGUAGE_TAGGED_VALUE_PATTERN, $entry, $matches);
            $entryLang = $matches[2] ?? $fallbackLanguage;
            $sortedResults[$entryLang][] = $matches[1] ?? $entry;
        }

        $languageOrderedEntries = [
            $dataLanguage,
            $defaultLanguage,
            $fallbackLanguage,
        ];

        $returnValue = [];
        foreach ($languageOrderedEntries as $language) {
            if (isset($sortedResults[$language])) {
                $returnValue = $sortedResults[$language];
                break;
            }
        }

        return (array) $returnValue;
    }

    public function parseTranslatedValue($value): string
    {
        preg_match(self::LANGUAGE_TAGGED_VALUE_PATTERN, (string)$value, $matches);

        return $matches[1] ?? (string) $value;
    }

    public function parseTranslatedLang($value): string
    {
        preg_match(self::LANGUAGE_TAGGED_VALUE_PATTERN, (string)$value, $matches);

        return $matches[2] ?? '';
    }
}
