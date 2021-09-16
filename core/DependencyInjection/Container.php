<?php

namespace oat\generis\model\DependencyInjection;

use Psr\Container\ContainerInterface;

class Container implements ContainerInterface
{
    /** @var ContainerInterface */
    private $container;

    /**
     * @inheritDoc
     */
    public function get(string $id)
    {
        return $this->getContainer()->get($id);
    }

    /**
     * @inheritDoc
     */
    public function has(string $id): bool
    {
        return $this->getContainer()->has($id);
    }

    private function getContainer(): ContainerInterface
    {
        if (!$this->container) {
            $this->container = (new ContainerBuilder())->build();
        }

        return $this->container;
    }
}
