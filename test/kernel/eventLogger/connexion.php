<?php

require_once ("../../../common/common.php");

//error_reporting(E_ALL & ~E_WARNING);
//core_control_FrontController::connect("generis", md5("generis"), "generis");

$logger = core_kernel_events_EventLogger::getInstance();

class ParamClass {

  public $a = "lettre A";

  //public $debug = "le champ;|debug";

  function __toString() {
     return "la chaine; tostring ";
  }

}

$paramClass = new ParamClass();


class UneClasse extends common_Object {

  public $lol = 3615;

  public function test($x, $y=45, $z=36) {
    global $logger;
    //$logger->dropLog();
    $event = new core_kernel_events_Event("fonction test", "commentaires");
    $logger->trigEvent($event);
    $this->translator();
  }

  function translator() {
    global $logger;
    $translator = new core_kernel_events_EventTranslator($logger);
    $translator->getCSV("/tmp/log");
    $file = @fopen("/tmp/log", "r");
    if ($file) {
      while (!feof($file)) {
        $line = fgets($file);
        echo "<p>$line</p>";
      }
      fclose($file);
    }
  }

  function __construct() {
    $this->debug = "LOG";
  }

}

function testDropOldLogs() {
  global $logger;
  $logger->dropLog();
  for($i=1;$i<=20;$i++) {
    $logger->debugSetTime($i);
    $logger->trigEvent(new core_kernel_events_Event("$i", "$i"));
  }
  $logger->debugSetTime(20);
  //$logger->dropOldLogs(5, 3);
}

echo $logger->database;
//testDropOldLogs();


 $uneClasse = new UneClasse();
 $uneClasse->test($paramClass, array(1, 2, 3, ";|"), "1|2|3");
//
// for($i=0;$i<10;$i++) {
//   $logger->trigEvent();
//   sleep(1);
// }


?>