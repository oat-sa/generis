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
namespace   oat\generis\model\kernel\persistence\smoothsql\install;

use Doctrine\DBAL\Schema\Schema;
/**
 * Helper to setup the required tables for generis smoothsql
 */
class SmoothRdsModel {

    /**
     * 
     * @param Schema $schema
     * @return \Doctrine\DBAL\Schema\Schema
     */
    public static function addSmoothTables(Schema $schema)
    {
        $table = $schema->createTable("models");
        $table->addColumn('modelid', "integer",array("notnull" => true,"autoincrement" => true));
        $table->addColumn('modeluri', "string", array("length" => 255,"default" => null));
        $table->addOption('engine' , 'MyISAM');
        $table->setPrimaryKey(array('modelid'));

        $table = $schema->createTable("statements");
        $table->addColumn("modelid", "integer",array("notnull" => true,"default" => 0));
        $table->addColumn("subject", "string",array("length" => 255,"default" => null));
        $table->addColumn("predicate", "string",array("length" => 255,"default" => null));
        $table->addColumn("object", "text", array("default" => null,"notnull" => false));
            
        $table->addColumn("l_language", "string",array("length" => 255,"default" => null,"notnull" => false));
        $table->addColumn("id", "integer",array("notnull" => true,"autoincrement" => true));
        $table->addColumn("author", "string",array("length" => 255,"default" => null,"notnull" => false));
        $table->setPrimaryKey(array("id"));
        $table->addOption('engine' , 'MyISAM');
        $table->addColumn("epoch", "string" , array("notnull" => null));
            
        $table->addIndex(array("subject","predicate"),"k_sp");
        $table->addIndex(array("predicate","object"),"k_po");
        
        return $schema;
    }
}
