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
 * Copyright (c) 2019 (original work) Open Assessment Technologies SA
 *
 */

namespace oat\oatbox\http;

use oat\oatbox\session\LegacySessionUtils;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

abstract class Controller
{
    use HttpRequestHelperTrait;
    use HttpFlowTrait;
    use LegacySessionUtils;

    protected $request;
    protected $response;

    public function setRequest(ServerRequestInterface $request)
    {
        $this->request = $request;
        return $this;
    }

    public function setResponse(ResponseInterface $response)
    {
        $this->response = $response;
        return $this;
    }

    /**
     * @return ServerRequestInterface
     */
    protected function getPsrRequest()
    {
        return $this->request;
    }

    /**
     * @return ResponseInterface
     */
    public function getPsrResponse()
    {
        return $this->response;
    }

    /**
     * Check if the HTTP request method is GET
     *
     * @return bool
     */
    protected function isRequestGet()
    {
        return $this->getRequestMethod() == 'GET';
    }

    /**
     * Check if the HTTP request method is POST
     *
     * @return bool
     */
    protected function isRequestPost()
    {
        return $this->getRequestMethod() == 'POST';
    }

    /**
     * Check if the HTTP request method is PUT
     *
     * @return bool
     */
    protected function isRequestPut()
    {
        return $this->getRequestMethod() == 'PUT';
    }

    /**
     * Check if the HTTP request method is DELETE
     *
     * @return bool
     */
    protected function isRequestDelete()
    {
        return $this->getRequestMethod() == 'DELETE';
    }

    /**
     * Check if the HTTP request method is HEAD
     *
     * @return bool
     */
    protected function isRequestHead()
    {
        return $this->getRequestMethod() == 'HEAD';
    }

    /**
     * Check if the current request is using AJAX
     *
     * @return bool
     */
    protected function isXmlHttpRequest()
    {
        $serverParams = $this->getPsrRequest()->getServerParams();
        if(isset($serverParams['HTTP_X_REQUESTED_WITH'])){
            if(strtolower($serverParams['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
                return true;
            }
        }

        return false;
    }

    /**
     * Get the user agent from HTTP request header "user-agent"
     *
     * @return string[]
     */
    protected function getUserAgent()
    {
        return $this->getPsrRequest()->getHeader('user-agent');
    }

    /**
     * Get the query string of HTTP request Uri
     *
     * @return string
     */
    protected function getQueryString()
    {
        return $this->getPsrRequest()->getUri()->getQuery();
    }

    /**
     * Get the request uri e.q. the HTTP request path
     *
     * @return string
     */
    protected function getRequestURI()
    {
        return $this->getPsrRequest()->getUri()->getPath();
    }

    /**
     * Get the content type from HTTP request header "content-type"
     *
     * @return string[]
     */
    protected function getContentType()
    {
        return $this->getPsrRequest()->getHeader('content-type');
    }

    /**
     * Set cookie by setting the HTTP response header "set-cookie"
     *
     * @param $name
     * @param null $value
     * @param null $expire
     * @param null $domainPath
     * @param null $https
     * @param null $httpOnly
     * @return bool
     */
    protected function setCookie($name, $value = null, $expire = null, $domainPath = null, $https = null, $httpOnly = null)
    {
        return setcookie($name, $value, $expire, $domainPath, $https, $httpOnly);
    }

    /**
     * Set content-type by setting the HTTP response header "content-type"
     *
     * @param $contentType
     * @param string $charset
     * @return $this
     */
    protected function setContentHeader($contentType, $charset = 'UTF-8')
    {
        $this->response = $this->getPsrResponse()->withHeader('content-type', $contentType . ';' . $charset);
        return $this;
    }
}