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
 * Copyright (c) 2020 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 */

namespace oat\generis\test\unit\common\ext;

use oat\generis\test\TestCase;
use common_ext_Extension as Extension;

class ExtensionTest extends TestCase
{

    /**
     * @runInSeparateProcess
     * @throws \common_ext_ManifestException
     * @throws \common_ext_ManifestNotFoundException
     */
    public function testGetUpdater()
    {
        $dir = __DIR__.DIRECTORY_SEPARATOR.'samples'.DIRECTORY_SEPARATOR;
        $ext = new class ('foo', $dir) extends Extension {
            private $dir;
            public function __construct($id, $dir) {
                parent::__construct($id);
                $this->dir = $dir;
            }
            public function getDir()
            {
                return $this->dir.$this->getId().DIRECTORY_SEPARATOR;
            }
        };
        $ext->setServiceLocator($this->getServiceLocatorMock([]));
        $this->assertInstanceOf(\common_ext_ExtensionUpdater::class, $ext->getUpdater());
    }
}