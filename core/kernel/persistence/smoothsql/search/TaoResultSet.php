<?php

/**
 * This program is free software; you can redistribute it and/or
 *  modify it under the terms of the GNU General Public License
 *  as published by the Free Software Foundation; under version 2
 *  of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 *  Copyright (c) 2016 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 */

namespace oat\generis\model\kernel\persistence\smoothsql\search;

use core_kernel_classes_Resource;
use oat\search\base\ResultSetInterface;
use oat\search\ResultSet;

use function PHPUnit\Framework\returnArgument;

/**
 * Complex Search resultSet iterator
 *
 * @author Christophe GARCIA <christopheg@taotesting.com>
 */
class TaoResultSet extends ResultSet implements ResultSetInterface, \oat\search\base\ParentFluateInterface
{
    use \oat\search\UsableTrait\ParentFluateTrait;
    use \oat\generis\model\OntologyAwareTrait;

    /**
     *
     * @var \oat\search\QueryBuilder
     */
    protected $countQuery;
    protected $totalCount = null;
    private bool $isTriple = false;

    public function setCountQuery($query)
    {
        $this->countQuery = $query;
        return $this;
    }

    public function setIsTriple(bool $isTriple)
    {
        $this->isTriple = $isTriple;
    }

    /**
    * return total number of result
    * @return integer
    */
    public function total()
    {

        if (is_null($this->totalCount)) {
            $cpt = $this->getParent()->fetchQuery($this->countQuery);
            $this->totalCount = intval($cpt['cpt']);
        }

        return $this->totalCount;
    }

    /**
    * return a new resource create from current subject
    * @return core_kernel_classes_Resource|\core_kernel_classes_Triple
    */
    public function current()
    {
        $index = parent::current();
        if ($this->isTriple) {
            return $this->getTriple($index);
        } else {
            return $this->getResource($index->subject);
        }
    }

    private function getTriple($row): \core_kernel_classes_Triple
    {
        $triple = new \core_kernel_classes_Triple();

        $triple->id = $row->id ?? 0;
        $triple->subject = $row->subject ?? '';
        $triple->object = $row->object ?? $row->subject;

        return $triple;
    }
}
