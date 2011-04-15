<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - core/kernel/classes/class.EventFromJS.php
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatic generated with ArgoUML 0.24 on 30.09.2008, 14:50:19
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package core
 * @subpackage kernel_classes
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include core_kernel_events_Event
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 */


/* user defined includes */
// section 127-0-0-1-15e1af03:11cb3476755:-8000:0000000000000EE4-includes begin
// section 127-0-0-1-15e1af03:11cb3476755:-8000:0000000000000EE4-includes end

/* user defined constants */
// section 127-0-0-1-15e1af03:11cb3476755:-8000:0000000000000EE4-constants begin
// section 127-0-0-1-15e1af03:11cb3476755:-8000:0000000000000EE4-constants end

/**
 * Short description of class core_kernel_events_EventFromJS
 *
 * @access public
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package core
 * @subpackage kernel_classes
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
class core_kernel_events_EventFromJS
    extends core_kernel_events_Event
{
    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
     * @param string
     * @param string
     * @return core_kernel_classes_Session_void
     */
    public function __construct($sender, $comment)
    {
        // section 127-0-0-1-15e1af03:11cb3476755:-8000:0000000000000EE7 begin
        if (empty($GLOBALS["EVENTLOG"]["ENABLED"])) return;
        parent::__construct($sender, $comment);

        $debugBackTrace = null;

        $this->funct = null;
        $this->line = null;
        $this->fileName = null;
        $this->className = null;
        $this->object = null;
        $this->type = null;
        $this->args = null;
        // section 127-0-0-1-15e1af03:11cb3476755:-8000:0000000000000EE7 end
    }

} /* end of class core_kernel_events_EventFromJS */

?>