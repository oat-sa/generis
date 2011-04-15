<?php
/**
 * Response class
 * TODO Response class documentation.
 * 
 * @author J�r�me Bogaerts <jerome.bogaerts@tudor.lu> <jerome.bogaerts@gmail.com>
 */
class Response
{
	private $contentType;
	private $charset;
	
	public function __construct($contentType = 'text/html', $charset = 'UTF-8')
	{
		$this->changeHeader($contentType, $charset);
		
		$this->contentType = $contentType;
		$this->charset = $charset;
	}
	
	public function setCookie($name, $value = null, $expire = null, 
							  $domainPath = null, $https = null, $httpOnly = null)
	{
		setcookie($name, $value, $expire, $domainPath, $https, $httpOnly);
	}
	
	public function setContentHeader($contentType, $charset = 'UTF-8')
	{
		$this->changeHeader($contentType, $charset);
		$this->contentType = $contentType;
		$this->charset = $charset;
	}
	
	public function getContentType()
	{
		return $this->contentType;
	}
	
	public function getCharset()
	{
		return $this->charset;
	}
	
	private function changeHeader($contentType, $charset)
	{
		header('Content-Type: ' . $contentType . '; charset=' . $charset, true);
	}
}
?>