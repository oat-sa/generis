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
 * Copyright (c) (original work) 2015 Open Assessment Technologies SA
 *
 */

namespace oat\generis\test\integration\model\persistence\smoothsql;

use core_kernel_persistence_smoothsql_SmoothModel;
use oat\generis\test\GenerisPhpUnitTestRunner;

class SmoothModelTest extends GenerisPhpUnitTestRunner
{
    private core_kernel_persistence_smoothsql_SmoothModel $model;

    /**
     *
     * @see PHPUnit_Framework_TestCase::setUp()
     */
    public function setUp(): void
    {
        GenerisPhpUnitTestRunner::initTest();
        $conf = [
            'persistence' => 'default'
        ];

        $this->model = new core_kernel_persistence_smoothsql_SmoothModel($conf);
    }

    /**
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testGetConfig()
    {
        $this->assertEquals([
            'persistence' => 'default'
        ], $this->model->getOptions());
    }

    /**
     * @author Lionel Lecaque, lionel@taotesting.com
     * @param array $model
     */
    public function testGetRdfInterface()
    {
        $this->assertInstanceOf('core_kernel_persistence_smoothsql_SmoothRdf', $this->model->getRdfInterface());
    }

    /**
     * @author Lionel Lecaque, lionel@taotesting.com
     * @param array $model
     */
    public function testGetRdfsInterface()
    {
        $this->assertInstanceOf('core_kernel_persistence_smoothsql_SmoothRdfs', $this->model->getRdfsInterface());
    }

    /**
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testGetUpdatableModelIds()
    {
        $models = core_kernel_persistence_smoothsql_SmoothModel::getUpdatableModelIds();
        $this->assertContains(1, $models);
    }

    /**
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testGetReadableModelIds()
    {
        $models = core_kernel_persistence_smoothsql_SmoothModel::getReadableModelIds();
        $this->assertContains(1, $models);
    }
}
