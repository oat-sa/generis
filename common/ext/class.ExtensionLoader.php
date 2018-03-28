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
 * Short description of class common_ext_ExtensionLoader
 *
 * @access public
 * @author lionel.lecaque@tudor.lu
 * @package generis
 * @see @license  GNU General Public (GPL) Version 2 http://www.opensource.org/licenses/gpl-2.0.php
 
 */
class common_ext_ExtensionLoader
    extends common_ext_ExtensionHandler
{
    /**
     * Load the extension.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome@taotesting.com>
     * @return mixed
     */
    public function load()
    {
        // load the constants from the manifest
        if ($this->extension->getId() != "generis"){
            foreach ($this->extension->getConstants() as $key => $value) {
                if(!defined($key) && !is_array($value)){
                    define($key, $value);
                }
            }
        }
    }
}