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

use oat\generis\model\OntologyRdf;
use oat\generis\model\OntologyRdfs;
use oat\oatbox\session\SessionService;
use oat\oatbox\user\UserLanguageServiceInterface;
use oat\generis\model\kernel\uri\UriProvider;
use Zend\ServiceManager\ServiceLocatorInterface;
use function WikibaseSolutions\CypherDSL\node;
use function WikibaseSolutions\CypherDSL\query;
use function WikibaseSolutions\CypherDSL\procedure;
use function WikibaseSolutions\CypherDSL\relationshipTo;

class core_kernel_persistence_starsql_Resource implements core_kernel_persistence_ResourceInterface
{
    const RELATIONSHIP_PROPERTIES = [
        OntologyRdf::RDF_TYPE,
        OntologyRdfs::RDFS_CLASS,
        OntologyRdfs::RDFS_RANGE,
        OntologyRdfs::RDFS_DOMAIN,
        OntologyRdfs::RDFS_SUBCLASSOF,
        OntologyRdfs::RDFS_SUBPROPERTYOF,
    ];

    /**
     * @var core_kernel_persistence_starsql_StarModel
     */
    private $model;

    public function __construct(core_kernel_persistence_starsql_StarModel $model)
    {
        $this->model = $model;
    }

    protected function getModel(): core_kernel_persistence_starsql_StarModel
    {
        return $this->model;
    }

    /**
     * @return common_persistence_GraphPersistence
     */
    protected function getPersistence()
    {
        return $this->model->getPersistence();
    }

    protected function getNewTripleModelId()
    {
        return $this->model->getNewTripleModelId();
    }

    public function getTypes(core_kernel_classes_Resource $resource): array
    {
        $relatedResource = node();
        $query = query()
            ->match(
                node()->withProperties(['uri' => $resource->getUri()])
                    ->relationshipTo($relatedResource, OntologyRdf::RDF_TYPE)
            )
            ->returning($relatedResource->property('uri'))
            ->build();

        common_Logger::i('getTypes(): ' . var_export($query, true));
        $results = $this->getPersistence()->run($query);
        $returnValue = [];
        foreach ($results as $result) {
            $uri = $result->current();
            $returnValue[$uri] = $this->getModel()->getClass($uri);
        }

        return (array) $returnValue;
    }

    public function getPropertyValues(core_kernel_classes_Resource $resource, core_kernel_classes_Property $property, $options = []): array
    {
        if (isset($options['last'])) {
            throw new core_kernel_persistence_Exception('Option \'last\' no longer supported');
        }

        $node = node()->withProperties(['uri' => $resource->getUri()]);
        if (in_array($property->getUri(), self::RELATIONSHIP_PROPERTIES)) {
            $relationship = relationshipTo()->withTypes([$property->getUri()]);
            $remoteNode = node();
            $query = query()
                ->match($node->relationship($relationship, $remoteNode))
                ->returning($remoteNode->property('uri'));
        } else {
            $query = query()
                ->match($node)
                ->returning($node->property($property->getUri()));
        }

        common_Logger::i('getPropertyValues(): ' . var_export($query->build(), true));
        $results = $this->getPersistence()->run($query->build());
        $values = [];
        foreach ($results as $result) {
            // @FIXME filter results according to language
            $values[] = (string) $result->current();
        }

        return $values;
    }

    public function getPropertyValuesByLg(core_kernel_classes_Resource $resource, core_kernel_classes_Property $property, $lg): core_kernel_classes_ContainerCollection
    {
        throw new common_Exception('Not implemented! ' . __FILE__ . ' line: ' . __LINE__);
    }

    public function setPropertyValue(core_kernel_classes_Resource $resource, core_kernel_classes_Property $property, $object, $lg = null): ?bool
    {
        $uri = $resource->getUri();
        $propertyUri = $property->getUri();
        // @FIXME if language dependent, first pull current node properties to update
        $object  = $object instanceof core_kernel_classes_Resource ? $object->getUri() : (string) $object;
        if (in_array($propertyUri, self::RELATIONSHIP_PROPERTIES)) {
            $query = <<<CYPHER
                MATCH
                  (a), (b)
                WHERE a.uri = "{$uri}" AND b.uri = "{$object}"
                CREATE (a)-[r:`{$propertyUri}`]->(b)
                RETURN type(r)
CYPHER;
        } else {
            $query = <<<CYPHER
            MATCH (n {uri: "{$uri}"})
            SET n.`{$propertyUri}` = "{$object}"
CYPHER;
        }
        common_Logger::i('setPropertyValue(): ' . var_export($query, true));
        $this->getPersistence()->run($query);

        return true;
    }

    public function setPropertiesValues(core_kernel_classes_Resource $resource, $properties): ?bool
    {
        if (!is_array($properties) || count($properties) == 0) {
            return false;
        }

        $node = node();
        $node->addProperty('uri', $resource->getUri());

        /** @var common_session_Session $session */
        $session = $this->getServiceLocator()->get(SessionService::SERVICE_ID)->getCurrentSession();

        $node->addProperty('updatedBy', (string)$session->getUser()->getIdentifier());
        $node->addProperty('http://www.tao.lu/Ontologies/TAO.rdf#UpdatedAt', procedure()::raw('timestamp'));

        $dataLanguage = $session->getDataLanguage();

        $collectedProperties = [];
        $collectedRelationships = [];
        foreach ($properties as $propertyUri => $value) {
            $property = $this->getModel()->getProperty($propertyUri);
            $lang = ($property->isLgDependent() ? $dataLanguage : '');

            if (!is_array($value)) {
                $value = [$value];
            }
            foreach ($value as $val) {
                // @TODO check if the property exists already
                if ($val instanceof core_kernel_classes_Resource || in_array($propertyUri, self::RELATIONSHIP_PROPERTIES)) {
                    $valUri = $val instanceof core_kernel_classes_Resource ? $val->getUri() : $val;
                    $currentValues = $collectedRelationships[$valUri] ?? [];
                    $collectedRelationships[$valUri] = array_merge($currentValues, [$propertyUri]);
                } else {
                    if ($lang) {
                        $val = $val . '@' . $lang;
                    }
                    if (!empty($collectedProperties[$val->getUri()])) {
                        $currentValue = $collectedProperties[$val->getUri()];
                        if (is_array($currentValue)) {
                            $collectedProperties[$val->getUri()] = array_merge($currentValue, [$val]);
                        } else {
                            $collectedProperties[$val->getUri()] = [$currentValue, $val];
                        }
                    } else {
                        $collectedProperties[$val->getUri()] = $val;
                    }
                }
            }
        }

        $node->addProperties($collectedProperties);
        foreach ($collectedRelationships as $target => $relationshipTypes) {
            foreach ($relationshipTypes as $type) {
                $node->relationshipTo(node()->withProperties(['uri' => $target]), $type);
            }
        }

        $query = query()->create($node)->build();

        common_Logger::i('setPropertiesValues(): ' . var_export($query, true));
        $result = $this->getModel()->getPersistence()->run($query);

        return true;
    }

    public function setPropertyValueByLg(core_kernel_classes_Resource $resource, core_kernel_classes_Property $property, $value, $lg): ?bool
    {
        throw new common_Exception('Not implemented! ' . __FILE__ . ' line: ' . __LINE__);
    }

    public function removePropertyValues(core_kernel_classes_Resource $resource, core_kernel_classes_Property $property, $options = []): ?bool
    {
        $uri = $resource->getUri();
        $propertyUri = $property->getUri();
        $conditions = [];
        $pattern = $options['pattern'] ?? null;
        $isLike = !empty($options['like']);
        if (!empty($pattern)) {
            if (!is_array($pattern)) {
                $pattern = [$pattern];
            }

            $multiCondition = "( ";
            foreach ($pattern as $index => $token) {
                if (empty($token)) {
                    continue;
                }
                if ($index > 0) {
                    $multiCondition .= ' OR ';
                }
                if ($isLike) {
                    $multiCondition .= "n.`{$propertyUri}` =~ '" . str_replace('*', '.*', $token) . "'";
                } else {
                    $multiCondition .= "n.`{$propertyUri}` = '$token'";
                }
            }
            $conditions[] = "{$multiCondition} ) ";
        }

        $assembledConditions = '';
        foreach ($conditions as $i => $additionalCondition) {
            if (empty($assembledConditions)) {
                $assembledConditions .= " WHERE ( {$additionalCondition} ) ";
            } else {
                $assembledConditions .= " AND ( {$additionalCondition} ) ";
            }
        }

        $query = <<<CYPHER
            MATCH (n {uri: "{$uri}"})
            {$assembledConditions}
            REMOVE n.`{$propertyUri}`
            RETURN n
CYPHER;

        // @FIXME if value is array, then query should be for update. Try to deduce if $prop->isLgDependent or isMultiple
        // @FIXME if property is represented as node relationship, query should remove that instead

        common_Logger::i('removePropertyValues(): ' . var_export($query, true));
        $this->getPersistence()->run($query);

        return true;
    }

    public function removePropertyValueByLg(core_kernel_classes_Resource $resource, core_kernel_classes_Property $property, $lg, $options = []): ?bool
    {
        throw new common_Exception('Not implemented! ' . __FILE__ . ' line: ' . __LINE__);
    }

    public function getRdfTriples(core_kernel_classes_Resource $resource): core_kernel_classes_ContainerCollection
    {
        throw new common_Exception('Not implemented! ' . __FILE__ . ' line: ' . __LINE__);
    }

    public function isWritable(core_kernel_classes_Resource $resource): bool
    {
        return $this->model->isWritable($resource);
    }

    public function getUsedLanguages(core_kernel_classes_Resource $resource, core_kernel_classes_Property $property): array
    {
        throw new common_Exception('Not implemented! ' . __FILE__ . ' line: ' . __LINE__);
    }

    public function duplicate(core_kernel_classes_Resource $resource, $excludedProperties = []): core_kernel_classes_Resource
    {
        throw new common_Exception('Not implemented! ' . __FILE__ . ' line: ' . __LINE__);
    }

    public function delete(core_kernel_classes_Resource $resource, $deleteReference = false): ?bool
    {
        // @FIXME does $deleteReference still make sense? Since detach delete removes relationships as well?

        $resourceNode = node()->withProperties(['uri' => $resource->getUri()]);
        $query = query()
            ->match($resourceNode)
            ->detachDelete($resourceNode)
            ->build();

        $result = $this->getPersistence()->run($query);
        // @FIXME handle failure, return false if no nodes/relationships affected

        return true;
    }

    public function getPropertiesValues(core_kernel_classes_Resource $resource, $properties): array
    {
        if (count($properties) == 0) {
            return [];
        }

        $uri = $resource->getUri();
        $query = <<<CYPHER
            MATCH (resource)-[relationshipTo]->(relatedResource)
            WHERE resource.uri = "{$uri}"
            RETURN resource, collect({relationship: type(relationshipTo), relatedResourceUri: relatedResource.uri}) AS relationships
CYPHER;

        common_Logger::i('getPropertiesValues(): ' . var_export($query, true));
        $results = $this->getPersistence()->run($query);
        $result = $results->first();

        $propertyUris = [];
        foreach ($properties as $property) {
            $uri = (is_string($property) ? $property : $property->getUri());
            $propertyUris[] = $uri;
            $returnValue[$uri] = [];
        }
        foreach ($result->get('resource')->getProperties() as $key => $value) {
            if (in_array($key, $propertyUris)) {
                $returnValue[$key][] = common_Utils::isUri($value)
                    ? $this->getModel()->getResource($value)
                    : new core_kernel_classes_Literal($value);
                // @FIXME language dependent values according to the language
            }
        }
        foreach ($result->get('relationships') as $relationship) {
            if (in_array($relationship['relationship'], $propertyUris)) {
                $returnValue[$relationship['relationship']][] = common_Utils::isUri($relationship['relatedResourceUri'])
                    ? $this->getModel()->getResource($relationship['relatedResourceUri'])
                    : new core_kernel_classes_Literal($relationship['relatedResourceUri']);
            }
        }

        return (array) $returnValue;
    }

    public function setType(core_kernel_classes_Resource $resource, core_kernel_classes_Class $class): ?bool
    {
        return $this->setPropertyValue($resource, $this->getModel()->getProperty(OntologyRdf::RDF_TYPE), $class);
    }

    public function removeType(core_kernel_classes_Resource $resource, core_kernel_classes_Class $class): ?bool
    {
        throw new common_Exception('Not implemented! ' . __FILE__ . ' line: ' . __LINE__);
    }

    /**
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->getModel()->getServiceLocator();
    }
}
