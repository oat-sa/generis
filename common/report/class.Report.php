<?php
/**  
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * 
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 * 
 */

/**
 * The Report allows to return a more detailed return value
 * then a simple boolean variable denoting the success
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package common
 * @subpackage report
 */
class common_report_Report
{
    const TYPE_SUCCESS = 1;
    
    const TYPE_INFO = 2;
    
    const TYPE_WARNING = 4;
    
    const TYPE_ERROR = 8;
    
    /**
     * type of the report
     * @var int
     */
	private $type;
	
    /**
     * message of the report
     * @var string
     */
	private $message;

	/**
	 * elements of the report
	 * @var array
	 */
	private $elements;
	
	/**
	 * Attached data
	 * @var mixed
	 */
    private $data = null;
	
	/**
	 * convenience methode to create a simple success report
	 * 
	 * @param string $title
	 * @param mixed $data
	 * @return common_report_Report
	 */
	public static function createSuccess($message = '', $data = null) {
	    return new static(self::TYPE_SUCCESS, $message, $data);
	}
	
	/**
	 * convenience methode to create a simple failure report
	 * 
	 * @param string $title
	 * @param mixed $errors
	 * @return common_report_Report
	 */
	public static function createFailure($message, $errors = array()) {
	    $report = new static(self::TYPE_ERROR, $message);
	    foreach ($errors as $error) {
	        $report->add($error);
	    }
	    return $report;
	}
	
	public function __construct($type, $message = '', $data = null) {
	    $this->type = $type;
		$this->message = $message;
	    $this->elements = array();
	    $this->data = $data;
	}
	
	/**
	 * Change the title of the report
	 * @param string $title
	 */
	public function setTitle($message) {
		$this->message = $message;
	}
	
	/**
	 * returns the tile of the report
	 * @return string
	 */
	public function getTitle() {
		return $this->message;
	}
	
	/**
	 * returns the type of the report
	 * @return int
	 */
	public function getType() {
	    return $this->type;
	}
	
	public function getData() {
	    return $this->data;
	}
	
	/**
	 * returns all success elements
	 * @return array
	 */
	public function getSuccesses() {
        $successes = array();
		foreach ($this as $element) {
		    if ($element->getType == self::TYPE_SUCCESS) {
		        $successes[] = $element;
		    }
		}
		return $successes;
	}
	
	/**
	 * returns all error elements
	 * @return array
	 */
	public function getErrors() {
        $errors = array();
		foreach ($this as $element) {
    		if ($element->getType == self::TYPE_ERROR) {
                $successes[] = $element;
            }
		}
		return $errors;
	}
	
	/**
	 * Whenever or not teh report contains errors
	 * @return boolean
	 */
    public function containsError() {
	    $found = false;
		foreach ($this->elements as $element) {
		    if ($element->getType == self::TYPE_ERROR) {
                $found = true;
		        break;
		    }
		}
		return $found;
    }
    
	/**
	 * Whenever or not teh report contains successes
	 * @return boolean
	 */
    public function containsSuccess() {
	    $found = false;
        foreach ($this->elements as $element) {
		    if ($element->getType == self::TYPE_ERROR) {
                $found = true;
		        break;
		    }
		}
		return $found;
	}
	
	/**
	 * Add something to the report
	 * @param mixed $mixed accepts Arrays, Reports, ReportElements and Exceptions
	 */
	public function add($mixed) {
	    $mixedArray = is_array($mixed) ? $mixed : array($mixed);
		foreach ($mixedArray as $element) {
		    if ($element instanceof common_report_Report) {
		        $this->elements[] = $element;
		    } elseif ($element instanceof common_exception_UserReadableException) {
		        $this->elements[] = new static(self::TYPE_ERROR, $element->getUserMessage());
		    } else {
		        throw new common_exception_Error('Tried to add '.(is_object($element) ? get_class($element) : gettype($element)).' to report');
		    }
		}
	}

	public function __toString() {
	    return $this->message;
	}
}