<?php

/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg
 *                         (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung
 *                         (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor
 *                         (under the project TAO-SUSTAIN & TAO-DEV);
 *               2017-2021 (update and modification) Open Assessment Technologies SA;
 */

use oat\generis\model\OntologyRdf;
use oat\generis\model\OntologyRdfs;
use oat\oatbox\event\EventAggregator;
use oat\oatbox\service\ServiceManager;
use oat\generis\model\OntologyAwareTrait;
use Zend\ServiceManager\ServiceLocatorInterface;
use oat\generis\model\data\event\ResourceUpdated;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use oat\generis\model\resource\Repository\ResourceRepository;
use oat\generis\model\resource\Context\ResourceRepositoryContext;
use oat\generis\model\resource\Contract\ResourceRepositoryInterface;

/**
 * Resource implements rdf:resource container identified by an uri (a string).
 * Methods enable meta data management for this resource
 *
 * @author patrick.plichart@tudor.lu
 *
 * @see http://www.w3.org/RDF/
 */
class core_kernel_classes_Resource extends core_kernel_classes_Container
{
    use OntologyAwareTrait;

    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * long uri as string (including namespace)
     * direct access to the uri is deprecated,
     * please use getUri()
     *
     * @access public
     * @var string
     * @deprecated
     */
    public $uriResource = '';

    /**
     * The resource label
     * direct access to the label is deprecated,
     * please use getLabel()
     *
     * @access public
     * @var string
     * @deprecated
     */
    public $label = null;

    /**
     * The resource comment
     * direct access to the comment is deprecated,
     * please use getComment()
     *
     * @access public
     * @var string
     * @deprecated
     */
    public $comment = '';

    // --- OPERATIONS ---
    /**
     *
     * @return core_kernel_persistence_ResourceInterface
     */
    private function getImplementation()
    {
        return $this->getModel()->getRdfsInterface()->getResourceImplementation();
    }


    /**
     * create the object
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  mixed uri
     * @param  string debug
     * @return void
     */
    public function __construct($uri, $debug = '')
    {
        if (empty($uri)) {
            throw new common_exception_Error(
                'cannot construct the resource because the uri cannot be empty, debug: ' . $debug
            );
        }
        if (!is_string($uri) && !$uri instanceof self) {
            throw new common_exception_Error(
                'could not create resource from ' . (is_object($uri) ? get_class($uri) : gettype($uri))
                    . ' debug: ' . $debug
            );
        }
        $this->uriResource = $uri instanceof self ? $uri->getUri() : $uri;
    }


    /**
     * Conveniance method to duplicate a resource using the clone keyword
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     */
    public function __clone()
    {
        throw new common_exception_DeprecatedApiMethod(
            'Use duplicated instead, because clone resource could not share same uri that original'
        );
    }

    public function isCustom(): bool
    {
        $uri = $this->getUri();

        return strpos($uri, 'www.tao.lu') === false && strpos($uri, 'www.w3.org') === false;
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
        if (count($this->getPropertyValues($this->getProperty(OntologyRdfs::RDFS_SUBCLASSOF))) > 0) {
            $returnValue = true;
        } else {
            foreach ($this->getTypes() as $type) {
                if ($type->getUri() == OntologyRdfs::RDFS_CLASS) {
                    $returnValue = true;
                    break;
                }
            }
        }
        return (bool) $returnValue;
    }

    public function isWritable(): bool
    {
        $implementation = $this->getImplementation();

        if ($implementation instanceof core_kernel_persistence_smoothsql_Resource) {
            return $implementation->isWritable($this);
        }

        return true;
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
        foreach ($this->getTypes() as $type) {
            if ($type->getUri() == OntologyRdf::RDF_PROPERTY) {
                $returnValue = true;
                break;
            }
        }
        return (bool) $returnValue;
    }

    /**
     * Returns all the types of this resource as core_kernel_classes_Class objects.
     *
     * @author Joel Bout <joel@taotesting.com>
     * @return core_kernel_classes_Class[] An associative array where keys are class URIs and values are
     *                                     core_kernel_classes_Class objects.
     */
    public function getTypes()
    {
        return $this->getImplementation()->getTypes($this);
    }

    /**
     * Returns the label of this resource as a string. This method is a convenience
     * method preventing to call the get getPropertyValues() method for a such common
     * operation.
     *
     * @author Patrick Plichart <patrick@taotesting.com>
     * @return string A Uniform Resource Identifier (URI).
     */
    public function getLabel()
    {
        if (is_null($this->label)) {
            $label =  $this->getOnePropertyValue($this->getProperty(OntologyRdfs::RDFS_LABEL));
            $this->label = is_null($label)
                ? ''
                : (
                    $label instanceof core_kernel_classes_Resource
                    ? $label->getUri()
                    : (string)$label
                )
            ;
        }

        return $this->label;
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
        $this->removePropertyValues($this->getProperty(OntologyRdfs::RDFS_LABEL));
        $this->setPropertyValue($this->getProperty(OntologyRdfs::RDFS_LABEL), $label);
        $this->label = $label;
        return (bool) $returnValue;
    }

    /**
     * Short description of method getComment
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return string
     */
    public function getComment()
    {
        $returnValue = (string) '';
        if ($this->comment == '') {
            $comment =  $this->getOnePropertyValue($this->getProperty(OntologyRdfs::RDFS_COMMENT));
            $this->comment = $comment != null ? $comment->literal : '';
        }
        $returnValue = $this->comment;
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
        $this->removePropertyValues($this->getProperty(OntologyRdfs::RDFS_COMMENT));
        $this->setPropertyValue($this->getProperty(OntologyRdfs::RDFS_COMMENT), $comment);
        $this->comment = $comment;
        return (bool) $returnValue;
    }

    /**
     * Returns a collection of triples with all objects found for the provided
     * regarding the contextual resource.
     * The return format is an array of strings
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param core_kernel_classes_Property $property uriProperty is string and may be short in the case of a locally
     *                                               defined property (module namespace), or long uri
     * @param array $options
     * @return array
     */
    public function getPropertyValues(core_kernel_classes_Property $property, $options = [])
    {
        $returnValue = $this->getImplementation()->getPropertyValues($this, $property, $options);
        return $returnValue;
    }

    /**
     * Return a collection of values associated to $property
     *
     * @param core_kernel_classes_Property $property
     * @param array $options
     * @return core_kernel_classes_ContainerCollection
     */
    public function getPropertyValuesCollection(core_kernel_classes_Property $property, $options = [])
    {
        $returnValue = new core_kernel_classes_ContainerCollection($this);
        foreach ($this->getPropertyValues($property, $options) as $value) {
            $returnValue->add($this->toResource($value));
        }
        return $returnValue;
    }

    /**
     * Short description of method getUniquePropertyValue
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Property property
     * @throws common_Exception
     * @throws core_kernel_classes_EmptyProperty
     * @return core_kernel_classes_Container
     */
    public function getUniquePropertyValue(core_kernel_classes_Property $property)
    {
        $returnValue = null;

        $collection = $this->getPropertyValuesCollection($property);

        if ($collection->isEmpty()) {
            throw new core_kernel_classes_EmptyProperty($this, $property);
        }
        if ($collection->count() == 1) {
            $returnValue = $collection->get(0);
        } else {
            throw new core_kernel_classes_MultiplePropertyValuesException($this, $property);
        }
        return $returnValue;
    }

    /**
     * Helper to return one property value, since there is no order
     * if there are multiple values the value to be returned will be choosen by random
     *
     * optional parameter $last should NOT be used since it is no longer supported
     * and will be removed in future versions
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Property property
     * @param  boolean last
     * @return core_kernel_classes_Container
     */
    public function getOnePropertyValue(core_kernel_classes_Property $property, $last = false)
    {
        $returnValue = null;
        if ($last) {
            throw new core_kernel_persistence_Exception(
                'parameter \'last\' for getOnePropertyValue no longer supported'
            );
        };

        $options = [
            'forceDefaultLg' => true,
            'one' => true
        ];

        $value = $this->getPropertyValues($property, $options);

        if (count($value)) {
            $returnValue = $this->toResource(current($value));
        }

        return $returnValue;
    }

    /**
     * Short description of method getPropertyValuesByLg
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Property property
     * @param  string lg
     * @return core_kernel_classes_ContainerCollection
     */
    public function getPropertyValuesByLg(core_kernel_classes_Property $property, $lg)
    {
        $returnValue = null;
        $returnValue = $this->getImplementation()->getPropertyValuesByLg($this, $property, $lg);
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
    public function setPropertyValue(core_kernel_classes_Property $property, $object)
    {
        $returnValue = (bool) false;
        $returnValue = $this->getImplementation()->setPropertyValue($this, $property, $object);
        $this->onUpdate();
        return (bool) $returnValue;
    }

    /**
     * Set multiple properties and their value at one time.
     * Conveniance method isntead of adding the property values one by one.
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  array propertiesValues
     * @return boolean
     */
    public function setPropertiesValues($propertiesValues)
    {
        $returnValue = (bool) false;
        $returnValue = $this->getImplementation()->setPropertiesValues($this, $propertiesValues);
        $this->onUpdate();
        return (bool) $returnValue;
    }

    /**
     * Short description of method setPropertyValueByLg
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Property property
     * @param  string value
     * @param  string lg
     * @return boolean
     */
    public function setPropertyValueByLg(core_kernel_classes_Property $property, $value, $lg)
    {
        $returnValue = (bool) false;
        $returnValue = $this->getImplementation()->setPropertyValueByLg($this, $property, $value, $lg);
        $this->onUpdate();
        return (bool) $returnValue;
    }

    /**
     * edit the assigned value(s) for the provided uriProperty regarding the
     * resource using the provided object. Specific assignation edition (a
     * triple) shouldbe made using triple management
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Property property
     * @param  string object
     * @return boolean
     */
    public function editPropertyValues(core_kernel_classes_Property $property, $object)
    {
        $returnValue =  $this->removePropertyValues($property);
        if (is_array($object)) {
            foreach ($object as $value) {
                $returnValue = $this->setPropertyValue($property, $value);
            }
        } else {
            $returnValue = $this->setPropertyValue($property, $object);
        }

        return (bool) $returnValue;
    }

    /**
     * Short description of method editPropertyValueByLg
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Property prop
     * @param  string value
     * @param  string lg
     * @return boolean
     */
    public function editPropertyValueByLg(core_kernel_classes_Property $prop, $value, $lg)
    {
        $returnValue = (bool) false;
        $returnValue = $this->removePropertyValueByLg($prop, $lg);
        $returnValue &= $this->setPropertyValueByLg($prop, $value, $lg);
        return (bool) $returnValue;
    }

    /**
     * remove a single triple with this subject and predicate
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Property property
     * @param  mixed value
     * @return boolean
     */
    public function removePropertyValue(core_kernel_classes_Property $property, $value)
    {
        $returnValue = (bool) false;
        $returnValue = $this->getImplementation()->removePropertyValues($this, $property, [
            'pattern'   => (is_object($value) && $value instanceof self ? $value->getUri() : $value),
            'like'      => false
        ]);
        $this->onUpdate();
        return (bool) $returnValue;
    }

    /**
     * remove all triples with this subject and predicate
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Property property
     * @param  array options
     * @return boolean
     */
    public function removePropertyValues(core_kernel_classes_Property $property, $options = [])
    {
        $returnValue = (bool) false;
        $returnValue = $this->getImplementation()->removePropertyValues($this, $property, $options);
        $this->onUpdate();
        return (bool) $returnValue;
    }

    /**
     * Short description of method removePropertyValueByLg
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Property prop
     * @param  string lg
     * @param  array options
     * @return boolean
     */
    public function removePropertyValueByLg(core_kernel_classes_Property $prop, $lg, $options = [])
    {
        $returnValue = (bool) false;
        $returnValue = $this->getImplementation()->removePropertyValueByLg($this, $prop, $lg, $options);
        $this->onUpdate();
        return (bool) $returnValue;
    }

    /**
     * returns all generis statements about an uri, rdf level (there is no
     * inferred by the rdfs level), restricted according to rights priviliges
     * on statements
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return core_kernel_classes_ContainerCollection
     */
    public function getRdfTriples()
    {
        $returnValue = null;
        $returnValue = $this->getImplementation()->getRdfTriples($this);
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
    public function getUsedLanguages(core_kernel_classes_Property $property)
    {
        return $this->getImplementation()->getUsedLanguages($this, $property);
    }

    /**
     * Duplicate a resource: create a new URI and duplicate all the triples
     * those with the predicate listed in excludedProperties.
     * The method returns the new resource.
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  array excludedProperties
     * @return core_kernel_classes_Resource
     */
    public function duplicate($excludedProperties = [])
    {
        $returnValue = $this->getImplementation()->duplicate($this, $excludedProperties);
        return $returnValue;
    }

    /**
     * Remove any assignation made to this resource, the uri is consequently
     *
     * @deprecated Use \oat\generis\model\resource\Repository\ResourceRepository::delete() instead
     *
     * @author patrick.plichart@tudor.lu
     *
     * @param bool $deleteReference set deleteReference to true when you need that all reference to this resource are
     *                              removed.
     *
     * @return bool
     */
    public function delete($deleteReference = false)
    {
        try {
            $this->getResourceRepository()->delete(
                new ResourceRepositoryContext(
                    [
                        ResourceRepositoryContext::PARAM_RESOURCE => $this,
                        ResourceRepositoryContext::PARAM_DELETE_REFERENCE => $deleteReference,
                    ]
                )
            );

            return true;
        } catch (Throwable $exception) {
            return false;
        }
    }



    /**
     * Short description of method __toString
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return string
     */
    public function __toString(): string
    {
        return $this->getUri() . "\n" . $this->getLabel() ;
    }

    /**
     * Short description of method getPropertiesValues
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  array properties
     * @return array
     */
    public function getPropertiesValues($properties): array
    {
        if (!is_array($properties)) {
            throw new common_exception_InvalidArgumentType(__CLASS__, __FUNCTION__, 0, 'array', $properties);
        }
        $returnValue = $this->getImplementation()->getPropertiesValues($this, $properties/*, $last*/);
        return (array) $returnValue;
    }

    /**
     * Short description of method setType
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Class type
     * @return boolean
     */
    public function setType(core_kernel_classes_Class $type): bool
    {
        $returnValue = $this->getImplementation()->setType($this, $type);

        $this->onUpdate();

        return (bool) $returnValue;
    }

    /**
     * Short description of method removeType
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Class type
     * @return boolean
     */
    public function removeType(core_kernel_classes_Class $type): bool
    {
        $returnValue = $this->getImplementation()->removeType($this, $type);

        $this->onUpdate();

        return (bool) $returnValue;
    }

    /**
     * Short description of method hasType
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Class class
     * @return boolean
     */
    public function hasType(core_kernel_classes_Class $class): bool
    {
        $returnValue = (bool) false;
        foreach ($this->getTypes() as $type) {
            if ($class->equals($type)) {
                $returnValue = true;
                break;
            }
        }
        return (bool) $returnValue;
    }

    /**
     * Short description of method exists
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return boolean
     */
    public function exists()
    {
        $returnValue = (bool) false;
        try {
            $returnValue = count($this->getTypes()) ? true : false;
        } catch (Exception $e) {
            ;//return false by default
        }
        return (bool) $returnValue;
    }

    /**
     * returns the full URI as string (including namespace)
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @return string
     */
    public function getUri(): string
    {
        return (string) $this->uriResource;
    }

    /**
     * Short description of method equals
     *
     * @access public
     * @author Joel Bout, <joel.bout@tudor.lu>
     * @param  Resource resource
     * @return boolean
     */
    public function equals(core_kernel_classes_Resource $resource): bool
    {
        return $this->getUri() === $resource->getUri();
    }

    /**
     * Whenever or not the current resource is an instance of the specified class
     *
     * @author Joel Bout, <joel.bout@tudor.lu>
     */
    public function isInstanceOf(core_kernel_classes_Class $class): bool
    {
        foreach ($this->getTypes() as $type) {
            if ($class->equals($type) || $type->isSubClassOf($class)) {
                return true;
            }
        }

        return false;
    }

    public function getRootId(): string
    {
        $parentClassesIds = $this->getParentClassesIds();

        return array_pop($parentClassesIds);
    }

    /**
     * Return the parent class URI of a resource
     */
    public function getParentClassId(): ?string
    {
        return current($this->getParentClassesIds()) ?: null;
    }

    public function getParentClassesIds(): array
    {
        $implementation = $this->getImplementation();

        if ($implementation instanceof core_kernel_persistence_smoothsql_Resource) {
            return $implementation->getParentClassesIds($this->getUri());
        }

        return [];
    }

    /**
     * @return array [
     *     '{classUri}' => [
     *         '{resourceUri_1}',
     *         '{resourceUri_N...}',
     *     ]
     * ]
     */
    public function getParentClassesResourceIds(array $classIds = null): array
    {
        $implementation = $this->getImplementation();

        if ($implementation instanceof core_kernel_persistence_smoothsql_Resource) {
            return $implementation->getClassesResourceIds($classIds ?? $this->getParentClassesIds());
        }

        return [];
    }

    /**
     * Returns a list of nested resources/classes under a resource
     *
     * @return array [
     *     [
     *         'id' => '{resourceId}',
     *         'isClass' => true|false,
     *         'level' => 1..N,
     *     ]
     * ]
     */
    public function getNestedResources(): array
    {
        $implementation = $this->getImplementation();

        if ($implementation instanceof core_kernel_persistence_smoothsql_Resource) {
            return $implementation->getNestedResources($this->getUri());
        }

        return [];
    }

    public function getServiceManager(): ServiceLocatorInterface
    {
        return ($this->getModel() instanceof ServiceLocatorAwareInterface)
            ? $this->getModel()->getServiceLocator()
            : ServiceManager::getServiceManager();
    }

    /**
     * Moved from common_Utils to not break dependency ingestion chain
     * @param string $value
     * @return core_kernel_classes_Literal|core_kernel_classes_Resource|core_kernel_classes_Resource[]
     */
    protected function toResource($value)
    {
        if (is_array($value)) {
            $returnValue = [];
            foreach ($value as $val) {
                $returnValue[] = $this->toResource($val);
            }
            return $returnValue;
        } else {
            if (common_Utils::isUri($value)) {
                return $this->getResource($value);
            } else {
                return new core_kernel_classes_Literal($value);
            }
        }
    }

    private function onUpdate(): void
    {
        /** @var EventAggregator $eventAggregator */
        $eventAggregator = $this->getServiceManager()->get(EventAggregator::SERVICE_ID);
        $eventAggregator->put($this->getUri(), new ResourceUpdated($this));
    }

    private function getResourceRepository(): ResourceRepositoryInterface
    {
        return $this->getServiceManager()->getContainer()->get(ResourceRepository::class);
    }
}
