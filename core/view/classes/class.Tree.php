<?php
/**
 * Description
 * 
 * @author lionel.lecaque@tudor.lu
 * @license //TODO
 * 
 *
 */




class Tree{

	public $children;

	function __construct(){
		$this->children = new TreeNodeCollection($this);
	}

	public function getTree(){
		return $this;
	}


	public function __toString(){
		$nodeList = $this->children->getAllNodes();
		$str      = '';
		foreach ($nodeList as $node) {
			$generisNode = $node->getUserObject();

			$str .= str_repeat('&nbsp;&nbsp;&nbsp;', $node->getDepth()) . "<a target=".$generisNode->getTarget()." href=\"".$generisNode->getLink()."\">".trim($generisNode->getLabel()) ."</a>". "\n<br>";
		}

		return $str;
	}

	/**
	 * flatten tree and returns json string
	 *	wrong code, assumes that the tree is built accordingly, the json is not closed properly and so on
	 **/
	public function getJsonString(){
		$nodeList = $this->children->getAllNodes();
		$str      = '[\n';
		$initialDepth='-1';
		$currentDepth = $initialDepth;
		foreach ($nodeList as $node) {

			$generisNode = $node->getUserObject();


			if ($node->getDepth() > $currentDepth) 
			{ $str .= str_repeat('   ', $node->getDepth());$currentDepth=$node->getDepth(); $str.="[\r\n";};
			if ($node->getDepth() < $currentDepth) 
			{ $str .= str_repeat('   ', $node->getDepth());$currentDepth=$node->getDepth(); $str.="],\r\n";}
			if ($node->getDepth() == $currentDepth) $str .= str_repeat('   ', $node->getDepth());
			$str.="{label:".trim(strip_tags($generisNode->getLabel())).",link:".$generisNode->getLink().",icon:".$generisNode->getIcon().",checkbox:".$generisNode->isSelectable().",target:".$generisNode->getTarget()."},\r\n";

		}

		//close those arrays

		return $str;
	}
}

?>
