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

class RdsModelFactory extends ModelFactory
{
    /**
     * @inheritdoc
     */
    public function addNewModel($namespace)
    {
        $this->dbWrapper->insert('models', ['modeluri' => $namespace]);
        $result = $this->dbWrapper->query('select modelid from models where modeluri = ?', [$namespace]);
        return $result->fetch()['modelid'];
    }

    /**
     * @inheritdoc
     */
    public function prepareStatement($modelId, $subject, $predicate, $object, $lang, $author)
    {
        $date = $this->dbWrapper->getPlatForm()->getNowExpression();

        return [
            'modelid' => $modelId,
            'subject' => $subject,
            'predicate' => $predicate,
            'object' => $object,
            'l_language' => $lang,
            'author' => is_null($author) ? '' : $author,
            'epoch' => $date,
        ];
    }

    /**
     * @inheritdoc
     */
    public function getPropertySortingField()
    {
        return 'id';
    }

    /**
     * @inheritdoc
     */
    public function createModelsTable(Schema $schema)
    {
        // Models table.
        $table = $schema->createTable('models');
        $table->addColumn('modelid', 'integer', ['notnull' => true, 'autoincrement' => true]);
        $table->addColumn('modeluri', 'string', ['length' => 255, 'default' => null]);
        $table->setPrimaryKey(['modelid']);
        $table->addOption('engine', 'MyISAM');

        return $table;
    }

    /**
     * @inheritdoc
     */
    public function createStatementsTable(Schema $schema)
    {
        $table = $schema->createTable('statements');
        $table->addColumn('id', 'integer', ['notnull' => true, 'autoincrement' => true]);
        $table->addColumn('modelid', 'integer', ['notnull' => true, 'default' => 0]);
        $table->addColumn('subject', 'string', ['length' => 255, 'default' => null]);
        $table->addColumn('predicate', 'string', ['length' => 255, 'default' => null]);
        $table->addColumn('object', 'text', ['default' => null, 'notnull' => false]);
        $table->addColumn('l_language', 'string', ['length' => 255, 'default' => null, 'notnull' => false]);
        $table->addColumn('author', 'string', ['length' => 255, 'default' => null, 'notnull' => false]);
        $table->addColumn('epoch', 'string', ['notnull' => null]);

        $table->setPrimaryKey(['id']);
        $table->addIndex(['subject', 'predicate'], 'k_sp', [], ['lengths' => [164, 164]]);
        $table->addIndex(['predicate', 'object'], 'k_po', [], ['lengths' => [164, 164]]);
        $table->addOption('engine', 'MyISAM');

        return $table;
    }
}
