# Dependency Injection

## How to add new services to container?

1) In the extension, create a `Service Provider`. Example: 

```php
<?php
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

class MyContainerServiceProvider implements ContainerServiceProviderInterface
{
    public function __invoke(ContainerConfigurator $configurator): void
    {
        $services = $configurator->services();

        $services->set(MyService::class, MyService::class)
            ->args(
            [
                service(MyOtherService::class),
                service(MyLegacyService::SERVICE_ID),
            ]
        );
    }
}
```

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
    ServiceManager::getServiceManager()->get(common_ext_ExtensionsManager::SERVICE_ID), //ExtensionsManager
))->build();
```

Notice that this is already on `ServiceManager->getContainer()`. So you do not need to do it. 

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

## Avoid caching / Debug mode

To avoid container caching (useful on dev mode), please add the following variable on your `.env` file.

```shell
DI_CONTAINER_DEBUG=true
DI_CONTAINER_FORCE_BUILD=true
```
