<?php
/*  
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
 * Copyright (c) 2017 (original work) Open Assessment Technologies S.A.
 * 
 */

use oat\generis\model\OntologyRdfs;
use oat\generis\test\GenerisPhpUnitTestRunner;

use oat\oatbox\service\ServiceManager;

class ComplexSearchTest extends GenerisPhpUnitTestRunner
{	
    private $search;
    
    protected function setUp(){
        GenerisPhpUnitTestRunner::initTest();

		$this->object = new core_kernel_classes_Class(OntologyRdfs::RDFS_RESOURCE);
		$this->object->debug = __METHOD__;
        
        $this->search = ServiceManager::getServiceManager()->get(\oat\generis\model\kernel\persistence\smoothsql\search\ComplexSearchService::SERVICE_ID);
	}
    
    public function testRandomized()
    {
        $queryBuilder = $this->search->query();
        $query = $this->search->searchType($queryBuilder, 'http://www.w3.org/2000/01/rdf-schema#Resource', true);
        $queryBuilder->setCriteria($query)->setRandom();
        $queryBuilder->setCriteria($query)->setLimit(10);
        
        // Initial call
        $result = $result = $this->search->getGateway()->search($queryBuilder);
        $pickup = array();
        foreach ($result as $r) {
            $pickup[] = $r->getUri();
        }
        
        for ($i = 0; $i < 10; $i++) {
            // Leave when result different from initial one.
            $result = $result = $this->search->getGateway()->search($queryBuilder);
            $newPickup = array();
            foreach ($result as $r) {
                $newPickup[] = $r->getUri();
            }
            
            if ($pickup !== $newPickup) {
                break;
            }
        }
        
        if ($i >= 10) {
            $this->fail('The Complex Search API randomization failed.');
        } else {
            $this->assertTrue(true);
        }
    }
}
?>
