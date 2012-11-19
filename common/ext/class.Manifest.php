<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - common\ext\class.Manifest.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 09.11.2012, 10:51:37 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package common
 * @subpackage ext
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section -64--88-0-2--ea43850:13ae1d8a335:-8000:0000000000001C02-includes begin
// section -64--88-0-2--ea43850:13ae1d8a335:-8000:0000000000001C02-includes end

/* user defined constants */
// section -64--88-0-2--ea43850:13ae1d8a335:-8000:0000000000001C02-constants begin
// section -64--88-0-2--ea43850:13ae1d8a335:-8000:0000000000001C02-constants end

/**
 * Short description of class common_ext_Manifest
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package common
 * @subpackage ext
 */
class common_ext_Manifest
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute filePath
     *
     * @access private
     * @var string
     */
    private $filePath = '';

    /**
     * Short description of attribute name
     *
     * @access private
     * @var string
     */
    private $name = '';

    /**
     * Short description of attribute description
     *
     * @access private
     * @var string
     */
    private $description = '';

    /**
     * Short description of attribute author
     *
     * @access private
     * @var string
     */
    private $author = '';

    /**
     * Short description of attribute version
     *
     * @access private
     * @var string
     */
    private $version = '';

    /**
     * Short description of attribute dependencies
     *
     * @access private
     * @var array
     */
    private $dependencies = array();

    /**
     * Short description of attribute models
     *
     * @access private
     * @var array
     */
    private $models = array();

    /**
     * Short description of attribute modelsRights
     *
     * @access private
     * @var array
     */
    private $modelsRights = array();

    /**
     * Short description of attribute installModelFiles
     *
     * @access private
     * @var array
     */
    private $installModelFiles = array();

    /**
     * Short description of attribute installChecks
     *
     * @access private
     * @var array
     */
    private $installChecks = array();

    /**
     * Short description of attribute classLoaderPackages
     *
     * @access private
     * @var array
     */
    private $classLoaderPackages = array();

    /**
     * Short description of attribute constants
     *
     * @access private
     * @var array
     */
    private $constants = array();

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string filePath
     * @return mixed
     */
    public function __construct($filePath)
    {
        // section -64--88-0-2--ea43850:13ae1d8a335:-8000:0000000000001C26 begin
        
    	// the file exists, we can refer to the $filePath.
    	if (is_readable($filePath)){
    		$this->setFilePath($filePath);
    		$array = require($this->getFilePath());
    		
    		// mandatory
    		if (!empty($array['name'])){
    			$this->setName($array['name']);
    		}
    		else{
    			throw new common_ext_MalformedManifestException("The 'name' component is mandatory in manifest located at '{$this->getFilePath()}'.");
    		}
    		
    		if (!empty($array['description'])){
    			$this->setDescription($array['description']);
    		}
    		
    		if (!empty($array['author'])){
    			$this->setAuthor($array['author']);	
    		}
    		
    		if (!empty($array['version'])){
    			$this->setVersion($array['version']);
    		}
    		else{
    			throw new common_ext_MalformedManifestException("The 'version' component is mandatory in manifest located at '{$this->getFilePath()}'.");
    		}
    		
    		if (!empty($array['dependencies'])){
    			$this->setDependencies($array['dependencies']);
    		}
    		
    		if (!empty($array['models'])){
    			$this->setModels($array['models']);
    		}
    		
    		if (!empty($array['modelsRight'])){
    			$this->setModelsRights($array['modelsRight']);
    		}
    		
    		if (!empty($array['install'])){
    			if (!empty($array['install']['rdf'])){
    				$a = array();
    				foreach ($array['install']['rdf'] as $rdf){
    					$ns = $rdf['ns'];
    					$file = $rdf['file'];
    					
    					if (!array_key_exists($ns, $a)){
    						$a[$ns] = array();
    					}
    					
    					array_push($a[$ns], $file);
    				}
    				$this->setInstallModelFiles($a);
    			}
    			
    			if (!empty($array['install']['checks'])){
    				$this->setInstallChecks($array['install']['checks']);
    			}
    		}
    		
    		// mandatory
    		if (!empty($array['classLoaderPackages'])){
    			$this->setClassLoaderPackages($array['classLoaderPackages']);
    		}
    		else{
    			throw new common_ext_MalformedManifestException("The 'classLoaderPackages' component is mandatory in manifest located at '{$this->getFilePath()}'.");
    		}
    		
    		if (!empty($array['constants'])){
    			$this->setConstants($array['constants']);
    		}
    	}
    	else{
    		throw new common_ext_ManifestNotFoundException("The Extension Manifest file located at '${filePath}' could not be read.");
    	}
    	
        $this->setFilePath($filePath);
        // section -64--88-0-2--ea43850:13ae1d8a335:-8000:0000000000001C26 end
    }

    /**
     * Short description of method getFilePath
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return string
     */
    public function getFilePath()
    {
        $returnValue = (string) '';

        // section -64--88-0-2--ea43850:13ae1d8a335:-8000:0000000000001C41 begin
        if (!empty($this->filePath)){
        	$returnValue = $this->filePath;
        }
        else{
        	$returnValue = null;
        }
        // section -64--88-0-2--ea43850:13ae1d8a335:-8000:0000000000001C41 end

        return (string) $returnValue;
    }

    /**
     * Short description of method setFilePath
     *
     * @access private
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string filePath
     * @return void
     */
    private function setFilePath($filePath)
    {
        // section -64--88-0-2--ea43850:13ae1d8a335:-8000:0000000000001C57 begin
        $this->filePath = $filePath;
        // section -64--88-0-2--ea43850:13ae1d8a335:-8000:0000000000001C57 end
    }

    /**
     * Short description of method getName
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return string
     */
    public function getName()
    {
        $returnValue = (string) '';

        // section -64--88-0-2--ea43850:13ae1d8a335:-8000:0000000000001C43 begin
        if (!empty($this->name)){
        	$returnValue = $this->name;
        }
        else{
        	$returnValue = null;
        }
        // section -64--88-0-2--ea43850:13ae1d8a335:-8000:0000000000001C43 end

        return (string) $returnValue;
    }

    /**
     * Short description of method setName
     *
     * @access private
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string name
     * @return void
     */
    private function setName($name)
    {
        // section -64--88-0-2--ea43850:13ae1d8a335:-8000:0000000000001C5A begin
        $this->name = $name;
        // section -64--88-0-2--ea43850:13ae1d8a335:-8000:0000000000001C5A end
    }

    /**
     * Short description of method getDescription
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return string
     */
    public function getDescription()
    {
        $returnValue = (string) '';

        // section -64--88-0-2--ea43850:13ae1d8a335:-8000:0000000000001C45 begin
        if (!empty($this->description)){
        	$returnValue = $this->description;
        }
        else{
        	$returnValue = null;
        }
        // section -64--88-0-2--ea43850:13ae1d8a335:-8000:0000000000001C45 end

        return (string) $returnValue;
    }

    /**
     * Short description of method setDescription
     *
     * @access private
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string description
     * @return void
     */
    private function setDescription($description)
    {
        // section -64--88-0-2--ea43850:13ae1d8a335:-8000:0000000000001C5D begin
        $this->description = $description;
        // section -64--88-0-2--ea43850:13ae1d8a335:-8000:0000000000001C5D end
    }

    /**
     * Short description of method getAuthor
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return string
     */
    public function getAuthor()
    {
        $returnValue = (string) '';

        // section 10-13-1-85-9f61b60:13ae4914bf6:-8000:0000000000001C62 begin
        $returnValue = $this->author;
        // section 10-13-1-85-9f61b60:13ae4914bf6:-8000:0000000000001C62 end

        return (string) $returnValue;
    }

    /**
     * Short description of method setAuthor
     *
     * @access private
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string author
     * @return void
     */
    private function setAuthor($author)
    {
        // section 10-13-1-85-9f61b60:13ae4914bf6:-8000:0000000000001C64 begin
        $this->author = $author;
        // section 10-13-1-85-9f61b60:13ae4914bf6:-8000:0000000000001C64 end
    }

    /**
     * Short description of method getVersion
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return string
     */
    public function getVersion()
    {
        $returnValue = (string) '';

        // section -64--88-0-2--ea43850:13ae1d8a335:-8000:0000000000001C47 begin
        if (!empty($this->version)){
        	$returnValue = $this->version;
        }
        else{
        	$returnValue = null;
        }
        // section -64--88-0-2--ea43850:13ae1d8a335:-8000:0000000000001C47 end

        return (string) $returnValue;
    }

    /**
     * Short description of method setVersion
     *
     * @access private
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string version
     * @return void
     */
    private function setVersion($version)
    {
        // section -64--88-0-2--ea43850:13ae1d8a335:-8000:0000000000001C60 begin
        $this->version = $version;
        // section -64--88-0-2--ea43850:13ae1d8a335:-8000:0000000000001C60 end
    }

    /**
     * Short description of method getDependencies
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return array
     */
    public function getDependencies()
    {
        $returnValue = array();

        // section -64--88-0-2--ea43850:13ae1d8a335:-8000:0000000000001C49 begin
        $returnValue = $this->dependencies;
        // section -64--88-0-2--ea43850:13ae1d8a335:-8000:0000000000001C49 end

        return (array) $returnValue;
    }

    /**
     * Short description of method setDependencies
     *
     * @access private
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  array dependencies
     * @return void
     */
    private function setDependencies($dependencies)
    {
        // section -64--88-0-2--ea43850:13ae1d8a335:-8000:0000000000001C63 begin
        $this->dependencies = $dependencies;
        // section -64--88-0-2--ea43850:13ae1d8a335:-8000:0000000000001C63 end
    }

    /**
     * Short description of method getModels
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return array
     */
    public function getModels()
    {
        $returnValue = array();

        // section -64--88-0-2--ea43850:13ae1d8a335:-8000:0000000000001C4B begin
        $returnValue = $this->models;
        // section -64--88-0-2--ea43850:13ae1d8a335:-8000:0000000000001C4B end

        return (array) $returnValue;
    }

    /**
     * Short description of method setModels
     *
     * @access private
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  array models
     * @return void
     */
    private function setModels($models)
    {
        // section -64--88-0-2--ea43850:13ae1d8a335:-8000:0000000000001C66 begin
        $this->models = $models;
        // section -64--88-0-2--ea43850:13ae1d8a335:-8000:0000000000001C66 end
    }

    /**
     * Short description of method getModelsRights
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return array
     */
    public function getModelsRights()
    {
        $returnValue = array();

        // section -64--88-0-2--ea43850:13ae1d8a335:-8000:0000000000001C4D begin
        $returnValue = $this->modelsRights;
        // section -64--88-0-2--ea43850:13ae1d8a335:-8000:0000000000001C4D end

        return (array) $returnValue;
    }

    /**
     * Short description of method setModelsRights
     *
     * @access private
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  array modelsRights
     * @return void
     */
    private function setModelsRights($modelsRights)
    {
        // section -64--88-0-2--ea43850:13ae1d8a335:-8000:0000000000001C69 begin
        $this->modelsRights = $modelsRights;
        // section -64--88-0-2--ea43850:13ae1d8a335:-8000:0000000000001C69 end
    }

    /**
     * Short description of method getInstallModelFiles
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return array
     */
    public function getInstallModelFiles()
    {
        $returnValue = array();

        // section -64--88-0-2--ea43850:13ae1d8a335:-8000:0000000000001C4F begin
        $returnValue = $this->installModelFiles;
        // section -64--88-0-2--ea43850:13ae1d8a335:-8000:0000000000001C4F end

        return (array) $returnValue;
    }

    /**
     * Short description of method setInstallModelFiles
     *
     * @access private
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  array installModelFiles
     * @return void
     */
    private function setInstallModelFiles($installModelFiles)
    {
        // section -64--88-0-2--ea43850:13ae1d8a335:-8000:0000000000001C6E begin
        $this->installModelFiles = $installModelFiles;
        // section -64--88-0-2--ea43850:13ae1d8a335:-8000:0000000000001C6E end
    }

    /**
     * Short description of method getInstallChekcs
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return array
     */
    public function getInstallChekcs()
    {
        $returnValue = array();

        // section -64--88-0-2--ea43850:13ae1d8a335:-8000:0000000000001C51 begin
        $returnValue = $this->installChecks;
        // section -64--88-0-2--ea43850:13ae1d8a335:-8000:0000000000001C51 end

        return (array) $returnValue;
    }

    /**
     * Short description of method setInstallChecks
     *
     * @access private
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  array installChecks
     * @return void
     */
    private function setInstallChecks($installChecks)
    {
        // section -64--88-0-2--ea43850:13ae1d8a335:-8000:0000000000001C71 begin
        // Check if the content is well formed.
    	if (!is_array($installChecks)){
    		throw new common_ext_MalformedManifestException("The 'install->checks' component must be an array.");	
    	}
    	else{
    		foreach ($installChecks as $check){
    			// Mandatory fields for any kind of check are 'id' (string), 
    			// 'type' (string), 'value' (array).
    			if (empty($check['type'])){
    				throw new common_ext_MalformedManifestException("The 'install->checks->type' component is mandatory.");	
    			}else if (!is_string($check['type'])){
    				throw new common_ext_MalformedManifestException("The 'install->checks->type' component must be a string.");
    			}
    			
    			if (empty($check['value'])){
    				throw new common_ext_MalformedManifestException("The 'install->checks->value' component is mandatory.");
    			}
    			else if (!is_array($check['value'])){
    				throw new common_ext_MalformedManifestException("The 'install->checks->value' component must be an array.");	
    			}
    			
    			if (empty($check['value']['id'])){
    				throw new common_ext_MalformedManifestException("The 'install->checks->value->id' component is mandatory.");	
    			}
    			else if (!is_string($check['value']['id'])){
    				throw new common_ext_MalformedManifestException("The 'install->checks->value->id' component must be a string.");	
    			}
    			
    			switch ($check['type']){
    				case 'CheckPHPRuntime':
    					if (empty($check['value']['min'])){
    						throw new common_ext_MalformedManifestException("The 'install->checks->value->min' component is mandatory for PHPRuntime checks.");	
    					}
    				break;
    				
    				case 'CheckPHPExtension':
    					if (empty($check['value']['name'])){
    						throw new common_ext_MalformedManifestException("The 'install->checks->value->name' component is mandatory for PHPExtension checks.");
    					}
    				break;
    				
    				case 'CheckPHPINIValue':
    					if (empty($check['value']['name'])){
    						throw new common_ext_MalformedManifestException("The 'install->checks->value->name' component is mandatory for PHPINIValue checks.");
    					}
    					else if ($check['value']['value'] == ''){
    						throw new common_ext_MalformedManifestException("The 'install->checks->value->value' component is mandatory for PHPINIValue checks.");
    					}
    				break;
    				
    				case 'CheckFileSystemComponent':
    					if (empty($check['value']['location'])){
    						throw new common_ext_MalformedManifestException("The 'install->checks->value->location' component is mandatory for FileSystemComponent checks.");	
    					}
    					else if (empty($check['value']['rights'])){
    						throw new common_ext_MalformedManifestException("The 'install->checks->value->rights' component is mandatory for FileSystemComponent checks.");	
    					}
    				break;
    				
    				case 'CheckCustom':
    					if (empty($check['value']['name'])){
    						throw new common_ext_MalformedManifestException("The 'install->checks->value->name' component is mandatory for Custom checks.");	
    					}
    					else if (empty($check['value']['extension'])){
    						throw new common_ext_MalformedManifestException("The 'install->checks->value->extension' component is mandatory for Custom checks.");		
    					}
    				break;
    				
    				default:
    					throw new common_ext_MalformedManifestException("The 'install->checks->type' component value is unknown.");	
    				break;
    			}
    		}
    	}
    	
        $this->installChecks = $installChecks;
        // section -64--88-0-2--ea43850:13ae1d8a335:-8000:0000000000001C71 end
    }

    /**
     * Short description of method getClassLoaderPackages
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return array
     */
    public function getClassLoaderPackages()
    {
        $returnValue = array();

        // section -64--88-0-2--ea43850:13ae1d8a335:-8000:0000000000001C53 begin
        $returnValue = $this->classLoaderPackages;
        // section -64--88-0-2--ea43850:13ae1d8a335:-8000:0000000000001C53 end

        return (array) $returnValue;
    }

    /**
     * Short description of method setClassLoaderPackages
     *
     * @access private
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  array classLoaderPackages
     * @return void
     */
    private function setClassLoaderPackages($classLoaderPackages)
    {
        // section -64--88-0-2--ea43850:13ae1d8a335:-8000:0000000000001C75 begin
        $this->classLoaderPackages = $classLoaderPackages;
        // section -64--88-0-2--ea43850:13ae1d8a335:-8000:0000000000001C75 end
    }

    /**
     * Short description of method getConstants
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return array
     */
    public function getConstants()
    {
        $returnValue = array();

        // section -64--88-0-2--ea43850:13ae1d8a335:-8000:0000000000001C55 begin
        $returnValue = $this->constants;
        // section -64--88-0-2--ea43850:13ae1d8a335:-8000:0000000000001C55 end

        return (array) $returnValue;
    }

    /**
     * Short description of method setConstants
     *
     * @access private
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  array constants
     * @return void
     */
    private function setConstants($constants)
    {
        // section -64--88-0-2--ea43850:13ae1d8a335:-8000:0000000000001C78 begin
        $this->constants = $constants;
        // section -64--88-0-2--ea43850:13ae1d8a335:-8000:0000000000001C78 end
    }

} /* end of class common_ext_Manifest */

?>