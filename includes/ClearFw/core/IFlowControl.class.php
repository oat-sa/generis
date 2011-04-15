<?php
/**
 * IFlowControl interface
 * TODO IFlowControl class documentation.
 * 
 * @author Jérôme Bogaerts <jerome.bogaerts@tudor.lu> <jerome.bogaerts@gmail.com>
 */
interface IFlowControl
{
	public function forward($module, $action);
	public function redirect($url);
}
?>