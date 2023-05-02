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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 *               2012-2017 (update and modification) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 */

use Doctrine\DBAL\DBALException;
use oat\generis\model\data\event\ClassPropertyCreatedEvent;
use oat\generis\model\GenerisRdf;
use oat\generis\model\kernel\persistence\smoothsql\search\ComplexSearchService;
use oat\generis\model\kernel\uri\UriProvider;
use oat\generis\model\OntologyRdf;
use oat\generis\model\OntologyRdfs;
use oat\oatbox\event\EventManagerAwareTrait;

/**
 * Short description of class core_kernel_persistence_smoothsql_Class
 */
class core_kernel_persistence_smoothsql_Class extends core_kernel_persistence_smoothsql_Resource implements core_kernel_persistence_ClassInterface
{
    use EventManagerAwareTrait;

    /**
     * (non-PHPdoc)
     *
     * @see core_kernel_persistence_ClassInterface::getSubClasses()
     *
     * @param mixed $recursive
     */
    public function getSubClasses(core_kernel_classes_Class $resource, $recursive = false)
    {
        if (!$recursive) {
            $returnValue = [];
            $sqlQuery = 'SELECT subject FROM statements WHERE predicate = ? and ' . $this->getPersistence()->getPlatForm()->getObjectTypeCondition() . ' = ?';
            $sqlResult = $this->getPersistence()->query($sqlQuery, [OntologyRdfs::RDFS_SUBCLASSOF, $resource->getUri()]);

            while ($row = $sqlResult->fetch()) {
                $returnValue[$row['subject']] = $this->getModel()->getClass($row['subject']);
            }

            return $returnValue;
        }

        return $this->getRecursiveSubClasses($resource);
    }

    /**
     * Improved recursive class traversal (reduced to 1 query per tree depth)
     *
     * @param core_kernel_classes_Class $resource
     *
     * @return array
     */
    private function getRecursiveSubClasses(core_kernel_classes_Class $resource)
    {
        $returnValue = [];
        $todo = [$resource];

        while (!empty($todo)) {
            $classString = '';

            foreach ($todo as $class) {
                $classString .= ', ' . $this->getPersistence()->quote($class->getUri()) ;
            }
            $sqlQuery = 'SELECT subject FROM statements WHERE predicate = ? and ' . $this->getPersistence()->getPlatForm()->getObjectTypeCondition()
                . ' in (' . substr($classString, 1) . ')';
            $sqlResult = $this->getPersistence()->query($sqlQuery, [OntologyRdfs::RDFS_SUBCLASSOF]);
            $todo = [];

            while ($row = $sqlResult->fetch()) {
                $subClass = $this->getModel()->getClass($row['subject']);

                if (!isset($returnValue[$subClass->getUri()])) {
                    $todo[] = $subClass;
                }
                $returnValue[$subClass->getUri()] = $subClass;
            }
        }

        return (array) $returnValue;
    }

    /**
     * (non-PHPdoc)
     *
     * @see core_kernel_persistence_ClassInterface::isSubClassOf()
     */
    public function isSubClassOf(core_kernel_classes_Class $resource, core_kernel_classes_Class $parentClass)
    {
        $returnValue = false;

        $query = 'SELECT object FROM statements WHERE subject = ? AND predicate = ? AND ' . $this->getPersistence()->getPlatForm()->getObjectTypeCondition() . ' = ?';
        $result = $this->getPersistence()->query($query, [
            $resource->getUri(),
            OntologyRdfs::RDFS_SUBCLASSOF,
            $parentClass->getUri(),
        ]);

        while ($row = $result->fetch()) {
            $returnValue = true;

            break;
        }

        if (!$returnValue) {
            $parentSubClasses = $parentClass->getSubClasses(true);

            foreach ($parentSubClasses as $subClass) {
                if ($subClass->getUri() == $resource->getUri()) {
                    $returnValue = true;

                    break;
                }
            }
        }

        return $returnValue;
    }

    /**
     * (non-PHPdoc)
     *
     * @see core_kernel_persistence_ClassInterface::getParentClasses()
     *
     * @param mixed $recursive
     */
    public function getParentClasses(core_kernel_classes_Class $resource, $recursive = false)
    {
        $returnValue = [];

        $sqlQuery = 'SELECT object FROM statements WHERE subject = ?  AND predicate = ?';

        $sqlResult = $this->getPersistence()->query($sqlQuery, [$resource->getUri(), OntologyRdfs::RDFS_SUBCLASSOF]);

        while ($row = $sqlResult->fetch()) {
            $parentClass = $this->getModel()->getClass($row['object']);

            $returnValue[$parentClass->getUri()] = $parentClass ;

            if ($recursive == true && $parentClass->getUri() != OntologyRdfs::RDFS_CLASS && $parentClass->getUri() != OntologyRdfs::RDFS_RESOURCE) {
                if ($parentClass->getUri() == GenerisRdf::CLASS_GENERIS_RESOURCE) {
                    $returnValue[OntologyRdfs::RDFS_RESOURCE] = $this->getModel()->getClass(OntologyRdfs::RDFS_RESOURCE);
                } else {
                    $plop = $parentClass->getParentClasses(true);
                    $returnValue = array_merge($returnValue, $plop);
                }
            }
        }

        return $returnValue;
    }

    /**
     * (non-PHPdoc)
     *
     * @see core_kernel_persistence_ClassInterface::getProperties()
     *
     * @param mixed $recursive
     */
    public function getProperties(core_kernel_classes_Class $resource, $recursive = false)
    {
        $returnValue = [];

        $sqlQuery = 'SELECT subject FROM statements WHERE predicate = ?  AND ' . $this->getPersistence()->getPlatForm()->getObjectTypeCondition() . ' = ?';
        $sqlResult = $this->getPersistence()->query($sqlQuery, [
            OntologyRdfs::RDFS_DOMAIN,
            $resource->getUri(),
        ]);

        while ($row = $sqlResult->fetch()) {
            $property = $this->getModel()->getProperty($row['subject']);
            $returnValue[$property->getUri()] = $property;
        }

        if ($recursive == true) {
            $parentClasses = $this->getParentClasses($resource, true);

            foreach ($parentClasses as $parent) {
                if ($parent->getUri() != OntologyRdfs::RDFS_CLASS) {
                    $returnValue = array_merge($returnValue, $parent->getProperties(false));
                }
            }
        }

        return $returnValue;
    }

    /**
     * (non-PHPdoc)
     *
     * @see core_kernel_persistence_ClassInterface::getInstances()
     *
     * @param mixed $recursive
     * @param mixed $params
     */
    public function getInstances(core_kernel_classes_Class $resource, $recursive = false, $params = [])
    {
        $returnValue = [];

        $params = array_merge($params, ['like' => false, 'recursive' => $recursive]);

        $query = $this->getFilteredQuery($resource, [], $params);
        $result = $this->getPersistence()->query($query);

        while ($row = $result->fetch()) {
            $foundInstancesUri = $row['subject'];
            $returnValue[$foundInstancesUri] = $this->getModel()->getResource($foundInstancesUri);
        }

        return $returnValue;
    }

    /**
     * @param core_kernel_classes_Class $resource
     * @param core_kernel_classes_Resource $instance
     *
     * @throws common_exception_DeprecatedApiMethod
     *
     * @return core_kernel_classes_Resource
     *
     * @deprecated
     */
    public function setInstance(core_kernel_classes_Class $resource, core_kernel_classes_Resource $instance)
    {
        throw new common_exception_DeprecatedApiMethod(__METHOD__ . ' is deprecated. ');
    }

    /**
     * Short description of method setSubClassOf
     *
     * @access public
     *
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     *
     * @param  core_kernel_classes_Class resource
     * @param  Class iClass
     *
     * @return boolean
     */
    public function setSubClassOf(core_kernel_classes_Class $resource, core_kernel_classes_Class $iClass)
    {
        $returnValue = (bool) false;

        $subClassOf = $this->getModel()->getProperty(OntologyRdfs::RDFS_SUBCLASSOF);
        $returnValue = $this->setPropertyValue($resource, $subClassOf, $iClass->getUri());

        return (bool) $returnValue;
    }

    /**
     * Short description of method setProperty
     *
     * @access public
     *
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     *
     * @param  core_kernel_classes_Class resource
     * @param  Property property
     *
     * @return boolean
     *
     * @deprecated
     */
    public function setProperty(core_kernel_classes_Class $resource, core_kernel_classes_Property $property)
    {
        throw new common_exception_DeprecatedApiMethod(__METHOD__ . ' is deprecated. ');
    }

    /**
     * (non-PHPdoc)
     *
     * @see core_kernel_persistence_ClassInterface::createInstance()
     *
     * @param mixed $label
     * @param mixed $comment
     * @param mixed $uri
     */
    public function createInstance(core_kernel_classes_Class $resource, $label = '', $comment = '', $uri = '')
    {
        $returnValue = null;

        $subject = '';

        if ($uri == '') {
            $subject = $this->getServiceLocator()->get(UriProvider::SERVICE_ID)->provide();
        } elseif ($uri[0] == '#') { //$uri should start with # and be well formed
            $modelUri = common_ext_NamespaceManager::singleton()->getLocalNamespace()->getUri();
            $subject = rtrim($modelUri, '#') . $uri;
        } else {
            $subject = $uri;
        }

        $returnValue = $this->getModel()->getResource($subject);

        if (!$returnValue->hasType($resource)) {
            $returnValue->setType($resource);
        } else {
            common_Logger::e('already had type ' . $resource);
        }

        if (!empty($label)) {
            $returnValue->setLabel($label);
        }

        if (!empty($comment)) {
            $returnValue->setComment($comment);
        }

        return $returnValue;
    }

    /**
     * (non-PHPdoc)
     *
     * @see core_kernel_persistence_ClassInterface::createSubClass()
     *
     * @param mixed $label
     * @param mixed $comment
     * @param mixed $uri
     */
    public function createSubClass(core_kernel_classes_Class $resource, $label = '', $comment = '', $uri = '')
    {
        if (!empty($uri)) {
            common_Logger::w('Use of parameter uri in ' . __METHOD__ . ' is deprecated');
        }
        $uri = empty($uri) ? $this->getServiceLocator()->get(UriProvider::SERVICE_ID)->provide() : $uri;
        $returnValue = $this->getModel()->getClass($uri);
        $properties = [
            OntologyRdfs::RDFS_SUBCLASSOF => $resource,
        ];

        if (!empty($label)) {
            $properties[OntologyRdfs::RDFS_LABEL] = $label;
        }

        if (!empty($comment)) {
            $properties[OntologyRdfs::RDFS_COMMENT] = $comment;
        }

        $returnValue->setPropertiesValues($properties);

        return $returnValue;
    }

    /**
     * (non-PHPdoc)
     *
     * @see core_kernel_persistence_ClassInterface::createProperty()
     *
     * @param mixed $label
     * @param mixed $comment
     * @param mixed $isLgDependent
     */
    public function createProperty(core_kernel_classes_Class $resource, $label = '', $comment = '', $isLgDependent = false)
    {
        $returnValue = null;

        $property = $this->getModel()->getClass(OntologyRdf::RDF_PROPERTY);
        $propertyInstance = $property->createInstance($label, $comment);
        $returnValue = $this->getModel()->getProperty($propertyInstance->getUri());
        $returnValue->setLgDependent($isLgDependent);

        if (!$returnValue->setDomain($resource)) {
            throw new common_Exception('problem creating property');
        }

        $this->getEventManager()->trigger(
            new ClassPropertyCreatedEvent(
                $resource,
                [
                    'propertyUri' => $propertyInstance->getUri(),
                    'propertyLabel' => $propertyInstance->getLabel(),
                ]
            )
        );

        return $returnValue;
    }

    /**
     * (non-PHPdoc)
     * prefer use
     *
     * @deprecated since 3.0
     * @see core_kernel_persistence_ClassInterface::searchInstances()
     *
     * @param mixed $propertyFilters
     * @param mixed $options
     */
    public function searchInstances(core_kernel_classes_Class $resource, $propertyFilters = [], $options = [])
    {
        $returnValue = [];

        // Avoid a 'like' search on OntologyRdf::RDF_TYPE!
        if (count($propertyFilters) === 0) {
            $options = array_merge($options, ['like' => false]);
        }

        $query = $this->getFilteredQuery($resource, $propertyFilters, $options);
        $result = $this->getPersistence()->query($query);

        while ($row = $result->fetch()) {
            $foundInstancesUri = $row['subject'];
            $returnValue[$foundInstancesUri] = $this->getModel()->getResource($foundInstancesUri);
        }

        return $returnValue;
    }

    /**
     * (non-PHPdoc)
     *
     * @see core_kernel_persistence_ClassInterface::countInstances()
     *
     * @param mixed $propertyFilters
     * @param mixed $options
     */
    public function countInstances(core_kernel_classes_Class $resource, $propertyFilters = [], $options = [])
    {
        if (isset($options['offset'])) {
            unset($options['offset']);
        }

        if (isset($options['limit'])) {
            unset($options['limit']);
        }

        if (isset($options['order'])) {
            unset($options['order']);
        }

        $query = 'SELECT count(subject) FROM (' . $this->getFilteredQuery($resource, $propertyFilters, $options) . ') as countq';

        return (int)$this->getPersistence()->query($query)->fetchColumn();
    }

    /**
     * (non-PHPdoc)
     *
     * @see core_kernel_persistence_ClassInterface::getInstancesPropertyValues()
     *
     * @param mixed $propertyFilters
     * @param mixed $options
     */
    public function getInstancesPropertyValues(core_kernel_classes_Class $resource, core_kernel_classes_Property $property, $propertyFilters = [], $options = [])
    {
        $returnValue = [];

        $distinct = $options['distinct'] ?? false;

        if (count($propertyFilters) === 0) {
            $options = array_merge($options, ['like' => false]);
        }

        $filteredQuery = $this->getFilteredQuery($resource, $propertyFilters, $options);

        // Get all the available property values in the subset of instances
        $query = 'SELECT';

        if ($distinct) {
            $query .= ' DISTINCT';
        }

        $query .= " object FROM (SELECT overq.subject, valuesq.object FROM (${filteredQuery}) as overq JOIN statements AS valuesq ON (overq.subject = valuesq.subject AND valuesq.predicate = ?)) AS overrootq";

        $sqlResult = $this->getPersistence()->query($query, [$property->getUri()]);

        while ($row = $sqlResult->fetch()) {
            $returnValue[] = common_Utils::toResource($row['object']);
        }

        return (array) $returnValue;
    }

    /**
     * Remove a Property from its Class definition.
     *
     * @param core_kernel_classes_Class $resource
     * @param core_kernel_classes_Property $property
     *
     * @deprecated
     *
     * @throws common_exception_DeprecatedApiMethod
     */
    public function unsetProperty(core_kernel_classes_Class $resource, core_kernel_classes_Property $property)
    {
        throw new common_exception_DeprecatedApiMethod(__METHOD__ . ' is deprecated. ');
    }

    /**
     * (non-PHPdoc)
     *
     * @see core_kernel_persistence_ClassInterface::createInstanceWithProperties()
     *
     * @param mixed $properties
     */
    public function createInstanceWithProperties(core_kernel_classes_Class $type, $properties)
    {
        $returnValue = null;

        if (isset($properties[OntologyRdf::RDF_TYPE])) {
            throw new core_kernel_persistence_Exception('Additional types in createInstanceWithProperties not permited');
        }

        $properties[OntologyRdf::RDF_TYPE] = $type;
        $returnValue = $this->getModel()->getResource($this->getServiceLocator()->get(UriProvider::SERVICE_ID)->provide());
        $returnValue->setPropertiesValues($properties);

        return $returnValue;
    }

    /**
     * (non-PHPdoc)
     *
     * @see core_kernel_persistence_ClassInterface::deleteInstances()
     *
     * @param mixed $resources
     * @param mixed $deleteReference
     */
    public function deleteInstances(core_kernel_classes_Class $resource, $resources, $deleteReference = false)
    {
        $returnValue = false;

        $class = $this->getModel()->getClass($resource->getUri());
        $uris = [];

        foreach ($resources as $r) {
            $uri = (($r instanceof core_kernel_classes_Resource) ? $r->getUri() : $r);
            $uris[] = $this->getPersistence()->quote($uri);
        }

        if ($class->exists()) {
            $inValues = implode(',', $uris);
            $query = 'DELETE FROM statements WHERE subject IN (' . $inValues . ')';

            if (true === $deleteReference) {
                $params[] = $resource->getUri();
                $query .= ' OR object IN (' . $inValues . ')';
            }

            try {
                // Even if now rows are affected, we consider the resources
                // as deleted.
                $this->getPersistence()->exec($query);
                $returnValue = true;
            } catch (DBALException $e) {
                throw new core_kernel_persistence_smoothsql_Exception('An error occured while deleting resources: ' . $e->getMessage());
            }
        }

        return $returnValue;
    }

    /**
     * @param core_kernel_classes_Class $resource
     * @param array $propertyFilters
     * @param array $options
     *
     * @return string
     */
    public function getFilteredQuery(core_kernel_classes_Class $resource, $propertyFilters = [], $options = [])
    {
        $rdftypes = [];

        // Check recursivity...
        if (isset($options['recursive']) && $options['recursive']) {
            foreach ($this->getSubClasses($resource, $options['recursive']) as $subClass) {
                $rdftypes[] = $subClass->getUri();
            }
        }

        // Check additional classes...
        if (isset($options['additionalClasses'])) {
            foreach ($options['additionalClasses'] as $aC) {
                $rdftypes[] = ($aC instanceof core_kernel_classes_Resource) ? $aC->getUri() : $aC;
                $rdftypes = array_unique($rdftypes);
            }
        }

        // Add the class type of the given class
        if (!in_array($resource->getUri(), $rdftypes)) {
            $rdftypes[] = $resource->getUri();
        }

        $and = (isset($options['chaining']) === false) ? true : ((strtolower($options['chaining']) === 'and') ? true : false);
        $like = (isset($options['like']) === false) ? true : $options['like'];
        $lang = (isset($options['lang']) === false) ? '' : $options['lang'];
        $offset = (isset($options['offset']) === false) ? 0 : $options['offset'];
        $limit = (isset($options['limit']) === false) ? 0 : $options['limit'];
        $order = (isset($options['order']) === false) ? '' : $options['order'];
        $orderdir = (isset($options['orderdir']) === false) ? 'ASC' : $options['orderdir'];

        if ($this->getServiceLocator()->has(ComplexSearchService::SERVICE_ID)) {
            $search = $this->getModel()->getSearchInterface();
            $query = $search->getQuery($this->getModel(), $rdftypes, $propertyFilters, $and, $like, $lang, $offset, $limit, $order, $orderdir);
        } else {
            $query = core_kernel_persistence_smoothsql_Utils::buildFilterQuery($this->getModel(), $rdftypes, $propertyFilters, $and, $like, $lang, $offset, $limit, $order, $orderdir);
        }

        return $query;
    }
}
