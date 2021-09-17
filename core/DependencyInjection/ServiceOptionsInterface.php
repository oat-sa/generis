<?php

namespace oat\generis\model\DependencyInjection;

interface ServiceOptionsInterface
{
    /**
     * @return mixed
     */
    public function get(string $serviceId, string $option, $default = null);

    public function save(string $serviceId, string $option, $value): self;

    public function remove(string $serviceId, string $option): self;
}
