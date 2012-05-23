<?php
# Logging
$GLOBALS["EVENTLOG"]["QUIET"] = false; // no exception raised
$GLOBALS["EVENTLOG"]["ENABLED"] = false; // no logging at all
$GLOBALS["EVENTLOG"]["DATABASE"] = ""; // forces the database to store logs
$GLOBALS["EVENTLOG"]["TABLE"] = "";    // changes the table to store logs
$GLOBALS["EVENTLOG"]["TIME_WINDOW"] = 86400; // one day
$GLOBALS["EVENTLOG"]["SIZE_WINDOW"] = 20000; // lines to keep
$GLOBALS["EVENTLOG"]["CHECK_OLD_LOGS_WINDOW"] = 1000;  // check log size periodicity
// to not save logs that rer deleted (see TIME and SIZE WINDOW) don't indicate a file
$GLOBALS["EVENTLOG"]["AUTO_SAVE_FILE_PREFIX"] = "";    // the file for auto-deleted logs
$GLOBALS["EVENTLOG"]["AUTO_SAVE_FILE_SUFFIX"] = ".log"; // prefix for the saved logs