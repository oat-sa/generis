<?php
require_once ("../../../common/common.php");
$logger = core_kernel_events_EventLogger::getInstance();
$csvFile = "log.csv";

$eventTranslator = new core_kernel_events_EventTranslator($logger);

if (isset($_REQUEST["raw"])) {

   echo $eventTranslator->getRaw();
   exit;
}

$logState = "";
if (isset($_REQUEST["del"])) {
  switch ($_REQUEST["del"]) {
    case "want":
      $logState = "want";
      break;
    case "really":
      $logger->dropLog();
      $page = "http://{$_SERVER['SERVER_NAME']}{$_SERVER['PHP_SELF']}";
      header("Location: $page?del=done");
    case "done":
      $logState = "done";
      break;
  }
}

if (isset($_REQUEST["populate"])) {
  //$logger->dropLog();
  for ($i=0;$i<5;$i++)
    $logger->trigEvent(new core_kernel_events_Event("<h1>one</h1>", "two"));
  $page = "http://{$_SERVER['SERVER_NAME']}{$_SERVER['PHP_SELF']}";
  header("Location: $page");
}
//http://www.databasejournal.com/features/php/article.php/2234861
//TODO: use SelectLimit() of AdoDb, and even cache management
//$logger->dropOldLogs(10, 5);
$eventTranslator->getCsv($csvFile);
?>
<html>
  <head>
    <title>EventLogger</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <link type="text/css" rel="StyleSheet" href="dumpLog.css"/>
  </head>
  <body>
    <div id="introduction">
      <h1>Affichage du contenu des événements enregistrées</h1>
      <p>
        Chaque événements est présenté en trois parties&nbsp;: d'abord les informations de contexte d'un intérêt secondaire, ensuite les informations directement exploitables puis les arguments passés à la méthode.
      </p>
    </div>
    <div id="commands" style="text-align:right;padding-right:1mm;border-right:3px dotted gray;">
      <p>
        <a href="?raw" target="_blank">Show the raw CSV content</a>
      </p>
      <p>
        <a href="#bnf">BNF</a>
      </p>
      <p>
        <a href="?">Reload</a>
      </p>
      <p style="margin-top: 2cm">
        <a href="?populate">Populate the logs</a>
      </p>
      <p>
        <?php
          switch ($logState) {
            case "want":
              ?>
                <a href="?del=really" style="color:red;font-weight:bold">
                  Really Delete the logs?
                </a>
              <?php
              break;
            case "done":
              ?>
                <span style="color:red;font-weight:bold">
                  Logs deleted!
                </span>
              <?php
              break;
            default:
              ?>
                <a href="?del=want" style="color:red;font-weight:bold">
                  Delete the logs
                </a>
              <?php
          }
        ?>
      </p>
    </div>
    <div id="content">
      <p><a href="<?php echo $csvFile ?>">CSV file</a></p>
      <?php echo $eventTranslator->getHtml() ;?>
    </div>
    <div id="BNF">
      <a name="bnf">
      <h1>Forme BNF</h1>
      <pre style="width:50%">
newline ::= \n
ESC ::= \
X ::= ; // separator for CSV
Y ::= | // separator for inner arrays
number ::= // a strictly positive integer
string ::= // a general string, without X not precessed by ESC
identifier ::= // formatted like a Php identifier, without X but can contain ESC,X
array_of_string ::= string {Y string} // string plus same rule with Y

file ::= [ header ] { line }
header ::= { string X } string newline
line ::= epoch X sender X comment X fileName X line X className X object X type X funct [ { X arg } ]

epoch ::= number
sender ::= string
comment ::= string
fileName ::= string
line ::= number
className ::= identifier
object ::= string
type ::= "->" | "::"
funct ::= identifier
arg ::= array_of_string | string
</pre>
    </div>
  </body>
</html>
