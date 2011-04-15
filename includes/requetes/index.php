<?php /*
GUI for queries in Generis 4, 
by Bertrand Grégoire.
more info at: http://www.tao.lu
*/

require("../../core/api/generisApiPhp.php");
// interface
print '<html>
<head>
<title>Generis 4 - queries</title>
<link media="all" href="../../CSS/generis_default.css" type="text/css" rel="stylesheet" />
<link media="screen" href="requetes.css" type="text/css" rel="stylesheet" />
<script src="js/generisQueries.js" type="text/javascript"></script>
<link rel="shortcut icon" href="favicon.ico"/>
</head>

<body class="paneIframe queries">

<div id="reqCreator" class="group">
<h2 class="divhead Title">create your query</h2>
<form name="queryForm" action="javascript:runQuery()">
  <fieldset>
    <label for="classF">class</label>
    <input type="text" id="classF" title="start typing your class\' name, \'*\' lists all classes"/>
    <div id="autoClass" class="autoComplete"><!-- <ul><li id="#113707105956542" class="selected">demand</li><li id="#other">other</li></ul>--></div>
    <div id="propP">
      <label for="propF">property</label>
      <select type="text" id="propF" title="select a property" class="layout">
        <option>choose a class first</option>
      <!-- <option value="http://www.w3.org/2000/01/rdf-schema#literal" id="http://www.w3.org/2000/01/rdf-schema#label">label</option> -->
      </select>
    </div>
  <div id="valueP">
    <label for="operatorF">operator</label>
    <select type="text" id="operatorF" title="select an operator" class="layout">
      <option>choose a property first</option>
      <!-- <option value="http://www.tao.lu/ontologies/queries.rdf#is_ins">is</option> -->
    </select>
    <label for="valueF">value</label>
    <span id="valueHolder"><input type="text" id="valueF" title="enter the value here">choose a property first</input>
    <!-- <select id="valuef" title="select a value"><option value="http://www.w3.org/1999/02/22-rdf-syntax-ns#xmlliteral">xmlliteral</option> -->
    </span>
  </div>  
  <div id="action">
    <a href="#" id="run" class="button">run !</a>
    <a href="#" id="save" class="button">save !</a>
  </div>  
  </fieldset>
</form>

</div>

<div id="result" class="group">
  <h2 class="divhead Title">results</h2>
  <div id="resultF">
  <!-- <ul> <li class="instance" id="http://qinstanceURI">My result label</li> </ul> -->
  </div>
</div>

<div id="reqLibrary" class="group">
  <h2 class="divhead Title">saved queries</h2>
  <div id="libraryF">
  <!-- <ul class="instances" id="savedqueries">
    <li class="query" title="generis query" id="http://fulluri">
      <a href="javascript:removequery(&quot;http://fulluri&quot;,&quot;my query&quot;)" class="button delete" title="definetely delete query: my query">&oslash;</a>
      <a href="javascript:loadquery(&quot;http://fulluri&quot;)" class="button load" title="load query: my query">&eth;</a>my query
    </li> </ul> -->
  </div>
</div>
  
<script type="text/javascript" language="javascript" charset="utf-8"> 
  queriesInit();
</script>';

?>

</body>
</html>


