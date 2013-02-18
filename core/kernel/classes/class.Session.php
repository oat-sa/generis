<?php

error_reporting(E_ALL);

/**
 * returns conencted user(string)
 *
 * @author patrick.plichart@tudor.lu
 * @package core
 * @subpackage kernel_classes
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 10-13-1--31-64e54c36:1190f0455d3:-8000:000000000000071F-includes begin
// section 10-13-1--31-64e54c36:1190f0455d3:-8000:000000000000071F-includes end

/* user defined constants */
// section 10-13-1--31-64e54c36:1190f0455d3:-8000:000000000000071F-constants begin
// section 10-13-1--31-64e54c36:1190f0455d3:-8000:000000000000071F-constants end

/**
 * returns conencted user(string)
 *
 * @access private
 * @author patrick.plichart@tudor.lu
 * @package core
 * @subpackage kernel_classes
 */
class core_kernel_classes_Session
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute instance
     *
     * @access private
     * @var Session
     */
    private static $instance = null;

    /**
     * Short description of attribute dataLanguage
     *
     * @access private
     * @var string
     */
    private $dataLanguage = '';

    /**
     * Short description of attribute interfaceLanguage
     *
     * @access private
     * @var string
     */
    private $interfaceLanguage = '';

    /**
     * Short description of attribute userLogin
     *
     * @access private
     * @var string
     */
    private $userLogin = '';

    /**
     * Short description of attribute userUri
     *
     * @access private
     * @var string
     */
    private $userUri = '';

    /**
     * Short description of attribute defaultLg
     *
     * @access public
     * @var string
     */
    public $defaultLg = '';

    /**
     * Short description of attribute loadedModels
     *
     * @access protected
     * @var array
     */
    protected $loadedModels = array();

    /**
     * Short description of attribute updatableModels
     *
     * @access protected
     * @var array
     */
    protected $updatableModels = array();

    /**
     * Short description of attribute userRoles
     *
     * @access private
     * @var array
     */
    private $userRoles = array();

    // --- OPERATIONS ---

    /**
     * Short description of method singleton
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return core_kernel_classes_Session
     */
    public static function singleton()
    {
        $returnValue = null;

        // section 10-13-1--31--7858878e:119b84cada6:-8000:0000000000000AE0 begin
        $session = PHPSession::singleton();
        
		if (!isset(self::$instance) || is_null(self::$instance)) {
			if ($session->hasAttribute('generis_session')) {
				self::$instance = $session->getAttribute('generis_session');
			} else {
				self::$instance = new self();
				$session->setAttribute('generis_session', self::$instance);
			}
		}
		$returnValue = self::$instance;

        // section 10-13-1--31--7858878e:119b84cada6:-8000:0000000000000AE0 end

        return $returnValue;
    }

    /**
     * This function is used to reset the session
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return void
     */
    public function reset()
    {
        // section 10-13-1--31--626b8103:11b358dabdb:-8000:0000000000000D63 begin
		common_Logger::d('resetting session');
		$this->defaultLg = DEFAULT_LANG;
		$this->setDataLanguage('');
		$this->setInterfaceLanguage('');

		$this->userLogin	= '';
		$this->userUri		= null;
		$this->userRoles	= array();
		$this->update();
        // section 10-13-1--31--626b8103:11b358dabdb:-8000:0000000000000D63 end
    }

    /**
     * Short description of method __construct
     *
     * @access private
     * @author Joel Bout, <joel.bout@tudor.lu>
     */
    private function __construct()
    {
        // section 10-13-1--31--7714f845:11984dc9fef:-8000:0000000000000AE7 begin
		
		//active  models needed by extension
    	$extensionManager = common_ext_ExtensionsManager::singleton();
		foreach ($extensionManager->getModelsToLoad() as $model){
			$this->loadModel($model);
		}
		
		//load local model
		$this->loadModel(LOCAL_NAMESPACE);
		
		//get updatable models
		$this->updatableModels = $extensionManager->getUpdatableModels ();
		
		//set default language
		$this->defaultLg			= DEFAULT_LANG;
		$this->interfaceLanguage	= '';
		$this->dataLanguage			= '';
		
        // section 10-13-1--31--7714f845:11984dc9fef:-8000:0000000000000AE7 end
    }

    /**
     * please use setDataLanguage() instead
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @deprecated
     * @param  string lg
     * @return boolean
     */
    public function setLg($lg)
    {
        $returnValue = (bool) false;

        // section 10-13-1--31-64e54c36:1190f0455d3:-8000:0000000000000752 begin
        $returnValue = $this->setDataLanguage($lg);
        // section 10-13-1--31-64e54c36:1190f0455d3:-8000:0000000000000752 end

        return (bool) $returnValue;
    }

    /**
     * please use getDataLanguage() instead
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @deprecated
     * @return string
     */
    public function getLg()
    {
        $returnValue = (string) '';

        // section 10-13-1--31-64e54c36:1190f0455d3:-8000:0000000000000754 begin
        $returnValue = $this->getDataLanguage();
        // section 10-13-1--31-64e54c36:1190f0455d3:-8000:0000000000000754 end

        return (string) $returnValue;
    }

    /**
     * Short description of method getNameSpace
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return string
     */
    public function getNameSpace()
    {
        $returnValue = (string) '';

        // section 10-13-1--31--63d751b4:11914bbbbc4:-8000:0000000000000B0C begin
		$returnValue= LOCAL_NAMESPACE;
        // section 10-13-1--31--63d751b4:11914bbbbc4:-8000:0000000000000B0C end

        return (string) $returnValue;
    }

    /**
     * Short description of method getUserLogin
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return string
     */
    public function getUserLogin()
    {
        $returnValue = (string) '';

        // section 10-13-1--31-42d46662:11bb6ef4845:-8000:0000000000000D59 begin
		$returnValue = $this->userLogin;
        // section 10-13-1--31-42d46662:11bb6ef4845:-8000:0000000000000D59 end

        return (string) $returnValue;
    }

    /**
     * Short description of method getUserUri
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return string
     */
    public function getUserUri()
    {
        $returnValue = (string) '';

        // section 127-0-1-1--104cb9d8:137c774c247:-8000:0000000000001A07 begin
        return $this->userUri;
        // section 127-0-1-1--104cb9d8:137c774c247:-8000:0000000000001A07 end

        return (string) $returnValue;
    }

    /**
     * Short description of method setUser
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  string login
     * @param  string uri
     * @param  array roles
     * @return mixed
     */
    public function setUser($login, $uri = null, $roles = array())
    {
        // section 127-0-1-1--14f68f95:12f59b39209:-8000:000000000000143A begin
    	$this->userLogin	= $login;
    	$this->userUri		= $uri;
    	$this->userRoles	= $roles;
        // section 127-0-1-1--14f68f95:12f59b39209:-8000:000000000000143A end
    }

    /**
     * Short description of method loadModel
     *
     * @access protected
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  string model
     * @return boolean
     */
    protected function loadModel($model)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--ded7727:12f59911cd7:-8000:0000000000001433 begin
        
        if(!preg_match("/#$/", $model)){
        	$model .= '#';
        }
        if(in_array($model, $this->loadedModels)){
        	$returnValue = true;
        }
        else{
        	$nsManager = common_ext_NamespaceManager::singleton();
        	foreach($nsManager->getAllNamespaces() as $namespace){
        		if($namespace->getUri() == $model){
        			$this->loadedModels[$namespace->getModelId()] = $model;
        			$returnValue = true;
        			break;
        		}
        	}
        }
        // section 127-0-1-1--ded7727:12f59911cd7:-8000:0000000000001433 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getLoadedModels
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return array
     */
    public function getLoadedModels()
    {
        $returnValue = array();

        // section 127-0-1-1--14f68f95:12f59b39209:-8000:0000000000001438 begin
        
        $returnValue = $this->loadedModels;
        
        // section 127-0-1-1--14f68f95:12f59b39209:-8000:0000000000001438 end

        return (array) $returnValue;
    }

    /**
     * Short description of method getUpdatableModels
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return array
     */
    public function getUpdatableModels()
    {
        $returnValue = array();

        // section 127-0-1-1--450598c3:13175ea282e:-8000:0000000000003C47 begin
        $returnValue = $this->updatableModels;        
        // section 127-0-1-1--450598c3:13175ea282e:-8000:0000000000003C47 end

        return (array) $returnValue;
    }

    /**
     * Unload a model from the current session.
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string model The model URI.
     * @return boolean
     */
    public function unloadModel($model)
    {
        $returnValue = (bool) false;

        // section 10-13-1-85--3885cdb:135aa86a412:-8000:0000000000001941 begin
        foreach ($this->loadedModels as $loadedModel){
        	if ($loadedModel == $model){
        		unset($loadedModel);
        		$returnValue = true;
        		break;
        	}
        }
        // section 10-13-1-85--3885cdb:135aa86a412:-8000:0000000000001941 end

        return (bool) $returnValue;
    }

    /**
     * Updates the session by reloading references to models.
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return void
     */
    public function update()
    {
        // section 10-13-1-85-91f6d5e:135c7e94b2b:-8000:0000000000002A48 begin
        $this->loadedModels = array();
        $extensionManager = common_ext_ExtensionsManager::singleton();
        common_ext_NamespaceManager::singleton()->reset();
		foreach ($extensionManager->getModelsToLoad() as $model){
			$this->loadModel($model);
		}
		
		//load local model
		$this->loadModel(LOCAL_NAMESPACE);
		
		//get updatable models
		$this->updatableModels = array();
		$this->updatableModels = $extensionManager->getUpdatableModels ();
        // section 10-13-1-85-91f6d5e:135c7e94b2b:-8000:0000000000002A48 end
    }

    /**
     * Behaviour to adopt at PHP __wakup time.
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return void
     */
    public function __wakeup()
    {
        // section 10-13-1-85-91f6d5e:135c7e94b2b:-8000:0000000000002A4B begin
        $this->update();
        // section 10-13-1-85-91f6d5e:135c7e94b2b:-8000:0000000000002A4B end
    }

    /**
     * Short description of method setDataLanguage
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string language
     * @return mixed
     */
    public function setDataLanguage($language)
    {
        // section 127-0-1-1--104cb9d8:137c774c247:-8000:0000000000001A13 begin
        if (empty($language)) {
        	$this->dataLanguage = '';
        } else {
        	$this->dataLanguage = $language;
        }
	    common_Logger::d('Set data language to '.$language, array('GENERIS', 'I18N'));
        // section 127-0-1-1--104cb9d8:137c774c247:-8000:0000000000001A13 end
    }

    /**
     * returns the language code to use for data
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return string
     */
    public function getDataLanguage()
    {
        $returnValue = (string) '';

        // section 127-0-1-1--104cb9d8:137c774c247:-8000:0000000000001A0E begin
        $returnValue = empty($this->dataLanguage) ? $this->defaultLg : $this->dataLanguage;
        // section 127-0-1-1--104cb9d8:137c774c247:-8000:0000000000001A0E end

        return (string) $returnValue;
    }

    /**
     * Short description of method setInterfaceLanguage
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string language
     * @return mixed
     */
    public function setInterfaceLanguage($language)
    {
        // section 127-0-1-1--104cb9d8:137c774c247:-8000:0000000000001A15 begin
		if (empty($language)) {
        	$this->interfaceLanguage = '';
        } else {
        	$this->interfaceLanguage = $language;
        }
	    common_Logger::d('Set interface language to '.$language, array('GENERIS', 'I18N'));
        // section 127-0-1-1--104cb9d8:137c774c247:-8000:0000000000001A15 end
    }

    /**
     * returns the language code associated with user interactions
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return string
     */
    public function getInterfaceLanguage()
    {
        $returnValue = (string) '';

        // section 127-0-1-1--104cb9d8:137c774c247:-8000:0000000000001A0C begin
		$returnValue = empty($this->interfaceLanguage) ? $this->defaultLg : $this->interfaceLanguage;
        // section 127-0-1-1--104cb9d8:137c774c247:-8000:0000000000001A0C end

        return (string) $returnValue;
    }

    /**
     * returns the roles of the current user
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return array
     */
    public function getUserRoles()
    {
        $returnValue = array();

        // section 127-0-1-1--67a0c37:137dbbe2925:-8000:0000000000001A0F begin
        return $this->userRoles;
        // section 127-0-1-1--67a0c37:137dbbe2925:-8000:0000000000001A0F end

        return (array) $returnValue;
    }

} /* end of class core_kernel_classes_Session */

?>