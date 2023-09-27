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
use oat\generis\model\GenerisRdf;
use oat\generis\model\kernel\persistence\Filter;
use oat\generis\model\kernel\uri\UriProvider;
use oat\generis\model\OntologyRdf;
use oat\generis\model\OntologyRdfs;
use oat\oatbox\event\EventManagerAwareTrait;
use oat\search\helper\SupportedOperatorHelper;
use oat\search\QueryBuilder;
use WikibaseSolutions\CypherDSL\Query;

use function WikibaseSolutions\CypherDSL\node;
use function WikibaseSolutions\CypherDSL\parameter;
use function WikibaseSolutions\CypherDSL\query;
use function WikibaseSolutions\CypherDSL\relationshipTo;
use function WikibaseSolutions\CypherDSL\variable;

class core_kernel_persistence_starsql_Class extends core_kernel_persistence_starsql_Resource implements core_kernel_persistence_ClassInterface
{
    use EventManagerAwareTrait;
    public function getSubClasses(core_kernel_classes_Class $resource, $recursive = false)
    {
        $uri = $resource->getUri();
        $relationship = OntologyRdfs::RDFS_SUBCLASSOF;
        $parameter = parameter();

        if (!empty($recursive)) {
            $startNodeFilteringUri = node()
                ->withLabels(['Resource'])
                ->withVariable("startNode")
                ->withProperties(["uri" => $parameter]);

            $startNode = node()
                ->withVariable("startNode");
            $descendantRelationship = relationshipTo()
                ->addType($relationship)
                ->withArbitraryHops(true);
            $descendantNode = node()
                ->withVariable("descendantNode");

            $query = query()
                ->match($startNodeFilteringUri)
                ->match($descendantNode->relationship($descendantRelationship, $startNode))
                ->returning([$descendantNode->property('uri')])
                ->build();
        } else {
            $startNodeFilteringUri = node()
                ->withLabels(['Resource'])
                ->withVariable("startNode")
                ->withProperties(["uri" => $parameter]);

            $startNode = node()
                ->withVariable("startNode");
            $descendantNode = node()
                ->withVariable("descendantNode");

            $query = query()
                ->match($startNodeFilteringUri)
                ->match($descendantNode->relationshipTo($startNode, $relationship))
                ->returning([$descendantNode->property('uri')])
                ->build();
        }

//        \common_Logger::i('getSubClasses(): ' . var_export($query, true));
        $results = $this->getPersistence()->run($query, [$parameter->getParameter() => $uri]);
        $returnValue = [];
        foreach ($results as $result) {
            $uri = $result->current();
            if (!$uri) {
                continue;
            }
            $subClass = $this->getModel()->getClass($uri);
            $returnValue[$subClass->getUri()] = $subClass;
        }

        return $returnValue;
    }

    public function isSubClassOf(core_kernel_classes_Class $resource, core_kernel_classes_Class $parentClass)
    {
        // @TODO would it be worth it to check direct relationship of node:IS_SUBCLASS_OF?
        $parentSubClasses = $parentClass->getSubClasses(true);
        foreach ($parentSubClasses as $subClass) {
            if ($subClass->getUri() === $resource->getUri()) {
                return true;
            }
        }

        return false;
    }

    public function getParentClasses(core_kernel_classes_Class $resource, $recursive = false)
    {
        $uri = $resource->getUri();
        $relationship = OntologyRdfs::RDFS_SUBCLASSOF;
        $parameter = parameter();
        if (!empty($recursive)) {
            $startNodeFilteringUri = node()
                ->withLabels(['Resource'])
                ->withVariable("startNode")
                ->withProperties(["uri" => $parameter]);

            $startNode = node()
                ->withVariable("startNode");
            $ancestorNode = node()
                ->withVariable("ancestorsNode");
            $descendantRelationship = relationshipTo()
                ->addType($relationship)
                ->withArbitraryHops(true);

            $query = query()
                ->match($startNodeFilteringUri)
                ->match($startNode->relationship($descendantRelationship, $ancestorNode))
                ->returning([$ancestorNode->property('uri')])
                ->build();
//            $query = <<<CYPHER
//                MATCH (startNode:Resource {uri: \$uri})
//                MATCH path = (startNode)-[:`{$relationship}`*]->(ancestorNode)
//                RETURN ancestorNode.uri
//CYPHER;
        } else {
            $startNodeFilteringUri = node()
                ->withLabels(['Resource'])
                ->withVariable("startNode")
                ->withProperties(["uri" => $parameter]);

            $startNode = node()
                ->withVariable("startNode");
            $ancestorNode = node()
                ->withVariable("ancestorNode");

            $query = query()
                ->match($startNodeFilteringUri)
                ->match($startNode->relationshipTo($ancestorNode, $relationship))
                ->returning([$ancestorNode->property('uri')])
                ->build();
//            $query = <<<CYPHER
//                MATCH (startNode:Resource {uri: \$uri})
//                MATCH path = (startNode)-[:`{$relationship}`]->(ancestorNode)
//                RETURN ancestorNode.uri
//CYPHER;
        }
//        $results = $this->getPersistence()->run($query, ['uri' => $uri]);
        $results = $this->getPersistence()->run($query, [$parameter->getParameter() => $uri]);

        $returnValue = [];
        foreach ($results as $result) {
            $uri = $result->current();
            $parentClass = $this->getModel()->getClass($uri);
            $returnValue[$parentClass->getUri()] = $parentClass;
        }

        return $returnValue;
    }

    public function getProperties(core_kernel_classes_Class $resource, $recursive = false)
    {
        $relationship = OntologyRdfs::RDFS_DOMAIN;
        $startNode = node()
            ->withVariable("startNode");
        $parameter = parameter();
        $uri = $resource->getUri();
        $startNodeFilteringUri = node()
            ->withLabels(['Resource'])
            ->withVariable("startNode")
            ->withProperties(["uri" => $parameter]);
        $descendantNode = node()->withVariable("descendantNode");
        $query = query()
            ->match($startNodeFilteringUri)
            ->match($descendantNode->relationshipTo($startNode, $relationship))
            ->returning([$descendantNode->property('uri')])
            ->build();

        $results = $this->getPersistence()->run($query, [$parameter->getParameter() => $uri]);

        $returnValue = [];
        foreach ($results as $result) {
            $uri = $result->current();
            if (!$uri) {
                continue;
            }
            $property = $this->getModel()->getProperty($uri);
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

    public function getInstances(core_kernel_classes_Class $resource, $recursive = false, $params = [])
    {
        $returnValue = [];

        $params = array_merge($params, ['like' => false, 'recursive' => $recursive]);

        $search = $this->getModel()->getSearchInterface();
        $query = $this->getFilterQuery($search->query(), $resource, [], $params);

        $resultList = $search->getGateway()->search($query);
        foreach ($resultList as $resource) {
            $returnValue[$resource->getUri()] = $resource;
        }

        return $returnValue;
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
        if ($uri == '') {
            $subject = $this->getServiceLocator()->get(UriProvider::SERVICE_ID)->provide();
        } elseif ($uri[0] == '#') { //$uri should start with # and be well formed
            $modelUri = common_ext_NamespaceManager::singleton()->getLocalNamespace()->getUri();
            $subject = rtrim($modelUri, '#') . $uri;
        } else {
            $subject = $uri;
        }

        $node = node()->addProperty('uri', $uriParameter = parameter())
            ->addLabel('Resource');
        if (!empty($label)) {
            $node->addProperty(OntologyRdfs::RDFS_LABEL, $label);
        }
        if (!empty($comment)) {
            $node->addProperty(OntologyRdfs::RDFS_COMMENT, $comment);
        }

        $nodeForRelationship = node()->withVariable($variableForRelatedResource = variable());
        $relatedResource = node('Resource')->withProperties(['uri' => $relatedUri = parameter()])->withVariable($variableForRelatedResource);
        $node = $node->relationshipTo($nodeForRelationship, OntologyRdf::RDF_TYPE);

        $query = query()
            ->match($relatedResource)
            ->create($node);
        $this->getPersistence()->run(
            $query->build(),
            [$uriParameter->getParameter() => $subject, $relatedUri->getParameter() => $resource->getUri()]
        );

        return $this->getModel()->getResource($subject);
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

        $propertyClass = $this->getModel()->getClass(OntologyRdf::RDF_PROPERTY);
        $properties = [
            OntologyRdfs::RDFS_DOMAIN => $resource->getUri(),
            GenerisRdf::PROPERTY_IS_LG_DEPENDENT => ((bool)$isLgDependent) ?  GenerisRdf::GENERIS_TRUE : GenerisRdf::GENERIS_FALSE
        ];
        if (!empty($label)) {
            $properties[OntologyRdfs::RDFS_LABEL] = $label;
        }
        if (!empty($comment)) {
            $properties[OntologyRdfs::RDFS_COMMENT] = $comment;
        }
        $propertyInstance = $propertyClass->createInstanceWithProperties($properties);

        $returnValue = $this->getModel()->getProperty($propertyInstance->getUri());

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
        $returnValue = [];

        $search = $this->getModel()->getSearchInterface();
        $query = $this->getFilterQuery($search->query(), $resource, $propertyFilters, $options);
        $resultList = $search->getGateway()->search($query);

        foreach ($resultList as $resource) {
            $returnValue[$resource->getUri()] = $resource;
        }

        return $returnValue;
    }

    public function countInstances(
        core_kernel_classes_Class $resource,
        $propertyFilters = [],
        $options = []
    ) {
        $search = $this->getModel()->getSearchInterface();
        $query = $this->getFilterQuery($search->query(), $resource, $propertyFilters, $options);

        return $search->getGateway()->count($query);
    }

    public function getInstancesPropertyValues(
        core_kernel_classes_Class $resource,
        core_kernel_classes_Property $property,
        $propertyFilters = [],
        $options = []
    ) {
        $options['return_field'] = $property->getUri();

        $search = $this->getModel()->getSearchInterface();
        $query = $this->getFilterQuery($search->query(), $resource, $propertyFilters, $options);

        return $search->getGateway()->search($query);
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
        //TODO: We need to figure out if commented checks below is still correct.
//        $class = $this->getModel()->getClass($resource->getUri());
//        if (!$class->exists() || empty($resources)) {
        if (empty($resources)) {
            return false;
        }

        $uris = [];
        foreach ($resources as $r) {
            $uri = (($r instanceof core_kernel_classes_Resource) ? $r->getUri() : $r);
            $uris[] = $uri;
        }

        $node = Query::node('Resource');
        $query = Query::new()
            ->match($node)
            ->where($node->property('uri')->in($uris))
            ->delete($node, $deleteReference);

        $this->getPersistence()->run($query->build());

        return true;
    }

    private function getFilterQuery(
        QueryBuilder $query,
        core_kernel_classes_Class $resource,
        array $propertyFilters = [],
        array $options = []
    ): QueryBuilder {
        $queryOptions = $query->getOptions();

        $queryOptions = array_merge(
            $queryOptions,
            $this->getClassFilter($options, $resource, $queryOptions),
            [
                'language' => $options['lang'] ?? '',
                'distinct' => $options['distinct'] ?? false,
                'return_field' => $options['return_field'] ?? null,
            ]
        );

        $query->setOptions($queryOptions);

        $order = $options['order'] ?? '';
        if (!empty($order)) {
            $orderDir = $options['orderdir'] ?? 'ASC';
            $query->sort([$order => strtolower($orderDir)]);
        }
        $query
            ->setLimit($options['limit'] ?? 0)
            ->setOffset($options['offset'] ?? 0);


        $this->addFilters($query, $propertyFilters, $options);

        return $query;
    }

    /**
     * @param QueryBuilder $query
     * @param array $propertyFilters
     * @param array $options
     */
    private function addFilters(QueryBuilder $query, array $propertyFilters, array $options): void
    {
        $isLikeOperator = $options['like'] ?? true;
        $and = (!isset($options['chaining']) || (strtolower($options['chaining']) === 'and'));

        $criteria = $query->newQuery();
        foreach ($propertyFilters as $filterProperty => $filterValue) {
            if ($filterValue instanceof Filter) {
                $propertyUri = $filterValue->getKey();
                $operator = $filterValue->getOperator();
                $mainValue = $filterValue->getValue();
                $extraValues = $filterValue->getOrConditionValues();
            } else {
                $propertyUri = $filterProperty;
                $operator = $isLikeOperator ? SupportedOperatorHelper::CONTAIN : SupportedOperatorHelper::EQUAL;

                if (is_array($filterValue) && !empty($filterValue)) {
                    $mainValue = array_shift($filterValue);
                    $extraValues = $filterValue;
                } else {
                    $mainValue = $filterValue;
                    $extraValues = [];
                }
            }

            $criteria->addCriterion(
                $propertyUri,
                $operator,
                $mainValue
            );

            foreach ($extraValues as $value) {
                $criteria->addOr($value);
            }

            if (!$and) {
                $query->setOr($criteria);
                $criteria = $query->newQuery();
            } else {
                $query->setCriteria($criteria);
            }
        }
    }

    /**
     * @param array $options
     * @param core_kernel_classes_Class $resource
     * @param array $queryOptions
     *
     * @return array
     */
    private function getClassFilter(array $options, core_kernel_classes_Class $resource, array $queryOptions): array
    {
        $rdftypes = [];

        if (isset($options['additionalClasses'])) {
            foreach ($options['additionalClasses'] as $aC) {
                $rdftypes[] = ($aC instanceof core_kernel_classes_Resource) ? $aC->getUri() : $aC;
            }
        }

        $rdftypes = array_unique($rdftypes);

        $queryOptions['type'] = [
            'resource' => $resource,
            'recursive' => $options['recursive'] ?? false,
            'extraClassUriList' => $rdftypes
        ];

        return $queryOptions;
    }
}
