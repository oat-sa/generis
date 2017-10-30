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
 * Copyright (c) 2017 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 * @author "Jérôme Bogaerts, <jerome@taotesting.com>"
 * @license GPLv2
 * @package generis
 *
 */
 
trait common_persistence_sql_InsertMultiple
{
    public function insertMultiple($tableName, array $data)
    {
        if (is_array($data) && count($data) > 0) {
            
            $platform = $this->getPlatform();
            
            $quotedColumnIdentifiers = array_map(
                function ($value) use ($platform) {
                    return $platform->quoteIdentifier($value);
                },
                array_keys($data[0])
            );
            
            $query = "INSERT INTO ${tableName} (" . implode(', ', $quotedColumnIdentifiers) . ') VALUES ';
            $valuesQueries = [];
            $allValues = [];
            
            foreach ($data as $values) {
                $valuesQueries[] .= '(' . implode(', ', array_fill(0, count($values), '?')) . ')';
                $allValues = array_merge($allValues, array_values($values));
            }
            
            $query .= implode(', ', $valuesQueries);
            
            return $this->exec($query, $allValues);
        } else {
            return 0;
        }
    }
}
