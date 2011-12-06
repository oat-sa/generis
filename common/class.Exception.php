<?php
/**
 * Generis Object Oriented API - common\class.Exception.php
 *
 *
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 02.04.2009, 14:14:33 with ArgoUML PHP module 
 * (last revised $Date: 2008-04-19 08:22:08 +0200 (Sat, 19 Apr 2008) $)
 *
 * @author lionel.lecaque@tudor.lu
 * @package common
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */

class common_Exception extends Exception{
	
    public function __construct($message = null, $code = 0)
    {
        if (!$message) {
            throw new $this('Unknown '. get_class($this));
        }
        parent::__construct($message, $code);
        common_Logger::i($this->__toString());
    }
	
	public function __toString()
    {
        return get_class($this) . " '{$this->message}' in {$this->file}({$this->line})\n"
                                . "{$this->getTraceAsString()}";
    }
	
}
?>