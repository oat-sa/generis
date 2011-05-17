<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - core/kernel/users/class.Service.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 04.05.2011, 15:31:32 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package core
 * @subpackage kernel_users
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section -87--2--3--76-270abbe1:12886b059d2:-8000:0000000000001815-includes begin
// section -87--2--3--76-270abbe1:12886b059d2:-8000:0000000000001815-includes end

/* user defined constants */
// section -87--2--3--76-270abbe1:12886b059d2:-8000:0000000000001815-constants begin
// section -87--2--3--76-270abbe1:12886b059d2:-8000:0000000000001815-constants end

/**
 * Short description of class core_kernel_users_Service
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package core
 * @subpackage kernel_users
 */
class core_kernel_users_Service
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute userResource
     *
     * @access private
     * @var Resource
     */
    private $userResource = null;

    /**
     * Short description of attribute instance
     *
     * @access private
     * @var Service
     */
    private static $instance = null;

    /**
     * the key to retrieve the authentication token in the presistent session
     *
     * @access public
     * @var string
     */
    const AUTH_TOKEN_KEY = 'auth_id';

    /**
     * Short description of attribute module
     *
     * @access private
     * @var string
     */
    private $module = '';

    // --- OPERATIONS ---

    /**
     * Short description of method loginExists
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string login
     * @param  Class class
     * @return boolean
     */
    public function loginExists($login,  core_kernel_classes_Class $class = null)
    {
        $returnValue = (bool) false;

        // section -87--2--3--76-270abbe1:12886b059d2:-8000:0000000000001816 begin
		
        if(is_null($class)){
        	$class = new core_kernel_classes_Class(CLASS_GENERIS_USER);
        }
        $users = $class->searchInstances(
        	array(PROPERTY_USER_LOGIN => $login), 
        	array('like' => true, 'recursive' => true)
        );
        if(count($users) > 0){
        	$returnValue = true;
        }
		
		
        // section -87--2--3--76-270abbe1:12886b059d2:-8000:0000000000001816 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method addRole
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string label
     * @param  string comment
     * @param  string parentRole
     * @return core_kernel_classes_Class
     */
    public function addRole($label = '', $comment = '', $parentRole = null)
    {
        $returnValue = null;

        // section -87--2--3--76-270abbe1:12886b059d2:-8000:0000000000001819 begin

        $roleUri = $parentRole != null ? $parentRole->uriResource :  CLASS_ROLE;

        $classRole =  new core_kernel_classes_Class($roleUri);
        $returnValue = $classRole->createInstance($label,$comment);
        $returnValue->setPropertyValue(new core_kernel_classes_Property(RDF_SUBCLASSOF),CLASS_GENERIS_USER);

        // section -87--2--3--76-270abbe1:12886b059d2:-8000:0000000000001819 end

        return $returnValue;
    }

    /**
     * Short description of method removeRole
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Resource role
     * @return boolean
     */
    public function removeRole( core_kernel_classes_Resource $role)
    {
        $returnValue = (bool) false;

        // section -87--2--3--76-270abbe1:12886b059d2:-8000:0000000000001828 begin
        $returnValue = $role->delete();
        // section -87--2--3--76-270abbe1:12886b059d2:-8000:0000000000001828 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method addUser
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string login
     * @param  string password
     * @param  Resource role
     * @return core_kernel_classes_Resource
     */
    public function addUser($login, $password,  core_kernel_classes_Resource $role)
    {
        $returnValue = null;

        // section -87--2--3--76-270abbe1:12886b059d2:-8000:000000000000182C begin
        if($this->loginExists($login)){
        	throw new core_kernel_users_Exception('login already taken',core_kernel_users_Exception::LOGIN_EXITS);
        }
        $roleClass = new core_kernel_classes_Class($role->uriResource);
        $returnValue = $roleClass->createInstance('User_'.$login , 'Created on'. date(DATE_ISO8601));
        $loginProp = new core_kernel_classes_Property(PROPERTY_USER_LOGIN);
		$passProp = new core_kernel_classes_Property(PROPERTY_USER_PASSWORD);
		
		$returnValue->setPropertyValue($loginProp,$login);
		$returnValue->setPropertyValue($passProp,$password);
		
        // section -87--2--3--76-270abbe1:12886b059d2:-8000:000000000000182C end

        return $returnValue;
    }

    /**
     * Short description of method removeUser
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Resource user
     * @return boolean
     */
    public function removeUser( core_kernel_classes_Resource $user)
    {
        $returnValue = (bool) false;

        // section -87--2--3--76-270abbe1:12886b059d2:-8000:0000000000001831 begin
       	$returnValue = $user->delete();
        // section -87--2--3--76-270abbe1:12886b059d2:-8000:0000000000001831 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method login
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string login
     * @param  string password
     * @param  string role
     * @return boolean
     */
    public function login($login, $password, $role)
    {
        $returnValue = (bool) false;

        // section -87--2--3--76-270abbe1:12886b059d2:-8000:0000000000001834 begin
		
        if(!empty($role)){
	        
        	$userClass = new core_kernel_classes_Class(CLASS_GENERIS_USER);
	        
	        //check login
			$users = $userClass->searchInstances(
				array(
					PROPERTY_USER_LOGIN 	=> $login,
					PROPERTY_USER_PASSWORD	=> $password
				), 
				array(
					'like' 		=> true,
					'recursive'	=> true
				)
			);
			$user = null;
			foreach($users as $foundUser){
				$user = $foundUser;
				break;
			}
			if(is_null($user)){
				$this->logout();
				throw new core_kernel_users_Exception('Authentication failed',core_kernel_users_Exception::BAD_LOGIN );
			}
			
				
			$this->loginApi($user->uriResource);
			
			//check Role
			$this->userResource = $user;
			
			$roleClass =  new core_kernel_classes_Class($role);
			$acceptedRole =  array_merge(array($role) , array_keys($roleClass->getInstances(true))); 
			
			$returnValue = false; 
			foreach ($this->userResource->getType() as $userRole){
				if(in_array($userRole->uriResource, $acceptedRole)){
					$returnValue = true;
					break;
				}
			}
        }

		if(!$returnValue) {
			$this->logout();
			throw new core_kernel_users_Exception('Authentication failed : Role do not match',core_kernel_users_Exception::BAD_PASSWORD );
		}
		
		
        // section -87--2--3--76-270abbe1:12886b059d2:-8000:0000000000001834 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getOneUser
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string login
     * @param  Class class
     * @return core_kernel_classes_Resource
     */
    public function getOneUser($login,  core_kernel_classes_Class $class = null)
    {
        $returnValue = null;

        // section -87--2--3--76-270abbe1:12886b059d2:-8000:0000000000001839 begin
		
    	if(is_null($class)){
        	$class = new core_kernel_classes_Class(CLASS_GENERIS_USER);
    	}
        
    	$users = $class->searchInstances(
    		array(PROPERTY_USER_LOGIN => $login), 
    		array('like' => false, 'recursive' => true)
    	);
    	foreach($users as $user){
    		$returnValue = $user;
    		break;
    	}
    	
        // section -87--2--3--76-270abbe1:12886b059d2:-8000:0000000000001839 end

        return $returnValue;
    }

    /**
     * Short description of method __construct
     *
     * @access private
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return mixed
     */
    private function __construct()
    {
        // section -87--2--3--76-270abbe1:12886b059d2:-8000:000000000000183B begin
        $this->module = DATABASE_NAME;
        
         if(!$this->isASessionOpened()) { 
         	
         	//init a fake session to do the 1st cheks
		    core_kernel_classes_Session::singleton(true, $this->module);
		    $this->db = core_kernel_classes_DbWrapper::singleton($this->module);
         }
		    
        // section -87--2--3--76-270abbe1:12886b059d2:-8000:000000000000183B end
    }

    /**
     * Short description of method singleton
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return core_kernel_users_Service
     */
    public static function singleton()
    {
        $returnValue = null;

        // section -87--2--3--76-16cc328d:128a5fc99af:-8000:0000000000002E95 begin
        if (!isset(self::$instance)) {
			$c = __CLASS__;
			self::$instance = new $c();
		}
		$returnValue = self::$instance;
        // section -87--2--3--76-16cc328d:128a5fc99af:-8000:0000000000002E95 end

        return $returnValue;
    }

    /**
     * Short description of method loginApi
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string uri
     * @return boolean
     */
    public function loginApi($uri)
    {
        $returnValue = (bool) false;

        // section -87--2--3--76-16cc328d:128a5fc99af:-8000:0000000000002E9B begin
        
    	if(Session::hasAttribute('generis_session')){	
        	//re-init the objects from the session
        	
        	core_kernel_classes_Session::reset(Session::getAttribute('generis_session'));
			core_kernel_classes_DbWrapper::singleton(Session::getAttribute('generis_module'));
			$returnValue = true ;
        }
        
    	//check if the URI of the user exists
        if(!$this->isASessionOpened()) { 
        	
        	try{
        		
	        	Session::setAttribute(self::AUTH_TOKEN_KEY,	$uri);
			       		
	       		//Initialize the real generis session 
	       		core_kernel_classes_Session::reset();
		        $session = core_kernel_classes_Session::singleton($uri, $this->module);
		       	
		       
		        //save the current generis session
				Session::setAttribute('generis_session', $session);
				Session::setAttribute('generis_module',  $this->module);
	        	
				//get the login of the user
				$this->userResource = new core_kernel_classes_Resource($uri);
        		$login = $this->userResource->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_USER_LOGIN));
        		$session->setUser($login->literal);
        		
        	}
        	catch(common_Exception $ce){
        		//the login must be unique
        		core_kernel_classes_Session::reset();
        		Session::removeAttribute(self::AUTH_TOKEN_KEY);
        		Session::removeAttribute('generis_session');
        		Session::removeAttribute('generis_module');
        		return false;	
        	}
        	$returnValue = true ;
        }
        
    	 
        // section -87--2--3--76-16cc328d:128a5fc99af:-8000:0000000000002E9B end

        return (bool) $returnValue;
    }

    /**
     * Short description of method isASessionOpened
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return boolean
     */
    public function isASessionOpened()
    {
        $returnValue = (bool) false;

        // section -87--2--3--76-16cc328d:128a5fc99af:-8000:0000000000002E9D begin
		if(Session::hasAttribute(self::AUTH_TOKEN_KEY)) {
        	$returnValue = true;
        }
        // section -87--2--3--76-16cc328d:128a5fc99af:-8000:0000000000002E9D end

        return (bool) $returnValue;
    }

    /**
     * Short description of method logout
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return boolean
     */
    public function logout()
    {
        $returnValue = (bool) false;

        // section -87--2--3--76-16cc328d:128a5fc99af:-8000:0000000000002EB5 begin
        core_kernel_classes_Session::reset();
        Session::removeAttribute(self::AUTH_TOKEN_KEY);
        $returnValue = true;
        // section -87--2--3--76-16cc328d:128a5fc99af:-8000:0000000000002EB5 end

        return (bool) $returnValue;
    }

} /* end of class core_kernel_users_Service */

?>