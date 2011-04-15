<?php
/**
 * generis_Node is a data node used for rendering and represeting generis resources, it is used in TreeNode as userObject
 * @author patrick.plichart@tudor.lu
 *
 */
class generis_Node{
	private $label;
	private $link;
	private $type; 
	private $addCheckBox="0";
	private $target="pane";
	
	public function __construct($label,$link,$type) {
		$this->label=strip_tags($label);
		$this->link = $link;
		$this->type = $type;
	}

	public function isSelectable(){
		return $this->addCheckBox;
	}
	
	public function getLink() {
		return $this->link;
	}
	public function getType() {
		return $this->type;
	}
	public function getLabel() {
		return $this->label;
	}
	public function getTarget() {
		return $this->target;
	}

}
