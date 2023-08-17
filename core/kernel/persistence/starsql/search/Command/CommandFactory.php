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

use oat\search\helper\SupportedOperatorHelper;
use WikibaseSolutions\CypherDSL\Expressions\Operators;

class CommandFactory
{
    public static function createCommand(string $operator): CommandInterface
    {
        return match ($operator) {
            SupportedOperatorHelper::EQUAL => new ConfigurableCommand(Operators\Equality::class),
            SupportedOperatorHelper::DIFFERENT => new NotCommandWrapper(
                new ConfigurableCommand(Operators\Equality::class)
            ),
            SupportedOperatorHelper::GREATER_THAN => new ConfigurableCommand(Operators\GreaterThan::class),
            SupportedOperatorHelper::LESSER_THAN => new ConfigurableCommand(Operators\LessThan::class),
            SupportedOperatorHelper::GREATER_THAN_EQUAL => new ConfigurableCommand(Operators\GreaterThanOrEqual::class),
            SupportedOperatorHelper::LESSER_THAN_EQUAL => new ConfigurableCommand(Operators\LessThanOrEqual::class),
            SupportedOperatorHelper::MATCH => new RegexCommand(),
            SupportedOperatorHelper::NOT_MATCH => new NotCommandWrapper(new RegexCommand()),
            SupportedOperatorHelper::IN => new ConfigurableCommand(Operators\In::class),
            SupportedOperatorHelper::NOT_IN => new NotCommandWrapper(new ConfigurableCommand(Operators\In::class)),
            SupportedOperatorHelper::BETWEEN => new BetweenCommand(),
            SupportedOperatorHelper::CONTAIN => new RegexCommand(true, true),
            SupportedOperatorHelper::BEGIN_BY => new RegexCommand(false, true),
            SupportedOperatorHelper::ENDING_BY => new RegexCommand(true, false),
            SupportedOperatorHelper::IS_NULL => new ConfigurableCommand(Operators\IsNull::class),
            SupportedOperatorHelper::IS_NOT_NULL => new ConfigurableCommand(Operators\IsNotNull::class),
            default => new ConfigurableCommand(Operators\Equality::class),
        };
    }
}
