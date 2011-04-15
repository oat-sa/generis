<?php
/**
 * VersionException class
 * TODO VersionException class documentation.
 * 
 * @author Jérôme Bogaerts <jerome.bogaerts@tudor.lu> <jerome.bogaerts@gmail.com>
 */
class VersionException extends Exception
{
	public function __construct($message)
	{
		parent::__construct($message);
	}
}
?>