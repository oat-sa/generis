<?php

error_reporting(E_ALL);

/**
 * uriProperty must be a valid property otherwis return false, add this as a
 * of uriProperty
 *
 * @author patrick.plichart@tudor.lu
 * @package core
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
// section 10-13-1--31-64e54c36:1190f0455d3:-8000:0000000000000769-includes begin
// section 10-13-1--31-64e54c36:1190f0455d3:-8000:0000000000000769-includes end

/* user defined constants */
// section 10-13-1--31-64e54c36:1190f0455d3:-8000:0000000000000769-constants begin
// section 10-13-1--31-64e54c36:1190f0455d3:-8000:0000000000000769-constants end

/**
 * uriProperty must be a valid property otherwis return false, add this as a
 * of uriProperty
 *
 * @access public
 * @author patrick.plichart@tudor.lu
 * @package core
 * @subpackage kernel_classes
 */
class core_kernel_classes_Property
    extends core_kernel_classes_Resource
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * The property domain defines the classes the property is attached to.
     *
     * @access public
     * @var ContainerCollection
     */
    public $domain = null;

    /**
     * The property's range defines either the possibles class' instances 
     * or a literal value if the range is the Literal class
     *
     * @access public
     * @var Class
     */
    public $range = null;

    /**
     * The widget the can be used to represents the property
     *
     * @access public
     * @var Property
     */
    public $widget = null;

    /**
     * If the property depends of a language or not
     *
     * @access private
     * @var boolean
     */
    private $lgDependent = true;

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string uri
     * @param  string debug
     * @return void
     */
    public function __construct($uri, $debug = '')
    {
        // section 10-5-2-6--89b5018:11b0b8ddfb0:-8000:0000000000000D5E begin
		parent::__construct($uri,$debug);
        // section 10-5-2-6--89b5018:11b0b8ddfb0:-8000:0000000000000D5E end
    }

    /**
     * Short description of method feed
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return void
     */
    public function feed()
    {
        // section 10-5-2-6--89b5018:11b0b8ddfb0:-8000:0000000000000D60 begin
		
    	parent::feed();
		$this->getWidget();
		$this->getRange();
		$this->getDomain();
		$this->isLgDependent();
		
        // section 10-5-2-6--89b5018:11b0b8ddfb0:-8000:0000000000000D60 end
    }

    /**
     * Short description of method getSubProperties
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  boolean recursive
     * @return array
     */
    public function getSubProperties($recursive = false)
    {
        $returnValue = array();

        // section 10-13-1--31-64e54c36:1190f0455d3:-8000:0000000000000780 begin
        $dbWrapper = core_kernel_classes_DbWrapper::singleton();
        $sqlQuery = "select subject from statements where predicate = 'http://www.w3.org/2000/01/rdf-schema#subPropertyOf' and object = '".$this->uriResource."'";
        $returnValue = array();
		$sqlResult = $dbWrapper->execSql($sqlQuery);
		while (!$sqlResult-> EOF){
			$property = new core_kernel_classes_Property($sqlResult->fields['subject']);
			$returnValue[$property->uriResource] = $property;

			if($recursive == true) {
				$returnValue = array_merge($returnValue,$property->getSubProperties(true));
			}
			$sqlResult->MoveNext();
		}
        // section 10-13-1--31-64e54c36:1190f0455d3:-8000:0000000000000780 end

        return (array) $returnValue;
    }

    /**
     * Short description of method setSubPropertyOf
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Property property
     * @return boolean
     */
    public function setSubPropertyOf( core_kernel_classes_Property $property)
    {
        $returnValue = (bool) false;

        // section 127-0-0-1-6c221a5e:1193c8e5541:-8000:0000000000000ABC begin
       
        // section 127-0-0-1-6c221a5e:1193c8e5541:-8000:0000000000000ABC end

        return (bool) $returnValue;
    }

    /**
     * return classes that are described by this property
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return core_kernel_classes_ContainerCollection
     */
    public function getDomain()
    {
        $returnValue = null;

        // section 10-13-1--31--64270bf:11918ad765e:-8000:0000000000000972 begin
        if (is_null($this->domain)){
        	$this->domain = new core_kernel_classes_ContainerCollection(new common_Object(__METHOD__));
			$domainValues = $this->getPropertyValues(new core_kernel_classes_Property(RDF_DOMAIN));
			foreach ($domainValues as $domainValue){
				$this->domain->add(new core_kernel_classes_Class($domainValue));
			}
		}
		$returnValue = $this->domain;
        // section 10-13-1--31--64270bf:11918ad765e:-8000:0000000000000972 end

        return $returnValue;
    }

    /**
     * Short description of method setDomain
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Class class
     * @return boolean
     */
    public function setDomain( core_kernel_classes_Class $class)
    {
        $returnValue = (bool) false;

        // section 127-0-0-1-6c221a5e:1193c8e5541:-8000:0000000000000AB4 begin
        
        if(!is_null($class)){
        	foreach($this->getDomain()->getIterator() as $domainClass){
        		if($class->uriResource == $domainClass->uriResource){
        			$returnValue = true;
        			break;
        		}
        	}
        	if(!$returnValue){
        		$this->setPropertyValue(new core_kernel_classes_Property(RDF_DOMAIN), $class->uriResource);
        		if(!is_null($this->domain)){
        			$this->domain->add($class);
        		}
        		$returnValue = true;
        	}
        }
        
        // section 127-0-0-1-6c221a5e:1193c8e5541:-8000:0000000000000AB4 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getRange
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return core_kernel_classes_ContainerCollection
     */
    public function getRange()
    {
        $returnValue = null;

        // section 127-0-0-1-6c221a5e:1193c8e5541:-8000:0000000000000ABA begin
		if (is_null($this->range)){
			$rangeValues = $this->getPropertyValues(new core_kernel_classes_Property(RDFS_RANGE));
			if (sizeOf($rangeValues)>0)
			{
				$this->range = new core_kernel_classes_Class($rangeValues[0]);
			}
			else
			{
				$this->range = new core_kernel_classes_Class("");
			}
		}
		$returnValue=$this->range;
        // section 127-0-0-1-6c221a5e:1193c8e5541:-8000:0000000000000ABA end

        return $returnValue;
    }

    /**
     * Short description of method setRange
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Class class
     * @return boolean
     */
    public function setRange( core_kernel_classes_Class $class)
    {
        $returnValue = (bool) false;

        // section 127-0-0-1-6c221a5e:1193c8e5541:-8000:0000000000000AB7 begin
        
        $rangeProp = new core_kernel_classes_Property(RDFS_RANGE,__METHOD__);
        $this->setPropertyValue($rangeProp, $class->uriResource);
        
        // section 127-0-0-1-6c221a5e:1193c8e5541:-8000:0000000000000AB7 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getWidget
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return core_kernel_classes_Property
     */
    public function getWidget()
    {
        $returnValue = null;

        // section 10-5-2-6--89b5018:11b0b8ddfb0:-8000:0000000000000D5C begin
		
        if (!(isset($this->widget))){
			$returnValue = $this->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_WIDGET));
		}
		
        // section 10-5-2-6--89b5018:11b0b8ddfb0:-8000:0000000000000D5C end

        return $returnValue;
    }

    /**
     * Short description of method isLgDependent
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return boolean
     */
    public function isLgDependent()
    {
        $returnValue = (bool) false;

        // section 10-13-1--99--152a2f30:1201eae099d:-8000:000000000000157A begin

        if(is_null($this->lgDependent )){
	        
        	$lgDependentProperty = new core_kernel_classes_Property(PROPERTY_IS_LG_DEPENDENT,__METHOD__);
	        $lgDependent = $this->getOnePropertyValue($lgDependentProperty);
	        if(is_null($lgDependent)){
	        	$this->lgDependent = false;
	        }
	        else{
	        	$this->lgDependent = ($lgDependent->uriResource == GENERIS_TRUE);
	        }     	
        }
 
        $returnValue = $this->lgDependent;
        
        // section 10-13-1--99--152a2f30:1201eae099d:-8000:000000000000157A end

        return (bool) $returnValue;
    }

    /**
     * Short description of method setLgDependent
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  boolean isLgDependent
     * @return mixed
     */
    public function setLgDependent($isLgDependent)
    {
        // section 10-13-1--99--152a2f30:1201eae099d:-8000:000000000000157E begin
        
    	$lgDependentProperty = new core_kernel_classes_Property(PROPERTY_IS_LG_DEPENDENT,__METHOD__);
        $value = $isLgDependent ?  GENERIS_TRUE : GENERIS_FALSE ;
        if($this->editPropertyValues($lgDependentProperty,$value)) {
        	$this->lgDependent = $isLgDependent;
        }
        
        // section 10-13-1--99--152a2f30:1201eae099d:-8000:000000000000157E end
    }

} /* end of class core_kernel_classes_Property */

?>