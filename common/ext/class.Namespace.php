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
 * Copyright (c) 2008-2010 (original work) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 *
 */

/**
 * Short description of class common_ext_Namespace
 *
 * @access  public
 * @author  Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package generis
 */
class common_ext_Namespace
{
    /**
     * A unique identifier of the namespace
     *
     * @var string
     */
    protected $modelId;

    /**
     * the namespace URI
     *
     * @var string
     */
    protected $uri;

    /**
     * Namespace constructor.
     *
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     *
     * @param  string id
     * @param  string uri
     */
    public function __construct($id = '', $uri = '')
    {
        $this->modelId = (string) $id;
        $this->uri = $uri;
    }

    /**
     * Get the identifier of the namespace instance
     *
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return string
     */
    public function getModelId()
    {
        return $this->modelId;
    }

    /**
     * Get the namespace URI
     *
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * Magic method, return the Namespace URI
     *
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return string
     */
    public function __toString()
    {
        return $this->getUri();
    }

    /**
     * Remove a namespace from the ontology. All triples bound to the model will
     * be removed.
     *
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     *
     * @return boolean
     * @throws core_kernel_persistence_Exception
     */
    public function remove()
    {
        $db = core_kernel_classes_DbWrapper::singleton();

        // TODO refactor this to use triple store abstraction.
        if (false === $db->exec("DELETE FROM statements WHERE modelid = ?", [$this->getModelId()])) {
            return false;
        }

        // TODO refactor this to use triple store abstraction.
        return $db->exec("DELETE FROM models WHERE modelid = ?", [$this->getModelId()]);
    }
}
