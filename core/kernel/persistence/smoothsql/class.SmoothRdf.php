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
 * Copyright (c) 2017-2020 (original work) Open Assessment Technologies SA
 *
 */

use Doctrine\DBAL\ParameterType;
use oat\generis\model\data\event\ResourceCreated;
use oat\generis\model\data\Ontology;
use oat\generis\model\data\RdfInterface;
use oat\generis\model\OntologyRdf;
use oat\generis\model\OntologyRdfs;
use oat\oatbox\event\EventManager;

/**
 * Implementation of the RDF interface for the smooth sql driver
 *
 * @author joel bout <joel@taotesting.com>
 * @package generis
 */
class core_kernel_persistence_smoothsql_SmoothRdf implements RdfInterface
{
    const BATCH_SIZE = 100;
    /**
     * @var core_kernel_persistence_smoothsql_SmoothModel
     */
    private $model;
    
    public function __construct(core_kernel_persistence_smoothsql_SmoothModel $model)
    {
        $this->model = $model;
    }
    
    protected function getPersistence()
    {
        return $this->model->getPersistence();
    }
    
    /**
     * (non-PHPdoc)
     * @see \oat\generis\model\data\RdfInterface::get()
     */
    public function get($subject, $predicate)
    {
        throw new \common_Exception('Not implemented');
    }

    /**
     * (non-PHPdoc)
     * @see \oat\generis\model\data\RdfInterface::add()
     */
    public function add(core_kernel_classes_Triple $triple)
    {
        $query = "INSERT INTO statements ( modelId, subject, predicate, object, l_language, epoch, author) "
            . "VALUES ( ? , ? , ? , ? , ? , ?, ?);";

        $success = $this->getPersistence()->exec(
            $query,
            [
                $triple->modelid,
                $triple->subject,
                $triple->predicate,
                $triple->object,
                is_null($triple->lg) ? '' : $triple->lg,
                $this->getPersistence()->getPlatForm()->getNowExpression(),
                is_null($triple->author) ? '' : $triple->author
            ],
            $this->getTripleParameterTypes()
        );
        return $success;
    }

    /**
     * @inheritDoc
     */
    public function addTripleCollection(iterable $triples)
    {
        $valuesToInsert = [];

        foreach ($triples as $triple) {
            $valuesToInsert [] = $triple;

            if (count($valuesToInsert) >= self::BATCH_SIZE) {
                $this->insertTriples($valuesToInsert);
                $valuesToInsert = [];
            }
        }

        if (!empty($valuesToInsert)) {
            $this->insertTriples($valuesToInsert);
        }
    }

    protected function insertTriples(array $triples)
    {
        $values = array_map([$this,"tripleToValue"], $triples);
        $isInsertionSuccessful = $this->insertValues($values);
        return $isInsertionSuccessful;
    }

    protected function insertValues(array $valuesToInsert)
    {
        $types = [];
        foreach ($valuesToInsert as $value) {
            array_push($types, ...$this->getTripleParameterTypes());
        }
        return $this->getPersistence()->insertMultiple('statements', $valuesToInsert, $types);
    }
    
    /**
     * (non-PHPdoc)
     * @see \oat\generis\model\data\RdfInterface::remove()
     */
    public function remove(core_kernel_classes_Triple $triple)
    {
        $query = "DELETE FROM statements WHERE subject = ? AND predicate = ? AND object = ? AND l_language = ?;";
        return $this->getPersistence()->exec($query, [$triple->subject, $triple->predicate, $triple->object, is_null($triple->lg) ? '' : $triple->lg]);
    }
    
    /**
     * (non-PHPdoc)
     * @see \oat\generis\model\data\RdfInterface::search()
     */
    public function search($predicate, $object)
    {
        throw new \common_Exception('Not implemented');
    }
    
    public function getIterator()
    {
        return new core_kernel_persistence_smoothsql_SmoothIterator($this->getPersistence());
    }

    /**
     * @return Ontology
     */
    protected function getModel()
    {
        return $this->model;
    }

    /**
     * @param core_kernel_classes_Triple $triple
     * @param array $valuesToInsert
     * @return array
     */
    protected function tripleToValue(core_kernel_classes_Triple $triple)
    {
        return [
            'modelid' => $triple->modelid,
            'subject' => $triple->subject,
            'predicate' => $triple->predicate,
            'object' => $triple->object,
            'l_language' => is_null($triple->lg) ? '' : $triple->lg,
            'author' => is_null($triple->author) ? '' : $triple->author,
            'epoch' => $this->getPersistence()->getPlatForm()->getNowExpression()
        ];
    }

    protected function getTripleParameterTypes()
    {
        return [
            // modelid
            ParameterType::STRING,
            // subject
            ParameterType::STRING,
            // predicate
            ParameterType::STRING,
            // object
            ParameterType::STRING,
            // l_language
            ParameterType::STRING,
            // epoch
            ParameterType::STRING,
            // author
            ParameterType::STRING,
        ];
    }
}
