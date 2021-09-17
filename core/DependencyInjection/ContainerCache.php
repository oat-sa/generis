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
        bool $isDebugEnabled = null,
        string $cachedContainerClassName = null
    ) {
        $this->cacheFile = $cacheFile;
        $this->builder = $builder;
        $this->configCache = $configCache ?? new ConfigCache(
            $this->cacheFile,
            $isDebugEnabled ?? $this->isEnvVarTrue('DI_CONTAINER_DEBUG')
        );

        $this->dumper = $dumper;
        $this->cachedContainerClassName = $cachedContainerClassName ?? 'MyCachedContainer';
    }

    public function isFresh(): bool
    {
        return !$this->isEnvVarTrue('DI_CONTAINER_FORCE_BUILD') && $this->configCache->isFresh();
    }

    public function load(): ContainerInterface
    {
        if (!$this->isFresh()) {
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

    private function isEnvVarTrue(string $envVar): bool
    {
        if (!isset($_ENV[$envVar])) {
            return false;
        }

        return filter_var($_ENV[$envVar], FILTER_VALIDATE_BOOLEAN) ?? false;
    }

    private function getDumper(): PhpDumper
    {
        return $this->dumper ?? new PhpDumper($this->builder);
    }
}
