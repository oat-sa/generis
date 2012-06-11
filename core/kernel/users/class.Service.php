<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - core/kernel/users/class.Service.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 08.06.2012, 14:25:42 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Joel Bout, <joel.bout@tudor.lu>
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
 * @author Joel Bout, <joel.bout@tudor.lu>
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

    // --- OPERATIONS ---

    /**
     * Short description of method loginExists
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
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
        	array('like' => false, 'recursive' => 1)
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
     * @author Joel Bout, <joel.bout@tudor.lu>
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

        $classRole		=  new core_kernel_classes_Class($roleUri);
        $newRole		= $classRole->createInstance($label,$comment);
        $returnValue	= new core_kernel_classes_Class($newRole->getUri());
        $returnValue->setPropertyValue(new core_kernel_classes_Property(RDF_SUBCLASSOF),CLASS_GENERIS_USER);

        // section -87--2--3--76-270abbe1:12886b059d2:-8000:0000000000001819 end

        return $returnValue;
    }

    /**
     * Short description of method removeRole
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
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
     * @author Joel Bout, <joel.bout@tudor.lu>
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
     * @author Joel Bout, <joel.bout@tudor.lu>
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
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string login
     * @param  string password
     * @param  string role
     * @return boolean
     */
    public function login($login, $password, core_kernel_classes_Class $role)
    {
        $returnValue = (bool) false;

        // section -87--2--3--76-270abbe1:12886b059d2:-8000:0000000000001834 begin
		
        if(empty($role)){
        	throw new common_Exception('no role provided for login');
        }
	        
		if(!is_string($login)){
			throw new core_kernel_users_Exception('The login must be of "string" type');
			return $returnValue;
		}
		$login = trim($login);
		if(empty($login)){
			throw new core_kernel_users_Exception('The login cannot be empty');
			return $returnValue;
		}
		if(!is_string($password)){
			throw new core_kernel_users_Exception('The password must be of "string" type');
			return $returnValue;
		}
			
		/* get all the concrete roles
		
		$roleClass = new core_kernel_classes_Class($role);
		$userClasses = $roleClass->getInstances(true);
		
        //check login
        $pip = array_shift($userClasses);
		$pipclass = new core_kernel_classes_Class($pip->getUri());
		*/
		$users = $role->searchInstances(
			array(
				PROPERTY_USER_LOGIN 	=> $login,
				PROPERTY_USER_PASSWORD	=> $password
			), 
			array(
				'like' 				=> false,
				'recursive'			=> 1,
//				'additionalClasses'	=> $userClasses
			)
		);
        
		if (count($users) != 1) {
			$this->logout();
		} else {
			$returnValue = true;
		}
		
		if ($returnValue) {
			$this->userResource = reset($users);
			
			$roles = tao_models_classes_UserService::singleton()->getUserRoles($this->userResource);
				
			core_kernel_classes_Session::singleton()->reset();
			$session = core_kernel_classes_Session::singleton();
			$session->setUser($login, $this->userResource->getUri(), $roles);
		}
        // section -87--2--3--76-270abbe1:12886b059d2:-8000:0000000000001834 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getOneUser
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
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
    		array('like' => false, 'recursive' => 1)
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
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return mixed
     */
    private function __construct()
    {
        // section -87--2--3--76-270abbe1:12886b059d2:-8000:000000000000183B begin
         if(!$this->isASessionOpened()) { 
         	
         	//init a fake session to do the 1st cheks
		    core_kernel_classes_Session::singleton();
		    $this->db = core_kernel_classes_DbWrapper::singleton();
         }
		    
        // section -87--2--3--76-270abbe1:12886b059d2:-8000:000000000000183B end
    }

    /**
     * Short description of method singleton
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
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
     * Short description of method isASessionOpened
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return boolean
     */
    public function isASessionOpened()
    {
        $returnValue = (bool) false;

        // section -87--2--3--76-16cc328d:128a5fc99af:-8000:0000000000002E9D begin
        $userUri = core_kernel_classes_Session::singleton()->getUserUri();
        $returnValue = !is_null($userUri);
        // section -87--2--3--76-16cc328d:128a5fc99af:-8000:0000000000002E9D end

        return (bool) $returnValue;
    }

    /**
     * Short description of method logout
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return boolean
     */
    public function logout()
    {
        $returnValue = (bool) false;

        // section -87--2--3--76-16cc328d:128a5fc99af:-8000:0000000000002EB5 begin
        core_kernel_classes_Session::singleton()->reset();
        $returnValue = true;
        // section -87--2--3--76-16cc328d:128a5fc99af:-8000:0000000000002EB5 end

        return (bool) $returnValue;
    }

    /**
     * used in conjunction with the callback validator
     * to test the pasword entered
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  string password raw password, unencrypted
     * @param  Resource user
     * @return boolean
     */
    public function isPasswordValid($password,  core_kernel_classes_Resource $user)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--dd65dd6:137c0b39408:-8000:00000000000019FE begin
		if(!is_string($password)){
			throw new core_kernel_users_Exception('The password must be of "string" type');
			return $returnValue;
		}
		
		$userPass = $user->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_USER_PASSWORD));
		$returnValue = md5($password) == $userPass;
        // section 127-0-1-1--dd65dd6:137c0b39408:-8000:00000000000019FE end

        return (bool) $returnValue;
    }

} /* end of class core_kernel_users_Service */

?>