<?php


require_once dirname(__FILE__).'/../../common/common.php';
require_once INCLUDES_PATH.'/simpletest/autorun.php';
require_once INCLUDES_PATH.'/simpletest/web_tester.php';
require_once(INCLUDES_PATH.'/adodb5/adodb.inc.php');
if (!empty($_SERVER["SERVER_ADMIN"]))
if ("che@tudor.lu" == $_SERVER["SERVER_ADMIN"]) {
	require_once(INCLUDES_PATH.'/adodb5/adodb-exceptions.inc.php');
	// It only works on the system of the so-called che... For testing purposes!
	//TODO: remove this when we took decision about Exceptions
}

class EventLoggerTestCase extends UnitTestCase {

	//h ttp://simpletest.sourceforge.net/en/first_test_tutorial.html

	function setUp() {

		// sets the temp directory, the log file and creates the logger
		$this->database = "testCaseDatabase";
		$GLOBALS["EVENTLOG"]["DATABASE"] = $this->database;
		$tmpDirUnix = "/tmp";
		$tempDirMsWin = "c:\\WINDOWS\\TEMP";

		if     (file_exists($tmpDirUnix))
		$this->tmpDir = $tmpDirUnix;
		elseif (file_exists($tmpDirMsWin))
		$this->tmpDir = $tmpDirMsWin;
		else
		throw new Exception("No temporary directory available");
		$this->fileForExport = $this->tmpDir."/log";

		$this->db = NewADOConnection(SGBD_DRIVER);
		$this->db->Connect(DATABASE_URL, DATABASE_LOGIN, DATABASE_PASS);

	}


	function testUseDatabase() {
		// - if a database is set in the config file, use it
		// - else if database is already used, keep it
		// - else take the default database name

		$this->useDataBase();
		$this->assertEqual(null, $this->getUsedDatabase());

		// if a database is set in the config file, use it
		$this->useDataBase();
		$GLOBALS["EVENTLOG"]["DATABASE"] = $this->database;
		$logger = core_kernel_events_EventLogger::newInstance();
		$this->assertEqual($logger->database, $this->database);

		// else if database is already used, keep it
		$customDatabase = "testCaseDb";
		$this->db->Execute("CREATE DATABASE `$customDatabase`");
		$this->db->Execute("USE `$customDatabase`"); // always exists
		unset($GLOBALS["EVENTLOG"]["DATABASE"]);
		$logger = core_kernel_events_EventLogger::newInstance();
		$this->assertEqual($logger->database, $this->getUsedDatabase());
		$this->db->Execute("DROP DATABASE `$customDatabase`");

		// else take the default database name
		unset($GLOBALS["EVENTLOG"]["DATABASE"]);
		$this->useDataBase();
		$logger = core_kernel_events_EventLogger::newInstance();
		$this->assertEqual($logger->database,"eventlogger"); // look in the class itself!



		$this->useDataBase(); // just in case...
		$GLOBALS["EVENTLOG"]["DATABASE"] = $this->database; // just in case...
	}


	function testTrigEvent() {
		// simple database access
		$this->logger = core_kernel_events_EventLogger::newInstance();
		$this->logger->dropLog();
		$this->logger->trigEvent();
		$this->logger->trigEvent(
		new core_kernel_events_Event("sender", "comment")
		);
		$this->assertNoErrors();
		// The expected error is an exception, catched by the test itself.
	}

	function testExportOfLog() {
		// existence of the file log
		// compares the written line of log
		$this->logger = core_kernel_events_EventLogger::newInstance();
		$this->logger->dropLog();
		$lineOfEvent = $this->forExportOfLog(3615, array("one\narray;| ", 1), "string", 3615);
		$lineOfEvent = __LINE__-1; // the line number may vary!
		$thisFile = __FILE__; // the path to this file may vary!
		$modele =
      "3615;one return\;;two  returns;$thisFile;$lineOfEvent;EventLoggerTestCase;@;->;forExportOfLog;3615;one array\;\||1;string;3615\n";

		$this->exportLog();
		$this->assertTrue(file_exists($this->fileForExport));
		$this->assertEqual(
		$this->getSecondLineOfLog(),
		$modele
		);
		@unlink($this->fileForExport);
	}

	function testDropOfLog() {
		// dropping of the logs based upon the log size
		// dropping of the logs based upon the dates
		$this->logger = core_kernel_events_EventLogger::newInstance();
		$this->logger->dropLog();
		//echo $this->logger->database;
		for($i=1;$i<=20;$i++) {
			$this->logger->debugSetTime($i);
			$this->logger->trigEvent(new core_kernel_events_Event("$i", "$i"));
		}
		$this->logger->debugSetTime(20);

		unset($GLOBALS["EVENTLOG"]["AUTO_SAVE_FILE_PREFIX"]); // no log export
		// Only 16,17,18,19,20 remains. It should let only 5 lines in the logs.
		$this->logger->dropOldLogs(5, 999999); // 5 lines at most
		$this->assertEqual(5, $this->logger->getSize());

		// Only 18,19 and 20 remains. It should let only 3 lines in the logs.
		$this->logger->dropOldLogs(99999, 3); // not older than 3 seconds
		$this->assertEqual(3, $this->logger->getSize());
	}


	// UTILITTY METHODS

	private function useDatabase($database=null) {
		if (empty($database)) {
			// You're warned: this is tricky! SELECT DATABASE() will return NULL
			$trickyDatabase = "trickyForTestCase";
			$this->db->Execute("CREATE DATABASE IF NOT EXISTS `$trickyDatabase`");
			$this->db->Execute("USE `$trickyDatabase`");
			$this->db->Execute("DROP DATABASE `$trickyDatabase`");
		} else {
			$this->db->Execute("USE `$database`");
		}
	}

	private function getUsedDatabase() {
		return $this->db->Execute("select database()")->fields[0];
	}

	private function exportLog() {
		$eventTranslator = new core_kernel_events_EventTranslator($this->logger);
		$eventTranslator->getCSV($this->fileForExport);
	}

	private function catLog() {
		// outputs the log
		$fileName = $this->fileForExport;
		$file = @fopen($fileName, "r");
		echo "<pre>";
		while (!feof($file)) {
			$line = fgets($file);
			echo "$line";
		}
		echo "</pre>";
		fclose($file);
	}

	private function getSecondLineOfLog() {
		// gets the second line of the log -that is, the first line of data
		// the very first line contains the headers
		$fileName = $this->fileForExport;
		$file = @fopen($fileName, "r");
		@fgets($file);
		$line = @fgets($file);
		fclose($file);
		return $line;
	}

	private function forExportOfLog($time, $a, $b, $c) {
		// this method will be logged
		$this->logger->debugSetTime($time);
		$event = new core_kernel_events_Event("one\nreturn;", "two\n\nreturns");
		$event->debugSetTime($time);
		$this->logger->trigEvent($event);
	}
}

// class TestOfWeb extends WebTestCase {
//
//   function setUp() {
//
//     // sets the temp directory, the log file and creates the logger
//     $this->database = "testCaseDatabase";
//     $GLOBALS["EVENTLOG"]["DATABASE"] = $this->database;
//     $tmpDirUnix = "/tmp";
//     $tempDirMsWin = "c:\\WINDOWS\\TEMP";
//
//     if     (file_exists($tmpDirUnix))
//       $this->tmpDir = $tmpDirUnix;
//     elseif (file_exists($tmpDirMsWin))
//       $this->tmpDir = $tmpDirMsWin;
//     else
//       throw new Exception("No temporary directory available");
//     $this->fileForExport = $this->tmpDir."/log";
//
//     $this->db = NewADOConnection(SGBD_DRIVER);
//     $this->db->Connect(DATABASE_URL, DATABASE_LOGIN, DATABASE_PASS);
//
//   }
//
//   function testJsTrigEvent() {
//     $this->logger = core_kernel_events_EventLogger::newInstance();
//     $this->logger->dropLog();
//     // trigger a JS event
//     // checks that it exists
//     $baseUrl = "http://localhost/generis/core/kernel/eventLoggerService.php?";
//     $sender = "thesender";
//     $comment = "aire";
//     $this->get("{$baseUrl}?sender=$sender&comment=$comment");
//     $this->assertResponse("200");
//     $this->checkLogContent($sender, $comment);
//   }
//
//   private function checkLogContent($sender, $comment) {
//     // existence of the file log
//     // compares the written line of log
//     $this->exportLog();
//     $this->assertTrue(file_exists($this->fileForExport));
//     $logLine = $this->getSecondLineOfLog();
//     echo "'$logLine'";
//     $this->assertTrue(
//       strstr($sender, $logLine) && strstr($comment, $logLine)
//     );
//     //unlink($this->fileForExport);
//   }
//
//   private function exportLog() {
//     $eventTranslator = new core_kernel_events_EventTranslator($this->logger);
//     $eventTranslator->getCSV($this->fileForExport);
//     //echo "<p>export de {$this->fileForExport}</p>";
//   }
//
//   private function getSecondLineOfLog() {
//     // gets the second line of the log -that is, the first line of data
//     // the very first line contains the headers
//     $fileName = $this->fileForExport;
//     $file = @fopen($fileName, "r");
//     @fgets($file);
//     echo $file;
//     $line = @fgets($file);
//     fclose($file);
//     return $line;
//   }
//
// }
//

