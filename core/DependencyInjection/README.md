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

This needs to be added in the application bootstrap.

```php
$container = (new oat\generis\model\DependencyInjection\ContainerBuilder(
    CONFIG_PATH, // TAO config path
    GENERIS_CACHE_PATH . '/_di/container.php', // Container cache file
    true // Debug mode - no cache
))->build();
```