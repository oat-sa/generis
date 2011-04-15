<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - common\ext\class.ClassLoader.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 24.03.2010, 14:38:37 with ArgoUML PHP module 
 * (last revised $Date: 2008-04-19 08:22:08 +0200 (Sat, 19 Apr 2008) $)
 *
 * @author lionel.lecaque@tudor.lu
 * @package common
 * @see @license  GNU General Public (GPL) Version 2 http://www.opensource.org/licenses/gpl-2.0.php
 * @subpackage ext
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section -87--2--3--76-f244745:1240028df28:-8000:00000000000017B5-includes begin
// section -87--2--3--76-f244745:1240028df28:-8000:00000000000017B5-includes end

/* user defined constants */
// section -87--2--3--76-f244745:1240028df28:-8000:00000000000017B5-constants begin
// section -87--2--3--76-f244745:1240028df28:-8000:00000000000017B5-constants end

/**
 * Short description of class common_ext_ClassLoader
 *
 * @access public
 * @author lionel.lecaque@tudor.lu
 * @package common
 * @see @license  GNU General Public (GPL) Version 2 http://www.opensource.org/licenses/gpl-2.0.php
 * @subpackage ext
 */
class common_ext_ClassLoader
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * array[folder] to register set of packages to the autoload
     *
     * @access private
     * @var array
     */
    private $packages = array();

    /**
     * array[class => file] to register set of files to the autoload
     *
     * @access private
     * @var array
     */
    private $files = array();

    // --- OPERATIONS ---

    /**
     * add folder to the classLoader
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  string package
     * @return mixed
     */
    public function addPackage($package )
    {
        // section -87--2--3--76-f244745:1240028df28:-8000:00000000000017B8 begin
		$this->packages[] = $package;
        // section -87--2--3--76-f244745:1240028df28:-8000:00000000000017B8 end
    }

    /**
     * add file to the classLoader for a specific class
     *
     * @access public
     * @author lionel.lecaque@tudor.lu
     * @param  string file
     * @param  string class
     * @return mixed
     */
    public function addFile($file, $class)
    {
        // section -87--2--3--76-f244745:1240028df28:-8000:00000000000017BB begin
		$this->files[$class] = $file;
        // section -87--2--3--76-f244745:1240028df28:-8000:00000000000017BB end
    }

    /**
     * return all files the classloader will have to autoload
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return mixed
     */
    public function getFiles()
    {
        // section -87--2--3--76-f244745:1240028df28:-8000:00000000000017CA begin
        return $this->files;
        // section -87--2--3--76-f244745:1240028df28:-8000:00000000000017CA end
    }

    /**
     * return all packages the classloader will have to autoload
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return mixed
     */
    public function getPackages()
    {
        // section -87--2--3--76-f244745:1240028df28:-8000:00000000000017CC begin
        return $this->packages;
        // section -87--2--3--76-f244745:1240028df28:-8000:00000000000017CC end
    }

    /**
     * set an array[class => files] the classloader have to autoload
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  array files
     * @return mixed
     */
    public function setFiles($files)
    {
        // section -87--2--3--76-f244745:1240028df28:-8000:00000000000017CE begin
        $this->files = $files;
        // section -87--2--3--76-f244745:1240028df28:-8000:00000000000017CE end
    }

    /**
     * set an array[folder] the classloader have to autoload
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  array packages
     * @return mixed
     */
    public function setPackages($packages)
    {
        // section -87--2--3--76-f244745:1240028df28:-8000:00000000000017D1 begin
		$this->packages = $packages;
        // section -87--2--3--76-f244745:1240028df28:-8000:00000000000017D1 end
    }

} /* end of class common_ext_ClassLoader */

?>