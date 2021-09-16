<?php

namespace oat\generis\model\DependencyInjection;

use Psr\Container\ContainerInterface;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\DependencyInjection\Container as SymfonyContainer;
use Symfony\Component\DependencyInjection\ContainerBuilder as SymfonyContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;

class ContainerCache
{
    /** @var string */
    private $cacheFile;

    /** @var ConfigCache */
    private $configCache;

    /** @var SymfonyContainerBuilder */
    private $builder;

    /** @var PhpDumper */
    private $dumper;

    /** @var string */
    private $cachedContainerClassName;

    public function __construct(
        string $cacheFile,
        SymfonyContainerBuilder $builder,
        ConfigCache $configCache = null,
        PhpDumper $dumper = null,
        bool $isDebugMode = null,
        string $cachedContainerClassName = null
    ) {
        $this->cacheFile = $cacheFile;
        $this->configCache = $configCache ?? new ConfigCache($this->cacheFile, $isDebugMode ?? false);
        $this->builder = $builder;
        $this->dumper = $dumper;
        $this->cachedContainerClassName = $cachedContainerClassName ?? 'MyCachedContainer';
    }

    public function load(): ContainerInterface
    {
        if (!$this->configCache->isFresh()) {
            $this->storeCache();
        }

        require_once $this->cacheFile;

        return new $this->cachedContainerClassName();
    }

    private function storeCache(): void
    {
        $this->builder->compile();

        $this->configCache->write(
            $this->getDumper()->dump(
                [
                    'class' => $this->cachedContainerClassName,
                    'base_class' => SymfonyContainer::class
                ]
            ),
            $this->builder->getResources()
        );
    }

    private function getDumper(): PhpDumper
    {
        return $this->dumper ?? new PhpDumper($this->builder);
    }
}
