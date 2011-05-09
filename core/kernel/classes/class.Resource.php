<?php

error_reporting(E_ALL);

/**
 * Resource implements rdf:resource container identified by an uri (a string).
 * Methods enable meta data management for this resource
 *
 * @author patrick.plichart@tudor.lu
 * @package core
 * @see http://www.w3.org/RDF/
 * @subpackage kernel_classes
 * @version v1.0
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include core_kernel_classes_Container
 *
 * @author patrick.plichart@tudor.lu
 */
require_once('core/kernel/classes/class.Container.php');

/* user defined includes */
// section 10-13-1--31-64e54c36:1190f0455d3:-8000:0000000000000765-includes begin
// section 10-13-1--31-64e54c36:1190f0455d3:-8000:0000000000000765-includes end

/* user defined constants */
// section 10-13-1--31-64e54c36:1190f0455d3:-8000:0000000000000765-constants begin
// section 10-13-1--31-64e54c36:1190f0455d3:-8000:0000000000000765-constants end

/**
 * Resource implements rdf:resource container identified by an uri (a string).
 * Methods enable meta data management for this resource
 *
 * @access public
 * @author patrick.plichart@tudor.lu
 * @package core
 * @see http://www.w3.org/RDF/
 * @subpackage kernel_classes
 * @version v1.0
 */
class core_kernel_classes_Resource
    extends core_kernel_classes_Container
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * long uri as string (including namespace)
     *
     * @access public
     * @var string
     */
    public $uriResource = '';

    /**
     * The resource label
     *
     * @access public
     * @var string
     */
    public $label = '';

    /**
     * The resource comment
     *
     * @access public
     * @var string
     */
    public $comment = '';

    // --- OPERATIONS ---

    /**
     * create the object
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  string uri
     * @param  string debug
     * @return void
     */
    public function __construct($uri, $debug = '')
    {
        // section 127-0-0-1-59fa2263:1193cca7051:-8000:0000000000000AFB begin
        //we should check using utils if the uri is short or long always use long uri inside the api (nevertheless the api may be called with short )
        $this->uriResource =$uri;
        
        if(DEBUG_MODE){
        	$this->debug = $debug;
        }
        // section 127-0-0-1-59fa2263:1193cca7051:-8000:0000000000000AFB end
    }

    /**
     * Conveniance method to duplicate a resource using the clone keyword
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return core_kernel_classes_Resource
     */
    public function __clone()
    {
        $returnValue = null;

        // section 10-13-1--31-64e54c36:1190f0455d3:-8000:000000000000082A begin
        
        $returnValue = $this->duplicate();
        
        // section 10-13-1--31-64e54c36:1190f0455d3:-8000:000000000000082A end

        return $returnValue;
    }

    /**
     * returns true if the resource is a valid class (using facts or entailment
     *
     * @access public
     * @author patrick.plichart@tudor.lu
     * @return boolean
     * @see http://www.w3.org/RDF/
     */
    public function isClass()
    {
        $returnValue = (bool) false;

        // section 10-13-1--31--647ec317:119141cd117:-8000:0000000000000913 begin
        
        $returnValue = in_array(RDF_CLASS, $this->getType());

        // section 10-13-1--31--647ec317:119141cd117:-8000:0000000000000913 end

        return (bool) $returnValue;
    }

    /**
     * returns true if the resource is a valid property (using facts or
     * rules)
     *
     * @access public
     * @author patrick.plichart@tudor.lu
     * @return boolean
     * @see http://www.w3.org/RDF/
     */
    public function isProperty()
    {
        $returnValue = (bool) false;

        // section 10-13-1--31--647ec317:119141cd117:-8000:0000000000000915 begin
        
        $returnValue = in_array(RDF_PROPERTY, $this->getType());
        
        // section 10-13-1--31--647ec317:119141cd117:-8000:0000000000000915 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getType
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return array
     */
    public function getType()
    {
        $returnValue = array();

        // section 127-0-1-1-62cf85dc:12bab18dc39:-8000:000000000000135F begin
        
        $returnValue = core_kernel_persistence_ResourceProxy::singleton()->getType ($this);
        
        // section 127-0-1-1-62cf85dc:12bab18dc39:-8000:000000000000135F end

        return (array) $returnValue;
    }

    /**
     * returns label of the resources as string, alias to getPropertyValues
     * rdfs:label property
     *
     * @access public
     * @author patrick.plichart
     * @return string
     * @see www.generis.lu/documentation/design#getLabel
     * @version 1.0
     */
    public function getLabel()
    {
        $returnValue = (string) '';

        // section 10-13-1--31-64e54c36:1190f0455d3:-8000:0000000000000840 begin
        
        if($this->label == '') {
            $label =  $this->getOnePropertyValue(new core_kernel_classes_Property(RDFS_LABEL));
            $this->label = ($label != null) ? $label->literal : '';
        }
        $returnValue = $this->label;
        
        // section 10-13-1--31-64e54c36:1190f0455d3:-8000:0000000000000840 end

        return (string) $returnValue;
    }

    /**
     * alias to setPropertyValue using rdfs:label property
     *
     * @access public
     * @author patrick.plichart@tudor:lu
     * @param  string label
     * @return boolean
     */
    public function setLabel($label)
    {
        $returnValue = (bool) false;

        // section 127-0-0-1-6c221a5e:1193c8e5541:-8000:0000000000000AA6 begin

        $this->removePropertyValues(new core_kernel_classes_Property(RDFS_LABEL));
        $this->setPropertyValue(new core_kernel_classes_Property(RDFS_LABEL), $label);
        $this->label = $label;
        
        // section 127-0-0-1-6c221a5e:1193c8e5541:-8000:0000000000000AA6 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getComment
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return string
     */
    public function getComment()
    {
        $returnValue = (string) '';

        // section 127-0-1-1--f4ec538:12c30e15fc8:-8000:00000000000013A8 begin
        if($this->comment == '') {
            $comment =  $this->getOnePropertyValue(new core_kernel_classes_Property(RDFS_COMMENT));
            $this->comment = $comment != null ? $comment->literal : '';
             
        }
        $returnValue = $this->comment;
        // section 127-0-1-1--f4ec538:12c30e15fc8:-8000:00000000000013A8 end

        return (string) $returnValue;
    }

    /**
     * alias to setPropertyValue using rdfs:label property
     *
     * @access public
     * @author patrick.plichart
     * @param  string comment
     * @return boolean
     */
    public function setComment($comment)
    {
        $returnValue = (bool) false;

        // section 127-0-0-1-6c221a5e:1193c8e5541:-8000:0000000000000AA8 begin
        
        $this->removePropertyValues(new core_kernel_classes_Property(RDFS_COMMENT));
        $this->setPropertyValue(new core_kernel_classes_Property(RDFS_COMMENT), $comment);
        $this->comment = $comment;
        
        // section 127-0-0-1-6c221a5e:1193c8e5541:-8000:0000000000000AA8 end

        return (bool) $returnValue;
    }

    /**
     * Returns a collection of triples with all objects found for the provided
     * regarding the contextual resource
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Property property uriProperty is string and may be short in the case of a locally defined property (module namespace), or long uri
     * @return array
     */
    public function getPropertyValues( core_kernel_classes_Property $property)
    {
        $returnValue = array();

        // section 127-0-0-1-71ce5466:11938f47d30:-8000:0000000000000A99 begin
    	
        $returnValue = core_kernel_persistence_ResourceProxy::singleton()->getPropertyValues ($this, $property);

        // section 127-0-0-1-71ce5466:11938f47d30:-8000:0000000000000A99 end

        return (array) $returnValue;
    }

    /**
     * Short description of method getPropertyValuesCollection
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Property property
     * @return core_kernel_classes_ContainerCollection
     */
    public function getPropertyValuesCollection( core_kernel_classes_Property $property)
    {
        $returnValue = null;

        // section 10-13-1--99--32cd3c54:11be55033bf:-8000:0000000000000D79 begin
    	
        $returnValue = core_kernel_persistence_ResourceProxy::singleton()->getPropertyValuesCollection ($this, $property);
        
        // section 10-13-1--99--32cd3c54:11be55033bf:-8000:0000000000000D79 end

        return $returnValue;
    }

    /**
     * Short description of method getUniquePropertyValue
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Property property
     * @return core_kernel_classes_Container
     */
    public function getUniquePropertyValue( core_kernel_classes_Property $property)
    {
        $returnValue = null;

        // section 10-13-1--99--2465c76a:11c0440e8db:-8000:0000000000001466 begin
 
        $collection = $this->getPropertyValuesCollection($property);

        if($collection->isEmpty()){
        	$propLabel = $property->getLabel();
        	$label = $this->getLabel();
            throw new common_Exception("Property {$propLabel} ({$property->uriResource}) of resource {$label} ({$this->uriResource})
            							 should not be empty");
        }
        if($collection->count() == 1 ) {
            $returnValue= $collection->get(0);
            if(DEBUG_MODE){
            	$returnValue->debug = __METHOD__;
            }
        }
        else {
        	$propLabel = $property->getLabel();
        	$label = $this->getLabel();
            throw new common_Exception("Property {$propLabel} ({$property->uriResource}) of resource {$label} ({$this->uriResource}) 
            							has more than one value do not use getUniquePropertyValue but use getPropertyValue instead");
        }
        	
        // section 10-13-1--99--2465c76a:11c0440e8db:-8000:0000000000001466 end

        return $returnValue;
    }

    /**
     * Short description of method getOnePropertyValue
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Property property
     * @param  boolean last
     * @return core_kernel_classes_Container
     */
    public function getOnePropertyValue( core_kernel_classes_Property $property, $last = false)
    {
        $returnValue = null;

        // section -87--2--3--76-51a982f1:1278aabc987:-8000:0000000000008925 begin
        
        $returnValue = core_kernel_persistence_ResourceProxy::singleton()->getOnePropertyValue ($this, $property, $last);
      
        // section -87--2--3--76-51a982f1:1278aabc987:-8000:0000000000008925 end

        return $returnValue;
    }

    /**
     * Short description of method getPropertyValuesByLg
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Property property
     * @param  string lg
     * @return core_kernel_classes_ContainerCollection
     */
    public function getPropertyValuesByLg( core_kernel_classes_Property $property, $lg)
    {
        $returnValue = null;

        // section -87--2--3--76--570dd3e1:12507aae5fa:-8000:00000000000023A4 begin
        
        $returnValue = core_kernel_persistence_ResourceProxy::singleton()->getPropertyValuesByLg ($this, $property, $lg);
        
        // section -87--2--3--76--570dd3e1:12507aae5fa:-8000:00000000000023A4 end

        return $returnValue;
    }

    /**
     * assign the (string) object for the provided uriProperty reagarding the
     * resource
     *
     * @access public
     * @author Patrick.plichart
     * @param  Property property
     * @param  string object
     * @return boolean
     * @version 1.0
     */
    public function setPropertyValue( core_kernel_classes_Property $property, $object)
    {
        $returnValue = (bool) false;

        // section 10-13-1--31-64e54c36:1190f0455d3:-8000:00000000000007AC begin
        
        $returnValue = core_kernel_persistence_ResourceProxy::singleton()->setPropertyValue ($this, $property, $object);
        
        // section 10-13-1--31-64e54c36:1190f0455d3:-8000:00000000000007AC end

        return (bool) $returnValue;
    }

    /**
     * Set multiple properties and their value at one time. 
     * Conveniance method isntead of adding the property values one by one.
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  array propertiesValues
     * @return boolean
     */
    public function setPropertiesValues($propertiesValues)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-44e4845c:12f4ef0414d:-8000:0000000000001437 begin
        
        $returnValue = core_kernel_persistence_ResourceProxy::singleton()->setPropertiesValues ($this, $propertiesValues);
        
        // section 127-0-1-1-44e4845c:12f4ef0414d:-8000:0000000000001437 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method setPropertyValueByLg
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Property property
     * @param  string value
     * @param  string lg
     * @return boolean
     */
    public function setPropertyValueByLg( core_kernel_classes_Property $property, $value, $lg)
    {
        $returnValue = (bool) false;

        // section -87--2--3--76-2d6cca2d:12579c74420:-8000:0000000000001831 begin
        
        $returnValue = core_kernel_persistence_ResourceProxy::singleton()->setPropertyValueByLg ($this, $property, $value, $lg);
        
        // section -87--2--3--76-2d6cca2d:12579c74420:-8000:0000000000001831 end

        return (bool) $returnValue;
    }

    /**
     * edit the assigned value(s) for the provided uriProperty regarding the
     * resource using the provided object. Specific assignation edition (a
     * triple) shouldbe made using triple management
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Property property
     * @param  string object
     * @return boolean
     */
    public function editPropertyValues( core_kernel_classes_Property $property, $object)
    {
        $returnValue = (bool) false;

        // section 10-13-1--31-64e54c36:1190f0455d3:-8000:00000000000009D5 begin
        $this->removePropertyValues($property);
        if(is_array($object)){
            foreach($object as $value){
                $returnValue = $this->setPropertyValue($property, (string) $value);
            }
        }else{
            $returnValue = $this->setPropertyValue($property, (string) $object);
        }

        // section 10-13-1--31-64e54c36:1190f0455d3:-8000:00000000000009D5 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method editPropertyValueByLg
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Property prop
     * @param  string value
     * @param  string lg
     * @return boolean
     */
    public function editPropertyValueByLg( core_kernel_classes_Property $prop, $value, $lg)
    {
        $returnValue = (bool) false;

        // section -87--2--3--76-18452630:1270a514a71:-8000:00000000000023F0 begin
        $returnValue = $this->removePropertyValueByLg($prop,$lg);
        $returnValue &= $this->setPropertyValueByLg($prop,$value,$lg);
        // section -87--2--3--76-18452630:1270a514a71:-8000:00000000000023F0 end

        return (bool) $returnValue;
    }

    /**
     * remove all triples with this subject and predicate
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Property property
     * @return boolean
     */
    public function removePropertyValues( core_kernel_classes_Property $property)
    {
        $returnValue = (bool) false;

        // section 10-13-1--31--64270bf:11918ad765e:-8000:000000000000097C begin

        $returnValue = core_kernel_persistence_ResourceProxy::singleton()->removePropertyValues ($this, $property);
        
        // section 10-13-1--31--64270bf:11918ad765e:-8000:000000000000097C end

        return (bool) $returnValue;
    }

    /**
     * Short description of method removePropertyValueByLg
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Property prop
     * @param  string lg
     * @return boolean
     */
    public function removePropertyValueByLg( core_kernel_classes_Property $prop, $lg)
    {
        $returnValue = (bool) false;

        // section -87--2--3--76-18452630:1270a514a71:-8000:00000000000023EC begin
       
        $returnValue = core_kernel_persistence_ResourceProxy::singleton()->removePropertyValueByLg ($this, $prop, $lg);
        
        // section -87--2--3--76-18452630:1270a514a71:-8000:00000000000023EC end

        return (bool) $returnValue;
    }

    /**
     * returns all generis statements about an uri, rdf level (there is no
     * inferred by the rdfs level), restricted according to rights priviliges
     * on statements
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return core_kernel_classes_ContainerCollection
     */
    public function getRdfTriples()
    {
        $returnValue = null;

        // section 10-13-1--31--63d751b4:11914bbbbc4:-8000:0000000000000B29 begin
        
        $returnValue = core_kernel_persistence_ResourceProxy::singleton()->getRdfTriples ($this);
        
        // section 10-13-1--31--63d751b4:11914bbbbc4:-8000:0000000000000B29 end

        return $returnValue;
    }

    /**
     * return the languages in which a value exists for uriProperty for this
     *
     * @access public
     * @author aptrick.plichart@tudor.lu
     * @param  Property property
     * @return array
     */
    public function getUsedLanguages( core_kernel_classes_Property $property)
    {
        $returnValue = array();

        // section 10-13-1--31--63d751b4:11914bbbbc4:-8000:0000000000000B13 begin
        $sqlQuery = "select l_language from statements where subject = '". $this->uriResource."' and predicate = '". $property->uriResource . "' ";
        $dbWrapper = core_kernel_classes_DbWrapper::singleton();
        $sqlResult = $dbWrapper->execSql($sqlQuery);
        while (!$sqlResult-> EOF){
            $returnValue[]=$sqlResult->fields['l_language'];
            $sqlResult->MoveNext();
        }

        // section 10-13-1--31--63d751b4:11914bbbbc4:-8000:0000000000000B13 end

        return (array) $returnValue;
    }

    /**
     * Duplicate a resource: create a new URI and duplicate all the triples
     * those with the predicate listed in excludedProperties.
     * The method returns the new resource.
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  array excludedProperties
     * @return core_kernel_classes_Resource
     */
    public function duplicate($excludedProperties = array())
    {
        $returnValue = null;

        // section 127-0-1-1-440a1f14:12e71f49661:-8000:000000000000141E begin
        
        $returnValue = core_kernel_persistence_ResourceProxy::singleton()->duplicate ($this, $excludedProperties);
        
        // section 127-0-1-1-440a1f14:12e71f49661:-8000:000000000000141E end

        return $returnValue;
    }

    /**
     * remove any assignation made to this resource, the uri is consequently
     *
     * @access public
     * @author patrick.plichart@tudor.lu
     * @param  boolean deleteReference set deleteRefence to true when you need that all reference to this resource are removed.
     * @return boolean
     */
    public function delete($deleteReference = false)
    {
        $returnValue = (bool) false;

        // section 10-13-1--31-5c77d5ee:119187ec9d2:-8000:0000000000000976 begin
        
        $returnValue = core_kernel_persistence_ResourceProxy::singleton()->delete ($this, $deleteReference);
        
        // section 10-13-1--31-5c77d5ee:119187ec9d2:-8000:0000000000000976 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getPrivileges
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return array
     */
    public function getPrivileges()
    {
        $returnValue = array();

        // section -87--2--3--76--148ee98a:12452773959:-8000:00000000000017DD begin
        $returnValue = array(
       						'u' => array('r' => true, 'w' => true),
        		 			'g' => array('r' => true, 'w' => true),
        		 			'a' => array('r' => true, 'w' => true)
        );
        // section -87--2--3--76--148ee98a:12452773959:-8000:00000000000017DD end

        return (array) $returnValue;
    }

    /**
     * Short description of method getLastModificationDate
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Property property
     * @return doc_date
     */
    public function getLastModificationDate( core_kernel_classes_Property $property = null)
    {
        $returnValue = null;

        // section -87--2--3--76--148ee98a:12452773959:-8000:000000000000235D begin
        
        $returnValue = core_kernel_persistence_ResourceProxy::singleton()->getLastModificationDate ($this, $property);
        
        // section -87--2--3--76--148ee98a:12452773959:-8000:000000000000235D end

        return $returnValue;
    }

    /**
     * Short description of method getLastModificationUser
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return string
     */
    public function getLastModificationUser()
    {
        $returnValue = (string) '';

        // section -87--2--3--76--148ee98a:12452773959:-8000:0000000000002361 begin
        
        $returnValue = core_kernel_persistence_ResourceProxy::singleton()->getLastModificationUser ($this);
        
        // section -87--2--3--76--148ee98a:12452773959:-8000:0000000000002361 end

        return (string) $returnValue;
    }

    /**
     * Short description of method toHtml
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return string
     */
    public function toHtml()
    {
        $returnValue = (string) '';

        // section 10-13-1--31--3bf74db1:119c3d777ef:-8000:0000000000000B3F begin
        $returnValue .= '<span style="postition:relative;margin:5px;display:block;align:center;border: #9c9c9c 1px solid;border-color:black;font-family:Verdana;background-color:#Ffffcc;width:32%;height:9%;">';
        $returnValue .= '<span style="display:block;height=10px;border: #9c9c9c 1px solid;border-color:black;font-weight:bold;text-align:center;background-color:#ffcc99;font-size:10;">';
        $returnValue .= ''.$this->label;
        $returnValue .= '</span>';
        $returnValue .= '<span style="display:block;height=90px;font-weight:normal;font-style:italic;font-size:9;">';
        $returnValue .= ''.$this->comment."<br />";
        $returnValue .= '<span style="font-size:5;">'.$this->uriResource.'</span>';
        $returnValue .= '</span>';

        $returnValue .= '</span>';

        // section 10-13-1--31--3bf74db1:119c3d777ef:-8000:0000000000000B3F end

        return (string) $returnValue;
    }

    /**
     * Short description of method __toString
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return string
     */
    public function __toString()
    {
        $returnValue = (string) '';

        // section 10-13-1--99-20ac9d48:11a723d33d6:-8000:0000000000001253 begin
        $returnValue = $this->uriResource.'<br/>' . $this->getLabel() . '<br/>' ;
        // section 10-13-1--99-20ac9d48:11a723d33d6:-8000:0000000000001253 end

        return (string) $returnValue;
    }

    /**
     * Short description of method getPropertiesValue
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  array properties
     * @param  boolean last
     * @return array
     */
    public function getPropertiesValue($properties, $last)
    {
        $returnValue = array();

        // section 127-0-1-1-77557f59:12fa87873f4:-8000:00000000000014CD begin
        
        $returnValue = core_kernel_persistence_ResourceProxy::singleton()->getPropertiesValue ($this, $properties, $last);
        
        // section 127-0-1-1-77557f59:12fa87873f4:-8000:00000000000014CD end

        return (array) $returnValue;
    }

    /**
     * Short description of method setType
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Class type
     * @return boolean
     */
    public function setType( core_kernel_classes_Class $type)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--398d2ad6:12fd3f7ebdd:-8000:0000000000001550 begin
        
        $returnValue = core_kernel_persistence_ResourceProxy::singleton()->setType ($this, $type);
        
        // section 127-0-1-1--398d2ad6:12fd3f7ebdd:-8000:0000000000001550 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method removeType
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Class type
     * @return boolean
     */
    public function removeType( core_kernel_classes_Class $type)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--398d2ad6:12fd3f7ebdd:-8000:0000000000001553 begin
        
        $returnValue = core_kernel_persistence_ResourceProxy::singleton()->removeType ($this, $type);
        
        // section 127-0-1-1--398d2ad6:12fd3f7ebdd:-8000:0000000000001553 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method hasType
     *
     * @access public
     * @author Cedric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Class class
     * @return boolean
     */
    public function hasType( core_kernel_classes_Class $class)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1--72f5bf1f:12fd500f94d:-8000:0000000000001552 begin
        
    	foreach ($this->getType () as $type){
        	if ($class->uriResource == $type->uriResource){
        		$returnValue = true;
        		break;
        	}
        }
        
        // section 127-0-1-1--72f5bf1f:12fd500f94d:-8000:0000000000001552 end

        return (bool) $returnValue;
    }

} /* end of class core_kernel_classes_Resource */

?>