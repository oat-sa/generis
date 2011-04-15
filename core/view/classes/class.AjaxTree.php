<?php
if (!defined("HtmlViewLink")) {
	define("HtmlViewLink", "generis_UiControllerHtml.php", true);
}
/**
 * This class used Ajax Yahoo Toolkit to build javascript Tree 
 *
 */
class core_view_classes_AjaxTree{

	private $selectedNode = array();
	private $checkBoxTree ;
	private $expended = true;

	private $yuiSrcInclusions ='
		<link rel="stylesheet" type="text/css" href="../../include/ajax/yui/build/fonts/fonts-min.css" />
		<link rel="stylesheet" type="text/css" href="../../include/ajax/yui/build/treeview/assets/skins/sam/treeview.css" />
		<script type="text/javascript" src="../../include/ajax/yui/build/yahoo/yahoo.js"></script>
		<script type="text/javascript" src="../../include/ajax/yui/build/event/event.js"></script>
		<script type="text/javascript" src="../../include/ajax/yui/build/treeview/treeview.js"></script>
		<script type="text/javascript" src="../../include/ajax/yui/build/connection/connection.js"></script>
		<script type="text/javascript" src="../../include/ajax/yui/build/json/json-beta.js"></script>
		<script type="text/javascript" src="../../include/ajax/yui/build/yahoo-dom-event/yahoo-dom-event.js"></script>
		<script type="text/javascript" src="./JS/generisTree.js"></script>
';
	private $yuiCssInclusions ='
	
		<style type="text/css">
		#treewrapper {background: #fff; position:relative;}
		#treediv {position:relative; width:350px; background: #fff; padding:2em;}
		.icon-model { display:block; height: 22px; padding-left: 30px; background: transparent url(./icons/Generis_Model.png)  no-repeat; }
		.icon-class { display:block; height: 22px; padding-left: 30px; background: transparent url(./icons/Class.png) no-repeat; }
		.icon-property { display:block; height: 22px; padding-left: 30px; background: transparent url(./icons/Property.png)  no-repeat; }
		.icon-instance { display:block; height: 22px; padding-left: 30px; background: transparent url(./icons/Instance.png)  no-repeat; }
		.icon-metaclass  { display:block; height: 22px; padding-left: 30px; background: transparent url(./icons/MetaClass.gif)  no-repeat; }
		.ygtvcheck0 { background: url(../../include/ajax/yui/examples/treeview/assets/img/check/check0.gif) 0 0 no-repeat; width:16px; cursor:pointer }
		.ygtvcheck1 { background: url(../../include/ajax/yui/examples/treeview/assets/img/check/check1.gif) 0 0 no-repeat; width:16px; cursor:pointer }
		.ygtvcheck2 { background: url(../../include/ajax/yui/examples/treeview/assets/img/check/check2.gif) 0 0 no-repeat; width:16px; cursor:pointer }
		</style>
	';
	private $yuiTreeDiv ='
		<div id="treeDiv1"></div>
		';
	
	
	private function yuiTreeControl(){
		return '
			
			<script type="text/javascript">
			
			YAHOO.example.treeExample = function() {
			
				var tree, currentIconMode;
				var nodes = [];
				var nodeIndex;
			
				function changeIconMode() {
					var newVal = parseInt(this.value);
					if (newVal != currentIconMode) {
						currentIconMode = newVal;
			}
			buildTree();
			}
			
			//handler for expanding all nodes
			YAHOO.util.Event.on("expand", "click", function(e) {
				YAHOO.log("Expanding all TreeView  nodes.", "info", "example");
				tree.expandAll();
				YAHOO.util.Event.preventDefault(e);
			});
			
			//handler for collapsing all nodes
			YAHOO.util.Event.on("collapse", "click", function(e) {
				YAHOO.log("Collapsing all TreeView  nodes.", "info", "example");
				tree.collapseAll();
				YAHOO.util.Event.preventDefault(e);
			});
			
			//handler for checking all nodes
			YAHOO.util.Event.on("check", "click", function(e) {
				YAHOO.log("Checking all TreeView  nodes.", "info", "example");
				checkAll();
				YAHOO.util.Event.preventDefault(e);
			});
			
			//handler for unchecking all nodes
			YAHOO.util.Event.on("uncheck", "click", function(e) {
				YAHOO.log("Unchecking all TreeView  nodes.", "info", "example");
				uncheckAll();
				YAHOO.util.Event.preventDefault(e);
			});
			
			
			YAHOO.util.Event.on("getchecked", "click", function(e) {
				YAHOO.log("Checked nodes: " + YAHOO.lang.dump(getCheckedNodes()), "info", "example");
			
				YAHOO.util.Event.preventDefault(e);
			});
			
			function loadNodeData(node, fnLoadComplete)  {
			
			
				var param = encodeURI(node.href);
			
				//prepare URL for XHR request:
				var sUrl = "./JS/generis_jsonProxy.php?query=" + param;
			
				//prepare our callback object
				var callback = {
			
					//if our XHR call is successful, we want to make use
					//of the returned data and create child nodes.
					success: function(oResponse) {
						var oResults = eval("(" + oResponse.responseText + ")");
						if((oResults.ResultSet.Result) && (oResults.ResultSet.Result.length)) {
							//Result is an array if more than one result, string otherwise
							if(YAHOO.lang.isArray(oResults.ResultSet.Result)) {
								for (var i=0, j=oResults.ResultSet.Result.length; i<j; i++) {
			
									var tempNode = new YAHOO.widget.generisNode(oResults.ResultSet.Result[i].label, node, false,false);
									tempNode.href = oResults.ResultSet.Result[i].link ;
									tempNode.target = oResults.ResultSet.Result[i].target ;
									tempNode.labelStyle = oResults.ResultSet.Result[i].icon;
			
									if( oResults.ResultSet.Result[i].icon == "icon-class")
										tempNode.setDynamicLoad(loadNodeData, currentIconMode);	
									tempNode.checkNodeEnable = '.$this->getCheckBoxTree().';							
			}
			} else {
				//there is only one result; comes as string:
				//var tempNode = new YAHOO.widget.generisNode(oResults.ResultSet.Result, node, false,false)
			}
			}
			
			
			oResponse.argument.fnLoadComplete();
			},
			
				//if our XHR call is not successful, we want to
				//fire the TreeView callback and let the Tree
				//proceed with its business.
				failure: function(oResponse) {
					YAHOO.log("Failed to process XHR transaction.", "info", "example");
					oResponse.argument.fnLoadComplete();
			},
			
			
				argument: {
					"Node": node,
						"fnLoadComplete": fnLoadComplete
			},
			
			
			timeout: 7000
			};
			
			
			YAHOO.util.Connect.asyncRequest(\'GET\', sUrl, callback);
			}
			
			function buildTree() {
				//create a new tree:
				tree = new YAHOO.widget.TreeView("treeDiv1");
			
				//turn dynamic loading on for entire tree:
				//tree.setDynamicLoad(loadNodeData, currentIconMode);
			
				//get root node for tree:
				var root = tree.getRoot();
		';
	}
	
	private $yuiTreeEnd = '
	
			// Expand and collapse happen prior to the actual expand/collapse,
			// and can be used to cancel the operation
			tree.subscribe("expand", function(node) {
				YAHOO.log(node.index + " was expanded", "info", "example");
				// return false; // return false to cancel the expand
		});
		
		tree.subscribe("collapse", function(node) {
			YAHOO.log(node.index + " was collapsed", "info", "example");
		});
		
		// Trees with TextNodes will fire an event for when the label is clicked:
		tree.subscribe("labelClick", function(node) {
			YAHOO.log(node.index + " label was clicked", "info", "example");
		});
		
		// Trees with TaskNodes will fire an event for when a check box is clicked
		tree.subscribe("checkClick", function(node) {
			YAHOO.log(node.index + " check was clicked", "info", "example");
		});
		
		//render tree with these toplevel nodes; all descendants of these nodes
		//will be generated as needed by the dynamic loader.
		tree.draw();
		}
		
		
		return {
			init: function() {
				YAHOO.util.Event.on(["mode0", "mode1"], "click", changeIconMode);
				var el = document.getElementById("mode1");
				if (el && el.checked) {
					currentIconMode = parseInt(el.value);
		} else {
			currentIconMode = 0;
		}
		
		buildTree();
		}
		
		}
		} ();
		
		//once the DOM has loaded, we can go ahead and set up our tree:
		YAHOO.util.Event.onDOMReady(YAHOO.example.treeExample.init, YAHOO.example.treeExample,true)
		
		</script>
		
	
	';

	public function __construct($checkBox = false){
		$this->checkBoxTree = $checkBox;
	}

	/**
	 * return javascript code of the tree
	 *
	 * @param Tree $tree
	 * @return String
	 */
	public function getAjaxTree(common_Tree $tree){
		$return = $this->yuiSrcInclusions.$this->yuiCssInclusions.$this->yuiTreeDiv;
		$return .= $this->yuiTreeControl();
		$return .= $this->getRootTree($tree);
		$return .= $this->yuiTreeEnd;
		return $return;
	}
	/**
	 * enter a list of node to select checkbox
	 *
	 * @param array selected uri
	 * @return void
	 */
	public function	setSelectedNode($array) {
		$this->selectedNode= $array;
	}
	
	public function setExpanded($bool){
		$this->expended = $bool;
	}
	
	public function getExpanded(){
		return $this->expended ? 'true' : 'false';
	}
	
	/**
	 * check if node is in a list of node to select the cjheckbox
	 *
	 * @param array selected uri
	 * @return void
	 */
	private function isNodeSelected($node){
		if($this->checkBoxTree){
			if(in_array($node->uriResource, $this->selectedNode)) {
				return 2;
			}
			
		}
		return 0;
	}
	
	/**
	 * return a javascript formated boolean to know if yes or not checkbox must be used
	 *
	 * @return String
	 */
	private function getCheckBoxTree(){
		return $this->checkBoxTree ? 'true' : 'false' ;
	}
	
	private function getLink(core_kernel_classes_Resource $node){
		//TODO handle type
		return HtmlViewLink.'?do=show&param1='.urlencode($node->uriResource).'&type=c';
		
	}
	
	private function getTarget(core_kernel_classes_Resource $node){
		return "pane";
	}
	
	private function getNodeType(core_kernel_classes_Resource $node){
		//TODO handle type in a better way
		if ($node instanceof core_kernel_classes_Class ){
			return "c";
		}
		if ($node instanceof core_kernel_classes_Property ){
			return "p";
		}
		if ($node instanceof core_kernel_classes_Resource ){
			return "i";
		}
		throw new common_Exception('problem getting type of node',E_ALL);
	}

	/**
	 * Enter description here...
	 *
	 * @param generis_Node $node
	 * @return unknown
	 */
	private function  getIconMode( $node){


		$s = $this->getNodeType($node);
		switch($s){
			case "m" : 
				return "icon-model"; break;
			case "c" :
				return 	"icon-class";break;
			case "p" :
				return "icon-property";	break;		
			case "i" :
				return "icon-instance";break;
			case "im" :
				return "icon-metaclass";break;
			default:
				$logger = new common_Logger('Ajax Tree');
				$logger->error("problem getting Type of Resource : " .$s , __FILE__, __LINE__);
				break; 
		}
	}

	private function getRootTree(common_Tree $tree){
		$rootNode = $tree->children->get(0);
		$root = $rootNode->getUserObject();
		$rootNumber = 0;
		$return ='
		var model = new YAHOO.widget.generisNode("Model", root,true,false);
		model.href="http://www.w3.org/1999/02/22-rdf-syntax-ns#&type=root";
		model.target="pane";
		model.labelStyle = "icon-model";
		
		';
		foreach ($rootNode->children->getIterator() as $nodeObject){	
			$rootNumber++;
			$node = $nodeObject->getUserObject(); 
			$return .=  '
				var tempNode'.$rootNumber.' = new YAHOO.widget.generisNode("'.trim($node->getLabel()) .'",model,true,false);
				tempNode'.$rootNumber.'.href="'.$this->getLink($node).'";
				tempNode'.$rootNumber.'.target= "'.$this->getTarget($node).'";
				tempNode'.$rootNumber.'.labelStyle = "'.$this->getIconMode($node).'";
				tempNode'.$rootNumber.'.checkNodeEnable = '.$this->getCheckBoxTree().';
				tempNode'.$rootNumber.'.checkState = '.$this->isNodeSelected($node).';
				tempNode'.$rootNumber.'.expanded = '.$this->getExpanded().';
			';


			if (!$nodeObject->isLeaf()) {
				$sonNumber = 0;	
				foreach ($nodeObject->children->getIterator() as $sonObject){
						$sonNumber++;
						$son = $sonObject->getUserObject();

						$return .=  '
							var sonNode'.$sonNumber.' = new YAHOO.widget.generisNode("'.trim($son->getLabel()) .'",tempNode'.$rootNumber.',true,false);
							sonNode'.$sonNumber.'.href="'.$this->getLink($son).'";
							sonNode'.$sonNumber.'.target= "'.$this->getTarget($son).'";
							sonNode'.$sonNumber.'.labelStyle = "'.$this->getIconMode($son).'";
							sonNode'.$sonNumber.'.checkNodeEnable = '.$this->getCheckBoxTree().';
							sonNode'.$sonNumber.'.checkState = '.$this->isNodeSelected($son).';
						';	

						if (!$sonObject->isLeaf()) {
							$leafNumber = 0;
							foreach ($sonObject->children->getIterator() as $leafObject){
							$leafNumber++;

								$leaf = $leafObject->getUserObject();

								$return .=  '
									var leafNode'.$leafNumber.' = new YAHOO.widget.generisNode("'.trim($leaf->getLabel()) .'",sonNode'.$sonNumber.',false,false);
									leafNode'.$leafNumber.'.href="'.$this->getLink($leaf).'";
									leafNode'.$leafNumber.'.target= "'.$this->getTarget($leaf).'";
									leafNode'.$leafNumber.'.labelStyle = "'.$this->getIconMode($leaf).'";
									leafNode'.$leafNumber.'.checkNodeEnable = '.$this->getCheckBoxTree().';
									leafNode'.$leafNumber.'.checkState = '.$this->isNodeSelected($leaf).';

								';	
								if ($this->getNodeType($leaf) === "c"){
									 $return .=  'leafNode'.$leafNumber.'.setDynamicLoad(loadNodeData, currentIconMode)';	
								}
							}
						}
					}
			}
		}

		return $return;
	}


}
?>
