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
        switch ($operator) {
            case SupportedOperatorHelper::DIFFERENT:
                return new NotCommandWrapper(
                    new ConfigurableCommand(Operators\Equality::class)
                );
            case SupportedOperatorHelper::GREATER_THAN:
                return new ConfigurableCommand(Operators\GreaterThan::class);
            case SupportedOperatorHelper::LESSER_THAN:
                return new ConfigurableCommand(Operators\LessThan::class);
            case SupportedOperatorHelper::GREATER_THAN_EQUAL:
                return new ConfigurableCommand(Operators\GreaterThanOrEqual::class);
            case SupportedOperatorHelper::LESSER_THAN_EQUAL:
                return new ConfigurableCommand(Operators\LessThanOrEqual::class);
            case SupportedOperatorHelper::MATCH:
                return new RegexCommand();
            case SupportedOperatorHelper::NOT_MATCH:
                return new NotCommandWrapper(new RegexCommand());
            case SupportedOperatorHelper::IN:
                return new ConfigurableCommand(Operators\In::class);
            case SupportedOperatorHelper::NOT_IN:
                return new NotCommandWrapper(new ConfigurableCommand(Operators\In::class));
            case SupportedOperatorHelper::BETWEEN:
                return new BetweenCommand();
            case SupportedOperatorHelper::CONTAIN:
                return new RegexCommand(true, true);
            case SupportedOperatorHelper::BEGIN_BY:
                return new RegexCommand(false, true);
            case SupportedOperatorHelper::ENDING_BY:
                return new RegexCommand(true, false);
            case SupportedOperatorHelper::IS_NULL:
                return new ConfigurableCommand(Operators\IsNull::class);
            case SupportedOperatorHelper::IS_NOT_NULL:
                return new ConfigurableCommand(Operators\IsNotNull::class);
            case SupportedOperatorHelper::EQUAL:
            default:
                return new ConfigurableCommand(Operators\Equality::class);
        }
    }
}
