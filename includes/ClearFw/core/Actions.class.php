<?php
/**
 * Actions class
 * TODO Actions class documentation.
 * 
 * @author J�r�me Bogaerts <jerome.bogaerts@tudor.lu> <jerome.bogaerts@gmail.com>
 */
abstract class Actions implements IRequest, IResponse, ISession
{
	public function getRequest()
	{
		return Context::getInstance()->getRequest();
	}
	
	public function getResponse()
	{
		return Context::getInstance()->getResponse();
	}
	
	public function getRequestParameters()
	{
		return $this->getRequest()->getParameters();
	}
	
	public function hasRequestParameter($name)
	{
		return $this->getRequest()->hasParameter($name);
	}
	
	public function getRequestParameter($name)
	{
		return $this->getRequest()->getParameter($name);
	}
	
	public function hasCookie($name)
	{
		return $this->getRequest()->hasCookie($name);
	}
	
	public function getCookie($name)
	{
		return $this->getRequest()->getCookie($name);
	}
	
	public function getRequestMethod()
	{
		return $this->getRequest()->getMethod();
	}
	
	public function isRequestGet()
	{
		return $this->getRequest()->isGet();
	}
	
	public function isRequestPost()
	{
		return $this->getRequest()->isPost();
	}
	
	public function isRequestPut()
	{
		return $this->getRequest()->isPut();
	}
	
	public function isRequestDelete()
	{
		return $this->getRequest()->isDelete();
	}
	
	public function isRequestHead()
	{
		return $this->getRequest()->isHead();
	}
	
	public function getUserAgent()
	{
		return $this->getRequest()->getUserAgent();
	}
	
	public function getQueryString()
	{
		return $this->getRequest()->getQueryString();
	}
	
	public function getRequestURI()
	{
		return $this->getRequest()->getRequestURI();
	}
	
	public function setCookie($name, $value = null, $expire = null, 
							  $domainPath = null, $https = null, $httpOnly = null)
	{
		return $this->getResponse()->setCookie($name, $value, $expire, $domainPath, $https, $httpOnly);						  	
	}
	
	public function setContentHeader($contentType, $charset = 'UTF-8')
	{
		$this->getResponse()->setContentHeader($contentType, $charset);
	}
	
	public function getContentType()
	{
		$this->getResponse()->getContentType();
	}
	
	public function getCharset()
	{
		$this->getCharset();
	}
	
	public function hasSessionAttribute($name)
	{
		return Context::getInstance()->getSession()->hasAttribute($name);
	}
	
	public function getSessionAttribute($name)
	{
		return Context::getInstance()->getSession()->getAttribute($name);
	}
	
	public function setSessionAttribute($name, $value)
	{
		Context::getInstance()->getSession()->setAttribute($name, $value);
	}
	
	public function removeSessionAttribute($name){
		Context::getInstance()->getSession()->removeAttribute($name);
	}
	
	public function clearSession($global = true)
	{
		Context::getInstance()->getSession()->clear($global);
	}
}
?>