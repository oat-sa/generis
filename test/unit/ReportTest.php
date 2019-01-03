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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 * 
 */

use oat\generis\test\TestCase;
use common_report_Report as Report;

class ReportTest extends TestCase {
	
	public function testBasicReport()
	{
	    $report = new common_report_Report(common_report_Report::TYPE_SUCCESS, 'test message');
	    $this->assertFalse($report->hasChildren());
	    $this->assertEquals('test message', (string)$report);
	    $this->assertEquals('test message', $report->getMessage());	    
	    $this->assertEquals(common_report_Report::TYPE_SUCCESS, $report->getType());
	    foreach ($report as $child) {
	        $this->fail('Should not contain children');
	    }
	}
	
	public function testDataInReport()
	{
	    $exception = new Exception('testing');
	    $report = new common_report_Report(common_report_Report::TYPE_INFO, 'test message2', $exception);
	    $this->assertFalse($report->hasChildren());
	    $this->assertEquals('test message2', (string)$report);
	    $this->assertEquals(common_report_Report::TYPE_INFO, $report->getType());
	    foreach ($report as $child) {
	        $this->fail('Should not contain children');
	    }
	    $this->assertSame($exception, $report->getData());
	}
   
	public function testNestedReport()
	{
	    $report = new common_report_Report(common_report_Report::TYPE_WARNING, 'test message3');
	    $sub1 = new common_report_Report(common_report_Report::TYPE_INFO, 'info31');
	    $sub2 = new common_report_Report(common_report_Report::TYPE_ERROR, 'error31');
	    $report->add(array($sub1, $sub2));
	    
	    $this->assertTrue($report->hasChildren());
	    $this->assertEquals('test message3', (string)$report);
	    $this->assertEquals(common_report_Report::TYPE_WARNING, $report->getType());
	    $array = array();
	    foreach ($report as $child) {
	        $array[] = $child;
	    }
	    $this->assertEquals(2, count($array));
	    list($first, $second) = $array;
	    
	    $this->assertFalse($first->hasChildren());
	    $this->assertEquals('info31', (string)$first);
	    $this->assertEquals(common_report_Report::TYPE_INFO, $first->getType());
	    foreach ($first as $child) {
	        $this->fail('Should not contain children');
	    }
	    
	    $this->assertFalse($second->hasChildren());
	    $this->assertEquals('error31', (string)$second);
	    $this->assertEquals(common_report_Report::TYPE_ERROR, $second->getType());
	    foreach ($second as $child) {
	        $this->fail('Should not contain children');
	    }

        $this->assertFalse($report->contains(common_report_Report::TYPE_SUCCESS));
        $this->assertTrue($report->contains(common_report_Report::TYPE_INFO));
	    $this->assertTrue($report->contains(common_report_Report::TYPE_ERROR));
	}

	public function testJsonUnserialize()
	{
		$root = new common_report_Report(common_report_Report::TYPE_WARNING, 'test message3');
		$sub1 = new common_report_Report(common_report_Report::TYPE_INFO, 'info31');
		$sub2 = new common_report_Report(common_report_Report::TYPE_ERROR, 'error31');
		$subsub = new common_report_Report(common_report_Report::TYPE_SUCCESS, 'success31');

		// make report tree
		$sub1->add([$subsub]);
		$root->add([$sub1, $sub2]);

		$json = json_encode($root, JSON_PRETTY_PRINT);

		$report = common_report_Report::jsonUnserialize($json);

		$this->assertTrue($report->hasChildren());
		$this->assertEquals('test message3', (String)$report);
		$this->assertEquals(common_report_Report::TYPE_WARNING, $report->getType());

		$array = array();
		foreach ($report as $child) {
			$array[] = $child;
		}
		$this->assertEquals(2, count($array));
		list($first, $second) = $array;

		$this->assertTrue($first->hasChildren());
		$this->assertEquals('info31', (string)$first);
		$this->assertEquals(common_report_Report::TYPE_INFO, $first->getType());
		foreach ($first as $child) {
			$this->assertEquals('success31', (string)$child);
			$this->assertEquals(common_report_Report::TYPE_SUCCESS, $child->getType());
		}

		$this->assertFalse($second->hasChildren());
		$this->assertEquals('error31', (string)$second);
		$this->assertEquals(common_report_Report::TYPE_ERROR, $second->getType());
		foreach ($second as $child) {
			$this->fail('Should not contain children');
		}

		$this->assertTrue($report->contains(common_report_Report::TYPE_SUCCESS));
		$this->assertTrue($report->contains(common_report_Report::TYPE_INFO));
		$this->assertTrue($report->contains(common_report_Report::TYPE_ERROR));
	}

    public function testGetSuccessesAsFlat()
    {
        $report = new Report(Report::TYPE_INFO);
        $succes_1 = new Report(Report::TYPE_SUCCESS, 'success_1');
        $succes_1_1 = new Report(Report::TYPE_SUCCESS, 'success_1_1');
        $succes_1->add($succes_1_1);
        $succes_2 = new Report(Report::TYPE_SUCCESS, 'success_2');

        $report->add($succes_1);
        $report->add($succes_2);

        $successes = $report->getSuccesses(true);

        $this->assertCount(3, $successes, '3 successes should be returned');
        $this->assertEquals('success_1', (string) array_shift($successes));
        $this->assertEquals('success_1_1', (string) array_shift($successes));
        $this->assertEquals('success_2', (string) array_shift($successes));
    }

    public function testGetInfosAsFlat()
    {
        $report = new Report(Report::TYPE_SUCCESS);
        $info_1 = new Report(Report::TYPE_INFO, 'info_1');
        $info_1_1 = new Report(Report::TYPE_INFO, 'info_1_1');
        $info_1->add($info_1_1);
        $info_2 = new Report(Report::TYPE_INFO, 'info_2');

        $report->add($info_1);
        $report->add($info_2);

        $infos = $report->getInfos(true);

        $this->assertCount(3, $infos, '3 infos should be returned');
        $this->assertEquals('info_1', (string) array_shift($infos));
        $this->assertEquals('info_1_1', (string) array_shift($infos));
        $this->assertEquals('info_2', (string) array_shift($infos));
    }

    public function testGetErrosAsFlat()
    {
        $report = new Report(Report::TYPE_SUCCESS);
        $error_1 = new Report(Report::TYPE_ERROR, 'error_1');
        $error_1_1 = new Report(Report::TYPE_ERROR, 'error_1_1');
        $error_1->add($error_1_1);
        $error_2 = new Report(Report::TYPE_ERROR, 'error_2');

        $report->add($error_1);
        $report->add($error_2);

        $errors = $report->getErrors(true);

        $this->assertCount(3, $errors, '3 errors should be returned');
        $this->assertEquals('error_1', (string) array_shift($errors));
        $this->assertEquals('error_1_1', (string) array_shift($errors));
        $this->assertEquals('error_2', (string) array_shift($errors));
    }
}
