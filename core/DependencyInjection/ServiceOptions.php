<?php

namespace oat\generis\model\DependencyInjection;

use oat\oatbox\service\ConfigurableService;

/**
 * @notice It is NOT RECOMMENDED to use this class. New services on container should rely on ENVIRONMENT VARIABLES,
 *         but when this is really not possible and OO techniques like, Proxy, Factory, Strategy cannot solve the
 *         issue than MAYBE this class can be used.
 */
final class ServiceOptions extends ConfigurableService implements ServiceOptionsInterface
{
    public const SERVICE_ID = 'generis/ServiceOptions';

    public function save(string $serviceId, string $option, $value): ServiceOptionsInterface
    {
        $mainOption = parent::getOption($serviceId, []);
        $mainOption[$option] = $value;

        parent::setOption($serviceId, $mainOption);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function get(string $serviceId, string $option, $default = null)
    {
        return parent::getOption($serviceId, [])[$option] ?? $default;
    }

    public function remove(string $serviceId, string $option): ServiceOptionsInterface
    {
        $allOptions = parent::getOption($serviceId, []);

        if (isset($allOptions[$option])) {
            unset($allOptions[$option]);
        }

        parent::setOption($serviceId, $allOptions);

        return $this;
    }
}
