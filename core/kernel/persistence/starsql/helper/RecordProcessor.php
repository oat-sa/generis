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
 * @author Gabriel Felipe Soares <gabriel.felipe.soares@taotesting.com>
 */

namespace oat\generis\model\kernel\persistence\starsql\helper;

class RecordProcessor
{
    public const LANGUAGE_TAGGED_VALUE_PATTERN = "/^(.*)@([a-zA-Z\\-]{5,6})$/";

    public function filterRecordsByLanguage($entries, $allowedLanguages): array
    {
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

    public function filterRecordsByAvailableLanguage($entries, $dataLanguage, $defaultLanguage): array
    {
        $fallbackLanguage = '';

        $sortedResults = [
            $dataLanguage => [],
            $defaultLanguage => [],
            $fallbackLanguage => []
        ];

        foreach ($entries as $entry) {
            $matchSuccess = preg_match(self::LANGUAGE_TAGGED_VALUE_PATTERN, $entry, $matches);
            $entryLang = $matches[2] ?? '';
            $sortedResults[$entryLang][] = [
                'value' => $matches[1] ?? $entry,
                'language' => $entryLang
            ];
        }

        $languageOrderedEntries = array_merge(
            $sortedResults[$dataLanguage],
            (count($sortedResults) > 2) ? $sortedResults[$defaultLanguage] : [],
            $sortedResults[$fallbackLanguage]
        );

        $returnValue = [];
        if (count($languageOrderedEntries) > 0) {
            $previousLanguage = $languageOrderedEntries[0]['language'];

            foreach ($languageOrderedEntries as $value) {
                if ($value['language'] == $previousLanguage) {
                    $returnValue[] = $value['value'];
                } else {
                    break;
                }
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