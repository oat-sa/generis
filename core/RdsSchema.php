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
 * Copyright (c) 2019 (original work) Open Assessment Technologies SA;
 *
 */

namespace oat\generis\model;

use Doctrine\DBAL\Schema\Schema;

class RdsSchema
{
    /**
     * @author "Lionel Lecaque, <lionel@taotesting.com>"
     * @return Schema
     */
    public function getSchema()
    {
        $schema = new \Doctrine\DBAL\Schema\Schema() ;
        $this->createModelsSchema($schema);
        $this->createStatementsSchena($schema);
        $this->createSequenceUriProvider($schema);
        $this->createKeyValueStoreTable($schema);
        return $schema;
    }

    /**
     * @author "Lionel Lecaque, <lionel@taotesting.com>"
     */
    private function createModelsSchema(Schema $schema)
    {
        $table = $schema->createTable("models");
	    $table->addColumn('modelid', "integer",array("notnull" => true,"autoincrement" => true));
	    $table->addColumn('modeluri', "string", array("length" => 255,"default" => null));
	    $table->addOption('engine' , 'MyISAM');
	    $table->setPrimaryKey(array('modelid'));
    }

    /**
     * @author "Lionel Lecaque, <lionel@taotesting.com>"
     */
    private function createStatementsSchena(Schema $schema)
    {
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

   		$table->addIndex(array("predicate","object"),"k_po");
    }

    /**
     * 
     * @author "Lionel Lecaque, <lionel@taotesting.com>"
     */
    private function createKeyValueStoreTable(Schema $schema)
    {
        $table = $schema->createTable("kv_store");
        $table->addColumn('kv_id',"string",array("notnull" => null,"length" => 255));
        $table->addColumn('kv_value',"text",array("notnull" => null));
        $table->addColumn('kv_time',"integer",array("notnull" => null,"length" => 30));
        $table->setPrimaryKey(array("kv_id"));
        $table->addOption('engine' , 'MyISAM');
    }

    /**
     * @author "Lionel Lecaque, <lionel@taotesting.com>"
     */
    private function createSequenceUriProvider(Schema $schema)
    {
    	$table = $schema->createTable("sequence_uri_provider");
    	$table->addColumn("uri_sequence", "integer",array("notnull" => true,"autoincrement" => true));
    	$table->addOption('engine' , 'MyISAM');
    	$table->setPrimaryKey(array("uri_sequence"));
    	
    	//$schema->createSequence('sequence_uri_provider_uri_sequence_seq');
    }
}