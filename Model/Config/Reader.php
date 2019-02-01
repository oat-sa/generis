<?php

namespace oat\generis\Model\Config;

/**
 * Reader used to get values from config.
 *
 * @author Martijn Swinkels <m.swinkels@taotesting.com>
 */
class Reader
{

    /**
     * @var array
     */
    private $config;

    /**
     * Read configuration for the project
     *
     * @return array
     */
    private function getConfig()
    {
        if ($this->config === null) {
            $initializer = new Initialize();
            $this->config = $initializer->initialize();
        }

        return $this->config;
    }

    /**
     * Get a configuration.
     *
     * @param string $path
     * @return mixed|null
     */
    public function get($path)
    {
        $config = $this->getConfig();
        $keys = explode('/', $path);
        $configValue = null;

        foreach ($keys as $key) {
            $configValue = $config;
            if (!isset($configValue[$key])) {
                return null;
            }

            $configValue = $configValue[$key];
        }

        return $configValue;
    }
}