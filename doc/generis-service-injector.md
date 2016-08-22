# TAO Service injector

## About

TAO Service Injector is th way to integrate any dependencies container.
It must implement Container interop interface.
TAO provide configuration for generis Service Manager and Zend Framework Service Locator.
As well it's possible to integrate others libraries.

## Usage : 

Service injector is acccessible form controller using getServiceInjector since tao-core 7.5.0 and generis 3.0.0.

Sercice injector implement [container interop interface] [containerinterop]. 

example : in your controler action 

```php
if($this->getServiceInjector()->has('myService')) {
    $myService = $this->getServiceInjector()->get('myService');
}
```

### get service injector in your objects :

you must implement  \oat\oatbox\service\ServiceInjectorAwareInterface and use \oat\oatbox\service\ServiceInjectorAwareTrait (or implement interface methods) AND instanciate by the service injector.

## Add your own dependencies using Zend ServiceLocator : 
### for a new extension : 

An helper is available to overload tao configuration : 

1. Create your install script :

put it in scripts/install.
```php
namespace myVendor\myExtension\scripts\install;

class ServiceInjectorInstaller extends \common_ext_action_InstallAction {
    /**
    * set up a new service injector configuration
    */
    public function __invoke($params) {
        $this->setServiceInjectorConfig(
                [
                        \oat\oatbox\service\factory\ZendServiceManager::class =>
                            [
                                //my Zf2 config
                            ],
                    ]
        );
    }
}
```

see [Service Locator Usage](https://framework.zend.com/manual/2.4/en/modules/zend.service-manager.quick-start.html)

2. Add your script in your manifest :  open manifest.php on your extension root directory

example : 

```php
return [
    'name' => 'MyTAOExtension',
    'label' => 'my extension',
    'description' => 'my extension description',
    'license' => 'GPL-2.0',
    'version' => '1.0.0',
    'author' => 'my company',
    'requires' => [],
    'install' => [
        'php' => 
        [
            \oat\myExtension\scripts\install\ServiceInjectorInstaller::class,
        ]
    ]
]
```

### Add your your favorite container :

1. Create a service factory : 

```php

namespace myVendor\myExtension\model;

class MyContainerFactory implements FactoryInterface 
{
    public function __invoke(array $config) {
        return new \myVendor\myExtension\model\MyContainer($config);
    }
}

```
2. add it to your configuration :
In your install script : 

```php

$this->setServiceInjectorConfig(
                [
                        \myVendor\myExtension\model\MyContainerFactory::class =>
                            [
                                //my Config
                            ],
                    ]
        );

```
reminder : your container must implement [container interop interface] [containerinterop]
### Update an existing extension :

In your update class :

```php

$injectorConfig = [
                        \oat\oatbox\service\factory\ZendServiceManager::class => [
                            // my config
                        ]
];

$injector = $this->getServiceManager()->get(ServiceInjectorRegistry::SERVICE_ID);
                $injector->overLoad(
                    $injectorConfig
                );

```
### Over load an other extension :

is it possible to overload an other extension configuration : 

example : 

MyExtension 1 config : 

```php
 [
                        \myVendor\myExtension\model\MyContainerFactory::class =>
                            [
                                'alias' => 
                                [
                                    'model1' => \myVendor\myExtension\model\Model1
                                ]
                            ],
                    ]
```

My Extension 2 config

```php
 [
                        \myVendor\myExtension\model\MyContainerFactory::class =>
                            [
                                'alias' => 
                                [
                                    'model1' => \myVendor\myNewExtension\model\OverLoadModel1,
                                    'model2' => \myVendor\myNewExtension\model\Model2,
                                ]
                            ],
                    ]
```

[containerinterop]: <https://github.com/container-interop/container-interop>
