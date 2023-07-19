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
 * Copyright (c) 2020  (original work) Open Assessment Technologies SA;
 */

declare(strict_types=1);

namespace oat\generis\migrations;

use Doctrine\DBAL\Schema\Schema;
use oat\generis\persistence\DriverConfigurationFeeder;
use oat\tao\scripts\tools\migrations\AbstractMigration;

final class Version202009301828472348_generis extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Register DriverConfigurationFeeder';
    }

    public function up(Schema $schema): void
    {
        $this->getServiceLocator()->register(
            DriverConfigurationFeeder::SERVICE_ID,
            new DriverConfigurationFeeder(
                [
                    DriverConfigurationFeeder::OPTION_DRIVER_OPTIONS => [],
                ]
            )
        );
    }

    public function down(Schema $schema): void
    {
        $this->getServiceLocator()->unregister(DriverConfigurationFeeder::SERVICE_ID);
    }
}
