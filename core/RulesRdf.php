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
    const CLASS_URI = 'http://www.tao.lu/middleware/Rules.rdf';
    const PROPERTY_OPERATION_FIRST_OP = 'http://www.tao.lu/middleware/Rules.rdf#FirstOperand';
    const PROPERTY_OPERATION_SECOND_OP = 'http://www.tao.lu/middleware/Rules.rdf#SecondOperand';
    const PROPERTY_OPERATION_OPERATOR = 'http://www.tao.lu/middleware/Rules.rdf#HasOperator';
    const PROPERTY_RULE_IF = 'http://www.tao.lu/middleware/Rules.rdf#If';
    const CLASS_URI_TERM_X_PREDICATE_OBJECT = 'http://www.tao.lu/middleware/Rules.rdf#XPredicateObject';
    const PROPERTY_TERM_XPO_OBJECT = 'http://www.tao.lu/middleware/Rules.rdf#Object';
    const PROPERTY_TERM_XPO_PREDICATE = 'http://www.tao.lu/middleware/Rules.rdf#Predicate';
    const INSTANCE_OPERATOR_ADD = 'http://www.tao.lu/middleware/Rules.rdf#Plus';
    const INSTANCE_OPERATOR_MINUS = 'http://www.tao.lu/middleware/Rules.rdf#Minus';
    const INSTANCE_OPERATOR_DIVISION = 'http://www.tao.lu/middleware/Rules.rdf#Division';
    const INSTANCE_OPERATOR_MULTIPLY = 'http://www.tao.lu/middleware/Rules.rdf#Multiply';
    const INSTANCE_OPERATOR_CONCAT = 'http://www.tao.lu/middleware/Rules.rdf#Concat';
    const INSTANCE_OPERATOR_UNION = 'http://www.tao.lu/middleware/Rules.rdf#Union';
    const INSTANCE_OPERATOR_INTERSECT = 'http://www.tao.lu/middleware/Rules.rdf#Intersect';
    const CLASS_URI_CONSTRUCTED_SET = 'http://www.tao.lu/middleware/Rules.rdf#ConstrcuctedSet';
    const PROPERTY_SET_OPERATOR = 'http://www.tao.lu/middleware/Rules.rdf#HasSetOperator';
    const PROPERTY_SUBSET = 'http://www.tao.lu/middleware/Rules.rdf#SubSets';
    const CLASS_URI_ASSIGNMENT = 'http://www.tao.lu/middleware/Rules.rdf#Assignment';
    const PROPERTY_ASSIGNMENT_VARIABLE = 'http://www.tao.lu/middleware/Rules.rdf#Variable';
    const PROPERTY_ASSIGNMENT_VALUE = 'http://www.tao.lu/middleware/Rules.rdf#Value';
    const CLASS_URI_EXPRESSION = 'http://www.tao.lu/middleware/Rules.rdf#Expression';
    const PROPERTY_FIRST_EXPRESSION = 'http://www.tao.lu/middleware/Rules.rdf#FirstExpression';
    const PROPERTY_SECOND_EXPRESSION = 'http://www.tao.lu/middleware/Rules.rdf#SecondExpression';
    const PROPERTY_HASLOGICALOPERATOR = 'http://www.tao.lu/middleware/Rules.rdf#HasLogicalOperator';
    const INSTANCE_OR_OPERATOR = 'http://www.tao.lu/middleware/Rules.rdf#Or';
    const INSTANCE_AND_OPERATOR = 'http://www.tao.lu/middleware/Rules.rdf#And';
    const INSTANCE_EXPRESSION_TRUE = 'http://www.tao.lu/middleware/Rules.rdf#TrueExpression';
    const INSTANCE_EXPRESSION_FALSE = 'http://www.tao.lu/middleware/Rules.rdf#FalseExpression';
    const PROPERTY_TERMINAL_EXPRESSION = 'http://www.tao.lu/middleware/Rules.rdf#TerminalExpression';
    const CLASS_DYNAMICTEXT = 'http://www.tao.lu/middleware/Rules.rdf#DynamicText';
    const CLASS_RULE = 'http://www.tao.lu/middleware/Rules.rdf#Rule';
    const CLASS_TERM = 'http://www.tao.lu/middleware/Rules.rdf#Term';
    const CLASS_TERM_CONST = 'http://www.tao.lu/middleware/Rules.rdf#Const';
    const CLASS_OPERATION = 'http://www.tao.lu/middleware/Rules.rdf#Operation';
    const CLASS_TERM_SUJET_PREDICATE_X = 'http://www.tao.lu/middleware/Rules.rdf#SubjectPredicateX';
    const PROPERTY_TERM_SPX_SUBJET = 'http://www.tao.lu/middleware/Rules.rdf#Subject';
    const PROPERTY_TERM_SPX_PREDICATE = 'http://www.tao.lu/middleware/Rules.rdf#Predicate';
    const PROPERTY_TERM_VALUE = 'http://www.tao.lu/middleware/Rules.rdf#TermValue';
    const INSTANCE_EXISTS_OPERATOR_URI = 'http://www.tao.lu/middleware/Rules.rdf#Exists';
    const INSTANCE_EQUALS_OPERATOR_URI = 'http://www.tao.lu/middleware/Rules.rdf#Equal';
    const INSTANCE_DIFFERENT_OPERATOR_URI = 'http://www.tao.lu/middleware/Rules.rdf#NotEqual';
    const INSTANCE_SUP_EQ_OPERATOR_URI = 'http://www.tao.lu/middleware/Rules.rdf#GreaterThanOrEqual';
    const INSTANCE_INF_EQ_OPERATOR_URI = 'http://www.tao.lu/middleware/Rules.rdf#LessThanOrEqual';
    const INSTANCE_SUP_OPERATOR_URI = 'http://www.tao.lu/middleware/Rules.rdf#GreaterThan';
    const INSTANCE_INF_OPERATOR_URI = 'http://www.tao.lu/middleware/Rules.rdf#LessThan';
    const INSTANCE_EMPTY_TERM_URI = 'http://www.tao.lu/middleware/Rules.rdf#Empty';
    const INSTANCE_TERM_IS_NULL = 'http://www.tao.lu/middleware/Rules.rdf#IsNull';
}