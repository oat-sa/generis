<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API -
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 21.10.2011, 16:23:23 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package core
 * @subpackage kernel_versioning
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 127-0-1-1-6b8f17d3:132493e0488:-8000:0000000000001659-includes begin
// section 127-0-1-1-6b8f17d3:132493e0488:-8000:0000000000001659-includes end

/* user defined constants */
// section 127-0-1-1-6b8f17d3:132493e0488:-8000:0000000000001659-constants begin
// section 127-0-1-1-6b8f17d3:132493e0488:-8000:0000000000001659-constants end

/**
 * Short description of class core_kernel_versioning_FileInterface
 *
 * @access public
 * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package core
 * @subpackage kernel_versioning
 */
interface core_kernel_versioning_FileInterface
{


    // --- OPERATIONS ---

    /**
     * Short description of method commit
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  File resource
     * @param  string message
     * @param  string path
     * @return boolean
     */
    public function commit( core_kernel_classes_File $resource, $message, $path);

    /**
     * Short description of method update
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  File resource
     * @param  string path
     * @param  int revision
     * @return boolean
     */
    public function update( core_kernel_classes_File $resource, $path, $revision = null);

    /**
     * Short description of method revert
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  File resource
     * @param  int revision
     * @param  string msg
     * @return boolean
     */
    public function revert( core_kernel_classes_File $resource, $revision = null, $msg = "");

    /**
     * Short description of method delete
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  File resource
     * @param  string path
     * @return boolean
     */
    public function delete( core_kernel_classes_File $resource, $path);

    /**
     * Short description of method getVersion
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  File resource
     * @return string
     */
    public function getVersion( core_kernel_versioning_File $resource);

    /**
     * Short description of method add
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  File resource
     * @param  string path
     * @return boolean
     */
    public function add( core_kernel_classes_File $resource, $path);

    /**
     * Short description of method isVersioned
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  File resource
     * @param  string path
     * @return boolean
     */
    public function isVersioned( core_kernel_classes_File $resource, $path);

    /**
     * Short description of method isUnversioned
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  File resource
     * @param  string path
     * @return boolean
     */
    public function isUnversioned( core_kernel_classes_File $resource, $path);

    /**
     * Short description of method getHistory
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  File resource
     * @param  string path
     * @return array
     */
    public function getHistory( core_kernel_classes_File $resource, $path);

    /**
     * Short description of method hasLocalChanges
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  File resource
     * @param  string path
     * @return boolean
     */
    public function hasLocalChanges( core_kernel_classes_File $resource, $path);

} /* end of interface core_kernel_versioning_FileInterface */

?>