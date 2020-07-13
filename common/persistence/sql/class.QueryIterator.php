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
 * Copyright (c) 2002-2020 (original work) 2014 Open Assessment Technologies SA
 *
 */

/**
 * Iterator over all triples
 *
 * @author joel bout <joel@taotesting.com>
 */
class common_persistence_sql_QueryIterator implements Iterator
{
    private const CACHE_SIZE = 100;

    /**
     * @var common_persistence_SqlPersistence
     */
    private $persistence;

    private $query;

    private $params;

    /**
     * Id of the current instance
     *
     * @var int
     */
    private $currentResult;

    /**
     * Return statements of the last query
     *
     * @var array
     */
    private $cache;
    private $types;
    private $limit;

    public function __construct(
        common_persistence_SqlPersistence $persistence,
        $query,
        $params = [],
        array $types = [],
        int $limit = self::CACHE_SIZE
    ) {
        $this->persistence = $persistence;
        $this->query = $query;
        $this->params = $params;
        $this->types = $types;
        $this->limit = $limit;
        $this->rewind();
    }

    public function rewind()
    {
        $this->load(0);
    }

    /**
     * @return core_kernel_classes_Triple
     */
    public function current()
    {
        return $this->cache[$this->currentResult];
    }

    public function key()
    {
        return $this->currentResult;
    }

    public function next()
    {
        if ($this->valid()) {
            $last = $this->key();
            $this->currentResult++;
            if (!isset($this->cache[$this->currentResult])) {
                $this->load($last + 1);
            }
        }
    }

    public function valid()
    {
        return !empty($this->cache);
    }

    /**
     * Loads the next n results, starting with $offset
     *
     * @param int $offset
     */
    protected function load($offset)
    {
        $query = $this->persistence->getPlatForm()->limitStatement($this->query, $this->limit, $offset);
        $result = $this->persistence->query($query, $this->params, $this->types);

        $this->cache = [];
        $pos = $offset;
        while ($statement = $result->fetch()) {
            $this->cache[$pos++] = $statement;
        }

        $this->currentResult = $offset;
    }
}
