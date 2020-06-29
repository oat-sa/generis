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

interface GenerisRdf
{
    public const GENERIS_NS = 'http://www.tao.lu/Ontologies/generis.rdf';
    public const GENERIS_BOOLEAN = 'http://www.tao.lu/Ontologies/generis.rdf#Boolean';
    public const GENERIS_TRUE = 'http://www.tao.lu/Ontologies/generis.rdf#True';
    public const GENERIS_FALSE = 'http://www.tao.lu/Ontologies/generis.rdf#False';
    public const PROPERTY_ALIAS = 'http://www.tao.lu/Ontologies/generis.rdf#alias';
    public const PROPERTY_IS_LG_DEPENDENT = 'http://www.tao.lu/Ontologies/generis.rdf#is_language_dependent';
    public const CLASS_GENERIS_RESOURCE = 'http://www.tao.lu/Ontologies/generis.rdf#generis_Ressource';
    public const PROPERTY_MULTIPLE = 'http://www.tao.lu/Ontologies/generis.rdf#Multiple';
    public const CLASS_GENERIS_FILE = 'http://www.tao.lu/Ontologies/generis.rdf#File';
    public const PROPERTY_FILE_FILENAME = 'http://www.tao.lu/Ontologies/generis.rdf#FileName';
    public const PROPERTY_FILE_FILEPATH = 'http://www.tao.lu/Ontologies/generis.rdf#FilePath';
    public const PROPERTY_FILE_FILESYSTEM = 'http://www.tao.lu/Ontologies/generis.rdf#FileRepository';
    public const PROPERTY_VERSIONEDFILE_VERSION = 'http://www.tao.lu/Ontologies/generis.rdf#FileVersion';
    public const CLASS_GENERIS_VERSIONEDREPOSITORY = 'http://www.tao.lu/Ontologies/generis.rdf#VersionedRepository';
    public const PROPERTY_GENERIS_VERSIONEDREPOSITORY_URL = 'http://www.tao.lu/Ontologies/generis.rdf#VersionedRepositoryUrl';
    public const PROPERTY_GENERIS_VERSIONEDREPOSITORY_PATH = 'http://www.tao.lu/Ontologies/generis.rdf#VersionedRepositoryPath';
    public const PROPERTY_GENERIS_VERSIONEDREPOSITORY_TYPE = 'http://www.tao.lu/Ontologies/generis.rdf#VersionedRepositoryType';
    public const PROPERTY_GENERIS_VERSIONEDREPOSITORY_LOGIN = 'http://www.tao.lu/Ontologies/generis.rdf#VersionedRepositoryLogin';
    public const PROPERTY_GENERIS_VERSIONEDREPOSITORY_PASSWORD = 'http://www.tao.lu/Ontologies/generis.rdf#VersionedRepositoryPassword';
    public const PROPERTY_GENERIS_VERSIONEDREPOSITORY_ENABLED = 'http://www.tao.lu/Ontologies/generis.rdf#VersionedRepositoryEnabled';
    public const PROPERTY_GENERIS_VERSIONEDREPOSITORY_ROOTFILE = 'http://www.tao.lu/Ontologies/generis.rdf#RepositoryRootFile';
    public const PROPERTY_GENERIS_VCS_TYPE_SUBVERSION = 'http://www.tao.lu/Ontologies/generis.rdf#VCSTypeSubversion';
    public const PROPERTY_GENERIS_VCS_TYPE_SUBVERSION_WIN = 'http://www.tao.lu/Ontologies/generis.rdf#VCSTypeSubversionWindows';
    public const PROPERTY_GENERIS_VCS_TYPE_CVS = 'http://www.tao.lu/Ontologies/generis.rdf#VCSTypeCvs';
    public const INSTANCE_GENERIS_VCS_TYPE_LOCAL = 'http://www.tao.lu/Ontologies/generis.rdf#VCSLocalDirectory';
    public const CLASS_ROLE = 'http://www.tao.lu/Ontologies/generis.rdf#ClassRole';
    public const PROPERTY_ROLE_ISSYSTEM = 'http://www.tao.lu/Ontologies/generis.rdf#isSystem';
    public const PROPERTY_ROLE_INCLUDESROLE = 'http://www.tao.lu/Ontologies/generis.rdf#includesRole';
    public const INSTANCE_ROLE_GENERIS = 'http://www.tao.lu/Ontologies/generis.rdf#GenerisRole';
    public const INSTANCE_ROLE_ANONYMOUS = 'http://www.tao.lu/Ontologies/generis.rdf#AnonymousRole';
    public const CLASS_SUBCRIPTION = 'http://www.tao.lu/Ontologies/generis.rdf#Subscription';
    public const PROPERTY_SUBCRIPTION_URL = 'http://www.tao.lu/Ontologies/generis.rdf#SubscriptionUrl';
    public const PROPERTY_SUBCRIPTION_MASK = 'http://www.tao.lu/Ontologies/generis.rdf#SubscriptionMask';
    public const CLASS_MASK = 'http://www.tao.lu/Ontologies/generis.rdf#Mask';
    public const PROPERTY_MASK_SUBJECT = 'http://www.tao.lu/Ontologies/generis.rdf#MaskSubject';
    public const PROPERTY_MASK_PREDICATE = 'http://www.tao.lu/Ontologies/generis.rdf#MaskPredicate';
    public const PROPERTY_MASK_OBJECT = 'http://www.tao.lu/Ontologies/generis.rdf#MaskObject';
    //@deprecated use UserRdf::CLASS_URI
    public const CLASS_GENERIS_USER = 'http://www.tao.lu/Ontologies/generis.rdf#User';
    //@deprecated use UserRdf::PROPERTY_LOGIN
    public const PROPERTY_USER_LOGIN = 'http://www.tao.lu/Ontologies/generis.rdf#login';
    //@deprecated use UserRdf::PROPERTY_PASSWORD
    public const PROPERTY_USER_PASSWORD = 'http://www.tao.lu/Ontologies/generis.rdf#password';
    //@deprecated use UserRdf::PROPERTY_UILG
    public const PROPERTY_USER_UILG = 'http://www.tao.lu/Ontologies/generis.rdf#userUILg';
    //@deprecated use UserRdf::PROPERTY_DEFLG
    public const PROPERTY_USER_DEFLG = 'http://www.tao.lu/Ontologies/generis.rdf#userDefLg';
    //@deprecated use UserRdf::PROPERTY_MAIL
    public const PROPERTY_USER_MAIL = 'http://www.tao.lu/Ontologies/generis.rdf#userMail';
    //@deprecated use UserRdf::PROPERTY_FIRSTNAME
    public const PROPERTY_USER_FIRSTNAME = 'http://www.tao.lu/Ontologies/generis.rdf#userFirstName';
    //@deprecated use UserRdf::PROPERTY_USER_LASTNAME
    public const PROPERTY_USER_LASTNAME = 'http://www.tao.lu/Ontologies/generis.rdf#userLastName';
    //@deprecated use UserRdf::PROPERTY_ROLES
    public const PROPERTY_USER_ROLES = 'http://www.tao.lu/Ontologies/generis.rdf#userRoles';
    //@deprecated use UserRdf::PROPERTY_TIMEZONE
    public const PROPERTY_USER_TIMEZONE = 'http://www.tao.lu/Ontologies/generis.rdf#userTimezone';
}
