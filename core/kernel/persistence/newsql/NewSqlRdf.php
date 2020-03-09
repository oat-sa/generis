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

use core_kernel_classes_Triple;
use core_kernel_persistence_smoothsql_SmoothRdf;
use oat\generis\Helper\UuidPrimaryKeyTrait;
use oat\generis\model\OntologyRdfs;
use oat\generis\model\OntologyRdf;
use oat\oatbox\event\EventManager;
use oat\generis\model\data\event\ResourceCreated;

/**
 * NewSQL rdf interface
 */
class NewSqlRdf extends core_kernel_persistence_smoothsql_SmoothRdf
{
    use UuidPrimaryKeyTrait;

    public function add(core_kernel_classes_Triple $triple)
    {
        $query = 'INSERT INTO statements ( id, modelId, subject, predicate, object, l_language, epoch, author) VALUES ( ?, ? , ? , ? , ? , ? , ?, ?);';

        $success = $this->getPersistence()
        ->exec(
            $query,
            [
                $this->getUniquePrimaryKey(),
                $triple->modelid,
                $triple->subject,
                $triple->predicate,
                $triple->object,
                is_null($triple->lg) ? '' : $triple->lg,
                $this->getPersistence()->getPlatForm()->getNowExpression(),
                is_null($triple->author) ? '' : $triple->author
            ]
        );
        if ($triple->predicate == OntologyRdfs::RDFS_SUBCLASSOF || $triple->predicate == OntologyRdf::RDF_TYPE) {
            $eventManager = $this->getModel()->getServiceLocator()->get(EventManager::SERVICE_ID);
            $eventManager->trigger(new ResourceCreated($this->getModel()->getResource($triple->subject)));
        }

        return $success;
    }
    
    public function addTripleCollection(iterable $triples)
    {
        $valuesToInsert = [];
        
        foreach ($triples as $triple) {
            $valuesToInsert[] = [
                'id' => $this->getUniquePrimaryKey(),
                'modelid' => $triple->modelid,
                'subject' => $triple->subject,
                'predicate' => $triple->predicate,
                'object' => $triple->object,
                'l_language' => is_null($triple->lg) ? '' : $triple->lg,
                'author' => is_null($triple->author) ? '' : $triple->author,
                'epoch' => $this->getPersistence()->getPlatForm()->getNowExpression()
            ];
            
            if (count($valuesToInsert) >= self::BATCH_SIZE) {
                $this->insertValues($valuesToInsert);
                $valuesToInsert = [];
            }
        }
        
        if (!empty($valuesToInsert)) {
            $this->insertValues($valuesToInsert);
        }
    }
}
