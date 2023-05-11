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
 * Copyright (c) 2017 (original work) Open Assessment Technologies SA;
 *
 */

namespace oat\generis\model;

interface RulesRdf
{
    public const CLASS_URI = 'http://www.tao.lu/middleware/Rules.rdf';
    public const PROPERTY_OPERATION_FIRST_OP = 'http://www.tao.lu/middleware/Rules.rdf#FirstOperand';
    public const PROPERTY_OPERATION_SECOND_OP = 'http://www.tao.lu/middleware/Rules.rdf#SecondOperand';
    public const PROPERTY_OPERATION_OPERATOR = 'http://www.tao.lu/middleware/Rules.rdf#HasOperator';
    public const PROPERTY_RULE_IF = 'http://www.tao.lu/middleware/Rules.rdf#If';
    public const CLASS_URI_TERM_X_PREDICATE_OBJECT = 'http://www.tao.lu/middleware/Rules.rdf#XPredicateObject';
    public const PROPERTY_TERM_XPO_OBJECT = 'http://www.tao.lu/middleware/Rules.rdf#Object';
    public const PROPERTY_TERM_XPO_PREDICATE = 'http://www.tao.lu/middleware/Rules.rdf#Predicate';
    public const INSTANCE_OPERATOR_ADD = 'http://www.tao.lu/middleware/Rules.rdf#Plus';
    public const INSTANCE_OPERATOR_MINUS = 'http://www.tao.lu/middleware/Rules.rdf#Minus';
    public const INSTANCE_OPERATOR_DIVISION = 'http://www.tao.lu/middleware/Rules.rdf#Division';
    public const INSTANCE_OPERATOR_MULTIPLY = 'http://www.tao.lu/middleware/Rules.rdf#Multiply';
    public const INSTANCE_OPERATOR_CONCAT = 'http://www.tao.lu/middleware/Rules.rdf#Concat';
    public const INSTANCE_OPERATOR_UNION = 'http://www.tao.lu/middleware/Rules.rdf#Union';
    public const INSTANCE_OPERATOR_INTERSECT = 'http://www.tao.lu/middleware/Rules.rdf#Intersect';
    public const CLASS_URI_CONSTRUCTED_SET = 'http://www.tao.lu/middleware/Rules.rdf#ConstrcuctedSet';
    public const PROPERTY_SET_OPERATOR = 'http://www.tao.lu/middleware/Rules.rdf#HasSetOperator';
    public const PROPERTY_SUBSET = 'http://www.tao.lu/middleware/Rules.rdf#SubSets';
    public const CLASS_URI_ASSIGNMENT = 'http://www.tao.lu/middleware/Rules.rdf#Assignment';
    public const PROPERTY_ASSIGNMENT_VARIABLE = 'http://www.tao.lu/middleware/Rules.rdf#Variable';
    public const PROPERTY_ASSIGNMENT_VALUE = 'http://www.tao.lu/middleware/Rules.rdf#Value';
    public const CLASS_URI_EXPRESSION = 'http://www.tao.lu/middleware/Rules.rdf#Expression';
    public const PROPERTY_FIRST_EXPRESSION = 'http://www.tao.lu/middleware/Rules.rdf#FirstExpression';
    public const PROPERTY_SECOND_EXPRESSION = 'http://www.tao.lu/middleware/Rules.rdf#SecondExpression';
    public const PROPERTY_HASLOGICALOPERATOR = 'http://www.tao.lu/middleware/Rules.rdf#HasLogicalOperator';
    public const INSTANCE_OR_OPERATOR = 'http://www.tao.lu/middleware/Rules.rdf#Or';
    public const INSTANCE_AND_OPERATOR = 'http://www.tao.lu/middleware/Rules.rdf#And';
    public const INSTANCE_EXPRESSION_TRUE = 'http://www.tao.lu/middleware/Rules.rdf#TrueExpression';
    public const INSTANCE_EXPRESSION_FALSE = 'http://www.tao.lu/middleware/Rules.rdf#FalseExpression';
    public const PROPERTY_TERMINAL_EXPRESSION = 'http://www.tao.lu/middleware/Rules.rdf#TerminalExpression';
    public const CLASS_DYNAMICTEXT = 'http://www.tao.lu/middleware/Rules.rdf#DynamicText';
    public const CLASS_RULE = 'http://www.tao.lu/middleware/Rules.rdf#Rule';
    public const CLASS_TERM = 'http://www.tao.lu/middleware/Rules.rdf#Term';
    public const CLASS_TERM_CONST = 'http://www.tao.lu/middleware/Rules.rdf#Const';
    public const CLASS_OPERATION = 'http://www.tao.lu/middleware/Rules.rdf#Operation';
    public const CLASS_TERM_SUJET_PREDICATE_X = 'http://www.tao.lu/middleware/Rules.rdf#SubjectPredicateX';
    public const PROPERTY_TERM_SPX_SUBJET = 'http://www.tao.lu/middleware/Rules.rdf#Subject';
    public const PROPERTY_TERM_SPX_PREDICATE = 'http://www.tao.lu/middleware/Rules.rdf#Predicate';
    public const PROPERTY_TERM_VALUE = 'http://www.tao.lu/middleware/Rules.rdf#TermValue';
    public const INSTANCE_EXISTS_OPERATOR_URI = 'http://www.tao.lu/middleware/Rules.rdf#Exists';
    public const INSTANCE_EQUALS_OPERATOR_URI = 'http://www.tao.lu/middleware/Rules.rdf#Equal';
    public const INSTANCE_DIFFERENT_OPERATOR_URI = 'http://www.tao.lu/middleware/Rules.rdf#NotEqual';
    public const INSTANCE_SUP_EQ_OPERATOR_URI = 'http://www.tao.lu/middleware/Rules.rdf#GreaterThanOrEqual';
    public const INSTANCE_INF_EQ_OPERATOR_URI = 'http://www.tao.lu/middleware/Rules.rdf#LessThanOrEqual';
    public const INSTANCE_SUP_OPERATOR_URI = 'http://www.tao.lu/middleware/Rules.rdf#GreaterThan';
    public const INSTANCE_INF_OPERATOR_URI = 'http://www.tao.lu/middleware/Rules.rdf#LessThan';
    public const INSTANCE_EMPTY_TERM_URI = 'http://www.tao.lu/middleware/Rules.rdf#Empty';
    public const INSTANCE_TERM_IS_NULL = 'http://www.tao.lu/middleware/Rules.rdf#IsNull';
}
