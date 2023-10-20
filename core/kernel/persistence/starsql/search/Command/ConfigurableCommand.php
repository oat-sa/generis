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

namespace oat\generis\model\kernel\persistence\starsql\search\Command;

use WikibaseSolutions\CypherDSL\Expressions\Operators\UnaryOperator;
use WikibaseSolutions\CypherDSL\Query;

class ConfigurableCommand implements CommandInterface
{
    private string $operationClass;

    public function __construct(string $operationClass)
    {
        $this->operationClass = $operationClass;
    }

    public function buildQuery($predicate, $values): Condition
    {
        if (is_a($this->operationClass, UnaryOperator::class, true)) {
            $condition =  new $this->operationClass($predicate);
            $parameterList = [];
        } else {
            $condition =  new $this->operationClass($predicate, $valueParam = Query::parameter());
            $parameterList = [$valueParam->getParameter() => $values];
        }

        return new Condition($condition, $parameterList);
    }
}
