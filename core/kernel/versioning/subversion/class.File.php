<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API -
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 02.02.2012, 16:53:22 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package core
 * @subpackage kernel_versioning_subversion
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
// section 127-0-1-1--411682cd:13465a5ef15:-8000:00000000000018C0-includes begin
// section 127-0-1-1--411682cd:13465a5ef15:-8000:00000000000018C0-includes end

/* user defined constants */
// section 127-0-1-1--411682cd:13465a5ef15:-8000:00000000000018C0-constants begin
// section 127-0-1-1--411682cd:13465a5ef15:-8000:00000000000018C0-constants end

/**
 * Short description of class core_kernel_versioning_subversion_File
 *
 * @access public
 * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package core
 * @subpackage kernel_versioning_subversion
 */
class core_kernel_versioning_subversion_File
        implements core_kernel_versioning_FileInterface
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute instance
     *
     * @access private
     * @var File
     */
    private static $instance = null;

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
        
        $startTime = helpers_Time::getMicroTime();
        if($resource->getRepository()->authenticate()){
            $paths = is_array($path) ? $path : array($path);
        	$returnValue = svn_commit($message, $paths/*, !$recursive*/);
            $returnValue = $returnValue===false ? false : true;
        }
        $endTime = helpers_Time::getMicroTime();
        common_Logger::i("svn_commit (".$path.') recursive='.($recursive==true?'true':'false').'-> '.($endTime-$startTime).'s');
        
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
        
        common_Logger::i('svn_update '.$path. ' revision='.$revision);
        if($resource->getRepository()->authenticate()){
            $returnValue = svn_update($path, $revision)===false ? false : true;
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
        
        if($resource->getRepository()->authenticate()){
			
            //no revision, revert local change
            if (is_null($revision)){
                $returnValue = svn_revert($resource->getAbsolutePath());
            }
            else{
                $path = realpath($resource->getAbsolutePath());
                common_Logger::i('svn_revert '.$path);

                //get the svn revision number
                $log = svn_log($path);
				$oldRevision = count($log) - $revision;
				
				if(isset($log[$oldRevision])){
					
					$svnRevision = $log[$oldRevision];
					$svnRevisionNumber = $svnRevision['rev'];

					//destroy the existing version
					unlink($path);
					//replace with the target revision
					if ($resource->update($svnRevisionNumber)) {
						//get old content
						$content = $resource->getFileContent();
						//update to the current version
						$resource->update();
						//set the new content
						$resource->setContent($content);
						//commit the change
						if ($resource->commit($msg)) {
							$returnValue = true;
						}
						//restablish the head version
						else {
							@unlink($path);
							$resource->update();
						}
					}
					//restablish the head version
					else {
						@unlink($path);
						$resource->update();
					}
				}
                
            }
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
        
        $startTime = helpers_Time::getMicroTime();
        if($resource->getRepository()->authenticate()){
            $returnValue = svn_delete($path, true); //force the delete
        }
        $endTime =  helpers_Time::getMicroTime();
        common_Logger::i("svn_delete (".$path.') ->'.($endTime - $startTime).'s');
        
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
     * @param  boolean force
     * @return boolean
     * @see core_kernel_versioning_File::add()
     */
    public function add( core_kernel_classes_File $resource, $path, $recursive = false, $force = false)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-13a27439:132dd89c261:-8000:00000000000016F1 begin
        
        $startTime = helpers_Time::getMicroTime();
	    if($resource->getRepository()->authenticate()){
        	$returnValue = svn_add($path, $recursive, $force);
	    }else{
            //throw an Exception
        }
        $endTime = helpers_Time::getMicroTime();
        common_Logger::i("svn_add (".$path.') recursive='.($recursive?'true':'false').' -> '.($endTime-$startTime).'s');
        
        // section 127-0-1-1-13a27439:132dd89c261:-8000:00000000000016F1 end

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
        
        $startTime = helpers_Time::getMicroTime();
        if($resource->getRepository()->authenticate()){
            $returnValue = svn_log($path);
        }
        $endTime = helpers_Time::getMicroTime();
        common_Logger::i('svn_getHistory ('.$path.') -> '.($endTime-$startTime).'s');
        
        // section 127-0-1-1--57fd8084:132ecf4b934:-8000:00000000000016FB end

        return (array) $returnValue;
    }

    /**
     * Short description of method getStatus
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  File resource
     * @param  string path
     * @param  array options
     * @return int
     */
    public function getStatus( core_kernel_classes_File $resource, $path, $options = array())
    {
        $returnValue = (int) 0;

        // section 127-0-1-1-7a3aeccb:1351527b8af:-8000:0000000000001902 begin
        
        $startTime = helpers_Time::getMicroTime();
        
        if($resource->getRepository()->authenticate()){
            
            //Status of the target
            $status = null;
            //Get a list of statuses
            $svnStatusOptions = SVN_NON_RECURSIVE;
            if($options['SHOW_UPDATES']){
                $svnStatusOptions = $svnStatusOptions|SVN_SHOW_UPDATES;
            }
            
            $statuses = @svn_status($path, $svnStatusOptions);
            
            // * An explanation could be that the file is in a non working copy directory, it occured when we create a folders structure
            if($statuses !== false){
                //Extract required status
                foreach($statuses as $s){
                    if($s['path'] == $path){
                        $status = $s;
                    }
                }
                
                // If the file has a status, check the status is not unversioned or added
                if(!is_null($status)){
                    if($status['locked']){
                        $returnValue = VERSIONING_FILE_STATUS_LOCKED;
                    }
                    /**
                     * @todo implement this in the shell implementation
                     */
                    else if($status['repos_text_status'] == VERSIONING_FILE_STATUS_DELETED){
                        $returnValue = VERSIONING_FILE_STATUS_REMOTELY_DELETED;
                    }
                    /**
                     * @todo implement this in the shell implementation
                     */
                    else if($status['repos_text_status'] == VERSIONING_FILE_STATUS_MODIFIED){
                        $returnValue = VERSIONING_FILE_STATUS_REMOTELY_MODIFIED;
                    }
                    else{
                        $returnValue = $status['text_status'];
                    }
                }
                //No status can provide the following information, the file has been versioned & no changes have been made
                else {
                    if(!file_exists($path)){
                        $returnValue = VERSIONING_FILE_STATUS_UNVERSIONED;
                    }
                    else {
                        $returnValue = VERSIONING_FILE_STATUS_NORMAL;
                    }
                }
            }
            //the return of the request is false
            else{
                $returnValue = VERSIONING_FILE_STATUS_UNVERSIONED;
            }
        }
        
        $endTime =  helpers_Time::getMicroTime();
        common_Logger::i("svn_getStatus ('.$path.') '.$returnValue.' -> ".($endTime - $startTime).'s');
        // section 127-0-1-1-7a3aeccb:1351527b8af:-8000:0000000000001902 end

        return (int) $returnValue;
    }

    /**
     * Short description of method resolve
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  File resource
     * @param  string path
     * @param  string version
     * @return boolean
     */
    public function resolve( core_kernel_classes_File $resource, $path, $version)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-7a3aeccb:1351527b8af:-8000:0000000000001921 begin
        
        $startTime = helpers_Time::getMicroTime();
        $listParentFolder = tao_helpers_File::scandir(dirname($path));
		return core_kernel_versioning_subversionWindows_File::singleton()->resolve($resource, $path, $version);
		
        var_dump('resolving');
        switch($version){
            case VERSIONING_FILE_VERSION_MINE:
                //use our version of the file before the update we made the conflict
                $resource->setContent(file_get_contents($path.'.mine'));
        
                //delete the noisy files (mine, r***)
				var_dump($listParentFolder,$path,  preg_quote($path), '@^' . preg_quote($path) . '\.@');
                foreach($listParentFolder as $file) {
                    if(preg_match('@^' . preg_quote($path) . '\.@', $file)) {
						var_dump('deleted noisy file '.$path);
                        unlink($file);
                    }
                }
                
                $returnValue = true;
                break;
                
            case VERSIONING_FILE_VERSION_THEIRS:
                //use the incoming version of the file
                if($resource->revert()
                   && $resource->update()){
                    $returnValue = true;
                }
                break;
                
            case VERSIONING_FILE_VERSION_WORKING:
                //nothing to do, we keep the current version of the file
                $returnValue = true;
                break;
            
            default:
                //@todo change with invalid argument exception
                throw new common_Exception('invalid argument version');
        }
        
        //$returnValue = core_kernel_versioning_subversionWindows_File::singleton()->resolve($resource, $path, $version);
        $endTime =  helpers_Time::getMicroTime();
        common_Logger::i("svn_resolve ('.$path.' : '.$version.') -> ".($endTime - $startTime).'s');
        
        // section 127-0-1-1-7a3aeccb:1351527b8af:-8000:0000000000001921 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method singleton
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return core_kernel_versioning_File
     */
    public static function singleton()
    {
        $returnValue = null;

        // section 127-0-1-1--411682cd:13465a5ef15:-8000:00000000000018C4 begin
        if(is_null(self::$instance)){
			self::$instance = new self();
		}
		$returnValue = self::$instance;
        // section 127-0-1-1--411682cd:13465a5ef15:-8000:00000000000018C4 end

        return $returnValue;
    }

} /* end of class core_kernel_versioning_subversion_File */

?>