<?php

declare(strict_types=1);

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

namespace oat\oatbox\extension;

use oat\oatbox\extension\exception\ManifestException;
use oat\oatbox\extension\exception\ManifestNotFoundException;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

/**
 * Class Manifest
 * @package oat\oatbox\extension
 */
class Manifest implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * The path to the file where the manifest is described.
     *
     * @access private
     * @var string
     */
    private $filePath = '';

    /**
     * The name of the Extension the manifest describes.
     *
     * @access private
     * @var string
     */
    private $name = '';

    /**
     * The human readable name of the extension
     *
     * @access private
     * @var string
     */
    private $label = '';
    
    /**
     * The description of the Extension the manifest describes.
     *
     * @access private
     * @var string
     */
    private $description = '';

    /**
     * The author of the Extension the manifest describes.
     *
     * @access private
     * @var string
     */
    private $author = '';

    /**
     * The version of the Extension the manifest describes.
     *
     * @access private
     * @var string
     */
    private $version = null;
    
    /**
     * The license of the Extension the manifest describes.
     *
     * @access private
     * @var string
     */
    private $license = 'unknown';

    /**
     * The dependencies of the Extension the manifest describes.
     *
     * @access private
     * @var array
     */
    private $dependencies = [];

    /**
     * The RDF models that are required by the Extension the manifest describes.
     *
     * @access private
     * @var array
     */
    private $models = [];

    /**
     * The files corresponding to the RDF models to be imported at installation time.
     *
     * @access private
     * @var array
     */
    private $installModelFiles = [];

    /**
     * The configuration checks that have to be performed prior to installation.
     *
     * @access private
     * @var array
     */
    private $installChecks = [];

    /**
     * The paths to PHP Scripts to be run at installation time.
     *
     * @access private
     * @var array
     */
    private $installPHPFiles = [];
    
    /**
     * The data associated with the uninstall
     *
     * @access private
     * @var array
     */
    private $uninstallData = null;
    
    /**
     * The update handler
     *
     * @access private
     * @var string
     */
    private $updateHandler = null;
    
    /**
     * The routes to the controllers described by the manifest.
     *
     * @access private
     * @var array
     */
    private $routes = [];
    
    /**
     * The constants to be defined for the described extension.
     *
     * @access private
     * @var array
     */
    private $constants = [];

    /**
     * The Management Role of the extension described by the manifest.
     *
     * @access private
     * @var Resource
     */
    private $managementRoleUri = null;

    /**
     * Local data which can be added as an example
     * uses same format as install data
     *
     * @access private
     * @var array
     */
    private $localData = [];
    
    /**
     * The RDFS Classes that are considered optimizable for the described Extension.
     *
     * @access private
     * @var array
     */
    private $optimizableClasses = [];
    
    /**
     * The RDF Properties that are considered optimizable for the described Extension.
     * @access private
     * @var array
     */
    private $optimizableProperties = [];

    /**
     * The Access Control Layer table
     * @access private
     * @var array
     */
    private $acl = [];
    
    /**
     * Extra information, not consumed by the framework
     * @access private
     * @var array
     */
    private $extra = [];
    

    /**
     * Creates a new instance of Manifest.
     *
     * @access public
     * @author Jerome Bogaerts <jerome@taotesting.com>
     * @param  string $filePath The path to the manifest.php file to parse.
     */
    public function __construct($filePath)
    {
        
        // the file exists, we can refer to the $filePath.
        if (is_readable($filePath)) {
            $this->setFilePath($filePath);
            $array = require($this->getFilePath());
            
            // legacy support
            if (isset($array['additional']) && is_array($array['additional'])) {
                foreach ($array['additional'] as $key => $val) {
                    $array[$key] = $val;
                }
                unset($array['additional']);
            }
            
            // mandatory
            if (!empty($array['name'])) {
                $this->setName($array['name']);
            } else {
                throw new exception\MalformedManifestException("The 'name' component is mandatory in manifest located at '{$this->getFilePath()}'.");
            }
            
            
            if (!empty($array['label'])) {
                $this->setLabel($array['label']);
            }
            if (!empty($array['description'])) {
                $this->setDescription($array['description']);
            }
            
            if (!empty($array['license'])) {
                $this->setLicense($array['license']);
            }
            
            if (!empty($array['author'])) {
                $this->setAuthor($array['author']);
            }
            
            if (!empty($array['models'])) {
                $this->setModels($array['models']);
            }
            
            if (!empty($array['acl'])) {
                $this->setAclTable($array['acl']);
            }
            
            if (!empty($array['install'])) {
                if (!empty($array['install']['rdf'])) {
                    $files = is_array($array['install']['rdf']) ? $array['install']['rdf'] : [$array['install']['rdf']];
                    $this->setInstallModelFiles($files);
                }
                
                if (!empty($array['install']['checks'])) {
                    $this->setInstallChecks($array['install']['checks']);
                }
                
                if (!empty($array['install']['php'])) {
                    $files = is_array($array['install']['php']) ? $array['install']['php'] : [$array['install']['php']];
                    $this->setInstallPHPFiles($files);
                }
            }
            
            if (isset($array['uninstall'])) {
                $this->uninstallData = $array['uninstall'];
            }
            
            if (isset($array['update'])) {
                $this->updateHandler = $array['update'];
            }
            
            if (!empty($array['local'])) {
                $this->localData = $array['local'];
            }
            
            if (!empty($array['routes'])) {
                $this->setRoutes($array['routes']);
            }
            
            if (!empty($array['constants'])) {
                $this->setConstants($array['constants']);
            }
            
            if (!empty($array['extra'])) {
                $this->setExtra($array['extra']);
            }
            
            if (!empty($array['managementRole'])) {
                $this->setManagementRole($array['managementRole']);
            }
            
            if (!empty($array['optimizableClasses'])) {
                if (!is_array($array['optimizableClasses'])) {
                    throw new exception\MalformedManifestException("The 'optimizableClasses' component must be an array.");
                } else {
                    $this->setOptimizableClasses($array['optimizableClasses']);
                }
            }
            
            if (!empty($array['optimizableProperties'])) {
                if (!is_array($array['optimizableProperties'])) {
                    throw new exception\MalformedManifestException("The 'optimizableProperties' component must be an array.");
                } else {
                    $this->setOptimizableProperties($array['optimizableProperties']);
                }
            }
        } else {
            throw new ManifestNotFoundException("The Extension Manifest file located at '${filePath}' could not be read.");
        }
        
        $this->setFilePath($filePath);
    }

    /**
     * Get the path to the manifest file.
     *
     * @access public
     * @author Jerome Bogaerts <jerome@taotesting.com>
     * @return string
     */
    public function getFilePath()
    {
        $returnValue = (string) '';

        if (!empty($this->filePath)) {
            $returnValue = $this->filePath;
        }

        return (string) $returnValue;
    }

    /**
     * Set the path to the manifest file.
     *
     * @access private
     * @author Jerome Bogaerts <jerome@taotesting.com>
     * @param  string $filePath An absolute path.
     */
    private function setFilePath($filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * Get the name of the Extension the manifest describes.
     *
     * @access public
     * @author Jerome Bogaerts <jerome@taotesting.com>
     * @return string
     */
    public function getName()
    {
        if (!empty($this->name)) {
            $returnValue = $this->name;
        } else {
            $returnValue = null;
        }

        return (string) $returnValue;
    }

    /**
     * Set the name of the Extension the manifest describes.
     *
     * @access private
     * @author Jerome Bogaerts <jerome@taotesting.com>
     * @param  string $name A name
     */
    private function setName($name)
    {
        $this->name = $name;
    }
    
    /**
     * Get the license of the Extension the manifest describes.
     *
     * @access public
     * @return string
     */
    public function getLicense()
    {
        return $this->license;
    }
    
    /**
     * Set the license of the Extension the manifest describes.
     *
     * @access private
     * @param  string $license the livense
     */
    private function setLicense($license)
    {
        $this->license = $license;
    }

    /**
     * Get the description of the Extension the manifest describes.
     *
     * @access public
     * @author Jerome Bogaerts <jerome@taotesting.com>
     * @return string
     */
    public function getDescription()
    {
        return (string) $this->description;
    }

    /**
     * Set the description of the Extension that the manifest describes.
     *
     * @access private
     * @author Jerome Bogaerts <jerome@taotesting.com>
     * @param  string $description A description
     */
    private function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Get the author of the Extension the manifest describes.
     *
     * @access public
     * @author Jerome Bogaerts <jerome@taotesting.com>
     * @return string
     */
    public function getAuthor()
    {
        return (string) $this->author;
    }

    /**
     * Set the author of the Extension the manifest describes.
     *
     * @access private
     * @author Jerome Bogaerts <jerome@taotesting.com>
     * @param  string $author The author name
     */
    private function setAuthor($author)
    {
        $this->author = $author;
    }
    
    /**
     * Get the human readable label of the Extension the manifest describes.
     *
     * @access public
     * @author Jerome Bogaerts <jerome@taotesting.com>
     * @return string
     */
    public function getLabel()
    {
        return (string) $this->label;
    }
    
    /**
     * Set the human readable label of the Extension the manifest describes.
     *
     * @access private
     * @author Jerome Bogaerts <jerome@taotesting.com>
     * @param  string $label The extensions label
     */
    private function setLabel($label)
    {
        $this->label = $label;
    }
    
    /**
     * Sets the Access Controll Layer table
     * @param array $table
     */
    private function setAclTable($table)
    {
        $this->acl = $table;
    }
    
    /**
     * Returns the Access Controll Layer table
     * @return array
     */
    public function getAclTable()
    {
        return $this->acl;
    }

    /**
     * Get the version of the Extension the manifest describes.
     * @return string
     * @throws ManifestException
     */
    public function getVersion()
    {
        if ($this->version === null) {
            $packageInfo = $this->getComposerInfo()->getPackageInfo($this->getPackageId());
            $this->version = $packageInfo['version'];
        }
        return (string) $this->version;
    }

    /**
     * Get the dependencies of the Extension the manifest describes.
     *
     * The content of the array are extensionIDs, represented as strings.
     *
     * @access public
     * @author @author Jerome Bogaerts <jerome@taotesting.com>
     * @return array
     */
    public function getDependencies()
    {
        if (empty($this->dependencies)) {
            /** @var \common_ext_ExtensionsManager $extensionsManager */
            $extensionsManager = $this->getServiceLocator()->get(\common_ext_ExtensionsManager::class);
            $availablePackages = $extensionsManager->getAvailablePackages();
            $composerJson = $this->getComposerInfo()->getComposerJson(dirname($this->getFilePath()));
            foreach ($composerJson['require'] as $packageId => $packageVersion) {
                if (isset($availablePackages[$packageId])) {
                    $this->dependencies[$availablePackages[$packageId]] = $packageVersion;
                }
            }
        }
        return $this->dependencies;
    }

    /**
     * Get the models related to the Extension the manifest describes.
     *
     * The returned value is an array containing model URIs as strings.
     *
     * @access public
     * @author @author Jerome Bogaerts <jerome@taotesting.com>
     * @return array
     */
    public function getModels()
    {
        return (array) $this->models;
    }

    /**
     * Set the models related to the Extension the manifest describes.
     *
     * The $models parameter must be an array of strings that represent model URIs.
     *
     * @access private
     * @author Jerome Bogaerts <jerome@taotesting.com>
     * @param  array $models
     */
    private function setModels($models)
    {
        $this->models = $models;
    }

    /**
     * returns an array of RDF files
     * to import during install. The returned array contains paths to the files
     * to be imported.
     *
     * @access public
     * @author @author Jerome Bogaerts <jerome@taotesting.com>
     * @return array
     */
    public function getInstallModelFiles()
    {
        return (array) $this->installModelFiles;
    }

    /**
     * Sets the the RDF files to be imported during install. The array must contain
     * paths to the files to be imported.
     *
     * @access private
     * @author Jerome Bogaerts <jerome@taotesting.com>
     * @throws \common_ext_InstallationException
     * @param  array $installModelFiles
     */
    private function setInstallModelFiles($installModelFiles)
    {
        $this->installModelFiles = [];
        $installModelFiles = is_array($installModelFiles) ? $installModelFiles : [$installModelFiles];
        foreach ($installModelFiles as $row) {
            if (is_string($row)) {
                $rdfpath = $row;
            } elseif (is_array($row) && isset($row['file'])) {
                $rdfpath = $row['file'];
            } else {
                throw new \common_ext_InstallationException('Error in definition of model to add into the ontology for ' . $this->extension->getId(), 'INSTALL');
            }
            $this->installModelFiles[] = $rdfpath;
        }
    }

    /**
     * Get the installation checks to be performed prior installation of the described Extension.
     *
     * @access public
     * @author Jerome Bogaerts <jerome@taotesting.com>
     * @return array
     */
    public function getInstallChecks()
    {
        return (array) $this->installChecks;
    }

    /**
     * Set the installation checks to be performed prior installation of the described Extension.
     *
     * @access private
     * @author Jerome Bogaerts <jerome@taotesting.com>
     * @param  array $installChecks
     */
    private function setInstallChecks($installChecks)
    {
        // Check if the content is well formed.
        if (!is_array($installChecks)) {
            throw new exception\MalformedManifestException("The 'install->checks' component must be an array.");
        } else {
            foreach ($installChecks as $check) {
                // Mandatory fields for any kind of check are 'id' (string),
                // 'type' (string), 'value' (array).
                if (empty($check['type'])) {
                    throw new exception\MalformedManifestException("The 'install->checks->type' component is mandatory.");
                } elseif (!is_string($check['type'])) {
                    throw new exception\MalformedManifestException("The 'install->checks->type' component must be a string.");
                }
                
                if (empty($check['value'])) {
                    throw new exception\MalformedManifestException("The 'install->checks->value' component is mandatory.");
                } elseif (!is_array($check['value'])) {
                    throw new exception\MalformedManifestException("The 'install->checks->value' component must be an array.");
                }
                
                if (empty($check['value']['id'])) {
                    throw new exception\MalformedManifestException("The 'install->checks->value->id' component is mandatory.");
                } elseif (!is_string($check['value']['id'])) {
                    throw new exception\MalformedManifestException("The 'install->checks->value->id' component must be a string.");
                }
                
                switch ($check['type']) {
                    case 'CheckPHPRuntime':
                        if (empty($check['value']['min'])) {
                            throw new exception\MalformedManifestException("The 'install->checks->value->min' component is mandatory for PHPRuntime checks.");
                        }
                        break;
                    
                    case 'CheckPHPExtension':
                        if (empty($check['value']['name'])) {
                            throw new exception\MalformedManifestException("The 'install->checks->value->name' component is mandatory for PHPExtension checks.");
                        }
                        break;
                    
                    case 'CheckPHPINIValue':
                        if (empty($check['value']['name'])) {
                            throw new exception\MalformedManifestException("The 'install->checks->value->name' component is mandatory for PHPINIValue checks.");
                        } elseif ($check['value']['value'] == '') {
                            throw new exception\MalformedManifestException("The 'install->checks->value->value' component is mandatory for PHPINIValue checks.");
                        }
                        break;
                    
                    case 'CheckFileSystemComponent':
                        if (empty($check['value']['location'])) {
                            throw new exception\MalformedManifestException("The 'install->checks->value->location' component is mandatory for FileSystemComponent checks.");
                        } elseif (empty($check['value']['rights'])) {
                            throw new exception\MalformedManifestException("The 'install->checks->value->rights' component is mandatory for FileSystemComponent checks.");
                        }
                        break;
                    
                    case 'CheckCustom':
                        if (empty($check['value']['name'])) {
                            throw new exception\MalformedManifestException("The 'install->checks->value->name' component is mandatory for Custom checks.");
                        } elseif (empty($check['value']['extension'])) {
                            throw new exception\MalformedManifestException("The 'install->checks->value->extension' component is mandatory for Custom checks.");
                        }
                        break;
                    
                    default:
                        throw new exception\MalformedManifestException("The 'install->checks->type' component value is unknown.");
                    break;
                }
            }
        }
        
        $this->installChecks = $installChecks;
    }

    /**
     * Get a list of PHP files to be executed at installation time.
     *
     * The returned array contains absolute paths to the files to execute.
     *
     * @access public
     * @author @author Jerome Bogaerts <jerome@taotesting.com>
     * @return array
     */
    public function getInstallPHPFiles()
    {
        return $this->installPHPFiles;
    }
    
    /**
     * Return the uninstall data as an array if present, or null if not
     *
     * @return mixed
     */
    public function getUninstallData()
    {
        return $this->uninstallData;
    }
    
    /**
     * Return the className of the updateHandler
     *
     * @return string
     */
    public function getUpdateHandler()
    {
        return $this->updateHandler;
    }
    
    /**
      * PHP scripts to execute in order to add some sample data to an install
      *
      * @access public
      * @author joel.bout <joel@taotesting.com>
      * @return array
      */
    public function getLocalData()
    {
        return $this->localData;
    }

    /**
     * Set the PHP files to be run at installation time of the described Extension.
     *
     * The array must contain absolute paths to theses PHP files.
     *
     * @access private
     * @author Jerome Bogaerts <jerome@taotesting.com>
     * @param  array $installPHPFiles
     */
    private function setInstallPHPFiles($installPHPFiles)
    {
        $this->installPHPFiles = $installPHPFiles;
    }

    /**
     * Sets the routes for this extension.
     *
     * @param array $routes
     */
    private function setRoutes($routes)
    {
        $this->routes = $routes;
    }
    
    /**
     * Gets the controller routes of this extension.
     *
     * @return array
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * Get an array of constants to be defined where array keys are constant names
     * and values are the values of these constants.
     *
     * @access public
     * @author Jerome Bogaerts <jerome@taotesting.com>
     * @return array
     */
    public function getConstants()
    {
        return (array) $this->constants;
    }

    /**
     * Set an array of constants to be defined where array keys are constant names
     * and values are the values of these constants.
     *
     * @access private
     * @author Jerome Bogaerts <jerome@taotesting.com>
     * @param  array $constants
     */
    private function setConstants($constants)
    {
        $this->constants = $constants;
    }
    
    /**
     * Get the array with unformated extra data
     *
     * @access public
     * @author Jerome Bogaerts <jerome@taotesting.com>
     * @return array
     */
    public function getExtra()
    {
        return $this->extra;
    }
    
    /**
     * Set an array with extra data
     *
     * @access private
     * @author Jerome Bogaerts <jerome@taotesting.com>
     * @param  array $extra
     */
    private function setExtra($extra)
    {
        $this->extra = $extra;
    }
    
    /**
     * Extract checks from a given manifest file.
     *
     * @access public
     * @author Jerome Bogaerts <jerome@taotesting.com>
     * @param  string $file The path to a manifest.php file.
     * @return array
     */
    public static function extractDependencies($file)
    {
        $manifest = @include $file;
        return isset($manifest['requires']) && is_array($manifest['requires'])
            ? array_keys($manifest['requires'])
            : (isset($manifest['dependencies']) && is_array($manifest['dependencies'])
                ? $manifest['dependencies']
                : []
        );
    }

    /**
     * Extract checks from a given manifest file.
     *
     * @access public
     * @author Jerome Bogaerts <jerome@taotesting.com>
     * @param  $string $file The path to a manifest.php file.
     * @return \common_configuration_ComponentCollection
     */
    public static function extractChecks($file)
    {
        $returnValue = null;

        if (is_readable($file)) {
            $manifestPath = $file;
            $content = file_get_contents($manifestPath);
            $matches = [];
            preg_match_all("/(?:\"|')\s*checks\s*(?:\"|')\s*=>(\s*array\s*\((\s*array\((?:.*)\s*\)\)\s*,{0,1})*\s*\))/", $content, $matches);
            
            if (!empty($matches[1][0])) {
                $returnValue = eval('return ' . $matches[1][0] . ';');
                
                foreach ($returnValue as &$component) {
                    if (strpos($component['type'], 'FileSystemComponent') !== false) {
                        $root = realpath(dirname(__FILE__) . '/../../../');
                        $component['value']['location'] = $root . '/' . $component['value']['location'];
                    }
                }
            } else {
                $returnValue = [];
            }
        } else {
            $msg = "Extension Manifest file could not be found in '${file}'.";
            throw new ManifestNotFoundException($msg);
        }

        return $returnValue;
    }

    /**
     * Removing all generis references from framework
     *
     * @access public
     * @author Jerome Bogaerts <jerome@taotesting.com>
     * @return \core_kernel_classes_Resource
     * @deprecated
     * @see Manifest::getManagementRoleUri()
     */
    public function getManagementRole()
    {
        return is_null($this->managementRoleUri) ? null : new \core_kernel_classes_Resource($this->managementRoleUri);
    }
    
    /**
     * Get the Role dedicated to manage this extension. Returns null if there is
     *
     * @return string
     */
    public function getManagementRoleUri()
    {
        return $this->managementRoleUri;
    }
    

    /**
     * Set the Management Role of the Extension Manifest.
     *
     * @access private
     * @author Jerome Bogaerts <jerome@taotesting.com>
     * @param  string $managementRole The URI of the Management Role of the Extension.
     */
    private function setManagementRole($managementRoleUri)
    {
        $this->managementRoleUri = $managementRoleUri;
    }
    
    /**
     * Get an array of Class URIs (as strings) that are considered optimizable for the
     * described Extension.
     *
     * @access public
     * @author Jerome Bogaerts <jerome@taotesting.com>
     * @return array
     */
    public function getOptimizableClasses()
    {
        return $this->optimizableClasses;
    }
    
    /**
     * Set the Classes that are considered optimizable for the described Extension.
     *
     * The array passed as a parameter must be a set of URIs (as strings) referencing
     * RDFS Classes.
     *
     * @param array $optimizableClasses
     */
    private function setOptimizableClasses(array $optimizableClasses)
    {
        $this->optimizableClasses = $optimizableClasses;
    }
    
    /**
     * Get an array of Property URIs (as strings) that are considered optimizable for the
     * described Extension.
     *
     * @return array
     */
    public function getOptimizableProperties()
    {
        return $this->optimizableProperties;
    }

    /**
     * @return ComposerInfo
     */
    private function getComposerInfo()
    {
        return new ComposerInfo();
    }

    /**
     * Set the Properties that are considered optimizable for the described Extension.
     *
     * The array passed as a parameter must be a set of URIs (as strings) referencing
     * RDF Properties.
     *
     * @param array $optimizableProperties
     */
    private function setOptimizableProperties(array $optimizableProperties)
    {
        $this->optimizableProperties = $optimizableProperties;
    }

    /**
     * @return string
     * @throws ManifestException
     */
    private function getPackageId()
    {
        return $this->getComposerInfo()->getComposerJson(dirname($this->getFilePath()))['name'];
    }

}
