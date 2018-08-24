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

interface OntologyRdfs
{
    const RDFS_COMMENT = 'http://www.w3.org/2000/01/rdf-schema#comment';
    const RDFS_LABEL = 'http://www.w3.org/2000/01/rdf-schema#label';
    const RDFS_LITERAL = 'http://www.w3.org/2000/01/rdf-schema#Literal';
    const RDFS_SEEALSO = 'http://www.w3.org/2000/01/rdf-schema#seeAlso';
    const RDFS_DATATYPE = 'http://www.w3.org/2000/01/rdf-schema#Datatype';
    const RDFS_CLASS = 'http://www.w3.org/2000/01/rdf-schema#Class';
    const RDFS_SUBCLASSOF = 'http://www.w3.org/2000/01/rdf-schema#subClassOf';
    const RDFS_DOMAIN = 'http://www.w3.org/2000/01/rdf-schema#domain';
    const RDFS_RESOURCE = 'http://www.w3.org/2000/01/rdf-schema#Resource';
    const RDFS_RANGE = 'http://www.w3.org/2000/01/rdf-schema#range';
    const RDFS_SUBPROPERTYOF = 'http://www.w3.org/2000/01/rdf-schema#subPropertyOf';
}
