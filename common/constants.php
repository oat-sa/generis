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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 *               2017 (update and modification) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 */

/**
 * Generis Object Oriented API - common\constants.php
 *
 * This file is part of Generis Object Oriented API.
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package generis

 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */

/**
 * @deprecated
 */
#RDF
define('RDF_TYPE', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type');
define('RDF_PROPERTY', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property');
define('RDF_VALUE', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#value');
define('RDF_STATEMENT', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Statement');
define('RDF_FIRST', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#first');
define('RDF_REST', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#rest');
define('RDF_LIST', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#List');
define('RDF_NIL', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#nil');
#RDFS
define('RDFS_COMMENT', 'http://www.w3.org/2000/01/rdf-schema#comment');
define('RDFS_LABEL', 'http://www.w3.org/2000/01/rdf-schema#label');
define('RDFS_LITERAL', 'http://www.w3.org/2000/01/rdf-schema#Literal');
define('RDFS_SEEALSO', 'http://www.w3.org/2000/01/rdf-schema#seeAlso');
define('RDFS_DATATYPE', 'http://www.w3.org/2000/01/rdf-schema#Datatype');
define('RDFS_CLASS', 'http://www.w3.org/2000/01/rdf-schema#Class');
define('RDFS_SUBCLASSOF', 'http://www.w3.org/2000/01/rdf-schema#subClassOf');
define('RDFS_DOMAIN', 'http://www.w3.org/2000/01/rdf-schema#domain');
define('RDFS_RESOURCE', 'http://www.w3.org/2000/01/rdf-schema#Resource');
define('RDFS_MEMBER', 'http://www.w3.org/2000/01/rdf-schema#member');
define('RDFS_RANGE', 'http://www.w3.org/2000/01/rdf-schema#range');
#generis
define('GENERIS_NS', 'http://www.tao.lu/Ontologies/generis.rdf');
define('GENERIS_BOOLEAN', GENERIS_NS . '#Boolean');
define('GENERIS_TRUE', GENERIS_NS . '#True');
define('GENERIS_FALSE', GENERIS_NS . '#False');
define('PROPERTY_IS_LG_DEPENDENT', GENERIS_NS . '#is_language_dependent');
define('CLASS_GENERIS_USER', GENERIS_NS . '#User');
define('CLASS_GENERIS_RESOURCE', GENERIS_NS . '#generis_Ressource');
define('PROPERTY_MULTIPLE', GENERIS_NS . '#Multiple');
#file
define('CLASS_GENERIS_FILE', GENERIS_NS . '#File');
define('PROPERTY_FILE_FILENAME', GENERIS_NS . '#FileName');
define('PROPERTY_FILE_FILEPATH', GENERIS_NS . '#FilePath');
define('PROPERTY_FILE_FILESYSTEM', GENERIS_NS . '#FileRepository');
#versioned file
define('PROPERTY_VERSIONEDFILE_VERSION', GENERIS_NS . '#FileVersion');
#Versioned Repository
define('CLASS_GENERIS_VERSIONEDREPOSITORY', GENERIS_NS . '#VersionedRepository');
define('PROPERTY_GENERIS_VERSIONEDREPOSITORY_URL', GENERIS_NS . '#VersionedRepositoryUrl');
define('PROPERTY_GENERIS_VERSIONEDREPOSITORY_PATH', GENERIS_NS . '#VersionedRepositoryPath');
define('PROPERTY_GENERIS_VERSIONEDREPOSITORY_TYPE', GENERIS_NS . '#VersionedRepositoryType');
define('PROPERTY_GENERIS_VERSIONEDREPOSITORY_LOGIN', GENERIS_NS . '#VersionedRepositoryLogin');
define('PROPERTY_GENERIS_VERSIONEDREPOSITORY_PASSWORD', GENERIS_NS . '#VersionedRepositoryPassword');
define('PROPERTY_GENERIS_VERSIONEDREPOSITORY_ENABLED', GENERIS_NS . '#VersionedRepositoryEnabled');
define('PROPERTY_GENERIS_VERSIONEDREPOSITORY_ROOTFILE', GENERIS_NS . '#RepositoryRootFile');
define('PROPERTY_GENERIS_VCS_TYPE_SUBVERSION', GENERIS_NS . '#VCSTypeSubversion');
define('PROPERTY_GENERIS_VCS_TYPE_SUBVERSION_WIN', GENERIS_NS . '#VCSTypeSubversionWindows');
define('PROPERTY_GENERIS_VCS_TYPE_CVS', GENERIS_NS . '#VCSTypeCvs');
define('INSTANCE_GENERIS_VCS_TYPE_LOCAL', GENERIS_NS . '#VCSLocalDirectory');

#user
define('CLASS_ROLE', GENERIS_NS . '#ClassRole');
define('PROPERTY_ROLE_ISSYSTEM', GENERIS_NS . '#isSystem');
define('PROPERTY_ROLE_INCLUDESROLE', GENERIS_NS . '#includesRole');
define('PROPERTY_USER_LOGIN', GENERIS_NS . '#login');
define('PROPERTY_USER_PASSWORD', GENERIS_NS . '#password');
define('PROPERTY_USER_UILG', GENERIS_NS . '#userUILg');
define('PROPERTY_USER_DEFLG', GENERIS_NS . '#userDefLg');
define('PROPERTY_USER_MAIL', GENERIS_NS . '#userMail');
define('PROPERTY_USER_FIRSTNAME', GENERIS_NS . '#userFirstName');
define('PROPERTY_USER_LASTNAME', GENERIS_NS . '#userLastName');
define('PROPERTY_USER_ROLES', GENERIS_NS . '#userRoles');
define('PROPERTY_USER_TIMEZONE', GENERIS_NS . '#userTimezone');
define('INSTANCE_ROLE_GENERIS', GENERIS_NS . '#GenerisRole');
define('INSTANCE_ROLE_ANONYMOUS', GENERIS_NS . '#AnonymousRole');
define('CLASS_SUBCRIPTION', GENERIS_NS . '#Subscription');
define('PROPERTY_SUBCRIPTION_URL', GENERIS_NS . '#SubscriptionUrl');
define('PROPERTY_SUBCRIPTION_MASK', GENERIS_NS . '#SubscriptionMask');
define('CLASS_MASK', GENERIS_NS . '#Mask');
define('PROPERTY_MASK_SUBJECT', GENERIS_NS . '#MaskSubject');
define('PROPERTY_MASK_PREDICATE', GENERIS_NS . '#MaskPredicate');
define('PROPERTY_MASK_OBJECT', GENERIS_NS . '#MaskObject');

#Rules
define('RULES_NS', 'http://www.tao.lu/middleware/Rules.rdf');

define('PROPERTY_OPERATION_FIRST_OP', RULES_NS . '#FirstOperand');
define('PROPERTY_OPERATION_SECND_OP', RULES_NS . '#SecondOperand');
define('PROPERTY_OPERATION_OPERATOR', RULES_NS . '#HasOperator');
define('PROPERTY_RULE_IF', RULES_NS . '#If');
define('CLASS_TERM_X_PREDICATE_OBJECT', RULES_NS . '#XPredicateObject');
define('PROPERTY_TERM_XPO_OBJECT', RULES_NS . '#Object');
define('PROPERTY_TERM_XPO_PREDICATE', RULES_NS . '#Predicate');
define('INSTANCE_OPERATOR_ADD', RULES_NS . '#Plus');
define('INSTANCE_OPERATOR_MINUS', RULES_NS . '#Minus');
define('INSTANCE_OPERATOR_DIVISION', RULES_NS . '#Division');
define('INSTANCE_OPERATOR_MULTIPLY', RULES_NS . '#Multiply');
define('INSTANCE_OPERATOR_CONCAT', RULES_NS . '#Concat');
define('INSTANCE_OPERATOR_UNION', RULES_NS . '#Union');
define('INSTANCE_OPERATOR_INTERSECT', RULES_NS . '#Intersect');
define('CLASS_CONSTRUCTED_SET', RULES_NS . '#ConstrcuctedSet');
define('PROPERTY_SET_OPERATOR', RULES_NS . '#HasSetOperator');
define('PROPERTY_SUBSET', RULES_NS . '#SubSets');
define('PROPERTY_ASSIGNMENT_VARIABLE', RULES_NS . '#Variable');
define('PROPERTY_ASSIGNMENT_VALUE', RULES_NS . '#Value');
define('CLASS_ASSIGNMENT', RULES_NS . '#Assignment');
define('CLASS_EXPRESSION', RULES_NS . '#Expression');
define('PROPERTY_FIRST_EXPRESSION', RULES_NS . '#FirstExpression');
define('PROPERTY_SECOND_EXPRESSION', RULES_NS . '#SecondExpression');
define('PROPERTY_HASLOGICALOPERATOR', RULES_NS . '#HasLogicalOperator');
define('INSTANCE_OR_OPERATOR', RULES_NS . '#Or');
define('INSTANCE_AND_OPERATOR', RULES_NS . '#And');
define('INSTANCE_EXPRESSION_TRUE', RULES_NS . '#TrueExpression');
define('INSTANCE_EXPRESSION_FALSE', RULES_NS . '#FalseExpression');
define('PROPERTY_TERMINAL_EXPRESSION', RULES_NS . '#TerminalExpression');
define('CLASS_DYNAMICTEXT', RULES_NS . '#DynamicText');
define('CLASS_RULE', RULES_NS . '#Rule');
define('CLASS_TERM', RULES_NS . '#Term');
define('CLASS_TERM_CONST', RULES_NS . '#Const');
define('CLASS_OPERATION', RULES_NS . '#Operation');
define('CLASS_TERM_SUJET_PREDICATE_X', RULES_NS . '#SubjectPredicateX');
define('PROPERTY_TERM_SPX_SUBJET', RULES_NS . '#Subject');
define('PROPERTY_TERM_SPX_PREDICATE', RULES_NS . '#Predicate');
define('PROPERTY_TERM_VALUE', RULES_NS . '#TermValue');
define('INSTANCE_EXISTS_OPERATOR_URI', RULES_NS . '#Exists');
define('INSTANCE_EQUALS_OPERATOR_URI', RULES_NS . '#Equal');
define('INSTANCE_DIFFERENT_OPERATOR_URI', RULES_NS . '#NotEqual');
define('INSTANCE_SUP_EQ_OPERATOR_URI', RULES_NS . '#GreaterThanOrEqual');
define('INSTANCE_INF_EQ_OPERATOR_URI', RULES_NS . '#LessThanOrEqual');
define('INSTANCE_SUP_OPERATOR_URI', RULES_NS . '#GreaterThan');
define('INSTANCE_INF_OPERATOR_URI', RULES_NS . '#LessThan');
define('INSTANCE_EMPTY_TERM_URI', RULES_NS . '#Empty');
define('INSTANCE_TERM_IS_NULL', RULES_NS . '#IsNull');

//not used
define('PERSISTENCE_SMOOTH', "smoothsql");
define('PERSISTENCE_HARD', "hardsql");
define('PERSISTENCE_VIRTUOSO', "virtuoso");
define('PERSISTENCE_SUBSCRIPTION', "subscription");
