/*
GUI for queries in Generis 4, 
by Bertrand Grégoire.
more info at: http://www.tao.lu
*/
document.write('<script src="js/prototype.js" type="text/javascript"></script>');
document.write('<script src="js/scriptaculous.js" type="text/javascript"></script>');
document.write('<script src="js/DOMutils.js" type="text/javascript"></script>');

//globals
var props = new Array();
var values = new Array();
var details = new Array();
var loadstatus = 0;
var highlightEColor = "#e0e4d0";

function queriesInit(){
	//hide empty elements, using scriptaculous.js
	Element.hide("propP"); //hide
	Element.hide("valueP"); //hide
	Element.hide("action"); //hide

	// autocomplete class name (from scripaculous.js), triggers properties content update.
	new Ajax.Autocompleter("classF","autoClass","_requetesBackend.php",{paramName:"classV", afterUpdateElement:updateProperties}); 
	// load existing queries in libraryF
	new Ajax.Updater("libraryF", "_requetesBackend.php", {parameters:"listQueries=savedQueries", onComplete: function() {addQueriesNav("savedQueries");}, onFailure: reportError}); //new Ajax.Updater("resultF", "runQuery.php", {parameters: params, onFailure: reportError});
	// trigger operator and value hide on class selection // used Event.observe from prototype.js rather than addEvent for it prevents memory leaks.
	Event.observe("classF", "change", function(){hideDetails();}, false); // from prototype.js;
	// trigger operator and value update on property selection
	Event.observe("propF", "change", updateOperatorsAndValues, false); 
	// trigger save/run operations on links.
	Event.observe("run","click", runQuery, false);
	Event.observe("save","click", saveQuery, false);

	resultClass = $("result").className;
}

// gets list of properties on selected class and shows it in propF
function updateProperties(element, selectedElement){
	var params = "classID="+selectedElement.id+"&select=propF";
	new Ajax.Request("_requetesBackend.php", {parameters: params, onComplete: function(req){Effect.Appear("propP", {duration: 1.5}); eval(req.responseText); prependSelect($("propF"));}, onFailure: reportError}); 		
}

function prependSelect(select){
// insertBefore not well supported by IE6 for <option>, used add instead, from http://www.mredkj.com/tutorials/tutorial005.html
//      select.insertBefore(new Option("-- choose one --","",true,true), select.firstChild);
	insertBeforeIEFix(select, new Option("-- choose one --","",true,true), select.firstChild, 0);
}

function hideDetails(){
	Effect.Fade("propP", {duration: 0.5});
	Effect.Fade("valueP", {duration: 0.5});
	Effect.Fade("action", {duration: 0.5});
}

// gets list of operators and values on selected property and shows it in operatorF and valueF
function updateOperatorsAndValues(event){
	var params = "targetClassID="+ $("propF").value+"&targetNS=&valuesName=values&operatorsName=operators"; //propF.value is RANGE of property
	new Ajax.Request("_requetesBackend.php", { parameters: params, onComplete: showOperatorsAndValues, onFailure: reportError});
}

function showOperatorsAndValues(originalRequest){
	Effect.Appear("valueP", { duration: 1.5 });
	if (originalRequest.responseText) { //else: keep operators and values content, allows loading from backend.
		operators = new Array();
		values = new Array();
		eval(originalRequest.responseText); //sets "operators" and "values" content
	}

	var content=$("operatorF");
	removeChilds(content);
	if (operators.length == 0) {
		alert("You cannot define queries using this property by now... sorry. Contact your administrator for more information.");
	} else {
		for (var i=0; i<operators.length; i++)
			//content.appendChild(new Option(operators[i][0],operators[i][1])); //[0] is label, [1] is URI //KO in IE6
			content.options[i] = new Option(operators[i][0],operators[i][1]);
		prependSelect(content);
	}

	content=$("valueHolder");
	removeChilds(content);
	var child;
	if (values.length == 0) { // no proposed values -> input
		child = createElementTIT("input", "valueF", "enter the value here");
		content.appendChild(child);
	} else if (values.length < 5) { // few choices -> select
		child = createElementTIT("select", "valueF", "select a value");
		content.appendChild(child);
		for (var i=0; i<values.length; i++) {
			//child.appendChild(new Option(values[i][1],values[i][0])); //[1] is label, [0] is URI	//KO in IE6
			child.options[i] = new Option(values[i][1],values[i][0]);
		}
		prependSelect(child);
	} else { // TODO many choices -> autocomplete.
		child = createElementTIT("select", "valueF", "select a value");
		content.appendChild(child);
		for (var i=0; i<values.length; i++) {
			//child.appendChild(new Option(values[i][1],values[i][0])); //[1] is label, [0] is URI	//KO in IE6
			child.options[i] = new Option(values[i][1],values[i][0]);
		}
		prependSelect(child);
	}
	Event.observe("operatorF", "change", function(){Effect.Appear("action", { duration: 1.5 });}, false); // from prototype.js
}

// executes query
function runQuery(){
	try{
		var classID = getElementsByClass("selected",$("autoClass"),"li")[0].id;
		if (classID == null || classID == "") throw "Class field is empty";
		var propID = $("propF").options[$("propF").selectedIndex].id;
		if (propID == null || propID == "") throw "Property field is empty";
		var oper = $("operatorF").value;
		if (oper == null || oper == "") throw "Operator field is empty";
		var value = $("valueF").value;
		if (value == null || value == "") throw "Value field is empty";

		var params = "op=build&cURI="+classID+"&propURI="+propID+"&operTxt="+oper+"&valTxt="+value+"&ulID=resultUl";
//		alert ("running: "+params);
		// Ajax.Updater puts list of results in resultF
		new Ajax.Updater("resultF", "runQuery.php", {parameters: params, onComplete:function(){addViewNav("resultUl"); new Effect.Highlight("result", {endcolor:highlightEColor, restorecolor:true});}, onFailure: reportError});
		setStatus("... searching ...");
	}catch(ex){
		alert("You need to complete all elements of the query prior to executing the request. \n"+ex);
		throw ex;
	}
}

//saves query
function saveQuery(){
	try{
		var classID = getElementsByClass("selected",$("autoClass"),"li")[0].id;
		if (classID == null || classID == "") throw "Class field is empty";
		var propID = $("propF").options[$("propF").selectedIndex].id;
		if (propID == null || propID == "") throw "Property field is empty";
		var oper = $("operatorF").value;
		if (oper == null || oper == "") throw "Operator field is empty";
		var value = $("valueF").value;
		if (value == null || value == "") throw "Value field is empty";

		var label = prompt("Please give a label for this query",$F("classF")+": "+$("propF").options[$("propF").selectedIndex].text+" "+$("operatorF").options[$("operatorF").selectedIndex].text+" "+($F("valueF")));
		if (label==null) //cancel has been hit
			return;
		var params = "op=save&cURI="+classID+"&propURI="+propID+"&operTxt="+oper+"&valTxt="+value+"&label="+label;
		// Ajax.Updater puts list of results (query ID) in resultF
		new Ajax.Request("runQuery.php", {parameters:params, onComplete: function() {	new Ajax.Updater("libraryF", "_requetesBackend.php", {parameters:"listQueries=savedQueries", onComplete: function() {addQueriesNav("savedQueries"); new Effect.Highlight("reqLibrary", {endcolor:highlightEColor, restorecolor:true});}, onFailure: reportError});}, onFailure: reportError});
	}catch(ex){
		alert("You need to complete all elements of the query prior to saving."+ex);
	}
}

function removeQuery(uri,label){
	if (confirm("Are you sure you want to delete the query "+label)) {
		new Ajax.Request("runQuery.php", {parameters:"op=dele&uri="+uri, onComplete: function() {	new Ajax.Updater("libraryF", "_requetesBackend.php", {parameters:"listQueries=savedQueries", onComplete: function() {addQueriesNav("savedQueries");}, onFailure: reportError});}, onFailure: reportError});
	}
}

/* gest the details of a query and sets up the GUI to edit it */
function loadQuery(uri){
	new Ajax.Request("runQuery.php", {parameters:"op=details&uri="+uri+"&array=details", onComplete: editQuery, onFailure: reportError});
	Effect.Fade("propP", {duration: 0.5});
	hideDetails();
	setStatus("... loading ...");
}


function editQuery(request){
	eval(request.responseText); //sets "details" content
	loadstatus = 2; //synchronize 2 requests

	//operators and values
	operators = new Array();
	values = new Array();

	var params = "targetClassID="+details[5]+"&targetNS=&valuesName=values&operatorsName=operators";
	new Ajax.Request("_requetesBackend.php", { parameters: params, onComplete: function(req){eval(req.responseText); updateEdit();}, onFailure: reportError}); // eval sets "values" and "operators" content
	//properties
	params = "classID="+details[1]+"&select=propF";
	new Ajax.Request("_requetesBackend.php", {parameters: params, onComplete: function(req){eval(req.responseText); prependSelect($("propF")); updateEdit();}, onFailure: reportError}); //sets propF content.

	//class
	$("classF").value=details[2];
	removeChilds($("autoClass"));
	var li=createElementTIT("li",details[1],details[2]);
//	var selected = "selected";
	li.className= "selected";//setAttribute("class",selected);
	li.appendChild(document.createTextNode(details[2]));
	//seems not to work with IE6 
	$("autoClass").appendChild(li);
}

function updateEdit(){
	loadstatus -= 1;
	if (loadstatus == 0) {
		showOperatorsAndValues("");
		setSelectedById($("propF"), details[3]);
		setSelectedByValue($("operatorF"), details[6]); //todo use label as operators label.
		if (values.length < 2) { // valueF is input
			$("valueF").value = details[8];
		} else { // valueF is select
			setSelectedByValue($("valueF"), details[8]);
		}
		Effect.Appear("propP", { duration: 1.5 });
		Effect.Appear("valueP", { duration: 1.5 });
		Effect.Appear("action", { duration: 1.5 });
		setStatus("");
	} 
	//else //waiting for one more requests to finish
}

// add load/delete button in a list of (query) instances
function addQueriesNav(lID){
	var queries = $(lID).getElementsByTagName("li");
	var li, a;
	for (var i=0; i<queries.length; i++) {
		if((li = queries[i]).className == "query") { //TODO check for inclusion and not equality!
			a = createElementTV("a","Ø"); // X or Ø
			a.setAttribute("href","javascript:removeQuery(\""+li.id+"\",\""+li.lastChild.data+"\")");
			a.className = "button delete";
			a.setAttribute("title","definetely delete query: "+li.lastChild.data);
			li.insertBefore(a,li.lastChild);
			a = createElementTV("a","Ð"); // «, Ð or î 
			a.setAttribute("href","javascript:loadQuery(\""+li.id+"\")");
			a.className = "button load";
			a.setAttribute("title","load query: "+li.lastChild.data);
			li.insertBefore(a,li.lastChild);
			// eventually use generis icons: /generis/generis/GenerisUI/icons/edit.png and erase.png
		}
	}
}

// add view button in a list of (generis_resources) instances
function addViewNav(lID){
	var queries = $(lID).getElementsByTagName("li");
	var li, a;
	for (var i=0; i<queries.length; i++) {
		li = queries[i];
		a = createElementTV("a","view !");
		a.setAttribute("href","javascript:view(\""+li.id+"\")");
		a.className="button view";
		a.setAttribute("title","view instance: "+li.lastChild.data);
		li.insertBefore(a,li.lastChild);
			// eventually use generis icons: /generis/generis/GenerisUI/icons/b_search.png
	}
}

function view(instance){
	window.location="../../TAOPaneControllerH.php?ns=&show="+instance+"&type=i";
	//todo check, seems not to work with full URIs ... gives smthg like: 
	// http://gorgonzola:82/generis/generis/GenerisUI//TAOPaneControllerH.php?ns=&show=http://gorgonzola:82/middleware/businessmodel.rdf#i1138096974050807800&type=i
}

function reportError(request) {
	setStatus("Error encountered: ".request);
}

function setStatus(status){
	removeChilds($("resultF"))
	$("resultF").appendChild(createElementTV("p",status));
}
