<?php

namespace oat\generis\Model\Config;

use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

/**
 * Class used to initialize configuration files based on a given file name
 *
 * @author Martijn Swinkels <m.swinkels@taotesting.com>
 */
abstract class AbstractInitializer
{

    const CONFIG_DIR       = 'config';
    const CONFIG_CACHE_DIR = 'config' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR;
    const CACHE_LIFETIME   = 18000;

    /**
     * @var string
     */
    private $fileName;

    /**
     * @var ConfigCache
     */
    private $configCache;

    /**
     * @var string
     */
    private $rootPath;

    /**
     * @var string[]
     */
    private static $excludedPaths = [
        'bin', 'vendor', 'composer', 'config', 'data', 'tests'
    ];

    /**
     * AbstractInitializer constructor.
     *
     * @param string $fileName
     */
    public function __construct($fileName)
    {
        $rootPathParts = explode(DIRECTORY_SEPARATOR, __DIR__);
        $rootPathParts = array_splice($rootPathParts, 0, -3);
        $this->rootPath = implode(DIRECTORY_SEPARATOR, $rootPathParts) . DIRECTORY_SEPARATOR;
        $this->fileName = $fileName;
        $this->configCache = new ConfigCache($this->rootPath . self::CONFIG_CACHE_DIR . $this->fileName, false);
    }


    /**
     * Read configuration for the project
     *
     * @param bool $rebuild
     * @return array
     */
    public function initialize($rebuild = false)
    {
        if (!$rebuild && $this->configCache->isFresh() && !$this->isCacheExpired()) {
            return Yaml::parseFile($this->rootPath . self::CONFIG_CACHE_DIR . $this->fileName);
        }

        $configPaths = $this->getConfigPaths();
        $fileLocator = new FileLocator($configPaths);
        $fileList = $fileLocator->locate(self::CONFIG_DIR . DIRECTORY_SEPARATOR . $this->fileName, null, false);

        if (!count($fileList)) {
            return [];
        }

        $config = [];
        foreach ($fileList as $file) {
            try {
                $config = array_merge_recursive($config, Yaml::parseFile($file));
            } catch (ParseException $e) {
                throw new ParseException(
                    sprintf(
                        'The Yaml config in file "%s" is invalid:' . "\n%s\nVerify the files contents and try again.",
                        $file, $e->getMessage()
                    )
                );
            }
        }

        $this->configCache->write(Yaml::dump($config));

        return $config;
    }

    /**
     * @return string[]
     */
    private function getConfigPaths()
    {
        $configPaths = array_filter(glob('*'), 'is_dir');

        foreach ($configPaths as $key => $configPath) {
            if (in_array($configPath, self::$excludedPaths, true)) {
                unset($configPaths[$key]);
                continue;
            }
            $configPaths[$key] = $this->rootPath . $configPath;
        }

        return $configPaths;
    }

    /**
     * Check if the cached config file is expired
     *
     * @return bool
     */
    private function isCacheExpired()
    {
        $lastModificationTime = filemtime($this->rootPath . self::CONFIG_CACHE_DIR . $this->fileName);

        return time() > $lastModificationTime + self::CACHE_LIFETIME;
    }
}