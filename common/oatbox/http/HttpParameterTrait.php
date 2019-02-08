<?php

namespace oat\oatbox\http;

use Psr\Http\Message\ServerRequestInterface;
use tao_helpers_Request;

trait HttpParameterTrait
{
    /**
     * @var ServerRequestInterface
     */
    protected $request;

    /**
     * @return ServerRequestInterface
     */
    protected function getPsrRequest()
    {
        return $this->request;
    }

    public function getRequest()
    {
        return \Context::getInstance()->getRequest();
    }

    /**
     * Checks if a header exists by the given case-insensitive name.
     *
     * @param string $name Case-insensitive header field name.
     * @return bool Returns true if any header names match the given header
     *     name using a case-insensitive string comparison. Returns false if
     *     no matching header name is found in the message.
     */
    public function hasHeader($name)
    {
        return $this->getPsrRequest()->hasHeader(strtolower($name));
    }

    /**
     * Check if the current request is using AJAX
     *
     * @return bool
     */
    protected function isXmlHttpRequest()
    {
        return tao_helpers_Request::isAjax();
    }

    protected function maskAsDeprecatedCall($function = null)
    {
        $message = '[DEPRECATED]  Deprecated call ';
        if (!is_null($function)) {
            $message .= 'of "' . $function . '"';
        }
        $message .= ' (' . get_called_class() .')';
        \common_Logger::w($message);
    }
}