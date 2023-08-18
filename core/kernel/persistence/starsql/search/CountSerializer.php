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

class CountSerializer extends QuerySerializer
{
    public function serialyse()
    {
        $subject = Query::node('Resource')->withVariable(Query::variable('subject'));

        $this->buildMatchPatters($subject);
        $this->buildWhereConditions($subject);

        $query = Query::new()->match($this->matchPatterns);
        $query->where($this->whereConditions);
        $query->returning(Procedure::raw('count', $subject));

        return Statement::create($query->build(), $this->parameters);
    }

    public function count(bool $count = true): self
    {
        if ($count) {
            return $this;
        } else {
            return (new QuerySerializer())
                ->setServiceLocator($this->getServiceLocator())
                ->setOptions($this->getOptions())
                ->setDriverEscaper($this->getDriverEscaper())
                ->setCriteriaList($this->criteriaList);
        }
    }
}
