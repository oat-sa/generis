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
 * Copyright (c) 2021 (original work) Open Assessment Technologies SA;
 */

declare(strict_types=1);

namespace oat\generis\migrations;

use Doctrine\DBAL\Schema\Schema;
use oat\generis\model\DependencyInjection\ServiceOptions;
use oat\oatbox\reporting\Report;
use oat\oatbox\user\UserLanguageService;
use oat\tao\scripts\tools\migrations\AbstractMigration;
use common_ext_Extension;

final class Version202112131529847730_generis extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Register ' . ServiceOptions::class;
    }

    public function up(Schema $schema): void
    {
        $extension = $this->getGenerisExtension();
        $langService = $extension->getConfig('UserLanguageService');

        $langService->setOption(UserLanguageService::OPTION_DEFAULT_LANGUAGE, 'en-US');
        $extension->setConfig('UserLanguageService', $langService);

        $this->reportCompletion('Setting default_language');
    }

    public function down(Schema $schema): void
    {
        $extension = $this->getGenerisExtension();
        $langService = $extension->getConfig('UserLanguageService');

        $langService->setOption(UserLanguageService::OPTION_DEFAULT_LANGUAGE, null);
        $extension->setConfig('UserLanguageService', $langService);

        $this->reportCompletion('Unsetting default_language');
    }

    private function reportCompletion(string $message): void
    {
        $this->addReport(
            Report::createSuccess(
                "$message. Configuration of generis (UserLanguageService.conf) was successfully updated"
            )
        );
    }

    private function getGenerisExtension(): common_ext_Extension
    {
        $extensionManager = $this->getServiceLocator()->get(\common_ext_ExtensionsManager::SERVICE_ID);

        return $extensionManager->getExtensionById('generis');
    }
}
