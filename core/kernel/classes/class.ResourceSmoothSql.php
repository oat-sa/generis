<?php

error_reporting(E_ALL);

/**
 * TAO - core/kernel/classes/class.ResourceSmoothSql.php
 *
 * $Id$
 *
 * This file is part of TAO.
 *
 * Automatically generated on 19.04.2011, 13:55:12 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package core
 * @subpackage kernel_classes
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include core_kernel_classes_ResourceInterface
 *
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 */
require_once('core/kernel/classes/interface.ResourceInterface.php');

/* user defined includes */
// section 127-0-1-1-7002f6a4:12f67d9b54d:-8000:0000000000002D87-includes begin
// section 127-0-1-1-7002f6a4:12f67d9b54d:-8000:0000000000002D87-includes end

/* user defined constants */
// section 127-0-1-1-7002f6a4:12f67d9b54d:-8000:0000000000002D87-constants begin
// section 127-0-1-1-7002f6a4:12f67d9b54d:-8000:0000000000002D87-constants end

/**
 * Short description of class core_kernel_classes_ResourceSmoothSql
 *
 * @access public
 * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
 * @package core
 * @subpackage kernel_classes
 */
class core_kernel_classes_ResourceSmoothSql
        implements core_kernel_classes_ResourceInterface
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute instance
     *
     * @access public
     * @var RessourceSmoothSql
     */
    public static $instance = null;

    // --- OPERATIONS ---

    /**
     * Short description of method getType
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Resource resource
     * @return array
     */
    public function getType( core_kernel_classes_Resource $resource)
    {
        $returnValue = array();

        // section 127-0-1-1-7002f6a4:12f67d9b54d:-8000:0000000000002D8E begin
        
        $sqlQuery = "select object from statements where subject = '". $resource->uriResource."' and predicate = '".RDF_TYPE."';";
        $dbWrapper = core_kernel_classes_DbWrapper::singleton();
        $sqlResult = $dbWrapper->execSql($sqlQuery);
        while (!$sqlResult-> EOF){
            $uri = $sqlResult->fields['object'];
            $returnValue[$uri] = new core_kernel_classes_Resource($uri);
            $sqlResult->MoveNext();
        }
        
        // section 127-0-1-1-7002f6a4:12f67d9b54d:-8000:0000000000002D8E end

        return (array) $returnValue;
    }

    /**
     * Short description of method getPropertyValues
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Resource resource
     * @param  Property property
     * @return array
     */
    public function getPropertyValues( core_kernel_classes_Resource $resource,  core_kernel_classes_Property $property)
    {
        $returnValue = array();

        // section 127-0-1-1-7002f6a4:12f67d9b54d:-8000:0000000000002D90 begin
        
    	$session = core_kernel_classes_Session::singleton();
       	$modelIds	= implode(',',array_keys($session->getLoadedModels()));
    	$dbWrapper = core_kernel_classes_DbWrapper::singleton();
        $query =  "SELECT object FROM statements 
		    		WHERE subject = ? AND predicate = ?
		    		AND (l_language = '' OR l_language = ?)
		    		AND modelID IN ({$modelIds})";
    	
        $result	= $dbWrapper->execSql($query, array(
        	$resource->uriResource,
        	$property->uriResource,
        	($session->getLg() != '') ? $session->getLg() : $session->defaultLg
        ));
        while ($row = $result->FetchRow()) {
        	$returnValue[] = $row['object'];
        }
		
        if(defined('ENABLE_SUBSCRIPTION') 	&& ENABLE_SUBSCRIPTION
        && $resource->uriResource != CLASS_SUBCRIPTION 	&& $resource->uriResource != CLASS_MASK
        && $property->uriResource != PROPERTY_SUBCRIPTION_MASK && $property->uriResource != PROPERTY_SUBCRIPTION_URL
        && $property->uriResource != PROPERTY_MASK_SUBJECT && $property->uriResource != PROPERTY_MASK_PREDICATE
        && $property->uriResource != PROPERTY_MASK_OBJECT
        ){
            $subcriptions = core_kernel_subscriptions_Service::singleton()->getSubscriptions($resource,$property,null);
            foreach ($subcriptions as $sub){
                $subcriptionResource = new core_kernel_classes_Resource($sub);
                $subcriptionsInstances = core_kernel_subscriptions_Service::singleton()->getPropertyValuesFromSubscription($subcriptionResource,$resource,$property);
                $returnValue = array_merge($returnValue,$subcriptionsInstances);
            }

        }
        
        // section 127-0-1-1-7002f6a4:12f67d9b54d:-8000:0000000000002D90 end

        return (array) $returnValue;
    }

    /**
     * Short description of method getPropertyValuesCollection
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Resource resource
     * @param  Property property
     * @return core_kernel_classes_ContainerCollection
     */
    public function getPropertyValuesCollection( core_kernel_classes_Resource $resource,  core_kernel_classes_Property $property)
    {
        $returnValue = null;

        // section 127-0-1-1-7002f6a4:12f67d9b54d:-8000:0000000000002D95 begin
        
    	if(DEBUG_MODE){
        	$returnValue->debug = __METHOD__;
        }
        $returnValue = new core_kernel_classes_ContainerCollection($resource);
        
    	$session = core_kernel_classes_Session::singleton();
    	$modelIds	= implode(',',array_keys($session->getLoadedModels()));
    	$dbWrapper = core_kernel_classes_DbWrapper::singleton();
        $query =  "SELECT object FROM statements 
		    		WHERE subject = ? AND predicate = ?
		    		AND (l_language = '' OR l_language = ?)
		    		AND modelID IN ({$modelIds})";
        $result	= $dbWrapper->execSql($query, array(
        	$resource->uriResource,
        	$property->uriResource,
        	($session->getLg() != '') ? $session->getLg() : $session->defaultLg
        ));
      	
        $propertiesValues = array();
        while ($row = $result->FetchRow()) {
        	$propertiesValues[] = $row['object'];
        }

        if(defined('ENABLE_SUBSCRIPTION') 	&& ENABLE_SUBSCRIPTION
        && $resource->uriResource != CLASS_SUBCRIPTION 	&& $resource->uriResource != CLASS_MASK
        && $property->uriResource != PROPERTY_SUBCRIPTION_MASK && $property->uriResource != PROPERTY_SUBCRIPTION_URL
        && $property->uriResource != PROPERTY_MASK_SUBJECT && $property->uriResource != PROPERTY_MASK_PREDICATE
        && $property->uriResource != PROPERTY_MASK_OBJECT
        ){

            $subcriptions = core_kernel_subscriptions_Service::singleton()->getSubscriptions($resource,$property,null);

            foreach ($subcriptions as $sub){
                $subcriptionResource = new core_kernel_classes_Resource($sub);
                $subcriptionsInstances = core_kernel_subscriptions_Service::singleton()->getPropertyValuesFromSubscription($subcriptionResource,$resource,$property);
                $propertiesValues = array_merge($propertiesValues,$subcriptionsInstances);
            }

        }
        
        foreach ($propertiesValues as $value){
            if(!common_Utils::isUri($value)) {
                $container = new core_kernel_classes_Literal($value);
            }
            else {
                $container = new core_kernel_classes_Resource($value);
            }

            if(DEBUG_MODE){
            	$container->debug = __METHOD__ .'|' . $property->debug;
            }
            $returnValue->add($container);
        }
        
        // section 127-0-1-1-7002f6a4:12f67d9b54d:-8000:0000000000002D95 end

        return $returnValue;
    }

    /**
     * Short description of method getOnePropertyValue
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Resource resource
     * @param  Property property
     * @param  boolean last
     * @return core_kernel_classes_Container
     */
    public function getOnePropertyValue( core_kernel_classes_Resource $resource,  core_kernel_classes_Property $property, $last = false)
    {
        $returnValue = null;

        // section 127-0-1-1-7002f6a4:12f67d9b54d:-8000:0000000000002D97 begin

   	 	$session 	= core_kernel_classes_Session::singleton();
    	$modelIds	= implode(',',array_keys($session->getLoadedModels()));
    	$dbWrapper 	= core_kernel_classes_DbWrapper::singleton();
        $query =  "SELECT object FROM statements 
        			WHERE subject = ? AND predicate = ?
		    		AND (l_language = '' OR l_language = ?) 
		    		AND modelID IN ({$modelIds})";
        $params = array(
        	$resource->uriResource,
        	$property->uriResource,
        	($session->getLg() != '') ? $session->getLg() : $session->defaultLg
        );
        
    	if($last){
    		$result	= $dbWrapper->execSql($query, $params);
    		if(!$result->EOF){
    			$result->moveLast();
    		}
    	}
    	else{
	        $result	= $dbWrapper->dbConnector->selectLimit($query, 1, -1, $params);
    	}
    	
    	while(!$result->EOF){
    		
        	$value = $result->fields['object'];
        	if(!common_Utils::isUri($value)) {
        		  
                $returnValue = new core_kernel_classes_Literal($value);
	         }
	         else {
                $returnValue = new core_kernel_classes_Resource($value);
            }
            $result->moveNext();
          
    	}
        
        // section 127-0-1-1-7002f6a4:12f67d9b54d:-8000:0000000000002D97 end

        return $returnValue;
    }

    /**
     * Short description of method getPropertyValuesByLg
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Resource resource
     * @param  Property property
     * @param  string lg
     * @return core_kernel_classes_ContainerCollection
     */
    public function getPropertyValuesByLg( core_kernel_classes_Resource $resource,  core_kernel_classes_Property $property, $lg)
    {
        $returnValue = null;

        // section 127-0-1-1-7002f6a4:12f67d9b54d:-8000:0000000000002D9C begin
        
    	$returnValue = new core_kernel_classes_ContainerCollection($resource);
        $sqlQuery = "select object from statements where subject = '". $resource->uriResource."' and predicate = '".$property->uriResource."' and l_language = '".$lg."';";
        $dbWrapper = core_kernel_classes_DbWrapper::singleton();
        $sqlResult = $dbWrapper->execSql($sqlQuery);
        while (!$sqlResult-> EOF){
        	if(!common_Utils::isUri($sqlResult->fields['object'])) {
                $container = new core_kernel_classes_Literal($sqlResult->fields['object']);
            }
            else {
                $container = new core_kernel_classes_Resource($sqlResult->fields['object']);
            }
            $returnValue->add($container);
            $sqlResult->MoveNext();
        }        
        
        // section 127-0-1-1-7002f6a4:12f67d9b54d:-8000:0000000000002D9C end

        return $returnValue;
    }

    /**
     * Short description of method setPropertyValue
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Resource resource
     * @param  Property property
     * @param  string object
     * @return boolean
     */
    public function setPropertyValue( core_kernel_classes_Resource $resource,  core_kernel_classes_Property $property, $object)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-7002f6a4:12f67d9b54d:-8000:0000000000002DA0 begin
        
    	$dbWrapper 	= core_kernel_classes_DbWrapper::singleton();
        $session 	= core_kernel_classes_Session::singleton();
        $localNs 	= common_ext_NamespaceManager::singleton()->getLocalNamespace();
        $mask		= 'yyy[admin,administrators,authors]';	//now it's the default right mode
        $lang 		= ($property->isLgDependent() ? ( $session->getLg() != '' ? $session->getLg() : $session->defaultLg) : '');
        
        $query = "INSERT into statements (modelID,subject,predicate,object,l_language,author,stread,stedit,stdelete,epoch)
        			VALUES  (?, ?, ?, ?, ?, ?, '{$mask}','{$mask}','{$mask}', CURRENT_TIMESTAMP);";

        $returnValue = $dbWrapper->execSql($query, array(
       		$localNs->getModelId(),
       		$resource->uriResource,
       		$property->uriResource,
       		$object,
       		$lang,
       		$session->getUser()
        ));
    	
        // section 127-0-1-1-7002f6a4:12f67d9b54d:-8000:0000000000002DA0 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method setPropertiesValues
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Resource resource
     * @param  array properties
     * @return boolean
     */
    public function setPropertiesValues( core_kernel_classes_Resource $resource, $properties)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--6761ba9f:12f6868ffc5:-8000:0000000000002DA0 begin
        
    	if(is_array($properties)){
        	if(count($properties) > 0){
        		
	        	$dbWrapper 	= core_kernel_classes_DbWrapper::singleton();
	        	$session 	= core_kernel_classes_Session::singleton();
	        	
	        	$localNs 	= common_ext_NamespaceManager::singleton()->getLocalNamespace();
	       		$modelId	= $localNs->getModelId();
	        	$mask		= 'yyy[admin,administrators,authors]';	//now it's the default right mode
	        	$user		= $session->getUser();
	       		
	       		$query = "INSERT into statements (modelID,subject,predicate,object,l_language,author,stread,stedit,stdelete,epoch) VALUES ";
	       		
	       		foreach($properties as $propertyUri => $value){
	       			
	       			if(!common_Utils::isUri($propertyUri)){
	       				$label = $resource->getLabel();
	       				throw new common_Exception("setPropertiesValues' argument must contains property uris as keys, 
	       												in {$label} ({$resource->uriResource})");
	       			}
	       			$property = new core_kernel_classes_Property($propertyUri);
	       			$object = $dbWrapper->dbConnector->escape($value);
	       			$lang 	= ($property->isLgDependent() ? ( $session->getLg() != '' ? $session->getLg() : $session->defaultLg) : '');
	       			
	       			$query .= " ($modelId, '{$resource->uriResource}', '{$property->uriResource}', '{$object}', '{$lang}', '{$user}', '{$mask}','{$mask}','{$mask}', CURRENT_TIMESTAMP),";
	       		}
	       		
	       		$query = substr($query, 0, strlen($query) -1);
	       		$returnValue = $dbWrapper->execSql($query);
        	}
        }
        
        // section 127-0-1-1--6761ba9f:12f6868ffc5:-8000:0000000000002DA0 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method setPropertyValueByLg
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Resource resource
     * @param  Property property
     * @param  string value
     * @param  string lg
     * @return boolean
     */
    public function setPropertyValueByLg( core_kernel_classes_Resource $resource,  core_kernel_classes_Property $property, $value, $lg)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--6761ba9f:12f6868ffc5:-8000:0000000000002DA3 begin
        
        $dbWrapper 	= core_kernel_classes_DbWrapper::singleton();
        $session 	= core_kernel_classes_Session::singleton();
        $localNs 	= common_ext_NamespaceManager::singleton()->getLocalNamespace();
        $mask		= 'yyy[admin,administrators,authors]';	//now it's the default right mode
        
        $query = "INSERT into statements (modelID,subject,predicate,object,l_language,author,stread,stedit,stdelete,epoch)
        			VALUES  (?, ?, ?, ?, ?, ?, '{$mask}','{$mask}','{$mask}', CURRENT_TIMESTAMP);";

        $returnValue = $dbWrapper->execSql($query, array(
       		$localNs->getModelId(),
       		$resource->uriResource,
       		$property->uriResource,
       		$value,
       		($property->isLgDependent() ? $lg : ''),
       		$session->getUser()
        ));
        
        // section 127-0-1-1--6761ba9f:12f6868ffc5:-8000:0000000000002DA3 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method removePropertyValues
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Resource resource
     * @param  Property property
     * @return boolean
     */
    public function removePropertyValues( core_kernel_classes_Resource $resource,  core_kernel_classes_Property $property)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--6761ba9f:12f6868ffc5:-8000:0000000000002DA7 begin
        
    	$dbWrapper = core_kernel_classes_DbWrapper::singleton();
        $session = core_kernel_classes_Session::singleton();
        $modelIds	= implode(',',array_keys($session->getLoadedModels()));
        
        $query =  "DELETE FROM statements 
		    		WHERE subject = ? AND predicate = ?
		    		AND modelID IN ({$modelIds}) ";
        
        if($property->isLgDependent()){
        	
        	$query .=  " AND (l_language = '' OR l_language = ?) ";
        	$returnValue	= $dbWrapper->execSql($query,array(
	        		$resource->uriResource,
	        		$property->uriResource,
	        		($session->getLg() != '') ? $session->getLg() : $session->defaultLg
	        ));
	        
        }
        else{
        	
        	$returnValue	= $dbWrapper->execSql($query,array(
	        		$resource->uriResource,
	        		$property->uriResource
	        ));
	        
        }
        
        // section 127-0-1-1--6761ba9f:12f6868ffc5:-8000:0000000000002DA7 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method removePropertyValueByLg
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Resource resource
     * @param  Property property
     * @param  string lg
     * @return boolean
     */
    public function removePropertyValueByLg( core_kernel_classes_Resource $resource,  core_kernel_classes_Property $property, $lg)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--6761ba9f:12f6868ffc5:-8000:0000000000002DAA begin
        
    	$dbWrapper = core_kernel_classes_DbWrapper::singleton();
        $sqlQuery = "delete from statements where subject = '". $resource->uriResource."' and predicate = '".$property->uriResource."' and l_language = '".$lg."';";
        $sqlResult = $dbWrapper->execSql($sqlQuery);
        $returnValue = $sqlResult->EOF;
    	
        // section 127-0-1-1--6761ba9f:12f6868ffc5:-8000:0000000000002DAA end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getRdfTriples
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Resource resource
     * @return core_kernel_classes_ContainerCollection
     */
    public function getRdfTriples( core_kernel_classes_Resource $resource)
    {
        $returnValue = null;

        // section 127-0-1-1--6761ba9f:12f6868ffc5:-8000:0000000000002DAE begin

	     $dbWrapper = core_kernel_classes_DbWrapper::singleton();
	
	     $namespaces = common_ext_NamespaceManager::singleton()->getAllNamespaces();
	     $namespace = $namespaces[substr($resource->uriResource, 0, strpos($resource->uriResource, '#') + 1)];
	
	     $query = "SELECT * FROM statements WHERE subject = ? AND modelID = ?";
	     $result = $dbWrapper->execSql($query, array(
	    	 $resource->uriResource,
	     	$namespace->getModelId()
	     ));
	
	     $returnValue = new core_kernel_classes_ContainerCollection(new common_Object(__METHOD__));
	     while($statement = $result->fetchRow()){
	     	$triple = new core_kernel_classes_Triple();
	     	$triple->subject = $statement["subject"];
	     	$triple->predicate = $statement["predicate"];
	     	$triple->object = $statement["object"];
	     	$triple->id = $statement["id"];
	     	$triple->lg = $statement["l_language"];
	     	$triple->readPrivileges = $statement["stread"];
	     	$triple->editPrivileges = $statement["stedit"];
	     	$triple->deletePrivileges = $statement["stdelete"];
	     	$returnValue->add($triple);
	     }

        // section 127-0-1-1--6761ba9f:12f6868ffc5:-8000:0000000000002DAE end

        return $returnValue;
    }

    /**
     * Short description of method getUsedLanguages
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Resource resource
     * @param  Property property
     * @return array
     */
    public function getUsedLanguages( core_kernel_classes_Resource $resource,  core_kernel_classes_Property $property)
    {
        $returnValue = array();

        // section 127-0-1-1--6761ba9f:12f6868ffc5:-8000:0000000000002DB0 begin
        
    	$dbWrapper = core_kernel_classes_DbWrapper::singleton();
        
        $namespaces = common_ext_NamespaceManager::singleton()->getAllNamespaces();
        $namespace = $namespaces[substr($resource->uriResource, 0, strpos($resource->uriResource, '#') + 1)];
        
        $query = "SELECT * FROM statements WHERE subject = ? AND modelID = ?";
        $result	= $dbWrapper->execSql($query, array(
        	$resource->uriResource,
        	$namespace->getModelId()
        ));
        
        $returnValue = new core_kernel_classes_ContainerCollection(new common_Object(__METHOD__));
        while($statement = $result->fetchRow()){
            $triple = new core_kernel_classes_Triple();
            $triple->subject = $statement["subject"];
            $triple->predicate = $statement["predicate"];
            $triple->object = $statement["object"];
            $triple->id = $statement["id"];
            $triple->lg = $statement["l_language"];
            $triple->readPrivileges = $statement["stread"];
            $triple->editPrivileges = $statement["stedit"];
            $triple->deletePrivileges = $statement["stdelete"];
            $returnValue->add($triple);
        }
        
        // section 127-0-1-1--6761ba9f:12f6868ffc5:-8000:0000000000002DB0 end

        return (array) $returnValue;
    }

    /**
     * Short description of method duplicate
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Resource resource
     * @param  array excludedProperties
     * @return core_kernel_classes_Resource
     */
    public function duplicate( core_kernel_classes_Resource $resource, $excludedProperties = array())
    {
        $returnValue = null;

        // section 127-0-1-1--6761ba9f:12f6868ffc5:-8000:0000000000002DB3 begin
        
    	$newUri = common_Utils::getNewUri();
    	
    	$collection = $resource->getRdfTriples();
    	if($collection->count() > 0){
    		
    		$session = core_kernel_classes_Session::singleton();
    		$user = $session->getUser();
    		
	    	$insert = "INSERT INTO `statements` (`modelID`, `subject`, `predicate`, `object`, `l_language`, `author`, `stread`, `stedit`, `stdelete`) VALUES ";
    		foreach($collection->getIterator() as $triple){
    			if(!in_array($triple->predicate, $excludedProperties)){
	    			$insert .= "(8, '$newUri', '{$triple->predicate}', '{$triple->object}',  '{$triple->lg}', '{$user}', '{$triple->readPrivileges}', '{$triple->editPrivileges}', '{$triple->deletePrivileges}'),"; 
	    		}
	    	}
	    	$insert = substr($insert, 0, strlen($insert) -1);
	    	
	    	$dbWrapper = core_kernel_classes_DbWrapper::singleton();
        	if($dbWrapper->execSql($insert)){
        		$returnValue = new core_kernel_classes_Resource($newUri);
        	}
    	}
        
        // section 127-0-1-1--6761ba9f:12f6868ffc5:-8000:0000000000002DB3 end

        return $returnValue;
    }

    /**
     * Short description of method delete
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Resource resource
     * @param  boolean deleteReference
     * @return boolean
     */
    public function delete( core_kernel_classes_Resource $resource, $deleteReference = false)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--6761ba9f:12f6868ffc5:-8000:0000000000002DB6 begin
        
    	$dbWrapper = core_kernel_classes_DbWrapper::singleton();
        
		$query = "DELETE FROM statements WHERE subject = ?";
        $returnValue = $dbWrapper->execSql($query, array($resource->uriResource));
        
    	if($deleteReference){
        	$dbWrapper = core_kernel_classes_DbWrapper::singleton();
        	$sqlQuery = "DELETE FROM statements WHERE object = '".$resource->uriResource."'";
        	$returnValue = $dbWrapper->execSql($sqlQuery) && $returnValue;
        }
        
        // section 127-0-1-1--6761ba9f:12f6868ffc5:-8000:0000000000002DB6 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getLastModificationDate
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Resource resource
     * @param  Property property
     * @return doc_date
     */
    public function getLastModificationDate( core_kernel_classes_Resource $resource,  core_kernel_classes_Property $property)
    {
        $returnValue = null;

        // section 127-0-1-1--6761ba9f:12f6868ffc5:-8000:0000000000002DB9 begin
        
    	$sqlQuery = "select epoch from statements where subject = '". $resource->uriResource."' ";

        if(!is_null($property) && $property instanceof core_kernel_classes_Property){
            $sqlQuery = "$sqlQuery and predicate = '". $property->uriResource."' ";
        }

        $dbWrapper = core_kernel_classes_DbWrapper::singleton();
        $sqlResult = $dbWrapper->execSql($sqlQuery);

        if(!is_null($property) && $sqlResult-> EOF){
            throw new common_Exception("The resource does not have the specified property.");
        }

        while (!$sqlResult-> EOF){
            $last = $sqlResult->fields['epoch'];
            $lastDate = date_create($last);
            if($returnValue == null ) {
                $returnValue = $lastDate;
            }
            else {
                if($returnValue < $lastDate) {
                    $returnValue = $lastDate;
                }
            }

            $sqlResult->MoveNext();
        }
        
        // section 127-0-1-1--6761ba9f:12f6868ffc5:-8000:0000000000002DB9 end

        return $returnValue;
    }

    /**
     * Short description of method getLastModificationUser
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Resource resource
     * @return string
     */
    public function getLastModificationUser( core_kernel_classes_Resource $resource)
    {
        $returnValue = (string) '';

        // section 127-0-1-1--6761ba9f:12f6868ffc5:-8000:0000000000002DBD begin
        
        $sqlQuery = "select author from statements where subject = '". $resource->uriResource."' and predicate = '".RDF_TYPE."'";
        $dbWrapper = core_kernel_classes_DbWrapper::singleton();
        $sqlResult = $dbWrapper->execSql($sqlQuery);
        $returnValue =  $sqlResult->fields['author'];
        
        // section 127-0-1-1--6761ba9f:12f6868ffc5:-8000:0000000000002DBD end

        return (string) $returnValue;
    }

    /**
     * Short description of method singleton
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return core_kernel_classes_RessourceSmoothSql
     */
    public static function singleton()
    {
        $returnValue = null;

        // section 127-0-1-1--9398bc2:12f6a3d8694:-8000:0000000000002E13 begin
        
        if (core_kernel_classes_ResourceSmoothSql::$instance == null){
        	core_kernel_classes_ResourceSmoothSql::$instance = new core_kernel_classes_ResourceSmoothSql();
        }
        $returnValue = core_kernel_classes_ResourceSmoothSql::$instance;
        
        // section 127-0-1-1--9398bc2:12f6a3d8694:-8000:0000000000002E13 end

        return $returnValue;
    }

} /* end of class core_kernel_classes_ResourceSmoothSql */

?>