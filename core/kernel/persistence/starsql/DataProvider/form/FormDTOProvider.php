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
 * Copyright (c) 2023 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */

namespace oat\generis\model\kernel\persistence\starsql\DataProvider\form;

use common_persistence_Persistence;
use core_kernel_classes_Property;
use oat\generis\model\data\Ontology;
use oat\generis\model\GenerisRdf;
use oat\generis\model\kernel\persistence\DataProvider\form\DTO\FormDTO;
use oat\generis\model\kernel\persistence\DataProvider\form\FormDTOProviderInterface;
use oat\generis\model\kernel\persistence\starsql\helper\RecordProcessor;
use oat\generis\model\OntologyRdf;
use oat\generis\model\OntologyRdfs;

use function WikibaseSolutions\CypherDSL\node;
use function WikibaseSolutions\CypherDSL\query;
use function WikibaseSolutions\CypherDSL\relationshipTo;

class FormDTOProvider implements FormDTOProviderInterface
{
    private const PROPERTIES_WITHOUT_OPTIONS = [
        OntologyRdfs::RDFS_LITERAL,
        OntologyRdfs::RDFS_CLASS,
        OntologyRdfs::RDFS_RESOURCE,
        GenerisRdf::CLASS_GENERIS_FILE,
    ];

    private common_persistence_Persistence $persistence;
    private RecordProcessor $recordProcessor;

    public function __construct(Ontology $ontology, RecordProcessor $recordProcessor)
    {
        $this->persistence = $ontology->getPersistence();
        $this->recordProcessor = $recordProcessor;
    }

    public function get(string $classUri, string $topClassUri, string $elementUri, string $language): ?FormDTO
    {
        $formData = [];
        $ranges = [];
        $relationProperties = [];
        $notRelationProperties = [];
        $passNextProperty = false;
        $listRanges = $this->getListRanges();
        $propertiesData = $this->getPropertiesData($classUri);
        foreach ($propertiesData as $propertyData) {
            if (
                $propertyData['property'] === null ||
                ($passNextProperty && $propertyData['property'] !== 'http://www.w3.org/2000/01/rdf-schema#label')
            ) {
                continue;
            }
            $propertyData['label'] = $this->recordProcessor->filterRecordsByAvailableLanguage(
                $propertyData['label'],
                $language,
                $language
            )[0] ?? null;
            $propertyData['isList'] = in_array($propertyData['range'], $listRanges);
            $propertyData['validationRule'] = $propertyData['validationRule'] !== null ?
                $propertyData['validationRule'] :
                [];
            if ($this->isPropertyRelation($propertyData['property'], $propertyData['range'])) {
                $relationProperties[] = $propertyData['property'];
            } else {
                $notRelationProperties[] = $propertyData['property'];
            }

            $formData[$propertyData['property']] = $propertyData;
            $formData[$propertyData['property']]['value'] = [];
            if (
                !in_array($propertyData['range'], $ranges) &&
                !in_array($propertyData['range'], self::PROPERTIES_WITHOUT_OPTIONS)
            ) {
                $ranges[] = $propertyData['range'];
            }
            if ($propertyData['class'] === $topClassUri) {
                $passNextProperty = true;
            }
        }

        $optionsData = $this->getOptionsData($ranges);
        foreach ($optionsData as $optionData) {
            foreach ($formData as $propertyUri => $propertyData) {
                if ($propertyData['range'] === $optionData['range']) {
                    $formData[$propertyUri]['options'][$optionData['option']] =
                        [
                            'uri' => $optionData['option'],
                            'level' => $optionData['level'],
                            'label' => $this->recordProcessor->filterRecordsByAvailableLanguage(
                                $optionData['label'],
                                $language,
                                $language
                            )[0] ?? null
                        ];
                }
            }
        }

        $propertiesValues = $this->getPropertiesValues($elementUri, $relationProperties, $notRelationProperties);
        foreach ($formData as $i => $propertyData) {
            foreach ($propertiesValues as $result) {
                foreach ($result as $propertyId => $propertyValue) {
                    if (!str_contains($propertyId, $propertyData['property'])) {
                        continue;
                    }
                    if (!is_array($propertyValue) && $propertyValue !== null) {
                        if (!in_array($propertyValue, $formData[$i]['value'])) {
                            $formData[$i]['value'][] = $propertyValue;
                        }
                    } else {
                        $value = $this->recordProcessor->filterRecordsByAvailableLanguage(
                            $propertyValue,
                            $language,
                            $language
                        )[0] ?? null;
                        if ($value !== null && !in_array($value, $formData[$i]['value'])) {
                            $formData[$i]['value'][] = $value;
                        }
                    }
                }
            }
        }

        return new FormDTO($formData);
    }

    private function getListRanges(): array
    {
        $query = <<<CYPHER
MATCH (startNode:Resource {uri: 'http://www.tao.lu/Ontologies/TAO.rdf#List'}) 
MATCH (listRangeNode)-[:`http://www.w3.org/2000/01/rdf-schema#subClassOf`*]->(startNode) 
RETURN listRangeNode.uri
CYPHER;

        return array_column($this->persistence->run($query)->toRecursiveArray(), 'descendantNode.uri');
    }

    private function getPropertiesData(string $classUri): array
    {
        $query = <<<CYPHER
MATCH (classNode:Resource {uri: \$uri})
OPTIONAL MATCH (propertyNode)-[:`http://www.w3.org/2000/01/rdf-schema#domain`]->(classNode)
OPTIONAL MATCH (propertyNode)-[:`http://www.w3.org/2000/01/rdf-schema#range`]->(rangeResource)
OPTIONAL MATCH (propertyNode)-[:`http://www.tao.lu/datatypes/WidgetDefinitions.rdf#widget`]->(widgetResource)
RETURN distinct 
classNode.uri as class, 
propertyNode.uri as property, 
rangeResource.uri as range, 
widgetResource.uri as widget, 
propertyNode.`http://www.w3.org/2000/01/rdf-schema#label` as label,
propertyNode.`http://www.tao.lu/Ontologies/generis.rdf#validationRule` as validationRule,
propertyNode.`http://www.tao.lu/Ontologies/TAO.rdf#TAOGUIOrder` as guiOrder
UNION
MATCH (classNode:Resource {uri: \$uri})
MATCH (classNode)-[:`http://www.w3.org/2000/01/rdf-schema#subClassOf`*]->(classAncestorNode)
OPTIONAL MATCH (propertyNode)-[:`http://www.w3.org/2000/01/rdf-schema#domain`]->(classAncestorNode)
OPTIONAL MATCH (propertyNode)-[:`http://www.w3.org/2000/01/rdf-schema#range`]->(rangeResource)
OPTIONAL MATCH (propertyNode)-[:`http://www.tao.lu/datatypes/WidgetDefinitions.rdf#widget`]->(widgetResource)
RETURN distinct 
classAncestorNode.uri as class, 
propertyNode.uri as property, 
rangeResource.uri as range, 
widgetResource.uri as widget, 
propertyNode.`http://www.w3.org/2000/01/rdf-schema#label` as label,
propertyNode.`http://www.tao.lu/Ontologies/generis.rdf#validationRule` as validationRule,
propertyNode.`http://www.tao.lu/Ontologies/TAO.rdf#TAOGUIOrder` as guiOrder
CYPHER;

        $results = $this->persistence->run($query, ['uri' => $classUri]);

        return $results->toRecursiveArray();
    }

    private function getOptionsData(array $ranges): array
    {
        $query = <<<CYPHER
MATCH (subject:Resource)-[:`http://www.w3.org/1999/02/22-rdf-syntax-ns#type`]
->(parent:Resource)-[:`http://www.w3.org/2000/01/rdf-schema#subClassOf`*0..]->(grandParent:Resource)
WHERE ((parent.uri IN \$ranges) OR (grandParent.uri IN \$ranges)) 
RETURN distinct parent.uri as range, 
subject.uri as option, 
subject.`http://www.tao.lu/Ontologies/TAO.rdf#level` as level,
subject.`http://www.w3.org/2000/01/rdf-schema#label` as label
CYPHER;

        return $this->persistence->run($query, ['ranges' => $ranges])->toRecursiveArray();
    }

    private function getPropertiesValues(
        string $elementUri,
        array $relationProperties,
        array $notRelationProperties
    ): array {
        $startNode = node()
            ->withLabels(['Resource'])
            ->withVariable('startNode')
            ->withProperties(['uri' => $elementUri]);

        $query = query()->match($startNode);

        $descendantRelationNodes = [];
        foreach ($relationProperties as $relation) {
            $descendantRelationship = relationshipTo()->addType($relation);
            $variable = $relation;
            $descendantNode = node()->withVariable($variable);
            $descendantRelationNodes[] = $descendantNode;

            $query->optionalMatch($startNode->relationship($descendantRelationship, $descendantNode));
        }

        $query->returning(
            array_merge(
                array_map(fn($e) => $e->property('uri'), $descendantRelationNodes),
                array_map(fn($e) => $startNode->property($e), $notRelationProperties)
            )
        );

        return $this->persistence->run($query->build(), ['uri' => $elementUri])->toRecursiveArray();
    }

    private function isPropertyRelation($uri, $range): bool
    {
        if (in_array($uri, core_kernel_classes_Property::RELATIONSHIP_PROPERTIES)) {
            return true;
        }

        if ($uri === OntologyRdf::RDF_VALUE) {
            return false;
        }

        return !in_array(
            $range,
            [
                OntologyRdfs::RDFS_LITERAL,
                GenerisRdf::CLASS_GENERIS_FILE
            ],
            true
        );
    }
}
