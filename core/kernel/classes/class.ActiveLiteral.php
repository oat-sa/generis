<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - core\kernel\classes\class.ActiveLiteral.php
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatic generated with ArgoUML 0.24 on 05.12.2008, 17:02:58
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
 * include core_kernel_classes_Literal
 *
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 */
require_once('core/kernel/classes/class.Literal.php');

/* user defined includes */
// section 10-13-1--99--635ab970:11cb92c4b43:-8000:0000000000000EF1-includes begin
// section 10-13-1--99--635ab970:11cb92c4b43:-8000:0000000000000EF1-includes end

/* user defined constants */
// section 10-13-1--99--635ab970:11cb92c4b43:-8000:0000000000000EF1-constants begin
// section 10-13-1--99--635ab970:11cb92c4b43:-8000:0000000000000EF1-constants end

/**
 * Short description of class core_kernel_classes_ActiveLiteral
 *
 * @access public
 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
 * @package core
 * @subpackage kernel_classes
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
class core_kernel_classes_ActiveLiteral
extends core_kernel_classes_Literal
{
	// --- ATTRIBUTES ---

	// --- OPERATIONS ---

	/**
	 * Short description of method getDisplayedText
	 *
	 * @access public
	 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
	 * @param array
	 * @return string
	 */
	public function getDisplayedText($variable = array())
	{
		$returnValue = (string) '';

		// section 10-13-1--99--635ab970:11cb92c4b43:-8000:0000000000000EF9 begin
		 
		//variable substitution
		$returnValue = $this->literal;
			
		$matches = array();
		preg_match_all('/\^[^\^\s\?\!\.\:&;,]*/i', $returnValue, $matches);

		
		foreach ($matches[0] as $infvariable)
		{
			$var = substr($infvariable,1);
			$var = str_replace(',', '', $var);
			//retrieve current value
			//find the uri of the property behind the variable

			$predicate  = core_kernel_classes_Session::singleton()->model->execSQL("AND predicate='" . PROPERTY_CODE . "' AND object ='" . $var . "'");
			
			if (isset($predicate[0][0]))
			{
				var_dump($variable);
				$predicateUri = $predicate[0][0];
				$interviewee = new core_kernel_classes_resource($variable[VAR_INTERVIEWEE_URI]);
				$varResource = new core_kernel_classes_Property($predicateUri);
				$values = $interviewee->getPropertyValues($varResource);

				$i = 0;

				if (isset($values[0]))
				{
					if (count($values) > 1)
					{
						foreach ($values as $val)
						{
							if (!common_Utils::isUri($val))
							break;

							$i++;
						}
					}
						
					if ($i > count($values))
					$i = 0;	// Only resource URIs in the collection were found, we take the first
					// one as a text placeholder.

					if (common_Utils::isUri($values[$i]))
					{
						$resValue = new core_kernel_classes_resource($values[$i]);
						$returnValue = str_replace($infvariable, $resValue->getLabel(),$returnValue);
					}
					else
					{
						$returnValue = str_replace($infvariable, $values[$i],$returnValue);
					}
						
					$returnValue = str_replace(array('DK', 'RF'), '', $returnValue);
				}
			}
			else
			{
				//trigger_error("a variable here was not imported :".$var."<br />");
			}
		}

		$secondPart = strstr($returnValue,START_DYNAMIC_TEXT_DELIMITER);
		if ($secondPart === false)
		return $returnValue;

		$posStart = strpos($returnValue, START_DYNAMIC_TEXT_DELIMITER);
		$firstPart = substr($returnValue, 0, $posStart);
		$posEnd = strpos($secondPart, END_DYNAMIC_TEXT_DELIMITER);
		$dynamicText = substr($secondPart, strlen(START_DYNAMIC_TEXT_DELIMITER), $posEnd-strlen(END_DYNAMIC_TEXT_DELIMITER) + 1);
		$stillToEval = substr (strstr($secondPart, END_DYNAMIC_TEXT_DELIMITER), strlen(END_DYNAMIC_TEXT_DELIMITER));
		$dynamicTextObject = new core_kernel_classes_DynamicText($dynamicText, __METHOD__);
		$newActiveLiteral = new core_kernel_classes_ActiveLiteral($stillToEval, __METHOD__);
		$returnValue = $firstPart . $dynamicTextObject->getDisplayedText($variable) . $newActiveLiteral->getDisplayedText($variable);

		// section 10-13-1--99--635ab970:11cb92c4b43:-8000:0000000000000EF9 end

		return (string) $returnValue;
	}
	/**
	 * Short description of method getDisplayedText
	 *
	 * @access public
	 * @author CRP Henri Tudor - TAO Team - {@link http://www.tao.lu}
	 * @param array
	 * @return string
	 */
	public function getDisplayedCode($variable = array())
	{
		$returnValue = (string) '';

		// section 10-13-1--99--635ab970:11cb92c4b43:-8000:0000000000000EF9 begin
		//print_r($variable);
		//variable substitution
		$returnValue = $this->literal;
			
		$matches = array();
		preg_match_all('/\^[^\^\s\?\!\.\:&;,]*/i', $returnValue, $matches);


		foreach ($matches[0] as $infvariable)
		{
			$var = substr($infvariable,1);
			$var = str_replace(',', '', $var);
			//retrieve current value
			//find the uri of the property behind the variable

			$predicate  = core_kernel_classes_Session::singleton()->model->execSQL("AND predicate='" . PROPERTY_CODE . "' AND object ='" . $var . "'");
				
			if (isset($predicate[0][0]))
			{
				$predicateUri = $predicate[0][0];
				if(isset($variable[$var])) {
					$returnValue = str_replace($infvariable, $variable[$var]['value'],$returnValue);
				}

			}
			else
			{
				//trigger_error("a variable here was not imported :".$var."<br />");
			}
		}
		

//		$returnValue = str_replace("^var_interviewee", $variable["Var_Interviewee"]["value"],$returnValue);


		// section 10-13-1--99--635ab970:11cb92c4b43:-8000:0000000000000EF9 end

		return (string) $returnValue;
	}

} /* end of class core_kernel_classes_ActiveLiteral */

?>