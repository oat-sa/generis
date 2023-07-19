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
 * Copyright (c) 2020 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */

declare(strict_types=1);

namespace oat\oatbox\extension;

use oat\generis\model\DependencyInjection\ContainerServiceProviderInterface;
use oat\oatbox\extension\exception\ManifestException;
use oat\oatbox\extension\exception\ManifestNotFoundException;
use oat\oatbox\service\exception\InvalidServiceManagerException;
use oat\tao\model\Middleware\Contract\MiddlewareMapInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Composer\InstalledVersions;

/**
 * Class Manifest
 * @author Jerome Bogaerts <jerome@taotesting.com>
 * @package oat\oatbox\extension
 */
class Manifest implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * The path to the file where the manifest is described.
     * @var string
     */
    private $filePath = '';

    /**
     * The version of the Extension the manifest describes.
     * @var string
     */
    private $version = null;

    /**
     * The dependencies of the Extension the manifest describes.
     * @var array
     */
    private $dependencies = [];

    /**
     * @var array
     */
    private $manifest = [];

    /**
     * @var ComposerInfo
     */
    private $composerInfo = null;

    /**
     * Creates a new instance of Manifest.
     *
     * @access public
     * @param string $filePath The path to the manifest.php file to parse.
     * @param ComposerInfo|null $composerInfo
     * @throws ManifestNotFoundException
     * @throws exception\MalformedManifestException
     */
    public function __construct($filePath, ComposerInfo $composerInfo = null)
    {
        // the file exists, we can refer to the $filePath.
        if (!is_readable($filePath)) {
            throw new ManifestNotFoundException(
                "The Extension Manifest file located at '${filePath}' could not be read."
            );
        }
        $this->manifest = require($filePath);
        // mandatory
        if (empty($this->manifest['name'])) {
            throw new exception\MalformedManifestException(
                "The 'name' component is mandatory in manifest located at '{$this->filePath}'."
            );
        }
        $this->composerInfo = $composerInfo;
        $this->filePath = $filePath;
    }

    /**
     * Get the name of the Extension the manifest describes.
     * @return string
     */
    public function getName(): string
    {
        return isset($this->manifest['name']) ? $this->manifest['name'] : '';
    }

    /**
     * Get the description of the Extension the manifest describes.
     * @return string
     */
    public function getDescription(): string
    {
        return isset($this->manifest['description']) ? $this->manifest['description'] : '';
    }

    /**
     * Get the author of the Extension the manifest describes.
     * @return string
     */
    public function getAuthor(): string
    {
        return isset($this->manifest['author']) ? $this->manifest['author'] : '';
    }

    /**
     * Get the human readable label of the Extension the manifest describes.
     * @return string
     */
    public function getLabel(): string
    {
        return isset($this->manifest['label']) ? $this->manifest['label'] : '';
    }

    /**
     * Returns the Access Control Layer table
     * @return array
     */
    public function getAclTable(): array
    {
        return isset($this->manifest['acl']) ? $this->manifest['acl'] : [];
    }

    /**
     * Get the version of the Extension the manifest describes.
     * @return string
     * @throws ManifestException
     */
    public function getVersion(): string
    {
        if ($this->version === null) {
            $packageId = $this->getComposerInfo()->getPackageId();
            $this->version = InstalledVersions::getVersion($packageId);
        }
        return (string) $this->version;
    }

    /**
     * Get the dependencies of the Extension the manifest describes.
     * The content of the array are extensionIDs, represented as strings.
     * @return array
     * @throws ManifestException
     * @throws InvalidServiceManagerException
     */
    public function getDependencies(): array
    {
        if (empty($this->dependencies)) {
            $this->dependencies = $this->getComposerInfo()->extractExtensionDependencies();
            //backward compatibility with old requirements declaration
            //todo: remove after dependencies of all Tao extensions be moved from manifest to composer.json
            if (isset($this->manifest['requires'])) {
                $this->dependencies = array_merge($this->dependencies, $this->manifest['requires']);
            }
        }
        return $this->dependencies;
    }

    /**
     * returns an array of RDF files to import during install.
     * The returned array contains paths to the files to be imported.
     * @return array
     */
    public function getInstallModelFiles(): array
    {
        if (!isset($this->manifest['install']['rdf'])) {
            return [];
        }
        $files = is_array($this->manifest['install']['rdf']) ?
            $this->manifest['install']['rdf'] :
            [$this->manifest['install']['rdf']];
        $files = array_filter($files);
        return (array) $files;
    }

    /**
     * Get a list of PHP files to be executed at installation time.
     * The returned array contains absolute paths to the files to execute.
     * @return array
     */
    public function getInstallPHPFiles(): array
    {
        $result = [];
        if (isset($this->manifest['install']['php'])) {
            $result = is_array($this->manifest['install']['php'])
                ? $this->manifest['install']['php']
                : [$this->manifest['install']['php']];
        }
        return $result;
    }

    /**
     * Return the uninstall data as an array if present, or null if not
     * @return mixed
     */
    public function getUninstallData()
    {
        return isset($this->manifest['uninstall']) ? $this->manifest['uninstall'] : null;
    }

    /**
     * Return the className of the updateHandler
     * @return string
     */
    public function getUpdateHandler()
    {
        return isset($this->manifest['update']) ? $this->manifest['update'] : null;
    }

    /**
      * PHP scripts to execute in order to add some sample data to an install
      * @return array
      */
    public function getLocalData()
    {
        return isset($this->manifest['local']) ? $this->manifest['local'] : [];
    }

    /**
     * Gets the controller routes of this extension.
     * @return array
     */
    public function getRoutes()
    {
        return isset($this->manifest['routes']) ? $this->manifest['routes'] : [];
    }

    /**
     * Get an array of constants to be defined where array keys are constant names
     * and values are the values of these constants.
     * @return array
     */
    public function getConstants(): array
    {
        return isset($this->manifest['constants']) && is_array($this->manifest['constants'])
            ? $this->manifest['constants'] : [];
    }

    /**
     * Get the array with unformatted extra data
     * @return array
     */
    public function getExtra()
    {
        return isset($this->manifest['extra']) ? $this->manifest['extra'] : [];
    }

    /**
     * @return string[] Array with ContainerServiceProviderInterface FQNs
     */
    public function getContainerServiceProvider(): array
    {
        return $this->manifest['containerServiceProviders'] ?? [];
    }

    /**
     * @return string[] Array with MiddlewareMapInterface FQNs
     */
    public function getMiddlewares(): array
    {
        return $this->manifest['middlewares'] ?? [];
    }

    /**
     * @return AbstractAction[]
     */
    public function getE2ePrerequisiteActions(): array
    {
        return $this->manifest['e2ePrerequisiteActions'] ?? [];
    }

    /**
     * Extract dependencies for extensions
     * @param string $file
     * @return array
     * @throws \common_ext_ExtensionException
     */
    public static function extractDependencies(string $file)
    {
        $file = realpath($file);
        $composer = new ComposerInfo(dirname($file));
        return array_keys($composer->extractExtensionDependencies());
    }

    /**
     * Extract checks from a given manifest file.
     * @param $file string The path to a manifest.php file.
     * @return \common_configuration_ComponentCollection
     * @throws ManifestNotFoundException
     */
    public static function extractChecks(string $file)
    {
        // the file exists, we can refer to the $filePath.
        if (!is_readable($file)) {
            throw new ManifestNotFoundException(
                sprintf('The Extension Manifest file located at %s could not be read.', $file)
            );
        }

        $manifest = require($file);
        $returnValue = $manifest['install']['checks'] ?? [];
        foreach ($returnValue as &$component) {
            if (strpos($component['type'], 'FileSystemComponent') !== false) {
                $root = realpath(dirname(__FILE__) . '/../../../../');
                $component['value']['location'] = $root . '/' . $component['value']['location'];
            }
        }

        return $returnValue;
    }

    /**
     * Get the Role dedicated to manage this extension. Returns null if there is
     * @return string
     */
    public function getManagementRoleUri()
    {
        return isset($this->manifest['managementRole']) ? $this->manifest['managementRole'] : '';
    }

    /**
     * @return ComposerInfo
     * @throws \common_ext_ExtensionException
     */
    private function getComposerInfo()
    {
        if ($this->composerInfo === null) {
            $this->composerInfo = new ComposerInfo(dirname($this->filePath));
        }
        return $this->composerInfo;
    }
}
