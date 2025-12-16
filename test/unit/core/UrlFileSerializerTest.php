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
 * Copyright (c) 2018 (original work) Open Assessment Technologies SA;
 *
 */

namespace oat\generis\test\unit\model;

use oat\generis\test\ServiceManagerMockTrait;
use PHPUnit\Framework\TestCase;
use oat\generis\model\fileReference\UrlFileSerializer;
use oat\generis\model\fileReference\FileSerializerException;
use oat\oatbox\filesystem\File;
use oat\oatbox\filesystem\FileSystemService;
use oat\oatbox\filesystem\Directory;

class UrlFileSerializerTest extends TestCase
{
    use ServiceManagerMockTrait;

    public function testSerialize(): void
    {
        $file = $this->createMock(File::class);
        $file
            ->method('getFileSystemId')
            ->willReturn('sampleFs');

        $baseDir = $this->createMock(Directory::class);
        $baseDir
            ->method('getRelPath')
            ->with($file)
            ->willReturn('sample~Path.txt');

        $fsMock = $this->createMock(FileSystemService::class);
        $fsMock
            ->method('getDirectory')
            ->with('sampleFs')
            ->willReturn($baseDir);

        $serializer = new UrlFileSerializer();
        $serializer->setServiceLocator($this->getServiceManagerMock([
            FileSystemService::SERVICE_ID => $fsMock
        ]));

        $serial = $serializer->serialize($file);
        $this->assertEquals('file://sampleFs/sample%7EPath.txt', $serial);
    }

    public function testUnSerialize(): void
    {
        $file = $this->createMock(File::class);

        $baseDir = $this->createMock(Directory::class);
        $baseDir
            ->method('getFile')
            ->with('sample~Path.txt')
            ->willReturn($file);

        $fsMock = $this->createMock(FileSystemService::class);
        $fsMock
            ->method('getDirectory')
            ->with('sampleFs')
            ->willReturn($baseDir);

        $serializer = new UrlFileSerializer();
        $serializer->setServiceLocator($this->getServiceManagerMock([
            FileSystemService::SERVICE_ID => $fsMock
        ]));

        $unserialized = $serializer->unserialize('file://sampleFs/sample%7EPath.txt');
        $this->assertEquals($file, $unserialized);
    }

    /**
     * @dataProvider invalidUrlReferenceProvider
     */
    public function testInvalidUrlFile($url): void
    {
        $this->expectException(FileSerializerException::class);
        $serializer = new UrlFileSerializer();
        $serializer->unserialize($url);
    }

    public function invalidUrlReferenceProvider(): array
    {
        return [
            ['file://aaa'],
            ['cloudy://default/lalala'],
            ['randomstuff'],
            [null],
            [[]],
            [1]
        ];
    }
}
