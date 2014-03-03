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
 * @author "Lionel Lecaque, <lionel@taotesting.com>"
 * @license GPLv2
 * @package core
 * @subpackage kernel_api
 *
 */
class core_kernel_api_ModelFactory{
    
    
    /**
     * @author "Lionel Lecaque, <lionel@taotesting.com>"
     * @param string $namespace
     * @return string
     */
    private function getModelId($namespace){
        $dbWrapper = core_kernel_classes_DbWrapper::singleton();
        
        $query = 'SELECT modelid FROM models WHERE (modeluri = ?)';
        $results = $dbWrapper->query($query, array($namespace));
       
        return $results->fetchColumn(0);

    }
    
    /**
     * @author "Lionel Lecaque, <lionel@taotesting.com>"
     * @param string $namespace
     */
    private function addNewModel($namespace){
        $dbWrapper = core_kernel_classes_DbWrapper::singleton();
        $results = $dbWrapper->insert('models', array('modeluri' =>$namespace));
        
        
    }
    
    
    
    
    /**
     * @author "Lionel Lecaque, <lionel@taotesting.com>"
     * @param string $namespace
     * @param string $data xml content
     */
    public function createModel($namespace, $data){

        $modelId = $this->getModelId($namespace);
        if($modelId === false){
            common_Logger::d('modelId not found, need to add namespace '. $namespace);
            $this->addNewModel($namespace);
            //TODO bad way, need to find better
            $modelId = $this->getModelId($namespace);
        }
        $modelDefinition = new EasyRdf_Graph($namespace);
        if(is_file($data)){
            $modelDefinition->parseFile($data);
        }else {
            $modelDefinition->parse($data);
        }
        $graph = $modelDefinition->toRdfPhp();
        $resources = $modelDefinition->resources();
        $format = EasyRdf_Format::getFormat('php');
        
        $dbWrapper = core_kernel_classes_DbWrapper::singleton();
        
        $data = $modelDefinition->serialise($format);

        
        foreach ($data as $subjectUri => $propertiesValues){
             
            foreach ($propertiesValues as $prop=>$values){
                foreach ($values as $k => $v) { 
                    $datetime = new \DateTime();
                    $date= $datetime->format('Y-m-d H:i:s');
                    $dbWrapper->insert('statements',
                        array(
                            'modelid' =>  $modelId,
                            'subject' =>$subjectUri,
                            'predicate'=> $prop,
                            'object' => $v['value'],
                            'l_language' => isset($v['lang']) ? $v['lang'] : '',
                            'author' => 'http://www.tao.lu/Ontologies/TAO.rdf#installator',
                            'stedit' => 'yyy[admin,administrators,authors]',
                            'stread' => 'yyy[admin,administrators,authors]',
                            'stdelete' => 'yyy[admin,administrators,authors]',
                           // 'epoch' => $date
                        ),
                        array(
                            PDO::PARAM_INT,
                            PDO::PARAM_STR,
                            PDO::PARAM_STR,
                            PDO::PARAM_STR,
                            PDO::PARAM_STR,
                            PDO::PARAM_STR,
                            PDO::PARAM_STR,
                            PDO::PARAM_STR,
                            PDO::PARAM_STR,
                            //'datetime'
                        )
                    );
                }
            }
        }
        
        return true;
    }
    
    
    

}