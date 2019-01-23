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
 * Copyright (c) 2019 (original work) Open Assessment Technologies SA;
 */

namespace oat\generis\test\unit\common\configuration;

use common_configuration_FileSystemComponent;
use common_configuration_Report;
use oat\generis\test\TestCase;
use org\bovigo\vfs\vfsStream;

/**
 * Test the \common_configuration_FileSystemComponent class
 *
 * @author Jonathan VUILLEMIN <jonathan@taotesting.com>
 */
class FileSystemComponentTest extends TestCase
{
    /** @var common_configuration_FileSystemComponent */
    private $subject;

    protected function setUp()
    {
        parent::setUp();

        $this->subject = new common_configuration_FileSystemComponent(
            '/path',
            'rw',
            false,
            false,
            false
        );
    }

    public function testGetters()
    {
        $this->assertEquals('/path', $this->subject->getLocation());
        $this->assertEquals('rw', $this->subject->getExpectedRights());
        $this->assertFalse($this->subject->isOptional());
        $this->assertFalse($this->subject->getRecursive());
        $this->assertFalse($this->subject->getMustCheckIfEmpty());
    }

    public function testSetters()
    {
        $this->subject->setLocation('/otherPath');
        $this->subject->setExpectedRights('r');
        $this->subject->setOptional(true);
        $this->subject->setRecursive(true);
        $this->subject->setMustCheckIfEmpty(true);

        $this->assertEquals('/otherPath', $this->subject->getLocation());
        $this->assertEquals('r', $this->subject->getExpectedRights());
        $this->assertTrue($this->subject->isOptional());
        $this->assertTrue($this->subject->getRecursive());
        $this->assertTrue($this->subject->getMustCheckIfEmpty());
    }

    public function testIsReadable()
    {
        vfsStream::setup('testDir1', 0777);
        $this->assertTrue($this->subject->isReadable(vfsStream::url('testDir1')));

        vfsStream::setup('testDir2', 0333);
        $this->assertFalse($this->subject->isWritable(vfsStream::url('testDir2')));
    }

    public function testIsWritable()
    {
        vfsStream::setup('testDir3', 0777);
        $this->assertTrue($this->subject->isWritable(vfsStream::url('testDir3')));

        vfsStream::setup('testDir4', 0555);
        $this->assertFalse($this->subject->isWritable(vfsStream::url('testDir4')));
    }

    public function testIsExecutable()
    {
        vfsStream::setup('testDir5', 0777);
        file_put_contents(vfsStream::url('testDir5/test.txt'), 'data');
        chmod(vfsStream::url('testDir5/test.txt'), 0777);
        $this->assertTrue($this->subject->isExecutable(vfsStream::url('testDir5/test.txt')));

        vfsStream::setup('testDir6', 0777);
        file_put_contents(vfsStream::url('testDir6/test.txt'), 'data');
        chmod(vfsStream::url('testDir6/test.txt'), 0666);
        $this->assertFalse($this->subject->isExecutable(vfsStream::url('testDir6/test.txt')));
    }

    public function testCheckDirectoryPermissionsSuccess()
    {
        vfsStream::setup('testDir7', 0777);
        $this->subject->setLocation(vfsStream::url('testDir7'));
        $this->subject->setExpectedRights('rw');

        $output = $this->subject->check();

        $this->assertEquals(common_configuration_Report::VALID, $output->getStatus());
        $this->assertEquals(
            "File system component 'tao.configuration.filesystem' in 'vfs://testDir7 is compliant with expected rights (rw).'",
            $output->getMessage()
        );
    }

    public function testCheckFilePermissionsSuccess()
    {
        vfsStream::setup('testDir8', 0777);
        file_put_contents(vfsStream::url('testDir8/test.txt'), 'data');
        chmod(vfsStream::url('testDir8/test.txt'), 0777);
        $this->subject->setLocation(vfsStream::url('testDir8/test.txt'));
        $this->subject->setExpectedRights('rwx');

        $output = $this->subject->check();

        $this->assertEquals(common_configuration_Report::VALID, $output->getStatus());
        $this->assertEquals(
            "File system component 'tao.configuration.filesystem' in 'vfs://testDir8/test.txt is compliant with expected rights (rwx).'",
            $output->getMessage()
        );
    }

    public function testCheckDirectoryContentFailure()
    {
        vfsStream::setup('testDir9', 0777);
        file_put_contents(vfsStream::url('testDir9/test.txt'), 'data');
        chmod(vfsStream::url('testDir9/test.txt'), 0777);
        $this->subject->setLocation(vfsStream::url('testDir9'));
        $this->subject->setExpectedRights('rw');
        $this->subject->setMustCheckIfEmpty(true);

        $output = $this->subject->check();

        $this->assertEquals(common_configuration_Report::INVALID, $output->getStatus());
        $this->assertEquals(
            "File system component 'tao.configuration.filesystem' in 'vfs://testDir9 is not empty.",
            $output->getMessage()
        );
    }

    public function testCheckDirectoryContentSuccess()
    {
        vfsStream::setup('testDir10', 0777);
        $this->subject->setLocation(vfsStream::url('testDir10'));
        $this->subject->setExpectedRights('rw');
        $this->subject->setMustCheckIfEmpty(true);

        $output = $this->subject->check();

        $this->assertEquals(common_configuration_Report::VALID, $output->getStatus());
        $this->assertEquals(
            "File system component 'tao.configuration.filesystem' in 'vfs://testDir10 is compliant with expected rights (rw).'",
            $output->getMessage()
        );
    }
}
