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
 * Copyright (c) 2017  (original work) Open Assessment Technologies SA;
 *
 * @author Martijn Swinkels <m.swinkels@taotesting.com>
 */

namespace oat\generis\scripts\tools;

use common_Exception;
use common_exception_Error;
use common_report_Report as Report;
use oat\generis\Helper\FileSerializerMigrationHelper;
use oat\oatbox\extension\script\ScriptAction;
use oat\oatbox\service\exception\InvalidServiceManagerException;

/**
 * Migrate ResourceFileSerializer references to the new UrlFileSerializer system
 *   [properties]
 *      --wet-run (-w) - Execute the migration
 */
class FileSerializerMigration extends ScriptAction
{
    const RESOURCE_LIMIT = 200;

    /**
     * @var bool
     */
    private $isDryRun;

    /**
     * Run the script.
     *
     * @param FileSerializerMigrationHelper|null $migrationHelper
     * @param Report|null $report
     * @param int $resourceOffset
     * @return \common_report_Report
     * @throws InvalidServiceManagerException
     * @throws \oat\search\base\exception\SearchGateWayExeption
     * @throws common_Exception
     * @throws common_exception_Error
     */
    protected function run($migrationHelper = null, $report = null, $resourceOffset = 0)
    {
        if ($migrationHelper === null) {
            $migrationHelper = new FileSerializerMigrationHelper(
                $this->getServiceManager(),
                $this->getServiceLocator(),
                $this->isDryRun()
            );
        }

        if ($report === null) {
            $report = Report::createInfo('Starting file serializer migration.');
        }

        $resourceLimit = $this->hasOption('resourceLimit') ? $this->getOption('resourceLimit') : self::RESOURCE_LIMIT;
        $oldResourcesData = $migrationHelper->getOldResourcesData($resourceLimit, $resourceOffset);
        $oldResourceCount = $migrationHelper->migrationInformation['old_resource_count'];

        $rerun = false;
        if ($oldResourceCount >= $resourceLimit) {
            $rerun = true;
        }

        $report->add(Report::createInfo(
            sprintf('%s old file resources were found', $oldResourceCount)
        ));

        if ($migrationHelper->migrationInformation['old_resource_count'] !== 0) {
            $report->add(Report::createInfo('Starting migration of old file reference resources'));

            $migrationHelper->migrateResources($oldResourcesData);

        } else {
            $report->add(Report::createSuccess('All file resources are using the new file serializer.'));
        }


        if ($rerun) {
            $report->add(Report::createInfo('Batched finished. Processing next batch.'));

            if ($this->isDryRun()) {
                $resourceOffset += $resourceLimit;
            }

            return $this->run($migrationHelper, $report, $resourceOffset);
        }

        $report->add(Report::createSuccess(sprintf(
            'Successfully migrated %s old references to %s new file serializer references.',
            $migrationHelper->migrationInformation['old_resource_migration_count'],
            $migrationHelper->migrationInformation['migrated_count']
        )));

        if ($this->isDryRun()) {
            $report->add(Report::createFailure('Use the --wet-run (-w) parameter to execute reference migration.'));
        }

        if ($migrationHelper->updateFileSerializer()) {
            $report->add(Report::createSuccess(
                'Successfully updated FileReferenceSerializer service to use UrlFileSerializer service'
            ));
        }

        $report->add(Report::createSuccess('Completed file migration process.'));

        return $report;
    }

    /**
     * @inheritdoc
     */
    protected function provideUsage()
    {
        return [
            'prefix' => 'h',
            'longPrefix' => 'help',
            'description' => 'Prints this help dialog'
        ];
    }

    /**
     * @inheritdoc
     */
    protected function provideOptions()
    {
        return [
            'wetRun' => [
                'flag' => true,
                    'prefix' => 'w',
                    'longPrefix' => 'wet-run',
                    'description' => 'Add this flag to migrate the references, as opposed to just listing them (dry/wet run)'
                ],
            'resourceLimit' => [
                'flag' => true,
                    'prefix' => 'l',
                    'longPrefix' => 'limit',
                    'cast' => 'integer',
                    'defaultValue' => self::RESOURCE_LIMIT,
                    'description' => 'How many items should be processed each run?'
                ]
            ];
    }

    /**
     * @inheritdoc
     */
    protected function provideDescription()
    {
        return 'Migration script to migrate Resource file references to URL file references';
    }

    /**
     * Check whether we're running a dry run or not.
     *
     * @return bool
     */
    private function isDryRun()
    {
        if ($this->isDryRun !== null) {
            return $this->isDryRun;
        }

        $this->isDryRun = true;
        if ($this->hasOption('wetRun')) {
            $this->isDryRun = false;
        }

        return $this->isDryRun;
    }
}
