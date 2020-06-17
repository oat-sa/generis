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
 * Copyright (c) 2020 (original work) Open Assessment Technologies SA;
 *
 */

namespace oat\generis\test;

use League\Flysystem\Memory\MemoryAdapter;
use oat\oatbox\filesystem\FileSystemService;

trait FileSystemMockTrait
{
    protected function getFileSystemMock($dirs = []): FileSystemService
    {
        $adapterparam = [
            'testfs' => [
                'class' => MemoryAdapter::class
            ]
        ];
        $dirparam = [];
        foreach ($dirs as $dir) {
            $dirparam[$dir] = 'testfs';
        }
        $service = new FileSystemService([
            FileSystemService::OPTION_ADAPTERS => $adapterparam,
            FileSystemService::OPTION_DIRECTORIES => $dirparam
        ]);
        $service->setServiceLocator($this->getServiceLocatorMock([
            FileSystemService::SERVICE_ID => $service
        ]));
        return $service;
    }
}
