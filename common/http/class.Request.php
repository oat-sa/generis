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
 * Copyright (c) 20013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 * 
 */

/**
 * Represents an http request
 *
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package generis
 * @subpackage common_http
 */
class common_http_Request
{

    const METHOD_POST = 'POST';

    const METHOD_GET = 'GET';

    /**
     * Creates an request from the current call
     *
     * @return common_http_Request
     */
    public static function currentRequest()
    {
        if (php_sapi_name() == 'cli') {
            throw common_exception_Error('Cannot call ' . __FUNCTION__ . ' from command line');
        }
        
        $scheme = (! isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] != "on") ? 'http' : 'https';
        $url = $scheme . '://' . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . $_SERVER['REQUEST_URI'];
        
        $method = $_SERVER['REQUEST_METHOD'];
        
        $params = $_POST;
        
        if (function_exists('apache_request_headers')) {
            $headers = apache_request_headers();
        } else {
            $headers = array();
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

    public function __construct($url, $method = self::METHOD_POST, $params = array(), $headers = array())
    {
        $this->url = $url;
        $this->method = $method;
        $this->params = $params;
        $this->headers = $headers;
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
    
    public function getHeaders()
    {
        return $this->headers;
    }
}