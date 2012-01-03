<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API -
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 03.01.2012, 19:01:49 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package core
 * @subpackage kernel_versioning_subversionWindows
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include core_kernel_versioning_FileInterface
 *
 * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
 */
require_once('core/kernel/versioning/interface.FileInterface.php');

/* user defined includes */
// section 127-0-1-1-a831e14:134415460c1:-8000:000000000000188A-includes begin
// section 127-0-1-1-a831e14:134415460c1:-8000:000000000000188A-includes end

/* user defined constants */
// section 127-0-1-1-a831e14:134415460c1:-8000:000000000000188A-constants begin
// section 127-0-1-1-a831e14:134415460c1:-8000:000000000000188A-constants end

/**
 * Short description of class core_kernel_versioning_subversionWindows_File
 *
 * @access public
 * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package core
 * @subpackage kernel_versioning_subversionWindows
 */
class core_kernel_versioning_subversionWindows_File
        implements core_kernel_versioning_FileInterface
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute instance
     *
     * @access public
     * @var File
     */
    public static $instance = null;

    // --- OPERATIONS ---

    /**
     * Short description of method commit
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  File resource
     * @param  string message
     * @param  string path
     * @param  boolean recursive
     * @return boolean
     * @see core_kernel_versioning_File::commit()
     */
    public function commit( core_kernel_classes_File $resource, $message, $path, $recursive = false)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-6b8f17d3:132493e0488:-8000:000000000000165A begin
        
        try {
            $rStr = !$recursive ? '--non-recursive' : '';
        	$returnValue = core_kernel_versioning_subversionWindows_Utils::exec($resource, 'commit ' . $path . ' -m "'. $message . '" '.$rStr);
        }
        catch (Exception $e) {
        	die('Error code `svn_error_commit` in ' . $e->getMessage());
        }

        // section 127-0-1-1-6b8f17d3:132493e0488:-8000:000000000000165A end

        return (bool) $returnValue;
    }

    /**
     * Short description of method update
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  File resource
     * @param  string path
     * @param  int revision
     * @return boolean
     * @see core_kernel_versioning_File::update()
     */
    public function update( core_kernel_classes_File $resource, $path, $revision = null)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-6b8f17d3:132493e0488:-8000:000000000000165C begin
        try {
        	$returnValue = core_kernel_versioning_subversionWindows_Utils::exec($resource, 'update "' . $path .'"');
        } 
        catch (Exception $e) {
        	die('Error code `svn_error_update` in ' . $e->getMessage());
        }
        // section 127-0-1-1-6b8f17d3:132493e0488:-8000:000000000000165C end

        return (bool) $returnValue;
    }

    /**
     * Short description of method revert
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  File resource
     * @param  int revision
     * @param  string msg
     * @return boolean
     * @see core_kernel_versioning_File::revert()
     */
    public function revert( core_kernel_classes_File $resource, $revision = null, $msg = "")
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-6b8f17d3:132493e0488:-8000:000000000000165E begin
        try {
        	$returnValue = core_kernel_versioning_subversionWindows_Utils::exec($resource, 'revert "' . $path.'"');
        }
        catch (Exception $e) {
        	die('Error code `svn_error_revert` in ' . $e->getMessage());
        }
        // section 127-0-1-1-6b8f17d3:132493e0488:-8000:000000000000165E end

        return (bool) $returnValue;
    }

    /**
     * Short description of method delete
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  File resource
     * @param  string path
     * @return boolean
     * @see core_kernel_versioning_File::delete()
     */
    public function delete( core_kernel_classes_File $resource, $path)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-7caa4aeb:1324dd0a1a4:-8000:0000000000001678 begin
        
        try {
        	$returnValue = core_kernel_versioning_subversionWindows_Utils::exec($resource, 'delete "' . $path.'"');
        }
        catch (Exception $e) {
        	die('Error code `svn_error_delete` in ' . $e->getMessage());
        }
        
        // section 127-0-1-1-7caa4aeb:1324dd0a1a4:-8000:0000000000001678 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method add
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  File resource
     * @param  string path
     * @param  boolean recursive
     * @return boolean
     * @see core_kernel_versioning_File::add()
     */
    public function add( core_kernel_classes_File $resource, $path, $recursive = false)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-13a27439:132dd89c261:-8000:00000000000016F1 begin
        
        try {
        	$returnValue = core_kernel_versioning_subversionWindows_Utils::exec($resource, 'add --non-recursive "' . $path .'"');
        } 
        catch (Exception $e) {
        	die('Error code `svn_error_add` in ' . $e->getMessage());
        }
        
        // section 127-0-1-1-13a27439:132dd89c261:-8000:00000000000016F1 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method isVersioned
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  File resource
     * @param  string path
     * @return boolean
     * @see core_kernel_versioning_File::isVersioned()
     */
    public function isVersioned( core_kernel_classes_File $resource, $path)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-13a27439:132dd89c261:-8000:00000000000016FA begin
        
        $status = core_kernel_versioning_subversionWindows_Utils::exec($resource, 'status "' . $path .'"');
        
        // If the file has a status, check the status is not unversioned or added
        if(!empty($status)){
        	$resourceStatus = null;
                $lines = explode("\n", $status);
                foreach ($lines as $line) {
                    //if(preg_match('#^.*'.preg_quote($path).'$#', $line)){
                    if(strstr($line, realpath($path)) !== false){
                        $resourceStatus = $line;
                    }
                }
                if(!is_null($resourceStatus)){
                    $text_status = substr($resourceStatus, 0, 1);
                    if($text_status		!= '?'	// FILE UNVERSIONED
                            && $text_status	!= 'A'	// JUST ADDED FILE
                            && $text_status	!= 'D'	// JUST DELETED FILE
                    ){
                            // 6. SVN_WC_STATUS_DELETED
                            // 7. SVN_WC_STATUS_REPLACED
                            // 8. SVN_WC_STATUS_MODIFIED
                            $returnValue = true;
                    }
                }
        } 
        else {
            //the file is maybe inside an unversioned folder (status null, info null)
        	$info = core_kernel_versioning_subversionWindows_Utils::exec($resource, 'info "' . $path .'"');
            if (!empty($info) && file_exists($path)){
                $returnValue = true;
            }
        }
        
        // section 127-0-1-1-13a27439:132dd89c261:-8000:00000000000016FA end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getHistory
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  File resource
     * @param  string path
     * @return array
     * @see core_kernel_versioning_File::gethistory()
     */
    public function getHistory( core_kernel_classes_File $resource, $path)
    {
        $returnValue = array();

        // section 127-0-1-1--57fd8084:132ecf4b934:-8000:00000000000016FB begin
        throw new core_kernel_versioning_subversionWindows_Repository("The function (".__METHOD__.") is not available in this versioning implementation (".__CLASS__.")");
        // section 127-0-1-1--57fd8084:132ecf4b934:-8000:00000000000016FB end

        return (array) $returnValue;
    }

    /**
     * Short description of method hasLocalChanges
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  File resource
     * @param  string path
     * @return boolean
     * @see core_kernel_versioning_File::hasLocalChanges()
     */
    public function hasLocalChanges( core_kernel_classes_File $resource, $path)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--485428cc:133267d2802:-8000:0000000000001732 begin
        throw new core_kernel_versioning_subversionWindows_Repository("The function (".__METHOD__.") is not available in this versioning implementation (".__CLASS__.")");
        // section 127-0-1-1--485428cc:133267d2802:-8000:0000000000001732 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method singleton
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return core_kernel_classes_File
     */
    public static function singleton()
    {
        $returnValue = null;

        // section 127-0-1-1-a831e14:134415460c1:-8000:000000000000188E begin
        
        if (self::$instance == null){
			self::$instance = new self();
		}
		$returnValue = self::$instance;
        
        // section 127-0-1-1-a831e14:134415460c1:-8000:000000000000188E end

        return $returnValue;
    }

} /* end of class core_kernel_versioning_subversionWindows_File */

?>