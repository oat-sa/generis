<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API - common\class.Collection.php
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 24.03.2010, 14:38:36 with ArgoUML PHP module 
 * (last revised $Date: 2008-04-19 08:22:08 +0200 (Sat, 19 Apr 2008) $)
 *
 * @author lionel.lecaque@tudor.lu
 * @package common
 * @see @license  GNU General Public (GPL) Version 2 http://www.opensource.org/licenses/gpl-2.0.php
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * include common_Object
 *
 * @author lionel.lecaque@tudor.lu
 * @see @license  GNU General Public (GPL) Version 2 http://www.opensource.org/licenses/gpl-2.0.php
 */
require_once('common/class.Object.php');

/* user defined includes */
// section 10-13-1--99-2f1559da:11a15934f36:-8000:0000000000000BA1-includes begin
// section 10-13-1--99-2f1559da:11a15934f36:-8000:0000000000000BA1-includes end

/* user defined constants */
// section 10-13-1--99-2f1559da:11a15934f36:-8000:0000000000000BA1-constants begin
// section 10-13-1--99-2f1559da:11a15934f36:-8000:0000000000000BA1-constants end

/**
 * Short description of class common_Collection
 *
 * @access public
 * @author lionel.lecaque@tudor.lu
 * @package common
 * @see @license  GNU General Public (GPL) Version 2 http://www.opensource.org/licenses/gpl-2.0.php
 */
class common_Collection
    extends common_Object
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute sequence
     *
     * @access public
     * @var array
     */
    public $sequence = array();

    /**
     * Short description of attribute container
     *
     * @access public
     * @var Object
     */
    public $container = null;

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  Object container
     * @param  string debug
     * @return void
     */
    public function __construct( common_Object $container, $debug = '')
    {
        // section 10-13-1--99--4fdec042:11a2a3b44dc:-8000:00000000000010A2 begin
        $this->sequence = array();
		$this->container = $container;	
        // section 10-13-1--99--4fdec042:11a2a3b44dc:-8000:00000000000010A2 end
    }

    /**
     * return the number of node of the collection (only this level)
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return int
     */
    public function count()
    {
        $returnValue = (int) 0;

        // section 10-13-1--99-2f1559da:11a15934f36:-8000:0000000000000BB7 begin
        $returnValue = count($this->sequence);
        // section 10-13-1--99-2f1559da:11a15934f36:-8000:0000000000000BB7 end

        return (int) $returnValue;
    }

    /**
     * return the index of the node array at which the given node resides
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  Object object
     * @return int
     */
    public function indexOf( common_Object $object)
    {
        $returnValue = (int) 0;

        // section 10-13-1--99-2f1559da:11a15934f36:-8000:0000000000000BB9 begin
    	$returnValue = -1;
        foreach($this->sequence as $index => $_object){
			if($object === $_object){
				return $index;
			}
		}
        // section 10-13-1--99-2f1559da:11a15934f36:-8000:0000000000000BB9 end

        return (int) $returnValue;
    }

    /**
     * Retrun the node at the given index
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  int index
     * @return common_Object
     */
    public function get($index)
    {
        $returnValue = null;

        // section 10-13-1--99-2f1559da:11a15934f36:-8000:0000000000000BBC begin
			
		$returnValue = isset($this->sequence[$index]) ? $this->sequence[$index] : null;
		if($returnValue == null) {
			throw new common_Exception('index is out of range');
		}
        // section 10-13-1--99-2f1559da:11a15934f36:-8000:0000000000000BBC end

        return $returnValue;
    }

    /**
     * Short description of method isEmpty
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return boolean
     */
    public function isEmpty()
    {
        $returnValue = (bool) false;

        // section 10-13-1--99-2f1559da:11a15934f36:-8000:0000000000000BC8 begin
        $returnValue = (count($this->sequence) == 0);
        // section 10-13-1--99-2f1559da:11a15934f36:-8000:0000000000000BC8 end

        return (bool) $returnValue;
    }

    /**
     * Implementation of ArrayAccess:offsetSet()
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  Object key
     * @param  Object value
     * @return void
     */
    public function offsetSet( common_Object $key,  common_Object $value)
    {
        // section 10-13-1--99-2f1559da:11a15934f36:-8000:0000000000000BCA begin
        $this->sequence[$key] = $value;
        // section 10-13-1--99-2f1559da:11a15934f36:-8000:0000000000000BCA end
    }

    /**
     * Implementation of ArrayAccess:offsetGet()
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  Object key
     * @return common_Object
     */
    public function offsetGet( common_Object $key)
    {
        $returnValue = null;

        // section 10-13-1--99-2f1559da:11a15934f36:-8000:0000000000000BCE begin
        $returnValue = $this->sequence[$key];
        // section 10-13-1--99-2f1559da:11a15934f36:-8000:0000000000000BCE end

        return $returnValue;
    }

    /**
     * Implementation of ArrayAccess:offsetUnset()
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  Object key
     * @return void
     */
    public function offsetUnset( common_Object $key)
    {
        // section 10-13-1--99-2f1559da:11a15934f36:-8000:0000000000000BD1 begin
        unset($this->sequence[$key]);
        // section 10-13-1--99-2f1559da:11a15934f36:-8000:0000000000000BD1 end
    }

    /**
     * Implementation of ArrayAccess:offsetExists()
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  Object key
     * @return boolean
     */
    public function offsetExists( common_Object $key)
    {
        $returnValue = (bool) false;

        // section 10-13-1--99-2f1559da:11a15934f36:-8000:0000000000000BD4 begin
        $returnValue = isset($this->sequence[$key]);
        // section 10-13-1--99-2f1559da:11a15934f36:-8000:0000000000000BD4 end

        return (bool) $returnValue;
    }

    /**
     * Implementation of IteratorAggregate::getIterator()
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return mixed
     */
    public function getIterator()
    {
        // section 10-13-1--99-2f1559da:11a15934f36:-8000:0000000000000BD6 begin
         return new ArrayIterator($this->sequence);
        // section 10-13-1--99-2f1559da:11a15934f36:-8000:0000000000000BD6 end
    }

    /**
     * Add a node to the collection
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  Object node
     * @return mixed
     */
    public function add( common_Object $node)
    {
        // section 10-13-1--99-2f1559da:11a15934f36:-8000:0000000000000BF6 begin
		$this->sequence[] = $node;
		$returnValue = $node;
        // section 10-13-1--99-2f1559da:11a15934f36:-8000:0000000000000BF6 end
    }

    /**
     * return a string with HTML representation of the collection
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return string
     */
    public function toHtml()
    {
        $returnValue = (string) '';

        // section 10-13-1--99-2f1559da:11a15934f36:-8000:0000000000000BFB begin
    	if (!($this->isEmpty()))
		{
			$returnValue.='<span style="display:block;margin:5px;padding:5px;align:center;border: #9c9c9c 1px dashed;border-color:black;font-family:Verdana;background-color:#F5F5F5;font-size:8;font-weight:bold;"><span style=color:#003399;>'.$this->debug.'</span><br>';
			foreach ($this->sequence as $container)
				{
					$returnValue.=$container->toHtml();
				}
			$returnValue.='</span>';
		}
        // section 10-13-1--99-2f1559da:11a15934f36:-8000:0000000000000BFB end

        return (string) $returnValue;
    }

    /**
     * Remove the node from the collection
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  Object object
     * @return boolean
     */
    public function remove( common_Object $object)
    {
        $returnValue = (bool) false;

        // section 10-13-1--99-2f1559da:11a15934f36:-8000:00000000000013CC begin
        foreach($this->sequence as $index => $_node){
			if($_node === $object){
				unset($this->sequence[$index]);
				$this->sequence = array_values($this->sequence);
				return true;
			}		
		}
		return false;
        // section 10-13-1--99-2f1559da:11a15934f36:-8000:00000000000013CC end

        return (bool) $returnValue;
    }

    /**
     * Short description of method union
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  Collection collection
     * @return common_Collection
     */
    public function union( common_Collection $collection)
    {
        $returnValue = null;

        // section 10-13-1--99--1201ed7f:11c6b266eba:-8000:0000000000000EA4 begin
        $returnValue = new common_Collection($this);     
        $returnValue->sequence = array_merge($this->sequence, $collection->sequence );      
        // section 10-13-1--99--1201ed7f:11c6b266eba:-8000:0000000000000EA4 end

        return $returnValue;
    }

    /**
     * Short description of method intersect
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  Collection collection
     * @return common_Collection
     */
    public function intersect( common_Collection $collection)
    {
        $returnValue = null;

        // section 10-13-1--99--1201ed7f:11c6b266eba:-8000:0000000000000EA7 begin
         $returnValue = new common_Collection(new common_Object(__METHOD__));
         $returnValue->sequence = array_uintersect($this->sequence, $collection->sequence, 'core_kernel_classes_ContainerComparator::compare');
        // section 10-13-1--99--1201ed7f:11c6b266eba:-8000:0000000000000EA7 end

        return $returnValue;
    }

    /**
     * Short description of method toArray
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @return array
     */
    public function toArray()
    {
        $returnValue = array();

        // section -87--2--3--76--8049d3c:12424ebfe97:-8000:00000000000017D1 begin
        foreach ($this->getIterator() as $it){
        	$returnValue[] = $it;
        }
        // section -87--2--3--76--8049d3c:12424ebfe97:-8000:00000000000017D1 end

        return (array) $returnValue;
    }

} /* end of class common_Collection */

?>