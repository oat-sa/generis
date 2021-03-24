<?php

declare(strict_types=1);

namespace oat\generis\migrations;

use Doctrine\DBAL\Schema\Schema;
use oat\oatbox\event\EventAggregator;
use oat\tao\scripts\tools\migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version202008201138552348_generis extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'New EventAggregator Service for extension generis.';
    }

    public function up(Schema $schema): void
    {
        $eventAggregator = new EventAggregator(
          [
              'numberOfAggregatedEvents' => 10
          ]
        );

        $this->getServiceLocator()->register(
            EventAggregator::SERVICE_ID,
            $eventAggregator
        );
    }

    public function down(Schema $schema): void
    {
        $this->getServiceLocator()->unregister(EventAggregator::SERVICE_ID);
    }
}
