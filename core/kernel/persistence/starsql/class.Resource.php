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

use oat\generis\model\kernel\persistence\starsql\LanguageProcessor;
use oat\generis\model\OntologyRdf;
use oat\oatbox\session\SessionService;
use oat\oatbox\user\UserLanguageServiceInterface;
use WikibaseSolutions\CypherDSL\Clauses\SetClause;
use Zend\ServiceManager\ServiceLocatorInterface;

use function WikibaseSolutions\CypherDSL\node;
use function WikibaseSolutions\CypherDSL\parameter;
use function WikibaseSolutions\CypherDSL\procedure;
use function WikibaseSolutions\CypherDSL\query;
use function WikibaseSolutions\CypherDSL\raw;
use function WikibaseSolutions\CypherDSL\relationshipTo;
use function WikibaseSolutions\CypherDSL\variable;

class core_kernel_persistence_starsql_Resource implements core_kernel_persistence_ResourceInterface
{
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
                node()->withProperties(['uri' => $uriParameter = parameter()])
                    ->withLabels(['Resource'])
                    ->relationshipTo($relatedResource, OntologyRdf::RDF_TYPE)
            )
            ->returning($relatedResource->property('uri'))
            ->build();

        $results = $this->getPersistence()->run($query, [$uriParameter->getParameter() => $resource->getUri()]);
        $returnValue = [];
        foreach ($results as $result) {
            $uri = $result->current();
            $returnValue[$uri] = $this->getModel()->getClass($uri);
        }

        return (array) $returnValue;
    }

    public function getPropertyValues(
        core_kernel_classes_Resource $resource,
        core_kernel_classes_Property $property,
        $options = []
    ): array {
        if (isset($options['last'])) {
            throw new core_kernel_persistence_Exception('Option \'last\' no longer supported');
        }

        $node = node()
            ->withProperties(['uri' => $uriParameter = parameter()])
            ->withLabels(['Resource']);
        if ($property->isRelationship()) {
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

        $results = $this->getPersistence()->run(
            $query->build(),
            [$uriParameter->getParameter() => $resource->getUri()]
        );
        $values = [];
        $selectedLanguage = $options['lg'] ?? null;
        $dataLanguage = $this->getDataLanguage();
        $defaultLanguage = $this->getDefaultLanguage();

        foreach ($results as $result) {
            $value = $result->current();
            if ($value === null) {
                continue;
            }
            if (is_iterable($value)) {
                if (isset($selectedLanguage)) {
                    $values = array_merge(
                        $values,
                        $this->getLanguageProcessor()->filterByLanguage($value, [$selectedLanguage])
                    );
                } else {
                    $values = array_merge(
                        $values,
                        $this->getLanguageProcessor()->filterByAvailableLanguage(
                            $value,
                            $dataLanguage,
                            $defaultLanguage
                        )
                    );
                }
            } else {
                $values[] = $this->getLanguageProcessor()->parseTranslatedValue($value);
            }
        }

        return $values;
    }

    public function getPropertyValuesByLg(
        core_kernel_classes_Resource $resource,
        core_kernel_classes_Property $property,
        $lg
    ): core_kernel_classes_ContainerCollection {
        $options =  ['lg' => $lg];

        $returnValue = new core_kernel_classes_ContainerCollection($resource);
        foreach ($this->getPropertyValues($resource, $property, $options) as $value) {
            $returnValue->add(common_Utils::toResource($value));
        }

        return $returnValue;
    }

    public function setPropertyValue(
        core_kernel_classes_Resource $resource,
        core_kernel_classes_Property $property,
        $object,
        $lg = null
    ): ?bool {
        $uri = $resource->getUri();
        $propertyUri = $property->getUri();
        if ($object instanceof core_kernel_classes_Resource) {
            $object = $object->getUri();
        } else {
            $object = (string) $object;
            if ($property->isLgDependent()) {
                $lang = ((null != $lg)
                    ? $lg
                    : $this->getDataLanguage());
                if (!empty($lang)) {
                    $object .= '@' . $lang;
                }
            }
        }
        if ($property->isRelationship()) {
            $query = <<<CYPHER
                MATCH
                  (a:Resource), (b:Resource)
                WHERE a.uri = \$uri AND b.uri = \$object
                CREATE (a)-[r:`{$propertyUri}`]->(b)
                RETURN type(r)
CYPHER;
        } elseif ($property->isLgDependent()) {
            $query = <<<CYPHER
            MATCH (n:Resource {uri: \$uri})
            SET n.`{$propertyUri}` = coalesce(n.`{$propertyUri}`, []) + \$object
CYPHER;
        } else {
            $query = <<<CYPHER
            MATCH (n:Resource {uri: \$uri})
            SET n.`{$propertyUri}` = \$object
CYPHER;
        }
        $this->getPersistence()->run($query, ['uri' => $uri, 'object' => $object]);

        return true;
    }

    public function setPropertiesValues(core_kernel_classes_Resource $resource, $properties): ?bool
    {
        if (!is_array($properties) || count($properties) == 0) {
            return false;
        }

        $parameters = [];
        $node = node();
        $node->addLabel('Resource');
        $node->addProperty('uri', $uriParameter = parameter());
        $parameters[$uriParameter->getParameter()] = $resource->getUri();

        /** @var common_session_Session $session */
        $session = $this->getServiceLocator()->get(SessionService::SERVICE_ID)->getCurrentSession();

        $setClause = new SetClause();
        $setClause->add(
            $node->property('http://www.tao.lu/Ontologies/TAO.rdf#UpdatedBy')
                ->replaceWith($authorParameter = parameter())
        );
        $parameters[$authorParameter->getParameter()] = (string)$session->getUser()->getIdentifier();
        $setClause->add(
            $node->property('http://www.tao.lu/Ontologies/TAO.rdf#UpdatedAt')
                ->replaceWith(procedure()::raw('timestamp'))
        );

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
                if ($val instanceof core_kernel_classes_Resource || $property->isRelationship()) {
                    $valUri = $val instanceof core_kernel_classes_Resource ? $val->getUri() : $val;
                    if (empty($valUri)) {
                        continue;
                    }
                    $currentValues = $collectedRelationships[$valUri] ?? [];
                    $collectedRelationships[$valUri] = array_merge($currentValues, [$propertyUri]);
                } else {
                    if ($lang) {
                        $val .= '@' . $lang;
                    }
                    if (!empty($collectedProperties[$propertyUri])) {
                        $currentValue = $collectedProperties[$propertyUri];
                        if (is_array($currentValue)) {
                            $collectedProperties[$propertyUri] = array_merge($currentValue, [$val]);
                        } else {
                            $collectedProperties[$propertyUri] = [$currentValue, $val];
                        }
                    } else {
                        $collectedProperties[$propertyUri] = $val;
                    }
                }
            }
        }

        foreach ($collectedProperties as $propUri => $values) {
            $setClause->add($node->property($propUri)->replaceWith($propertyParameter = parameter()));
            $parameters[$propertyParameter->getParameter()] = $values;
        }
        $relatedResources = [];
        $merges = [];
        $nodeCopy = node()->withVariable($node->getVariable());
        foreach ($collectedRelationships as $target => $relationshipTypes) {
            foreach ($relationshipTypes as $type) {
                $variableForRelatedResource = variable();
                $nodeForRelationship = node()->withVariable($variableForRelatedResource);
                $relatedResource = node('Resource')
                    ->withProperties(['uri' => $relatedUriParameter = parameter()])
                    ->withVariable($variableForRelatedResource);
                $parameters[$relatedUriParameter->getParameter()] = $target;
                $merges[] = $nodeCopy->relationshipTo($nodeForRelationship, $type);
                $relatedResources[] = $relatedResource;
            }
        }

        $query = query();
        foreach ($relatedResources as $relResource) {
            $query->match($relResource);
        }

        $query->merge($node, $setClause, $setClause);
        foreach ($merges as $mergeNode) {
            $query->merge($mergeNode);
        }

        $query = $query->build();

        $result = $this->getModel()->getPersistence()->run($query, $parameters);

        return true;
    }

    public function setPropertyValueByLg(
        core_kernel_classes_Resource $resource,
        core_kernel_classes_Property $property,
        $value,
        $lg
    ): ?bool {
        return $this->setPropertyValue($resource, $property, $value, $lg);
    }

    public function removePropertyValues(
        core_kernel_classes_Resource $resource,
        core_kernel_classes_Property $property,
        $options = []
    ): ?bool {
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

        if (!$property->isRelationship()) {
            $query = <<<CYPHER
                MATCH (n:Resource {uri: "{$uri}"})
                {$assembledConditions}
                REMOVE n.`{$propertyUri}`
                RETURN n
CYPHER;
        } else {
            $query = <<<CYPHER
                MATCH (n:Resource {uri: "{$uri}"})-[p:`{$propertyUri}`]->()
                {$assembledConditions}
                DELETE p
                RETURN n
CYPHER;
        }

        //@FIXME if value is array, then query should be for update. Try to deduce if $prop->isLgDependent or isMultiple
        //@FIXME if property is represented as node relationship, query should remove that instead

        $this->getPersistence()->run($query);

        return true;
    }

    public function removePropertyValueByLg(
        core_kernel_classes_Resource $resource,
        core_kernel_classes_Property $property,
        $lg,
        $options = []
    ): ?bool {
        if (!$property->isLgDependent()) {
            return $this->removePropertyValues($resource, $property, $options);
        }

        $node = node('Resource')->withProperties(['uri' => $uriParameter = parameter()]);
        $property = $node->property($property->getUri());
        $removeKeyProcedure = raw(sprintf(
            "[item in %s WHERE NOT item ENDS WITH '@%s']",
            $property->toQuery(),
            $lg
        ));

        $query = query()
            ->match($node)
            ->where($property->isNotNull())
            ->set($property->replaceWith($removeKeyProcedure))
            ->build();

        $this->getPersistence()->run($query, [$uriParameter->getParameter() => $resource->getUri()]);

        return true;
    }

    public function getRdfTriples(core_kernel_classes_Resource $resource): core_kernel_classes_ContainerCollection
    {
        $relationship = relationshipTo()
            ->withVariable($relationshipVar = variable())
            ->withMinHops(0)
            ->withMaxHops(1);
        $relatedNode = node()->withLabels(['Resource'])->withVariable($relatedNodeVar = variable());
        $node = node()->withProperties(['uri' => $uriParameter = parameter()])
            ->withVariable($nodeVar = variable())
            ->withLabels(['Resource'])
            ->relationship($relationship, $relatedNode);
        $query = query()
            ->match($node)
            ->returning([$nodeVar, $relatedNodeVar, $relationshipVar])
            ->build();

        $results = $this->getPersistence()->run($query, [$uriParameter->getParameter() => $resource->getUri()]);
        $returnValue = new core_kernel_classes_ContainerCollection(new common_Object(__METHOD__));
        $nodeProcessed = false;
        foreach ($results as $result) {
            $resultNode = $result->get($nodeVar->getName());
            /** @var \Laudis\Neo4j\Types\CypherMap $resultRelationship */
            $resultRelationship = $result->get($relationshipVar->getName());
            $resultRelatedNode = $result->get($relatedNodeVar->getName());
            if (!$nodeProcessed) {
                $returnValue = $this->buildTriplesFromNode($returnValue, $resource->getUri(), $resultNode);
                $nodeProcessed = true;
            }
            if (!$resultRelationship->isEmpty()) {
                $resultRelationship = $resultRelationship->first();
                $triple = new core_kernel_classes_Triple();
                $triple->subject = $resource->getUri();
                $triple->predicate = $resultRelationship->getType();
                $triple->object = $resultRelatedNode->getProperty('uri');
                $returnValue->add($triple);
            }
        }

        return $returnValue;
    }

    public function isWritable(core_kernel_classes_Resource $resource): bool
    {
        return $this->model->isWritable($resource);
    }

    public function getUsedLanguages(
        core_kernel_classes_Resource $resource,
        core_kernel_classes_Property $property
    ): array {
        $node = node()->withProperties(['uri' => $uriParameter = parameter()])
            ->withLabels(['Resource']);
        $query = query()
            ->match($node)
            ->returning($node->property($property->getUri()))
            ->build();

        $results = $this->getPersistence()->run($query, [$uriParameter->getParameter() => $resource->getUri()]);
        $foundLanguages = [];
        foreach ($results as $result) {
            $values = $result->current();
            if (!is_iterable($values)) {
                $values = [$values];
            }
            foreach ($values as $value) {
                preg_match(LanguageProcessor::LANGUAGE_TAGGED_VALUE_PATTERN, $value, $matches);
                if (isset($matches[2])) {
                    $foundLanguages[] = $matches[2];
                }
            }
        }

        return (array) $foundLanguages;
    }

    public function duplicate(
        core_kernel_classes_Resource $resource,
        $excludedProperties = []
    ): core_kernel_classes_Resource {
        throw new common_Exception('Not implemented! ' . __FILE__ . ' line: ' . __LINE__);
    }

    public function delete(core_kernel_classes_Resource $resource, $deleteReference = false): ?bool
    {
        // @FIXME does $deleteReference still make sense? Since detach delete removes relationships as well?

        $resourceNode = node()
            ->withProperties(['uri' => $uriParameter = parameter()])
            ->withLabels(['Resource']);
        $query = query()
            ->match($resourceNode)
            ->detachDelete($resourceNode)
            ->build();

        $result = $this->getPersistence()->run($query, [$uriParameter->getParameter() => $resource->getUri()]);
        // @FIXME handle failure, return false if no nodes/relationships affected

        return true;
    }

    public function getPropertiesValues(core_kernel_classes_Resource $resource, $properties): array
    {
        if (count($properties) == 0) {
            return [];
        }

        $query = <<<CYPHER
            MATCH (resource:Resource)-[relationshipTo]->(relatedResource:Resource)
            WHERE resource.uri = \$uri
            RETURN resource,
                collect({relationship: type(relationshipTo), relatedResourceUri: relatedResource.uri}) AS relationships
CYPHER;

        $results = $this->getPersistence()->run($query, ['uri' => $resource->getUri()]);
        if ($results->isEmpty()) {
            return [];
        }
        $result = $results->first();

        $propertyUris = [];
        foreach ($properties as $property) {
            $uri = (is_string($property) ? $property : $property->getUri());
            $propertyUris[] = $uri;
            $returnValue[$uri] = [];
        }
        $dataLanguage = $this->getDataLanguage();
        $defaultLanguage = $this->getDefaultLanguage();
        foreach ($result->get('resource')->getProperties() as $key => $value) {
            if (in_array($key, $propertyUris)) {
                if (is_iterable($value)) {
                    $returnValue[$key] =
                        array_map(
                            fn($value) => $this->formatValue($value),
                            array_merge(
                                $returnValue[$key] ?? [],
                                $this->getLanguageProcessor()->filterByLanguage(
                                    $value,
                                    [$dataLanguage, $defaultLanguage]
                                )
                            )
                        );
                } else {
                    $returnValue[$key][] = $this->formatValue(
                        $value,
                        [$this->getLanguageProcessor(), 'parseTranslatedValue']
                    );
                }
            }
        }
        foreach ($result->get('relationships') as $relationship) {
            if (in_array($relationship['relationship'], $propertyUris)) {
                $returnValue[$relationship['relationship']][] = $this->formatValue($relationship['relatedResourceUri']);
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
        $typeRelationship = relationshipTo()->withTypes([OntologyRdf::RDF_TYPE]);
        $classNode = node()->withProperties(['uri' => $classUriParameter = parameter()])
            ->withLabels(['Resource']);
        $node = node()->withProperties(['uri' => $uriParameter = parameter()])
            ->withLabels(['Resource'])
            ->relationship($typeRelationship, $classNode);
        $query = query()
            ->match($node)
            ->delete($typeRelationship)
            ->build();

        $this->getPersistence()->run($query, [
            $uriParameter->getParameter() => $resource->getUri(),
            $classUriParameter->getParameter() => $class->getUri()
        ]);

        return true;
    }

    /**
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->getModel()->getServiceLocator();
    }

    protected function getDataLanguage()
    {
        return $this->getServiceLocator()->get(SessionService::SERVICE_ID)->getCurrentSession()->getDataLanguage();
    }

    protected function getDefaultLanguage()
    {
        return $this->getServiceLocator()->get(UserLanguageServiceInterface::SERVICE_ID)->getDefaultLanguage();
    }

    protected function getLanguageProcessor(): LanguageProcessor
    {
        return $this->getServiceLocator()->getContainer()->get(LanguageProcessor::class);
    }

    private function buildTriplesFromNode(core_kernel_classes_ContainerCollection $tripleCollection, $uri, $resultNode)
    {
        foreach ($resultNode->getProperties() as $propKey => $propValue) {
            if ($propKey === 'uri') {
                continue;
            }
            $triple = new core_kernel_classes_Triple();
            $triple->subject = $uri;
            $triple->predicate = $propKey;
            if (is_iterable($propValue)) {
                foreach ($propValue as $value) {
                    $langTriple = clone $triple;
                    $langTriple->lg = $this->getLanguageProcessor()->parseTranslatedLang($value);
                    $langTriple->object = $this->getLanguageProcessor()->parseTranslatedValue($value);
                    $tripleCollection->add($langTriple);
                }
            } else {
                $triple->lg = $this->getLanguageProcessor()->parseTranslatedLang($propValue);
                $triple->object = $this->getLanguageProcessor()->parseTranslatedValue($propValue);
                $tripleCollection->add($triple);
            }
        }

        return $tripleCollection;
    }

    private function formatValue($value, array $literalValueProcessingCallback = [])
    {
        return common_Utils::isUri($value) ?
            $this->getModel()->getResource($value)
            : new core_kernel_classes_Literal(
                !empty($literalValueProcessingCallback)
                    ? call_user_func($literalValueProcessingCallback, $value)
                    : $value
            );
    }
}
