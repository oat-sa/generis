<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - core\kernel\events\class.InternalEvent.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 23.03.2010, 15:58:22 with ArgoUML PHP module 
 * (last revised $Date: 2008-04-19 08:22:08 +0200 (Sat, 19 Apr 2008) $)
 *
 * @author firstname and lastname of author, <author@example.org>
 * @package core
 * @subpackage kernel_events
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include core_kernel_rules_Expression
 *
 * @author firstname and lastname of author, <author@example.org>
 */
require_once('core/kernel/events/class.Event.php');

/* user defined includes */
// section 10-13-1-85-5a611d25:1213534f5a0:-8000:000000000000163A-includes begin
// section 10-13-1-85-5a611d25:1213534f5a0:-8000:000000000000163A-includes end

/* user defined constants */
// section 10-13-1-85-5a611d25:1213534f5a0:-8000:000000000000163A-constants begin
// section 10-13-1-85-5a611d25:1213534f5a0:-8000:000000000000163A-constants end

/**
 * Short description of class core_kernel_events_InternalEvent
 *
 * @access public
 * @author firstname and lastname of author, <author@example.org>
 * @package core
 * @subpackage kernel_events
 */
class core_kernel_events_InternalEvent
    extends core_kernel_rules_Expression
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute funct
     *
     * @access public
     * @var string
     */
    public $funct = '';

    /**
     * Short description of attribute line
     *
     * @access private
     * @var int
     */
    private $line = 0;

    /**
     * Short description of attribute fileName
     *
     * @access public
     * @var string
     */
    public $fileName = '';

    /**
     * Short description of attribute className
     *
     * @access public
     * @var string
     */
    public $className = '';

    /**
     * Short description of attribute object
     *
     * @access public
     */
    public $object;

    /**
     * Short description of attribute type
     *
     * @access public
     * @var string
     */
    public $type = '';

    /**
     * Short description of attribute args
     *
     * @access public
     * @var array
     */
    public $args = array();

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  string sender
     * @param  string comment
     * @param  boolean inPlace
     * @return void
     */
    public function __construct($sender, $comment, $inPlace)
    {
        // section 10-13-1-85-5a611d25:1213534f5a0:-8000:0000000000001650 begin
        parent::__construct($sender, $comment);
        
    $debugBackTrace = debug_backtrace($provide_object=true);
        
        // $inPlace tells if the Event is created explicitly in the caller environment
        // or if the Event was created by the EventLogger because none were provided.
        // In one case, the EventLogger call is in the stack trace, in the other, no.
        $index = $inPlace?1:2;
        $lastIndex = count($debugBackTrace)-1;
        if ($index>$lastIndex) $index = $lastIndex;
        // If called at the top level of a php file, no method is available and the
        // index has to be lowered.

        $epoch = time();
        
        $debugBackTrace = $debugBackTrace[$index];
        $this->epoch = $epoch;
        $this->sender = $sender;
        $this->comment = $comment;
        $this->funct = $debugBackTrace["function"];
        $this->line = $debugBackTrace["line"];
        $this->fileName = $debugBackTrace["file"];
        $this->className = @$debugBackTrace["class"];
        $this->object = core_kernel_rules_ExpressionParamConverter::convert(
          @$debugBackTrace["object"]
        );
        $this->type = @$debugBackTrace["type"];
        $this->args = array();
        foreach ($debugBackTrace["args"] as $arg) {
          if (!empty($arg))
            $this->args[] = core_kernel_rules_ExpressionParamConverter::convert($arg);
        }
        // section 10-13-1-85-5a611d25:1213534f5a0:-8000:0000000000001650 end
    }

} /* end of class core_kernel_events_InternalEvent */

?>