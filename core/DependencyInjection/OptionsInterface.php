<?php

namespace oat\generis\model\DependencyInjection;

interface OptionsInterface
{
    public function getOptions(): array;

    /**
     * @return mixed
     */
    public function getOption(string $key, $default = null);

    public function setOptions(array $options): self;

    public function addOption(string $key, $value): self;

    public function removeOption(string $key): self;
}
