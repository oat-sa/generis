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

use WikibaseSolutions\CypherDSL\Expressions\Operators\Regex;
use WikibaseSolutions\CypherDSL\Query;

class RegexCommand implements CommandInterface
{
    private bool $hasStartWildcard;
    private bool $hasEndWildcard;

    public function __construct(bool $startWildcard = false, bool $endWildcard = false)
    {
        $this->hasStartWildcard = $startWildcard;
        $this->hasEndWildcard = $endWildcard;
    }

    public function buildQuery($predicate, $values): Condition
    {
        // Compatibility with legacy queries
        if (str_contains($values, '*')) {
            $this->hasStartWildcard = str_starts_with($values, '*');
            $this->hasEndWildcard = str_ends_with($values, '*');
            $values = trim($values, '*');
        }

        $patternToken = $this->escapeString($values);

        if ($this->hasStartWildcard) {
            $patternToken = '.*' . $patternToken;
        }

        if ($this->hasEndWildcard) {
            $patternToken = $patternToken . '.*';
        }

        return new Condition(
            new Regex($predicate, $valueParam = Query::parameter()),
            [
                $valueParam->getParameter() => "(?i)" . $patternToken,
            ]
        );
    }

    /**
     * @param $values
     *
     * @return string
     */
    public function escapeString($values): string
    {
        return strtr(
            trim($values, '%'),
            [
                '.' => '\\.',
                '+' => '\\+',
                '?' => '\\?',
                '[' => '\\[',
                ']' => '\\]',
                '(' => '\\(',
                ')' => '\\)',
                '{' => '\\{',
                '}' => '\\}',
                '^' => '\\^',
                '$' => '\\$',
                '|' => '\\|',
                '\\_' => '_',
                '\\%' => '%',
                '*' => '.*',
                '_' => '.',
                '%' => '.*',
            ]
        );
    }
}
