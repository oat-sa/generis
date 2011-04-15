<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - common\class.Utils.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 24.03.2010, 14:38:36 with ArgoUML PHP module
 * (last revised $Date: 2008-04-19 08:22:08 +0200 (Sat, 19 Apr 2008) $)
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
$GLOBALS["HYPER_MASK"] = array();
/*
 Request to get all theses words:
 mysql -hHOST -uLOGIN -pPASS -e "SELECT object FROM statements where object like '%#%#%'" interview|egrep --only-matching "#.+#" | sort|uniq
 mysql -hHOST -uLOGIN -pPASS -e "SELECT object FROM statements where object like '%{%}%'" interview|egrep --only-matching "{.+}" | sort|uniq
 mysql -hHOST -uLOGIN -pPASS -e "SELECT object FROM statements where object like '%[%]%'" interview|egrep --only-matching "\[.+\]" | sort|uniq
 */
$GLOBALS["HYPER_MASK"][0][] = array("#insert test language#", "german");
$GLOBALS["HYPER_MASK"][0][] = array("#insert country name#", "France");
$GLOBALS["HYPER_MASK"][0][] = array("{NAME}", "Bond");
$GLOBALS["HYPER_MASK"][0][] = array("{SURVEY INSTITUTE}", "CRP Henri Tudor");
$GLOBALS["HYPER_MASK"][0][] = array("{SPONSOR}", "STATICA");

$GLOBALS["HYPER_MASK"][1][] = array("#are/were#", "are");
$GLOBALS["HYPER_MASK"][1][] = array("#can/could#", "can");
$GLOBALS["HYPER_MASK"][1][] = array("#do/did#", "do");
$GLOBALS["HYPER_MASK"][1][] = array("#does/did#", "does");
$GLOBALS["HYPER_MASK"][1][] = array("#Does/did#", "Does");
$GLOBALS["HYPER_MASK"][1][] = array("#insert calculated hourly wage#", "50â‚¬");
$GLOBALS["HYPER_MASK"][1][] = array("#insert country#", "France");
$GLOBALS["HYPER_MASK"][1][] = array("#insert country name#", "France");
$GLOBALS["HYPER_MASK"][1][] = array("#insert highest program level completed according to (B5c-p)#", "High graduate");
$GLOBALS["HYPER_MASK"][1][] = array("# insert month and year #", "november 2008");
$GLOBALS["HYPER_MASK"][1][] = array("# insert month and year#", "november 2008");
$GLOBALS["HYPER_MASK"][1][] = array("#insert period in D22a#", "beginning");
$GLOBALS["HYPER_MASK"][1][] = array("#insert test language#", "french");
$GLOBALS["HYPER_MASK"][1][] = array("#is/was#", "is");
$GLOBALS["HYPER_MASK"][1][] = array("#Is/Was#", "Is");
$GLOBALS["HYPER_MASK"][1][] = array("#last 12 months/12 months before you left the job#", "last 12 months");
$GLOBALS["HYPER_MASK"][1][] = array("#month and year#", "november 2008");
$GLOBALS["HYPER_MASK"][1][] = array("#this change/these changes#", "this change");
$GLOBALS["HYPER_MASK"][1][] = array("#country#", "France");

$GLOBALS["HYPER_MASK"][1][] = array('{, and you will be paid \$xx for your participation}', ", and you will be paid 10\$ for your participation");
$GLOBALS["HYPER_MASK"][1][] = array('\[ALL RESPONDENTS:\]', "");
$GLOBALS["HYPER_MASK"][1][] = array("\[IF BQ RESPONDENT IS NOT THE SAME AS SCREENER RESPONDENT:\]", "");
$GLOBALS["HYPER_MASK"][1][] = array("{SURVEY INSTITUTE}", "CRP Henri Tudor");
$GLOBALS["HYPER_MASK"][1][] = array("{NAME}", "Jerome");
$GLOBALS["HYPER_MASK"][1][] = array("{SPONSOR}", "FNR");
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
     * @author firstname and lastname of author, <author@example.org>
     * @param  string strarg
     * @return boolean
     */
    public static function isUri($strarg)
    {
        $returnValue = (bool) false;

        // section 10-13-1--31--3b304a1e:11b08118c60:-8000:0000000000000D10 begin
        //problem with  preg_match( '/^(http|https):\/\/[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]*'.'((:[0-9]{1,5})?\/.*)?$/i' ,$strarg)
        error_reporting("^E_NOTICE");
        $infos = parse_url($strarg);
        if (($infos['scheme']=='http'||$infos['scheme']=='https' || $infos['scheme']=='file'||$infos['scheme']=='ftp')&& $infos['host']!="" )
        {
            if(strpos($strarg,'#')>0){        
                $returnValue = true;
            }
            else {
                  $logger = new common_Logger('isUri',Logger::debug_level);
                  $logger->info('# not found in isUri, remove $resource->uriResource, check your code ' . $strarg ,__FILE__,__LINE__);
            }    
        }
        else
        {
            //todo normally the first char after the # should not be numeric
            if (strpos($strarg,"#")===0){
                $returnValue = true;
            } 
            else {
                $returnValue = false;
            }
        }
        error_reporting("E_ALL");
        // section 10-13-1--31--3b304a1e:11b08118c60:-8000:0000000000000D10 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getLongUri
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
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
     * @author firstname and lastname of author, <author@example.org>
     * @param  string strarg
     * @return string
     */
    public function getShortUri($strarg)
    {
        $returnValue = (string) '';

        // section 10-13-1--31--3b304a1e:11b08118c60:-8000:0000000000000D15 begin
        // section 10-13-1--31--3b304a1e:11b08118c60:-8000:0000000000000D15 end

        return (string) $returnValue;
    }

    /**
     * Removes starting/ending spaces, strip html tags out, remove any \r and \n
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  string strarg
     * @return string
     */
    public function fullTrim($strarg)
    {
        $returnValue = (string) '';

        // section 10-13-1--31--3b304a1e:11b08118c60:-8000:0000000000000D1A begin
        $returnValue = trim($strarg);
        $returnValue = strip_tags($returnValue);
        // section 10-13-1--31--3b304a1e:11b08118c60:-8000:0000000000000D1A end

        return (string) $returnValue;
    }

    /**
     * Short description of method startTimer
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
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
     * @author firstname and lastname of author, <author@example.org>
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
     * @author firstname and lastname of author, <author@example.org>
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
     * @author firstname and lastname of author, <author@example.org>
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
     * @author firstname and lastname of author, <author@example.org>
     * @return string
     */
    public static function getNewUri()
    {
        $returnValue = (string) '';

        // section 10-13-1--99-5d680c37:11e406b020f:-8000:0000000000000F21 begin
        $modelUri = core_kernel_classes_Session::singleton()->getNameSpace();
		$dbWrapper = core_kernel_classes_DbWrapper::singleton();
		$uriExist = false;
		do{
			list($usec, $sec) = explode(" ", microtime());
        	$uri = $modelUri .'#i'. (str_replace(".","",$sec."".$usec));
			$sqlResult = $dbWrapper->execSql(
				"select count(subject) as num from statements where subject = '".$uri."'"
			);
			if (!$sqlResult-> EOF){
				$found = (int)$sqlResult->fields['num'];
				if($found > 0){
					$uriExist = true;
				}
			}
		}while($uriExist);
		
		$returnValue = $uri;
        
        // section 10-13-1--99-5d680c37:11e406b020f:-8000:0000000000000F21 end

        return (string) $returnValue;
    }

    /**
     * Short description of method get
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
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
     * @author firstname and lastname of author, <author@example.org>
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
     * @author firstname and lastname of author, <author@example.org>
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
            $db = core_kernel_classes_DbWrapper::singleton(DATABASE_NAME);
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

    /**
     * Short description of method registerAutoload
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  string pClassName
     * @return mixed
     */
    public static function registerAutoload($pClassName)
    {
        // section -87--2--3--76--570dd3e1:12507aae5fa:-8000:0000000000002379 begin
        global $__classLoader;


        $files = $__classLoader->getFiles();
        if(!empty($files) && is_array($files)){
            if(isset($files[$pClassName])){
                require_once ($files[$pClassName]);
                return;
            }
        }
        $packages = $__classLoader->getPackages();

        if(!empty($packages) && is_array($packages)){
            foreach($packages as $path) {

                if (file_exists($path. $pClassName . '.class.php')) {
                    require_once $path . $pClassName . '.class.php';
                    return;
                }
                if (file_exists($path. 'class.'.$pClassName . '.php')) {
                    require_once $path . 'class.'. $pClassName . '.php';
                    return;
                }
            }
        }
        $split = explode("_",$pClassName);
        $path = GENERIS_BASE_PATH.'/../';
        for ( $i = 0 ; $i<sizeof($split)-1 ; $i++){
            $path .= $split[$i].'/';
        }
        $filePath = $path . 'class.'.$split[sizeof($split)-1] . '.php';

        if (file_exists($filePath)){
            require_once $filePath;
            return;
        }
        // section -87--2--3--76--570dd3e1:12507aae5fa:-8000:0000000000002379 end
    }

} /* end of class common_Utils */

?>