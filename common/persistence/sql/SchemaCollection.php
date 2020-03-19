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
 * Copyright (c) 2020 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 * @license GPLv2
 */

namespace oat\generis\persistence\sql;

use Doctrine\DBAL\Schema\Schema;
use \IteratorAggregate;
use ArrayIterator;
use \common_exception_InconsistentData;

/**
 * A collection of multiple schemas, in order to accomodate for the possibility
 * of multiple RDS databases with different schemas
 */
class SchemaCollection implements IteratorAggregate
{
    /**
     * Schemas as they were originally added, prior to modifications
     * @var Schema[]
     */
    private $originals;

    /**
     * Schemas per database, might have been modified
     * @var Schema[]
     */
    private $schemas;

    /**
     * Add a schema to the collection
     * @param string $id
     * @param Schema $schema
     */
    public function addSchema($id, Schema $schema) : void
    {
        $this->originals[$id] = clone $schema;
        $this->schemas[$id] = $schema;
    }

    /**
     * Get a schema as it was when it was added
     * @param string  $id
     * @throws common_exception_InconsistentData
     */
    public function getOriginalSchema($id) : Schema
    {
        if (!isset($this->originals[$id])) {
            throw new common_exception_InconsistentData('Expected original schema '.$id.' not found');
        }
        return $this->originals[$id];
    }

    /**
     * Get a schema in its current form, might have been changed
     * @param string $id
     * @throws common_exception_InconsistentData
     * @return Schema
     */
    public function getSchema($id) : Schema
    {
        if (!isset($this->schemas[$id])) {
            throw new common_exception_InconsistentData('Expected schema '.$id.' not found');
        }
        return $this->schemas[$id];
    }

    /**
     * {@inheritDoc}
     * @see IteratorAggregate::getIterator()
     */
    public function getIterator()
    {
        return new ArrayIterator($this->schemas);
    }
}
