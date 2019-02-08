<?php

namespace oat\oatbox\http;

use Context;
use GuzzleHttp\Psr7\Uri;
use HTTPToolkit;
use InterruptedActionException;
use oat\tao\model\routing\ActionEnforcer;
use oat\tao\model\routing\Resolver;
use Psr\Http\Message\ServerRequestInterface;

trait HttpFlowTrait
{
    /**
     * @return ServerRequestInterface
     */
    abstract public function getPsrRequest();

    /**
     * Redirect using the TAO FlowController implementation
     * @see {@link oat\model\routing\FlowController}
     * @param string $url
     * @param int $statusCode
     * @throws InterruptedActionException
     */
    public function redirect($url, $statusCode = 302)
    {
        $context = Context::getInstance();

        header(HTTPToolkit::statusCodeHeader($statusCode));
        header(HTTPToolkit::locationHeader($url));

        throw new InterruptedActionException(
            'Interrupted action after a redirection',
            $context->getModuleName(),
            $context->getActionName()
        );
    }

    /**
     * Forward the action to execute reqarding a URL
     * The forward runs into tha same HTTP request unlike redirect.
     * @param string $url the url to forward to
     * @throws \common_exception_InvalidArgumentType
     * @throws InterruptedActionException
     */
    public function forwardUrl($url)
    {
        $uri = new Uri($url);
        $query = $uri->getQuery();
        $queryParams = [];
        if (strlen($query) > 0) {
            parse_str($query, $queryParams);
        }

        switch ($this->getPsrRequest()->getMethod()) {
            case 'GET' :
                $params = $this->getPsrRequest()->getQueryParams();
                break;
            case 'POST' :
                $params = $this->getPsrRequest()->getParsedBody();
                break;
            default:
                $params = [];
        }
        $request = $this->getPsrRequest()
            ->withUri($uri)
            ->withQueryParams((array) $queryParams);

        //resolve the given URL for routing
        $resolver = $this->propagate(new Resolver($request));

        //update the context to the new route
        $context = \Context::getInstance();
        $context->setExtensionName($resolver->getExtensionId());
        $context->setModuleName($resolver->getControllerShortName());
        $context->setActionName($resolver->getMethodName());

        $request = $request
            ->withAttribute('extension', $resolver->getExtensionId())
            ->withAttribute('controller', $resolver->getControllerShortName())
            ->withAttribute('method', $resolver->getMethodName());

        //execute the new action
        $enforcer = new ActionEnforcer(
            $resolver->getExtensionId(),
            $resolver->getControllerClass(),
            $resolver->getMethodName(),
            $params
        );
        $this->propagate($enforcer);

        $enforcer(
            $request,
            $this->response->withHeader(
                'X-Tao-Forward',
                $resolver->getExtensionId() . '/' .  $resolver->getControllerShortName() . '/' . $resolver->getMethodName()
            )
        );

        throw new InterruptedActionException(
            'Interrupted action after a forwardUrl',
            $request->getAttribute('module')->getModuleName(),
            $request->getAttribute('action')->getModuleName(),
            $context->getActionName()
        );
    }

    /**
     * Forward routing.

     * @param string $action the name of the new action
     * @param string $controller the name of the new controller/module
     * @param string $extension the name of the new extension
     * @param array $params additional parameters
     */
    public function forward($action, $controller = null, $extension = null, $params = array())
    {
        //as we use a route resolver, it's easier to rebuild the URL to resolve it
        $this->forwardUrl(\tao_helpers_Uri::url($action, $controller, $extension, $params));
    }
}