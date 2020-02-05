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
 * Copyright (c) 2018 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */

namespace oat\generis\model\kernel\persistence\smoothsql\install;

use Doctrine\DBAL\Schema\Schema;

/**
 * Helper to setup the required tables for generis smoothsql
 */
class SmoothRdsModel
{
    /**
     *
     * @param Schema $schema
     *
     * @return \Doctrine\DBAL\Schema\Schema
     */
    public static function addSmoothTables(Schema $schema)
    {
        // Models table.
        $table = $schema->createTable('models');
        $table->addColumn('modelid', 'string', ['length' => 36, 'notnull' => true]);
        $table->addColumn('modeluri', 'string', ['length' => 255]);
        $table->setPrimaryKey(['modelid']);
        $table->addOption('engine', 'MyISAM');

        // Statements table.
        $table = $schema->createTable('statements');
        $table->addColumn('id', 'string', ['length' => 36, 'notnull' => true]);

        $table->addColumn('modelid', 'string', ['length' => 36, 'notnull' => true]);
        $table->addColumn('subject', 'string', ['length' => 255]);
        $table->addColumn('predicate', 'string', ['length' => 255]);
        $table->addColumn('object', 'text');
        $table->addColumn('l_language', 'string', ['length' => 255]);

        $table->addColumn('author', 'string', ['length' => 255]);
        $table->addColumn('epoch', 'string', ['notnull' => true]);

        $table->setPrimaryKey(['id']);
        $table->addIndex(['subject', 'predicate'], 'k_sp');
        $table->addIndex(['predicate', 'object'], 'k_po');
        $table->addOption('engine', 'MyISAM');

        return $schema;
    }
}
