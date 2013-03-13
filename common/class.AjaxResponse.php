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
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);\n *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
?>
<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - common/class.AjaxResponse.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 20.02.2012, 11:20:03 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package common
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 127-0-1-1-641a0ff0:1359a3da29e:-8000:0000000000001B1E-includes begin
// section 127-0-1-1-641a0ff0:1359a3da29e:-8000:0000000000001B1E-includes end

/* user defined constants */
// section 127-0-1-1-641a0ff0:1359a3da29e:-8000:0000000000001B1E-constants begin
// section 127-0-1-1-641a0ff0:1359a3da29e:-8000:0000000000001B1E-constants end

/**
 * Short description of class common_AjaxResponse
 *
 * @access public
 * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package common
 */
class common_AjaxResponse
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  array options
     * @return mixed
     */
    public function __construct($options = array())
    {
        // section 127-0-1-1-641a0ff0:1359a3da29e:-8000:0000000000001B1F begin
        
        $success    = isset($options['success'])    ?$options['success']    :true;
        $type       = isset($options['type'])       ?$options['type']       :'json';
        $data       = isset($options['data'])       ?$options['data']       :null;
        $message    = isset($options['message'])    ?$options['message']    :'';
        
        //position the header of the response
        $context = Context::getInstance();
        $context->getResponse()->setContentHeader('text/json');
        //set the response object
        $response = array(
            'success'           => $success
            , 'type'            => $type
            , 'message'         => $message
            , 'data'            => $data
        );
        
        //write the response
        echo json_encode($response);
        
        // section 127-0-1-1-641a0ff0:1359a3da29e:-8000:0000000000001B1F end
    }

} /* end of class common_AjaxResponse */

?>