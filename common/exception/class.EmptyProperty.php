<?php
/*  
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
 * 
 */
?>
<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - common/exception/class.EmptyProperty.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 16.04.2012, 11:13:26 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package common
 * @subpackage exception
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include common_Exception
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('common/class.Exception.php');

/* user defined includes */
// section 127-0-1-1--4a441dc4:135c963290f:-8000:0000000000001960-includes begin
// section 127-0-1-1--4a441dc4:135c963290f:-8000:0000000000001960-includes end

/* user defined constants */
// section 127-0-1-1--4a441dc4:135c963290f:-8000:0000000000001960-constants begin
// section 127-0-1-1--4a441dc4:135c963290f:-8000:0000000000001960-constants end

/**
 * Short description of class common_exception_EmptyProperty
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package common
 * @subpackage exception
 */
class common_exception_EmptyProperty
    extends common_Exception
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute property
     *
     * @access private
     * @var Property
     */
    private $property = null;

    /**
     * Short description of attribute resource
     *
     * @access private
     * @var Resource
     */
    private $resource = null;

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @param  Property property
     * @return mixed
     */
    public function __construct( core_kernel_classes_Resource $resource,  core_kernel_classes_Property $property)
    {
        // section 127-0-1-1-6b4920b6:136ba654e76:-8000:00000000000019C8 begin
        $this->resource = $resource;
        $this->property = $property;
        parent::__construct("Property {$property->getLabel()} ({$property->getUri()}) of resource {$resource->getLabel()} ({$resource->getUri()})
            							 should not be empty");
        // section 127-0-1-1-6b4920b6:136ba654e76:-8000:00000000000019C8 end
    }

    /**
     * Short description of method getProperty
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return core_kernel_classes_Property
     */
    public function getProperty()
    {
        $returnValue = null;

        // section 127-0-1-1-6b4920b6:136ba654e76:-8000:00000000000019CF begin
        $returnValue = $this->property;
        // section 127-0-1-1-6b4920b6:136ba654e76:-8000:00000000000019CF end

        return $returnValue;
    }

    /**
     * Short description of method getResource
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return core_kernel_classes_Resource
     */
    public function getResource()
    {
        $returnValue = null;

        // section 127-0-1-1-6b4920b6:136ba654e76:-8000:00000000000019D1 begin
        $returnValue = $this->resource;
        // section 127-0-1-1-6b4920b6:136ba654e76:-8000:00000000000019D1 end

        return $returnValue;
    }

} /* end of class common_exception_EmptyProperty */

?>