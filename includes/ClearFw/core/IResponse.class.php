<?php
/**
 * IResponse interface
 * TODO IResponse interface documentation.
 * 
 * @author Jérôme Bogaerts <jerome.bogaerts@tudor.lu> <jerome.bogaerts@gmail.com>
 */
interface IResponse
{
	public function setCookie($name, $value = null, $expire = null, 
							  $domainPath = null, $https = null, $httpOnly = null);
	
	public function setContentHeader($contentType, $charset = 'UTF-8');
	public function getContentType();
	public function getCharset();
}
?>