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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 *               2013      (update and modification) Open Assessment Technologies SA;
 */
namespace oat\generis\model\kernel\uri;

use oat\oatbox\service\ConfigurableService;
/**
 * UriProvider implementation based on PHP microtime and rand().
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package generis
 
 */
class MicrotimeRandUriProvider extends ConfigurableService
    implements UriProvider
{
    const OPTION_PERSISTENCE = 'persistence';
    
    const OPTION_NAMESPACE = 'namespace';
    // --- ASSOCIATIONS ---
    
    // --- ATTRIBUTES ---
    
    // --- OPERATIONS ---
    
    /**
     * @return common_persistence_SqlPersistence
     */
    public function getPersistence() {
        return \common_persistence_SqlPersistence::getPersistence($this->getOption(self::OPTION_PERSISTENCE));
    }
    
    /**
     * Generates a URI based on the value of PHP microtime() and rand().
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return string
     */
    public function provide()
    {
        
        $uriExist = false;
        do {
            list($usec, $sec) = explode(" ", microtime());
            $uri = $this->getOption(self::OPTION_NAMESPACE) . 'i' . (str_replace(".", "", $sec . "" . $usec)) . rand(0, 1000);
            $sqlResult = $this->getPersistence()->query("SELECT COUNT(subject) AS num FROM statements WHERE subject = '" . $uri . "'");
            if ($row = $sqlResult->fetch()) {
                $uriExist = $row['num'] > 0;
                $sqlResult->closeCursor();
            }
        } while ($uriExist);
        
        return (string) $uri;
    }

}