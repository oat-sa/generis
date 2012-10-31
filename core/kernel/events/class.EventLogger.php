<?php

error_reporting(E_ALL);

/**
 * implements IteratorAggregate
 *
 * @author firstname and lastname of author, <author@example.org>
 * @package core
 * @subpackage kernel_events
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 127-0-0-1-48ef8247:11bf9e06099:-8000:0000000000000D81-includes begin
if (!empty($_SERVER["SERVER_ADMIN"]))
if ("che@tudor.lu" == $_SERVER["SERVER_ADMIN"]) {
	// It only works on the system of the so-called che... For testing purposes!
	//TODO: remove this when we took decision about Exceptions
}

/* HOWTO
 * set the configuration options in config.php
 * use the logger:
 require_once (dirname(__FILE__)."/../../../common/common.php");
 core_kernel_events_EventLogger::getInstance()->trigEvent();
 $logger = core_kernel_events_EventLogger::getInstance();
 $logger->trigEvent(new core_kernel_events_Event("fonction test", "commentaires"));
 $logger->trigEvent();

 * export data: core_kernel_events_EventTranslator::getCSV($logger, "/tmp/log");
 */

//TODO: put theses two functions into the Utility class and document them
function minWithNull($a, $b) {
	if     (is_null($a)) $result = $b; // if $b is null, will return null
	elseif (is_null($b)) $result = $a; // $a is not null
	else                 $result = $a<$b ? $a : $b;
	return $result;
}

function maxWithNull($a, $b) {
	if     (is_null($a)) $result = $b; // if $b is null, will return null
	elseif (is_null($b)) $result = $a; // $a is not null
	else                 $result = $a>$b ? $a : $b;
	return $result;
}


// section 127-0-0-1-48ef8247:11bf9e06099:-8000:0000000000000D81-includes end

/* user defined constants */
// section 127-0-0-1-48ef8247:11bf9e06099:-8000:0000000000000D81-constants begin
define("MYSQL_TABLE_DOESNT_EXIST", 1146);
define("MYSQL_NO_DATABASE_SELECTED", 1046);
define("MYSQL_TABLE_ALREADY_EXISTS", 1050);
define("EVENTLOGGER_DEFAULT_DATABASE", "eventlogger");
//TODO: make case to use DELAYED only with Mysql. Generaly independant upon mysql.
//TODO: handle a specific database access to avoid switch database management.
//TODO: hook for the encryption when databas-ing, or creating Event? Where's the key?

// section 127-0-0-1-48ef8247:11bf9e06099:-8000:0000000000000D81-constants end

/**
 * implements IteratorAggregate
 *
 * @access public
 * @author firstname and lastname of author, <author@example.org>
 * @package core
 * @subpackage kernel_events
 */
class core_kernel_events_EventLogger
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute db
     *
     * @access public
     * @var DbWrapper
     */
    public $db = null;

    /**
     * Short description of attribute table
     *
     * @access public
     * @var string
     */
    public $table = 'eventlogger';

    /**
     * Short description of attribute field
     *
     * @access public
     * @var string
     */
    public $field = 'event';

    /**
     * Short description of attribute database
     *
     * @access public
     * @var string
     */
    public $database = 'eventlogger';

    /**
     * conservation time (seconds) of the data. Default is one day.
     *
     * @access public
     * @var int
     */
    public $timeWindow = 86400;

    /**
     * Short description of attribute sizeWindow
     *
     * @access public
     * @var int
     */
    public $sizeWindow = 20000;

    /**
     * Short description of attribute checkOldLogsWindow
     *
     * @access public
     * @var int
     */
    public $checkOldLogsWindow = 100;

    /**
     * Short description of attribute debugTime
     *
     * @access public
     * @var int
     */
    public $debugTime = null;

    /**
     * Short description of attribute instanceOfSingleton
     *
     * @access public
     * @var int
     */
    public static $instanceOfSingleton = null;

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access protected
     * @author firstname and lastname of author, <author@example.org>
     * @return void
     */
    protected function __construct()
    {
        // section 127-0-0-1-48ef8247:11bf9e06099:-8000:00000000000013D9 begin
		if (empty($GLOBALS["EVENTLOG"]["ENABLED"])) return;

		/* about database selection. There are three cases :
		 1/ A database is set in the configuration file for EventLog. This database will
		 be kept for every database operation.
		 2/ The 1/ is not true AND a database is yet selected. The yet selected database
		 will be kept.
		 3/ Neither pre-selected database nor config file database. The default database
		 name will be used.
		 For every actions, the existing current database is saved and restore after the
		 operations. Thus, a forced database (config case) will not changed anything
		 outside the EventLogger.
		 */
		if (!empty($GLOBALS["EVENTLOG"]["DATABASE"])) {
			$this->database = $GLOBALS["EVENTLOG"]["DATABASE"];
		} else {
			// get the database currently used by the Generis module
			$generisConnector = core_kernel_classes_DbWrapper::singleton();
			$generisResult = $generisConnector->query("SELECT database()");
			if ($row = $generisResult->fetch()){
				$generisDatabase = $row[0];
				$generisResult->closeCursor();
			}
			else{
				$generisDatabase = EVENTLOGGER_DEFAULT_DATABASE;
			}
			
			$this->database = $generisDatabase;
		}

		if (!empty($GLOBALS["EVENTLOG"]["TABLE"]))
		$this->table = $GLOBALS["EVENTLOG"]["TABLE"];

		if (!empty($GLOBALS["EVENTLOG"]["TIME_WINDOW"]))
		$this->timeWindow = $GLOBALS["EVENTLOG"]["TIME_WINDOW"];
		if (!empty($GLOBALS["EVENTLOG"]["SIZE_WINDOW"]))
		$this->sizeWindow = $GLOBALS["EVENTLOG"]["SIZE_WINDOW"];
		if (!empty($GLOBALS["EVENTLOG"]["CHECK_OLD_LOGS_WINDOW"]))
		$this->checkOldLogsWindow = $GLOBALS["EVENTLOG"]["CHECK_OLD_LOGS_WINDOW"];

		//echo "<h1>okay, la bdd sera '{$this->database}'</h1>";
		$this->setupDb();
        // section 127-0-0-1-48ef8247:11bf9e06099:-8000:00000000000013D9 end
    }

    /**
     * Short description of method getInstance
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return core_kernel_events_EventLogger
     */
    public function getInstance()
    {
        $returnValue = null;

        // section 127-0-0-1--344ec2dd:11c22f0e360:-8000:0000000000000E4A begin
		if (!isset(self::$instanceOfSingleton)) {
			$c = __CLASS__;
			self::$instanceOfSingleton = new $c;
		}
		$returnValue = self::$instanceOfSingleton;
        // section 127-0-0-1--344ec2dd:11c22f0e360:-8000:0000000000000E4A end

        return $returnValue;
    }

    /**
     * Short description of method resetInstance
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return void
     */
    public function resetInstance()
    {
        // section 127-0-0-1-7d0a944d:11c9e98f1a1:-8000:0000000000000EDB begin
		$c = __CLASS__;
		return new $c;
        // section 127-0-0-1-7d0a944d:11c9e98f1a1:-8000:0000000000000EDB end
    }

    /**
     * Do not create Event outside a method or function, as no function/method
     * and arguments are available.
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  event
     * @return void
     */
    public function trigEvent($event = null)
    {
        // section 127-0-0-1-48ef8247:11bf9e06099:-8000:0000000000000D89 begin
		if (empty($GLOBALS["EVENTLOG"]["ENABLED"])) return;
		if (null == $event) {
			$event = new core_kernel_events_Event("", "", $inplace=false);
		}

		// Good for logging, http://dev.mysql.com/doc/refman/5.0/en/insert-delayed.html
		// INSERT DELAYED works only with MyISAM, MEMORY, and ARCHIVE
		$serialized = @mysql_real_escape_string(serialize($event));

		$time = is_null($this->debugTime) ? time() : $this->debugTime;
		try {
			$query = "
            INSERT INTO `{$this->table}`
            VALUES ($time, '$serialized')
          ";
			$this->db->Execute($query);
		} catch (ADODB_Exception $e) {
			if (empty($GLOBALS["EVENTLOG"]["QUIET"])) throw $e;
		}

		// From time to time, deletes old logs
		// Development in progress... not usable yet!
		//TODO: re-enable de dropOldLogs()
		//         if ($time % $this->checkOldLogsWindow == 0) {
		//           $this->dropOldLogs();
		//         }
		//$this->debugTime = null;
        // section 127-0-0-1-48ef8247:11bf9e06099:-8000:0000000000000D89 end
    }

    /**
     * Short description of method getIterator
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  int oldestThreshold
     * @param  int youngestThreshold
     * @return core_kernel_events_EventIterator
     */
    public function getIterator($oldestThreshold = null, $youngestThreshold = null)
    {
        $returnValue = null;

        // section 127-0-0-1-48ef8247:11bf9e06099:-8000:00000000000013D7 begin
		return new core_kernel_events_EventIterator(
		$this,
		$oldestThreshold,
		$youngestThreshold
		);
        // section 127-0-0-1-48ef8247:11bf9e06099:-8000:00000000000013D7 end

        return $returnValue;
    }

    /**
     * Short description of method setupDb
     *
     * @access protected
     * @author firstname and lastname of author, <author@example.org>
     * @return void
     */
    protected function setupDb()
    {
        // section 127-0-0-1-48ef8247:11bf9e06099:-8000:000000000000140D begin
		//TODO:Store in the php session the fact that the table was created? It'd save mysql time.
		try {
			$this->db = NewADOConnection(SGBD_DRIVER);
			$this->db->Connect(DATABASE_URL, DATABASE_LOGIN, DATABASE_PASS);
			//$this->db->debug = true;

			$this->db->Execute("CREATE DATABASE IF NOT EXISTS `{$this->database}`");
			$this->db->Execute("USE `{$this->database}`");
			$this->db->Execute("
            CREATE TABLE IF NOT EXISTS `{$this->table}` (
              timestamp INT NOT NULL,
              event TEXT,
              KEY `timestamp` (`timestamp`)
            ) DEFAULT CHARSET=utf8
          ");
		} catch (ADODB_Exception $e) {
			if (empty($GLOBALS["EVENTLOG"]["QUIET"])) throw $e;
		}
        // section 127-0-0-1-48ef8247:11bf9e06099:-8000:000000000000140D end
    }

    /**
     * Short description of method dropLog
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return void
     */
    public function dropLog()
    {
        // section 127-0-0-1-293e509c:11c0d865d9e:-8000:0000000000000E7F begin
		if (empty($GLOBALS["EVENTLOG"]["ENABLED"])) return;
		//echo "dropLog()";
		try {
			$query = "DROP TABLE IF EXISTS `{$this->table}`";
			$this->db->Execute($query);
		} catch (ADODB_Exception $e) {
			if (empty($GLOBALS["EVENTLOG"]["QUIET"])) throw $e;
		}
		$this->setupDb(); // to recrate the table
        // section 127-0-0-1-293e509c:11c0d865d9e:-8000:0000000000000E7F end
    }

    /**
     * Short description of method dropOldLogs
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  int sizeWindow
     * @param  int timeWindow
     * @return void
     */
    public function dropOldLogs($sizeWindow = null, $timeWindow = null)
    {
        // section 127-0-0-1--f72dabc:11c4151be08:-8000:0000000000000E87 begin
		if (empty($timeWindow)) $timeWindow = $this->timeWindow;
		if (empty($sizeWindow)) $sizeWindow = $this->sizeWindow;

		/* principle:
		 * get the older timestamp to keep (the "threshold")
		 * according to the size of the log to keep
		 * according to the olded log to keep
		 * if nothing to delete, do nothing else
		 * export records if asked
		 * delete records but not if a previous asked record failed
		 */
		try {
			// a time was set for debugging purposes
			$time = is_null($this->debugTime) ? $time = time():$this->debugTime;

			// gets oldest and yougest time threshold
			$maxTimestamp = $time-$timeWindow; // maxWithNull age of logs
			$query = "
            SELECT MIN(timestamp), MAX(timestamp)
            FROM `{$this->table}`
            WHERE timestamp <= $maxTimestamp
          ";
			$result = $this->db->Execute($query);
			$oldestTimeThreshold = $result->fields[0];
			$youngestTimeThreshold = $result->fields[1];

			// gets oldest and yougest size threshold
			$query = "SELECT COUNT(*) FROM `{$this->table}`";
			$nbLines = $this->db->Execute($query)->fields[0];
			$linesToDelete = $nbLines - $sizeWindow;
			if ($linesToDelete>0) {
				$query = "
              SELECT MIN(timestamp), MAX(timestamp)
              FROM (
                SELECT timestamp
                FROM `{$this->table}`
                ORDER BY timestamp ASC LIMIT $linesToDelete
              ) AS e
            ";
				$result = $this->db->Execute($query);
				$oldestSizeThreshold = $result->fields[0];
				$youngestSizeThreshold = $result->fields[1];
			} else {
				$oldestSizeThreshold = null;
				$youngestSizeThreshold = null;
			}

			// gets the oldest interval
			// the key point is that exporting and deleting concerns exactly
			// the same thresholds! That's why their are first discoved, then used.
			$oldestThreshold   = minWithNull($oldestSizeThreshold,   $oldestTimeThreshold  );
			$youngestThreshold = maxWithNull($youngestSizeThreshold, $youngestTimeThreshold);

			//           echo "<pre>";
			//           echo "time: $oldestTimeThreshold - $youngestTimeThreshold\n";
			//           echo "size: $oldestSizeThreshold - $youngestSizeThreshold\n";
			//           echo "both: $oldestThreshold - $youngestThreshold\n";
			//           echo "</pre>";

			// Something to delete/export?
			if (!is_null($oldestThreshold) && !is_null($youngestThreshold)) {
				if (!empty($GLOBALS["EVENTLOG"]["AUTO_SAVE_FILE_PREFIX"])) {
					$this->exportOldLogs($oldestThreshold, $youngestThreshold);
					// should Except if any problem, to not delete data of the database.
				}
				$query = "
              DELETE FROM `{$this->table}`
              WHERE timestamp>=$oldestThreshold AND timestamp<=$youngestThreshold
            ";
				$this->db->Execute($query); // drops old logs in one pass
			}
		} catch (ADODB_Exception $e) {
			if (empty($GLOBALS["EVENTLOG"]["QUIET"])) throw $e;
		}
        // section 127-0-0-1--f72dabc:11c4151be08:-8000:0000000000000E87 end
    }

    /**
     * Short description of method debugSetTime
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  int time
     * @return void
     */
    public function debugSetTime($time)
    {
        // section 127-0-0-1-7d0a944d:11c9e98f1a1:-8000:0000000000000EE1 begin
		$this->debugTime = $time;
        // section 127-0-0-1-7d0a944d:11c9e98f1a1:-8000:0000000000000EE1 end
    }

    /**
     * Short description of method exportOldLogs
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  int oldestThreshold
     * @param  int youngestThreshold
     * @return void
     */
    public function exportOldLogs($oldestThreshold, $youngestThreshold)
    {
        // section 127-0-0-1-7d0a944d:11c9e98f1a1:-8000:0000000000000F66 begin
		$prefix = $GLOBALS["EVENTLOG"]["AUTO_SAVE_FILE_PREFIX"]; // is not null
		$suffix = $GLOBALS["EVENTLOG"]["AUTO_SAVE_FILE_SUFFIX"]; // can be null
		$fileName = "${prefix}${oldestThreshold}-${youngestThreshold}${suffix}";
		$translator = new core_kernel_events_EventTranslator($this);
		$translator->setSelection($oldestThreshold, $youngestThreshold);
		$translator->getCsv($fileName);
        // section 127-0-0-1-7d0a944d:11c9e98f1a1:-8000:0000000000000F66 end
    }

    /**
     * Short description of method getSize
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return void
     */
    public function getSize()
    {
        // section 127-0-0-1-7d0a944d:11c9e98f1a1:-8000:0000000000000F6A begin
    	return $this->db->Execute("SELECT COUNT(*) from `{$this->table}`")->fields[0];
        // section 127-0-0-1-7d0a944d:11c9e98f1a1:-8000:0000000000000F6A end
    }

} /* end of class core_kernel_events_EventLogger */

?>