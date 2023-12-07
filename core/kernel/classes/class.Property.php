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
 *
 */

declare(strict_types=1);

use oat\generis\model\OntologyRdf;
use oat\generis\model\WidgetRdf;
use oat\generis\model\GenerisRdf;
use oat\generis\model\OntologyRdfs;
use oat\generis\model\resource\DependsOnPropertyCollection;
use oat\generis\model\kernel\persistence\Cacheable;

/**
 * uriProperty must be a valid property otherwis return false, add this as a
 * of uriProperty
 *
 * @access public
 * @author patrick.plichart@tudor.lu
 * @package generis

 */
class core_kernel_classes_Property extends core_kernel_classes_Resource
{
    public const RELATIONSHIP_PROPERTIES = [
        OntologyRdf::RDF_TYPE,
        OntologyRdfs::RDFS_CLASS,
        OntologyRdfs::RDFS_RANGE,
        OntologyRdfs::RDFS_DOMAIN,
        OntologyRdfs::RDFS_SUBCLASSOF,
        OntologyRdfs::RDFS_SUBPROPERTYOF,
    ];

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
     * @var core_kernel_classes_Class
     */
    public $range = null;

    /**
     * The widget the can be used to represents the property.
     *
     * Dev note: this property is set to false because null is also a possible
     * valid value for this property. This will prevent the widget to be property
     * to be retrieved even if in cache, when no widget is set for the property.
     *
     * @access public
     * @var core_kernel_classes_Property
     */
    public $widget = false;

    /** @var DependsOnPropertyCollection */
    private $dependsOnPropertyCollection;

    // --- OPERATIONS ---
    /**
     * @return core_kernel_persistence_PropertyInterface
     */
    private function getImplementation()
    {
        return $this->getModel()->getRdfsInterface()->getPropertyImplementation();
    }

    /**
     * constructor
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  string uri
     * @param  string debug
     * @return void
     */
    public function __construct($uri, $debug = '')
    {
        parent::__construct($uri, $debug);
    }

    /**
     *
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     */
    public function feed()
    {
        $this->getWidget();
        $this->getRange();
        $this->getDomain();
        $this->isLgDependent();
    }

    public function feedFromData($widget, $range, $domain)
    {
        $this->widget = is_string($widget) ? $this->getModel()->getResource($widget) : $widget;
        $this->range = is_string($range) ? $this->getModel()->getClass($range) : $range;

        if (is_string($domain)) {
            $this->domain = new core_kernel_classes_ContainerCollection(new common_Object());
            $domainValues = [$domain];
            foreach ($domainValues as $domainValue) {
                $this->domain->add($this->getClass($domainValue));
            }
        } else {
            $this->domain = $domain;
        }

        $this->isLgDependent();
    }

    /**
     * return classes that are described by this property
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return core_kernel_classes_ContainerCollection
     */
    public function getDomain()
    {
        if (is_null($this->domain)) {
            $this->domain = new core_kernel_classes_ContainerCollection(new common_Object(__METHOD__));
            $domainValues = $this->getPropertyValues($this->getProperty(OntologyRdfs::RDFS_DOMAIN));
            foreach ($domainValues as $domainValue) {
                $this->domain->add($this->getClass($domainValue));
            }
        }

        return $this->domain;
    }

    public function getRelatedClass(): ?core_kernel_classes_Class
    {
        try {
            $class = $this->getDomain()->get(0);

            return $class instanceof core_kernel_classes_Class ? $class : null;
        } catch (common_Exception $exception) {
            return null;
        }
    }

    public function isStatistical(): bool
    {
        $value = $this->getOnePropertyValue($this->getProperty(GenerisRdf::PROPERTY_IS_STATISTICAL));

        return $value instanceof core_kernel_classes_Resource && $value->getUri() === GenerisRdf::GENERIS_TRUE;
    }

    /**
     * Short description of method setDomain
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Class class
     * @return boolean
     */
    public function setDomain(core_kernel_classes_Class $class)
    {
        $returnValue = (bool) false;

        if (!is_null($class)) {
            foreach ($this->getDomain()->getIterator() as $domainClass) {
                if ($class->equals($domainClass)) {
                    $returnValue = true;
                    break;
                }
            }
            if (!$returnValue) {
                $this->setPropertyValue($this->getProperty(OntologyRdfs::RDFS_DOMAIN), $class->getUri());
                if (!is_null($this->domain)) {
                    $this->domain->add($class);
                }
                $returnValue = true;
            }
        }
        return (bool) $returnValue;
    }

    /**
     * Short description of method getRange
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return core_kernel_classes_ContainerCollection
     */
    public function getRange()
    {
        $returnValue = null;

        if (is_null($this->range)) {
            $rangeProperty = $this->getProperty(OntologyRdfs::RDFS_RANGE);
            $rangeValues = $this->getPropertyValues($rangeProperty);

            if (!empty($rangeValues)) {
                $returnValue = $this->getClass($rangeValues[0]);
            }
            $this->range = $returnValue;
        }
        $returnValue = $this->range;
        return $returnValue;
    }

    /**
     * Short description of method setRange
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  Class class
     * @return boolean
     */
    public function setRange(core_kernel_classes_Class $class): bool
    {
        $returnValue = $this->getImplementation()->setRange($this, $class);
        if ($returnValue) {
            $this->range = $class;
        }
        return (bool)$returnValue;
    }

    public function getAlias(): ?string
    {
        $container = $this->getOnePropertyValue($this->getProperty(GenerisRdf::PROPERTY_ALIAS));

        if ($container instanceof core_kernel_classes_Literal) {
            return $container->__toString();
        }

        return null;
    }

    public function getDependsOnPropertyCollection(): DependsOnPropertyCollection
    {
        if (!isset($this->dependsOnPropertyCollection)) {
            $dependsOnProperty = $this->getProperty(GenerisRdf::PROPERTY_DEPENDS_ON_PROPERTY);
            $dependsOnPropertyValues = $this->getPropertyValues($dependsOnProperty);
            $this->dependsOnPropertyCollection = new DependsOnPropertyCollection();

            foreach ($dependsOnPropertyValues as $dependsOnPropertyValue) {
                if ($dependsOnPropertyValue !== GenerisRdf::PROPERTY_DEPENDS_ON_PROPERTY) {
                    $this->dependsOnPropertyCollection->append(
                        $this->getProperty($dependsOnPropertyValue)
                    );
                }
            }
        }

        $this->dependsOnPropertyCollection->rewind();

        return $this->dependsOnPropertyCollection;
    }

    /**
     * @TODO Improve setter
     */
    public function setDependsOnPropertyCollection(DependsOnPropertyCollection $dependsOnPropertyCollection): void
    {
        foreach ($dependsOnPropertyCollection as $dependsOnProperty) {
            $this->getImplementation()->setDependsOnProperty($this, $dependsOnProperty);
        }

        $this->dependsOnPropertyCollection = $dependsOnPropertyCollection;
    }

    /**
     * Get the Property object corresponding to the widget of this Property.
     *
     * @author Cédric Alfonsi <cedric.alfonsi@tudor.lu>
     * @author Antoine Delamarre <antoine.delamarre@vesperiagroup.com>
     * @author Jérôme Bogaerts <jerome@taotesting.com>
     * @return core_kernel_classes_Property The Property object corresponding to the widget of this Property.
     */
    public function getWidget()
    {
        if ($this->widget === false) {
            $this->widget = $this->getOnePropertyValue($this->getProperty(WidgetRdf::PROPERTY_WIDGET));
        }

        return $this->widget;
    }

    /**
     * Is the property translatable?
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return boolean
     */
    public function isLgDependent(): bool
    {
        if (
            $this->supportCache()
            && $this->getModel()->getCache()->has($this->generateIsLgDependentKey($this->getUri()))
        ) {
            return (bool)$this->getModel()->getCache()->get($this->generateIsLgDependentKey($this->getUri()));
        }

        $isLgDependent = $this->getImplementation()->isLgDependent($this);

        if ($this->supportCache()) {
            $this->getModel()->getCache()->set($this->generateIsLgDependentKey($this->getUri()), $isLgDependent);
        }

        return $isLgDependent;
    }

    /**
     * Set mannually if a property can be translated
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return mixed
     */
    public function setLgDependent($isLgDependent): void
    {
        $this->getImplementation()->setLgDependent($this, $isLgDependent);

        if ($this->supportCache()) {
            $this->getModel()->getCache()->set($this->generateIsLgDependentKey($this->getUri()), $isLgDependent);
        }
    }

    /**
     * Check if a property can have multiple values.
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return boolean
     */
    public function isMultiple(): bool
    {
        if (
            $this->supportCache()
            && $this->getModel()->getCache()->has($this->generateIsMultipleKey($this->getUri()))
        ) {
            return (bool)$this->getModel()->getCache()->get($this->generateIsMultipleKey($this->getUri()));
        }

        $multipleProperty = $this->getProperty(GenerisRdf::PROPERTY_MULTIPLE);
        $multiple = $this->getOnePropertyValue($multipleProperty);

        if (is_null($multiple)) {
            $returnValue = false;
        } else {
            $returnValue = ($multiple->getUri() == GenerisRdf::GENERIS_TRUE);
        }

        if ($this->supportCache()) {
            $this->getModel()->getCache()->set($this->generateIsMultipleKey($this->getUri()), $returnValue);
        }

        return $returnValue;
    }

    /**
     * Define mannualy if a property is multiple or not.
     * Usefull on just created property.
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @return mixed
     */
    public function setMultiple($isMultiple): void
    {
        $this->getImplementation()->setMultiple($this, $isMultiple);

        if ($this->supportCache()) {
            $this->getModel()->getCache()->set($this->generateIsMultipleKey($this->getUri()), $isMultiple);
        }
    }

    /**
     * Checks if property is a relation to other class
     *
     * @return bool
     */
    public function isRelationship(core_kernel_classes_Class $range = null): bool
    {
        if (in_array($this->getUri(), self::RELATIONSHIP_PROPERTIES)) {
            return true;
        }
        if ($this->getUri() === OntologyRdf::RDF_VALUE) {
            return false;
        }

        $model = $this->getModel();

        if ($this->supportCache() && $model->getCache()->has($this->generateIsRelationshipKey($this->getUri()))) {
            $isRelationship = (bool)$model->getCache()->get($this->generateIsRelationshipKey($this->getUri()));
        } else {
            if (empty($range)) {
                $range = $this->getRange();
            }

            $isRelationship = $range
                && !in_array(
                    $range->getUri(),
                    [
                        OntologyRdfs::RDFS_LITERAL,
                        GenerisRdf::CLASS_GENERIS_FILE
                    ],
                    true
                );

            if ($this->supportCache()) {
                $this->getModel()->getCache()->set($this->generateIsRelationshipKey($this->getUri()), $isRelationship);
            }
        }

        return $isRelationship;
    }

    /**
     * Short description of method delete
     *
     * @access public
     * @author Cédric Alfonsi, <cedric.alfonsi@tudor.lu>
     * @param  boolean deleteReference
     * @return boolean
     */
    public function delete($deleteReference = false): bool
    {
        $returnValue = $this->getImplementation()->delete($this, $deleteReference);

        $this->clearCachedValues();

        return (bool) $returnValue;
    }

    /**
     * Clear property cached data
     */
    public function clearCachedValues(): void
    {
        if (!$this->supportCache()) {
            return;
        }

        /** @var \oat\oatbox\cache\SimpleCache $cache */
        $cache = $this->getModel()->getCache();
        $isRelationshipKey = $this->generateIsRelationshipKey($this->getUri());
        $isMultipleKey = $this->generateIsMultipleKey($this->getUri());
        $isLgDependentKey = $this->generateIsLgDependentKey($this->getUri());

        if ($cache->has($isRelationshipKey)) {
            $cache->delete($isRelationshipKey);
        }

        if ($cache->has($isMultipleKey)) {
            $cache->delete($isMultipleKey);
        }

        if ($cache->has($isLgDependentKey)) {
            $cache->delete($isLgDependentKey);
        }
    }

    /**
     * Warmup property cached data
     */
    public function warmupCachedValues(): void
    {
        if (!$this->supportCache()) {
            return;
        }

        $this->isRelationship();
        $this->isMultiple();
        $this->isLgDependent();
    }

    protected function generateIsRelationshipKey(string $uri): string
    {
        return sprintf('PropIsRelationship_%s', $uri);
    }

    protected function generateIsLgDependentKey(string $uri): string
    {
        return sprintf('PropIsLgDependent_%s', $uri);
    }

    protected function generateIsMultipleKey(string $uri): string
    {
        return sprintf('PropIsMultiple_%s', $uri);
    }

    /**
     * @return bool
     */
    protected function supportCache(): bool
    {
        return $this->getModel() instanceof Cacheable;
    }
}
