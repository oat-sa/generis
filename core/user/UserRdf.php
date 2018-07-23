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
 * Copyright (c) 2018 (original work) Open Assessment Technologies SA;
 *
 */
namespace oat\generis\model\user;

interface UserRdf
{
    const CLASS_URI = 'http://www.tao.lu/Ontologies/generis.rdf#User';
    const PROPERTY_LOGIN = 'http://www.tao.lu/Ontologies/generis.rdf#login';
    const PROPERTY_PASSWORD = 'http://www.tao.lu/Ontologies/generis.rdf#password';
    const PROPERTY_UILG = 'http://www.tao.lu/Ontologies/generis.rdf#userUILg';
    const PROPERTY_DEFLG = 'http://www.tao.lu/Ontologies/generis.rdf#userDefLg';
    const PROPERTY_MAIL = 'http://www.tao.lu/Ontologies/generis.rdf#userMail';
    const PROPERTY_FIRSTNAME = 'http://www.tao.lu/Ontologies/generis.rdf#userFirstName';
    const PROPERTY_LASTNAME = 'http://www.tao.lu/Ontologies/generis.rdf#userLastName';
    const PROPERTY_ROLES = 'http://www.tao.lu/Ontologies/generis.rdf#userRoles';
    const PROPERTY_TIMEZONE = 'http://www.tao.lu/Ontologies/generis.rdf#userTimezone';
}