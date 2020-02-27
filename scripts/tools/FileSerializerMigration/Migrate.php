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
 * Copyright (c) 2019 (original work) Open Assessment Technologies SA;
 *
 * @author Martijn Swinkels <m.swinkels@taotesting.com>
 */

namespace oat\generis\scripts\tools\FileSerializerMigration;

use common_Exception;
use common_exception_Error;
use common_report_Report as Report;
use common_report_Report;
use oat\generis\model\fileReference\FileReferenceSerializer;
use oat\generis\model\fileReference\UrlFileSerializer;
use oat\oatbox\extension\script\ScriptAction;
use oat\oatbox\service\exception\InvalidServiceManagerException;

/**
 * Migrate ResourceFileSerializer references to the new UrlFileSerializer system
 *   [properties]
 *      --wet-run (-w) - Execute the migration
 */
class Migrate extends ScriptAction
{
    const MIGRATION_REPORT_LINES = [
        'migration_success' => [
            'dry' => '%s old references will be migrated into %s new file serializer references.',
            'wet' => 'Successfully migrated %s old references to %s new file serializer references.'
        ],
        'migration_errors' => [
            'dry' => '%s resources will fail to migrate. Please verify the following resources:',
            'wet' => 'Unable to migrate %s resources. Please verify the following resources:'
        ],
        'serializer_update_success' => [
            'dry' => 'FileReferenceSerializer service will be updated to use UrlFileSerializer service.',
            'wet' => 'Successfully updated FileReferenceSerializer service to use UrlFileSerializer service.'
        ],
    ];

    /**
     * @var MigrationHelper
     */
    private $migrationHelper;

    /**
     * @var Report
     */
    private $report;

    /**
     * Run the script.
     *
     * @return common_report_Report
     * @throws InvalidServiceManagerException
     * @throws common_Exception
     * @throws common_exception_Error
     */
    protected function run()
    {
        $this->report = Report::createInfo('Starting file serializer migration.');

        $migrationHelper = $this->getMigrationHelper();

        while ($migrationHelper->endReached === false) {
            $migrationHelper->migrateFiles();
        }

        $this->addMigrationReport();

        if ($this->updateFileSerializer()) {
            $this->report->add(Report::createSuccess(
                self::MIGRATION_REPORT_LINES['serializer_update_success'][$this->getMigrationReportKey()]
            ));
        }

        $this->report->add(Report::createSuccess('Completed file migration process.'));

        if (!$this->isWetRun()) {
            $this->report->add(Report::createFailure('Use the --wet-run (-w) parameter to execute reference migration.'));
        }

        return $this->report;
    }

    /**
     * Show script execution time.
     *
     * @return bool
     */
    protected function showTime()
    {
        return true;
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
     * Check whether we're running a wet run or not.
     *
     * @return bool
     */
    private function isWetRun()
    {
        return $this->hasOption('wetRun');
    }

    /**
     * Add the reports related to the migration
     *
     * @throws common_exception_Error
     * @throws InvalidServiceManagerException
     */
    private function addMigrationReport()
    {
        $migrationHelper = $this->getMigrationHelper();
        $this->report->add(Report::createSuccess(sprintf(
            self::MIGRATION_REPORT_LINES['migration_success'][$this->getMigrationReportKey()],
            $migrationHelper->migrationInformation['migrated_count'],
            $migrationHelper->migrationInformation['old_resource_count']
        )));

        if (count($migrationHelper->failedResources)) {
            $this->report->add(Report::createFailure(sprintf(
                self::MIGRATION_REPORT_LINES['migration_errors'][$this->getMigrationReportKey()],
                count($migrationHelper->failedResources)
            )));

            foreach ($migrationHelper->failedResources as $uri => $errorMessages) {
                $this->report->add(Report::createFailure(
                    sprintf("> %s \n    - %s", $uri, implode("\n    - ", $errorMessages))
                ));
            }
        }
    }

    /**
     * Update the FileReferenceSerializer service to use the UrlFileSerializer.
     *
     * @return bool
     * @throws common_Exception
     */
    public function updateFileSerializer()
    {
        $updated = false;
        if ($this->fileSerializerNeedsUpdate()) {
            if ($this->isWetRun()) {
                $this->getServiceManager()->register(FileReferenceSerializer::SERVICE_ID, new UrlFileSerializer());
            }
            $updated = true;
        }

        return $updated;
    }

    /**
     * Check if the file serializer service needs to be updated
     *
     * @return bool
     */
    private function fileSerializerNeedsUpdate()
    {
        $needsUpdate = true;
        $currentFileReferenceSerializer = $this->getServiceLocator()->get(FileReferenceSerializer::SERVICE_ID);
        if ($currentFileReferenceSerializer instanceof UrlFileSerializer) {
            $needsUpdate = false;
        }

        return $needsUpdate;
    }

    /**
     * Get the file serializer migration helper
     *
     * @return MigrationHelper
     */
    private function getMigrationHelper()
    {
        if ($this->migrationHelper === null) {
            $this->migrationHelper = new MigrationHelper($this->isWetRun());
            $this->migrationHelper->setServiceLocator($this->getServiceLocator());
        }

        return $this->migrationHelper;
    }

    /**
     * Retrieve key used to differentiate between reporting for the wet and dry run.
     *
     * @see self::MIGRATION_REPORT_LINES
     * @return string
     */
    private function getMigrationReportKey()
    {
        return $this->isWetRun() ? 'wet' : 'dry';
    }
}
