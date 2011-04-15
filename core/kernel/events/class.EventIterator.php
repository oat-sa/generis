<?php

error_reporting(E_ALL);

/**
 * Should implement Iterator but cannot due to Argo limitation. It's possible to
 * this with Iterator's function but won't be recognized as such by foreach. It
 * consider the object as an array....
 *
 * @author firstname and lastname of author, <author@example.org>
 * @package core
 * @subpackage kernel_events
 */

if (0 > version_compare(PHP_VERSION, '5')) {
	die('This file was generated for PHP 5');
}

/* user defined includes */
// section 127-0-0-1-48ef8247:11bf9e06099:-8000:0000000000000D87-includes begin
// section 127-0-0-1-48ef8247:11bf9e06099:-8000:0000000000000D87-includes end

/* user defined constants */
// section 127-0-0-1-48ef8247:11bf9e06099:-8000:0000000000000D87-constants begin

// function mycallbackx($className) {
//   echo "<p>$className</p>";
//   eval("class $className {}");
// }

//ini_set('unserialize_callback_func', 'mycallbackx');
// section 127-0-0-1-48ef8247:11bf9e06099:-8000:0000000000000D87-constants end

/**
 * Should implement Iterator but cannot due to Argo limitation. It's possible to
 * this with Iterator's function but won't be recognized as such by foreach. It
 * consider the object as an array....
 *
 * @access public
 * @author firstname and lastname of author, <author@example.org>
 * @package core
 * @subpackage kernel_events
 */
class core_kernel_events_EventIterator
{
	// --- ASSOCIATIONS ---


	// --- ATTRIBUTES ---

	/**
	 * Short description of attribute db
	 *
	 * @access protected
	 * @var DbWrapper
	 */
	protected $db = null;

	/**
	 * Short description of attribute table
	 *
	 * @access protected
	 * @var string
	 */
	protected $table = '';

	/**
	 * Short description of attribute field
	 *
	 * @access protected
	 * @var string
	 */
	protected $field = '';

	/**
	 * Short description of attribute eventLogger
	 *
	 * @access public
	 * @var EventLogger
	 */
	public $eventLogger = null;

	// --- OPERATIONS ---

	/**
	 * Short description of method __construct
	 *
	 * @access public
	 * @author firstname and lastname of author, <author@example.org>
	 * @param  EventLogger eventLogger
	 * @return void
	 */
	public function __construct( core_kernel_events_EventLogger $eventLogger)
	{
		// section 127-0-0-1--434528f6:11c08318dbe:-8000:0000000000000E0B begin
		$this->eventLogger = $eventLogger;
		$this->oldestThreshold = $oldestThreshold;
		$this->youngestThreshold = $youngestThreshold;
		$this->db = $this->eventLogger->db;
		$this->table = $this->eventLogger->table;
		$this->field = $this->eventLogger->field;
		$this->rewind();
		// section 127-0-0-1--434528f6:11c08318dbe:-8000:0000000000000E0B end
	}

	/**
	 * Short description of method current
	 *
	 * @access public
	 * @author firstname and lastname of author, <author@example.org>
	 * @return void
	 */
	public function current()
	{
		// section 127-0-0-1-48ef8247:11bf9e06099:-8000:00000000000013DB begin
		return unserialize($this->events->fields[0]);
		// section 127-0-0-1-48ef8247:11bf9e06099:-8000:00000000000013DB end
	}

	/**
	 * Short description of method key
	 *
	 * @access public
	 * @author firstname and lastname of author, <author@example.org>
	 * @return void
	 */
	public function key()
	{
		// section 127-0-0-1-48ef8247:11bf9e06099:-8000:00000000000013DD begin
		throw Exception("not implemented");
		// section 127-0-0-1-48ef8247:11bf9e06099:-8000:00000000000013DD end
	}

	/**
	 * Short description of method next
	 *
	 * @access public
	 * @author firstname and lastname of author, <author@example.org>
	 * @return void
	 */
	public function next()
	{
		// section 127-0-0-1-48ef8247:11bf9e06099:-8000:00000000000013DF begin
		$this->events->MoveNext();
		// section 127-0-0-1-48ef8247:11bf9e06099:-8000:00000000000013DF end
	}

	/**
	 * Short description of method rewind
	 *
	 * @access public
	 * @author firstname and lastname of author, <author@example.org>
	 * @return void
	 */
	public function rewind()
	{
		// section 127-0-0-1-48ef8247:11bf9e06099:-8000:00000000000013E1 begin
		try {
			$request = "
            SELECT $this->field
            FROM $this->table
            WHERE 1=1
          "; // yes, it's true that 1=1. It's useful to not mess up with AND statment...
			if (!empty($this->oldestThreshold)) {
				$request .= " AND timestamp>={$this->oldestThreshold}";
			}
			if (!empty($this->youngestThreshold)) {
				$request .= " AND timestamp<={$this->youngestThreshold}";
			}
			//DEBUG echo "EventIterator :'$request'\n";
			$this->events = $this->db->Execute($request);
		} catch (ADODB_Exception $e) {
			if (empty($GLOBALS["EVENTLOG"]["QUIET"])) throw $e;
		}
		// section 127-0-0-1-48ef8247:11bf9e06099:-8000:00000000000013E1 end
	}

	/**
	 * Short description of method valid
	 *
	 * @access public
	 * @author firstname and lastname of author, <author@example.org>
	 * @return void
	 */
	public function valid()
	{
		// section 127-0-0-1-48ef8247:11bf9e06099:-8000:00000000000013E3 begin
		//$self->next();
		if (is_object($this->events))
		return !$this->events->EOF;
		else
		return false; // in case of database error, don't even browse
		// section 127-0-0-1-48ef8247:11bf9e06099:-8000:00000000000013E3 end
	}

} /* end of class core_kernel_events_EventIterator */

?>