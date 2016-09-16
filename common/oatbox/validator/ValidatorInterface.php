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
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA;
 *               
 */
namespace oat\oatbox\validator;

/**
 * validator base interface
 * @author Christophe GARCIA <christopheg@taotesting.com>
 */
interface ValidatorInterface {
    
    /**
     * return validator name
     * @return string
     */
    public function getName();
    
    /**
     * return validator options
     * @return array
     */
    public function getOptions();
    
    /**
     * return error message
     * @return string
     */
    public function getMessage();
    
    /**
     * set up error message
     * @param string $message
     * @return $this
     */
    public function setMessage($message);
    
    /**
     * set up validator options
     * @param array $options
     * @return $this
     */
    public function setOptions(array $options);
    
    /**
     * valid $values
     * @param string $values
     * @return boolean
     */
    public function evaluate($values);
    
}
