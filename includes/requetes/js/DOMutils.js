// DOM facilitators, by Bertrand Grégoire for Generis 4.
//more info at: http://www.tao.lu
// removes all child of DOM Node
function removeChilds(elt){
	var child;
	while(child = elt.firstChild){
		elt.removeChild(child);
	}
}

// returns new DOM element <tag>value</tag>
function createElementTV(tag, value){
	var elt = document.createElement(tag);
	elt.appendChild(document.createTextNode(value));
	return elt;
}

// returns new DOM element <tag id="id" title="title"/>
function createElementTIT(tag, id, title){
	var elt = document.createElement(tag);
	elt.setAttribute("id",id);
	elt.setAttribute("title",title);
	return elt;
}

/* grab Elements from the DOM by className */
function getElementsByClass(searchClass,node,tag) {
	var classElements = new Array();
	if ( node == null )
		node = document;
	if ( tag == null )
		tag = "*";
	var els = node.getElementsByTagName(tag);
	var elsLen = els.length;
	var pattern = new RegExp("(^|\\s)"+searchClass+"(\\s|$)");
	for (i = 0, j = 0; i < elsLen; i++) {
		if ( pattern.test(els[i].className) ) {
			classElements[j] = els[i];
			j++;
		}
	}
	return classElements;
}

/* set a SELECT element according to a value */
function setSelectedByValue(select, value) {
    for (var i = 0; i < select.options.length; i++) {
        if (select.options[i].value == value) {
            select.selectedIndex = i;
            break;
        }    
    }
    return;
}

/* set a SELECT element accoring to one id */
function setSelectedById(select, id) {
    for (var i = 0; i < select.options.length; i++) {
		if (select.options[i].id == id) {
//			alert("match by id: "+i);
            select.selectedIndex = i;
            break;
        }    
    }
    return;
}


/* inserts node in parent before next (wich position is pos) */
function insertBeforeIEFix(parent, node, next, pos){
// insertBefore not well supported by IE6, used add instead, from http://www.mredkj.com/tutorials/tutorial005.html
//      select.insertBefore(new Option("-- choose one --","",true,true), select.firstChild);
	try {
      parent.add(node, next); // standards compliant; doesn't work in IE
    }
    catch(ex) {
      parent.add(node, pos); // IE only
    }

}