<?php
/**
 * Description
 * 
 * @author lionel.lecaque@tudor.lu
 * @license //TODO
 * 
 *
 */
class TreeNode{
	private $parent;
	public $children;
	private $userObject; 
	private $tree;


	/**
	 * Constructor
	 *
	 * @param Object $userObject
	 */
	public function __construct($userObject = null){
		$this->userObject = $userObject;
		$this->parent = null;
		$this->children = new TreeNodeCollection($this);
	}

	/**
	 * Returns the parent node if any
	 *
	 * @return TreeNode
	 */
	public function getParent(){
		return $this->parent;
	}

	/**
	 * return true/false as to whether this node is a leaf
	 *
	 * @return bool
	 */
	public function isLeaf(){
		return $this->children->count() === 0;
	}

	/**
	 * Removes this node from its' parent. If this
	 * node has no parent (ie its not been added to
	 * a Tree or Tree_Node object) then this method
	 * will do nothing.
	 *
	 */
	function removeFromParent(){
		if($this->parent){
			$this->parent->children->remove($this);
		}
	}

	/**
	 * Returns the tree object which this node is attached
	 *
	 * @return Tree
	 */
	public function getTree(){
		return $this->tree;
	}

	/**
	 * Sets the parent node of the node.
	 *
	 * @param object $tree
	 */
	public function setTree($tree){
		$this->tree = $tree;
	}

	/**
	 * Sets object attached to the the node.
	 *
	 * @param Object $userObject
	 */
	public function setUserObject(Object $userObject){
		$this->userObject = $userObject;
	}

	/**
	 * Returns object attached to the the node.
	 *
	 * @return Object
	 */
	public function getUserObject(){
		return $this->userObject;
	}

	/**
	 * Sets the parent node of the node.
	 *
	 * @param TreeNode $node
	 */
	public function setParent($node){
		$this->parent = $node;
	}

	/**
	 * Return number of child of the TreeNode
	 *
	 * @return integer 
	 */
	public function getChildCount(){
		return $this->children->count();
	}


	/**
	 * Returns the depth in the tree of this node
	 *
	 * @return integer  The depth of the node
	 */
	public function getDepth(){
		$currentLevel = $this;

		while($currentLevel->parent instanceof TreeNode){
			++$depthInt;
			$currentLevel = $currentLevel->parent;
		}
		return $depthInt;
	}

	/**
	 * Returns true/false as to whether this node is a child
	 * of the given node.
	 *
	 * @param TreeNode $treeNode The suspected parent  
	 * @return bool  Whether this node is a child of the suspected parent
	 */
	public function isNodeChild(TreeNode $treeNode){
		return $this->parent === $treeNode;
	}

	/**
	 * Returns the next child node or null if any
	 *
	 * @return TreeNode  the next child TreeNode
	 */
	public function getNextSibling(){
		if($this->parent){
			$indexInt = $this->parent->children->indexOf($this);	
			if($indexInt < ($this->parent->children->count() - 1)){
				return $this->parent->children->get($indexInt + 1 );
			}
		}
		return null;
	}

	/**
	 * Returns the previous child node or null if any
	 *
	 * @return TreeNode the previous child TreeNode
	 */
	public function getPreviousSibling(){
		if($this->parent){
			$indexInt = $this->parent->children->indexOf($this);	
			if($indexInt > 0){
				return $this->parent->children->get($indexInt - 1);
			}
		}
		return null;
	}

	/**
	 * Returns all childrens of a node
	 *
	 * @return TreeNodeCollection 
	 */
	public function children(){
		return $this->children;
	}

}

?>
