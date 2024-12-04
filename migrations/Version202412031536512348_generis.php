<?php

declare(strict_types=1);

namespace oat\generis\migrations;

use Doctrine\DBAL\Schema\Schema;
use oat\oatbox\filesystem\FileSystemService;
use oat\tao\scripts\tools\migrations\AbstractMigration;

final class Version202412031536512348_generis extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Update filesystem configuration';
    }

    public function up(Schema $schema): void
    {
        $filesystemService = $this->getServiceLocator()->get(FileSystemService::SERVICE_ID);
        $config = $updatedConfig = $filesystemService->getOption(FileSystemService::OPTION_ADAPTERS);
        foreach ($config as $adapterId => $adapterConfig) {
            if ($adapterConfig['class'] === 'Local'
                || $adapterConfig['class'] === 'League\\Flysystem\\Local\\LocalFilesystemAdapter'
            ) {
                if (!empty($adapterConfig['options']['root'])) {
                    $updatedConfig[$adapterId]['options']['location'] = $adapterConfig['options']['root'];
                    unset($updatedConfig[$adapterId]['options']['root']);
                }
            } elseif ($adapterConfig['class'] === 'League\\Flysystem\\Memory\\MemoryAdapter') {
                $updatedConfig[$adapterId]['class'] = 'League\\Flysystem\\InMemory\\InMemoryFilesystemAdapter';
            }
        }

        $filesystemService->setOption(FileSystemService::OPTION_ADAPTERS, $updatedConfig);
        $this->registerService(FileSystemService::SERVICE_ID, $filesystemService);
    }

    public function down(Schema $schema): void
    {
        $filesystemService = $this->getServiceLocator()->get(FileSystemService::SERVICE_ID);
        $config = $updatedConfig = $filesystemService->getOption(FileSystemService::OPTION_ADAPTERS);
        foreach ($config as $adapterId => $adapterConfig) {
            if ($adapterConfig['class'] === 'Local'
                || $adapterConfig['class'] === 'League\\Flysystem\\Local\\LocalFilesystemAdapter'
            ) {
                if (!empty($adapterConfig['options']['location'])) {
                    $updatedConfig[$adapterId]['options']['root'] = $adapterConfig['options']['location'];
                    unset($updatedConfig[$adapterId]['options']['location']);
                }
            } elseif ($adapterConfig['class'] === 'League\\Flysystem\\InMemory\\InMemoryFilesystemAdapter') {
                $updatedConfig[$adapterId]['class'] = 'League\\Flysystem\\Memory\\MemoryAdapter';
            }
        }

        $filesystemService->setOption(FileSystemService::OPTION_ADAPTERS, $updatedConfig);
        $this->registerService(FileSystemService::SERVICE_ID, $filesystemService);
    }
}
