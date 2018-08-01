<?php
/*  
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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */

namespace oat\generis\test\integration;

use oat\generis\test\GenerisPhpUnitTestRunner;

class ConfigurationTest extends GenerisPhpUnitTestRunner
{

    const TESTKEY = 'config_test_key';

    /**
     * A version of php that we can be sure will not be present on the system
     * and that we can use in our test cases as not supposed to be installed
     *
     * @var int
     */
    const UNSUPPORTED_PHP_MAJOR_VERSION = 9;

    protected function setUp()
    {
        GenerisPhpUnitTestRunner::initTest();
    }

    public function testUserConfig()
    {
        $generis = \common_ext_ExtensionsManager::singleton()->getExtensionById('generis');

        $this->assertFalse($generis->getConfig(self::TESTKEY));

        $random = rand(0, 999999);
        $generis->setConfig(self::TESTKEY, $random);
        $this->assertEquals($generis->getConfig(self::TESTKEY), $random);

        $generis->unsetConfig(self::TESTKEY);
        $this->assertFalse($generis->getConfig(self::TESTKEY));
    }
}
