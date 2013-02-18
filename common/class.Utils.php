<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - common/class.Utils.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 18.02.2013, 12:06:03 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author lionel.lecaque@tudor.lu
 * @package common
 * @see @license  GNU General Public (GPL) Version 2 http://www.opensource.org/licenses/gpl-2.0.php
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 10-13-1--31--3b304a1e:11b08118c60:-8000:0000000000000D0E-includes begin
// section 10-13-1--31--3b304a1e:11b08118c60:-8000:0000000000000D0E-includes end

/* user defined constants */
// section 10-13-1--31--3b304a1e:11b08118c60:-8000:0000000000000D0E-constants begin
// section 10-13-1--31--3b304a1e:11b08118c60:-8000:0000000000000D0E-constants end

/**
 * Short description of class common_Utils
 *
 * @access public
 * @author lionel.lecaque@tudor.lu
 * @package common
 * @see @license  GNU General Public (GPL) Version 2 http://www.opensource.org/licenses/gpl-2.0.php
 */
class common_Utils
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method isUri
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  string strarg
     * @return boolean
     */
    public static function isUri($strarg)
    {
        $returnValue = (bool) false;

        // section 10-13-1--31--3b304a1e:11b08118c60:-8000:0000000000000D10 begin
        $uri = trim($strarg);
        if(!empty($uri)){
        	if( (preg_match("/^(http|https|file|ftp):\/\/[\/:.A-Za-z0-9_-]+#[A-Za-z0-9_-]+$/", $uri) && strpos($uri,'#')>0) || strpos($uri,"#")===0){
        		$returnValue = true;
        	}
        }
        // section 10-13-1--31--3b304a1e:11b08118c60:-8000:0000000000000D10 end

        return (bool) $returnValue;
    }

    /**
     * Removes starting/ending spaces, strip html tags out, remove any \r and \n
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  string strarg
     * @return string
     */
    public static function fullTrim($strarg)
    {
        $returnValue = (string) '';

        // section 10-13-1--31--3b304a1e:11b08118c60:-8000:0000000000000D1A begin
        $returnValue = strip_tags(trim($strarg));
        // section 10-13-1--31--3b304a1e:11b08118c60:-8000:0000000000000D1A end

        return (string) $returnValue;
    }

    /**
     * Short description of method hyperMask
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  int withVariables
     * @return void
     */
    public function hyperMask($withVariables)
    {
        // section 127-0-0-1-28acd538:11d1f33fe1e:-8000:0000000000000EF6 begin
        $subject = $withVariables;
        $HYPER_MASK = $GLOBALS["HYPER_MASK"][1]; // CURRENT MASK SET

        foreach ($HYPER_MASK as $mask) {
            $pattern = $mask[0];
            $replacement = $mask[1];
            $subject = ereg_replace($pattern, $replacement, $subject);
            //echo "<p>'$pattern', '$replacement', '$subject', '$subject'</p>";
        }
        return $subject;
        // section 127-0-0-1-28acd538:11d1f33fe1e:-8000:0000000000000EF6 end
    }

    /**
     * Short description of method getNewUri
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @return string
     */
    public static function getNewUri()
    {
        $returnValue = (string) '';

        // section 10-13-1--99-5d680c37:11e406b020f:-8000:0000000000000F21 begin
		$uriProviderClassName = 'common_uri_' . GENERIS_URI_PROVIDER;
		$uriProvider = new $uriProviderClassName(SGBD_DRIVER);
		$returnValue = $uriProvider->provide();
        // section 10-13-1--99-5d680c37:11e406b020f:-8000:0000000000000F21 end

        return (string) $returnValue;
    }

    /**
     * Short description of method loadSqlFile
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  string file
     * @return mixed
     */
    public static function loadSqlFile($file)
    {
        // section -87--2--3--76--570dd3e1:12507aae5fa:-8000:0000000000001824 begin
        if ($fileStream = @fopen($file, "r")){
            $ch = "";

            while (!feof ($fileStream)){
                $line = utf8_decode(fgets($fileStream));

                if (isset($line[0]) && ($line[0] != '#') && ($line[0] != '-')){
                    $ch = $ch.$line;
                }
            }
            $db = core_kernel_classes_DbWrapper::singleton();
            $requests = explode(";", $ch);
            unset($requests[count($requests)-1]);
            foreach($requests as $request){
                $db->exec($request);
            }

            fclose($fileStream);
        }
        else{
            die("File not found ".$file);
        }
        // section -87--2--3--76--570dd3e1:12507aae5fa:-8000:0000000000001824 end
    }

    /**
     * Returns the php code, that if evaluated
     * would return the value provided
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  value
     * @return string
     */
    public static function toPHPVariableString($value)
    {
        $returnValue = (string) '';

        // section 10-30-1--78-48d19975:13bfc2c7bd4:-8000:0000000000001E6E begin
		switch (gettype($value)) {
        	case "string" :
        		// replace \ by \\ and then ' by \'
        		$returnValue =  '\''.str_replace('\'', '\\\'', str_replace('\\', '\\\\', $value)).'\'';
        		break;
        	case "boolean" :
        		$returnValue = $mixed ? 'true' : 'false';
        		break;
        	case "integer" :
        	case "double" :
        		$returnValue = $value;
        		break;
        	case "array" :
				$string = "";
				foreach ($value as $key => $val) {
					$string .= self::toPHPVariableString($key)." => ".self::toPHPVariableString($val).",";
				}
				$returnValue = "array(".substr($string, 0, -1).")";
				break;
        	case "NULL" :
        		$returnValue = 'null';
				break;
        	case "object" :
        		$returnValue = 'unserialize(\''.serialize($value).'\')';
        		break;
        	default:
    			// ressource and unexpected types
        		throw new common_exception_Error("Could not convert variable of type ".gettype($value)." to PHP variable string");
        }
        // section 10-30-1--78-48d19975:13bfc2c7bd4:-8000:0000000000001E6E end

        return (string) $returnValue;
    }

    /**
     * Creates a temporary file in the System Temp directory with a unique name.
     * the file cannot be created, a FileException will be thrown. This method
     * compliant with all operating systems.
     *
     * Please make sure you unlink your file after use.
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @return string
     */
    public static function createTempFile()
    {
        $returnValue = (string) '';

        // section 10-30-1--82-485ebc01:13cb90c6d20:-8000:0000000000001FA8 begin
        $returnValue = @tempnam('/tmp', 'tao');
        if ($returnValue === false){
        	$msg = "Unable to create a temporary file in '" . sys_get_temp_dir() . "'."; 
        	throw new common_exception_FileSystemError($msg);
        }
        // section 10-30-1--82-485ebc01:13cb90c6d20:-8000:0000000000001FA8 end

        return (string) $returnValue;
    }

} /* end of class common_Utils */

?>