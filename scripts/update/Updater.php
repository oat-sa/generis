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

namespace oat\generis\scripts\update;

use core_kernel_impl_ApiModelOO;
use common_Logger;

/**
 * 
 * @author Joel Bout <joel@taotesting.com>
 */
class Updater extends \common_ext_ExtensionUpdater {
    
    /**
     * 
     * @param string $currentVersion
     * @return string $versionUpdatedTo
     */
    public function update($initialVersion) {

        $currentVersion = $initialVersion;
        if ($currentVersion == '2.7') {
        
            $file = dirname(__FILE__).DIRECTORY_SEPARATOR.'widgetdefinitions_2.7.1.rdf';
        
            $api = core_kernel_impl_ApiModelOO::singleton();
            $success = $api->importXmlRdf('http://www.tao.lu/datatypes/WidgetDefinitions.rdf', $file);
            
            if ($success) {
                $currentVersion = '2.7.1';
            } else{
                common_Logger::w('Import failed for '.$file);
            }
        }
        
        if ($currentVersion == '2.7.1') {
        
            $file = dirname(__FILE__).DIRECTORY_SEPARATOR.'widgetdefinitions_2.7.2.rdf';
        
            $api = core_kernel_impl_ApiModelOO::singleton();
            $success = $api->importXmlRdf('http://www.tao.lu/datatypes/WidgetDefinitions.rdf', $file);
        
            if ($success) {
                $currentVersion = '2.7.2';
            } else{
                common_Logger::w('Import failed for '.$file);
            }
        }
        
        
        return $currentVersion;
    }
}
