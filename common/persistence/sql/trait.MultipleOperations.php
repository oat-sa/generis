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
 
trait common_persistence_sql_MultipleOperations
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

    /**
     * @example
     *  $table =  'kv_delivery_monitoring'
     *  $index = 'column_key',
     *  $rows =  [
     *         [
     *              'column_key' => '123465',
     *              'values' => [
     *                  'other_column' => 'other value',
     *                  'other_column_1' => 'other value_1',
     *              ]
     *          ]
     *   ]
     * $otherWheres = [ 'key_primary' => 'primary_value']
     *
     * @param string $table
     * @param string $index
     * @param array $rows
     * @param array $otherWheres
     * @return bool
     * @throws Exception
     */
    public function updateMultiple($table, $index, array $rows, array $otherWheres = [])
    {
        $final  = array();
        $ids    = array();
        $params = [];

        if(!count($rows))
            return false;

        if(!isset($index) AND empty($index)) {
            throw new \Exception('You must specify the index');
        }

        foreach ($rows as $row)
        {
            $ids[] = $row[$index];
            foreach ($row['values'] as $column => $updatedValue)
            {
                if ($column !== $index)
                {
                    $final[$column]['values'][] = $updatedValue;
                    $final[$column]['index'][] = $row[$index];
                }
            }
        }

        $cases = '';
        foreach ($final as $keyColumn => $valuesColumn)
        {
            $whens = [];
            foreach ($valuesColumn['values'] as $key => $value) {
                $whens [] = 'WHEN '. $index .' = ? THEN ? ';
                $params[] = $valuesColumn['index'][$key];
                $params[] = $value;
            }

            $cases .= $keyColumn.' = (CASE '. implode("\n", $whens) . "\n"
                . 'ELSE '. $keyColumn.' END), ';
        }
        $idsRepeat = str_repeat('?,', count($ids) - 1) . '?';

        $whereCondition = '';
        $whereCondition[] =  $index . ' IN('.$idsRepeat.')';
        foreach ($ids as $myKey => $myValue) {
            $params[] = ${$myKey} = $myValue;
        }

        foreach ($otherWheres as $columnWhere  => $columnValue) {
            $whereCondition[] = $columnWhere . ' = ?';
            $params[] = $columnValue;
        }

        $query = 'UPDATE ' . $table . ' SET '. substr($cases, 0, -2) . ' WHERE ' . implode(' AND ', $whereCondition);

        return $this->exec($query, $params);
    }
}
