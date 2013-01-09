<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - common/ext/class.Extension.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 08.01.2013, 17:31:43 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author lionel.lecaque@tudor.lu
 * @package common
 * @see @license  GNU General Public (GPL) Version 2 http://www.opensource.org/licenses/gpl-2.0.php
 * @subpackage ext
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section -87--2--3--76--959adf5:123ebfc12cd:-8000:00000000000017AF-includes begin
// section -87--2--3--76--959adf5:123ebfc12cd:-8000:00000000000017AF-includes end

/* user defined constants */
// section -87--2--3--76--959adf5:123ebfc12cd:-8000:00000000000017AF-constants begin
// section -87--2--3--76--959adf5:123ebfc12cd:-8000:00000000000017AF-constants end

/**
 * Short description of class common_ext_Extension
 *
 * @access public
 * @author lionel.lecaque@tudor.lu
 * @package common
 * @see @license  GNU General Public (GPL) Version 2 http://www.opensource.org/licenses/gpl-2.0.php
 * @subpackage ext
 */
class common_ext_Extension
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute id
     *
     * @access private
     * @var string
     */
    private $id = '';

    /**
     * Short description of attribute manifest
     *
     * @access public
     * @var Manifest
     */
    public $manifest = null;

    /**
     * Short description of attribute requiredExtensionsList
     *
     * @access public
     * @var array
     */
    public $requiredExtensionsList = array();

    /**
     * Short description of attribute classLoaderPackages
     *
     * @access public
     * @see @license  GNU General Public (GPL) Version 2 http://www.opensource.org/licenses/gpl-2.0.php
     * @var array
     */
    public $classLoaderPackages = array();

    /**
     * Short description of attribute installFiles
     *
     * @access public
     * @var array
     */
    public $installFiles = array();

    /**
     * configuration array read from db
     *
     * @access private
     * @var mixed
     */
    private $dbConfig = null;

    /**
     * configuration array read from file
     *
     * @access public
     * @var mixed
     */
    public $fileConfig = null;

    /**
     * Short description of attribute installed
     *
     * @access protected
     * @var boolean
     */
    protected $installed = false;

    /**
     * Short description of attribute localData
     *
     * @access public
     * @var array
     */
    public $localData = array();

    // --- OPERATIONS ---

    /**
     * Should not be called directly, please use ExtensionsManager
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string id
     * @param  boolean installed
     * @param  array data array to preload the dbconfiguration
     * @return mixed
     */
    public function __construct($id, $installed = false, $data = null)
    {
        // section -87--2--3--76--148ee98a:12452773959:-8000:0000000000002320 begin
    	$manifestFile = EXTENSION_PATH.'/'.$id.'/'.MANIFEST_NAME;
		$this->id = $id;
		$this->installed = $installed;
		if(is_file($manifestFile)){
			
			$this->manifest = new common_ext_Manifest($manifestFile);
			
			$manifestArray = require $manifestFile;
			// adapt legacy manifests
			if (isset($manifestArray['additional']) && is_array($manifestArray['additional'])) {
				foreach ($manifestArray['additional'] as $key => $val) {
					$manifestArray[$key] = $val;
				}
				unset($manifestArray['additional']);
			}
			
			$this->name = $manifestArray['name'];
			
			if(isset($manifestArray['install']) && is_array($manifestArray['install'])) {
				$this->installFiles = $manifestArray['install'];
			}
			$list = isset($manifestArray['dependencies']) ? $manifestArray['dependencies']
				: (isset($manifestArray['dependances']) ? $manifestArray['dependances'] : array());
			if(!is_array($list) && !empty($list)){
				$list = array($list);
			}
			foreach ($list as $ext) {
				$this->requiredExtensionsList[] = $ext;
			}
			
			if(isset($manifestArray['classLoaderPackages'])) {
				$this->classLoaderPackages = $manifestArray['classLoaderPackages'];	
			}
			if(isset($manifestArray['models'])) {
				if(!is_array($manifestArray['models']) && !empty($manifestArray['models'])){
					$this->model = array($manifestArray['models']);
				}
				else{
					$this->model = $manifestArray['models'];	
				}
			}
			if(isset($manifestArray['modelsRight']) && !empty ($manifestArray['modelsRight'])) {
				$this->modelsRight = $manifestArray['modelsRight'];
			}
			if(isset($manifestArray['install']) && is_array($manifestArray['install'])) {
				$this->installFiles = $manifestArray['install'];
			}
			if(isset($manifestArray['local']) && is_array($manifestArray['local'])) {
				$this->localData = $manifestArray['local'];
			}
			$this->constants = (isset($manifestArray['constants']) && is_array($manifestArray['constants']))
				? $manifestArray['constants']
				: array();
		}
		else {
			//Here the extension is set unvalided to not be displayed by the view
			throw new common_ext_ManifestNotFoundException("Extension Manifest not found for extension '${id}'.", $id);
		}
		$this->dbConfig = $data;
        // section -87--2--3--76--148ee98a:12452773959:-8000:0000000000002320 end
    }

    /**
     * returns the path to the config file
     * used for instalation specific configurations
     *
     * @access private
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return string
     */
    private function getConfigFilePath()
    {
        $returnValue = (string) '';

        // section 10-30-1--78--302e8d4b:13c05a95bce:-8000:0000000000001E9A begin
        $returnValue = $this->getDir().'common'.DIRECTORY_SEPARATOR.'conf'.DIRECTORY_SEPARATOR.'common.conf.php';
        // section 10-30-1--78--302e8d4b:13c05a95bce:-8000:0000000000001E9A end

        return (string) $returnValue;
    }

    /**
     * returns the id of the extension
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return string
     */
    public function getID()
    {
        $returnValue = (string) '';

        // section 127-0-1-1-6cdd9365:137e5078659:-8000:0000000000001A17 begin
        return $this->id;
        // section 127-0-1-1-6cdd9365:137e5078659:-8000:0000000000001A17 end

        return (string) $returnValue;
    }

    /**
     * returns all constants of the extension
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return array
     */
    public function getConstants()
    {
        $returnValue = array();

        // section 127-0-1-1--52d0f243:139d3b5b89e:-8000:0000000000001B39 begin
        $returnValue = $this->manifest->getConstants();
        // section 127-0-1-1--52d0f243:139d3b5b89e:-8000:0000000000001B39 end

        return (array) $returnValue;
    }

    /**
     * returns all configuration key/value pairs
     *
     * @access private
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return array
     */
    private function getConfigs()
    {
        $returnValue = array();

        // section -87--2--3--76--570dd3e1:12507aae5fa:-8000:00000000000023A0 begin
        if(is_null($this->dbConfig)) {
        	$db = core_kernel_classes_DbWrapper::singleton();
			$query = "SELECT loaded,\"loadAtStartUp\",ghost FROM extensions WHERE id = ?";
			$sth = $db->prepare($query);
			$success = $sth->execute(array($this->id));

			if ($success && $row = $sth->fetch()){
				$this->dbConfig = $row;
				/*
				$loaded = $row['loaded'] == 1;
				$loadedAtStartUp = $row['loadAtStartUp'] == 1;
				$ghost = $row['ghost'] == 1;
				$this->configuration = new common_ext_ExtensionConfiguration($loaded,$loadedAtStartUp, $ghost);
				*/
				$sth->closeCursor();	
			} else {
				common_Logger::w('Unable to load dbconfig for '.$this->getID());
				$this->dbConfig = array();
			}
			
        }
        if (is_null($this->fileConfig)) {
			$configFile = $this->getConfigFilePath();
			if (file_exists($configFile)) {
				$this->fileConfig = include $configFile;
			} else {
				$this->fileConfig = array();
			}
        }
        $returnValue = array_merge($this->dbConfig, $this->fileConfig);
        // section -87--2--3--76--570dd3e1:12507aae5fa:-8000:00000000000023A0 end

        return (array) $returnValue;
    }

    /**
     * sets a configuration value
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string key
     * @param  value
     * @return mixed
     */
    public function setConfig($key, $value)
    {
        // section 10-30-1--78--220638c3:13bfff7253d:-8000:0000000000009D34 begin
		$this->fileConfig[$key] = $value;
		$handle = fopen($this->getConfigFilePath(), 'w');
        $success = fwrite($handle, '<?php return '.common_Utils::toPHPVariableString($this->fileConfig).';');
        fclose($handle);
        if (!$success) {
			throw new common_exception_Error('Unable to write config for extension '.$this->getID());
        }
        // section 10-30-1--78--220638c3:13bfff7253d:-8000:0000000000009D34 end
    }

    /**
     * retrieves a configuration value
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string key
     * @return mixed
     */
    public function getConfig($key)
    {
        $returnValue = null;

        // section 10-30-1--78--220638c3:13bfff7253d:-8000:0000000000009D39 begin
        $config = $this->getConfigs();
        if (isset($config[$key])) {
        	$returnValue = $config[$key]; 
        } else {
        	common_Logger::w('Unknown config key '.$key.' used for extension '.$this->getID());
        }
        // section 10-30-1--78--220638c3:13bfff7253d:-8000:0000000000009D39 end

        return $returnValue;
    }

    /**
     * removes a configuration entry
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string key
     * @return mixed
     */
    public function unsetConfig($key)
    {
        // section 10-30-1--78--220638c3:13bfff7253d:-8000:0000000000009D49 begin
        unset($this->fileConfig[$key]);
        $handle = fopen($this->getConfigFilePath(), 'w');
        fwrite($handle, '<? return '.common_Utils::toPHPVariableString($this->fileConfig).';');
        fclose($handle);
        // section 10-30-1--78--220638c3:13bfff7253d:-8000:0000000000009D49 end
    }

    /**
     * returns the version of the extension
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return string
     */
    public function getVersion()
    {
        $returnValue = (string) '';

        // section 10-30-1--78--70d18191:13c00dcd1c6:-8000:0000000000001E9B begin
        $returnValue = $this->manifest->getVersion();
        // section 10-30-1--78--70d18191:13c00dcd1c6:-8000:0000000000001E9B end

        return (string) $returnValue;
    }

    /**
     * returns the author of the extension
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return string
     */
    public function getAuthor()
    {
        $returnValue = (string) '';

        // section 10-30-1--78--70d18191:13c00dcd1c6:-8000:0000000000001E9D begin
        $returnValue = $this->manifest->getAuthor();
        // section 10-30-1--78--70d18191:13c00dcd1c6:-8000:0000000000001E9D end

        return (string) $returnValue;
    }

    /**
     * returns the name of the extension
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return string
     */
    public function getName()
    {
        $returnValue = (string) '';

        // section 10-30-1--78--70d18191:13c00dcd1c6:-8000:0000000000001E9F begin
        $returnValue = $this->manifest->getName();
        // section 10-30-1--78--70d18191:13c00dcd1c6:-8000:0000000000001E9F end

        return (string) $returnValue;
    }

    /**
     * returns whenever or not the extension is enabled
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return boolean
     */
    public function isEnabled()
    {
        $returnValue = (bool) false;

        // section 10-30-1--78--70d18191:13c00dcd1c6:-8000:0000000000001EA1 begin
    	if ($this->isInstalled()) {
        	$returnValue = !$this->getConfig('ghost');
        }
        // section 10-30-1--78--70d18191:13c00dcd1c6:-8000:0000000000001EA1 end

        return (bool) $returnValue;
    }

    /**
     * returns whenever or not the extension is installed
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return boolean
     */
    public function isInstalled()
    {
        $returnValue = (bool) false;

        // section 10-30-1--78--70d18191:13c00dcd1c6:-8000:0000000000001EA3 begin
        $returnValue = $this->installed;
        // section 10-30-1--78--70d18191:13c00dcd1c6:-8000:0000000000001EA3 end

        return (bool) $returnValue;
    }

    /**
     * returns the base dir of the extension
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return string
     */
    public function getDir()
    {
        $returnValue = (string) '';

        // section 10-30-1--78--70d18191:13c00dcd1c6:-8000:0000000000001EA5 begin
		$returnValue = EXTENSION_PATH .DIRECTORY_SEPARATOR.$this->getID().DIRECTORY_SEPARATOR;
        // section 10-30-1--78--70d18191:13c00dcd1c6:-8000:0000000000001EA5 end

        return (string) $returnValue;
    }

    /**
     * retrieves a constant
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string key
     * @return mixed
     */
    public function getConstant($key)
    {
        $returnValue = null;

        // section 10-30-1--78--70d18191:13c00dcd1c6:-8000:0000000000001EA7 begin
        if (isset($this->constants[$key])) {
        	$returnValue = $this->constants[$key];
        } elseif (defined($key)) {
        	common_logger::w('constant outside of extension called: '.$key);
        	$returnValue = constant($key);
        } else {
        	throw new common_exception_Error('Unknown constant \''.$key.'\'');
        }
        // section 10-30-1--78--70d18191:13c00dcd1c6:-8000:0000000000001EA7 end

        return $returnValue;
    }

    /**
     * get all modules of the extension
     * by searching the actions directory, not the ontology
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return array
     */
    public function getAllModules()
    {
        $returnValue = array();

        // section 10-30-1--78--70d18191:13c00dcd1c6:-8000:0000000000001EAA begin
		$dir = new DirectoryIterator(ROOT_PATH.$this->getID().DIRECTORY_SEPARATOR.'actions');
	    foreach ($dir as $fileinfo) {
			if(preg_match('/^class\.[^.]*\.php$/', $fileinfo->getFilename())) {
				$module = substr($fileinfo->getFilename(), 6, -4);
				$class = $this->getID().'_actions_'.$module;
				if (class_exists($class)) {
					if (is_subclass_of($class, 'Module')) {
						$returnValue[$module] = $class;
					} else {
						common_Logger::w($class.' does not inherit Module');
					}
				} else {
					common_Logger::w($class.' not found for file \''.$fileinfo->getFilename().'\'');
				}
			}
		}
        // section 10-30-1--78--70d18191:13c00dcd1c6:-8000:0000000000001EAA end

        return (array) $returnValue;
    }

    /**
     * returns a module by ID
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string id
     * @return Module
     */
    public function getModule($id)
    {
        $returnValue = null;

        // section 10-30-1--78--70d18191:13c00dcd1c6:-8000:0000000000001EAC begin
    	$className = $this->getID().'_actions_'.$id;
		if(class_exists($className)) {
			$returnValue = new $className;
		} else {
			common_Logger::e('could not load '.$className);
		}
        // section 10-30-1--78--70d18191:13c00dcd1c6:-8000:0000000000001EAC end

        return $returnValue;
    }

    /**
     * returns the extension the current extension
     * depends on recursively
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return array
     */
    public function getDependencies()
    {
        $returnValue = array();

        // section 10-30-1--78--70d18191:13c00dcd1c6:-8000:0000000000001EAF begin
		if(is_array($this->requiredExtensionsList)) {
        	$returnValue = $this->requiredExtensionsList;
        	
        	foreach($this->requiredExtensionsList as $id){
        		$dependence = common_ext_ExtensionsManager::singleton()->getExtensionById($id);
        		$returnValue = array_merge($returnValue, $dependence->getDependencies());
        	}
        }
        $returnValue = array_unique($returnValue);
        // section 10-30-1--78--70d18191:13c00dcd1c6:-8000:0000000000001EAF end

        return (array) $returnValue;
    }

} /* end of class common_ext_Extension */

?>