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

namespace oat\generis\model\kernel\persistence\file;

use common_Exception;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use oat\generis\model\data\Ontology;

/**
 * Centralised helper to import RDF models
 * @author bout
 */
class RdfFileImporter implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * Imports an RDF file into the ontology as readonly model
     * @param string $filePath
     * @throws common_Exception
     * @return boolean
     */
    public function import(string $filePath) {
        if (!file_exists($filePath) || !is_readable($filePath)) {
            throw new common_Exception("Unable to load ontology : $filePath");
        }
        $iterator = new FileIterator($filePath);
        $rdf = $this->getServiceLocator()->get(Ontology::SERVICE_ID)->getRdfInterface();

        $success = true;
        $rdf->addTripleCollection($iterator);
        return $success;
    }
}
