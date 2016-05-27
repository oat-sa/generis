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
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA
 * 
 */

namespace oat\generis\model;

use oat\generis\model\data\ModelManager;
/**
 * Trait for classes that want to access the ontology
 * 
 * @author Joel Bout <joel@taotesting.com>
 */
trait OntologyAwareTrait
{
    private $model;
    
    function getModel()
    {
        if (is_null($this->model)) {
            return ModelManager::getModel();
        }
        return $this->model;
    }
    
    function setModel($model)
    {
        $this->model = $model;
    }
    
    function getResource($uri) {
        $resource = new \core_kernel_classes_Resource($uri);
        if (!is_null($this->model)) {
            $resource->setModel($this->getModel());
        }
        return $resource;
    }
    
    function getClass($uri) {
        $class = new \core_kernel_classes_Class($uri);
        if (!is_null($this->model)) {
            $class->setModel($this->getModel());
        }
        return $class;
    }
    
    function getProperty($uri) {
        $property = new \core_kernel_classes_Property($uri);
        if (!is_null($this->model)) {
            $property->setModel($this->getModel());
        }
        return $property;
    }	
}