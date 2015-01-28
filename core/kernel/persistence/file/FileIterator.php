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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA;
 *               
 * 
 */               

namespace oat\generis\model\kernel\persistence\file;

use EasyRdf_Graph;
use core_kernel_classes_Triple;
use IteratorAggregate;
use ArrayIterator;


class FileIterator implements IteratorAggregate {
    
    private $triples = array();
    
    /**
     * 
     * @param string $file
     * @param string $forceModelId
     */
    public function __construct($file, $forceModelId = null) {
        $modelId = is_null($forceModelId) ? FileModel::getModelIdFromXml($file) : $forceModelId;
        $this->load($modelId, $file);
    }
    
    /**
     * @see IteratorAggregate::getIterator()
     */
    public function getIterator() {
        return new ArrayIterator($this->triples);
    }
    
    /**
     * 
     * @param string $file
     * @throws common_exception_Error
     */
    public static function getModelIdFromXml($file) {
        $xml = simplexml_load_file($file);
        $attrs = $xml->attributes('xml', true);
        if(!isset($attrs['base']) || empty($attrs['base'])){
            throw new common_exception_Error('The namespace of '.$file.' has to be defined with the "xml:base" attribute of the ROOT node');
        }
        $namespaceUri = (string) $attrs['base'];
        $modelId = null;
        foreach (common_ext_NamespaceManager::singleton()->getAllNamespaces() as $namespace) {
            if ($namespace->getUri() == $namespaceUri) {
                $modelId = $namespace->getModelId();
            }
        }
        if (is_null($modelId)) {
            throw new common_exception_Error('The model corresponding to the namespace '.$namespaceUri.' is unknown');
        }
        return $modelId;
    }
    
    /**
     * load triples from rdf file
     * 
     * @param string $modelId
     * @param string $file
     */
    protected function load($modelId, $file) {
        
        $easyRdf = new EasyRdf_Graph();
        $easyRdf->parseFile($file);
        
        foreach ($easyRdf->toRdfPhp() as $subject => $propertiesValues){
            foreach ($propertiesValues as $predicate => $values){
                foreach ($values as $k => $v) {
                    $triple = new core_kernel_classes_Triple();
                    $triple->modelid = $modelId;
                    $triple->subject = $subject;
                    $triple->predicate = $predicate;
                    $triple->object = $v['value'];
                    $triple->lg = isset($v['lang']) ? $v['lang'] : null;
                    $this->triples[] = $triple;
                }
            }
        }
    }
}
