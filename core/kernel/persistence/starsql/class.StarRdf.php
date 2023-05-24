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

use Doctrine\DBAL\ParameterType;
use oat\generis\model\data\Ontology;
use oat\generis\model\data\RdfInterface;

class core_kernel_persistence_starsql_StarRdf implements RdfInterface
{
    public const BATCH_SIZE = 100;

    public const TRIPLE_PARAMETER_TYPE = [
        // modelid
        ParameterType::INTEGER,
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
     * (non-PHPdoc)
     * @see \oat\generis\model\data\RdfInterface::get()
     */
    public function get($subject, $predicate)
    {
        throw new common_Exception('Not implemented');
    }

    /**
     * (non-PHPdoc)
     * @see \oat\generis\model\data\RdfInterface::add()
     */
    public function add(core_kernel_classes_Triple $triple)
    {
        throw new common_Exception('Not implemented! ' . __FILE__ . ' line: ' . __LINE__);
    }

    /**
     * @inheritDoc
     */
    public function addTripleCollection(iterable $triples)
    {
        throw new common_Exception('Not implemented! ' . __FILE__ . ' line: ' . __LINE__);
    }

    protected function insertTriples(array $triples)
    {
        $values = array_map([$this, "tripleToValue"], $triples);
        return $this->insertValues($values);
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
        throw new common_Exception('Not implemented! ' . __FILE__ . ' line: ' . __LINE__);
    }

    /**
     * (non-PHPdoc)
     * @see \oat\generis\model\data\RdfInterface::search()
     */
    public function search($predicate, $object)
    {
        throw new common_Exception('Not implemented');
    }

    public function getIterator()
    {
        throw new common_Exception('Not implemented! ' . __FILE__ . ' line: ' . __LINE__);
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
     * @return array
     */
    protected function tripleToValue(core_kernel_classes_Triple $triple): array
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

    protected function getTripleParameterTypes(): array
    {
        return self::TRIPLE_PARAMETER_TYPE;
    }
}
