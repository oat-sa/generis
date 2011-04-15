<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - core\kernel\events\class.Event.php
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

/* user defined includes */
// section 127-0-0-1--6c40f9ad:11c0323fc26:-8000:0000000000000DE8-includes begin
// section 127-0-0-1--6c40f9ad:11c0323fc26:-8000:0000000000000DE8-includes end

/* user defined constants */
// section 127-0-0-1--6c40f9ad:11c0323fc26:-8000:0000000000000DE8-constants begin
// section 127-0-0-1--6c40f9ad:11c0323fc26:-8000:0000000000000DE8-constants end

/**
 * Short description of class core_kernel_events_Event
 *
 * @access public
 * @author firstname and lastname of author, <author@example.org>
 * @package core
 * @subpackage kernel_events
 */
class core_kernel_events_Event
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute epoch
     *
     * @access public
     * @var int
     */
    public $epoch = 0;

    /**
     * Short description of attribute sender
     *
     * @access public
     * @var string
     */
    public $sender = '';

    /**
     * Short description of attribute comment
     *
     * @access public
     * @var string
     */
    public $comment = '';

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  string sender
     * @param  string comment
     * @param  boolean inPlace true means that the Event was created from the EventLogger and not by the called method itself
     * @return void
     */
    public function __construct($sender, $comment, $inPlace = true)
    {
        // section 127-0-0-1--6c40f9ad:11c0323fc26:-8000:0000000000000DFA begin
        if (empty($GLOBALS["EVENTLOG"]["ENABLED"])) return;
        
        $this->epoch = time();
        $this->sender = $sender;
        $this->comment = $comment;
        
        // section 127-0-0-1--6c40f9ad:11c0323fc26:-8000:0000000000000DFA end
    }

} /* end of class core_kernel_events_Event */

?>