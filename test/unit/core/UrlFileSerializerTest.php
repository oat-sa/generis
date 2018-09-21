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

use oat\generis\test\TestCase;
use oat\generis\model\fileReference\UrlFileSerializer;
use oat\generis\model\fileReference\FileSerializerException;
use oat\oatbox\filesystem\File;
use oat\oatbox\filesystem\FileSystemService;
use oat\oatbox\filesystem\Directory;

class UrlFileSerializerTest extends TestCase
{
    public function testSerialize()
    {
        $fileP = $this->prophesize(File::class);
        $fileP->getFileSystemId()->willReturn('sampleFs');
        $file = $fileP->reveal();
        $baseDir = $this->prophesize(Directory::class);
        $baseDir->getRelPath($file)->willReturn('sample~Path.txt');
        
        $fsMock = $this->prophesize(FileSystemService::class);
        $fsMock->getDirectory('sampleFs')->willReturn($baseDir->reveal());
        
        $serializer = new UrlFileSerializer();
        $serializer->setServiceLocator($this->getServiceLocatorMock([
            FileSystemService::SERVICE_ID => $fsMock->reveal()
        ]));
        
        $serial = $serializer->serialize($file);
        $this->assertEquals('file://sampleFs/sample%7EPath.txt', $serial);
    }
    
    public function testUnSerialize()
    {
        $file = $this->prophesize(File::class)->reveal();
        $baseDir = $this->prophesize(Directory::class);
        $baseDir->getFile("sample~Path.txt")->willReturn($file);
        
        $fsMock = $this->prophesize(FileSystemService::class);
        $fsMock->getDirectory('sampleFs')->willReturn($baseDir->reveal());
        
        $serializer = new UrlFileSerializer();
        $serializer->setServiceLocator($this->getServiceLocatorMock([
            FileSystemService::SERVICE_ID => $fsMock->reveal()
        ]));
        
        $unserialized = $serializer->unserialize('file://sampleFs/sample%7EPath.txt');
        $this->assertEquals($file, $unserialized);
    }
    
    /**
     * @dataProvider invalidUrlReferenceProvider
     */
    public function testInvalidUrlFile($url)
    {
        $this->expectException(FileSerializerException::class);
        $serializer = new UrlFileSerializer();
        $serializer->unserialize($url);
    }
    
    public function invalidUrlReferenceProvider()
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
	