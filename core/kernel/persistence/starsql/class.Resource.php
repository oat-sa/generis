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
use WikibaseSolutions\CypherDSL\Clauses\SetClause;
use WikibaseSolutions\CypherDSL\Clauses\WhereClause;
use WikibaseSolutions\CypherDSL\Query;
use Zend\ServiceManager\ServiceLocatorInterface;

use function WikibaseSolutions\CypherDSL\literal;
use function WikibaseSolutions\CypherDSL\node;
use function WikibaseSolutions\CypherDSL\parameter;
use function WikibaseSolutions\CypherDSL\procedure;
use function WikibaseSolutions\CypherDSL\query;
use function WikibaseSolutions\CypherDSL\relationshipTo;
use function WikibaseSolutions\CypherDSL\variable;

class core_kernel_persistence_starsql_Resource implements core_kernel_persistence_ResourceInterface
{
    private const LANGUAGE_TAGGED_VALUE_PATTERN = "/^(.*)@([a-zA-Z\\-]{5,6})$/";

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

    public function getPropertyValues(core_kernel_classes_Resource $resource, core_kernel_classes_Property $property, $options = []): array
    {
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

        $results = $this->getPersistence()->run($query->build(), [$uriParameter->getParameter() => $resource->getUri()]);
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
                    $values = array_merge($values, $this->filterRecordsByLanguage($value, [$selectedLanguage]));
                } else {
                    $values = array_merge($values, $this->filterRecordsByAvailableLanguage($value, $dataLanguage, $defaultLanguage));
                }
            } else {
                $values[] = $this->parseTranslatedValue($value);
            }
        }

        return $values;
    }

    public function getPropertyValuesByLg(core_kernel_classes_Resource $resource, core_kernel_classes_Property $property, $lg): core_kernel_classes_ContainerCollection
    {
        $options =  ['lg' => $lg];

        $returnValue = new core_kernel_classes_ContainerCollection($resource);
        foreach ($this->getPropertyValues($resource, $property, $options) as $value) {
            $returnValue->add(common_Utils::toResource($value));
        }

        return $returnValue;
    }

    public function setPropertyValue(core_kernel_classes_Resource $resource, core_kernel_classes_Property $property, $object, $lg = null): ?bool
    {
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
            //TODO Clean. I am still working and testing
            $relationship = OntologyRdfs::RDFS_SUBCLASSOF;
            $aResource = node()
                ->withLabels(['Resource'])
                ->withVariable("a");
            $bResource = node()
                ->withLabels(['Resource'])
                ->withVariable("b");

            $a = node()
                ->withVariable("a");
            $b = node()
                ->withVariable("b");
            $r = relationshipTo()
                ->withVariable("r");
            $rURI = relationshipTo()
                ->withTypes([$propertyUri])
                ->withVariable("r");
            $type = procedure()::raw('type', $r);

            $querydsl = query()
                ->match([$aResource, $bResource])
                ->where([$a->property('uri')->equals($uri), $b->property('uri')->equals($object)])
                ->create($a->relationship($rURI, $b))
                ->returning($type)
                ->build();
            $results = $this->getPersistence()->run($querydsl,);

//            //TODO delete old query
//            $query = <<<CYPHER
//                MATCH
//                  (a:Resource), (b:Resource)
//                WHERE a.uri = \$uri AND b.uri = \$object
//                CREATE (a)-[r:`{$propertyUri}`]->(b)
//                RETURN type(r)
//CYPHER;

//            $this->getPersistence()->run($query, ['uri' => $uri, 'object' => $object]);
        } elseif ($property->isLgDependent()) {
////            TODO Clean up and finish query
//            $uri = $resource->getUri();
//            $object = $object->getUri();
////            TODO Clean up and delete all query
//            $query = <<<CYPHER
//MATCH (n:Resource {uri: \$uri})
//RETURN n.`{$propertyUri}`
//CYPHER;
//
//            $results =  $this->getPersistence()->run($query, ['uri' => $uri, 'object' => $object]);
//            $returnValue = [];
//            foreach ($results as $result) {
//                $exists = $result->values();
//            }
//            MATCH (n:Resource {uri: \$uri})
//            SET n.`{$propertyUri}` = coalesce(n.`{$propertyUri}`, []) + \$object

            $n = node()
                ->withLabels(['Resource'])
                ->withVariable("n")
                ->withProperties(["uri" => $uri]);
            $procedure = procedure()::raw('coalesce', [$n->property($uri), []]);
            $parameter = parameter();
            $expression = Query::rawExpression(sprintf('%s+ $%s', $procedure->toQuery(), $parameter->getParameter()));
            $querydsl = query()
                ->match($n)
                ->set(
                    $n->property($uri)->replaceWith($expression)
                )
                ->returning($n)
                ->build();

            $resultsdls = $this->getPersistence()->run($querydsl, [$parameter->getParameter() => $object]);


//            //TODO Clean up and delete all query
//            $query = <<<CYPHER
//MATCH (n:Resource {uri: \$uri})
//RETURN n.`{$propertyUri}`
//CYPHER;
//
//            $results =  $this->getPersistence()->run($query, ['uri' => $uri, 'object' => $object]);

            // @FIXME If there is multiple values and we are

//            //TODO Clean up and delete all query
//            $query = <<<CYPHER
//            MATCH (n:Resource {uri: \$uri})
//            SET n.`{$propertyUri}` = coalesce(n.`{$propertyUri}`, []) + \$object
//CYPHER;
//
//            $this->getPersistence()->run($query, ['uri' => $uri, 'object' => $object]);
        } else {
            //TODO: Delete old query and  comments
            $ndsl = node()
                ->withLabels(['Resource'])
                ->withVariable("n")
                ->withProperties(["uri" => $uri]);
            $querydls = query()
                ->match($ndsl)
                ->set($ndsl->property($propertyUri)->replaceWith($object))->build();
            $results = $this->getPersistence()->run($querydls);
//            $query = <<<CYPHER
//            MATCH (n:Resource {uri: \$uri})
//            SET n.`{$propertyUri}` = \$object
//CYPHER;
//
//            $this->getPersistence()->run($query, ['uri' => $uri, 'object' => $object]);
        }
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
        foreach ($collectedRelationships as $target => $relationshipTypes) {
            foreach ($relationshipTypes as $type) {
                $variableForRelatedResource = variable();
                $nodeForRelationship = node()->withVariable($variableForRelatedResource);
                $relatedResource = node('Resource')->withProperties(['uri' => $relatedUriParameter = parameter()])->withVariable($variableForRelatedResource);
                $parameters[$relatedUriParameter->getParameter()] = $target;
                $node = $node->relationshipTo($nodeForRelationship, $type);
                $relatedResources[] = $relatedResource;
            }
        }

        $query = query();
        foreach ($relatedResources as $relResource) {
            $query->match($relResource);
        }
        $query = $query->merge($node, $setClause, $setClause)->build();

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

    public function removePropertyValuesOriginal(
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

        $query = <<<CYPHER
            MATCH (n:Resource {uri: "{$uri}"})
            {$assembledConditions}
            REMOVE n.`{$propertyUri}`
            RETURN n
CYPHER;

        // @FIXME if value is array, then query should be for update. Try to deduce if $prop->isLgDependent or isMultiple
        // @FIXME if property is represented as node relationship, query should remove that instead

        $this->getPersistence()->run($query);

        return true;
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

//            $multiCondition = "( ";
//            foreach ($pattern as $index => $token) {
//                if (empty($token)) {
//                    continue;
//                }
//                if ($index > 0) {
//                    $multiCondition .= ' OR ';
//                }
//                if ($isLike) {
//                    $multiCondition .= "n.`{$propertyUri}` =~ '" . str_replace('*', '.*', $token) . "'";
//                } else {
//                    $multiCondition .= "n.`{$propertyUri}` = '$token'";
//                }
//            }
//            $conditions[] = "{$multiCondition} ) ";
//        }
//
//        $assembledConditions = '';
//        foreach ($conditions as $i => $additionalCondition) {
//            if (empty($assembledConditions)) {
//                $assembledConditions .= " WHERE ( {$additionalCondition} ) ";
//            } else {
//                $assembledConditions .= " AND ( {$additionalCondition} ) ";
//            }
        }
//TODO Delete original query when everthing is implemented
//        $query = <<<CYPHER
//            MATCH (n:Resource {uri: "{$uri}"})
//            {$assembledConditions}
//            REMOVE n.`{$propertyUri}`
//            RETURN n
//CYPHER;
//        $results =$this->getPersistence()->run($query);
        $parameter = parameter();
        $nResource = node()
            ->withLabels(['Resource'])
            ->withVariable("n")
            ->withProperties(["uri" => $uri]);;

        $n = node()
            ->withVariable("n")
            ->withProperties(["uri" => $uri]);;


        $whereCondition = [];

        if (!isset($pattern)) {
            $querydls = query()
                ->match($nResource)
                ->remove($n->property($propertyUri))
                ->returning($n)
                ->build();
        } else {
            if ($isLike) {
                $whereClause = new WhereClause();
                foreach ($pattern as $index => $token) {
                    if (!is_array($pattern[$index])) {
                        $pattern[$index] = [$pattern[$index]];
                    }
                    $clause = null;
                    foreach ($pattern[$index] as $i => $word) {
                        $word = str_replace('*', '.*', $word);
                        $queryCondition = $n->property($propertyUri)->regex($word);
                        $clause = ($clause === null)
                            ? $queryCondition
                            : $clause->or($queryCondition);
                    }
                    if ($clause != null) {
                        $whereClause->addExpression($clause);
                    }
                }
            } else {

                foreach ($pattern as $index => $token) {
                    if (!is_array($pattern[$index])) {
                        $pattern[$index] = [$pattern[$index]];
                    }

                    $whereCondition[] = $n->property($propertyUri)->in($pattern[$index]);
                }
            }
            $querydls = query()
                ->match($nResource)
//                ->where($whereCondition)
                ->addClause($whereClause)
                ->remove($n->property($propertyUri))
                ->returning($n)
                ->build();
        }
        $results = $this->getPersistence()->run($querydls, [$parameter->getParameter() => 'e']);

//        if ($$property->isMultiple() && $property->isLgDependent()){
//
//        }
//        elseif ($property->isRelationship()){
//            //
//        }
        // @FIXME if value is array, then query should be for update. Try to deduce if $prop->isLgDependent or isMultiple
        // @FIXME if property is represented as node relationship, query should remove that instead

//        $this->getPersistence()->run($query);

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
                preg_match(self::LANGUAGE_TAGGED_VALUE_PATTERN, $value, $matches);
                if (isset($matches[2])) {
                    $foundLanguages[] = $matches[2];
                }
            }
        }

        return (array) $foundLanguages;
    }

    public function duplicate(core_kernel_classes_Resource $resource, $excludedProperties = []): core_kernel_classes_Resource
    {
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

        $uriParameter = parameter();

        $relatedResource = node('Resource')->withVariable("relatedResource");
        $queryResource = node()
            ->withLabels(['Resource'])
            ->withVariable("resource");

        $params = literal()::map([
            'relationship' => procedure()::raw('type', Query::variable('relationshipTo')),
            'relatedResourceUri' => $relatedResource->property('uri')
        ]);

        $procedure = procedure()::raw('collect', $params)->alias('relationships');
        $query = query()
            ->match($queryResource->relationshipTo($relatedResource, null, null, "relationshipTo"))
            ->where($queryResource->property('uri')->equals($uriParameter))
            ->returning([$queryResource, $procedure])
            ->build();


        $results = $this->getPersistence()->run($query, [$uriParameter->getParameter() => $resource->getUri()]);
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
                    $returnValue[$key] = array_merge(
                        $returnValue[$key] ?? [],
                        $this->filterRecordsByLanguage($value, [$dataLanguage, $defaultLanguage])
                    );
                } else {
                    $returnValue[$key][] = common_Utils::isUri($value)
                        ? $this->getModel()->getResource($value)
                        : new core_kernel_classes_Literal($this->parseTranslatedValue($value));
                }
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

    protected function getDataLanguage()
    {
        return $this->getServiceLocator()->get(SessionService::SERVICE_ID)->getCurrentSession()->getDataLanguage();
    }

    protected function getDefaultLanguage()
    {
        return $this->getServiceLocator()->get(UserLanguageServiceInterface::SERVICE_ID)->getDefaultLanguage();
    }

    private function filterRecordsByLanguage($entries, $allowedLanguages): array
    {
        $filteredValues = [];
        foreach ($entries as $entry) {
            // collect all entries with matching language or without language
            $matchSuccess = preg_match(self::LANGUAGE_TAGGED_VALUE_PATTERN, $entry, $matches);
            if (!$matchSuccess) {
                $filteredValues[] = $entry;
            } elseif (isset($matches[2]) && in_array($matches[2], $allowedLanguages, true)) {
                $filteredValues[] = $matches[1];
            }
        }

        return $filteredValues;
    }

    private function filterRecordsByAvailableLanguage($entries, $dataLanguage, $defaultLanguage): array
    {
        $fallbackLanguage = '';

        $sortedResults = [
            $dataLanguage => [],
            $defaultLanguage => [],
            $fallbackLanguage => []
        ];

        foreach ($entries as $entry) {
            $matchSuccess = preg_match(self::LANGUAGE_TAGGED_VALUE_PATTERN, $entry, $matches);
            $entryLang = $matches[2] ?? '';
            $sortedResults[$entryLang][] = [
                'value' => $matches[1] ?? $entry,
                'language' => $entryLang
            ];
        }

        $languageOrderedEntries = array_merge(
            $sortedResults[$dataLanguage],
            (count($sortedResults) > 2) ? $sortedResults[$defaultLanguage] : [],
            $sortedResults[$fallbackLanguage]
        );

        $returnValue = [];
        if (count($languageOrderedEntries) > 0) {
            $previousLanguage = $languageOrderedEntries[0]['language'];

            foreach ($languageOrderedEntries as $value) {
                if ($value['language'] == $previousLanguage) {
                    $returnValue[] = $value['value'];
                } else {
                    break;
                }
            }
        }

        return (array) $returnValue;
    }

    private function parseTranslatedValue($value): string
    {
        preg_match(self::LANGUAGE_TAGGED_VALUE_PATTERN, (string)$value, $matches);

        return $matches[1] ?? (string) $value;
    }
}
