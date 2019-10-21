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
 * Copyright (c) 2019 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 * @license GPLv2
 * @package tao
 */
namespace oat\generis\persistence\sql;

use Doctrine\DBAL\Schema\Schema;
use IteratorAggregate;

class SchemaContainer implements IteratorAggregate
{
    private $schemas;

    /**
     * Augments the schema with required tables and indices
     * @param Schema $schema
     * @return Schema
     */
    public function addSchema($persistenceId, Schema $schema)
    {
        if (isset($this->schemas[$persistenceId])) {
            $this->schemas[$persistenceId] = $this->merge($this->schemas[$persistenceId], $schema);
        } else {
            $this->schemas[$persistenceId] = $schema;
        }
    }
    
    public function getIterator() {
        return new \ArrayIterator($this->schemas);
    }
    
    protected function merge(Schema $left, Schema $right) {
        $tables = array_merge($left->getTables(), $right->getTables());
        $sequences = array_merge($left->getSequences(), $right->getSequences());
        $namespaces = array_merge($left->getNamespaces(), $right->getNamespaces());
        return new Schema($tables, $sequences, null, $namespaces);
    }
}
