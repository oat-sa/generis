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
 * Copyright (c) 2022 (original work) Open Assessment Technologies SA ;
 */

declare(strict_types=1);

namespace oat\oatbox\cache;

use Exception;
use oat\generis\persistence\PersistenceManager;
use oat\oatbox\service\ConfigurableService;

class SetupFileCache extends ConfigurableService
{
    public const PERSISTENCE = 'cache';

    /**
     * @throws Exception
     */
    public function createDirectory($cachePath): void
    {
        if (is_dir($cachePath) && is_writable($cachePath)) {
            return;
        }

        if (!@mkdir($cachePath, 0700, true)) {
            $error = error_get_last();
            throw new Exception(
                sprintf(
                    'Could not create application cache at "%s". Error message: %s',
                    $cachePath,
                    $error['message'] ?? 'unknown'
                )
            );
        }
    }

    public function createPersistence(): void
    {
        $persistenceManager = $this->getPersistenceManager();
        $persistenceManager->registerPersistence(self::PERSISTENCE, [
            'driver' => 'phpfile'
        ]);
        $persistenceManager->getPersistenceById(self::PERSISTENCE)->purge();
        $this->getServiceManager()->register(PersistenceManager::SERVICE_ID, $persistenceManager);
    }

    private function getPersistenceManager(): PersistenceManager
    {
        return $this->getServiceManager()->get(PersistenceManager::SERVICE_ID);
    }
}
