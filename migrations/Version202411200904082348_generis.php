<?php

declare(strict_types=1);

namespace oat\generis\migrations;

use Doctrine\DBAL\Schema\Schema;
use oat\tao\scripts\tools\migrations\AbstractMigration;
use oat\tao\scripts\update\OntologyUpdater;

/**
 * phpcs:disable Squiz.Classes.ValidClassName
 */
final class Version202411200904082348_generis extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Sync models';
    }

    public function up(Schema $schema): void
    {
        OntologyUpdater::syncModels();
    }

    public function down(Schema $schema): void
    {
        OntologyUpdater::syncModels();
    }
}
