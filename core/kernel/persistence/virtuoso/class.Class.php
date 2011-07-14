<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API -
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 08.07.2011, 12:25:52 with ArgoUML PHP module 
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 * @package core
 * @subpackage kernel_persistence_virtuoso
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include core_kernel_persistence_PersistenceImpl
 *
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 */
require_once('core/kernel/persistence/class.PersistenceImpl.php');

/**
 * include core_kernel_persistence_ClassInterface
 *
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 */
require_once('core/kernel/persistence/interface.ClassInterface.php');

/* user defined includes */
// section 127-0-1-1--3a4c22:13104bcfe8d:-8000:00000000000022E9-includes begin
// section 127-0-1-1--3a4c22:13104bcfe8d:-8000:00000000000022E9-includes end

/* user defined constants */
// section 127-0-1-1--3a4c22:13104bcfe8d:-8000:00000000000022E9-constants begin
// section 127-0-1-1--3a4c22:13104bcfe8d:-8000:00000000000022E9-constants end

/**
 * Short description of class core_kernel_persistence_virtuoso_Class
 *
 * @access public
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 * @package core
 * @subpackage kernel_persistence_virtuoso
 */
class core_kernel_persistence_virtuoso_Class
    extends core_kernel_persistence_PersistenceImpl
        implements core_kernel_persistence_ClassInterface
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute instance
     *
     * @access public
     * @var Resource
     */
    public static $instance = null;

    // --- OPERATIONS ---

    /**
     * Short description of method getSubClasses
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource resource
     * @param  boolean recursive
     * @return array
     */
    public function getSubClasses( core_kernel_classes_Resource $resource, $recursive = false)
    {
        $returnValue = array();

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000014EB begin
        
        list($NS, $ID) = explode('#', $resource->uriResource);
        
        if (isset($ID) && !empty($ID)) {

                $virtuoso = core_kernel_persistence_virtuoso_VirtuosoDataStore::singleton();

                $query = 'PREFIX classNS: <' . $NS . '#>  SELECT ?s WHERE {?s rdfs:subClassOf classNS:' . $ID . '}';
                
                $resultArray = $virtuoso->execQuery($query);
                $count = count($resultArray);
                for ($i = 0; $i < $count; $i++) {
                        if (isset($resultArray[$i][0])) {
                                $subClass = new core_kernel_classes_Class($resultArray[$i][0]);
                                $returnValue[$subClass->uriResource] = $subClass;
                                if($recursive === true){
                                        $subSubClasses = $subClass->getSubClasses(true);
                                        $returnValue = array_merge($returnValue , $subSubClasses);
                                }
                        }
                }
        }
        
        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000014EB end

        return (array) $returnValue;
    }

    /**
     * Short description of method isSubClassOf
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource resource
     * @param  Class parentClass
     * @return boolean
     */
    public function isSubClassOf( core_kernel_classes_Resource $resource,  core_kernel_classes_Class $parentClass)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000014F0 begin
        list($NS, $ID) = explode('#', $resource->uriResource);
        list($parentNS, $parentID) = explode('#', $parentClass->uriResource);
        if (isset($ID) && !empty($ID)) {

                $virtuoso = core_kernel_persistence_virtuoso_VirtuosoDataStore::singleton();
                
                $query = 'PREFIX classNS: <' . $NS . '#> 
                        PREFIX parentNS: <' . $parentNS . '#> 
                        ASK {classNS:' . $ID . ' rdfs:subClassOf parentNS:' . $parentID . '}';
                //TODO: check issue: only one triple allowed for an identical SPO for a given language-> issue with multiple identical objects for SP (i.e. parallel branch for wfEngine)
                $returnValue = $virtuoso->execQuery($query, 'Boolean');
        }
        
        if (!$returnValue) {
                $parentSubClasses = $parentClass->getSubClasses(true);
                foreach ($parentSubClasses as $subClass) {
                        if ($subClass->uriResource == $resource->uriResource) {
                                $returnValue = true;
                                break;
                        }
                }
        }
        
        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000014F0 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getParentClasses
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource resource
     * @param  boolean recursive
     * @return array
     */
    public function getParentClasses( core_kernel_classes_Resource $resource, $recursive = false)
    {
        $returnValue = array();

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000014F5 begin
        
        list($NS, $ID) = explode('#', $resource->uriResource);
        if (isset($ID) && !empty($ID)) {

                $virtuoso = core_kernel_persistence_virtuoso_VirtuosoDataStore::singleton();
                //TODO: check why or condiiton with rdf:type?? in the smooth impl
                $query = 'PREFIX classNS: <' . $NS . '#>  SELECT ?o WHERE {classNS:' . $ID . ' rdfs:subClassOf ?o}';
                
                $resultArray = $virtuoso->execQuery($query);
                $count = count($resultArray);
                for ($i = 0; $i < $count; $i++) {
                        if (isset($resultArray[$i][0])) {
                                $parentClass = new core_kernel_classes_Class($resultArray[$i][0]);
                                $returnValue[$parentClass->uriResource] = $parentClass ;
                                if($recursive == true && $parentClass->uriResource != RDF_CLASS && $parentClass->uriResource != RDF_RESOURCE){
                                        $recursiveParents = $parentClass->getParentClasses(true);
                                        $returnValue = array_merge($returnValue, $recursiveParents);
                                }
                        }
                }
        }
        
        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000014F5 end

        return (array) $returnValue;
    }

    /**
     * Short description of method getProperties
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource resource
     * @param  boolean recursive
     * @return array
     */
    public function getProperties( core_kernel_classes_Resource $resource, $recursive = false)
    {
        $returnValue = array();

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000014FA begin
        
        list($NS, $ID) = explode('#', $resource->uriResource);
        if (isset($ID) && !empty($ID)) {

                $virtuoso = core_kernel_persistence_virtuoso_VirtuosoDataStore::singleton();
                //TODO: check why or condiiton with rdf:type?? in the smooth impl
                $query = 'PREFIX classNS: <' . $NS . '#>  SELECT ?s WHERE {?s rdfs:domain classNS:' . $ID . '}';

                $resultArray = $virtuoso->execQuery($query);
                $count = count($resultArray);
                for ($i = 0; $i < $count; $i++) {
                        if (isset($resultArray[$i][0])) {
                                $property = new core_kernel_classes_Property($resultArray[$i][0]);
                                $returnValue[$property->uriResource] = $property;
                        }
                }
                
                if($recursive == true) {
			$parentClasses = $resource->getParentClasses(true);
			foreach ($parentClasses as $parent) {
				if($parent->uriResource != RDF_CLASS) {
					$returnValue = array_merge($returnValue, $parent->getProperties(true));
				}
			}
		}
        }
                
        // section 127-0-1-1--30506d9:12f6daaa255:-8000:00000000000014FA end

        return (array) $returnValue;
    }

    /**
     * Short description of method getInstances
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource resource
     * @param  boolean recursive
     * @param  array params
     * @return array
     */
    public function getInstances( core_kernel_classes_Resource $resource, $recursive = false, $params = array())
    {
        $returnValue = array();

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:0000000000001500 begin
        list($NS, $ID) = explode('#', $resource->uriResource);
        if(isset($ID) && !empty($ID)){
                
                $virtuoso = core_kernel_persistence_virtuoso_VirtuosoDataStore::singleton();
                
                $query = 'PREFIX classNS: <'.$NS.'#>  SELECT ?s WHERE {?s rdf:type classNS:'.$ID.'} ';
                
                if(isset($params['limit'])){
                        $offset = 0;
                        $limit = intval($params['limit']);
                        if ($limit==0){
                                $limit = 1000000;
                                
                        }
                        $query .= " LIMIT ".$limit;
                        
                        if(isset($params['offset'])){
                                $offset = intval($params['offset']);
                                $query .= " OFFSET ".$offset;
                        }
                }
        
                $resultArray = $virtuoso->execQuery($query);
                $count = count($resultArray);
                for($i = 0; $i<$count; $i++){
                        if(isset($resultArray[$i][0])){
                                
                                $instance = new core_kernel_classes_Resource($resultArray[$i][0]);

                                $returnValue[$instance->uriResource] = $instance ;

                                //In case of a meta class, subclasses of instances may be returned*/
                                if (($instance->uriResource!=RDF_CLASS)
                                && ($resource->uriResource == RDF_CLASS)
                                && ($instance->uriResource!=RDF_RESOURCE)) {

                                        $instanceClass = new core_kernel_classes_Class($instance->uriResource);
                                        $subClasses = $instanceClass->getSubClasses(true);

                                        foreach($subClasses as $subClass) {
                                                $returnValue[$subClass->uriResource] = $subClass;
                                        }
                                }
                        }
                }
                
                if($recursive == true){
                        $subClasses = $resource->getSubClasses(true);
                        foreach ($subClasses as $subClass){
                                $returnValue = array_merge($returnValue, $subClass->getInstances(true, $params));
                        }
                }
                
        }
                
        // section 127-0-1-1--30506d9:12f6daaa255:-8000:0000000000001500 end

        return (array) $returnValue;
    }

    /**
     * Short description of method setInstance
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource resource
     * @param  Resource instance
     * @return core_kernel_classes_Resource
     */
    public function setInstance( core_kernel_classes_Resource $resource,  core_kernel_classes_Resource $instance)
    {
        $returnValue = null;

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:0000000000001506 begin
        
        $newInstance = $instance->duplicate();
        
        if(!is_null($newInstance)){
                if($newInstance->setType($resource)){
                        $returnValue = $newInstance; 
                }
        }
                
        // section 127-0-1-1--30506d9:12f6daaa255:-8000:0000000000001506 end

        return $returnValue;
    }

    /**
     * Short description of method setSubClassOf
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource resource
     * @param  Class iClass
     * @return boolean
     */
    public function setSubClassOf( core_kernel_classes_Resource $resource,  core_kernel_classes_Class $iClass)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:000000000000150F begin
        
        $subClassOf = new core_kernel_classes_Property(RDF_SUBCLASSOF);
        $returnValue = $resource->setPropertyValue($subClassOf,$iClass->uriResource);
        
        // section 127-0-1-1--30506d9:12f6daaa255:-8000:000000000000150F end

        return (bool) $returnValue;
    }

    /**
     * Short description of method setProperty
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource resource
     * @param  Property property
     * @return boolean
     */
    public function setProperty( core_kernel_classes_Resource $resource,  core_kernel_classes_Property $property)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--30506d9:12f6daaa255:-8000:0000000000001512 begin
        
        $domain = new core_kernel_classes_Property(RDF_DOMAIN,__METHOD__);
        $instanceProperty = new core_kernel_classes_Resource($property->uriResource,__METHOD__);
        $returnValue = $instanceProperty->setPropertyValue($domain, $resource->uriResource);
                
        // section 127-0-1-1--30506d9:12f6daaa255:-8000:0000000000001512 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method createInstance
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource resource
     * @param  string label
     * @param  string comment
     * @param  string uri
     * @return core_kernel_classes_Resource
     */
    public function createInstance( core_kernel_classes_Resource $resource, $label = '', $comment = '', $uri = '')
    {
        $returnValue = null;

        // section 127-0-1-1--6705a05c:12f71bd9596:-8000:0000000000001F27 begin
        
        if($uri == ''){
                $subject = common_Utils::getNewUri();
        }
        else {
                //$uri should start with # and be well formed
                if ($uri[0]=='#'){
                        $modelUri = core_kernel_classes_Session::singleton()->getNameSpace();
                        $subject = $modelUri . $uri;
                } else {
                        $subject = $uri;
                }
        }
        
        list($NS, $ID) = explode('#', $subject);
        list($classNS, $classID) = explode('#', $resource->uriResource);
        if(!empty($ID) && !empty($classID)){
                $virtuoso = core_kernel_persistence_virtuoso_VirtuosoDataStore::singleton();
                $query = '
                        PREFIX resourceNS: <'.$NS.'#>
                        PREFIX classNS: <'.$classNS.'#>
                        INSERT INTO <'.$virtuoso->getCurrentGraph().'> {resourceNS:'.$ID.' rdf:type classNS:'.$classID.'}';
                
                if($virtuoso->execQuery($query, 'Boolean')){
                        $returnValue = new core_kernel_classes_Resource($subject,__METHOD__);
                        if ($label != '') {
                                $returnValue->setLabel($label);
                        }
                        if( $comment != '') {
                                $returnValue->setComment($comment);
                        }
                }else{
                        throw new core_kernel_persistence_virtuoso_Exception("cannot create instance of {$resource->getLabel()} ({$resource->uriResource})");
                }
        }
        
        // section 127-0-1-1--6705a05c:12f71bd9596:-8000:0000000000001F27 end

        return $returnValue;
    }

    /**
     * Short description of method createSubClass
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource resource
     * @param  string label
     * @param  string comment
     * @return core_kernel_classes_Class
     */
    public function createSubClass( core_kernel_classes_Resource $resource, $label = '', $comment = '')
    {
        $returnValue = null;

        // section 127-0-1-1--6705a05c:12f71bd9596:-8000:0000000000001F32 begin
        
        $class = new core_kernel_classes_Class(RDF_CLASS,__METHOD__);
        $intance = $class->createInstance($label,$comment);
        $returnValue = new core_kernel_classes_Class($intance->uriResource);
        $returnValue->setSubClassOf($resource);
        
        // section 127-0-1-1--6705a05c:12f71bd9596:-8000:0000000000001F32 end

        return $returnValue;
    }

    /**
     * Short description of method createProperty
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource resource
     * @param  string label
     * @param  string comment
     * @param  boolean isLgDependent
     * @return core_kernel_classes_Property
     */
    public function createProperty( core_kernel_classes_Resource $resource, $label = '', $comment = '', $isLgDependent = false)
    {
        $returnValue = null;

        // section 127-0-1-1--6705a05c:12f71bd9596:-8000:0000000000001F3C begin
        
        $property = new core_kernel_classes_Class(RDF_PROPERTY,__METHOD__);
        $propertyInstance = $property->createInstance($label,$comment);
        $returnValue = new core_kernel_classes_Property($propertyInstance->uriResource,__METHOD__);
        $returnValue->setLgDependent($isLgDependent);

        if (!$resource->setProperty($returnValue)){
                throw new common_Exception('problem creating property');
        }
        
        // section 127-0-1-1--6705a05c:12f71bd9596:-8000:0000000000001F3C end

        return $returnValue;
    }

    /**
     * Short description of method searchInstances
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource resource
     * @param  array propertyFilters
     * @param  array options
     * @return array
     */
    public function searchInstances( core_kernel_classes_Resource $resource, $propertyFilters = array(), $options = array())
    {
        $returnValue = array();

        // section 10-13-1--128--26678bb4:12fbafcb344:-8000:00000000000014F0 begin
        
        if(count($propertyFilters) == 0){
			return $returnValue;
        }
        
        //add the type check to the filters
        $propertyFilters[RDF_TYPE] = $resource->uriResource;
        
        list($NS, $ID) = explode('#', $resource->uriResource);
        if(!empty($ID)){
                
                $session = core_kernel_classes_Session::singleton();
                $virtuoso = core_kernel_persistence_virtuoso_VirtuosoDataStore::singleton();
                
                $prefixes =  array($NS => 'classNS');//not really useful but set for information only
                $filters = array();
                $objects = array();
                
                $lg = '';
                if(isset($options['lang'])){
                        $lg = $virtuoso->filterLanguageValue($options['lang']);
                }
                
                $like = true;
                if(isset($options['like'])){
                        $like = ($options['like'] === true);
                }
                
                $conditions = array();
                foreach($propertyFilters as $propertyUri => $pattern){
                        
                        list($propNS, $propID) = explode('#', $propertyUri);
                        if(!empty($propID)){
                                
                                if(!isset($prefixes[$propNS])){
                                        $prefixes[$propNS] = 'NS'.count($prefixes);
                                }
                                        
                                if (is_string($pattern)) {
                                        if (!empty($pattern)) {
                                                $o = '?o'.count($objects);
                                                $objects[] = $o;
                                                
                                                $object = trim(str_replace('*', '', $pattern));
                                                if(common_Utils::isUri($object)){
                                                        //if it is a uri, ignore "like" and "lang" options:
                                                        list($objectNS, $objectID) = explode('#', $object);
                                                        if(!empty($objectID)){
                                                                if(!isset($prefixes[$objectNS])){
                                                                        $prefixes[$objectNS] = 'NS'.count($prefixes);
                                                                }
                                                                $conditions[] = $prefixes[$propNS].':'.$propID.' '.$prefixes[$objectNS].':'.$objectID.' ; '; 
                                                        }
                                                }else{
                                                        if(!empty($lg)){//&& !common_Utils::isUri($object)
                                                                $filters[] = 'langMatches(lang('.$o.'),"'.$lg.'")';
                                                        }
                                                        if (!$like) {
                                                                $object = preg_match('/^\^/', $object)? $object : '^'.$object;
                                                                $object = preg_match('/\$$/', $object)? $object : $object.'$';
                                                        }
                                                        $filters[] = 'regex(str('.$o.'), "'.$object.'")';
                                                        $conditions[] = $prefixes[$propNS].':'.$propID.' '.$o.' ; ';
                                                }
                                        }
                                } else if (is_array($pattern)) {
                                        if (count($pattern) > 0) {
                                                $o = '?o'.count($objects);
                                                $objects[] = $o;
                                                
                                                $validLanguageMatching = true;
                                                $multiCondition = '(';
                                                foreach ($pattern as $i => $patternToken) {
                                                        if ($i > 0) {
                                                                $multiCondition .= " || ";
                                                        }
                                                        
                                                        $object = trim(str_replace('*', '', $patternToken));
                                                        
                                                        if(!$validLanguageMatching && common_Utils::isUri($object)) $validLanguageMatching = false;//no resource available for language dependent check
                                                        
                                                        if (!$like) {
                                                                $object = preg_match('/^\^/', $object)? $object : '^'.$object;
                                                                $object = preg_match('/\$$/', $object)? $object : $object.'$';
                                                        }
                                                
                                                        $multiCondition .= 'regex(str('.$o.'), "'.$object.'")';
                                                }
                                                
                                                if(!empty($lg) && $validLanguageMatching){
                                                        $filters[] = 'langMatches(lang('.$o.'),'.$lg.')';
                                                }
                                                
                                                $filters[] = $multiCondition.')';
                                                
                                                $conditions[] = $prefixes[$propNS].':'.$propID.' '.$o.' ; ';
                                        }
                                }
                        }
                }
                if(count($conditions) == 0){
                        return $returnValue;
                }
                
                //start building query:
                $query = '';
                
                //insert prefixes:
                foreach($prefixes as $ns => $alias){
                        $query .= '
                                PREFIX '.$alias.':<'.$ns.'#> ';
                }
                
                $query .= '
                        SELECT ?s WHERE {?s ';
                
                //append conditions:
                foreach($conditions as $condition){
                        $query .= ' '.$condition;
                }
                $query = substr_replace($query, '.', -2);//close conditions
                
                //add filters:
                $intersect = true;
                if(isset($options['chaining'])){
                        if(strtolower($options['chaining']) == 'or'){
                                $intersect = false;
                        }
                }
                if($intersect){
                        foreach($filters as $filter){
                                $query .='
                                        FILTER '.$filter;
                        }
                }else{
                        $query .='
                                FILTER (';
                        $i = 0;
                        foreach($filters as $filter){
                                if($i>0) $query .= ' || ';
                                $query .= $filter;
                                $i++;
                        }
                }
                $query .= '}';
                
                $resultArray = $virtuoso->execQuery($query);
                $count = count($resultArray);
                for($i=0; $i<$count; $i++){
                        if (isset($resultArray[$i][0])) {
                                $instanceUri = $resultArray[$i][0];
                                $returnValue[$instanceUri] = new core_kernel_classes_Resource($instanceUri);
                        }
                }
                
                //Check in the subClasses recurslively.
                // Be carefull, it can be perf consuming with large data set and subclasses
                (isset($options['recursive'])) ? $recursive = (bool)$options['recursive'] : $recursive = false;
                if($recursive){
                        //the recusivity depth is set to one level
                        foreach($resource->getSubClasses(true) as $subClass){
                                unset($propertyFilters[RDF_TYPE]);//reset the RDF_TYPE filter for recursive searching!!!
                                $returnValue = array_merge(
                                        $returnValue, 
                                        $subClass->searchInstances($propertyFilters, array_merge($options, array('recursive' => false)))
                                );
                        }
                }
        }
        
        
        // section 10-13-1--128--26678bb4:12fbafcb344:-8000:00000000000014F0 end

        return (array) $returnValue;
    }

    /**
     * Short description of method countInstances
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource resource
     * @return Integer
     */
    public function countInstances( core_kernel_classes_Resource $resource)
    {
        $returnValue = null;

        // section 127-0-1-1--700ce06c:130dbc6fc61:-8000:000000000000159D begin
        
        list($NS, $ID) = explode('#', $resource->uriResource);
        if(isset($ID) && !empty($ID)){
                
                $virtuoso = core_kernel_persistence_virtuoso_VirtuosoDataStore::singleton();
                
                $query = 'PREFIX resourceNS: <'.$NS.'#>  SELECT ?s WHERE {?s rdf:type resourceNS:'.$ID.'}';
                $resultArray = $virtuoso->execQuery($query);
                $returnValue = count($resultArray);
        }
        
        // section 127-0-1-1--700ce06c:130dbc6fc61:-8000:000000000000159D end

        return $returnValue;
    }

    /**
     * Short description of method singleton
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @return core_kernel_classes_Resource
     */
    public static function singleton()
    {
        $returnValue = null;

        // section 127-0-1-1--3a4c22:13104bcfe8d:-8000:00000000000022ED begin
        
        if (core_kernel_persistence_virtuoso_Class::$instance == null){
        	core_kernel_persistence_virtuoso_Class::$instance = new core_kernel_persistence_virtuoso_Class();
        }
        $returnValue = core_kernel_persistence_virtuoso_Class::$instance;
        
        // section 127-0-1-1--3a4c22:13104bcfe8d:-8000:00000000000022ED end

        return $returnValue;
    }

    /**
     * Short description of method isValidContext
     *
     * @access public
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     * @param  Resource resource
     * @return boolean
     */
    public function isValidContext( core_kernel_classes_Resource $resource)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--3a4c22:13104bcfe8d:-8000:00000000000022EF begin
        
        list($NS, $id) = explode('#', $resource->uriResource);
        if(isset($id) && !empty($id)){
                
                $virtuoso = core_kernel_persistence_virtuoso_VirtuosoDataStore::singleton();
                
                $query = 'PREFIX resourceNS: <'.$NS.'#>
                        SELECT ?p ?o WHERE {resourceNS:'.$id.' ?p ?o} LIMIT 1';
                
                $resultArray = $virtuoso->execQuery($query);
                $returnValue = count($resultArray)?true:false;
        }
        
        // section 127-0-1-1--3a4c22:13104bcfe8d:-8000:00000000000022EF end

        return (bool) $returnValue;
    }

} /* end of class core_kernel_persistence_virtuoso_Class */

?>