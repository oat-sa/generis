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

namespace oat\generis\test\unit\core\kernel\persistence;

use core_kernel_classes_Triple;
use oat\generis\model\data\Ontology;
use oat\generis\model\OntologyRdf;
use oat\generis\model\OntologyRdfs;
use oat\generis\test\GenerisTestCase;

/**
 * Class OntologyRdfTest
 */
class OntologyRdfTest extends GenerisTestCase
{
    /**
     * @dataProvider getOntologies
     */
    public function testRdfInterface(Ontology $ontology)
    {
        $this->assertEquals(0, $this->getTripleCount($ontology));
        $triple1 = core_kernel_classes_Triple::createTriple(0, 'subject', 'predicate', 'object');
        $triple2 = core_kernel_classes_Triple::createTriple(0, 'subject', 'predicate', 'object2');
        $ontology->getRdfInterface()->add($triple1);
        $this->assertEquals(1, $this->getTripleCount($ontology));
        $ontology->getRdfInterface()->remove($triple2);
        $this->assertEquals(1, $this->getTripleCount($ontology));
        $ontology->getRdfInterface()->remove($triple1);
        $this->assertEquals(0, $this->getTripleCount($ontology));
    }


    /**
     * @dataProvider getOntologies
     */
    public function testAdd(Ontology $ontology)
    {
        $this->assertEquals(0, $this->getTripleCount($ontology));
        $triple1 = core_kernel_classes_Triple::createTriple(0, 'subject', 'predicate', 'object');
        $ontology->getRdfInterface()->add($triple1);
        $this->assertEquals(1, $this->getTripleCount($ontology));
        $triples = $ontology->getRdfInterface();
        /** @var core_kernel_classes_Triple $currentResource */
        foreach ($triples as $testTriple) {
            $this->assertEquals($triple1->subject, $testTriple->subject);
            $this->assertEquals($triple1->predicate, $testTriple->predicate);
            $this->assertEquals($triple1->object, $testTriple->object);
            $this->assertEquals($triple1->lg, $testTriple->lg);
        }

        $ontology->getRdfInterface()->remove($triple1);
        $this->assertEquals(0, $this->getTripleCount($ontology));
    }

    /**
     * @dataProvider getOntologies
     */
    public function testAddTripleCollection(Ontology $ontology)
    {
        $this->assertInstanceOf(Ontology::class, $ontology);
        $this->assertEquals(0, $this->getTripleCount($ontology));

        $triple1 = core_kernel_classes_Triple::createTriple(0, 'subject', 'predicate', 'object');
        $triple2 = core_kernel_classes_Triple::createTriple(0, 'subject2', OntologyRdf::RDF_TYPE, 'object2');
        $triple3 = core_kernel_classes_Triple::createTriple(0, 'subject3', OntologyRdfs::RDFS_SUBCLASSOF, 'object2');
        $ontology->getRdfInterface()->addTripleCollection([$triple1, $triple2, $triple3]);

        $this->assertEquals(3, $this->getTripleCount($ontology));
    }


    /**
     * @dataProvider getOntologies
     */
    public function testRemove(Ontology $ontology)
    {
        $this->assertInstanceOf(Ontology::class, $ontology);
        $this->assertEquals(0, $this->getTripleCount($ontology));
        $triple1 = core_kernel_classes_Triple::createTriple(0, 'subject', 'predicate', 'object');
        $ontology->getRdfInterface()->add($triple1);
        $this->assertEquals(1, $this->getTripleCount($ontology));
        $ontology->getRdfInterface()->remove($triple1);
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
