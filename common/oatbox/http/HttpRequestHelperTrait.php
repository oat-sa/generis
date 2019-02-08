<?php

namespace oat\oatbox\http;

use Psr\Http\Message\ServerRequestInterface;

trait HttpRequestHelperTrait
{
    /**
     * @return ServerRequestInterface
     */
    abstract protected function getPsrRequest();

    protected function getHeaders()
    {
        $headers = [];
        foreach ($this->getPsrRequest()->getHeaders() as $name => $values) {
            $headers[strtolower($name)] = (count($values) == 1) ? reset($values) : $values;
        }
        return $headers;
    }

    /**
     * Checks if a header exists by the given case-insensitive name.
     *
     * @param string $name Case-insensitive header field name.
     * @return bool Returns true if any header names match the given header
     *     name using a case-insensitive string comparison. Returns false if
     *     no matching header name is found in the message.
     */
    protected function hasHeader($name)
    {
        return $this->getPsrRequest()->hasHeader(strtolower($name));
    }

    protected function getHeader($name)
    {
        return $this->getPsrRequest()->getHeader($name);
    }

    /***/

    protected function getPostParameters()
    {
        return (array) $this->getPsrRequest()->getParsedBody();
    }

    protected function hasPostParameter($name)
    {
        return in_array($name, array_keys((array) $this->getPostParameters()));
    }

    protected function getPostParameter($name)
    {
        if ($this->hasPostParameter($name)) {
            return ((array) $this->getPostParameters())[$name];
        }
        return false;
    }

    /***/

    protected function getGetParameters()
    {
        return (array) $this->getPsrRequest()->getQueryParams();
    }

    protected function hasGetParameter($name)
    {
        return in_array($name, array_keys((array) $this->getGetParameters()));
    }

    protected function getGetParameter($name)
    {
        if ($this->hasGetParameter($name)) {
            return ((array) $this->getGetParameters())[$name];
        }
        return false;
    }

    /***/

    protected function getAttributesParameters()
    {
        return (array) $this->getPsrRequest()->getAttributes();
    }

    protected function hasAttributeParameter($name)
    {
        return in_array($name, array_keys((array) $this->getAttributesParameters()));
    }

    protected function getAttributeParameter($name)
    {
        if ($this->hasAttributeParameter($name)) {
            return ((array) $this->getAttributesParameters())[$name];
        }
        return false;
    }

    /***/

    protected function getCookieParams()
    {
        return (array) $this->getPsrRequest()->getCookieParams();
    }

    protected function hasCookie($name)
    {
        return isset($this->getCookieParams()[$name]);
    }

    protected function getCookie($name)
    {
        if ($this->hasCookie($name)) {
            return $this->getCookieParams()[$name];
        } else {
            return false;
        }
    }

    /***/

    /**
     * Get the HTTP request method
     *
     * @return string
     */
    protected function getRequestMethod()
    {
        return $this->getPsrRequest()->getMethod();
    }
}