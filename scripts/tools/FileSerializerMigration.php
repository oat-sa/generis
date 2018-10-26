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
 *      --limit (-l) - The amount of resources that are processed in one batch
 */
class FileSerializerMigration extends ScriptAction
{
    const RESOURCE_LIMIT = 200;

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
     * @return \common_report_Report
     * @throws InvalidServiceManagerException
     * @throws \oat\search\base\exception\SearchGateWayExeption
     * @throws common_Exception
     * @throws common_exception_Error
     */
    protected function run()
    {
        $this->report = Report::createInfo('Starting file serializer migration.');

        $migrationHelper = $this->getMigrationHelper();

        $migrationHelper->migrateFiles(
            $this->hasOption('resourceLimit') ? $this->getOption('resourceLimit') : self::RESOURCE_LIMIT
        );

        $this->addMigrationReport();

        if ($migrationHelper->updateFileSerializer()) {
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
            ],
            'resourceLimit' => [
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
            'Successfully migrated %s old references to %s new file serializer references.',
            $migrationHelper->migrationInformation['migrated_count'],
            $migrationHelper->migrationInformation['old_resource_count']
        )));

        if (count($migrationHelper->failedResources)) {
            $this->report->add(Report::createFailure(sprintf(
                    'Unable to migrate %s resources. Please verify the following resources:',
                    count($migrationHelper->failedResources
                    ))
            ));

            foreach ($migrationHelper->failedResources as $uri => $errorMessages) {
                $this->report->add(Report::createFailure(
                    sprintf("> %s \n    - %s", $uri, implode("\n    - ", $errorMessages))
                ));
            }
        }
    }

    /**
     * Get the file serializer migration helper
     *
     * @return FileSerializerMigrationHelper
     * @throws InvalidServiceManagerException
     */
    private function getMigrationHelper()
    {
        if ($this->migrationHelper === null) {
            $this->migrationHelper = new FileSerializerMigrationHelper($this->isWetRun());
            $this->migrationHelper->setServiceLocator($this->getServiceLocator());
            $this->migrationHelper->setServiceManager($this->getServiceManager());
        }

        return $this->migrationHelper;
    }
}
