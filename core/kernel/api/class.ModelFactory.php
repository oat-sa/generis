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

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\Table;

class core_kernel_api_ModelFactory
{
    const SERVICE_ID = __CLASS__;
    const DEFAULT_AUTHOR = 'http://www.tao.lu/Ontologies/TAO.rdf#installator';

    /** @var core_kernel_classes_DbWrapper */
    protected $dbWrapper;

    public function __construct()
    {
        // @TODO: inject dbWrapper as a dependency.
        $this->dbWrapper = core_kernel_classes_DbWrapper::singleton();
    }

    /**
     * @author "Lionel Lecaque, <lionel@taotesting.com>"
     *
     * @param string $namespace
     *
     * @return string
     */
    public function getModelId($namespace)
    {
        if (substr($namespace, -1) !== '#') {
            $namespace .= '#';
        }

        $query = 'SELECT modelid FROM models WHERE (modeluri = ?)';
        $results = $this->dbWrapper->query($query, [$namespace]);

        return $results->fetchColumn(0);
    }

    /**
     * @author "Lionel Lecaque, <lionel@taotesting.com>"
     *
     * @param string $namespace
     *
     * @return string|int new added model id
     */
    public function addNewModel($namespace)
    {
        $this->dbWrapper->insert('models', ['modeluri' => $namespace]);
        $result = $this->dbWrapper->query('select modelid from models where modeluri = ?', [$namespace]);
        return $result->fetch()['modelid'];
    }

    /**
     * @author "Lionel Lecaque, <lionel@taotesting.com>"
     *
     * @param string $namespace
     * @param string $data xml content
     *
     * @return bool Were triples added?
     */
    public function createModel($namespace, $data)
    {
        $modelId = $this->getModelId($namespace);
        if ($modelId === false) {
            common_Logger::d('modelId not found, need to add namespace ' . $namespace);
            $modelId = $this->addNewModel($namespace);
        }
        $modelDefinition = new EasyRdf_Graph($namespace);
        if (is_file($data)) {
            $modelDefinition->parseFile($data);
        } else {
            $modelDefinition->parse($data);
        }
        $format = EasyRdf_Format::getFormat('php');

        $data = $modelDefinition->serialise($format);

        $returnValue = false;

        foreach ($data as $subjectUri => $propertiesValues) {
            foreach ($propertiesValues as $prop => $values) {
                foreach ($values as $v) {
                    $returnValue |= $this->addStatement($modelId, $subjectUri, $prop, $v['value'], isset($v['lang']) ? $v['lang'] : null);
                }
            }
        }

        return $returnValue;
    }

    /**
     * Adds a statement to the ontology if it does not exist yet
     *
     * @author "Joel Bout, <joel@taotesting.com>"
     *
     * @param string|int $modelId
     * @param string     $subject
     * @param string     $predicate
     * @param string     $object
     * @param string     $lang
     * @param string     $author
     *
     * @return bool Was a row added?
     */
    public function addStatement($modelId, $subject, $predicate, $object, $lang = null, $author = self::DEFAULT_AUTHOR)
    {
        // Casting values and types.
        $object = (string)$object;
        if (is_null($lang)) {
            $lang = '';
        }

        // TODO: refactor this to use a triple store abstraction.
        $result = $this->dbWrapper->query(
            'SELECT count(*) FROM statements WHERE modelid = ? AND subject = ? AND predicate = ? AND object = ? AND l_language = ?',
            [$modelId, $subject, $predicate, $object, $lang]
        );

        if (intval($result->fetchColumn()) > 0) {
            return false;
        }

        return (bool)$this->dbWrapper->insert(
            'statements',
            $this->prepareStatement($modelId, $subject, $predicate, $object, $lang, is_null($author) ? '' : $author)
        );
    }

    /**
     * Prepares a statement to be inserted in the ontology
     *
     * @param string|int $modelId
     * @param string     $subject
     * @param string     $predicate
     * @param string     $object
     * @param string     $lang
     * @param string     $author
     *
     * @return array
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

    public function getIteratorQuery($modelIds)
    {
        return 'SELECT * FROM statements '
            . (is_null($modelIds) ? '' : 'WHERE modelid IN (' . implode(',', $modelIds) . ') ')
            . 'ORDER BY id';
    }

    public function getPropertySortingField()
    {
        return 'id';
    }

    public function quoteModelSqlCondition(array $models)
    {
        return 'modelid IN (' . implode(',', $models) . ')';
    }

    /**
     * Creates table schema for models.
     *
     * @param Schema $schema
     *
     * @return Table
     */
    public static function createModelsTable(Schema $schema)
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
     * Creates table schema for statements.
     *
     * @param Schema $schema
     *
     * @return Table
     */
    public static function createStatementsTable(Schema $schema)
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
