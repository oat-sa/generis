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
 * Copyright (c) 2020-2021 (original work) Open Assessment Technologies SA;
 */

declare(strict_types=1);

use oat\generis\model\kernel\persistence\starsql\StarSqlServiceProvider;
use oat\generis\persistence\PersistenceServiceProvider;
use oat\generis\scripts\update\Updater;
use oat\generis\scripts\install\TaskQueue;
use oat\generis\scripts\install\RegisterServices;
use oat\oatbox\log\ServiceProvider\LogServiceProvider;
use oat\generis\model\resource\ResourceServiceProvider;
use oat\generis\scripts\install\SetupDefaultKvPersistence;
use oat\generis\model\DependencyInjection\ContainerServiceProvider;

/**
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @license GPLv2 http://www.opensource.org/licenses/gpl-2.0.php
 */
return [
    'name' => 'generis',
    'label' => 'Generis Core',
    'description' => 'Core extension, provide the low level framework and an API to manage ontologies',
    'license' => 'GPL-2.0',
    'author' => 'Open Assessment Technologies, CRP Henri Tudor',
    'install' => [
        'rdf' => [
            __DIR__ . '/core/ontology/22-rdf-syntax-ns.rdf',
            __DIR__ . '/core/ontology/rdf-schema.rdf',
            __DIR__ . '/core/ontology/widgetdefinitions.rdf',
            __DIR__ . '/core/ontology/rules.rdf',
            __DIR__ . '/core/ontology/generis.rdf',
            __DIR__ . '/core/ontology/taskqueue.rdf',
        ],
        'checks' => [],
        'php' => [
            TaskQueue::class,
            SetupDefaultKvPersistence::class,
            RegisterServices::class,
        ],
    ],
    'update' => Updater::class,
    'containerServiceProviders' => [
        ContainerServiceProvider::class,
        LogServiceProvider::class,
        ResourceServiceProvider::class,
        PersistenceServiceProvider::class,
        StarSqlServiceProvider::class,
    ],
];
