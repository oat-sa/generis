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
use WikibaseSolutions\CypherDSL\Clauses\WhereClause;
use WikibaseSolutions\CypherDSL\Expressions\RawExpression;
use WikibaseSolutions\CypherDSL\Query;

use WikibaseSolutions\CypherDSL\Types\PropertyTypes\BooleanType;

use function WikibaseSolutions\CypherDSL\node;
use function WikibaseSolutions\CypherDSL\parameter;
use function WikibaseSolutions\CypherDSL\procedure;
use function WikibaseSolutions\CypherDSL\query;
use function WikibaseSolutions\CypherDSL\variable;

class core_kernel_persistence_starsql_Class extends core_kernel_persistence_starsql_Resource implements core_kernel_persistence_ClassInterface
{
    use EventManagerAwareTrait;

    public function getSubClasses(core_kernel_classes_Class $resource, $recursive = false)
    {
        $uri = $resource->getUri();
        $relationship = OntologyRdfs::RDFS_SUBCLASSOF;
        if (!empty($recursive)) {
            $query = <<<CYPHER
                MATCH (startNode:Resource {uri: \$uri})
                MATCH path = (descendantNode)-[:`{$relationship}`*]->(startNode)
                RETURN descendantNode.uri
CYPHER;
        } else {
            $query = <<<CYPHER
                MATCH (startNode:Resource {uri: \$uri})
                MATCH path = (descendantNode)-[:`{$relationship}`]->(startNode)
                RETURN descendantNode.uri
CYPHER;
        }

//        \common_Logger::i('getSubClasses(): ' . var_export($query, true));
        $results = $this->getPersistence()->run($query, ['uri' => $uri]);
        $returnValue = [];
        foreach ($results as $result) {
            $uri = $result->current();
            if (!$uri) {
                continue;
            }
            $subClass = $this->getModel()->getClass($uri);
            $returnValue[$subClass->getUri()] = $subClass ;
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
        if (!empty($recursive)) {
            $query = <<<CYPHER
                MATCH (startNode:Resource {uri: \$uri})
                MATCH path = (startNode)-[:`{$relationship}`*]->(ancestorNode)
                RETURN ancestorNode.uri
CYPHER;
        } else {
            $query = <<<CYPHER
                MATCH (startNode:Resource {uri: \$uri})
                MATCH path = (startNode)-[:`{$relationship}`]->(ancestorNode)
                RETURN ancestorNode.uri
CYPHER;
        }

        $results = $this->getPersistence()->run($query, ['uri' => $uri]);
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
        $uri = $resource->getUri();
        $relationship = OntologyRdfs::RDFS_DOMAIN;
        $query = <<<CYPHER
                MATCH (startNode:Resource {uri: \$uri})
                MATCH path = (descendantNode)-[:`{$relationship}`]->(startNode)
                RETURN descendantNode.uri
CYPHER;
        $results = $this->getPersistence()->run($query, ['uri' => $uri]);
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

        $query = $this->getFilteredQuery($resource, [], $params);
        $results = $this->getPersistence()->run($query);

        foreach ($results as $result) {
            $uri = $result->current();
            if (!$uri) {
                continue;
            }
            $returnValue[$uri] = $this->getModel()->getResource($uri);
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

        $session = $this->getServiceLocator()->get(\oat\oatbox\session\SessionService::SERVICE_ID)->getCurrentSession();
        $sessionLanguage = $this->getDataLanguage();
        $node = node()->addProperty('uri', $uriParameter = parameter())
            ->addLabel('Resource');
        if (!empty($label)) {
            $node->addProperty(OntologyRdfs::RDFS_LABEL, [$label . '@' . $sessionLanguage]);
        }
        if (!empty($comment)) {
            $node->addProperty(OntologyRdfs::RDFS_COMMENT, [$comment . '@' . $sessionLanguage]);
        }

        $node->addProperty(
            'http://www.tao.lu/Ontologies/TAO.rdf#UpdatedBy',
            (string)$session->getUser()->getIdentifier()
        );
        $node->addProperty(
            'http://www.tao.lu/Ontologies/TAO.rdf#UpdatedAt',
            procedure()::raw('timestamp')
        );

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

        // Avoid a 'like' search on OntologyRdf::RDF_TYPE!
        if (count($propertyFilters) === 0) {
            $options = array_merge($options, ['like' => false]);
        }

        $query = $this->getFilteredQuery($resource, $propertyFilters, $options);
        $results = $this->getPersistence()->run($query);

        foreach ($results as $result) {
            $uri = $result->current();
            if (!$uri) {
                continue;
            }
            $returnValue[$uri] = $this->getModel()->getResource($uri);
        }

        return $returnValue;
    }

    public function countInstances(
        core_kernel_classes_Class $resource,
        $propertyFilters = [],
        $options = []
    ) {
        if (isset($options['offset'])) {
            unset($options['offset']);
        }

        if (isset($options['limit'])) {
            unset($options['limit']);
        }

        if (isset($options['order'])) {
            unset($options['order']);
        }

        $options['return'] = 'count(subject)';

        $query = $this->getFilteredQuery($resource, $propertyFilters, $options);
        $results = $this->getPersistence()->run($query);

        return (int)$results->first()->current();
    }

    public function getInstancesPropertyValues(
        core_kernel_classes_Class $resource,
        core_kernel_classes_Property $property,
        $propertyFilters = [],
        $options = []
    ) {
        $returnValue = [];

        if (count($propertyFilters) === 0) {
            $options = array_merge($options, ['like' => false]);
        }

        $predicate = sprintf('subject.`%s`', $property->getUri());
        if ($property->isLgDependent()) {
            $predicate = $this->buildLanguagePattern($predicate, $options['lang'] ?? '')->toQuery();
        }

        $distinct = $options['distinct'] ?? false;
        if ($distinct) {
            $options['return'] = sprintf('DISTINCT toStringOrNull(%s)', $predicate);
        } else {
            $options['return'] = sprintf('toStringOrNull(%s)', $predicate);
        }

        $query = $this->getFilteredQuery($resource, $propertyFilters, $options);
        $results = $this->getPersistence()->run($query);

        foreach ($results as $result) {
            $object = $result->current();
            if (!$object) {
                continue;
            }
            $returnValue[] = common_Utils::toResource($object);
        }

        return $returnValue;
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

    private function getFilteredQuery(core_kernel_classes_Class $resource, $propertyFilters = [], $options = []): string
    {
        $and = (!isset($options['chaining']) || (strtolower($options['chaining']) === 'and'));
        $like = $options['like'] ?? true;
        $lang = $options['lang'] ?? '';
        $offset = $options['offset'] ?? 0;
        $limit = $options['limit'] ?? 0;
        $order = $options['order'] ?? '';
        $orderDir = $options['orderdir'] ?? 'ASC';
        $return = $options['return'] ?? 'subject.uri';

        $subject = Query::node('Resource')->withVariable(Query::variable('subject'));

        $rdftypes = [$resource->getUri()];

        if (isset($options['additionalClasses'])) {
            foreach ($options['additionalClasses'] as $aC) {
                $rdftypes[] = ($aC instanceof core_kernel_classes_Resource) ? $aC->getUri() : $aC;
            }
        }

        $rdftypes = array_unique($rdftypes);

        $parentClass = Query::node('Resource')->withVariable(Query::variable('parent'));
        $parentPath = $subject->relationshipTo($parentClass, OntologyRdf::RDF_TYPE);
        $parentWhere = $this->buildPropertyQuery($parentClass->property('uri'), $rdftypes, false);

        $recursive = $options['recursive'] ?? false;
        if ($recursive) {
            $grandParentClass = Query::node('Resource')->withVariable(Query::variable('grandParent'));
            $subClassRelation = Query::relationshipTo()->addType(OntologyRdfs::RDFS_SUBCLASSOF)->withArbitraryHops(true);

            $parentPath = $parentPath->relationship($subClassRelation, $grandParentClass);
            $parentWhere = $parentWhere->or($this->buildPropertyQuery($grandParentClass->property('uri'), $resource, false));
        }

        $matchPatterns = [$parentPath];
        $propertyFilter = [$parentWhere];
        foreach ($propertyFilters as $propertyUri => $filterValues) {
            if ($filterValues instanceof Filter) {
                throw new common_exception_NoImplementation();
            }

            $property = $this->getModel()->getProperty($propertyUri);
            if ($property->isRelationship()) {
                $object = Query::node('Resource');
                $matchPatterns[] = $subject->relationshipTo($object, $propertyUri);

                $predicate = $object->property('uri');
                $propertyFilter[] = $this->buildPropertyQuery($predicate, $filterValues, false);
            } else {
                $predicate = $subject->property($propertyUri);
                if ($property->isLgDependent()) {
                    $predicate = $this->buildLanguagePattern($predicate->toQuery(), $lang);
                }
                $propertyFilter[] = $this->buildPropertyQuery($predicate, $filterValues, $like);
            }
        }

        $query = Query::new()->match($matchPatterns);
        $query->where($propertyFilter, $and ? WhereClause::AND : WhereClause::OR);
        $query->returning(Query::rawExpression($return));

        if (!empty($order)) {
            $predicate = $subject->property($order);

            $orderProperty = $this->getModel()->getProperty($order);
            if ($orderProperty->isLgDependent()) {
                $predicate = $this->buildLanguagePattern($predicate->toQuery(), $lang);
            }
            //Can't use dedicated order function as it doesn't support raw expressions
            $query->raw(
                'ORDER BY',
                $predicate->toQuery() . ((strtoupper($orderDir) === 'DESC') ? ' DESCENDING': '')
            );
        }

        if ($limit > 0) {
            $query
                ->skip($offset)
                ->limit($limit);
        }

        return $query->build();
    }

    private function buildPropertyQuery(
        $predicate,
        $values,
        bool $like
    ): BooleanType {
        if (!is_array($values)) {
            $values = [$values];
        }

        $valuePatterns = null;
        $lastItem = array_key_last($values);
        foreach ($values as $key => $val) {
            if ($val instanceof core_kernel_classes_Resource) {
                $returnValue = $predicate->equals($val->getUri());
            } else {
                $patternToken = trim((string)$val);
                if ($like) {
                    $isWildcard = (strpos($patternToken, '*') !== false);
                    $patternToken = strtr(
                        trim($patternToken, '%'),
                        [
                            '.' => '\\.',
                            '\_' => '_',
                            '\%' => '%',
                            '*' => '.*',
                            '_' => '.',
                            '%' => '.*',
                        ]
                    );
                    if (!$isWildcard) {
                        $patternToken = '.*' . $patternToken . '.*';
                    }
                    $returnValue = $predicate->regex('(?i)' . $patternToken);
                } else {
                    $returnValue = $predicate->equals($patternToken);
                }
            }

            $valuePatterns = ($valuePatterns)
                ? $valuePatterns->or($returnValue, ($key === $lastItem))
                : $returnValue;
        }

        return $valuePatterns;
    }

    /**
     * @param string $predicate
     * @param string $lang
     *
     * @return RawExpression
     */
    private function buildLanguagePattern(string $predicate, string $lang = ''): RawExpression
    {
        $defaultLanguage = $this->getDefaultLanguage();

        if (empty($lang) || $lang === $defaultLanguage) {
            $resultExpression = Query::rawExpression(
                sprintf(
                    "n10s.rdf.getLangValue('%s', %s)",
                    $defaultLanguage,
                    $predicate
                )
            );
        } else {
            $resultExpression = Query::rawExpression(
                sprintf(
                    "coalesce(n10s.rdf.getLangValue('%s', %s), n10s.rdf.getLangValue('%s', %s))",
                    $lang,
                    $predicate,
                    $defaultLanguage,
                    $predicate
                )
            );
        }

        return $resultExpression;
    }
}
