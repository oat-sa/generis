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

namespace oat\generis\model\kernel\persistence\newsql;

use core_kernel_persistence_smoothsql_SmoothModel;
use oat\generis\persistence\sql\SchemaCollection;

/**
 * Abstraction for the new sql compatible ontology
 */
class NewSqlOntology extends core_kernel_persistence_smoothsql_SmoothModel
{
    /**
     * {@inheritDoc}
     * @see core_kernel_persistence_smoothsql_SmoothModel::getRdfInterface()
     */
    public function getRdfInterface()
    {
        return new NewSqlRdf($this);
    }

    /**
     * {@inheritDoc}
     * @see core_kernel_persistence_smoothsql_SmoothModel::provideSchema()
     */
    public function provideSchema(SchemaCollection $schemaCollection)
    {
        $schema = $schemaCollection->getSchema($this->getOption(self::OPTION_PERSISTENCE));
        $table = $schema->createTable("statements");
        $table->addColumn("id", "string", ["notnull" => true]);

        $table->addColumn("modelid", "integer", ["notnull" => true, "default" => 0]);
        $table->addColumn("subject", "string", ["length" => 255, "default" => null]);
        $table->addColumn("predicate", "string", ["length" => 255, "default" => null]);
        $table->addColumn("object", "text", ["default" => null, "notnull" => false]);

        $table->addColumn("l_language", "string", ["length" => 255, "default" => null, "notnull" => false]);

        $table->addColumn("author", "string", ["length" => 255, "default" => null, "notnull" => false]);
        $table->setPrimaryKey(["id"]);
        $table->addOption('engine', 'MyISAM');
        $table->addColumn("epoch", "string", ["notnull" => null]);

        $table->addIndex(["subject", "predicate"], "k_sp", [], ['lengths' => [164, 164]]);
        $table->addIndex(["predicate", "object"], "k_po", [], ['lengths' => [164, 164]]);
    }
}
