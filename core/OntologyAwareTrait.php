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
use oat\generis\model\data\Model;
/**
 * Trait for classes that want to access the ontology
 * 
 * @author Joel Bout <joel@taotesting.com>
 */
trait OntologyAwareTrait
{
    private $model;
    
    /**
     * Return the used model
     * @return Model
     */
    function getModel()
    {
        if (is_null($this->model)) {
            return ModelManager::getModel();
        }
        return $this->model;
    }
    
    /**
     * Sets the model to use
     * @param Model $model
     */
    function setModel(Model $model)
    {
        $this->model = $model;
    }
    
    /**
     * @param string $uri
     * @return \core_kernel_classes_Resource
     */
    function getResource($uri) {
        return $this->getModel()->getResource($uri);
    }
    
    /**
     * @param string $uri
     * @return \core_kernel_classes_Class
     */
     function getClass($uri) {
        return $this->getModel()->getClass($uri);
    }
    
    /**
     * @param string $uri
     * @return \core_kernel_classes_Property
     */
    function getProperty($uri) {
        return $this->getModel()->getProperty($uri);
    }	
}