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

use common_exception_Error;
use common_report_Report as Report;
use core_kernel_classes_Resource;
use oat\generis\model\fileReference\FileReferenceSerializer;
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

/**
 * Migrate ResourceFileSerializer references to the new UrlFileSerializer system
 *   [properties]
 *      --wet-run (-w) - Execute the migration
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
     * @var Report
     */
    private $report;

    /**
     * @var ComplexSearchService
     */
    private $search;

    /**
     * @var bool
     */
    private $isDryRun;

    /**
     * @var int
     */
    private $oldResourceCount = 0;

    /**
     * @var int
     */
    private $migratedCount = 0;

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
     * @throws InvalidServiceManagerException
     * @throws \oat\search\base\exception\SearchGateWayExeption
     * @throws common_exception_Error
     */
    protected function run()
    {
        $this->report = Report::createInfo('Starting file serializer migration.');

        $oldResourcesData = $this->getOldResourcesData();
        $this->oldResourceCount = count($oldResourcesData);

        $this->report->add(Report::createInfo(sprintf('%s old file resources were found', $this->oldResourceCount)));

        if ($this->oldResourceCount !== 0) {
            $this->report->add(Report::createInfo('Starting migration of old file reference resources'));
            $this->migrateResources($oldResourcesData);
            $this->report->add(Report::createSuccess(sprintf(
                'Successfully migrated %s old references to %s new file serializer references.',
                $this->oldResourceCount,
                $this->migratedCount
            )));
        } else {
            $this->report->add(Report::createSuccess('All file resources are already using the new file serializer.'));
        }


        if ($this->isDryRun()) {
            $this->report->add(Report::createFailure('Use the --wet-run (-w) parameter to execute reference migration.'));
            return $this->report;
        }

        $this->updateFileReferenceSerializer();

        $this->report->add(Report::createSuccess('Completed file migration process.'));

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
     * @param mixed[][] $oldResourcesData
     * @return void
     * @throws InvalidServiceManagerException
     * @throws \oat\search\base\exception\SearchGateWayExeption
     * @throws common_exception_Error
     */
    private function migrateResources(array $oldResourcesData)
    {
        $this->oldResourceCount = 0;
        $serviceLocator = $this->getServiceLocator();
        $this->urlFileSerializer->setServiceLocator($serviceLocator);
        $this->resourceFileSerializer->setServiceLocator($serviceLocator);

        foreach ($oldResourcesData as $oldResourceData) {
            foreach ($oldResourceData['properties'] as $data) {
                $this->migrateResource($oldResourceData['resource'], $data['predicate']);
            }
        }

        $this->report->add(Report::createSuccess('Migration of old file references completed'));
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
     * Get the resources that should be migrated.
     *
     * @return mixed[][]
     */
    private function getOldResourcesData()
    {
        $oldResources = [];
        $fileResources = $this->getClass(GenerisRdf::CLASS_GENERIS_FILE)->getInstances(true);

        foreach ($fileResources as $fileResource) {
            $oldResources[$fileResource->getUri()]['properties'] = $this->getPropertiesForResource($fileResource);
            $oldResources[$fileResource->getUri()]['resource'] = $fileResource;
        }

        return $oldResources;
    }

    /**
     * Migrate a single resource.
     *
     * @param core_kernel_classes_Resource $oldResource
     * @param string $predicateUri
     * @return void
     * @throws InvalidServiceManagerException
     * @throws \oat\search\base\exception\SearchGateWayExeption
     * @throws common_exception_Error
     */
    private function migrateResource($oldResource, $predicateUri)
    {
        $property = $this->getProperty($predicateUri);

        $resource = $this->getParentResource($oldResource, $predicateUri);

        if ($resource === null) {
            return;
        }

        try {
            /** @var Directory|File $unserializedFileResource */
            $unserializedFileResource = $this->resourceFileSerializer->unserialize($oldResource);
            $migratedValue = $this->urlFileSerializer->serialize($unserializedFileResource);
            if (!$this->isDryRun()) {
                $resource->editPropertyValues($property, $migratedValue);
                $oldResource->delete();
            }
            $this->migratedCount++;
        } catch (FileSerializerException $e) {
            $this->report->add(Report::createFailure(
                sprintf('Unable to serialize file resource "%s"', $unserializedFileResource->getBasename())
            ));
        }
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

    /**
     * Update the FileReferenceSerializer service to use the UrlFileSerializer.
     *
     * @throws common_exception_Error
     * @throws InvalidServiceManagerException
     * @return void
     */
    private function updateFileReferenceSerializer()
    {
        $serviceManager = $this->getServiceManager();
        $currentFileReferenceSerializer = $serviceManager->get(FileReferenceSerializer::SERVICE_ID);

        if ($currentFileReferenceSerializer instanceof UrlFileSerializer) {
            $this->report->add(Report::createInfo('System is already using the UrlFileSerializer'));
            return;
        }

        if (!$this->isDryRun()) {
            try {
                $serviceManager->register(FileReferenceSerializer::SERVICE_ID, new UrlFileSerializer());
            } catch (InvalidServiceManagerException $e) {
                $this->report->add(Report::createFailure($e->getMessage()));
            } catch (\common_Exception $e) {
                $this->report->add(Report::createFailure('Unable to update FileReferenceSerializer service to UrlFileSerializer service'));
            }
        }

        $this->report->add(Report::createSuccess('Successfully updated FileReferenceSerializer service to use UrlFileSerializer service'));
    }

    /**
     * Get the properties that are using this resource
     *
     * @param core_kernel_classes_Resource $fileResource
     * @return string[][]
     */
    private function getPropertiesForResource($fileResource)
    {
        $sql = "SELECT predicate FROM statements WHERE object = '" . $fileResource->getUri() . "'";
        $persistence = $fileResource->getModel()->getPersistence();
        return $persistence->query($sql)->fetchAll();
    }

    /**
     * @param $oldResource
     * @param $predicateUri
     * @return core_kernel_classes_Resource|null
     * @throws InvalidServiceManagerException
     * @throws \oat\search\base\exception\SearchGateWayExeption
     */
    private function getParentResource($oldResource, $predicateUri)
    {
        $search = $this->getSearch();
        $queryBuilder = $search->query();
        $query = $queryBuilder->newQuery();
        $criteria = $query->add($predicateUri)->equals($oldResource->getUri());
        $queryBuilder = $queryBuilder->setCriteria($criteria);
        $results = $search->getGateway()->search($queryBuilder);
        if ($results->total() === 0) {
            return null;
        }

        $this->oldResourceCount++;

        return $results->current();
    }
}
