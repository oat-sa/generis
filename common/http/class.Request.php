<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2013-2016 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */

/**
 * Represents an http request
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package generis

 */
class common_http_Request
{

    const METHOD_POST = 'POST';

    const METHOD_GET = 'GET';

    private const REDIRECT_CODES = [
        301,
        302,
        303,
        307,
        308
    ];

    /**
     * Creates an request from the current call
     *
     * The scheme in used (http|https) will be derived from
     *
     * * $_SERVER['HTTPS'] in case of a standard deployment
     * * $_SERVER['HTTP_X_FORWARDED_PROTO'] or $_SERVER['HTTP_X_FORWARDED_SSL'] in case of being deployed behing a load balancer/proxy.
     *
     * If no clues about whether HTTPS is in use are found, HTTP will be the scheme of the current request.
     *
     * @return common_http_Request A request corresponding to the current HTTP(S) context.
     * @throws common_exception_Error In case of a CLI execution context.
     */
    public static function currentRequest()
    {
        if (php_sapi_name() == 'cli') {
            throw new common_exception_Error('Cannot call ' . __FUNCTION__ . ' from command line');
        }

        $https = self::isHttps();

        $scheme = $https ? 'https' : 'http';
        $port = empty($_SERVER['HTTP_X_FORWARDED_PORT']) ? $_SERVER['SERVER_PORT'] : $_SERVER['HTTP_X_FORWARDED_PORT'];
        $url = $scheme . '://' . $_SERVER['SERVER_NAME'] . ':' . $port . $_SERVER['REQUEST_URI'];

        $method = $_SERVER['REQUEST_METHOD'];

        if ($_SERVER['REQUEST_METHOD'] == self::METHOD_GET) {
            $params = $_GET;
        } else {
            $params = $_POST;
        }
        if (function_exists('apache_request_headers')) {
            $headers = apache_request_headers();
        } else {
            $headers = [];
            if (isset($_SERVER['CONTENT_TYPE'])) {
                $headers['Content-Type'] = $_SERVER['CONTENT_TYPE'];
            }
            if (isset($_ENV['CONTENT_TYPE'])) {
                $headers['Content-Type'] = $_ENV['CONTENT_TYPE'];
            }
            foreach ($_SERVER as $key => $value) {
                if (substr($key, 0, 5) == "HTTP_") {
                    // this is chaos, basically it is just there to capitalize the first
                    // letter of every word that is not an initial HTTP and strip HTTP
                    // code from przemek
                    $key = str_replace(" ", "-", ucwords(strtolower(str_replace("_", " ", substr($key, 5)))));
                    $headers[$key] = $value;
                }
            }
        }
        return new self($url, $method, $params, $headers);
    }

    private $url;

    private $method;

    private $params;

    private $headers;

    private $body;

    public function __construct($url, $method = self::METHOD_POST, $params = [], $headers = [], $body = "")
    {
        $this->url = $url;
        $this->method = $method;
        $this->params = $params;
        $this->headers = $headers;
        $this->body = $body;
    }

    /**
     * Detect whether we use https or http
     * @return bool
     */
    public static function isHttps()
    {
        // Default is http scheme.
        $https = false;

        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") {
            // $_SERVER['HTTPS'] is NOT set behind a proxy / load balancer
            $https = true;
        } elseif (
            // $_SERVER['HTTPS'] is set behind a proxy / load balancer
            !empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' ||
            !empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on'
        ) {
            $https = true;
        }
        return $https;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function getParams()
    {
        return $this->params;
    }

    public function setParam($key, $value)
    {
        return $this->params[$key] = $value;
    }
    public function setHeader($key, $value)
    {
        return $this->headers[$key] = $value;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Get the value of an HTTP header
     * The lookup is case insensitive.
     * @param string $headerName the HTTP header name, 'Content-Type' for example
     * @return boolean|string the value or false if not found
     */
    public function getHeaderValue($headerName)
    {
        if (is_string($headerName) && count($this->headers) > 0) {
            $lowCaseHeaders = array_change_key_case($this->headers, CASE_LOWER);
            $lowCaseHeaderName = strtolower($headerName);
            if (isset($lowCaseHeaders[$lowCaseHeaderName])) {
                return $lowCaseHeaders[$lowCaseHeaderName];
            }
        }
        return false;
    }

    /**
     * set request body to send
     */
    public function setBody($requestBodyData)
    {
        $this->body = $requestBodyData;
    }
    public function getBody()
    {
        return $this->body;
    }

    public function send(bool $followRedirects = false): common_http_Response
    {
        $curlHandler = curl_init($this->getUrl());

          //set the headers
        if ((is_array($this->headers)) and (count($this->headers) > 0)) {
             curl_setopt($curlHandler, CURLOPT_HTTPHEADER, self::headerEncode($this->headers));
        }
        switch ($this->getMethod()) {
            case "HEAD":{
                    curl_setopt($curlHandler, CURLOPT_NOBODY, true);
                    curl_setopt($curlHandler, CURLOPT_HEADER, true);
                break;
            }
            case "POST":{
                   curl_setopt($curlHandler, CURLOPT_POST, 1);

                if (is_array($this->params) and (count($this->params) > 0)) {
                    $params =  $this->postEncode($this->params);
                    //application/x-www-form-urlencoded
                    curl_setopt($curlHandler, CURLOPT_POSTFIELDS, $params);
                } else {
                    //common_Logger::i(serialize($this->getBody()));
                    if (!is_null(($this->getBody()))) {
                        curl_setopt($curlHandler, CURLOPT_POSTFIELDS, ($this->getBody()));
                    }
                }


                   //analyse if there is a body or structured postfields

                break;
            }
            case "PUT":{

                break;
            }
            case "GET":{
                //curl_setopt($curlHandler,CURLOPT_HTTPGET, true);
                break;
            }
        }

        curl_setopt($curlHandler, CURLOPT_RETURNTRANSFER, 1);
        //curl_setopt($curlHandler, CURLINFO_HEADER_OUT, 1);
        //curl_setopt($curlHandler, CURLOPT_HEADER, true);

        //directly setting `FOLLOWLOCATION` to false to make sure next lines would be working as expected
        //and we can get the redirect url from curl
        curl_setopt($curlHandler, CURLOPT_FOLLOWLOCATION, 0);
        
        $responseData = curl_exec($curlHandler);
        $httpResponse = new common_http_Response();

        $httpResponse->httpCode = curl_getinfo($curlHandler, CURLINFO_HTTP_CODE);
        $httpResponse->headerOut = curl_getinfo($curlHandler, CURLINFO_HEADER_OUT);
        $httpResponse->effectiveUrl = curl_getinfo($curlHandler, CURLINFO_EFFECTIVE_URL);
        $httpResponse->responseData = $responseData;

        $redirectUrl = curl_getinfo($curlHandler, CURLINFO_REDIRECT_URL);
        $sameDomain = null;
        if ($redirectUrl) {
            $initialDomain = parse_url($this->getUrl(), PHP_URL_HOST);
            $redirectDomain = parse_url($redirectUrl, PHP_URL_HOST);

            $sameDomain = ($initialDomain === $redirectDomain);
        }

        //curl_setopt($curlHandler, );
        curl_close($curlHandler);

        if (
            $followRedirects
            && $sameDomain
            && in_array($httpResponse->httpCode, self::REDIRECT_CODES, true)
        ) {
            $this->url = $redirectUrl;
            $httpResponse = $this->send();
        }

        return $httpResponse;
    }

    /**
     * @param array
     * @return string
     */

    public static function postEncode($parameters)
    {

        //todo
        //$content_type = isset($this->headers['Content-Type']) ? $this->headers['Content-Type'] : 'text/plain';
        //should detect suitable encoding
        $format = 'text/plain';
        switch ($format) {
            default:
                return http_build_query($parameters, null, '&');
                break;
        }
    }

    public static function headerEncode($headers)
    {
        $encodedHeaders = [];
        //todo using aray_walk
        foreach ($headers as $key => $value) {
            $encodedHeaders[] = $key . ": " . $value . "";
        }
        //print_r($encodedHeaders);
        return $encodedHeaders;
    }
}
