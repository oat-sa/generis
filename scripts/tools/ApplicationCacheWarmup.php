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
 * Copyright (c) 2023 (original work) Open Assessment Technologies SA;
 */

declare(strict_types=1);

namespace oat\generis\scripts\tools;

use oat\generis\model\data\event\CacheWarmupEvent;
use oat\oatbox\cache\SimpleCache;
use oat\oatbox\event\EventManager;
use oat\oatbox\extension\script\ScriptAction;
use oat\oatbox\reporting\Report;

/**
 * php index.php 'oat\generis\scripts\tools\ApplicationCacheWarmup' --clear
 */
class ApplicationCacheWarmup extends ScriptAction
{
    protected function showTime()
    {
        return true;
    }

    protected function provideUsage(): array
    {
        return [
            'prefix' => 'h',
            'longPrefix' => 'help',
            'description' => 'Warmup TAO Cache',
        ];
    }

    protected function provideOptions(): array
    {
        return [
            'clear' => [
                'prefix' => 'c',
                'longPrefix' => 'clear',
                'flag' => true,
                'description' => 'Clear cache before warm it up.',
                'defaultValue' => false,
            ],
        ];
    }

    protected function provideDescription(): string
    {
        return 'Warmup TAO Cache';
    }

    protected function run(): Report
    {
        $reports = [];

        $clearCache = (bool)$this->getOption('clear');
        if ($clearCache) {
            $this->getServiceLocator()->get(SimpleCache::SERVICE_ID)->clear();
            $reports[] = Report::createInfo('Cache was cleared.');
        }

        try {
            $cacheWarmupEvent = new CacheWarmupEvent();
            /** @var EventManager $eventManager */
            $eventManager = $this->getServiceLocator()->get(EventManager::SERVICE_ID);
            $eventManager->trigger($cacheWarmupEvent);

            $reports = array_merge($reports, $cacheWarmupEvent->getReports());
        } catch (\Throwable $e) {
            return Report::createError(sprintf('Cache warmup failed: %s', $e->getMessage()));
        }

        return Report::createSuccess('TAO cache warmed up!', null, $reports);
    }
}
