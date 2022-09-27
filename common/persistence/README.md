Persistence
=======

## How to connect to persistence?

In case you need to connect to database, there are a few ways:

1) **RECOMMENDED:** Using out-of-box DI container services:

```php
<?php

use oat\generis\persistence\PersistenceServiceProvider;
use Doctrine\DBAL\Query\QueryBuilder;
use Psr\Container\ContainerInterface;

/** @var ContainerInterface $contaner */
$container = $this->getServiceManager()->getContainer();

/** @var common_persistence_SqlPersistence $persistence */
$persistence = $container->get(PersistenceServiceProvider::DEFAULT_SQL_PERSISTENCE);

/** @var common_persistence_sql_Platform $platform */
$platform = $container->get(PersistenceServiceProvider::DEFAULT_SQL_PLATFORM);

/** @var QueryBuilder $queryBuilder */
$queryBuilder = $container->get(PersistenceServiceProvider::DEFAULT_QUERY_BUILDER);
```

So if you need the query builder in you class you can simply do like the example bellow:

````php
<?php

use oat\generis\model\DependencyInjection\ContainerServiceProviderInterface;
use oat\generis\persistence\PersistenceServiceProvider;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

class MyServiceProvider implements ContainerServiceProviderInterface
{
    public function __invoke(ContainerConfigurator $configurator): void
    {
        $services = $configurator->services();

        $services->set(MyClass::class, MyClass::class)
            ->args([service(PersistenceServiceProvider::DEFAULT_QUERY_BUILDER)]);
    }
}

// Your class using the container
class MyClass 
{
    public function __construct(\Doctrine\DBAL\Query\QueryBuilder $queryBuilder) {
    }
}
````

2) **Legacy / NOT RECOMMENDED**: Using PersistenceManager factory method:

```php
<?php

use oat\generis\persistence\PersistenceManager;

/** @var ContainerInterface $contaner */
$container = $this->getServiceManager()->getContainer();

/** @var PersistenceManager $persistence */
$persistenceManager = $container->get(PersistenceManager::SERVICE_ID);

/** @var common_persistence_SqlPersistence $persistence */
$persistence = $persistenceManager->getPersistenceById('default');

/** @var common_persistence_sql_Platform $platform */
$platform = $persistence->getPlatform();

/** @var QueryBuilder $queryBuilder */
$queryBuilder = $platform->getQueryBuilder();
```

## Adding another persistence to the container

In case you need to access other persistence than the default one, 
please consider extending the [PersistenceServiceProvider](./PersistenceServiceProvider.php).