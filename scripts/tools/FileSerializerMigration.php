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
 * @author Alexander Zagovorichev <zagovorichev@1pt.com>
 */

namespace oat\generis\scripts\tools;

use common_exception_Error;
use common_persistence_Manager as PersistanceManager;
use common_report_Report as Report;
use core_kernel_classes_Resource;
use oat\generis\model\fileReference\FileSerializerException;
use oat\generis\model\fileReference\ResourceFileSerializer;
use oat\generis\model\fileReference\UrlFileSerializer;
use oat\generis\model\GenerisRdf;
use oat\generis\model\kernel\persistence\smoothsql\search\ComplexSearchService;
use oat\generis\model\OntologyAwareTrait;
use oat\oatbox\extension\script\ScriptAction;
use oat\oatbox\filesystem\Directory;
use oat\oatbox\filesystem\File;
use oat\oatbox\service\exception\InvalidServiceManagerException;
use oat\search\base\exception\SearchGateWayExeption;
use taoItems_models_classes_ItemsService;
use taoTests_models_classes_TestsService;


/**
 * Migrate ResourceFileSerializer references to the new UrlFileSerializer system
 *   [properties]
 *      -- fix - Execute the migration
 */
class FileSerializerMigration extends ScriptAction
{

    use OntologyAwareTrait;

    /**
     * @var UrlFileSerializer
     */
    private $urlFileSerializer;

    /**
     * @var ResourceFileSerializer
     */
    private $resourceFileSerializer;

    /**
     * @var PersistanceManager
     */
    private $persistence;

    /**
     * @var Report
     */
    private $report;

    /**
     * @var ComplexSearchService
     */
    private $search;

    /**
     * @var int
     */
    private $oldResourceCount = 0;

    /**
     * @var int
     */
    private $migratedCount = 0;

    /**
     * @var string[]
     */
    protected $itemClasses = [
        taoItems_models_classes_ItemsService::PROPERTY_ITEM_CONTENT,
        taoTests_models_classes_TestsService::PROPERTY_TEST_CONTENT
    ];



    /**
     * FileSerializerMigration constructor.
     */
    public function __construct()
    {
        $this->urlFileSerializer = new UrlFileSerializer();
        $this->resourceFileSerializer = new ResourceFileSerializer();
    }

    /**
     * Run the script.
     *
     * @return \common_report_Report
     * @throws common_exception_Error
     */
    protected function run()
    {
        $dryRun = true;
        if ($this->hasOption('wetRun')) {
            $dryRun = false;
        }

        $this->report = Report::createInfo('Starting file serializer migration.');

        $oldItemResourceGroups = $this->getOldResourceGroups();

        $this->report->add(Report::createInfo(
            sprintf('%s File resource references were found', $this->oldResourceCount)
        ));

        if ($dryRun) {
            $this->report->add(Report::createFailure('Use the --fix parameter to migrate the references.'));
            return $this->report;
        }

        return $this->wetRun($oldItemResourceGroups);
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
            'itemClasses' => [
                'prefix' => 'i',
                'longPrefix' => 'itemClasses',
                'description' => 'A list of additional item classes to migrate, seperated by spaces.'
            ],
            'wetRun' => [
                'flag' => true,
                    'prefix' => 'f',
                    'longPrefix' => 'fix',
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
     * Migrate ResourceFileSerializer resources to UrlFileSerializer resources
     *
     * @param core_kernel_classes_Resource[][] $oldItemResourceGroups
     * @return bool
     * @throws common_exception_Error
     */
    private function migrateResourceGroups(array $oldItemResourceGroups)
    {
        $serviceLocator = $this->getServiceLocator();
        $this->urlFileSerializer->setServiceLocator($serviceLocator);
        $this->resourceFileSerializer->setServiceLocator($serviceLocator);

        // Reset counter for migration count.
        $this->oldResourceCount = 0;

        foreach ($oldItemResourceGroups as $itemClass => $oldItemResources) {
            $this->report->add(Report::createInfo(sprintf('Migrating files in class %s', $itemClass)));
            foreach ($oldItemResources as $oldItem) {
                $this->migrateResource($itemClass, $oldItem);
            }
        }

        $this->report->add(Report::createSuccess('Migration of old file references completed'));

        return true;
    }

    /**
     * Get the search service
     * @throws \oat\oatbox\service\exception\InvalidServiceManagerException
     */
    private function getSearch()
    {
        if ($this->search !== null) {
            return $this->search;
        }

        $this->search = $this->getServiceManager()->get(ComplexSearchService::SERVICE_ID);
        return $this->search;
    }

    /**
     * Gather the item classes that should be migrated.
     *
     * @return string[]
     */
    private function getItemClasses()
    {
        if ($this->hasOption('itemClasses')) {
            $this->itemClasses = array_merge($this->itemClasses, explode(' ', $this->getOption('itemClasses')));
        }

        return $this->itemClasses;
    }

    /**
     * Get the resources that should be migrated.
     *
     * @return core_kernel_classes_Resource[][]
     * @throws common_exception_Error
     */
    private function getOldResourceGroups()
    {
        $itemClasses = $this->getItemClasses();

        try {
            $search = $this->getSearch();
        } catch (InvalidServiceManagerException $e) {
            $this->report->add(Report::createFailure('Unable to get the search service.'));
            return [];
        }

        $records = [];
        foreach ($itemClasses as $itemClass) {
            $records[$itemClass] = $this->getOldResources($itemClass, $search);
        }

        return $records;
    }

    /**
     * Migrate a single resource.
     *
     * @param $itemClass
     * @param core_kernel_classes_Resource $oldItem
     * @throws common_exception_Error
     */
    private function migrateResource($itemClass, $oldItem)
    {
        $property = $oldItem->getProperty($itemClass);
        $oldValues = $oldItem->getPropertyValues($property);
        $oldValue = reset($oldValues);

        if (strpos($oldValue, 'dir://') !== false || strpos($oldValue, 'file://') !== false) {
            return;
        }

        $oldResource = $this->getResource($oldValue);
        $this->oldResourceCount++;

        try {
            /** @var Directory|File $unserializedFileResource */
            $unserializedFileResource = $this->resourceFileSerializer->unserialize($oldResource);
            $migratedValue = $this->urlFileSerializer->serialize($unserializedFileResource);
            $oldItem->editPropertyValues($property, $migratedValue);
            $oldResource->delete();
            $this->migratedCount++;
        } catch (FileSerializerException $e) {
            $this->report->add(Report::createFailure(
                sprintf('Unable to serialize file resource "%s"', $unserializedFileResource->getBasename())
            ));
        }
    }

    /**
     * Continue script execution with actual migration step.
     *
     * @param core_kernel_classes_Resource[][] $oldItemResourceGroups
     * @return Report
     * @throws common_exception_Error
     */
    private function wetRun($oldItemResourceGroups)
    {
        $this->report->add(Report::createInfo('Starting migration of old file reference resources'));

        if ($this->migrateResourceGroups($oldItemResourceGroups) === false) {
            $this->report->add(Report::createFailure('Stopping execution.'));
            return $this->report;
        }

        if ($this->oldResourceCount === 0) {
            $this->report->add(Report::createSuccess('All file resources are using the new file serializer.'));
            return $this->report;
        }

        $this->report->add(Report::createSuccess(sprintf(
            'Successfully migrated %s old references to %s new file serializer references.',
            $this->oldResourceCount,
            $this->migratedCount
        )));

        return $this->report;
    }

    /**
     * Get the old resources, based on item class.
     *
     * @param string $itemClass
     * @param ComplexSearchService $search
     * @return core_kernel_classes_Resource[]
     * @throws common_exception_Error
     */
    private function getOldResources($itemClass, $search)
    {
        $records = [];
        $queryBuilder = $search->query();
        $query = $queryBuilder->newQuery();
        $query->add($itemClass)->notNull();
        $queryBuilder->setCriteria($query);
        try {
            $results = $search->getGateway()->search($queryBuilder);
            $this->oldResourceCount += $results->total();
            foreach ($results as $result) {
                $records[] = $result;
            }
        } catch (SearchGateWayExeption $e) {
            $this->report->add(Report::createFailure(
                sprintf('Unable to execute search. %s', $e->getMessage())
            ));
            return [];
        }

        return $records;
    }
}
