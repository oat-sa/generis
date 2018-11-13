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
use Exception;
use oat\generis\Helper\FileSerializerMigrationHelper;
use oat\generis\model\fileReference\FileReferenceSerializer;
use oat\generis\model\fileReference\UrlFileSerializer;
use oat\oatbox\extension\script\ScriptAction;

/**
 * Migrate ResourceFileSerializer references to the new UrlFileSerializer system
 *   [properties]
 *      --wet-run (-w) - Execute the migration
 *      --limit (-l) - The amount of resources that are processed in one batch
 */
class FileSerializerMigration extends ScriptAction
{

    const CHUNK_SIZE = 200;

    /**
     * @var FileSerializerMigrationHelper
     */
    private $migrationHelper;

    /**
     * @var Report
     */
    private $report;

    /**
     * Run the script.
     *
     * @return Report
     * @throws common_Exception
     * @throws common_exception_Error
     */
    protected function run()
    {
        $this->migrationHelper = new FileSerializerMigrationHelper($this->getServiceLocator(), $this->isWetRun());
        $this->report = Report::createInfo('Starting file serializer migration.');

        try {
            $this->migrate();
        } catch (Exception $e) {
            $this->report->add(Report::createFailure(
                sprintf('Migration process was stopped with error message: %s', $e->getMessage())
            ));
            return $this->report;
        }

        if (count($this->migrationHelper->failedResources)) {
            $this->reportMigrationErrors();
        }

        if ($this->updateFileSerializer()) {
            $this->report->add(Report::createSuccess(
                'Successfully updated FileReferenceSerializer service to use UrlFileSerializer service'
            ));
        }

        $this->report->add(Report::createSuccess('Completed file migration process.'));

        if (!$this->isWetRun()) {
            $this->report->add(Report::createFailure('Use the --wet-run (-w) parameter to execute reference migration.'));
        }

        return $this->report;
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
            'chunkSize' => [
                'prefix' => 'c',
                'defaultValue' => self::CHUNK_SIZE,
                'longPrefix' => 'chunk-size',
                'cast' => 'integer',
                'required' => false,
                'description' => 'Specifies items amount for processing (chunk size) per iteration for migration. Default value is '.self::CHUNK_SIZE
            ],
        ];
    }

    /**
     * Show how long it took to run the script.
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
     * @throws \oat\generis\model\fileReference\FileSerializerException
     * @throws \oat\search\base\exception\SearchGateWayExeption
     * @throws common_exception_Error
     */
    private function migrate()
    {
        $oldResourcesData = $this->migrationHelper->getOldResourcesData($this->getOption('chunkSize') ?: self::CHUNK_SIZE);

        foreach ($oldResourcesData as $resourceDocument) {
            $this->migrationHelper->migrateResources($resourceDocument);
        }

        $this->report->add(Report::createSuccess(sprintf(
            'Successfully migrated %s old references to %s new file serializer references.',
            $this->migrationHelper->migrationInformation['old_resource_migration_count'],
            $this->migrationHelper->migrationInformation['migrated_count']
        )));
    }

    /**
     * @throws common_exception_Error
     */
    private function reportMigrationErrors()
    {
        $this->report->add(Report::createFailure(sprintf(
                'Unable to migrate %s resources. Please verify the following resources:',
                count($this->migrationHelper->failedResources
            ))
        ));

        foreach ($this->migrationHelper->failedResources as $uri => $errorMessages) {
            $this->report->add(Report::createFailure(
                sprintf("> %s \n    - %s", $uri, implode("\n    - ", $errorMessages))
            ));
        }
    }

    /**
     * Update the FileReferenceSerializer service to use the UrlFileSerializer.
     *
     * @return bool
     * @throws common_Exception
     */
    private function updateFileSerializer()
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
}
