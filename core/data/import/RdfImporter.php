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
 * Copyright (c) 2020 (original work) Open Assessment Technologies SA;
 *
 *
 */

namespace oat\generis\model\data\import;

use common_Exception;
use core_kernel_classes_Triple;
use oat\generis\model\OntologyRdf;
use oat\generis\model\OntologyRdfs;
use oat\generis\model\data\Ontology;
use oat\oatbox\event\EventManager;
use oat\generis\model\data\event\ResourceCreated;
use oat\generis\model\kernel\persistence\file\FileIterator;
use oat\oatbox\service\ConfigurableService;
use oat\generis\model\OntologyAwareTrait;

/**
 * Centralised helper to import RDFS models
 * through the RDF interface
 * @author Joel Bout <joel@taotesting.com>
 */
class RdfImporter extends ConfigurableService
{
    use OntologyAwareTrait;
    /**
     * Imports an RDF file into the ontology as readonly model
     * @param string $filePath
     * @throws common_Exception
     * @return boolean
     */
    public function importFile(string $filePath) {
        if (!file_exists($filePath) || !is_readable($filePath)) {
            throw new common_Exception("Unable to load ontology : $filePath");
        }
        $this->importTriples(new FileIterator($filePath));
        return true;
    }

    /**
     *
     * @param iterable $triples
     * @return void
     */
    public function importTriples(iterable $triples) {
        $rdf = $this->getServiceLocator()->get(Ontology::SERVICE_ID)->getRdfInterface();
        $rdf->addTripleCollection($triples);
        foreach ($triples as $triple) {
            $this->watchResourceCreated($triple);
        }
    }

    /**
     * This will generate a Event if condition is meet
     * @param core_kernel_classes_Triple $triple
     */
    private function watchResourceCreated(core_kernel_classes_Triple $triple)
    {
        if ($triple->predicate == OntologyRdfs::RDFS_SUBCLASSOF || $triple->predicate == OntologyRdf::RDF_TYPE) {
            /** @var EventManager $eventManager */
            $eventManager = $this->getServiceLocator()->get(EventManager::SERVICE_ID);
            $eventManager->trigger(new ResourceCreated($this->getResource($triple->subject)));
        }
    }
}
