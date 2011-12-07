<?php

/**
 * include common_log_Appender
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
 */
require_once('common/log/interface.Appender.php');

/**
 * Short description of class common_log_BaseAppender
 *
 * @abstract
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package common
 * @subpackage log
 */
abstract class common_log_BaseAppender
        implements common_log_Appender
{

    /**
     * severity a logitem has to have to be considered
     *
     * @access protected
     * @var int
     */
    protected $threshold = 0;

    // --- OPERATIONS ---

    /**
     * Short description of method getLogThreshold
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return int
     */
    public function getLogThreshold()
    {
        $returnValue = (int) 0;

        $returnValue = $this->threshold;

        return (int) $returnValue;
    }

    /**
     * Short description of method init
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  array configuration
     * @return mixed
     */
    public function init($configuration)
    {
        if (isset($configuration['level']) && is_numeric($configuration['level']))
        	$this->threshold = intval($configuration['level']);
    }

}

?>