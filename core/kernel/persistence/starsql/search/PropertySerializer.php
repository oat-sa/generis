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
use oat\generis\model\data\ModelManager;
use WikibaseSolutions\CypherDSL\Expressions\Procedures\Procedure;
use WikibaseSolutions\CypherDSL\Patterns\Node;
use WikibaseSolutions\CypherDSL\Query;

class PropertySerializer extends QuerySerializer
{
    private string $propertyUri;
    private bool $isDistinct = false;

    public function __construct(string $propertyUri, bool $isDistinct = false)
    {
        $this->propertyUri = $propertyUri;
        $this->isDistinct = $isDistinct;
    }

    protected function buildReturn(Node $subject): void
    {
        $property = $this->propertyUri;
        $returnProperty = ModelManager::getModel()->getProperty($property);

        $predicate = $subject->property($property);
        if ($returnProperty->isLgDependent()) {
            $predicate = $this->buildLanguagePattern($predicate);
        }

        $predicate = Procedure::raw('toStringOrNull', $predicate);
        if ($this->isDistinct) {
            $predicate = Query::rawExpression(sprintf('DISTINCT %s', $predicate->toQuery()));
            $this->returnStatements = [
                $predicate->alias('object')
            ];
        } else {
            $this->returnStatements = [
                Procedure::raw('elementId', $subject)->alias('id'),
                $subject->property('uri')->alias('uri'),
                $predicate->alias('object')
            ];
        }
    }
}
