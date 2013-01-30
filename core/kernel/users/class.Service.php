<?php

error_reporting(E_ALL);

/**
 * The UserService aims at providing an API to manage Users and Roles witinh
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package core
 * @subpackage kernel_users
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include core_kernel_users_RolesManagement
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 */
require_once('core/kernel/users/interface.RolesManagement.php');

/**
 * include core_kernel_users_UsersManagement
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 */
require_once('core/kernel/users/interface.UsersManagement.php');

/* user defined includes */
// section -87--2--3--76-270abbe1:12886b059d2:-8000:0000000000001815-includes begin
// section -87--2--3--76-270abbe1:12886b059d2:-8000:0000000000001815-includes end

/* user defined constants */
// section -87--2--3--76-270abbe1:12886b059d2:-8000:0000000000001815-constants begin
// section -87--2--3--76-270abbe1:12886b059d2:-8000:0000000000001815-constants end

/**
 * The UserService aims at providing an API to manage Users and Roles witinh
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package core
 * @subpackage kernel_users
 */
class core_kernel_users_Service
        implements core_kernel_users_UsersManagement,
                   core_kernel_users_RolesManagement
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
     * Returns true if the a user with login = $login is currently in the
     * memory of Generis.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string login A string that is used as a Generis user login in the persistent memory.
     * @param  Class class A specific sub class of User where the login must be searched into.
     * @return boolean
     */
    public function loginExists($login,  core_kernel_classes_Class $class = null)
    {
        $returnValue = (bool) false;

        // section 10-13-1-85-789cda43:13c8b795f73:-8000:0000000000001F80 begin
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
        // section 10-13-1-85-789cda43:13c8b795f73:-8000:0000000000001F80 end

        return (bool) $returnValue;
    }

    /**
     * Create a new Generis User with a specific Role. If the $role is not
     * the user will be given the Generis Role.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string login A specific login for the User to create.
     * @param  string password A password for the User to create (md5 hash).
     * @param  Resource role A Role to grant to the new User.
     * @return core_kernel_classes_Resource
     */
    public function addUser($login, $password,  core_kernel_classes_Resource $role = null)
    {
        $returnValue = null;

        // section 10-13-1-85-789cda43:13c8b795f73:-8000:0000000000001FA3 begin
    	if($this->loginExists($login)){
        	throw new core_kernel_users_Exception("Login '${login}' already in use.", core_kernel_users_Exception::LOGIN_EXITS);
        }
        else{
        	$role = (empty($role)) ? new core_kernel_classes_Resource(INSTANCE_ROLE_GENERIS) : $role;
        	
        	$userClass = new core_kernel_classes_Class(CLASS_GENERIS_USER);
        	$newUser = $userClass->createInstance("User ${login}" , 'User Created on ' . date(DATE_ISO8601));
        	
        	if (!empty($newUser)){
        		$loginProperty = new core_kernel_classes_Property(PROPERTY_USER_LOGIN);
        		$passwordProperty = new core_kernel_classes_Property(PROPERTY_USER_PASSWORD);
        		$userRolesProperty = new core_kernel_classes_Property(PROPERTY_USER_ROLES);
        		
        		$newUser->setPropertyValue($loginProperty, $login);
        		$newUser->setPropertyValue($passwordProperty, $password);
        		$newUser->setPropertyValue($userRolesProperty, $role);
        		
        		$returnValue = $newUser;
        	}
        	else{
        		throw new core_kernel_users_Exception("Unable to create user with login = '${login}'.");
        	}
        }
        // section 10-13-1-85-789cda43:13c8b795f73:-8000:0000000000001FA3 end

        return $returnValue;
    }

    /**
     * Remove a Generis User from persistent memory. Bound roles will remain
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource user A reference to the User to be removed from the persistent memory of Generis.
     * @return boolean
     */
    public function removeUser( core_kernel_classes_Resource $user)
    {
        $returnValue = (bool) false;

        // section 10-13-1-85-789cda43:13c8b795f73:-8000:0000000000001FB1 begin
        $returnValue = $user->delete();
        // section 10-13-1-85-789cda43:13c8b795f73:-8000:0000000000001FB1 end

        return (bool) $returnValue;
    }

    /**
     * Get a specific Generis User from the persistent memory of Generis that
     * a specific login. If multiple users have the same login, a UserException
     * be thrown.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string login A Generis User login.
     * @param  Class class A specific sub Class of User where in the User has to be searched in.
     * @return core_kernel_classes_Resource
     */
    public function getOneUser($login,  core_kernel_classes_Class $class = null)
    {
        $returnValue = null;

        // section 10-13-1-85-789cda43:13c8b795f73:-8000:0000000000001FBB begin
    	if(empty($class)){
        	$class = new core_kernel_classes_Class(CLASS_GENERIS_USER);
    	}
        
    	$users = $class->searchInstances(
    		array(PROPERTY_USER_LOGIN => $login), 
    		array('like' => false, 'recursive' => true)
    	);
    	
    	if (count($users) == 1){
    		$returnValue = current($users);	
    	}
    	else if (count($users) > 1){
    		$msg = "More than one user have the same login '${login}'.";
    	}
        // section 10-13-1-85-789cda43:13c8b795f73:-8000:0000000000001FBB end

        return $returnValue;
    }

    /**
     * Indicates if an Authenticated Session is open.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return boolean
     */
    public function isASessionOpened()
    {
        $returnValue = (bool) false;

        // section 10-13-1-85-789cda43:13c8b795f73:-8000:0000000000001FC6 begin
        $userUri = core_kernel_classes_Session::singleton()->getUserUri();
        $returnValue = !is_null($userUri);
        // section 10-13-1-85-789cda43:13c8b795f73:-8000:0000000000001FC6 end

        return (bool) $returnValue;
    }

    /**
     * used in conjunction with the callback validator
     * to test the pasword entered
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string password used in conjunction with the callback validator
to test the pasword entered
     * @param  Resource user used in conjunction with the callback validator
to test the pasword entered
     * @return boolean
     */
    public function isPasswordValid($password,  core_kernel_classes_Resource $user)
    {
        $returnValue = (bool) false;

        // section 10-13-1-85-789cda43:13c8b795f73:-8000:0000000000001FCA begin
        if(!is_string($password)){
			throw new core_kernel_users_Exception('The password must be of "string" type, got '.gettype($password));
		}
		
		$userPass = $user->getUniquePropertyValue(new core_kernel_classes_Property(PROPERTY_USER_PASSWORD));
		$returnValue = md5($password) == $userPass;
        // section 10-13-1-85-789cda43:13c8b795f73:-8000:0000000000001FCA end

        return (bool) $returnValue;
    }

    /**
     * Set the password of a specifc user.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource user The user you want to set the password.
     * @param  string password The md5 hash of the password you want to set to the user.
     * @return void
     */
    public function setPassword( core_kernel_classes_Resource $user, $password)
    {
        // section 10-13-1-85-789cda43:13c8b795f73:-8000:0000000000001FD1 begin
        if(!is_string($password)){
			throw new core_kernel_users_Exception('The password must be of "string" type, got '.gettype($password));
		}
		
		$user->editPropertyValues(new core_kernel_classes_Property(PROPERTY_USER_PASSWORD),md5($password));
        // section 10-13-1-85-789cda43:13c8b795f73:-8000:0000000000001FD1 end
    }

    /**
     * Get the roles that a given user has.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource user A Generis User.
     * @return array
     */
    public function getUserRoles( core_kernel_classes_Resource $user)
    {
        $returnValue = array();

        // section 10-13-1-85-789cda43:13c8b795f73:-8000:0000000000001FD8 begin
        // We use a Depth First Search approach to flatten the Roles Graph.
        $rolesProperty = new core_kernel_classes_Property(PROPERTY_USER_ROLES);
        $rootRoles = $user->getPropertyValuesCollection($rolesProperty);
        
        foreach ($rootRoles->getIterator() as $r){
        	$returnValue[$r->getUri()] = $r;
        	$returnValue = array_merge($returnValue, $this->getIncludedRoles($r));
        }
        
        $returnValue = array_unique($returnValue);
        // section 10-13-1-85-789cda43:13c8b795f73:-8000:0000000000001FD8 end

        return (array) $returnValue;
    }

    /**
     * Indicates if a user is granted with a set of Roles.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource user The User instance you want to check Roles.
     * @param  roles Can be either a single Resource or an array of Resource depicting Role(s).
     * @return boolean
     */
    public function userHasRoles( core_kernel_classes_Resource $user, $roles)
    {
        $returnValue = (bool) false;

        // section 10-13-1-85-789cda43:13c8b795f73:-8000:0000000000001FDE begin
    	if (empty($roles)){
        	throw new InvalidArgumentException('The $roles parameter must not be empty.');	
        }
        else{
        	$roles = (is_array($roles)) ? $roles : array($roles);
        	$searchRoles = array();
        	foreach ($roles as $r){
        		$searchRoles[] = ($r instanceof core_kernel_classes_Resource) ? $r->getUri() : $r;
        	}
        	unset($roles);
        	
        	$userRoles = array_keys($this->getUserRoles($user));
        	$identicalRoles = array_intersect($searchRoles, $userRoles);
        	
        	$returnValue = (count($identicalRoles) === count($searchRoles));
        }
        // section 10-13-1-85-789cda43:13c8b795f73:-8000:0000000000001FDE end

        return (bool) $returnValue;
    }

    /**
     * Attach a Generis Role to a given Generis User. A UserException will be
     * if an error occurs. If the User already has the role, nothing happens.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource user The User you want to attach a Role.
     * @param  Resource role A Role to attach to a User.
     * @return void
     */
    public function attachRole( core_kernel_classes_Resource $user,  core_kernel_classes_Resource $role)
    {
        // section 10-13-1-85-789cda43:13c8b795f73:-8000:0000000000001FEB begin
    	try{
	        if (false === $this->userHasRoles($user, $role)){
	        	$rolesProperty = new core_kernel_classes_Property(PROPERTY_USER_ROLES);
	        	$user->setPropertyValue($rolesProperty, $role);	
	        }
        }
        catch (common_Exception $e){
        	$roleUri = $role->getUri;
        	$userUri = $user->getUri();
        	$msg = "An error occured while attaching role '${roleUri}' to user '${userUri}': " . $e->getMessage();
        	throw new core_kernel_users_Exception($msg);
        }
        // section 10-13-1-85-789cda43:13c8b795f73:-8000:0000000000001FEB end
    }

    /**
     * Short description of method unnatachRole
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource user A Generis user from which you want to unnattach the Generis Role.
     * @param  Resource role The Generis Role you want to Unnatach from the Generis User.
     * @return void
     */
    public function unnatachRole( core_kernel_classes_Resource $user,  core_kernel_classes_Resource $role)
    {
        // section 10-13-1-85-789cda43:13c8b795f73:-8000:0000000000001FF3 begin
    	try{
        	if (true === $this->userHasRoles($user, $role)){
        		$rolesProperty = new core_kernel_classes_Property(PROPERTY_USER_ROLES);
        		$options = array('like' => false, 'pattern' => $role->getUri());
        		$user->removePropertyValues($rolesProperty, $options);
        	}
        }
        catch (common_Exception $e){
        	$roleUri = $role->getUri();
        	$userUri = $user->getUri();
        	$msg = "An error occured while unnataching role '${roleUri}' from user '${userUri}': " . $e->getMessage();	
        }
        // section 10-13-1-85-789cda43:13c8b795f73:-8000:0000000000001FF3 end
    }

    /**
     * Add a role in Generis.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string label The label to apply to the newly created Generis Role.
     * @param  includedRoles The Role(s) to be included in the newly created Generis Role.
Can be either a Resource or an array of Resources.
     * @return core_kernel_classes_Resource
     */
    public function addRole($label, $includedRoles = null)
    {
        $returnValue = null;

        // section 10-13-1-85-789cda43:13c8b795f73:-8000:0000000000002000 begin
        $includedRoles = is_array($includedRoles) ? $includedRoles : array($includedRoles);
		$includedRoles = empty($includedRoles[0]) ? array() : $includedRoles;
		
		$classRole =  new core_kernel_classes_Class(CLASS_ROLE);
		$includesRoleProperty = new core_kernel_classes_Property(PROPERTY_ROLE_INCLUDESROLE);
        $role = $classRole->createInstance($label, "${label} Role");
        
        foreach ($includedRoles as $ir){
        	$role->setPropertyValue($includesRoleProperty, $ir);	
        }
        
        $returnValue = $role;
        // section 10-13-1-85-789cda43:13c8b795f73:-8000:0000000000002000 end

        return $returnValue;
    }

    /**
     * Remove a Generis role from the persistent memory. User References to this
     * will be removed.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource role The Role to remove.
     * @return boolean
     */
    public function removeRole( core_kernel_classes_Resource $role)
    {
        $returnValue = (bool) false;

        // section 10-13-1-85-789cda43:13c8b795f73:-8000:0000000000002008 begin
    	if (GENERIS_CACHE_USERS_ROLES == true && core_kernel_users_Cache::areIncludedRolesInCache($role)){	
        	if (core_kernel_users_Cache::removeIncludedRoles($role) == true){
        		$returnValue = $role->delete(true);	// delete references to this role!
        	}
        	else{
        		$roleUri = $role->getUri();
        		$msg = "An error occured while removing role '${roleUri}'. It could not be deleted from the cache.";
        		throw new core_kernel_users_Exception($msg);
        	}
        }
        else{
        	$returnValue = $role->delete(true);	// delete references to this role!
        }
        // section 10-13-1-85-789cda43:13c8b795f73:-8000:0000000000002008 end

        return (bool) $returnValue;
    }

    /**
     * Get an array of the Roles included by a Generis Role.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  Resource role A Generis Role.
     * @return array
     */
    public function getIncludedRoles( core_kernel_classes_Resource $role)
    {
        $returnValue = array();

        // section 10-13-1-85-789cda43:13c8b795f73:-8000:000000000000200B begin
    	if (GENERIS_CACHE_USERS_ROLES == true && core_kernel_users_Cache::areIncludedRolesInCache($role) == true){
        	$returnValue = core_kernel_users_Cache::retrieveIncludedRoles($role);
        	common_Logger::i("Included roles of '" . $role->getUri() . "' retrieved from cache memory.", 'CACHE');
        }
        else{
	        // We use a Depth First Search approach to flatten the Roles Graph.
	        $includesRoleProperty = new core_kernel_classes_Property(PROPERTY_ROLE_INCLUDESROLE);
	        $visitedRoles = array();
	        $s = array(); // vertex stack.
	        array_push($s, $role); // begin with $role as the first vertex.
	        
	        while (!empty($s)){
	        	$u = array_pop($s);
	
	        	if (false === in_array($u->getUri(), $visitedRoles)){
	        		$visitedRoles[] = $u->getUri();
	        		$returnValue[$u->getUri()] = $u;
	        		
	        		$ar = $u->getPropertyValuesCollection($includesRoleProperty);
	        		foreach ($ar->getIterator() as $w){
	        			if (false === in_array($w->getUri(), $visitedRoles)){ // not visited
	        				array_push($s, $w);
	        			}
	        		}
	        	}
	        }
	        
	        // remove the root vertex which is actually the role we are testing.
	        unset($returnValue[$role->getUri()]);
	        common_Logger::i("Included roles of '" . $role->getUri() . "' retrieved from persistent memory.", 'CACHE');
	        
	        if (GENERIS_CACHE_USERS_ROLES == true){
	        	try{
					core_kernel_users_Cache::cacheIncludedRoles($role, $returnValue);
					common_Logger::i("Included roles of '" . $role->getUri() . "' written in cache memory.", 'CACHE');
	        	}
	        	catch(core_kernel_users_CacheException $e){
	        		$roleUri = $role->getUri();
	        		$msg = "Unable to retrieve included roles from cache memory for role '${roleUri}': ";
	        		$msg.= $e->getMessage();
	        		throw new core_kernel_users_Exception($msg);	
	        	}
	        }
        }
        // section 10-13-1-85-789cda43:13c8b795f73:-8000:000000000000200B end

        return (array) $returnValue;
    }

    /**
     * Returns an array of Roles (as Resources) where keys are their URIs. The
     * roles represent which kind of Roles are accepted to be identified against
     * system.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return array
     */
    public function getAllowedRoles()
    {
        $returnValue = array();

        // section 10-13-1-85-789cda43:13c8b795f73:-8000:000000000000200E begin
        $role = new core_kernel_classes_Resource(INSTANCE_ROLE_GENERIS);
        $returnValue = array($role->getUri() => $role);
        // section 10-13-1-85-789cda43:13c8b795f73:-8000:000000000000200E end

        return (array) $returnValue;
    }

    /**
     * Returns a Role (as a Resource) which represents the default role of the
     * If a user has to be created but no Role is given to him, it will receive
     * role.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return core_kernel_classes_Resource
     */
    public function getDefaultRole()
    {
        $returnValue = null;

        // section 10-13-1-85-789cda43:13c8b795f73:-8000:0000000000002010 begin
        $returnValue = new core_kernel_classes_Resource(INSTANCE_ROLE_GENERIS);
        // section 10-13-1-85-789cda43:13c8b795f73:-8000:0000000000002010 end

        return $returnValue;
    }

    /**
     * Log in a user into Generis that has one of the provided $allowedRoles.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string login The login of the user.
     * @param  string password the md5 hash of the password.
     * @param  allowedRoles A Role or an array of Roles that are allowed to be logged in. If the user has a Role that matches one or more Roles in this array, the login request will be accepted.
     * @return boolean
     */
    public function login($login, $password, $allowedRoles)
    {
        $returnValue = (bool) false;

        // section -87--2--3--76-270abbe1:12886b059d2:-8000:0000000000001834 begin

        // Role can be either a scalar value or a collection.
        $allowedRoles = is_array($allowedRoles) ? $allowedRoles : array($allowedRoles);
        $roles = array();
        foreach ($allowedRoles as $r){
        	$roles[] = (($r instanceof core_kernel_classes_Resource) ? $r->getUri() : $r);
        }
        
        unset($allowedRoles);
        
        $roles = array_merge(array_keys($this->getAllowedRoles()), $roles);
        $roles = array_unique($roles);
	        
		if(!is_string($login)){
			throw new core_kernel_users_Exception('The login must be of "string" type');
		}
		$login = trim($login);
		if(empty($login)){
			throw new core_kernel_users_Exception('The login cannot be empty');
		}
		
		if(!is_string($password)){
			throw new core_kernel_users_Exception('The password must be of "string" type');
		}
		// Parameters are corect.
		$userClass = new core_kernel_classes_Class(CLASS_GENERIS_USER);
		$filters = array(PROPERTY_USER_LOGIN => $login, PROPERTY_USER_PASSWORD => $password);
		$options = array('like' => false, 'recursive' => true);
		$users = $userClass->searchInstances($filters, $options);
		
		if (count($users) != 1){
			// Multiple users matching or not at all.
			if (count($users) > 1) {
				common_Logger::w("Multiple Users found with the same password for login '${login}'.", 'GENERIS');
			}
			
			$this->logout();
		}
		else{
			
			$user = reset($users);
			
			// A Matching user was found.
			// We now search for a matching role for this user.
			$userRoles = $this->getUserRoles($user);
			$found = false;
			
			foreach ($userRoles as $r){
				if (in_array($r->getUri(), $roles)){
					$found = true;
					break;	
				}
			}
			
			if ($found === true){
				$returnValue = true;
				
				// Initialize current user.
				$this->userResource = $user;
				$session = core_kernel_classes_Session::singleton();
				$session->reset();
				$session->setUser($login, $this->userResource->getUri(), $userRoles);
			}
		}
        // section -87--2--3--76-270abbe1:12886b059d2:-8000:0000000000001834 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method __construct
     *
     * @access private
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
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
     * Get a unique instance of the UserService.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
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
     * Logout the current user.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
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

} /* end of class core_kernel_users_Service */

?>