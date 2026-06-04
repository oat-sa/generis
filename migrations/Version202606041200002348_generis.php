<?php

declare(strict_types=1);

namespace oat\generis\migrations;

use Doctrine\DBAL\Schema\Schema;
use oat\generis\model\log\LogFormatOpenTelemetryUpdater;
use oat\oatbox\log\LoggerService;
use oat\oatbox\service\ServiceNotFoundException;
use oat\tao\scripts\tools\migrations\AbstractMigration;

/**
 * phpcs:disable Squiz.Classes.ValidClassName
 */
final class Version202606041200002348_generis extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add OpenTelemetry traceId/spanId placeholders to TaoLog SingleFileAppender format (GCP stderr).';
    }

    public function up(Schema $schema): void
    {
        $this->migrateLogFormat(static fn (array $options) => (new LogFormatOpenTelemetryUpdater())->apply($options));
    }

    public function down(Schema $schema): void
    {
        $this->migrateLogFormat(static fn (array $options) => (new LogFormatOpenTelemetryUpdater())->revert($options));
    }

    /**
     * @param callable(array<string, mixed>): array<string, mixed> $transform
     */
    private function migrateLogFormat(callable $transform): void
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
