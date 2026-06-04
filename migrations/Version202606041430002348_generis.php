<?php

declare(strict_types=1);

namespace oat\generis\migrations;

use Doctrine\DBAL\Schema\Schema;
use oat\generis\model\log\LogSingleFileAppenderToJsonUpdater;
use oat\oatbox\log\LoggerService;
use oat\oatbox\service\ServiceNotFoundException;
use oat\tao\scripts\tools\migrations\AbstractMigration;

/**
 * phpcs:disable Squiz.Classes.ValidClassName
 */
final class Version202606041430002348_generis extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Migrate TaoLog SingleFileAppender text logs to TaoMonolog JSON on stderr (trace_id/span_id fields).';
    }

    public function up(Schema $schema): void
    {
        $this->migrateLogConfig(static fn (array $options) => (new LogSingleFileAppenderToJsonUpdater())->apply($options));
    }

    public function down(Schema $schema): void
    {
        $this->migrateLogConfig(static fn (array $options) => (new LogSingleFileAppenderToJsonUpdater())->revert($options));
    }

    /**
     * @param callable(array<string, mixed>): array<string, mixed> $transform
     */
    private function migrateLogConfig(callable $transform): void
    {
        $serviceLocator = $this->getServiceLocator();

        if (!$serviceLocator->has(LoggerService::SERVICE_ID)) {
            return;
        }

        try {
            $loggerService = $serviceLocator->get(LoggerService::SERVICE_ID);
        } catch (ServiceNotFoundException) {
            return;
        }

        if (!$loggerService instanceof LoggerService) {
            return;
        }

        $options = $loggerService->getOptions();
        $updatedOptions = $transform($options);

        if ($updatedOptions === $options) {
            return;
        }

        $loggerService->setOptions($updatedOptions);
        $this->registerService(LoggerService::SERVICE_ID, $loggerService);
    }
}
