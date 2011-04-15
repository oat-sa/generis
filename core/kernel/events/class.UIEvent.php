<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - core\kernel\events\class.UIEvent.php
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
 * include core_kernel_events_Event
 *
 * @author firstname and lastname of author, <author@example.org>
 */
require_once('core/kernel/events/class.Event.php');

/* user defined includes */
// section 10-13-1-85-5a611d25:1213534f5a0:-8000:0000000000001655-includes begin
// section 10-13-1-85-5a611d25:1213534f5a0:-8000:0000000000001655-includes end

/* user defined constants */
// section 10-13-1-85-5a611d25:1213534f5a0:-8000:0000000000001655-constants begin
// section 10-13-1-85-5a611d25:1213534f5a0:-8000:0000000000001655-constants end

/**
 * Short description of class core_kernel_events_UIEvent
 *
 * @access public
 * @author firstname and lastname of author, <author@example.org>
 * @package core
 * @subpackage kernel_events
 */
class core_kernel_events_UIEvent
    extends core_kernel_events_Event
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute timestamp
     *
     * @access public
     * @var int
     */
    public $timestamp = 0;

    /**
     * Short description of attribute type
     *
     * @access public
     * @var string
     */
    public $type = '';

    /**
     * Short description of attribute target
     *
     * @access public
     * @var string
     */
    public $target = '';

    /**
     * Short description of attribute screenX
     *
     * @access public
     * @var int
     */
    public $screenX = 0;

    /**
     * Short description of attribute screenY
     *
     * @access public
     * @var int
     */
    public $screenY = 0;

    /**
     * Short description of attribute altKey
     *
     * @access public
     * @var boolean
     */
    public $altKey = false;

    /**
     * Short description of attribute ctrlKey
     *
     * @access public
     * @var boolean
     */
    public $ctrlKey = false;

    /**
     * Short description of attribute shiftKey
     *
     * @access public
     * @var boolean
     */
    public $shiftKey = false;

    /**
     * Short description of attribute keyCode
     *
     * @access public
     * @var string
     */
    public $keyCode = '';

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  string sender
     * @param  string comment
     * @param  int timestamp
     * @param  string type
     * @param  string target
     * @param  int screenX
     * @param  int screenY
     * @param  boolean altKey
     * @param  boolean ctrlKey
     * @param  boolean shiftKey
     * @param  string keyCode
     * @return mixed
     */
    public function __construct($sender, $comment, $timestamp, $type, $target, $screenX, $screenY, $altKey, $ctrlKey, $shiftKey, $keyCode)
    {
        // section 10-13-1-85-5a611d25:1213534f5a0:-8000:0000000000001671 begin
        parent::__construct($sender, $comment);
        $this->timestamp = $timestamp;
        $this->type = $type;
        $this->target = $target;
        $this->screenX = $screenX;
        $this->screenY = $screenY;
        $this->altKey = $altKey;
        $this->ctrlKey = $ctrlKey;
        $this->shiftKey = $shiftKey;
        $this->keyCode = $keyCode;
        // section 10-13-1-85-5a611d25:1213534f5a0:-8000:0000000000001671 end
    }

} /* end of class core_kernel_events_UIEvent */

?>