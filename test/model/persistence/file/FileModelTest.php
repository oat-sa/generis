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
* Copyright (c) (original work) 2015 Open Assessment Technologies SA
*
*/

namespace oat\generis\test\model\kernel\persistence\file;

use oat\generis\test\GenerisPhpUnitTestRunner;
use oat\generis\model\kernel\persistence\file\FileModel;
use \common_exception_MissingParameter;

class FileModelTest extends GenerisPhpUnitTestRunner
{
    /**
     *
     * @see PHPUnit_Framework_TestCase::setUp()
     */
    public function setUp()
    {
        GenerisPhpUnitTestRunner::initTest();
    }
    
    /**
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     * @return FileModel
     */
    public function testConstruct()
    {
        // $this->markTestSkipped('test it');
        try {
            $model = new FileModel(array());
        } catch (\common_Exception $e) {
            $this->assertInstanceOf('common_exception_MissingParameter', $e);
        }
        $conf = array(
            'file' => 'default'
        );
        $model = new FileModel($conf);
        return $model;
    }
    
    /**
     * @depends testConstruct
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testGetConfig($model)
    {
        $this->assertEquals(array(
            'file' => 'default'
        ), $model->getConfig());
    }

    
    /**
     * @depends testConstruct
     *
     * @author Lionel Lecaque, lionel@taotesting.com
     * @param array $model
     */
    public function testGetRdfInterface($model)
    {
        $this->assertInstanceOf('oat\generis\model\kernel\persistence\file\FileRdf', $model->getRdfInterface());
    }
    
    /**
     * @depends testConstruct
     * @expectedException common_exception_NoImplementation
     * @author Lionel Lecaque, lionel@taotesting.com
     * @param array $model
     */
    public function testGetRdfsInterface($model)
    {
        $model->getRdfsInterface();
    }
    
    /**
     * 
     * @author Lionel Lecaque, lionel@taotesting.com
     */
    public function testGetModelIdFromXml(){
        
    }
    
}

?>