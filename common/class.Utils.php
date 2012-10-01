<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - common/class.Utils.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 01.10.2012, 10:05:57 with ArgoUML PHP module 
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
     * @author Joel Bout, <joel.bout@tudor.lu>
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
     * Short description of method getLongUri
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string strarg
     * @return string
     */
    public function getLongUri($strarg)
    {
        $returnValue = (string) '';

        // section 10-13-1--31--3b304a1e:11b08118c60:-8000:0000000000000D13 begin
        //if (strpos($ressource,"#")===0)
        //{return $this->modelURI.$ressource;} else {return $ressource;}
        // section 10-13-1--31--3b304a1e:11b08118c60:-8000:0000000000000D13 end

        return (string) $returnValue;
    }

    /**
     * Short description of method getShortUri
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string strarg
     * @return string
     */
    public function getShortUri($strarg)
    {
        $returnValue = (string) '';

        // section 10-13-1--31--3b304a1e:11b08118c60:-8000:0000000000000D15 begin
        
        $explode = explode('#', $strarg);
        $returnValue = $explode[1];
        
        // section 10-13-1--31--3b304a1e:11b08118c60:-8000:0000000000000D15 end

        return (string) $returnValue;
    }

    /**
     * Removes starting/ending spaces, strip html tags out, remove any \r and \n
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
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
     * Short description of method startTimer
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  int id
     * @return void
     */
    public function startTimer($id)
    {
        // section 10-13-1--31--3b304a1e:11b08118c60:-8000:0000000000000D24 begin
        // section 10-13-1--31--3b304a1e:11b08118c60:-8000:0000000000000D24 end
    }

    /**
     * Short description of method endTimer
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  int id
     * @return string
     */
    public function endTimer($id)
    {
        $returnValue = (string) '';

        // section 10-13-1--31--3b304a1e:11b08118c60:-8000:0000000000000D26 begin
        // section 10-13-1--31--3b304a1e:11b08118c60:-8000:0000000000000D26 end

        return (string) $returnValue;
    }

    /**
     * returns the string prepared for use in a CSV export : delimiter escaping,
     * triming, etc.
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string toFormat
     * @param  string separator
     * @return string
     */
    public function csvFormat($toFormat, $separator = ";")
    {
        $returnValue = (string) '';

        // section 127-0-0-1-309542f:11c27295718:-8000:0000000000000E4A begin
        $toFormat    = str_replace($separator, "\\".$separator, $toFormat); // separator
        $toFormat    = str_replace("\n", " ", $toFormat) ;                  // \n
        $returnValue = trim($toFormat);                                     // spaces
        // section 127-0-0-1-309542f:11c27295718:-8000:0000000000000E4A end

        return (string) $returnValue;
    }

    /**
     * Short description of method hyperMask
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
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
     * @author Joel Bout, <joel.bout@tudor.lu>
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
     * Short description of method get
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return core_kernel_classes_Container
     */
    public static function get()
    {
        $returnValue = null;

        // section 10-13-1--99--22ab85dd:11f17f6dbbf:-8000:0000000000000F3E begin
        // section 10-13-1--99--22ab85dd:11f17f6dbbf:-8000:0000000000000F3E end

        return $returnValue;
    }

    /**
     * Short description of method xmlEntityDecode
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string string
     * @return string
     */
    public function xmlEntityDecode($string)
    {
        $returnValue = (string) '';

        // section 10-13-1-85--52fc59aa:123be3a15d4:-8000:000000000000165B begin
        // section 10-13-1-85--52fc59aa:123be3a15d4:-8000:000000000000165B end

        return (string) $returnValue;
    }

    /**
     * Short description of method loadSqlFile
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
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
                $db->execSql($request);
            }

            fclose($fileStream);
        }
        else{
            die("File not found ".$file);
        }
        // section -87--2--3--76--570dd3e1:12507aae5fa:-8000:0000000000001824 end
    }

} /* end of class common_Utils */

?>