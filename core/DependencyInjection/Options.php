<?php

namespace oat\generis\model\DependencyInjection;

class Options implements OptionsInterface
{
    /** @var mixed[] */
    private $options;

    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @inheritDoc
     */
    public function getOption(string $key, $default = null)
    {
        return $this->options[$key] ?? $default;
    }

    public function setOptions(array $options): OptionsInterface
    {
        $this->options = $options;

        return $this;
    }

    public function addOption(string $key, $value): OptionsInterface
    {
        $this->options[$key] = $value;

        return $this;
    }

    public function removeOption(string $key): OptionsInterface
    {
        unset($this->options[$key]);

        return $this;
    }
}
