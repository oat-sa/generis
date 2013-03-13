<?php
/*  
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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);\n *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
?>
<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - core\kernel\subscriptions\class.Service.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 18.12.2010, 18:49:24 with ArgoUML PHP module
 * (last revised $Date: 2010-01-12 20:14:42 +0100 (Tue, 12 Jan 2010) $)
 *
 * @author firstname and lastname of author, <author@example.org>
 * @package core
 * @subpackage kernel_subscriptions
 */

if (0 > version_compare(PHP_VERSION, '5')) {
	die('This file was generated for PHP 5');
}

/* user defined includes */
// section 127-0-1-1-51aaeb25:12ce3ab9b71:-8000:00000000000013AC-includes begin
// section 127-0-1-1-51aaeb25:12ce3ab9b71:-8000:00000000000013AC-includes end

/* user defined constants */
// section 127-0-1-1-51aaeb25:12ce3ab9b71:-8000:00000000000013AC-constants begin
// section 127-0-1-1-51aaeb25:12ce3ab9b71:-8000:00000000000013AC-constants end

/**
 * Short description of class core_kernel_subscriptions_Service
 *
 * @access public
 * @author firstname and lastname of author, <author@example.org>
 * @package core
 * @subpackage kernel_subscriptions
 */
class core_kernel_subscriptions_Service
{
	// --- ASSOCIATIONS ---


	// --- ATTRIBUTES ---

	/**
	 * Short description of attribute instance
	 *
	 * @access private
	 * @var Service
	 */
	private static $instance = null;

	/**
	 * Short description of attribute subscriptionArray
	 *
	 * @access public
	 * @var array
	 */
	public $subscriptionArray = array();

	/**
	 * Short description of attribute subjectArray
	 *
	 * @access public
	 * @var array
	 */
	public $subjectArray = array();

	/**
	 * Short description of attribute objectArray
	 *
	 * @access public
	 * @var array
	 */
	public $objectArray = array();

	/**
	 * Short description of attribute predicateArray
	 *
	 * @access public
	 * @var array
	 */
	public $predicateArray = array();

	// --- OPERATIONS ---

	/**
	 * Short description of method getInstancesFromSubscription
	 *
	 * @access public
	 * @author firstname and lastname of author, <author@example.org>
	 * @param  Resource subscription
	 * @param  Class topClass
	 * @return array
	 */
	public function getInstancesFromSubscription( core_kernel_classes_Resource $subscription,  core_kernel_classes_Class $topClass)
	{
		$returnValue = array();

		// section 127-0-1-1--5f676de6:12cea59c091:-8000:000000000000141B begin
		$topClassArray = isset($this->subscriptionArray[$subscription->uriResource]['object']) ? $this->subscriptionArray[$subscription->uriResource]['object']  : null;

		if($topClassArray != null && in_array($topClass->uriResource,$topClassArray)){

			$mask = $subscription->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_SUBCRIPTION_MASK));
			$subscriptionUrl = $subscription->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_SUBCRIPTION_URL));
				
			$url = $subscriptionUrl . 'RestClass/instances?clazz=' .  urlencode($topClass->uriResource);
			$fileContent = file_get_contents($url);
			$xml = new DOMDocument('1.0', 'UTF-8');
			$xml->loadXML($fileContent);
			$instances = $xml->getElementsByTagName('uri');
			foreach ($instances as $inst){
				$instanceUri = (trim($inst->nodeValue));
				$resource = new core_kernel_classes_Resource($instanceUri);
				$returnValue[$instanceUri] = $resource;
				//TODO dirty
				$containerColleciton = new core_kernel_classes_ContainerCollection($resource);
				$containerColleciton->add($resource);
				$this->addSubject($containerColleciton,$subscription);
				if($mask instanceof core_kernel_classes_Resource){
					$mask->setPropertyValue(new core_kernel_classes_Property(PROPERTY_MASK_SUBJECT),$resource->uriResource);

				}

			}

				
		}


		// section 127-0-1-1--5f676de6:12cea59c091:-8000:000000000000141B end

		return (array) $returnValue;
	}

	/**
	 * Short description of method __construct
	 *
	 * @access private
	 * @author firstname and lastname of author, <author@example.org>
	 * @return mixed
	 */
	private function __construct()
	{
		// section 127-0-1-1--5f676de6:12cea59c091:-8000:0000000000001425 begin
		$subscriptionClass = new core_kernel_classes_Class(CLASS_SUBCRIPTION);
		$subscriptionInstances = $subscriptionClass->getInstances();

		$maskProp = new core_kernel_classes_Property(PROPERTY_SUBCRIPTION_MASK);
		$subjectProp = new core_kernel_classes_Property(PROPERTY_MASK_SUBJECT);
		$predicateProp = new core_kernel_classes_Property(PROPERTY_MASK_PREDICATE);
		$objectProp = new core_kernel_classes_Property(PROPERTY_MASK_OBJECT);


		foreach ($subscriptionInstances as $subscriptionInst){
			$masks = $subscriptionInst->getPropertyValuesCollection($maskProp);

			foreach ($masks->getIterator() as $mask){

				$subjects = $mask->getPropertyValuesCollection($subjectProp);
				$this->addSubject($subjects,$subscriptionInst);
				$predicates = $mask->getPropertyValuesCollection($predicateProp);
				$this->addPredicate($predicates,$subscriptionInst);
				$objects =  $mask->getPropertyValuesCollection($objectProp);
				$this->addObject($objects,$subscriptionInst);
			}
		}

		// section 127-0-1-1--5f676de6:12cea59c091:-8000:0000000000001425 end
	}

	/**
	 * Short description of method singleton
	 *
	 * @access public
	 * @author firstname and lastname of author, <author@example.org>
	 * @return core_kernel_subscriptions_Service
	 */
	public static function singleton()
	{
		$returnValue = null;

		// section 127-0-1-1--5f676de6:12cea59c091:-8000:0000000000001427 begin
		if (!isset(self::$instance)) {
			$c = __CLASS__;
			self::$instance = new $c();
		}
		$returnValue = self::$instance;
		// section 127-0-1-1--5f676de6:12cea59c091:-8000:0000000000001427 end

		return $returnValue;
	}

	/**
	 * Short description of method addSubject
	 *
	 * @access private
	 * @author firstname and lastname of author, <author@example.org>
	 * @param  ContainerCollection subjects
	 * @param  Resource subscription
	 * @return mixed
	 */
	private function addSubject( core_kernel_classes_ContainerCollection $subjects,  core_kernel_classes_Resource $subscription)
	{
		// section -64--88-0-10-6435a2f4:12cf4ccb9f8:-8000:00000000000013CF begin
		foreach ($subjects->getIterator() as $subject){
			if(!isset($this->subjectArray[$subject->uriResource])){
				$this->subjectArray[$subject->uriResource] = array();
			}
			$this->subjectArray[$subject->uriResource][] = $subscription->uriResource;
			if(!isset($this->subscriptionArray[$subscription->uriResource]['subject'])){
				$this->subscriptionArray[$subscription->uriResource]['subject'] =  array();
			}
			$this->subscriptionArray[$subscription->uriResource]['subject'][] = $subject->uriResource;
		}
		// section -64--88-0-10-6435a2f4:12cf4ccb9f8:-8000:00000000000013CF end
	}

	/**
	 * Short description of method addPredicate
	 *
	 * @access private
	 * @author firstname and lastname of author, <author@example.org>
	 * @param  ContainerCollection predicates
	 * @param  Resource subscription
	 * @return mixed
	 */
	private function addPredicate( core_kernel_classes_ContainerCollection $predicates,  core_kernel_classes_Resource $subscription)
	{
		// section -64--88-0-10-6435a2f4:12cf4ccb9f8:-8000:00000000000013D1 begin
		foreach ($predicates->getIterator() as $predicate){
			if(!isset($this->predicateArray[$predicate->uriResource])){
				$this->predicateArray[$predicate->uriResource] = array();
			}
			$this->predicateArray[$predicate->uriResource][] = $subscription->uriResource;
			if(!isset($this->subscriptionArray[$subscription->uriResource]['predicate'])){
				$this->subscriptionArray[$subscription->uriResource]['predicate'] =  array();
			}
			$this->subscriptionArray[$subscription->uriResource]['predicate'][] = $predicate->uriResource;
		}

		// section -64--88-0-10-6435a2f4:12cf4ccb9f8:-8000:00000000000013D1 end
	}

	/**
	 * Short description of method addObject
	 *
	 * @access private
	 * @author firstname and lastname of author, <author@example.org>
	 * @param  ContainerCollection objects
	 * @param  Resource subscriptionInst
	 * @return mixed
	 */
	private function addObject( core_kernel_classes_ContainerCollection $objects,  core_kernel_classes_Resource $subscriptionInst)
	{
		// section -64--88-0-10-6435a2f4:12cf4ccb9f8:-8000:00000000000013D3 begin
		foreach ($objects->getIterator() as $object){
			if($object instanceof core_kernel_classes_Resource){
				if(!isset($this->objectArray[$object->uriResource])){
					$this->objectArray[$object->uriResource] = array();
				}
				$this->objectArray[$object->uriResource][] = $subscriptionInst->uriResource;
				if(!isset($this->subscriptionArray[$subscriptionInst->uriResource]['object'])){
					$this->subscriptionArray[$subscriptionInst->uriResource]['object'] =  array();
				}
				$this->subscriptionArray[$subscriptionInst->uriResource]['object'][] = $object->uriResource;
			}
			if($object instanceof core_kernel_classes_Literal){
				if(!isset($this->objectArray[$object->literal])){
					$this->objectArray[$object->literal] = array();
				}
				$this->objectArray[$object->literal][] = $subscriptionInst->uriResource;
				if(!isset($this->subscriptionArray[$subscriptionInst->uriResource]['object'])){
					$this->subscriptionArray[$subscriptionInst->uriResource]['object'] =  array();
				}
				$this->subscriptionArray[$subscriptionInst->uriResource]['object'][] = $object->literal;
			}
		}
		// section -64--88-0-10-6435a2f4:12cf4ccb9f8:-8000:00000000000013D3 end
	}

	/**
	 * Short description of method getSubscriptions
	 *
	 * @access public
	 * @author firstname and lastname of author, <author@example.org>
	 * @param  subject
	 * @param  predicate
	 * @param  object
	 * @return array
	 */
	public function getSubscriptions($subject, $predicate, $object)
	{
		$returnValue = array();

		// section -64--88-0-10-6435a2f4:12cf4ccb9f8:-8000:00000000000013DB begin
		if($predicate != null && $object!=null) {
			if(isset($this->predicateArray[$predicate->uriResource])){
				if($object instanceof core_kernel_classes_Resource &&  isset($this->objectArray[$object->uriResource])
				|| ($object instanceof core_kernel_classes_Literal && isset($this->objectArray[$object->literal]))) {
					$returnValue = array_merge($returnValue,$this->predicateArray[$predicate->uriResource]);
				}
			}
		}
		if($predicate != null && $subject!=null) {
			if(isset($this->predicateArray[$predicate->uriResource]) && isset($this->subjectArray[$subject->uriResource]) ){
				$returnValue = array_merge($returnValue,$this->predicateArray[$predicate->uriResource]);
			}
		}
		// section -64--88-0-10-6435a2f4:12cf4ccb9f8:-8000:00000000000013DB end

		return (array) $returnValue;
	}

	/**
	 * Short description of method getPropertyValuesFromSubscription
	 *
	 * @access public
	 * @author firstname and lastname of author, <author@example.org>
	 * @param  Resource subscription
	 * @param  Resource instance
	 * @param  Property property
	 * @return array
	 */
	public function getPropertyValuesFromSubscription( core_kernel_classes_Resource $subscription,  core_kernel_classes_Resource $instance,  core_kernel_classes_Property $property)
	{
		$returnValue = array();

		// section -64--88-0-10--63e3923:12cfa7bcdaf:-8000:00000000000013DB begin
		$subjectArray = isset($this->subscriptionArray[$subscription->uriResource]['subject']) ? $this->subscriptionArray[$subscription->uriResource]['subject']  : null;
		$predicateArray = isset($this->subscriptionArray[$subscription->uriResource]['predicate']) ? $this->subscriptionArray[$subscription->uriResource]['predicate']  : null;
		if($subjectArray != null && in_array($instance->uriResource,$subjectArray)
		&& $predicateArray!= null && in_array($property->uriResource,$predicateArray)){
				
			$subscriptionUrl = $subscription->getOnePropertyValue(new core_kernel_classes_Property(PROPERTY_SUBCRIPTION_URL));
			$url = $subscriptionUrl .'RestResource/propertyValues?uri='
			. urlencode($instance->uriResource)
			. '&property='
			.  urlencode($property->uriResource);
				
			$fileContent = file_get_contents($url);
			$xml = new DOMDocument('1.0', 'UTF-8');
			$xml->loadXML($fileContent);
			$propertyValues = $xml->getElementsByTagName('propertyValue');
			foreach ($propertyValues as $propertyValue){
				$returnValue[] = trim($propertyValue->nodeValue);
			}
		}
		// section -64--88-0-10--63e3923:12cfa7bcdaf:-8000:00000000000013DB end

		return (array) $returnValue;
	}

} /* end of class core_kernel_subscriptions_Service */

?>