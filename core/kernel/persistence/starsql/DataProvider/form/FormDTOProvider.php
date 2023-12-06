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
use oat\oatbox\user\UserLanguageServiceInterface;
use oat\tao\helpers\form\ValidationRuleRegistry;
use oat\tao\model\TaoOntology;
use WikibaseSolutions\CypherDSL\Clauses\WhereClause;

use function WikibaseSolutions\CypherDSL\node;
use function WikibaseSolutions\CypherDSL\parameter;
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
    private UserLanguageServiceInterface $userLanguageService;

    public function __construct(
        Ontology $ontology,
        RecordProcessor $recordProcessor,
        UserLanguageServiceInterface $userLanguageService
    ) {
        $this->persistence = $ontology->getPersistence();
        $this->recordProcessor = $recordProcessor;
        $this->userLanguageService = $userLanguageService;
    }

    public function get(string $classUri, string $topClassUri, string $elementUri, string $language): FormDTO
    {
        $formData = [];
        $ranges = [];
        $relationProperties = [];
        $notRelationProperties = [];
        $reachedTopClass = false;
        $defaultLanguage = $this->userLanguageService->getDefaultLanguage();
        $listRanges = $this->getListRanges();
        $propertiesData = $this->getPropertiesData($classUri);
        foreach ($propertiesData as $propertyData) {
            if (
                $propertyData['property'] === null ||
                (
                    $reachedTopClass &&
                    $propertyData['class'] !== $topClassUri &&
                    // label should be added anyway even though it's beyond top class
                    $propertyData['property'] !== OntologyRdfs::RDFS_LABEL
                )
            ) {
                continue;
            }
            $propertyData['label'] = $this->recordProcessor->filterRecordsByAvailableLanguage(
                $propertyData['label'],
                $language,
                $defaultLanguage
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
                $propertyData['range'] !== null &&
                !in_array($propertyData['range'], $ranges) &&
                !in_array($propertyData['range'], self::PROPERTIES_WITHOUT_OPTIONS)
            ) {
                $ranges[] = $propertyData['range'];
            }
            if (!$reachedTopClass && $propertyData['class'] === $topClassUri) {
                $reachedTopClass = true;
            }
        }

        $optionsData = $this->getOptionsData($ranges);
        foreach ($optionsData as $optionData) {
            foreach ($formData as $propertyUri => $propertyData) {
                if (
                    $propertyData['range'] === $optionData['parentRange'] ||
                    $propertyData['range'] === $optionData['grandParentRange']
                ) {
                    $formData[$propertyUri]['options'][$optionData['option']] =
                        [
                            'uri' => $optionData['option'],
                            'level' => $optionData['level'],
                            'label' => $this->recordProcessor->filterRecordsByAvailableLanguage(
                                $optionData['label'],
                                $language,
                                $defaultLanguage
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
                            $defaultLanguage
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
        $startNodeVariable = 'startNode';
        $listRangeNodeVariable = 'listRangeNode';
        $uriParameter = parameter();
        $startNode = node()
            ->withLabels(['Resource'])
            ->withVariable($startNodeVariable)
            ->withProperties(['uri' => $uriParameter]);
        $listRangeNode = node()->withVariable($listRangeNodeVariable);
        $descendantRelationship = relationshipTo()
            ->addType(OntologyRdfs::RDFS_SUBCLASSOF)
            ->withArbitraryHops();

        $query = query()
            ->match($startNode)
            ->match($listRangeNode->relationship($descendantRelationship, node()->withVariable($startNodeVariable)))
            ->returning($listRangeNode->property('uri'));

        return array_column(
            $this->persistence->run($query->build(), [
                $uriParameter->getParameter() => TaoOntology::CLASS_URI_LIST
            ])->toRecursiveArray(),
            "$listRangeNodeVariable.uri"
        );
    }

    private function getPropertiesData(string $classUri): array
    {
        $classNodeVariable = 'classNode';
        $uriParameter = parameter();
        $classNode = node('Resource')->withVariable($classNodeVariable)->withProperties(['uri' => $uriParameter]);
        $propertyNode = node()->withVariable('propertyNode');
        $rangeResource = node()->withVariable('rangeResource');
        $widgetResource = node()->withVariable('widgetResource');
        $domainRelation = relationshipTo()->addType(OntologyRdfs::RDFS_DOMAIN);
        $rangeRelation = relationshipTo()->addType(OntologyRdfs::RDFS_RANGE);
        $widgetRelation = relationshipTo()->addType('http://www.tao.lu/datatypes/WidgetDefinitions.rdf#widget');
        $classPropertiesQueryReturn =  [
            $classNode->property('uri')->alias('class'),
            $propertyNode->property('uri')->alias('property'),
            $rangeResource->property('uri')->alias('range'),
            $widgetResource->property('uri')->alias('widget'),
            $propertyNode->property(OntologyRdfs::RDFS_LABEL)->alias('label'),
            $propertyNode
                ->property(ValidationRuleRegistry::PROPERTY_VALIDATION_RULE)
                ->alias('validationRule'),
            $propertyNode->property(TaoOntology::PROPERTY_GUI_ORDER)->alias('guiOrder'),
        ];

        $classPropertiesQuery = query()
            ->match($classNode)
            ->optionalMatch($propertyNode->relationship($domainRelation, node()->withVariable($classNodeVariable)))
            ->optionalMatch($propertyNode->relationship($rangeRelation, $rangeResource))
            ->optionalMatch($propertyNode->relationship($widgetRelation, $widgetResource))
            ->returning($classPropertiesQueryReturn, true);

        $classAncestorNode = node()->withVariable('classAncestorNode');
        $classAncestorRelation = relationshipTo()
            ->addType(OntologyRdfs::RDFS_SUBCLASSOF)
            ->withArbitraryHops();
        $classAncestorsPropertiesQueryReturn = $classPropertiesQueryReturn;
        $classAncestorsPropertiesQueryReturn[0] = $classAncestorNode->property('uri')->alias('class');

        $classAncestorsPropertiesQuery = query()
            ->match($classNode)
            ->match(node()->withVariable($classNodeVariable)->relationship($classAncestorRelation, $classAncestorNode))
            ->optionalMatch($propertyNode->relationship($domainRelation, $classAncestorNode))
            ->optionalMatch($propertyNode->relationship($rangeRelation, $rangeResource))
            ->optionalMatch($propertyNode->relationship($widgetRelation, $widgetResource))
            ->returning($classAncestorsPropertiesQueryReturn, true);

        $allClassesPropertiesQuery = $classPropertiesQuery->union($classAncestorsPropertiesQuery);

        $results = $this->persistence->run($allClassesPropertiesQuery->build(), [
            $uriParameter->getParameter() => $classUri
        ]);

        return $results->toRecursiveArray();
    }

    private function getOptionsData(array $ranges): array
    {
        $subjectNode = node('Resource')->withVariable('subject');
        $parentNode = node('Resource')->withVariable('parent');
        $grandParentNode = node('Resource')->withVariable('grandParent');
        $subjectParentRelation = relationshipTo()->addType(OntologyRdf::RDF_TYPE);
        $parentGrandParentRelation = relationshipTo()
            ->addType(OntologyRdfs::RDFS_SUBCLASSOF)
            ->withArbitraryHops();

        $rangesParameter = parameter();
        $query = query()->match(
            $subjectNode
                ->relationship($subjectParentRelation, $parentNode)
                ->relationship($parentGrandParentRelation, $grandParentNode)
        )->where(
            [
                $parentNode->property('uri')->in($rangesParameter),
                $grandParentNode->property('uri')->in($rangesParameter),
            ],
            WhereClause::OR
        )->returning(
            [
                $parentNode->property('uri')->alias('parentRange'),
                $grandParentNode->property('uri')->alias('grandParentRange'),
                $subjectNode->property('uri')->alias('option'),
                $subjectNode->property(TaoOntology::PROPERTY_LIST_LEVEL)->alias('level'),
                $subjectNode->property(OntologyRdfs::RDFS_LABEL)->alias('label'),
            ],
            true
        );

        return $this->persistence->run($query->build(), [
            $rangesParameter->getParameter() => $ranges
        ])->toRecursiveArray();
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

        return $this->persistence->run($query->build())->toRecursiveArray();
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
