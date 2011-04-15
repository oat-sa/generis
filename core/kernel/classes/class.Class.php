<?php

error_reporting(E_ALL);

/**
 * The class of rdfs:classes. It implements basic tests like isSubClassOf(Class
 * instances, properties and subclasses retrieval, but also enable to edit it
 * setSubClassOf setProperty, etc.
 *
 * @author patrick.plichart@tudor.lu
 * @package core
 * @see http://www.w3.org/TR/rdf-schema/
 * @subpackage kernel_classes
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * Resource implements rdf:resource container identified by an uri (a string).
 * Methods enable meta data management for this resource
 *
 * @author patrick.plichart@tudor.lu
 * @see http://www.w3.org/RDF/
 * @version v1.0
 */
require_once('core/kernel/classes/class.Resource.php');

/* user defined includes */
// section 10-13-1--31-64e54c36:1190f0455d3:-8000:0000000000000767-includes begin
// section 10-13-1--31-64e54c36:1190f0455d3:-8000:0000000000000767-includes end

/* user defined constants */
// section 10-13-1--31-64e54c36:1190f0455d3:-8000:0000000000000767-constants begin
// section 10-13-1--31-64e54c36:1190f0455d3:-8000:0000000000000767-constants end

/**
 * The class of rdfs:classes. It implements basic tests like isSubClassOf(Class
 * instances, properties and subclasses retrieval, but also enable to edit it
 * setSubClassOf setProperty, etc.
 *
 * @access public
 * @author patrick.plichart@tudor.lu
 * @package core
 * @see http://www.w3.org/TR/rdf-schema/
 * @subpackage kernel_classes
 */
class core_kernel_classes_Class
    extends core_kernel_classes_Resource
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * returns the collection of direct subClasses (see getIndirectSubClassesOf
     * a complete list of subclasses)
     *
     * @access public
     * @author patrick.plichart@tudor.lu
     * @param  boolean recursive
     * @return array
     * @see http://www.w3.org/TR/rdf-schema/
     */
    public function getSubClasses($recursive = false)
    {
        $returnValue = array();

        // section 10-13-1--31-64e54c36:1190f0455d3:-8000:0000000000000772 begin
		$dbWrapper = core_kernel_classes_DbWrapper::singleton();
		$sqlQuery = "select subject FROM statements where predicate = '".RDF_SUBCLASSOF."' and object = '".$this->uriResource."'";
		$returnValue = array();
		$sqlResult = $dbWrapper->execSql($sqlQuery);
		while (!$sqlResult-> EOF){
			$subClass = new core_kernel_classes_Class($sqlResult->fields['subject']);
			$returnValue[$subClass->uriResource] = $subClass;
			if($recursive == true ){
				$plop = $subClass->getSubClasses(true);
				$returnValue = array_merge($returnValue , $plop);
			}
			$sqlResult->MoveNext();
		}

        // section 10-13-1--31-64e54c36:1190f0455d3:-8000:0000000000000772 end

        return (array) $returnValue;
    }

    /**
     * returns true if this is a rdfs:subClassOf $parentClass
     *
     * @access public
     * @author patrick.plichart@tudor.lu
     * @param  Class parentClass
     * @return boolean
     */
    public function isSubClassOf( core_kernel_classes_Class $parentClass)
    {
        $returnValue = (bool) false;

        // section 10-13-1--31--63d751b4:11914bbbbc4:-8000:0000000000000AF2 begin
		
		$dbWrapper = core_kernel_classes_DbWrapper::singleton();
        
		$query = "SELECT object FROM statements
					WHERE subject = ?
					AND predicate = ? AND object = ?";
		$result = $dbWrapper->execSql($query, array(
			$this->uriResource,
			RDF_SUBCLASSOF,
			$parentClass->uriResource
		));
		while(!$result-> EOF){
			$returnValue =  true;
			$result->moveNext();
			break;
		}
		if(!$returnValue){
			foreach ($parentClass->getSubClasses(true) as $subClass){
				if ($subClass->uriResource == $this->uriResource) {
					$returnValue =  true;
					break;
				}
			}
		}
		
		// section 10-13-1--31--63d751b4:11914bbbbc4:-8000:0000000000000AF2 end

        return (bool) $returnValue;
    }


    /**
     * returns all parent classes as a collection
     *
     * @access public
     * @author patrick.plichart@tudor.lu
     * @param  boolean recursive
     * @return array
     */
    public function getParentClasses($recursive = false)
    {
        $returnValue = array();

        // section 10-13-1--31--63d751b4:11914bbbbc4:-8000:0000000000000B20 begin
		//		$classes = core_kernel_classes_Session::singleton()->model->getindirectSuperClasses($this->uriResource);
		$returnValue =  array();
		$sqlQuery ="SELECT object FROM statements WHERE subject = '". $this->uriResource."' AND (predicate = '".
												RDF_SUBCLASSOF."' OR predicate = '".RDF_TYPE."')";

		$dbWrapper = core_kernel_classes_DbWrapper::singleton();
		$sqlResult = $dbWrapper->execSql($sqlQuery);

		while (!$sqlResult-> EOF){

			$parentClass = new core_kernel_classes_Class($sqlResult->fields['object']);

			$returnValue[$parentClass->uriResource] = $parentClass ;
			if($recursive == true && $parentClass->uriResource != RDF_CLASS && $parentClass->uriResource != RDF_RESOURCE){
				$plop = $parentClass->getParentClasses(true);
				$returnValue = array_merge($returnValue,$plop);
			}
			$sqlResult->MoveNext();
		}
        // section 10-13-1--31--63d751b4:11914bbbbc4:-8000:0000000000000B20 end

        return (array) $returnValue;
    }

    /**
     * Short description of method getProperties
     *
     * @access public
     * @author patrick.plichart@tudor.lu
     * @param  boolean recursive
     * @return array
     */
    public function getProperties($recursive = false)
    {
        $returnValue = array();

        // section 10-13-1--31-5c77d5ee:119187ec9d2:-8000:000000000000094B begin

		$sqlQuery = "select subject from statements where predicate = '". RDF_DOMAIN."' and object = '".$this->uriResource."'";
		$dbWrapper = core_kernel_classes_DbWrapper::singleton();
		$sqlResult = $dbWrapper->execSql($sqlQuery);

		while (!$sqlResult-> EOF){
			$property = new core_kernel_classes_Property($sqlResult->fields['subject']);
			$returnValue[$property->uriResource] = $property;
			$sqlResult->MoveNext();
		}
    	if($recursive == true) {
				$parentClasses = $this->getParentClasses(true);
				foreach ($parentClasses as $parent) {
					if($parent->uriResource != RDF_CLASS) {
						$returnValue = array_merge($returnValue,$parent->getProperties(true));
					}
				}
			}	
        // section 10-13-1--31-5c77d5ee:119187ec9d2:-8000:000000000000094B end

        return (array) $returnValue;
    }


    /**
     * return direct instances of this class as a collection
     *
     * @access public
     * @author patrick.plichart@tudor.lu
     * @param  boolean recursive
     * @return array
     */
    public function getInstances($recursive = false)
    {
        $returnValue = array();

        // section 10-13-1--31-5c77d5ee:119187ec9d2:-8000:0000000000000958 begin
		$sqlQuery = "select subject from statements
									where predicate = '".RDF_TYPE."'  
									and object = '".$this->uriResource."' ";

		$dbWrapper = core_kernel_classes_DbWrapper::singleton();
		$sqlResult = $dbWrapper->execSql($sqlQuery);

		while (!$sqlResult-> EOF){

			$instance = new core_kernel_classes_Resource($sqlResult->fields['subject']);
				
			$returnValue[$instance->uriResource] = $instance ;

			//In case of a meta class, subclasses of instances may be returned*/
			if (($instance->uriResource!=RDF_CLASS) 
				&& ($this->uriResource == RDF_CLASS)
				&& ($instance->uriResource!=RDF_RESOURCE)) {

				$instanceClass = new core_kernel_classes_Class($instance->uriResource);
				$subClasses = $instanceClass->getSubClasses(true);

				foreach($subClasses as $subClass) {
					$returnValue[$subClass->uriResource] = $subClass;
				}
			}	
			$sqlResult->MoveNext();
		}
		if($recursive == true){
			$subClasses = $this->getSubClasses(true);
			foreach ($subClasses as $subClass){
				$returnValue = array_merge($returnValue,$subClass->getInstances(true));
			}
		}
		
		if(defined('ENABLE_SUBSCRIPTION') && ENABLE_SUBSCRIPTION && $this->uriResource != CLASS_SUBCRIPTION){
			$typeProp = new core_kernel_classes_Property(RDF_TYPE);
			$subcriptions = core_kernel_subscriptions_Service::singleton()->getSubscriptions(null,$typeProp,$this);

			foreach ($subcriptions as $sub){
				$subcriptionResource = new core_kernel_classes_Resource($sub);
				$subcriptionsInstances = core_kernel_subscriptions_Service::singleton()->getInstancesFromSubscription($subcriptionResource,$this);
				$returnValue = array_merge($returnValue,$subcriptionsInstances);
			}
		}
        // section 10-13-1--31-5c77d5ee:119187ec9d2:-8000:0000000000000958 end

        return (array) $returnValue;
    }

    /**
     * creates a new instance of the class todo : different from the method
     * which simply link the previously created ressource with this class
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  Resource instance
     * @return core_kernel_classes_Resource
     */
    public function setInstance( core_kernel_classes_Resource $instance)
    {
        $returnValue = null;

        // section 10-13-1--31-5c77d5ee:119187ec9d2:-8000:0000000000000978 begin

        $rdfType = new core_kernel_classes_Property(RDF_TYPE);
        $newInstance = clone $instance;	//call Resource::__clone
		$newInstance->setPropertyValue($rdfType, $this->uriResource);
		
		$returnValue = $newInstance;

        // section 10-13-1--31-5c77d5ee:119187ec9d2:-8000:0000000000000978 end

        return $returnValue;
    }

    /**
     * alias to setPropertyValues using rdfs: subClassOf, uriClass must be a
     * Class otherwise it returns false
     *
     * @access public
     * @author patrick.plichart@tudor.lu
     * @param  Class iClass
     * @return boolean
     */
    public function setSubClassOf( core_kernel_classes_Class $iClass)
    {
        $returnValue = (bool) false;

        // section 127-0-0-1-6c221a5e:1193c8e5541:-8000:0000000000000AB0 begin
        
		$subClassOf = new core_kernel_classes_Property(RDF_SUBCLASSOF);
		$returnValue = $this->setPropertyValue($subClassOf,$iClass->uriResource);
		
        // section 127-0-0-1-6c221a5e:1193c8e5541:-8000:0000000000000AB0 end

        return (bool) $returnValue;
    }

    /**
     * add a property to the class, uriProperty must be a valid property
     * the method returns false
     *
     * @access public
     * @author patrick.plichart@tudor.lu
     * @param  Property property
     * @return boolean
     */
    public function setProperty( core_kernel_classes_Property $property)
    {
        $returnValue = (bool) false;

        // section 127-0-0-1-6c221a5e:1193c8e5541:-8000:0000000000000AC1 begin
		
        $domain = new core_kernel_classes_Property(RDF_DOMAIN,__METHOD__);
		$instanceProperty = new core_kernel_classes_Resource($property->uriResource,__METHOD__);
		$returnValue = $instanceProperty->setPropertyValue($domain, $this->uriResource);
        
		// section 127-0-0-1-6c221a5e:1193c8e5541:-8000:0000000000000AC1 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method __construct
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  string uri
     * @param  string debug
     * @return void
     */
    public function __construct($uri, $debug = '')
    {
        // section 10-5-2-6-d9cdd2e:11b0c43cdd8:-8000:0000000000000D4D begin
		parent::__construct($uri,$debug);
        // section 10-5-2-6-d9cdd2e:11b0c43cdd8:-8000:0000000000000D4D end
    }


    /**
     * Creates the hyperClass related to a class
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return core_kernel_hyperclasses_HyperClass
     */
    public function createHyperClass()
    {
        $returnValue = null;

        // section 10-13-1--31-42d46662:11bb6ef4845:-8000:0000000000000D54 begin
			
			
		//creates a new hyperclass with the same label, comment
		//investigate the possibility to add a new type to the class as hyperclass , ie, addInstance instead of setInstance
		$classOfHyperClass = new core_kernel_classes_Class(CLASS_HYPERCLASS);
		$this->label =$this->label.time();
		$hyperClass = $classOfHyperClass->setInstance($this);
			
		//creates rootNode
		$classNode = new core_kernel_classes_Class(CLASS_NODE);
		$rootNode = $classNode->setInstance($this);

		//links the rootNode with the class
		$rootNode->setPropertyValue(new core_kernel_classes_Property(PROPERTY_REL_CLASS), $this->uriResource);
			
		//links the hyperclass with this node as rootnode
		$hyperClass->setPropertyValue(new core_kernel_classes_Property(PROPERTY_ROOTNODE), $rootNode->uriResource);



		//loops among properties of this class, create nodeattributes and link them with the rootnode
		$propertiesCollection = $this->getProperties();
		foreach ($propertiesCollection->sequence as $property)
		{

			$classNodeAttribute = new core_kernel_classes_Class(CLASS_NODE_ATTRIBUTE);
			$nodeAttribute = $classNodeAttribute->setInstance($property);

			//links the default css to this nodeattribute
			$nodeAttribute->setPropertyValue(new core_kernel_classes_Property(PROPERTY_CSS_STYLE), "simple");

			//links the default css to this nodeattribute
			$nodeAttribute->setPropertyValue(new core_kernel_classes_Property(PROPERTY_IS_EDITABLE), "http://www.tao.lu/Ontologies/generis.rdf#True");

			//links the related property to this nodeattribute
			$nodeAttribute->setPropertyValue(new core_kernel_classes_Property(PROPERTY_REL_PROPERTY), $property->uriResource);

			//links the rootNode with this NodeAttribute
			$rootNode->setPropertyValue(new core_kernel_classes_Property(PROPERTY_NODE_ATTRIBUTES), $nodeAttribute->uriResource);


		}
		$returnValue =$hyperClass;
        // section 10-13-1--31-42d46662:11bb6ef4845:-8000:0000000000000D54 end

        return $returnValue;
    }



    /**
     * Short description of method createInstance
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  string label
     * @param  string comment
     * @param  string uri
     * @return core_kernel_classes_Resource
     */
    public function createInstance($label = '', $comment = '', $uri = '')
    {
        $returnValue = null;

        // section 10-13-1--99-5d680c37:11e406b020f:-8000:0000000000000F23 begin

		if($uri == ''){
			$subject = common_Utils::getNewUri();
		}
		else {
			//$uri should start with # and be well formed
			$modelUri = core_kernel_classes_Session::singleton()->getNameSpace();
			$subject = $modelUri . $uri;
		}

		$returnValue = new core_kernel_classes_Resource($subject,__METHOD__);
		
		$rdfType = new core_kernel_classes_Property(RDF_TYPE);
		$returnValue->setPropertyValue($rdfType, $this->uriResource);
		
		if ($label != '') {
			$returnValue->setLabel($label);
		}
		if( $comment != '') {
			$returnValue->setComment($comment);
		}

        // section 10-13-1--99-5d680c37:11e406b020f:-8000:0000000000000F23 end

        return $returnValue;
    }

    /**
     * Short description of method createSubClass
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  string label
     * @param  string comment
     * @return core_kernel_classes_Class
     */
    public function createSubClass($label = '', $comment = '')
    {
        $returnValue = null;

        // section 10-13-1--99-3835caab:11e45736d24:-8000:0000000000000F2A begin
		$class = new core_kernel_classes_Class(RDF_CLASS,__METHOD__);
		$intance = $class->createInstance($label,$comment);
		$returnValue = new core_kernel_classes_Class($intance->uriResource);
		$returnValue->setSubClassOf($this);
        // section 10-13-1--99-3835caab:11e45736d24:-8000:0000000000000F2A end

        return $returnValue;
    }

    /**
     * Short description of method createProperty
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  string label
     * @param  string comment
     * @param  boolean isLgDependent
     * @return core_kernel_classes_Property
     */
    public function createProperty($label = '', $comment = '', $isLgDependent = false)
    {
        $returnValue = null;

        // section 10-13-1--99--47c96501:11e4ab45b34:-8000:0000000000000F34 begin
		$property = new core_kernel_classes_Class(RDF_PROPERTY,__METHOD__);
		$propertyInstance = $property->createInstance($label,$comment);
		$returnValue = new core_kernel_classes_Property($propertyInstance->uriResource,__METHOD__);
		$returnValue->setLgDependent($isLgDependent);
		
		if (!$this->setProperty($returnValue)){
			throw new common_Exception('proplem creating property');
		}
        // section 10-13-1--99--47c96501:11e4ab45b34:-8000:0000000000000F34 end

        return $returnValue;
    }

    /**
     * Short description of method getMethodes
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return array
     */
    public function getMethodes()
    {
        $returnValue = array();

        // section -87--2--3--76--148ee98a:12452773959:-8000:00000000000017DF begin
        $returnValue = array( 'instanciate' => true , 'addSubclass' => true , 'addPropery' => true);
        // section -87--2--3--76--148ee98a:12452773959:-8000:00000000000017DF end

        return (array) $returnValue;
    }



} /* end of class core_kernel_classes_Class */

?>