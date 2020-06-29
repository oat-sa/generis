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
 */

namespace oat\generis\test\unit\core\data\import;

use core_kernel_classes_Triple;
use oat\generis\model\data\Ontology;
use oat\generis\test\GenerisTestCase;
use oat\generis\model\data\import\RdfImporter;

class RdfImportTest extends GenerisTestCase
{
    /**
     * @dataProvider getOntologies
     */
    public function testRdfTripleImport(Ontology $ontology)
    {
        $this->assertEquals(0, $this->getTripleCount($ontology));
        $triple1 = core_kernel_classes_Triple::createTriple(0, 'subject', 'predicate', 'object');
        $triple2 = core_kernel_classes_Triple::createTriple(0, 'subject', 'predicate', 'object2');
        $importer = new RdfImporter();
        $importer->setServiceLocator($ontology->getServiceLocator());
        $importer->importTriples([$triple1, $triple2]);
        $this->assertEquals(2, $this->getTripleCount($ontology));
    }

    /**
     * @dataProvider getOntologies
     */
    public function testRdfFileImport(Ontology $ontology)
    {
        $this->assertEquals(0, $this->getTripleCount($ontology));
        $importer = new RdfImporter();
        $importer->setServiceLocator($ontology->getServiceLocator());
        $importer->importFile(__DIR__.'/../../../../samples/rdf/generis.rdf');
        $this->assertEquals(3, $this->getTripleCount($ontology));
    }

    private function getTripleCount(Ontology $ontology)
    {
        return iterator_count($ontology->getRdfInterface()->getIterator());
    }

    public function getOntologies()
    {
        return [
            [$this->getOntologyMock()],
            [$this->getNewSqlMock()],
        ];
    }
}
