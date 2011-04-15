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
/*
require_once('core/kernel/model.php');
require_once('core/kernel/rdfmodel.php');
require_once('core/kernel/rdfsmodel.php');
require_once('core/kernel/modelManager.php');
require_once('core/kernel/accesBD.php');
*/
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
     * Short description of attribute sessionID
     *
     * @access private
     * @var int
     */
    private $sessionID = 0;

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

    // --- OPERATIONS ---

    /**
     * Short description of method singleton
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string uri
     * @param  string module
     * @return core_kernel_classes_Session
     */
    public static function singleton($uri = "", $module = '')
    {
        $returnValue = null;

        // section 10-13-1--31--7858878e:119b84cada6:-8000:0000000000000AE0 begin
		if (!isset(self::$instance)) {
			$c = __CLASS__;
			self::$instance = new $c($uri,$module);
		}
		$returnValue = self::$instance;

        // section 10-13-1--31--7858878e:119b84cada6:-8000:0000000000000AE0 end

        return $returnValue;
    }

    /**
     * Short description of method __construct
     *
     * @access private
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string uri
     * @param  string module
     * @return string
     */
    private function __construct($uri, $module)
    {
        $returnValue = (string) '';

        // section 10-13-1--31--7714f845:11984dc9fef:-8000:0000000000000AE7 begin
	    $userService = core_kernel_users_Service::singleton();
        if(!$userService->isASessionOpened()){
			throw new common_Exception('Fail openning session, check if you log in');
	    }
	    
       	//initialize the dbWrapper
		core_kernel_classes_DbWrapper::singleton($module);
		
		
		//active  models needed by extension
    	$extensionManager = common_ext_ExtensionsManager::singleton();
		foreach ($extensionManager->getModelsToLoad() as $model){
			$this->loadModel($model);
		}
		
		$this->defaultLg = DEFAULT_LANG;

		
        // section 10-13-1--31--7714f845:11984dc9fef:-8000:0000000000000AE7 end

        return (string) $returnValue;
    }

    /**
     * Short description of method setLg
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string lg
     * @return boolean
     */
    public function setLg($lg)
    {
        $returnValue = (bool) false;

        // section 10-13-1--31-64e54c36:1190f0455d3:-8000:0000000000000752 begin
		$this->lg = $lg;
        // section 10-13-1--31-64e54c36:1190f0455d3:-8000:0000000000000752 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getLg
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
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
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
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
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
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
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
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
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
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
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
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
	        $dbWrapper = core_kernel_classes_DbWrapper::singleton();
	         
	        $query = 'SELECT modelID FROM models where baseURI = ?';
	        $result = $dbWrapper->execSql($query, array($model));
	        while($row = $result->fetchRow()){
	        	$this->loadedModels[$row['modelID']] = $model;
	        	$resturnValue = true;
	        }
        }
        // section 127-0-1-1--ded7727:12f59911cd7:-8000:0000000000001433 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getLoadedModels
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
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
     * This function is used to reset the static context to the instance , if
     * instance was created in another execution context (frontcontroller will
     * the singleton in the php session then wilol restore it for further http
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Session staticInstance
     * @return void
     */
    public function reset( core_kernel_classes_Session $staticInstance)
    {
        // section 10-13-1--31--626b8103:11b358dabdb:-8000:0000000000000D63 begin
		self::$instance = $staticInstance;
        // section 10-13-1--31--626b8103:11b358dabdb:-8000:0000000000000D63 end
    }

} /* end of class core_kernel_classes_Session */

?>