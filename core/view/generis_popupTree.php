<?php


							include_once("generis_ConstantsOfGui.php");
							include("generis_utils.php");
							include("generis_tree.php");
							$TAOtree = new TAOtree();
							$Treeoutput=$TAOtree->getOutput(TRUE,"","","");
							
							$Treeoutput=str_replace("show=","edit=",$Treeoutput);
							echo HEAD.'<body class=treeIframe><div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>
 <script language="JavaScript" src="./JS/overlib.js"><!-- overLIB (c) Erik Bosrup --></script>
<script language="JavaScript" src="./JS/tree.js"></script>
<script language="JavaScript" src="./JS/tree_items.js"></script>
				<script language="JavaScript" src="./JS/tree_tpl.js"></script><script language="JavaScript">var selected=[];var idproperty=\'\';</script>'.$Treeoutput.'</div>';
?>														