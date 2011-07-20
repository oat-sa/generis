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
		
        $returnValue = core_kernel_persistence_ClassProxy::singleton()->getSubClasses ($this, $recursive);

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
		
        $returnValue = core_kernel_persistence_ClassProxy::singleton()->isSubClassOf ($this, $parentClass);
		
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
		
        $returnValue = core_kernel_persistence_ClassProxy::singleton()->getParentClasses ($this, $recursive);
		
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
		
        $returnValue = core_kernel_persistence_ClassProxy::singleton()->getProperties ($this, $recursive);

		
        // section 10-13-1--31-5c77d5ee:119187ec9d2:-8000:000000000000094B end

        return (array) $returnValue;
    }

    /**
     * return direct instances of this class as a collection
     *
     * @access public
     * @author patrick.plichart@tudor.lu
     * @param  boolean recursive
     * @param  array params
     * @return array
     */
    public function getInstances($recursive = false, $params = array())
    {
        $returnValue = array();

        // section 10-13-1--31-5c77d5ee:119187ec9d2:-8000:0000000000000958 begin
		
        $returnValue = core_kernel_persistence_ClassProxy::singleton()->getInstances ($this, $recursive, $params);

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
		
        $returnValue = core_kernel_persistence_ClassProxy::singleton()->setInstance ($this, $instance);
        
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
		
        $returnValue = core_kernel_persistence_ClassProxy::singleton()->setSubClassOf ($this, $iClass);
        
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
		
        $returnValue = core_kernel_persistence_ClassProxy::singleton()->setProperty ($this, $property);
        
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
     * @return mixed
     */
    public function createHyperClass()
    {
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
		
        $returnValue = core_kernel_persistence_ClassProxy::singleton()->createInstance ($this, $label, $comment, $uri);

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
		
        $returnValue = core_kernel_persistence_ClassProxy::singleton()->createSubClass ($this, $label, $comment);
        
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
		
        $returnValue = core_kernel_persistence_ClassProxy::singleton()->createProperty ($this, $label, $comment, $isLgDependent);
        
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

    /**
     * Short description of method searchInstances
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  array propertyFilters
     * @param  array options
     * @return array
     */
    public function searchInstances($propertyFilters = array(), $options = array())
    {
        $returnValue = array();

        // section 10-13-1--128--26678bb4:12fbafcb344:-8000:0000000000001503 begin
		$returnValue = core_kernel_persistence_ClassProxy::singleton()->searchInstances ($this, $propertyFilters, $options);
        // section 10-13-1--128--26678bb4:12fbafcb344:-8000:0000000000001503 end

        return (array) $returnValue;
    }

    /**
     * Short description of method countInstances
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return Integer
     */
    public function countInstances()
    {
        $returnValue = null;

        // section 127-0-1-1--700ce06c:130dbc6fc61:-8000:000000000000159B begin
		$returnValue = core_kernel_persistence_ClassProxy::singleton()->countInstances ($this);
        // section 127-0-1-1--700ce06c:130dbc6fc61:-8000:000000000000159B end

        return $returnValue;
    }

    /**
     * Short description of method getInstancesPropertyValues
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  Property property
     * @param  array propertyFilters
     * @param  array options
     * @return array
     */
    public function getInstancesPropertyValues( core_kernel_classes_Property $property, $propertyFilters = array(), $options = array())
    {
        $returnValue = array();

        // section 127-0-1-1--120bf54f:13142fdf597:-8000:0000000000003137 begin
        $returnValue = core_kernel_persistence_ClassProxy::singleton()->getInstancesPropertyValues ($this, $property, $propertyFilters, $options);
        // section 127-0-1-1--120bf54f:13142fdf597:-8000:0000000000003137 end

        return (array) $returnValue;
    }

} /* end of class core_kernel_classes_Class */

?>