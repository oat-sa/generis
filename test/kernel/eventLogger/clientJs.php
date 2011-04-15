<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <title>Test JS de EventLogger</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <script type="text/javascript" src="../../../core/view/JS/event_logger.js"></script>
    <script type="text/javascript">
      function trig() {
        sender  = document.getElementById("sender").value;
        comment = document.getElementById("comment").value;
        trigEvent(sender, comment)
      }
    </script>
</head>
<body>

<form submit="?">
  <input type="submit" value="test" onclick="trig()"/>
  <input type="text" id="sender" value="cindy"/>
  <input type="text" id="comment" value="aire"/>
</form>
<hr/>
<?php
  sleep(0.5); // to have the time to update the database
  require_once ("../../../common/common.php");
  $GLOBALS["EVENTLOG"]["DATABASE"] = "webservice";
  $logger = core_kernel_events_EventLogger::getInstance();
  $eventTranslator = new core_kernel_events_EventTranslator($logger);
  echo "<pre>";
  $eventTranslator->getRaw();
  echo"</pre>";
?>
</body>
</html>