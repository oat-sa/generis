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
 * Copyright (c) 2002-2008 (original work) 2014 Open Assessment Technologies SA
 *
 */

use oat\generis\model\data\Model;

/**
 * transitory model for the smooth sql implementation
 * 
 * @author joel bout <joel@taotesting.com>
 * @package generis
 */
class core_kernel_persistence_smoothsql_SmoothModel
    implements Model
{
    /**
     * Id of the persistence to be used for this data-model
     * Currently unused
     * 
     * @var string
     */
    private $persistanceId;
    
    /**
     * Constructor of the smooth model, expects a persistence in the configuration
     * 
     * @param array $configuration
     * @throws common_exception_MissingParameter
     */
    public function __construct($configuration) {
        if (!isset($configuration['persistence'])) {
            throw new common_exception_MissingParameter('persistence', __CLASS__);
        }
        $this->persistanceId = $configuration['persistence']; 
    }
    
    /**
     * (non-PHPdoc)
     * @see \oat\generis\model\data\Model::getConfig()
     */
    public function getConfig() {
        return array(
            'persistence' => $this->persistanceId
        );
    }
    
    /**
     * (non-PHPdoc)
     * @see \oat\generis\model\data\Model::getRdfInterface()
     */
    public function getRdfInterface() {
        throw new \common_Exception('Not implemented');
    }
    
    /**
     * (non-PHPdoc)
     * @see \oat\generis\model\data\Model::getRdfsInterface()
     */
    public function getRdfsInterface() {
        return new core_kernel_persistence_smoothsql_SmoothRdfs();
    }
}