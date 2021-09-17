# Dependency Injection

## How to add new services to container?

1) In the extension, create a `Service Provider`. Example: 

```php
<?php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use function Symfony\Component\DependencyInjection\Loader\Configurator\env;
use function Symfony\Component\DependencyInjection\Loader\Configurator\param;

class MyContainerServiceProvider implements ContainerServiceProviderInterface
{
    public function __invoke(ContainerConfigurator $configurator): void
    {
        $services = $configurator->services();
        $parameters = $configurator->parameters();
        
        $parameters->set('someParam', 'someValue');

        $services->set(MyService::class, MyService::class)->args(
            [
                service(MyOtherService::class),
                service(MyLegacyService::SERVICE_ID),
                env('MY_ENV_VAR'),
                param('someParam'),
            ]
        );
    }
}
```

**RECOMMENDATION:** Avoid inflate the `ContainerServiceProvider` with too many services/params/etc. 
Be wise and use common sense to group your services within different `Service Providers` classes.  

For more information read the [Symfony Dependency Injection documentation](https://symfony.com/doc/current/components/dependency_injection.html).

2) Add the new `Service Provider` to the `manifest.php` file of the extension.

```php
<?php
return [
    // other manifest configs
    'containerServiceProviders' => [
        MyContainerServiceProvider::class
    ]
];
```

## How is the container started?

To start the container, we need to use the ContainerBuilder. Example:

```php
$container = (new oat\generis\model\DependencyInjection\ContainerBuilder(
    CONFIG_PATH, // TAO config path
    GENERIS_CACHE_PATH . '/_di/container.php', // Container cache file
    oat\oatbox\service\ServiceManager::getServiceManager()->get(common_ext_ExtensionsManager::SERVICE_ID), //ExtensionsManager
))->build();
```

Notice that this is already on `oat\oatbox\service\ServiceManager->getContainer()`. So you do not need to do it. 

## Accessing the container inside a legacy controller

You just need to use this method `$this->getPsrContainer()`.

```php
use oat\tao\model\http\Controller;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

class SomeController extends Controller implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    public function someMethod(): void
    {
        $service = $this->getPsrContainer()->get(MyService::class);
        // Other logic...
    }
}
```

## Using legacy configuration on new services

**ATTENTION!** It is **NOT RECOMMENDED** to use the `ServiceOptions` class. New services on 
container should rely on **ENVIRONMENT VARIABLES**, but when this is _really not possible_ 
and ObjectOriented techniques such as Proxy, Factory, Strategy, etc cannot solve the
issue, then _maybe_ you should consider using it.

1) Registering new configs

In a new _migrations file_ or _installation script_, register the new options. Example:

```php
use oat\generis\model\DependencyInjection\ServiceOptions;

$serviceOptions = $serviceLocator->get(ServiceOptions::SERVICE_ID);
$serviceOptions->save(MyService::class, 'foo', 'bar');

$serviceLocator->register(ServiceOptions::SERVICE_ID, $serviceOptions);
```

2) Inject the `ServiceOptions` in your service through the ServiceProvider:

```php
<?php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use oat\generis\model\DependencyInjection\ServiceOptions;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

class MyContainerServiceProvider implements ContainerServiceProviderInterface
{
    public function __invoke(ContainerConfigurator $configurator): void
    {
        $configurator->services()->set(MyService::class, MyService::class)->args(
            [
                service(ServiceOptions::SERVICE_ID)
            ]
        );
    }
}
```

3) Call it inside the new service like:

```php
use oat\generis\model\DependencyInjection\ServiceOptionsInterface;

class MyService
{
    /** @var ServiceOptionsInterface */
    private $options;

    public function __construct(ServiceOptionsInterface $options)
    {
        $this->options = $options;
    }

    public function foo()
    {
        $bar = $this->options->get(self::class, 'foo'); // Will get "bar" as response
    }
}
```

## Avoid caching / Debug mode

To avoid container caching (useful on dev mode), please add the following variable on your `.env` file.

```shell
DI_CONTAINER_DEBUG=true
DI_CONTAINER_FORCE_BUILD=true
```
