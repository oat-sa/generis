<?php

namespace oat\generis\Model\Extension;

use LogicException;

/**
 * Class Registration
 *
 * @author Martijn Swinkels <m.swinkels@taotesting.com>
 */
class Registrar
{

    /**
     * @var string[]
     */
    private static $paths;

    /**
     * @param string $extension
     * @param string $path
     */
    public static function register($extension, $path)
    {
        if (isset(self::$paths[$extension])) {
            throw new LogicException('Extension path for extension "' . $extension . '" is already registered.');
        }
        self::$paths[$extension] = $path;
    }

    /**
     * Get all extension paths.
     *
     * @return string[]
     */
    public function getPaths()
    {
        return self::$paths;
    }

    /**
     * Get the path of an extension
     *
     * @param string $extension
     * @return string
     */
    public function getPath($extension)
    {
        if (!isset(self::$paths[$extension])) {
            throw new LogicException('Registration for the extension "' . $extension . '" can not be found.');
        }
        return self::$paths[$extension];
    }
}