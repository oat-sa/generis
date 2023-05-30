<?php

/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA;
 *
 *
 */

use oat\oatbox\extension\Manifest;

/**
 * the generis autoloader
 *
 * @access public
 * @author Joel Bout <joel@taotesting.com>
 * @package generis
 */
class common_legacy_LegacyAutoLoader
{
    private static $singleton = null;

    /**
     *
     * @return common_legacy_LegacyAutoLoader
     */
    private static function singleton()
    {
        if (self::$singleton == null) {
            self::$singleton = new self();
        }
        return self::$singleton;
    }

    /** @var string[] */
    private $legacyPrefixes = [];

    /** @var string */
    private $generisPath;

    /** @var string */
    private $rootPath;

    /**
     * protect the cunstructer, singleton pattern
     */
    private function __construct()
    {
        $this->generisPath = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR;
        $this->rootPath    = $this->generisPath . '..' . DIRECTORY_SEPARATOR;
    }

    /**
     * Register this instance of ClassLoader as a php autoloader
     *
     * @access public
     * @author Joel Bout <joel@taotesting.com>
     */
    public static function register()
    {
        // init the autloader for generis
        spl_autoload_register([self::singleton(), 'autoload']);
    }

    /**
     * add support for legacy prefix
     */
    public static function supportLegacyPrefix($prefix, $namespace)
    {
        self::singleton()->legacyPrefixes[$prefix] = $namespace;
    }

    /**
     * Attempt to autload classes in tao
     *
     * @access public
     * @author Joel Bout <joel@taotesting.com>
     * @param  string pClassName
     * @return void
     */
    public function autoload($pClassName): void
    {
        if (strpos($pClassName, '_') === false) {
            return;
        }

        $tokens = explode("_", $pClassName);
        $size   = count($tokens);
        $path   = '';
        for ($i = 0; $i < $size - 1; $i++) {
            $path .= $tokens[$i] . '/';
        }

        // Search for class.X.php
        $filePath = '/' . $path . 'class.' . $tokens[$size - 1] . '.php';
        if (file_exists($this->generisPath . $filePath)) {
            require_once $this->generisPath . $filePath;
            return;
        }

        // Search for interface.X.php
        $filePathInterface = '/' . $path . 'interface.' . $tokens[$size - 1] . '.php';
        if (file_exists($this->generisPath . $filePathInterface)) {
            require_once $this->generisPath . $filePathInterface;
            return;
        }

        // Search for trait.X.php
        $filePathTrait = '/' . $path . 'trait.' . $tokens[$size - 1] . '.php';
        if (file_exists($this->generisPath . $filePathTrait)) {
            require_once $this->generisPath . $filePathTrait;
            return;
        }

        if (file_exists($this->rootPath . $filePath)) {
            require_once $this->rootPath . $filePath;
            return;
        }

        if (file_exists($this->rootPath . $filePathInterface)) {
            require_once $this->rootPath . $filePathInterface;
            return;
        }

        foreach ($this->legacyPrefixes as $key => $namespace) {
            if (substr($pClassName, 0, strlen($key)) == $key) {
                $newClass = $namespace . strtr(substr($pClassName, strlen($key)), '_', '\\');
                $this->wrapClass($pClassName, $newClass);
                return;
            }
        }

        $this->loadFromRootExtension($tokens);
    }

    private function loadFromRootExtension(array $classNameTokens): void
    {
        $manifestPath = $this->rootPath . 'manifest.php';

        if (!file_exists($manifestPath)) {
            return;
        }

        $manifest = new Manifest($manifestPath);
        if ($manifest->getName() !== reset($classNameTokens)) {
            return;
        }

        $path = implode(DIRECTORY_SEPARATOR, array_slice($classNameTokens, 1, -1)) . DIRECTORY_SEPARATOR;

        $classFilePath = $this->rootPath . $path . 'class.' . end($classNameTokens) . '.php';
        if (file_exists($classFilePath)) {
            require_once $classFilePath;

            return;
        }

        $interfaceFilePath = $this->rootPath . $path . 'interface.' . end($classNameTokens) . '.php';
        if (file_exists($interfaceFilePath)) {
            require_once $interfaceFilePath;
        }
    }

    private function wrapClass($legacyClass, $realClass)
    {
        common_Logger::w('Legacy classname "' . $legacyClass . '" referenced, please use "' . $realClass . '" instead');
        if (preg_match('/[^A-Za-z0-9_\\\\]/', $legacyClass) || preg_match('/[^A-Za-z0-9_\\\\]/', $realClass)) {
            throw new Exception('Unknown characters in class name');
        }
        $classDefinition = 'class ' . $legacyClass . ' extends ' . $realClass . ' {}';
        eval($classDefinition);
    }
}

// phpcs:disable PSR1.Files.SideEffects
common_legacy_LegacyAutoLoader::register();
// phpcs:enable PSR1.Files.SideEffects
