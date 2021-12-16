# Dependency Injection

We are **deprecating** the current service registry (`oat\oatbox\service\ServiceManager`) in favor of a 
faster and more modern [PSR-11](https://www.php-fig.org/psr/psr-11/) compliant solution based on
[Symfony Dependency Injection documentation](https://symfony.com/doc/current/components/dependency_injection.html).

- For now on, all the new services MUST be created as explained bellow.
- Whenever is possible, old services SHOULD be migrated to the new container.

## How to add new services to container?

1) In _any_ tao extension, create a `Container Service Provider`. Example: 

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

        $services->set(MyService::class, MyService::class)
            ->public()
            ->args(
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

**RECOMMENDATION:** Avoid inflating the `ContainerServiceProvider` with too many services/params/etc. 
Be wise and use common sense to group your services within different `Container Service Providers` classes.  

For more information read the [Symfony Dependency Injection documentation](https://symfony.com/doc/current/components/dependency_injection.html).

2) Add the new `Container Service Provider` to the `manifest.php` file of the extension.

```php
<?php
return [
    // other manifest configs
    'containerServiceProviders' => [
        MyContainerServiceProvider::class
    ]
];
```
## Actions/Controllers and DI container

You can find the documentation in **tao-core extension** on 
the [routing section](https://github.com/oat-sa/tao-core/tree/master/models/classes/routing).

## How is the container started?

To start the container, we need to use the ContainerBuilder. Example:

```php
use oat\oatbox\service\ServiceManager;
use oat\generis\model\DependencyInjection\ContainerStarter;

$container = (new ContainerStarter(ServiceManager::getServiceManager()))->getContainer();
```
**IMPORTANT**: This is already done on `ServiceManager->getContainer()`, so you do not need to do it.

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

## Warming up container cache

The cache warmup happens when we run `taoUpdate.php`, but if you need to warmup the cache only, run:

````shell
php index.php 'oat\generis\scripts\tools\ContainerCacheWarmup'
````

## Avoid caching / Debug mode

To avoid container caching (useful on dev mode), please add the following variable on your `.env` file.

```shell
DI_CONTAINER_DEBUG=true
```

## Testing container inside Legacy Services (ConfigurableService)

While in a legacy class (extending ConfigurableService) and calling the container inside, 
you can mock the services with `getServiceLocatorMock` (The same way we do for `ServiceManager`). Example: 

Having a class:

```php
use oat\oatbox\service\ConfigurableService;
use oat\generis\persistence\PersistenceManager;

class MyLegacyServiceTest extends ConfigurableService
{
    public function something()
    {
        return get_class($this->getServiceLocator()->getContainer()->get(PersistenceManager::SERVICE_ID));
    }
}
```

In your test:

```php
use oat\generis\persistence\PersistenceManager;
use oat\generis\test\TestCase;

class MyLegacyServiceTest extends TestCase
{
    public function testSomething(): void
    {
        $sut = new MyLegacyService();
        $sut->setServiceLocator(
            $this->getServiceLocatorMock(
                [
                    PersistenceManager::SERVICE_ID => $this->createMock(PersistenceManager::class),
                ]
            )
        );
        
        echo $sut->something(); // Will return the $persistenceManager class
    }
}
```
