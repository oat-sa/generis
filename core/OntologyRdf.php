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

interface OntologyRdf
{
    public const RDF_TYPE = 'http://www.w3.org/1999/02/22-rdf-syntax-ns#type';
    public const RDF_PROPERTY = 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Property';
    public const RDF_VALUE = 'http://www.w3.org/1999/02/22-rdf-syntax-ns#value';
    public const RDF_STATEMENT = 'http://www.w3.org/1999/02/22-rdf-syntax-ns#Statement';
    public const RDF_FIRST = 'http://www.w3.org/1999/02/22-rdf-syntax-ns#first';
    public const RDF_REST = 'http://www.w3.org/1999/02/22-rdf-syntax-ns#rest';
    public const RDF_LIST = 'http://www.w3.org/1999/02/22-rdf-syntax-ns#List';
}
