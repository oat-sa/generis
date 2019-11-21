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
 * Copyright (c) 2019 (original work) Open Assessment Technologies SA
 */

namespace oat\generis\model\kernel\api;

use core_kernel_api_ModelFactory as ModelFactory;
use Doctrine\DBAL\Schema\Schema;
use oat\generis\Helper\UuidPrimaryKeyTrait;
use RuntimeException;

class NewSqlModelFactory extends ModelFactory
{
    use UuidPrimaryKeyTrait;
    
    /**
     * @inheritdoc
     */
    public function addNewModel($namespace)
    {
        $modelId = md5($namespace);
        if ($this->getPersistence()->insert('models', ['modelid' => $modelId, 'modeluri' => $namespace]) === 0) {
            throw new RuntimeException('A problem occurred while creating a new model.');
        }
        return $modelId;
    }
    
    /**
     * @inheritdoc
     */
    public function prepareStatement($modelId, $subject, $predicate, $object, $lang, $author)
    {
        return [
            'id' => $this->getUniquePrimaryKey(),
            'modelid' => $modelId,
            'subject' => $subject,
            'predicate' => $predicate,
            'object' => $object,
            'l_language' => $lang,
            'author' => $author ?? '',
            'epoch' => $this->getPersistence()->getPlatForm()->getNowExpression(),
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function buildModelSqlCondition(array $models)
    {
        $models = array_map(
            function ($a) {
                return "'" . $a . "'";
            },
            $models
        );
        return parent::buildModelSqlCondition($models);
    }
    
    /**
     * @inheritdoc
     */
    public function getPropertySortingField()
    {
        return 'epoch';
    }
    
    /**
     * @inheritdoc
     */
    public function createModelsTable(Schema $schema)
    {
        $table = $schema->createTable('models');
        
        $table->addColumn('modelid', 'string', ['length' => 36, 'notnull' => true]);
        $table->addColumn('modeluri', 'string', ['length' => 255]);
        
        $table->setPrimaryKey(['modelid']);
        
        return $table;
    }
    
    /**
     * @inheritdoc
     */
    public function createStatementsTable(Schema $schema)
    {
        $table = $schema->createTable('statements');
        
        $table->addColumn('id', 'string', ['length' => 36, 'notnull' => true]);
        $table->addColumn('modelid', 'string', ['length' => 23, 'notnull' => true]);
        $table->addColumn('subject', 'string', ['length' => 255]);
        $table->addColumn('predicate', 'string', ['length' => 255]);
        $table->addColumn('object', 'text', []);
        $table->addColumn('l_language', 'string', ['length' => 255]);
        $table->addColumn('author', 'string', ['length' => 255]);
        $table->addColumn('epoch', 'string', ['notnull' => true]);
        
        $table->setPrimaryKey(['id']);
        $table->addIndex(['subject', 'predicate'], 'k_sp');
        $table->addIndex(['predicate', 'object'], 'k_po');
        
        return $table;
    }
}
