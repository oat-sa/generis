<?php
/**
 * IViewable interface
 * TODO IViewable interface documentation.
 * 
 * @author Jérôme Bogaerts <jerome.bogaerts@tudor.lu> <jerome.bogaerts@gmail.com>
 */
interface IViewable
{
	public function getView();
	public function setView($identifier);
	public function setData($key, $value);
	public function getData($key);
}
?>