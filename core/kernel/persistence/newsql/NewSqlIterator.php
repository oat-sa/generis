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
 * Copyright (c) 2020 (original work) 2014 Open Assessment Technologies SA
 */

namespace oat\generis\model\kernel\persistence\newsql;

use common_persistence_sql_QueryIterator;
use common_persistence_SqlPersistence;
use core_kernel_classes_Triple;

/**
 * @FIXME @TODO WIP Only for review purposes
 */
class NewSqlIterator extends common_persistence_sql_QueryIterator
{
    public function __construct(common_persistence_SqlPersistence $persistence, array $modelIds = null)
    {
        $query = 'SELECT * FROM statements '
            . (is_null($modelIds) ? '' : 'WHERE modelid IN (' . implode(',', $modelIds) . ') ')
            . 'ORDER BY id';

        parent::__construct($persistence, $query);
    }

    function current()
    {
        $statement = parent::current();

        $triple = new core_kernel_classes_Triple();
        $triple->modelid = $statement["modelid"];
        $triple->subject = $statement["subject"];
        $triple->predicate = $statement["predicate"];
        $triple->object = $statement["object"];
        $triple->id = $statement["id"];
        $triple->lg = $statement["l_language"];
        $triple->author = $statement["author"];

        return $triple;
    }
}
