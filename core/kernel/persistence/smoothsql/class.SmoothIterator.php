TODO changes
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
 * Copyright (c) 2002-2008 (original work) 2014 Open Assessment Technologies SA
 *
 */

use core_kernel_api_ModelFactory as ModelFactory;
use oat\oatbox\service\ServiceManager;

/**
 * Iterator over all triples
 * 
 * @author joel bout <joel@taotesting.com>
 * @package generis
 */
class core_kernel_persistence_smoothsql_SmoothIterator
    extends common_persistence_sql_QueryIterator
{
    /**
     * Constructor of the iterator expecting the model ids
     * 
     * @param array $modelIds
     */
    public function __construct(common_persistence_SqlPersistence $persistence, $modelIds = null) {

        $serviceManager = ServiceManager::getServiceManager();
        /** @var ModelFactory $modelFactory */
        $modelFactory = $serviceManager->get(ModelFactory::SERVICE_ID);
        parent::__construct($persistence, $modelFactory->getIteratorQuery($modelIds));
    }
    
    /**
     * (non-PHPdoc)
     * @see Iterator::current()
     * @return core_kernel_classes_Triple
     */
    function current() {
        $statement = parent::current();

        // TODO: create a constructor
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
