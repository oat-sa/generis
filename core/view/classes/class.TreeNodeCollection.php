<?php
/**
 * Description
 * 
 * @author lionel.lecaque@tudor.lu
 * @license //TODO
 * @package usergui
 *
 */
class TreeNodeCollection implements ArrayAccess,IteratorAggregate {

	private $collection;
	private $container;

	/**
	 * Constructor
	 */
	function __construct($container){
		$this->collection = array();
		$this->container = $container;	
	}

	/**
	 * Enter description here...
	 *
	 * @param TreeNode $child
	 * @return bool
	 */
	public function remove(TreeNode $child){
		foreach($this->collection as $index => $_node){
			if($_node === $node){
				//remove parent
				$node->setParent(null);
				$node->setTree(null);

				unset($this->collection[$index]);

				$this->collection = array_values($this->collection);
				return true;
			}		
		}
		return false;
	}

	/**
	 * Enter description here...
	 *
	 * @return integer
	 */
	public function count(){
		return count($this->collection);
	}

	/**
	 * Returns the index in the nodes array at which
	 * the given node resides
	 *
	 * @param TreeNode $treeNode
	 * @return integer
	 */
	public function indexOf(TreeNode $treeNode){
		foreach($this->collection as $index => $_node){
			if($treeNode === $_node){
				return $index;
			}
		}
	}

	/**
	 * Returns node at given index. 
	 *
	 * @param integer $indexInt
	 * @return TreeNode
	 */
	public function get($indexInt){
		return isset($this->collection[$indexInt]) ? $this->collection[$indexInt] : null;
	}

	/**
	 * Enter description here...
	 *
	 * @param TreeNode $node
	 * @return TreeNode
	 */
	public function add(TreeNode $node){
		$node->setParent($this->container);

		if ($this->container->getTree() instanceof Tree) {
			$node->setTree($this->container->getTree());
		}
		$this->collection[] = $node;
		return $node;
	}

	/**
	 * Implementation of ArrayAccess:offsetSet()
	 *
	 * @param mixed $key
	 * @param mixed $value
	 */
	public function offsetSet($key, $value){
		$this->collection[$key] = $value;
	}
	/**
	 * Implementation of ArrayAccess:offsetGet()
	 *
	 * @param mixed $key
	 * @return mixed
	 */
	public function offsetGet($key){
		return $this->collection[$key];
	}
	/**
	 * Implementation of ArrayAccess:offsetUnset()
	 *
	 * @param mixed $key
	 */
	public function offsetUnset($key){
		unset($this->collection[$key]);
	}
	/**
	 * Implementation of ArrayAccess:offsetExists()
	 *
	 * @param mixed $key Key to check for
	 * @return bool Whether it's set or not
	 */
	public function offsetExists($key){
		return isset($this->collection[$key]);
	}
	/**
	 * Implementation of IteratorAggregate::getIterator()
	 *
	 * @return Object
	 */
	public function getIterator(){
		return new ArrayIterator($this->collection);
	}

	/**
	 * Return a flat list of all node
	 *
	 * @return Array
	 */
	public function getAllNodes(){
		$return = array();
		foreach ($this->collection as $node) {
			$return[] = $node;

			// Recurse
			if ($node->getChildCount()>0) {
				$return = array_merge($return, $node->children->getAllNodes());
			}
		}

		return $return;
	}


}
?>
