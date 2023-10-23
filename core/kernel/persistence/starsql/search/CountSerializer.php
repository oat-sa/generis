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

use Laudis\Neo4j\Databags\Statement;
use WikibaseSolutions\CypherDSL\Expressions\Procedures\Procedure;
use WikibaseSolutions\CypherDSL\Query;

class CountSerializer extends QuerySerializer
{
    public function serialyse()
    {
        $subject = $this->getMainNode();

        $this->buildMatchPatterns($subject);
        $this->buildWhereConditions($subject);

        $query = Query::new()->match($this->matchPatterns);
        $query->where($this->whereConditions);
        $query->returning(Procedure::raw(
            'count',
            Query::rawExpression(sprintf('DISTINCT %s', $subject->getVariable()->getName()))
        ));

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
