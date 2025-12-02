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

namespace oat\generis\test\unit\helpers;

use core_kernel_classes_Property;
use oat\generis\model\OntologyRdfs;
use oat\generis\test\GenerisTestCase;
use PHPUnit\Framework\MockObject\MockObject;

class ContentSanitizerTest extends GenerisTestCase
{
    /**
     * Test that string values with HTML characters on RDFS_LABEL property are escaped.
     */
    public function testSanitizeStringWithHtmlCharactersOnRdfsLabel(): void
    {
        $property = $this->createPropertyMock(OntologyRdfs::RDFS_LABEL);
        $xssPayload = '<script>alert(document.cookie)</script>';
        $expected = '&lt;script&gt;alert(document.cookie)&lt;/script&gt;';

        $result = \helpers_ContentSanitizer::sanitize($property, $xssPayload);

        $this->assertSame($expected, $result);
    }

    /**
     * Test that various HTML special characters are properly escaped on RDFS_LABEL.
     */
    public function testSanitizeVariousHtmlCharactersOnRdfsLabel(): void
    {
        $property = $this->createPropertyMock(OntologyRdfs::RDFS_LABEL);

        $testCases = [
            '<script>' => '&lt;script&gt;',
            '</script>' => '&lt;/script&gt;',
            '<img src="x" onerror="alert(1)">' => '&lt;img src=&quot;x&quot; onerror=&quot;alert(1)&quot;&gt;',
            '<div onclick="alert(1)">Click</div>' => '&lt;div onclick=&quot;alert(1)&quot;&gt;Click&lt;/div&gt;',
            "Text with 'quotes' and \"double quotes\"" => 'Text with &#039;quotes&#039; and &quot;double quotes&quot;',
            '<>&"\'`' => '&lt;&gt;&amp;&quot;&#039;`',
        ];

        foreach ($testCases as $input => $expected) {
            $result = \helpers_ContentSanitizer::sanitize($property, $input);
            $this->assertSame($expected, $result, "Failed to sanitize: {$input}");
        }
    }

    /**
     * Test that non-string values are returned unchanged.
     */
    public function testSanitizeNonStringValuesReturnedUnchanged(): void
    {
        $property = $this->createPropertyMock(OntologyRdfs::RDFS_LABEL);

        $testCases = [
            123,
            45.67,
            true,
            false,
            null,
            [],
            ['key' => 'value'],
            new \stdClass(),
        ];

        foreach ($testCases as $value) {
            $result = \helpers_ContentSanitizer::sanitize($property, $value);
            $this->assertSame($value, $result, 'Non-string value should be returned unchanged');
        }
    }

    /**
     * Test that non-RDFS_LABEL properties return values unchanged.
     */
    public function testSanitizeNonRdfsLabelPropertiesReturnedUnchanged(): void
    {
        $property = $this->createPropertyMock('http://example.com/other-property');
        $xssPayload = '<script>alert(document.cookie)</script>';

        $result = \helpers_ContentSanitizer::sanitize($property, $xssPayload);

        $this->assertSame($xssPayload, $result, 'Non-RDFS_LABEL property should return value unchanged');
    }

    /**
     * Test that non-RDFS_LABEL properties return non-string values unchanged.
     */
    public function testSanitizeNonRdfsLabelPropertiesWithNonStringValues(): void
    {
        $property = $this->createPropertyMock('http://example.com/other-property');

        $testCases = [
            'plain text',
            123,
            ['array' => 'value'],
            null,
        ];

        foreach ($testCases as $value) {
            $result = \helpers_ContentSanitizer::sanitize($property, $value);
            $this->assertSame($value, $result, 'Non-RDFS_LABEL property should return value unchanged');
        }
    }

    /**
     * Test that empty encoding falls back to default UTF-8.
     */
    public function testSanitizeEmptyEncodingFallsBackToDefault(): void
    {
        $property = $this->createPropertyMock(OntologyRdfs::RDFS_LABEL);
        $xssPayload = '<script>alert(document.cookie)</script>';
        $expected = '&lt;script&gt;alert(document.cookie)&lt;/script&gt;';

        $result = \helpers_ContentSanitizer::sanitize($property, $xssPayload, '');

        $this->assertSame($expected, $result);
    }

    /**
     * Test that custom encoding is respected.
     */
    public function testSanitizeWithCustomEncoding(): void
    {
        $property = $this->createPropertyMock(OntologyRdfs::RDFS_LABEL);
        $xssPayload = '<script>alert(document.cookie)</script>';
        $expected = '&lt;script&gt;alert(document.cookie)&lt;/script&gt;';

        $result = \helpers_ContentSanitizer::sanitize($property, $xssPayload, 'UTF-8');

        $this->assertSame($expected, $result);
    }

    /**
     * Test that regular text without HTML is preserved on RDFS_LABEL.
     */
    public function testSanitizeRegularTextOnRdfsLabel(): void
    {
        $property = $this->createPropertyMock(OntologyRdfs::RDFS_LABEL);
        $plainText = 'This is regular text without any HTML';

        $result = \helpers_ContentSanitizer::sanitize($property, $plainText);

        $this->assertSame($plainText, $result);
    }

    /**
     * Test that empty string is handled correctly on RDFS_LABEL.
     */
    public function testSanitizeEmptyStringOnRdfsLabel(): void
    {
        $property = $this->createPropertyMock(OntologyRdfs::RDFS_LABEL);
        $emptyString = '';

        $result = \helpers_ContentSanitizer::sanitize($property, $emptyString);

        $this->assertSame($emptyString, $result);
    }

    /**
     * Create a mock Property object with the given URI.
     *
     * @param string $uri
     * @return core_kernel_classes_Property|MockObject
     */
    private function createPropertyMock(string $uri): core_kernel_classes_Property
    {
        $property = $this->createMock(core_kernel_classes_Property::class);
        $property->method('getUri')->willReturn($uri);

        return $property;
    }
}
