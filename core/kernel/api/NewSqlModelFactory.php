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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 * @author  "Lionel Lecaque, <lionel@taotesting.com>"
 * @license GPLv2
 * @package generis
 *
 */

namespace oat\generis\model\kernel\api;

use core_kernel_api_ModelFactory as ModelFactory;
use Doctrine\DBAL\Schema\Schema;
use oat\generis\Helper\UuidPrimaryKeyTrait;

class NewSqlModelFactory extends ModelFactory
{
    use UuidPrimaryKeyTrait;

    /**
     * @author "Lionel Lecaque, <lionel@taotesting.com>"
     *
     * @param string $namespace
     *
     * @return string new added model id
     */
    public function addNewModel($namespace)
    {
        $modelId = $this->getUniquePrimaryKey();
        $this->dbWrapper->insert('models', ['modelid' => $modelId, 'modeluri' => $namespace]);
        return $modelId;
    }

    /**
     * @inheritdoc
     */
    public function prepareStatement($modelId, $subject, $predicate, $object, $lang, $author)
    {
        $date = $this->dbWrapper->getPlatForm()->getNowExpression();

        return [
            'id' => $this->getUniquePrimaryKey(),
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
    public function getIteratorQuery($modelIds)
    {
        return 'SELECT * FROM statements '
            . (is_null($modelIds) ? '' : 'WHERE modelid IN ("' . implode('","', $modelIds) . '") ')
            . 'ORDER BY epoch';
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
    public function quoteModelSqlCondition(array $models)
    {
        $models = array_map(
            function ($a) {
                return "'" . $a . "'";
            },
            $models
        );
        return parent::quoteModelSqlCondition($models);
    }

    /**
     * @inheritdoc
     */
    public static function createModelsTable(Schema $schema)
    {
        $table = $schema->createTable('models');
        $table->addColumn('modelid', 'string', ['length' => 23, 'notnull' => true]);
        $table->addColumn('modeluri', 'string', ['length' => 255]);
        $table->setPrimaryKey(['modelid']);

        return $table;
    }

    /**
     * @inheritdoc
     */
    public static function createStatementsTable(Schema $schema)
    {
        $table = $schema->createTable('statements');
        $table->addColumn('id', 'string', ['length' => 23, 'notnull' => true]);
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
