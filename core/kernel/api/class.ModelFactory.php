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
    
    
    private function getModelId($namespace){
        $dbWrapper = core_kernel_classes_DbWrapper::singleton();
        $query = 'SELECT "modelID" FROM "models" WHERE ("modelURI" = ?)';
        $results = $dbWrapper->query($query, array($namespace));
        $result =  current($results->fetchAll());
        if(isset($result['modelID'])){
            return $result['modelID'];
        }
        else {
            return false;
        }
    }
    
    private function addNewModel($namespace){
        $dbWrapper = core_kernel_classes_DbWrapper::singleton();
        $sql = 'INSERT INTO models ("modelURI") VALUES (?)';
        $results = $dbWrapper->insert('models', array('modelURI' =>$namespace));
        
        
    }
    
    
    
    
    public function createModel($namespace, $data){

        $modelId = $this->getModelId($namespace);

        if($modelId === false){
            $this->addNewModel($namespace);
            //bad way, need to find better
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
                    $dbWrapper->insert('statements',
                        array(
                            '"modelID"' =>  $modelId,
                            'subject' =>$subjectUri,
                            'predicate'=> $prop,
                            'object' => $v['value'],
                            'l_language' => isset($v['lang']) ? $v['lang'] : '',
                            'author' => 'http://www.tao.lu/Ontologies/TAO.rdf#installator',
                            'stedit' => 'yyy[admin,administrators,authors]',
                            'stread' => 'yyy[admin,administrators,authors]',
                            'stdelete' => 'yyy[admin,administrators,authors]',
                            'epoch' => new \DateTime()
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
                            'datetime'
                        )
                    );
                }
            }
        }
    }
    
    
    

}