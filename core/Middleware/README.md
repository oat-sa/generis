# Middleware

The goal of this feature is to **intercept requests** with Middlewares and 
handle them before reaching your controller with a **chain of responsibility pattern**.

The middleware implementation is complient with [PSR-15](https://www.php-fig.org/psr/psr-15/).

### How to create a new Middleware?

Create a `Middleware` class as bellow and add it to the DI container.

```php
<?php

declare(strict_types=1);

namespace oat\{myExtension}\model\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class MyMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        //... Do something
        return $handler->handle($request);
    }
}
```

### How to map the Middleware to routes?

1) Create a `middleware configuration` as showed bellow:

The config MUST be added under the following namespace `oat\{myExtension}\controller\Middleware`.

```php
<?php
declare(strict_types=1);

namespace oat\{myExtension}\controller\Middleware;

use oat\generis\model\Middleware\MiddlewareMap;
use oat\generis\model\Middleware\MiddlewareConfigInterface;
use oat\tao\model\Middleware\OpenAPISchemaValidateRequestMiddleware;

class MiddlewareConfig implements MiddlewareConfigInterface
{
    public function __invoke(): array
    {
        return [
            MiddlewareMap::byRoute('/some/path/foo')
                ->andHttpMethod('POST')
                ->andHttpMethod('GET')
                ->andMiddlewareId(MyMiddleware::class)
                ->andMiddlewareId(MyOtherMiddleware::class)
            ),
            MiddlewareMap::byRoutes(['/some/path/foo', '/some/path/bar'])
                ->andHttpMethod('GET')
                ->andMiddlewareId(MyMiddleware::class)
                ->andMiddlewareId(MyOtherMiddleware::class)
            ),
            MiddlewareMap::byMiddlewareId(MyMiddleware::class)
                ->andRoute('/some/path/foo')
                ->andRoute('/some/path/bar')
                ->andHttpMethod('GET')
            ),
            MiddlewareMap::byMiddlewareIds([MyMiddleware::class, MyOtherMiddleware::class])
                ->andRoute('/some/path/foo')
                ->andHttpMethod('GET')
            ),
        ];
    }
}
```

2) Then add it to your `manifest.php`.

```php
<?php
return [
    // above, other manifest data...
    'middlewares' => [
        oat\{myExtension}\controller\Middleware\MyMiddleware::class,
    ]
];
```

## TODO

- [ ] Add support for dynamic routes like `/path/{id:[0-9]}/something`
- [ ] Add possibility to set oder of execution of middlewares.