<?php
/**
 * ISession interface
 * TODO ISession interface documentation.
 * 
 * @author J�r�me Bogaerts <jerome.bogaerts@tudor.lu> <jerome.bogaerts@gmail.com>
 */
interface ISession
{
	public function hasSessionAttribute($name);
	public function getSessionAttribute($name);
	public function setSessionAttribute($name, $value);
	public function removeSessionAttribute($name);
	
	public function clearSession($global = true);
}
?>