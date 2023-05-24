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
 * Copyright (c) 2023 (original work) Open Assessment Technologies SA ;
 */

declare(strict_types=1);

use oat\generis\model\data\event\ClassPropertyCreatedEvent;
use oat\generis\model\OntologyRdf;
use oat\generis\model\OntologyRdfs;
use oat\oatbox\event\EventManagerAwareTrait;
use oat\generis\model\kernel\uri\UriProvider;
use function WikibaseSolutions\CypherDSL\node;
use function WikibaseSolutions\CypherDSL\query;

class core_kernel_persistence_starsql_Class extends core_kernel_persistence_starsql_Resource implements core_kernel_persistence_ClassInterface
{
    use EventManagerAwareTrait;

    public function getSubClasses(core_kernel_classes_Class $resource, $recursive = false)
    {
        throw new common_Exception('Not implemented! ' . __FILE__ . ' line: ' . __LINE__);
    }

    private function getRecursiveSubClasses(core_kernel_classes_Class $resource): array
    {
        $returnValue = [];
        $todo = [$resource];
        while (!empty($todo)) {
            $classString = '';
            foreach ($todo as $class) {
                $classString .= ", " . $this->getPersistence()->quote($class->getUri()) ;
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

    public function isSubClassOf(core_kernel_classes_Class $resource, core_kernel_classes_Class $parentClass)
    {
        throw new common_Exception('Not implemented! ' . __FILE__ . ' line: ' . __LINE__);
    }

    public function getParentClasses(core_kernel_classes_Class $resource, $recursive = false)
    {
        $uri = $resource->getUri();
        $relationship = OntologyRdfs::RDFS_SUBCLASSOF;
        $query = <<<CYPHER
            MATCH (startNode {uri: "{$uri}"})
            MATCH path = (startNode)-[:`{$relationship}`*]->(ancestorNode)
            RETURN ancestorNode
CYPHER;

        \common_Logger::i('getParentClasses(): ' . var_export($query, true));
        $results = $this->getPersistence()->run($query);
        $returnValue = [];
        foreach ($results as $result) {
            $uri = $result->current();
            $parentClass = $this->getModel()->getClass($uri);
            $returnValue[$parentClass->getUri()] = $parentClass ;
        }

        return $returnValue;
    }

    public function getProperties(core_kernel_classes_Class $resource, $recursive = false)
    {
        throw new common_Exception('Not implemented! ' . __FILE__ . ' line: ' . __LINE__);
    }

    public function getInstances(core_kernel_classes_Class $resource, $recursive = false, $params = [])
    {
        throw new common_Exception('Not implemented! ' . __FILE__ . ' line: ' . __LINE__);
    }

    /**
     * @deprecated
     */
    public function setInstance(core_kernel_classes_Class $resource, core_kernel_classes_Resource $instance)
    {
        throw new common_exception_DeprecatedApiMethod(__METHOD__ . ' is deprecated. ');
    }

    public function setSubClassOf(core_kernel_classes_Class $resource, core_kernel_classes_Class $iClass): bool
    {
        $subClassOf = $this->getModel()->getProperty(OntologyRdfs::RDFS_SUBCLASSOF);
        $returnValue = $this->setPropertyValue($resource, $subClassOf, $iClass->getUri());

        return (bool) $returnValue;
    }

    /**
     * @deprecated
     */
    public function setProperty(core_kernel_classes_Class $resource, core_kernel_classes_Property $property)
    {
        throw new common_exception_DeprecatedApiMethod(__METHOD__ . ' is deprecated. ');
    }

    public function createInstance(core_kernel_classes_Class $resource, $label = '', $comment = '', $uri = '')
    {
        $subject = '';
        if ($uri == '') {
            $subject = $this->getServiceLocator()->get(UriProvider::SERVICE_ID)->provide();
        } elseif ($uri[0] == '#') { //$uri should start with # and be well formed
            $modelUri = common_ext_NamespaceManager::singleton()->getLocalNamespace()->getUri();
            $subject = rtrim($modelUri, '#') . $uri;
        } else {
            $subject = $uri;
        }

        $query = query()
            ->create(
                node()->withProperties(['uri' => $subject])
            )->build();
        $results = $this->getPersistence()->run($query);

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
     * @see core_kernel_persistence_ClassInterface::createSubClass()
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
                    'propertyLabel' => $propertyInstance->getLabel()
                ]
            )
        );

        return $returnValue;
    }

    /**
     * @deprecated
     */
    public function searchInstances(core_kernel_classes_Class $resource, $propertyFilters = [], $options = [])
    {
        throw new common_Exception('Not implemented! ' . __FILE__ . ' line: ' . __LINE__);
    }

    public function countInstances(core_kernel_classes_Class $resource, $propertyFilters = [], $options = [])
    {
        throw new common_Exception('Not implemented! ' . __FILE__ . ' line: ' . __LINE__);
    }

    public function getInstancesPropertyValues(core_kernel_classes_Class $resource, core_kernel_classes_Property $property, $propertyFilters = [], $options = [])
    {
        throw new common_Exception('Not implemented! ' . __FILE__ . ' line: ' . __LINE__);
    }

    /**
     * @deprecated
     */
    public function unsetProperty(core_kernel_classes_Class $resource, core_kernel_classes_Property $property)
    {
        throw new common_exception_DeprecatedApiMethod(__METHOD__ . ' is deprecated. ');
    }

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

    public function deleteInstances(core_kernel_classes_Class $resource, $resources, $deleteReference = false)
    {
        throw new common_Exception('Not implemented! ' . __FILE__ . ' line: ' . __LINE__);
    }

    public function getFilteredQuery(core_kernel_classes_Class $resource, $propertyFilters = [], $options = []): string
    {
        throw new common_Exception('Not implemented! ' . __FILE__ . ' line: ' . __LINE__);
    }
}
