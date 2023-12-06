<?php

declare(strict_types=1);

namespace oat\generis\migrations;

use common_ext_Extension as Extension;
use common_ext_ExtensionsManager as ExtensionsManager;
use Doctrine\DBAL\Schema\Schema;
use oat\generis\persistence\PersistenceManager;
use oat\oatbox\cache\PropertyCache;
use oat\tao\scripts\tools\migrations\AbstractMigration;

final class Version202312011658572348_generis extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add property cache config.';
    }

    public function up(Schema $schema): void
    {
        try {
            $this->getServiceManager()
                ->get(PersistenceManager::SERVICE_ID)
                ->getPersistenceById('redis');

            $config = new PropertyCache([
                PropertyCache::OPTION_PERSISTENCE => 'redis'
            ]);
        } catch (\Exception $e) {
            $config = new PropertyCache([
                PropertyCache::OPTION_PERSISTENCE => 'cache'
            ]);
        }

        $this->registerService(PropertyCache::SERVICE_ID, $config);
    }

    public function down(
        Schema $schema
    ): void {
        $this->getServiceManager()->unregister(PropertyCache::SERVICE_ID);
    }
}
