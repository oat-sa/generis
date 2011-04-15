<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - common\class.Object.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 24.03.2010, 14:38:37 with ArgoUML PHP module 
 * (last revised $Date: 2008-04-19 08:22:08 +0200 (Sat, 19 Apr 2008) $)
 *
 * @author lionel.lecaque@tudor.lu
 * @package common
 * @see @license  GNU General Public (GPL) Version 2 http://www.opensource.org/licenses/gpl-2.0.php
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 10-13-1--31--86a648e:11985da1cd8:-8000:0000000000000ACE-includes begin
// section 10-13-1--31--86a648e:11985da1cd8:-8000:0000000000000ACE-includes end

/* user defined constants */
// section 10-13-1--31--86a648e:11985da1cd8:-8000:0000000000000ACE-constants begin
// section 10-13-1--31--86a648e:11985da1cd8:-8000:0000000000000ACE-constants end

/**
 * Short description of class common_Object
 *
 * @access public
 * @author lionel.lecaque@tudor.lu
 * @package common
 * @see @license  GNU General Public (GPL) Version 2 http://www.opensource.org/licenses/gpl-2.0.php
 */
class common_Object
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * c'est wak cet attribut ?
     *
     * @access public
     * @var string
     */
    public $debug = '';

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  string debug
     * @return void
     */
    public function __construct($debug = '')
    {
        // section 10-13-1--99--2fd200b5:11b26e19c13:-8000:0000000000000D5B begin
        $this->debug = $debug;
        // section 10-13-1--99--2fd200b5:11b26e19c13:-8000:0000000000000D5B end
    }

    /**
     * Short description of method __toString
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return string
     */
    public function __toString()
    {
        $returnValue = (string) '';

        // section 10-13-1--99--150f74b9:12066be2698:-8000:000000000000172A begin
        $returnValue = 'Object Created in ' . $this->debug;
        // section 10-13-1--99--150f74b9:12066be2698:-8000:000000000000172A end

        return (string) $returnValue;
    }

} /* end of class common_Object */

?>