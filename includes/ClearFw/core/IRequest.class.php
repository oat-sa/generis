<?php
/**
 * IRequest interface
 * TODO IRequest interface documentation.
 * 
 * @author Jérôme Bogaerts <jerome.bogaerts@tudor.lu> <jerome.bogaerts@gmail.com>
 */
interface IRequest
{
	public function hasRequestParameter($name);
	public function getRequestParameter($name);
	
	public function hasCookie($name);
	public function getCookie($name);
	
	public function getRequestMethod();
	public function isRequestGet();
	public function isRequestPost();
	public function isRequestPut();
	public function isRequestDelete();
	public function isRequestHead();
	
	public function getUserAgent();
	public function getQueryString();
	public function getRequestURI();
}
?>