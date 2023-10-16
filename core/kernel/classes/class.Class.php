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
 *               2017-2021 (update and modification) Open Assessment Technologies SA.
 */

use oat\generis\model\OntologyRdf;
use oat\oatbox\event\EventManager;
use oat\oatbox\event\EventManagerAwareTrait;
use oat\generis\model\data\event\ResourceCreated;
use oat\generis\model\resource\ResourceCollection;
use oat\generis\model\resource\Repository\ClassRepository;
use oat\generis\model\resource\Context\ResourceRepositoryContext;
use oat\generis\model\resource\Contract\ResourceRepositoryInterface;

/**
 * The class of rdfs:classes. It implements basic tests like isSubClassOf(Class
 * instances, properties and subclasses retrieval, but also enable to edit it
 * setSubClassOf setProperty, etc.
 *
 * @author patrick.plichart@tudor.lu
 *
 * @see http://www.w3.org/RDF/
 * @see http://www.w3.org/TR/rdf-schema/
 */
class core_kernel_classes_Class extends core_kernel_classes_Resource
{
    use EventManagerAwareTrait;

    /**
     *
     * @return core_kernel_persistence_ClassInterface
     */
    protected function getImplementation()
    {
        return $this->getModel()->getRdfsInterface()->getClassImplementation();
    }


    /**
     * returns the collection of direct subClasses (see getIndirectSubClassesOf
     * a complete list of subclasses)
     *
     * @access public
     * @author patrick.plichart@tudor.lu
     * @param  boolean recursive
     * @return \core_kernel_classes_Class[]
     * @see http://www.w3.org/TR/rdf-schema/
     */
    public function getSubClasses($recursive = false)
    {
        return (array) $this->getImplementation()->getSubClasses($this, $recursive);
    }

    /**
     * returns true if this is a rdfs:subClassOf $parentClass
     *
     * @access public
     * @author patrick.plichart@tudor.lu
     * @param  \core_kernel_classes_Class parentClass
     * @return boolean
     */
    public function isSubClassOf(core_kernel_classes_Class $parentClass)
    {
        return (bool) $this->getImplementation()->isSubClassOf($this, $parentClass);
    }

    /**
     * returns all parent classes as a collection
     *
     * @access public
     * @author patrick.plichart@tudor.lu
     * @param  boolean recursive
     * @return \core_kernel_classes_Class[]
     */
    public function getParentClasses($recursive = false)
    {
        return (array) $this->getImplementation()->getParentClasses($this, $recursive);
    }

    /**
     * Returns the Properties bound to the Class. If the $recursive parameter is
     * to true, the whole class hierarchy will be inspected from the current
     * to the top one to retrieve tall its properties.
     *
     * @access public
     * @author patrick.plichart@tudor.lu
     * @param  boolean recursive Recursive Properties retrieval accross the Class hierarchy.
     * @return \core_kernel_classes_Property[]
     */
    public function getProperties($recursive = false)
    {
        return (array) $this->getImplementation()->getProperties($this, $recursive);
    }

    /**
     * return direct instances of this class as a collection
     *
     * @access public
     * @author patrick.plichart@tudor.lu
     * @param  boolean recursive
     * @param  array params
     * @return \core_kernel_classes_Resource[]
     */
    public function getInstances($recursive = false, $params = [])
    {
        return (array) $this->getImplementation()->getInstances($this, $recursive, $params);
    }

    /**
     * return direct instances of this class as a collection
     *
     * @param  boolean recursive
     * @param  array params
     * @return ResourceCollection
     */
    public function getInstanceCollection()
    {
        return new ResourceCollection($this);
    }

    /**
     * creates a new instance of the class todo : different from the method
     * which simply link the previously created ressource with this class
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  \core_kernel_classes_Resource instance
     * @return core_kernel_classes_Resource
     * @deprecated
     */
    public function setInstance(core_kernel_classes_Resource $instance)
    {
        return $this->getImplementation()->setInstance($this, $instance);
    }

    /**
     * alias to setPropertyValues using rdfs: subClassOf, uriClass must be a
     * Class otherwise it returns false
     *
     * @access public
     * @author patrick.plichart@tudor.lu
     * @param  \core_kernel_classes_Class iClass
     * @return boolean
     */
    public function setSubClassOf(core_kernel_classes_Class $iClass)
    {
        return (bool) $this->getImplementation()->setSubClassOf($this, $iClass);
    }

    /**
     * add a property to the class, uriProperty must be a valid property
     * the method returns false
     *
     * @access public
     * @author patrick.plichart@tudor.lu
     * @param  \core_kernel_classes_Property property
     * @return boolean
     * @deprecated
     */
    public function setProperty(core_kernel_classes_Property $property)
    {
        return (bool) $this->getImplementation()->setProperty($this, $property);
    }

    /**
     * Should not be called by application code, please use
     * core_kernel_classes_ResourceFactory::create() instead
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string label
     * @param  string comment
     * @param  string uri
     * @return \core_kernel_classes_Resource
     */
    public function createInstance($label = '', $comment = '', $uri = '')
    {
        $returnValue = $this->getImplementation()->createInstance($this, $label, $comment, $uri);
        $eventManager = $this->getServiceManager()->get(EventManager::SERVICE_ID);
        $eventManager->trigger(new ResourceCreated($returnValue));
        return $returnValue;
    }

    /**
     * Short description of method createSubClass
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string label
     * @param  string comment
     * @param  string uri
     * @return core_kernel_classes_Class
     */
    public function createSubClass($label = '', $comment = '', $uri = "")
    {
        $returnValue = $this->getImplementation()->createSubClass($this, $label, $comment, $uri);
        $eventManager = $this->getServiceManager()->get(EventManager::SERVICE_ID);
        $eventManager->trigger(new ResourceCreated($returnValue));
        return $returnValue;
    }

    /**
     * Retrieves a direct subclass by label.
     *
     * @param  string label
     * @return core_kernel_classes_Class|null
     */
    public function retrieveSubClassByLabel($label)
    {
        $subClasses = $this->getSubClasses();
        foreach ($subClasses as $subclass) {
            if ($subclass->getLabel() === $label) {
                return $subclass;
            }
        }

        return null;
    }

    /**
     * Retrieves a direct subclass by label or creates it if not existent.
     *
     * @param  string label
     * @return core_kernel_classes_Class
     */
    public function retrieveOrCreateSubClassByLabel($label)
    {
        return $this->retrieveSubClassByLabel($label) ?: $this->createSubClass($label);
    }

    /**
     * Creates a path of subclasses from an array of labels, URIs and comments.
     *
     * @param  array $labels indexed array of labels ordered from root to leaf class
     * @return core_kernel_classes_Class The last class created
     */
    public function createSubClassPathByLabel(array $labels)
    {
        $currentClass = $this;

        foreach ($labels as $label) {
            $currentClass = $currentClass->retrieveOrCreateSubClassByLabel($label);
        }

        return $currentClass;
    }

    /**
     * Short description of method createProperty
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string label
     * @param  string comment
     * @param  boolean isLgDependent
     * @return core_kernel_classes_Property
     */
    public function createProperty($label = '', $comment = '', $isLgDependent = false)
    {
        return $this->getImplementation()->createProperty($this, $label, $comment, $isLgDependent);
    }

    /**
     * Retrieve available methods on class
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return array
     */
    public function getMethodes()
    {
        return [ 'instanciate' => true , 'addSubclass' => true , 'addPropery' => true];
    }

    /**
     * Search for a specific instances according to filters and options
     *
     * options lists:
     * like         : (bool)    true/false (default: true)
     * chaining     : (string)  'or'/'and' (default: 'and')
     * recursive    : (bool)    search in subvlasses(default: false)
     * lang         : (string)  e.g. 'en-US', 'fr-FR' (default: '') for all properties!
     * offset       : default 0
     * limit        : default select all
     * order        : property to order by
     * orderdir     : direction of order (default: 'ASC')
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  array propertyFilters
     * @param  array options
     * @return \core_kernel_classes_Resource[]
     */
    public function searchInstances($propertyFilters = [], $options = [])
    {
        return (array) $this->getImplementation()->searchInstances($this, $propertyFilters, $options);
    }

    /**
     * Short description of method countInstances
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  array propertyFilters
     * @param  array options
     * @return integer
     */
    public function countInstances($propertyFilters = [], $options = [])
    {
        return $this->getImplementation()->countInstances($this, $propertyFilters, $options);
    }

    /**
     * Get instances' property values.
     * The instances can be filtered.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  core_kernel_classes_Property property
     * @param  array propertyFilters
     * @param  array options
     * @return \core_kernel_classes_Resource[]
     */
    public function getInstancesPropertyValues(
        core_kernel_classes_Property $property,
        $propertyFilters = [],
        $options = []
    ) {
        return (array) $this->getImplementation()->getInstancesPropertyValues(
            $this,
            $property,
            $propertyFilters,
            $options
        );
    }

    /**
     * Unset the domain of the property related to the class
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  core_kernel_classes_Property property
     * @deprecated
     */
    public function unsetProperty(core_kernel_classes_Property $property)
    {
        $this->getImplementation()->unsetProperty($this, $property);
    }

    /**
     * please use core_kernel_classes_ResourceFactory::create()
     * instead of this function whenever possible
     *
     * Creates a new instance using the properties provided.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  array properties May contain additional types
     * @return core_kernel_classes_Resource
     * @see core_kernel_classes_ResourceFactory
     */
    public function createInstanceWithProperties($properties)
    {
        $returnValue = null;
        // remove the additional types, because they might be implemented differently

        $additionalTypes = [];
        if (isset($properties[OntologyRdf::RDF_TYPE])) {
            $types = is_array($properties[OntologyRdf::RDF_TYPE])
                ? $properties[OntologyRdf::RDF_TYPE]
                : [$properties[OntologyRdf::RDF_TYPE]];
            foreach ($types as $type) {
                $uri = is_object($type) ? $type->getUri() : $type;
                if ($uri != $this->getUri()) {
                    $additionalTypes[] = $this->getClass($uri);
                }
            }
            unset($properties[OntologyRdf::RDF_TYPE]);
        }
        // create the instance
        $returnValue = $this->getImplementation()->createInstanceWithProperties($this, $properties);
        foreach ($additionalTypes as $type) {
            $returnValue->setType($type);
        }
        $eventManager = $this->getServiceManager()->get(EventManager::CONFIG_ID);
        $eventManager->trigger(new ResourceCreated($returnValue));
        return $returnValue;
    }

    /**
     * Delete instances of a Class from the database.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param array $resources An array of core_kernel_classes_Resource or URIs.
     * @param boolean $deleteReference If set to true, references about the resources will also be deleted from the
     *                                 database.
     * @return boolean
     */
    public function deleteInstances($resources, $deleteReference = false)
    {
        return (bool) $this->getImplementation()->deleteInstances($this, $resources, $deleteReference);
    }

    /**
     * @deprecated Use \oat\generis\model\resource\Repository\ClassRepository::delete() instead
     *
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     *
     * @param  bool deleteReference
     *
     * @return bool
     */
    public function delete($deleteReference = false)
    {
        try {
            $this->getClassRepository()->delete(
                new ResourceRepositoryContext(
                    [
                        ResourceRepositoryContext::PARAM_CLASS => $this,
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
     * States if the Class exists or not in persistent memory. The rule is
     * if the Class has parent classes, it exists. It works even for the
     * class because it inherits itself.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return boolean
     */
    public function exists()
    {
        // If the Class has one or more direct parent classes (this rdfs:isSubClassOf C),
        // we know that the class exists.
        return (bool) (count($this->getParentClasses(false)) > 0);
    }

    private function getClassRepository(): ResourceRepositoryInterface
    {
        return $this->getServiceManager()->getContainer()->get(ClassRepository::class);
    }

    public function updateUri(string $newUri)
    {
        return $this->getImplementation()->updateUri($this, $newUri);
    }
}
