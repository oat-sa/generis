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
     * Short description of attribute lg
     *
     * @access private
     * @var string
     */
    private $lg = '';

    /**
     * Short description of attribute user
     *
     * @access private
     * @var string
     */
    private $user = '';

    /**
     * Short description of attribute isAdmin
     *
     * @access private
     * @var boolean
     */
    private $isAdmin = false;

    /**
     * Short description of attribute defaultLg
     *
     * @access public
     * @var string
     */
    public $defaultLg = '';

    /**
     * Short description of attribute instance
     *
     * @access private
     * @var Session
     */
    private static $instance = null;

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
		if (!isset(self::$instance) || is_null(self::$instance)) {
			self::$instance = new self();
		}
		$returnValue = self::$instance;

        // section 10-13-1--31--7858878e:119b84cada6:-8000:0000000000000AE0 end

        return $returnValue;
    }

    /**
     * This function is used to reset the static context to the instance, if
     * the instance was created in another execution context
     * (frontcontroller will store the singleton in the php session then
     * will restore it for further http requests)
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Session staticInstance
     * @return void
     */
    public function reset( core_kernel_classes_Session $staticInstance = null)
    {
        // section 10-13-1--31--626b8103:11b358dabdb:-8000:0000000000000D63 begin
		if ($staticInstance !== null){
			self::$instance = $staticInstance;
        }
        else{
        	$this->defaultLg = DEFAULT_LANG;
        	$this->setLg('');
        	$this->setUser('');
        }
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
		$this->defaultLg = DEFAULT_LANG;
		
        // section 10-13-1--31--7714f845:11984dc9fef:-8000:0000000000000AE7 end
    }

    /**
     * Short description of method setLg
     *
     * @access public
     * @author C�dric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  string lg
     * @return boolean
     */
    public function setLg($lg)
    {
        $returnValue = (bool) false;

        // section 10-13-1--31-64e54c36:1190f0455d3:-8000:0000000000000752 begin
        if(empty($lg)){
                //if lg null lg is set to defaultLG
                $lg=$this->defaultLg;
        }
        $this->lg = $lg;
        // section 10-13-1--31-64e54c36:1190f0455d3:-8000:0000000000000752 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getLg
     *
     * @access public
     * @author C�dric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return string
     */
    public function getLg()
    {
        $returnValue = (string) '';

        // section 10-13-1--31-64e54c36:1190f0455d3:-8000:0000000000000754 begin
		$returnValue=$this->lg;
        // section 10-13-1--31-64e54c36:1190f0455d3:-8000:0000000000000754 end

        return (string) $returnValue;
    }

    /**
     * Short description of method getNameSpace
     *
     * @access public
     * @author C�dric Alfonsi, <cedric.alfonsi@tudor.lu>
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
     * Returns array of languages in which data is defined into this module
     *
     * @access public
     * @author C�dric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return array
     */
    public function getLanguages()
    {
        $returnValue = array();

        // section 10-13-1--31--63d751b4:11914bbbbc4:-8000:0000000000000B18 begin
        // section 10-13-1--31--63d751b4:11914bbbbc4:-8000:0000000000000B18 end

        return (array) $returnValue;
    }

    /**
     * Short description of method getUser
     *
     * @access public
     * @author C�dric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return string
     */
    public function getUser()
    {
        $returnValue = (string) '';

        // section 10-13-1--31-42d46662:11bb6ef4845:-8000:0000000000000D59 begin
	$returnValue = $this->user;
        // section 10-13-1--31-42d46662:11bb6ef4845:-8000:0000000000000D59 end

        return (string) $returnValue;
    }

    /**
     * Short description of method setUser
     *
     * @access public
     * @author C�dric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  string login
     * @return mixed
     */
    public function setUser($login)
    {
        // section 127-0-1-1--14f68f95:12f59b39209:-8000:000000000000143A begin
        
    	$this->user = $login;
    	
        // section 127-0-1-1--14f68f95:12f59b39209:-8000:000000000000143A end
    }

    /**
     * Short description of method loadModel
     *
     * @access protected
     * @author C�dric Alfonsi, <cedric.alfonsi@tudor.lu>
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
        	$resturnValue = true;
        }
        else{
        	$nsManager = common_ext_NamespaceManager::singleton();
        	foreach($nsManager->getAllNamespaces() as $namespace){
        		if($namespace->getUri() == $model){
        			$this->loadedModels[$namespace->getModelId()] = $model;
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
     * @author C�dric Alfonsi, <cedric.alfonsi@tudor.lu>
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
     * @author C�dric Alfonsi, <cedric.alfonsi@tudor.lu>
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

} /* end of class core_kernel_classes_Session */

?>