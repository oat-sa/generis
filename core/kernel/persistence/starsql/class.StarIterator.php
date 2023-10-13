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
 * Copyright (c) 2023 (original work) Open Assessment Technologies SA
 *
 */

use oat\generis\model\data\Model;
use oat\generis\model\kernel\persistence\smoothsql\search\ComplexSearchService;
use oat\generis\model\kernel\persistence\starsql\FlatRecursiveIterator;

/**
 * Iterator over all triples
 *
 */
class core_kernel_persistence_starsql_StarIterator extends \IteratorIterator implements \RecursiveIterator
{
    public function __construct(Model $model)
    {
        /** @var ComplexSearchService $search */
        $search = $model->getSearchInterface();
        $query = $search->query();

        $queryOptions = $query->getOptions();
        $queryOptions['system_only'] = true;
        $query->setOptions($queryOptions);

        parent::__construct($search->getGateway()->search($query));
    }

    public function hasChildren()
    {
        return true;
    }

    public function current()
    {
        $current = parent::current();

        if ($current instanceof \core_kernel_classes_Literal) {
            $current = new \core_kernel_classes_Resource($current->literal);
        }

        return $current;
    }


    public function getChildren()
    {
        /** @var \core_kernel_classes_Resource $currentResource */
        $currentResource = $this->current();

        return new FlatRecursiveIterator($currentResource->getRdfTriples());
    }
}
