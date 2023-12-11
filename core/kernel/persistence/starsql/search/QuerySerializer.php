<?php

/*
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
 * Copyright (c) 2023 (original work) Open Assessment Technologies SA;
 *
 */

namespace oat\generis\model\kernel\persistence\starsql\search;

use Laminas\ServiceManager\ServiceLocatorAwareTrait;
use Laudis\Neo4j\Databags\Statement;
use oat\generis\model\data\ModelManager;
use oat\generis\model\kernel\persistence\starsql\search\Command\CommandFactory;
use oat\generis\model\OntologyRdf;
use oat\generis\model\OntologyRdfs;
use oat\search\base\QueryBuilderInterface;
use oat\search\base\QueryCriterionInterface;
use oat\search\base\QuerySerialyserInterface;
use oat\search\helper\SupportedOperatorHelper;
use oat\search\UsableTrait\DriverSensitiveTrait;
use oat\search\UsableTrait\OptionsTrait;
use WikibaseSolutions\CypherDSL\Expressions\Procedures\Procedure;
use WikibaseSolutions\CypherDSL\Expressions\RawExpression;
use WikibaseSolutions\CypherDSL\Patterns\Node;
use WikibaseSolutions\CypherDSL\Query;
use WikibaseSolutions\CypherDSL\QueryConvertible;
use WikibaseSolutions\CypherDSL\Types\PropertyTypes\BooleanType;

class QuerySerializer implements QuerySerialyserInterface
{
    use DriverSensitiveTrait;
    use OptionsTrait;
    use ServiceLocatorAwareTrait;

    protected QueryBuilderInterface $criteriaList;

    protected array $matchPatterns = [];

    protected array $whereConditions = [];

    protected array $returnStatements = [];

    protected QueryConvertible $orderCondition;

    protected array $parameters = [];

    private string $userLanguage = '';

    private string $defaultLanguage = '';

    /**
     * {@inheritDoc}
     */
    public function pretty($pretty)
    {
        // As library, we currently use doesn't support such option, we ignore it for now.
        return $this;
    }

    public function prefixQuery()
    {
        //Do nothing as on this point we don't know if it is a normal, count or specific field query.
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function setCriteriaList(QueryBuilderInterface $criteriaList)
    {
        $this->criteriaList = $criteriaList;
        $this->setOptions($criteriaList->getOptions());

        return $this;
    }

    public function count(bool $count = true): self
    {
        if ($count) {
            return (new CountSerializer())
                ->setServiceLocator($this->getServiceLocator())
                ->setOptions($this->getOptions())
                ->setDriverEscaper($this->getDriverEscaper())
                ->setCriteriaList($this->criteriaList);
        } else {
            return $this;
        }
    }

    public function property(string $propertyUri, bool $isDistinct = false): self
    {
        return (new PropertySerializer($propertyUri, $isDistinct))
            ->setServiceLocator($this->getServiceLocator())
            ->setOptions($this->getOptions())
            ->setDriverEscaper($this->getDriverEscaper())
            ->setCriteriaList($this->criteriaList);
    }

    public function setOptions(array $options)
    {
        $this->defaultLanguage = !empty($options['defaultLanguage'])
            ? $options['defaultLanguage']
            : DEFAULT_LANG;

        $this->userLanguage = !empty($options['language'])
            ? $options['language']
            : $this->defaultLanguage;

        return $this;
    }

    public function serialyse()
    {
        $subject = $this->getMainNode();

        $this->buildMatchPatterns($subject);
        $this->buildWhereConditions($subject);
        $this->buildReturn($subject);
        $this->buildOrderCondition($subject);

        $query = Query::new()->match($this->matchPatterns);
        $query->where($this->whereConditions);
        $query->returning($this->returnStatements);

        if (isset($this->orderCondition)) {
            //Can't use dedicated order function as it doesn't support raw expressions
            $query->raw('ORDER BY', $this->orderCondition->toQuery());
        }

        if ($this->criteriaList->getLimit() > 0) {
            $query
                ->skip((int)$this->criteriaList->getOffset())
                ->limit((int)$this->criteriaList->getLimit());
        }

        return Statement::create($query->build(), $this->parameters);
    }

    protected function buildMatchPatterns(Node $subject): void
    {
        $queryOptions = $this->criteriaList->getOptions();

        if (isset($queryOptions['type']) && isset($queryOptions['type']['resource'])) {
            $mainClass = $queryOptions['type']['resource'];
            $isRecursive = (bool)$queryOptions['type']['recursive'] ?? false;

            $rdfTypes = array_unique(array_merge(
                [$mainClass->getUri()],
                $queryOptions['type']['extraClassUriList'] ?? []
            ));

            $parentClass = Query::node('Resource')->withVariable(Query::variable('parent'));
            $parentPath = $subject->relationshipTo($parentClass, OntologyRdf::RDF_TYPE);
            $parentWhere = $this->buildPropertyQuery(
                $parentClass->property('uri'),
                $rdfTypes,
                SupportedOperatorHelper::IN
            );

            if ($isRecursive) {
                $grandParentClass = Query::node('Resource')
                    ->withVariable(Query::variable('grandParent'));
                $subClassRelation = Query::relationshipTo()
                    ->addType(OntologyRdfs::RDFS_SUBCLASSOF)
                    ->withMinHops(0);

                $parentPath = $parentPath->relationship($subClassRelation, $grandParentClass);
                $parentWhere = $parentWhere->or(
                    $this->buildPropertyQuery(
                        $grandParentClass->property('uri'),
                        $mainClass,
                        SupportedOperatorHelper::EQUAL
                    )
                );
            }

            $this->matchPatterns[] = $parentPath;
            $this->whereConditions[] = $parentWhere;
        } else {
            $this->matchPatterns[] = $subject;
        }
    }

    protected function buildWhereConditions(Node $subject): void
    {
        $whereCondition = null;
        foreach ($this->criteriaList->getStoredQueries() as $query) {
            $operationList = $query->getStoredQueryCriteria();
            $queryCondition = null;
            /** @var QueryCriterionInterface $operation */
            foreach ($operationList as $operation) {
                $mainCondition = $this->buildCondition($operation, $subject);
                foreach ($operation->getAnd() as $subOperation) {
                    $subCondition = $this->buildCondition($subOperation, $subject, $operation);
                    $mainCondition = $mainCondition->and($subCondition);
                }

                foreach ($operation->getOr() as $subOperation) {
                    $subCondition = $this->buildCondition($subOperation, $subject, $operation);
                    $mainCondition = $mainCondition->or($subCondition);
                }

                $queryCondition = ($queryCondition === null)
                    ? $mainCondition
                    : $queryCondition->and($mainCondition);
            }

            $whereCondition = ($whereCondition === null)
                ? $queryCondition
                : $whereCondition->or($queryCondition);
        }

        if ($whereCondition) {
            $this->whereConditions[] = $whereCondition;
        }
    }

    protected function buildCondition(
        QueryCriterionInterface $operation,
        Node $subject,
        QueryCriterionInterface $parentOperation = null
    ): BooleanType {
        $propertyName = $operation->getName();

        if (empty($propertyName) && $parentOperation) {
            $propertyName = $parentOperation->getName();
        }

        $propertyName = $propertyName === QueryCriterionInterface::VIRTUAL_URI_FIELD
            ? 'uri'
            : $propertyName;

        $property = ModelManager::getModel()->getProperty($propertyName);
        if ($property->isRelationship()) {
            $object = Query::node('Resource');
            $this->matchPatterns[] = $subject->relationshipTo($object, $propertyName);

            $predicate = $object->property('uri');
            $values = $operation->getValue();

            $fieldCondition = $this->buildPropertyQuery(
                $predicate,
                $values,
                is_array($values) ? SupportedOperatorHelper::IN : SupportedOperatorHelper::EQUAL
            );
        } else {
            $predicate = $subject->property($propertyName);
            if ($property->isLgDependent()) {
                $predicate = $this->buildLanguagePattern($predicate);
            }
            $fieldCondition = $this->buildPropertyQuery(
                $predicate,
                $operation->getValue(),
                $operation->getOperator()
            );
        }

        return $fieldCondition;
    }

    protected function buildPropertyQuery(
        $predicate,
        $values,
        string $operation
    ): BooleanType {
        if ($values instanceof \core_kernel_classes_Resource) {
            $values = $values->getUri();
        }

        $command = CommandFactory::createCommand($operation);
        $condition = $command->buildQuery($predicate, $values);

        $this->parameters = array_merge($this->parameters, $condition->getParameterList());
        return $condition->getCondition();
    }

    protected function buildLanguagePattern(QueryConvertible $predicate): RawExpression
    {
        if (empty($this->userLanguage) || $this->userLanguage === $this->defaultLanguage) {
            $resultExpression = Query::rawExpression(
                sprintf(
                    "n10s.rdf.getLangValue('%s', %s)",
                    $this->defaultLanguage,
                    $predicate->toQuery()
                )
            );
        } else {
            $resultExpression = Query::rawExpression(
                sprintf(
                    "coalesce(n10s.rdf.getLangValue('%s', %s), n10s.rdf.getLangValue('%s', %s))",
                    $this->userLanguage,
                    $predicate->toQuery(),
                    $this->defaultLanguage,
                    $predicate->toQuery()
                )
            );
        }

        return $resultExpression;
    }

    protected function buildReturn(Node $subject): void
    {
        $this->returnStatements[] = Query::rawExpression(
            sprintf('DISTINCT %s', $subject->getVariable()->toQuery())
        );
    }

    protected function buildOrderCondition(Node $subject): void
    {
        $sortCriteria = $this->criteriaList->getSort();

        $sort = [];
        foreach ($sortCriteria as $field => $order) {
            $predicate = $subject->property($field);

            $orderProperty = ModelManager::getModel()->getProperty($field);
            if ($orderProperty->isLgDependent()) {
                $predicate = $this->buildLanguagePattern($predicate);
            }

            $sort[] = $predicate->toQuery() . ((strtolower($order) === 'desc') ? ' DESCENDING' : '');
        }

        if (!empty($sort)) {
            $this->orderCondition = Query::rawExpression(implode(', ', $sort));
        }
    }

    /**
     * @return Node
     */
    protected function getMainNode(): Node
    {
        $queryOptions = $this->criteriaList->getOptions();

        if (isset($queryOptions['system_only']) && $queryOptions['system_only']) {
            $node = Query::node('System');
        } else {
            $node = Query::node('Resource');
        }

        return $node->withVariable(Query::variable('subject'));
    }
}
