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
 *               2013 (update and modification) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */

/**
 * Enables you to manage the module namespaces
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package generis
 *
 * @deprecated
 */
class common_ext_NamespaceManager
{
    /**
     * Private constructor to force the use of the singleton
     *
     * @access private
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return mixed
     */
    private function __construct()
    {
    }

    /**
     * Main entry point to retrieve the NamespaceManager instance
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return common_ext_NamespaceManager
     */
    public static function singleton()
    {
        return new self();
    }

    /**
     * Get the list of all module's namespaces
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return array
     */
    public function getAllNamespaces()
    {
        return [];
    }

    /**
     * Conveniance method to retrieve the local Namespace
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return common_ext_Namespace
     */
    public function getLocalNamespace()
    {
        return new common_ext_Namespace(
            core_kernel_persistence_smoothsql_SmoothModel::DEFAULT_WRITABLE_MODEL,
            LOCAL_NAMESPACE . '#'
        );
    }

    /**
     * Get a namesapce identified by the modelId or modelUri
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  $modelid
     * @return common_ext_Namespace
     */
    public function getNamespace($modelid)
    {
        return null;
    }

    /**
     * Reset the current NamespaceManager instance.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return void
     */
    public function reset()
    {
    }
}
