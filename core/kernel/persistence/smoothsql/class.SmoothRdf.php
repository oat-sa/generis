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
            ]
        );

        if ($success > 0) {
            $this->watchResourceCreated($triple);
        }

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
        if ($isInsertionSuccessful) {
            foreach ($triples as $triple) {
                $this->watchResourceCreated($triple);
            }
        }
        return $isInsertionSuccessful;
    }

    protected function insertValues(array $valuesToInsert)
    {
        return $this->getPersistence()->insertMultiple('statements', $valuesToInsert);
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
     */
    private function watchResourceCreated(core_kernel_classes_Triple $triple)
    {
        if ($triple->predicate == OntologyRdfs::RDFS_SUBCLASSOF || $triple->predicate == OntologyRdf::RDF_TYPE) {
            /** @var EventManager $eventManager */
            $eventManager = $this->model->getServiceLocator()->get(EventManager::SERVICE_ID);
            $eventManager->trigger(new ResourceCreated($this->model->getResource($triple->subject)));
        }
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
}
