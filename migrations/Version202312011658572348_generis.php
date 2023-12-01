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
        $extension = $this->getExtension();

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

        $confKey = 'PropertyCache';

        if (!$extension->hasConfig($confKey)) {
            $extension->setConfig($confKey, $config);
        }
    }

    public function down(
        Schema $schema
    ): void {
        $extension = $this->getExtension();
        $confKey = 'PropertyCache';

        if ($extension->hasConfig($confKey)) {
            $extension->unsetConfig($confKey);
        }
    }

    private function getExtension(): Extension
    {
        return $this->getServiceLocator()
            ->getContainer()
            ->get(ExtensionsManager::SERVICE_ID)
            ->getExtensionById('generis');
    }
}
