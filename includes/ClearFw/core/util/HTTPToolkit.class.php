<?php
/**
 * The HTTPToolkit class provides services relevant to the HTTP protocol.
 *
 * @author Jérôme Bogaerts <jerome.bogaerts@tudor.lu>
 * @package util
 */
class HTTPToolkit
{
	/**
	 * Build the complete status HTTP/1.1 header as a string for a given
	 * status code.
	 * 
	 * e.g : statusCodeHeader(404) => 'HTTP/1.1 404 Not Found'.
	 *
	 * @param integer $statusCode A HTTP status code described in the HTTP specification.
	 * @return string The HTTP/1.1 header relevant to the given status code.
	 */
	public static function statusCodeHeader($statusCode)
	{
		$codes[100] = 'Continue';
		$codes[101] = 'Switching Protocols';
		$codes[200] = 'OK';
		$codes[201] = 'Created';
		$codes[202] = 'Accepted';
		$codes[203] = 'Non-Authorative Information';
		$codes[204] = 'No Content';
		$codes[205] = 'Reset Content';
		$codes[206] = 'Partial Content';
		$codes[300] = 'Multiple Choices';
		$codes[301] = 'Moved Permanently';
		$codes[302] = 'Found';
		$codes[303] = 'See Other';
		$codes[304] = 'Not Modified';
		$codes[305] = 'Use Proxy';
		// Status code 306 is unused and reserved in HTTP 1.1
		$codes[307] = 'Temporary Redirect';
		$codes[400] = 'Bad Request';
		$codes[401] = 'Unauthorized';
		$codes[402] = 'Payment Required';
		$codes[403] = 'Forbidden';
		$codes[404] = 'Not found';
		$codes[405] = 'Method not allowed';
		$codes[406] = 'Not Acceptable';
		$codes[407] = 'Proxy Authentication Required';
		$codes[408] = 'Request Timeout';
		$codes[409] = 'Conflict';
		$codes[410] = 'Gone';
		$codes[411] = 'Length Required';
		$codes[412] = 'Precondition Failed';
		$codes[413] = 'Request Entity Too Large';
		$codes[414] = 'Request-URI Too Long';
		$codes[415] = 'Unsupported Media Type';
		$codes[416] = 'Requested Range Not Satisfiable';
		$codes[417] = 'Expectation Failed';
		$codes[500] = 'Internal Server Error';
		$codes[501] = 'Not Implemented';
		$codes[502] = 'Bad Gateway';
		$codes[503] = 'Service Unavailable';
		$codes[504] = 'Gateway Timeout';
		$codes[505] = 'HTTP Version Not Supported';
		
		if (isset($codes[$statusCode]))
			return 'HTTP/1.1 ' . $statusCode . ' ' . $codes[$statusCode];
		else
			return null; 
	}
	
	/**
	 * Builds a location HTTP header for a given URL/URI.
	 *
	 * @param string $url The URL that the agent must follow.
	 * @return string the HTTP location header for the given URL/URI.
	 */
	public static function locationHeader($url)
	{
		return 'Location: ' . $url;
	}
}
?>