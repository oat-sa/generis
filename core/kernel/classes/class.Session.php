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
require_once('core/kernel/model.php');
require_once('core/kernel/rdfmodel.php');
require_once('core/kernel/rdfsmodel.php');
require_once('core/kernel/modelManager.php');
require_once('core/kernel/accesBD.php');
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
     * Short description of attribute model
     *
     * @access public
     * @var Object
     */
    public $model = null;

    /**
     * Short description of attribute instance
     *
     * @access private
     * @var Session
     */
    private static $instance = null;

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access private
     * @author firstname and lastname of author, <author@example.org>
     * @param  string uri
     * @param  string module
     * @return string
     */
    private function __construct($uri, $module)
    {
        $returnValue = (string) '';

        // section 10-13-1--31--7714f845:11984dc9fef:-8000:0000000000000AE7 begin
	    if(!core_kernel_users_Service::singleton()->isASessionOpened()){
			throw new common_Exception('Fail openning session, check if you log in');
	    }
       	
		$dbWrapper = core_kernel_classes_DbWrapper::singleton($module);
		

		
		
		$this->model = new generisrdfsmodel();

		/*
		 * When connecting through the new api, modelmanager is no more used, 
		 * but some methods of models are relying on some piece of informations
		 * coming from it. In order to not alterate rdfsmodel and rdfmodel which 
		 * are still used through the old api (retro compatibility), the connection 
		 * and the required data are inserted in the following section
		 *  
		 **/

		//put the connection to adodb into model, some of its method are relying on this attribute
		$userGroups = "";
		$this->model->con = $dbWrapper->dbConnector;
		//$this->model->updateIfneededModelofDatabase();
		$this->model->modelManager = new modelManager() ;

		$this->model->modelManager->usergroup =explode(",",$userGroups);
		
		$mask=array("read"=>"yyy[admin,administrators,authors]","edit"=>"yyy[admin,administrators,authors]","delete"=>"yyy[admin,administrators,authors]");
		
		$this->model->modelManager->umask = $mask ;
			
		$this->model->modelManager->deflg = DEFAULT_LANG;
		
		$this->model->getFilter("read");
		$this->model->getFilter("edit");
		$this->model->getFilter("delete");
		
		/*End of the section for retrocompatibility*/
		
		$modelURI = $this->getNameSpace();
		
		//active the local model
		$this->model->setModelURI($modelURI);
		
		//active  models needed by extension
    	$extensionManager = common_ext_ExtensionsManager::singleton();
		
		foreach ($extensionManager->getModelsToLoad() as $model){
			$this->model->loadModel($model);
		}
		
		$this->defaultLg = DEFAULT_LANG;

		
        // section 10-13-1--31--7714f845:11984dc9fef:-8000:0000000000000AE7 end

        return (string) $returnValue;
    }

    /**
     * Short description of method setLg
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  string lg
     * @return boolean
     */
    public function setLg($lg)
    {
        $returnValue = (bool) false;

        // section 10-13-1--31-64e54c36:1190f0455d3:-8000:0000000000000752 begin
		$this->lg = $lg;

		//adapt the old api
		$this->model->modelManager->lg=$lg;

        // section 10-13-1--31-64e54c36:1190f0455d3:-8000:0000000000000752 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getLg
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
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
     * @author firstname and lastname of author, <author@example.org>
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
     * @author firstname and lastname of author, <author@example.org>
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
     * Short description of method singleton
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
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
     * This function is used to reset the static context to the instance , if
     * instance was created in another execution context (frontcontroller will
     * the singleton in the php session then wilol restore it for further http
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  Session staticInstance
     * @return void
     */
    public function reset( core_kernel_classes_Session $staticInstance)
    {
        // section 10-13-1--31--626b8103:11b358dabdb:-8000:0000000000000D63 begin
		self::$instance = $staticInstance;
        // section 10-13-1--31--626b8103:11b358dabdb:-8000:0000000000000D63 end
    }

    /**
     * Short description of method getUser
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
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

} /* end of class core_kernel_classes_Session */

?>