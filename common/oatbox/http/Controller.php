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

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

abstract class Controller extends \Module
{
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
    public function getRequest()
    {
//        return Context::getInstance()->getRequest();
        return $this->request;
    }

    /**
     * @return ResponseInterface
     */
    public function getResponse()
    {
//        return Context::getInstance()->getResponse();
        return $this->response;
    }

    public function getRequestParameters()
    {
        return array_merge(
            $this->getRequest()->getParsedBody(),
            $this->getRequest()->getQueryParams(),
            $this->getHeaders()
        );
    }

    public function hasRequestParameter($name)
    {
        return isset($this->getRequestParameters()[$name]);
    }

    public function getRequestParameter($name)
    {
        if ($this->hasRequestParameter($name)) {
            return $this->getRequestParameters()[$name];
        } else {
            return false;
        }
    }

    public function getHeaders()
    {
        $headers = [];
        foreach ($this->getRequest()->getHeaders() as $name => $values) {
            $headers[$name] = $values[0];
        }
        return $headers;
    }

    public function getHeader($name)
    {
        return $this->getRequest()->getHeader($name);
    }

    public function hasHeader($name)
    {
        return $this->getRequest()->hasHeader($name);
    }

    public function hasCookie($name)
    {
        return isset($this->getRequest()->getCookieParams()[$name]);
    }

    public function getCookie($name)
    {
        if ($this->hasCookie($name)) {
            return $this->getRequest()->getCookieParams()[$name];
        } else {
            return false;
        }
    }

    public function getRequestMethod()
    {
        return $this->getRequest()->getMethod();
    }

    public function isRequestGet()
    {
        return $this->getRequestMethod() == 'GET';
    }

    public function isRequestPost()
    {
        return $this->getRequestMethod() == 'POST';
    }

    public function isRequestPut()
    {
        return $this->getRequestMethod() == 'PUT';
    }

    public function isRequestDelete()
    {
        return $this->getRequestMethod() == 'DELETE';
    }

    public function isRequestHead()
    {
        return $this->getRequestMethod() == 'HEAD';
    }

    public function getUserAgent()
    {
        return $this->getRequest()->getHeader('user-agent');
    }

    public function getQueryString()
    {
        return $this->getRequest()->getUri()->getQuery();
    }

    public function getRequestURI()
    {
        return $this->getRequest()->getUri()->getPath();
    }

    public function setCookie($name, $value = null, $expire = null, $domainPath = null, $https = null, $httpOnly = null)
    {
        return setcookie($name, $value, $expire, $domainPath, $https, $httpOnly);
    }

    public function setContentHeader($contentType, $charset = 'UTF-8')
    {
        $response = $this->getResponse()->withHeader('content-type', $contentType . ';' . $charset);
        $this->response = $response;
    }

    public function getContentType()
    {
        return $this->getResponse()->getHeader('content-type');
    }
}