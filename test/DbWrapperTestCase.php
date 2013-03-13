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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
?>
<?php
require_once dirname(__FILE__) . '/GenerisTestRunner.php';

/**
 * Test of the DbWrappers.
 * 
 * @author Jerome Bogaerts <jerome.bogaerts@tudor.lu>
 * @package generis
 * @subpackage test
 */
class DbWrapperTestCase extends UnitTestCase {
	
	public function setUp(){
        GenerisTestRunner::initTest();
	}
	
	public function testGetRowCount(){
		$dbWrapper = core_kernel_classes_DbWrapper::singleton();
		$rowCount = $dbWrapper->getRowCount('statements');
		$this->assertTrue(is_int($rowCount));
		$this->assertTrue($rowCount > 0);
	}
	
	public function testGetColumnNames(){
		$dbWrapper = core_kernel_classes_DbWrapper::singleton();
		$columns = $dbWrapper->getColumnNames('statements');
		$this->assertTrue(in_array('modelID', 		$columns));
		$this->assertTrue(in_array('subject', 		$columns));
		$this->assertTrue(in_array('predicate', 	$columns));
		$this->assertTrue(in_array('object', 		$columns));
		$this->assertTrue(in_array('l_language', 	$columns));
		$this->assertTrue(in_array('id', 			$columns));
		$this->assertTrue(in_array('author', 		$columns));
		$this->assertTrue(in_array('stread', 		$columns));
		$this->assertTrue(in_array('stedit', 		$columns));
		$this->assertTrue(in_array('stdelete', 		$columns));
		$this->assertTrue(in_array('epoch', 		$columns));
	}
}
?>