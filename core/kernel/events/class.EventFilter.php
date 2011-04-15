<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - core\kernel\events\class.EventFilter.php
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
// section 127-0-0-1-48ef8247:11bf9e06099:-8000:0000000000000D83-includes begin
// section 127-0-0-1-48ef8247:11bf9e06099:-8000:0000000000000D83-includes end

/* user defined constants */
// section 127-0-0-1-48ef8247:11bf9e06099:-8000:0000000000000D83-constants begin
// section 127-0-0-1-48ef8247:11bf9e06099:-8000:0000000000000D83-constants end

/**
 * Short description of class core_kernel_events_EventFilter
 *
 * @access public
 * @author firstname and lastname of author, <author@example.org>
 * @package core
 * @subpackage kernel_events
 */
class core_kernel_events_EventFilter
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return void
     */
    public function __construct()
    {
        // section 127-0-0-1-2d1c59d3:11c0478bd31:-8000:0000000000000EFC begin
        // section 127-0-0-1-2d1c59d3:11c0478bd31:-8000:0000000000000EFC end
    }

    /**
     * Short description of method addEvent
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  Event event
     * @return void
     */
    public function addEvent( core_kernel_events_Event $event)
    {
        // section 127-0-0-1-48ef8247:11bf9e06099:-8000:00000000000013E5 begin
        // section 127-0-0-1-48ef8247:11bf9e06099:-8000:00000000000013E5 end
    }

    /**
     * Short description of method setMode
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  boolean mode
     * @return void
     */
    public function setMode($mode)
    {
        // section 127-0-0-1-48ef8247:11bf9e06099:-8000:0000000000001407 begin
        // section 127-0-0-1-48ef8247:11bf9e06099:-8000:0000000000001407 end
    }

    /**
     * Short description of method isFiltered
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  Event event
     * @return void
     */
    public function isFiltered( core_kernel_events_Event $event)
    {
        // section 127-0-0-1-48ef8247:11bf9e06099:-8000:000000000000140A begin
        return true;
        // section 127-0-0-1-48ef8247:11bf9e06099:-8000:000000000000140A end
    }

} /* end of class core_kernel_events_EventFilter */

?>