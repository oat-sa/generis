<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - core\kernel\events\class.EventTranslator.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 24.03.2010, 13:43:34 with ArgoUML PHP module 
 * (last revised $Date: 2008-04-19 08:22:08 +0200 (Sat, 19 Apr 2008) $)
 *
 * @author firstname and lastname of author, <author@example.org>
 * @package core
 * @subpackage kernel_events
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 127-0-0-1-2d1c59d3:11c0478bd31:-8000:0000000000000DF2-includes begin
// section 127-0-0-1-2d1c59d3:11c0478bd31:-8000:0000000000000DF2-includes end

/* user defined constants */
// section 127-0-0-1-2d1c59d3:11c0478bd31:-8000:0000000000000DF2-constants begin
// section 127-0-0-1-2d1c59d3:11c0478bd31:-8000:0000000000000DF2-constants end

/**
 * Short description of class core_kernel_events_EventTranslator
 *
 * @access public
 * @author firstname and lastname of author, <author@example.org>
 * @package core
 * @subpackage kernel_events
 */
class core_kernel_events_EventTranslator
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute csvSeparator
     *
     * @access public
     * @var string
     */
    public static $csvSeparator = ';';

    /**
     * Short description of attribute escapeCsvChar
     *
     * @access public
     * @var string
     */
    public static $escapeCsvChar = '\\';

    // --- OPERATIONS ---

    /**
     * outputs a CSV file
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  EventLogger eventLogger
     * @param  string fileName
     * @param  EventFilter eventFilter Exports a CSV file
     * @param  boolean append
     * @return void
     */
    public static function getCSV( core_kernel_events_EventLogger $eventLogger, $fileName = '',  core_kernel_events_EventFilter $eventFilter = null, $append = false)
    {
        // section 127-0-0-1-2d1c59d3:11c0478bd31:-8000:0000000000000ED4 begin
        //TODO: Iterator to get Events from a file
        if (empty($GLOBALS["EVENTLOG"]["ENABLED"])) return;
        $file = fopen($fileName, $append?"a":"wb");
         //echo "<p>Wouhou (Transloator, $fileName)</p>";
        // we should use the foreach statement but ArgoUml prevents from using
        // the inheritance of the builtin php interface Iterator
        $events = $this->eventLogger->getIterator(
          $this->oldestThreshold,
          $this->youngestThreshold
        );
        $X = self::$csvSeparator; // less ugly for the print header of the CSV
        fwrite($file,
          "epoch{$X}sender{$X}comment{$X}fileName{$X}line{$X}"
         ."className{$X}object{$X}type{$X}funct{$X}{args}\n"
        );
        while ($events->valid()) {
          $current =  $events->current();
          $fields = array();
          $fields[] = $current->epoch;
          $fields[] = $current->sender;
          $fields[] = $current->comment;
          $fields[] = $current->fileName;
          $fields[] = $current->line;
          $fields[] = $current->className;
          $fields[] = $current->object;
          $fields[] = $current->type;
          $fields[] = $current->funct;
          if (!empty($current->args))        // Depending upon the Event manager,
            foreach ($current->args as $arg) // there can be no argument.
              if (!empty($arg))
                $fields[] = $arg;

          $line = "";
          foreach ($fields as $field) {
            $formattedField = common_Utils::csvFormat($field, self::$csvSeparator);
            $line .= "$formattedField".self::$csvSeparator;
          }
          $line = substr($line, 0, -1);
          fwrite($file, "$line\n");

          $events->next();

        }
        fclose($file);
        // section 127-0-0-1-2d1c59d3:11c0478bd31:-8000:0000000000000ED4 end
    }

    /**
     * outputs a XML file
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  EventLogger eventLogger
     * @param  EventFilter eventFilter outputs a XML file
     * @return void
     */
    public function getXML( core_kernel_events_EventLogger $eventLogger,  core_kernel_events_EventFilter $eventFilter = null)
    {
        // section 127-0-0-1-2d1c59d3:11c0478bd31:-8000:0000000000000ED9 begin
        //TODO: getXML
        if (empty($GLOBALS["EVENTLOG"]["ENABLED"])) return;
        // http://php.net/manual/en/ref.simplexml.php
        // section 127-0-0-1-2d1c59d3:11c0478bd31:-8000:0000000000000ED9 end
    }

    /**
     * Short description of method getRaw
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  EventLogger eventLogger
     * @param  EventFilter eventFilter
     * @return void
     */
    public function getRaw( core_kernel_events_EventLogger $eventLogger,  core_kernel_events_EventFilter $eventFilter = null)
    {
        // section 127-0-0-1-2b72ae59:11c559ca95a:-8000:0000000000000E8E begin
        $this->getCSV("php://output");
        // section 127-0-0-1-2b72ae59:11c559ca95a:-8000:0000000000000E8E end
    }

    /**
     * Short description of method getHTML
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  EventLogger eventLogger
     * @param  EventFilter eventFilter
     * @return void
     */
    public function getHTML( core_kernel_events_EventLogger $eventLogger,  core_kernel_events_EventFilter $eventFilter = null)
    {
        // section 127-0-0-1-2b72ae59:11c559ca95a:-8000:0000000000000E93 begin
      $nbColumns = 5; // to chunk the parameters
      $output = "
  <div class='dump_log'>
    <table>
      <thead>
        <tr class='line_1'>
          <td>epoch</td>
          <td>sender</td>
          <td>comment</td>
          <td>fileName</td>
          <td>line</td>
        </tr>
        <tr class='line_2'>
          <td>className</td>
          <td>object</td>
          <td>type</td>
          <td>funct</td>
          <td></td>
        </tr>
        <tr>
          <td colspan='$nbColumns' id='arguments_header'>{arguments}</td>
        </tr>
        <tr class='filler_line'>
          <td colspan='$nbColumns' >&nbsp;</td>
        </tr>
      </thead>
      <tbody>
";

      $events = $this->eventLogger->getIterator();
      while ($events->valid()) {
        $current =  $events->current();
        $epoch = htmlspecialchars($current->epoch);
        $sender = htmlspecialchars($current->sender);
        $comment = htmlspecialchars($current->comment);
        $fileName = htmlspecialchars($current->fileName);
        $line = htmlspecialchars($current->line);
        $className = htmlspecialchars($current->className);
        $object = htmlspecialchars($current->object);
        $type = htmlspecialchars($current->type);
        $funct = htmlspecialchars($current->funct);
        $args = $current->args;

        $parameters = "";
        if (sizeof($args)>0) {
          $parameters .= "<tr>";
          $cellIteration = 0;
          foreach ($args as $arg) {
            if (!empty($arg)) {
              $arg = htmlspecialchars($arg);
              if ($cellIteration % $nbColumns == 0) {
                $parameters .= "</tr></td>\n";
              }
              $cellIteration++;
              $parameters .= "<td>$arg</td>\n";
            }
          }
          $parameters .= "</tr>";
        }
        $output .= "<tr class='line_1'>
          <td>$epoch</td>
          <td>$sender</td>
          <td>$comment</td>
          <td>$fileName</td>
          <td>$line</td>
          </tr>
          <tr class='line_2'>
          <td>$className</td>
          <td>$object</td>
          <td>$type</td>
          <td>$funct</td>
          <td>&nbsp;</td>
          </tr>
          $parameters
        <tr class='filler_line'>
          <td colspan='$nbColumns'>&nbsp;</td>
        </tr>
";
        $events->next();
     }
     $output .= "
           </tbody>
    </table>
  </div>
";
     return $output;

        // section 127-0-0-1-2b72ae59:11c559ca95a:-8000:0000000000000E93 end
    }

    /**
     * Short description of method setSelection
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  int oldestThreshold
     * @param  int youngestThreshold
     * @return mixed
     */
    public function setSelection($oldestThreshold, $youngestThreshold)
    {
        // section -87--2--3--76-51a982f1:1278aabc987:-8000:0000000000007DE3 begin
		$this->oldestThreshold = $oldestThreshold;
        $this->youngestThreshold = $youngestThreshold;        
        // section -87--2--3--76-51a982f1:1278aabc987:-8000:0000000000007DE3 end
    }

    /**
     * Short description of method setFilter
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  EventFilter filter
     * @return mixed
     */
    public function setFilter( core_kernel_events_EventFilter $filter)
    {
        // section -87--2--3--76-51a982f1:1278aabc987:-8000:0000000000007DE7 begin
       	$this->filter = $filter;
        // section -87--2--3--76-51a982f1:1278aabc987:-8000:0000000000007DE7 end
    }

    /**
     * Short description of method __construct
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  EventLogger eventLogger
     * @return mixed
     */
    public function __construct( core_kernel_events_EventLogger $eventLogger)
    {
        // section -87--2--3--76-51a982f1:1278aabc987:-8000:0000000000007DEA begin
        $this->eventLogger = $eventLogger;
        // section -87--2--3--76-51a982f1:1278aabc987:-8000:0000000000007DEA end
    }

} /* end of class core_kernel_events_EventTranslator */

?> 