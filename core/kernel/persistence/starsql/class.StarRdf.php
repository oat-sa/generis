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
 * Copyright (c) 2023 (original work) Open Assessment Technologies SA ;
 */

declare(strict_types=1);

use EasyRdf\Format;
use EasyRdf\Graph;
use Laudis\Neo4j\Databags\Statement;
use oat\generis\model\data\RdfInterface;
use WikibaseSolutions\CypherDSL\Query;

class core_kernel_persistence_starsql_StarRdf implements RdfInterface
{
    /**
     * @var core_kernel_persistence_starsql_StarModel
     */
    private $model;

    public function __construct(core_kernel_persistence_starsql_StarModel $model)
    {
        $this->model = $model;
    }

    protected function getPersistence()
    {
        return $this->model->getPersistence();
    }

    /**
     * {@inheritDoc}
     */
    public function get($subject, $predicate)
    {
        throw new common_Exception('Not implemented');
    }

    /**
     * {@inheritDoc}
     */
    public function search($predicate, $object)
    {
        throw new common_Exception('Not implemented');
    }

    /**
     * {@inheritDoc}
     */
    public function add(core_kernel_classes_Triple $triple)
    {
        $this->addTripleCollection([$triple]);
    }

    /**
     * {@inheritDoc}
     */
    public function addTripleCollection(iterable $triples)
    {
        $nTriple = $this->triplesToValues($triples, Format::getFormat('ntriples'));

        $persistence = $this->getPersistence();
        $persistence->run(
            'CALL n10s.rdf.import.inline($nTriple,"N-Triples")',
            ['nTriple' => $nTriple]
        );

        $systemTripleQuery = $this->createSystemTripleQuery($triples);
        if ($systemTripleQuery instanceof Statement) {
            $persistence->runStatement($systemTripleQuery);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function remove(core_kernel_classes_Triple $triple)
    {
        $nTriple = $this->triplesToValues([$triple], Format::getFormat('ntriples'));

        $persistence = $this->getPersistence();
        $persistence->run(
            'CALL n10s.rdf.delete.inline($nTriple,"N-Triples")',
            ['nTriple' => $nTriple]
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getIterator()
    {
        return new RecursiveIteratorIterator(new core_kernel_persistence_starsql_StarIterator($this->model));
    }

    /**
     * @param iterable $tripleList
     * @param Format $format
     *
     * @return string
     */
    private function triplesToValues(iterable $tripleList, Format $format): string
    {
        $graph = new Graph();

        /** @var core_kernel_classes_Triple $triple */
        foreach ($tripleList as $triple) {
            if (!empty($triple->lg)) {
                $graph->addLiteral(
                    $triple->subject,
                    $triple->predicate,
                    $triple->object,
                    $triple->lg
                );
            } elseif (\common_Utils::isUri($triple->object)) {
                $graph->addResource($triple->subject, $triple->predicate, $triple->object);
            } else {
                $graph->addLiteral($triple->subject, $triple->predicate, $triple->object);
            }
        }

        return $graph->serialise($format);
    }

    private function createSystemTripleQuery(iterable $tripleList): ?Statement
    {
        $systemSubjectList = [];
        /** @var core_kernel_classes_Triple $triple */
        foreach ($tripleList as $triple) {
            if (
                !empty($triple->modelid)
                && $triple->modelid != \core_kernel_persistence_starsql_StarModel::DEFAULT_WRITABLE_MODEL
            ) {
                $systemSubjectList[$triple->subject] = true;
            }
        }

        $query  = null;
        if (!empty($systemSubjectList)) {
            $systemNode = Query::node('Resource');
            $query = Query::new()->match($systemNode)
                ->where($systemNode->property('uri')->in(array_keys($systemSubjectList)))
                ->set($systemNode->labeled('System'));

            $query = Statement::create($query->build());
        }

        return $query;
    }
}
