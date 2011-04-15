// Title: Tigra Tree
// Description: See the demo at url
// URL: http://www.softcomplex.com/products/tigra_menu_tree/
// Version: 1.1
// Date: 11-12-2002 (mm-dd-yyyy)
// Notes: This script is free. Visit official site for further details.

function tree (a_items, a_template, modifyrange) {

	
	this.a_tpl      = a_template;
	this.a_config   = a_items;
	this.o_root     = this;
	this.a_index    = [];
	this.o_selected = null;
	this.n_depth    = -1;
	this.selected = selected;
	this.idproperty = idproperty;
	var o_icone = new Image(),
		o_iconl = new Image();
	o_icone.src = 'icons/empty.gif';
	o_iconl.src = 'icons/line.gif';
	a_template['im_e'] = o_icone;
	a_template['im_l'] = o_iconl;
	
	for (var i = 0; i < 28; i++)
		if (a_template['icon_' + i]) {
			var o_icon = new Image();
			a_template['im_' + i] = o_icon;
			o_icon.src = a_template['icon_' + i];
		}
	
	this.toggle = function (n_id) {	var o_item = this.a_index[n_id]; o_item.open(o_item.b_opened) };
	this.select = function (n_id) { return this.a_index[n_id].select(); };
	this.mout   = function (n_id) { this.a_index[n_id].upstatus(true) };
	this.mover  = function (n_id) { this.a_index[n_id].upstatus() };

	this.a_children = [];
	for (var i = 0; i < a_items.length; i++)
		new tree_item(this, i);

	this.n_id = trees.length;
	
	trees[this.n_id] = this;
	
	for (var i = 0; i < this.a_children.length; i++) {
		document.write(this.a_children[i].init());
		this.a_children[i].open();
		if (indexo!=-1)
		{trees[0].toggle(indexo);}
	}
	
	
}
function tree_item (o_parent, n_order) {

	this.n_depth  = o_parent.n_depth + 1;
	this.a_config = o_parent.a_config[n_order + (this.n_depth ? 2 : 0)];
	if (!this.a_config) return;

	this.o_root    = o_parent.o_root;
	this.o_parent  = o_parent;
	this.n_order   = n_order;
	this.b_opened  = !this.n_depth;

	this.n_id = this.o_root.a_index.length;
	this.o_root.a_index[this.n_id] = this;
	o_parent.a_children[n_order] = this;

	this.a_children = [];
	for (var i = 0; i < this.a_config.length - 2; i++)
		new tree_item(this, i);

	this.get_icon = item_get_icon;
	this.open     = item_open;
	this.select   = item_select;
	this.init     = item_init;
	this.upstatus = item_upstatus;
	this.is_last  = function () { return this.n_order == this.o_parent.a_children.length - 1 };
}

function item_open (b_close) {
	var o_idiv = get_element('i_div' + this.o_root.n_id + '_' + this.n_id);
	indexo=-1;
	
	
	if (!o_idiv) return;
	
	if (!o_idiv.innerHTML) {
		var a_children = [];
		for (var i = 0; i < this.a_children.length; i++)
				
				a_children[i]= this.a_children[i].init();
				

		o_idiv.innerHTML = a_children.join('');
	}
	o_idiv.style.display = (b_close ? 'none' : 'block');
	
	this.b_opened = !b_close;
	var o_jicon = document.images['j_img' + this.o_root.n_id + '_' + this.n_id],
		o_iicon = document.images['i_img' + this.o_root.n_id + '_' + this.n_id];
	if (o_jicon) o_jicon.src = this.get_icon(true);
	if (o_iicon) o_iicon.src = this.get_icon();
	this.upstatus();
	
	if (indexo!=-1)	{trees[0].toggle(indexo);}
	
}

function item_select (b_deselect) {
	
	if (!b_deselect) {
		var o_olditem = this.o_root.o_selected;
		this.o_root.o_selected = this;
		if (o_olditem) o_olditem.select(true);
	}
	var o_iicon = document.images['i_img' + this.o_root.n_id + '_' + this.n_id];
	if (o_iicon) o_iicon.src = this.get_icon();
	get_element('i_txt' + this.o_root.n_id + '_' + this.n_id).style.textDecoration = b_deselect ? 'none' : 'underline';
	get_element('i_txt' + this.o_root.n_id + '_' + this.n_id).style. fontWeight  = b_deselect ? 'normal' : 'bold';


	this.upstatus();
	return Boolean(this.a_config[1]);
}

function item_upstatus (b_clear) {
	//window.setTimeout('window.status="' + (b_clear ? '' : this.a_config[0] + (this.a_config[1] ? ' ('+ this.a_config[1] + ')' : '')) + '"', 10);
}
function find(el,v_array)
	
	{
	
    var j = 0;
    var v_regexp = new RegExp(el,"gi");

    for ( var i = 0; i < v_array.length; i++ ) {           
         if ( v_regexp.test(v_array[i]) ) {
             return true
             j++;
         }
    }
	return false
	}

function item_init () {
	var a_offset = [],
		myimg='',
		substri='',
		o_current_item = this.o_parent;
	for (var i = this.n_depth; i > 1; i--) {
		a_offset[i] = '<img src="' + this.o_root.a_tpl[o_current_item.is_last() ? 'icon_e' : 'icon_l'] + '" border="0" align="absbottom">';
		o_current_item = o_current_item.o_parent;
	}
	
	


stpos=this.a_config[1].indexOf("param1=") + 8;
stpostype=this.a_config[1].indexOf("type=");
idressource = this.a_config[1].substring(stpos-1,stpostype-1);

if (index0==-1)
	{	//alert(substri);
		if (find(idressource,toOpen))
		{indexo=this.n_id;}
		
	}  
//




/****************************** Get resource type and change icon****************************************////
stpos=this.a_config[1].indexOf("type=") + 5;
substri = this.a_config[1].substring(stpos);
	
	switch (substri)
	{

	case "i":
		
		myimg = '<img src=./icons/'+urlicons+'Instance.gif border=0>';
		break;
	case "p":
		myimg = '<img src=./icons/'+urlicons+'Property.gif border=0>';
		break;
	case "m":
		myimg = '<img src=./icons/'+urlicons+'MetaClass.gif border=0>';
		break;
	case "im":
		myimg = '<img src=./icons/'+urlicons+'ClassWithMC.gif border=0>';
		break;
	case "sp":
		myimg = '<img src=./icons/'+urlicons+'Property.gif border=0>';
		break;
	case "root":
		myimg = '<img src=./icons/'+urlicons+'Generis_Model.png border=0>&nbsp:&nbsp;';
		break;
	case "c":
		if (this.b_opened ? 8 : 0)
		{
			myimg = '<img src=./icons/folderopen.gif border=0>';
		}
		else
		{
		myimg = '<img src=./icons/'+urlicons+'Class.gif border=0>';
		}
		break;
	}
	
	

/*************************
switch between options :
modifyrange :used in properties edition/creation to select range of this property
notarget : does not target the right pane
subscriber selction

***********////

	if (target=="notarget")
	{
	
	zelink = '<a href="' + this.a_config[1] + '" onclick="return trees[' + this.o_root.n_id + '].select(' + this.n_id + ')" ondblclick="trees[' + this.o_root.n_id + '].toggle(' + this.n_id + ')" onmouseover="trees[' + this.o_root.n_id + '].mover(' + this.n_id + ')" onmouseout="trees[' + this.o_root.n_id + '].mout(' + this.n_id + ')" class="t' + this.o_root.n_id + 'i" id="i_txt' + this.o_root.n_id + '_' + this.n_id + '">';
	}
	else
	{
		zelink = '<a href="' + this.a_config[1] + '" target="' + this.o_root.a_tpl['target'] + '" onclick="return trees[' + this.o_root.n_id + '].select(' + this.n_id + ')" ondblclick="trees[' + this.o_root.n_id + '].toggle(' + this.n_id + ')" onmouseover="trees[' + this.o_root.n_id + '].mover(' + this.n_id + ')" onmouseout="trees[' + this.o_root.n_id + '].mout(' + this.n_id + ')" class="t' + this.o_root.n_id + 'i" id="i_txt' + this.o_root.n_id + '_' + this.n_id + '">';
	}
check='';
if (setcheckbox=="0")	{check='';}
if (setcheckbox=="1")
	{ 
		if ((substri=="i") || (substri=="c"))
		{
		ischecked='';
		for (var i = 0; i < this.o_root.selected.length; i++) {
		
		if (this.o_root.selected[i]==idressource)
			{
				
				ischecked='CHECKED';
			}
		}
		
		propertyrdfid=this.o_root.idproperty;
		
		check = '&nbsp;&nbsp;<input type=checkbox '+ischecked+' name=instanceCreation[properties]['+propertyrdfid+'][] value=' + idressource + '>&nbsp;';

		}
		else 
		{	check='';
		}
	}


if (setcheckbox=="2")
{	
	
	idgrouppos=this.a_config[1].indexOf("=") + 1;
	idgroup = this.a_config[1].substring(idgrouppos);
	
	
	if (checkedgroup==idgroup)
	{
		
			check = '&nbsp;&nbsp;<input type=radio CHECKED value=' + idgroup + ' ' + ' name=editanuser[selectedgroup]>&nbsp;';
		
	}
	else 
		
	{	if (idgroup!='subscribers')
		{
		
		check = '&nbsp;&nbsp;<input type=radio value=' + idgroup + ' ' + ' name=editanuser[selectedgroup]>&nbsp;';
		}
		else {check='';}
	}	
	
}

if (setcheckbox=="3")
{	
	
	idgrouppos=this.a_config[1].indexOf("=") + 1;
	idrightspos=this.a_config[1].indexOf("rights=")-1;
	rights=this.a_config[1].substring(idrightspos+8);
	idgroup = this.a_config[1].substring(idgrouppos,idrightspos);
	
	if (rights=="0")
	{
		check = '&nbsp;&nbsp;<input type=radio CHECKED value=0 ' + ' name=rights[selectedsubscriber]['+idgroup+']>None<input type=radio value=1 ' + ' name=rights[selectedsubscriber]['+idgroup+']>Overview<input type=radio value=2 ' + ' name=rights[selectedsubscriber]['+idgroup+']>Read&nbsp;';

	}
	if (rights=="1")
	{
		check = '&nbsp;&nbsp;<input type=radio value=0 ' + ' name=rights[selectedsubscriber]['+idgroup+']>None<input CHECKED type=radio value=1 ' + ' name=rights[selectedsubscriber]['+idgroup+']>Overview<input type=radio value=2 ' + ' name=rights[selectedsubscriber]['+idgroup+']>Read&nbsp;';

	}
	if (rights=="2")
	{
		check = '&nbsp;&nbsp;<input type=radio value=0 ' + ' name=rights[selectedsubscriber]['+idgroup+']>None<input type=radio value=1 ' + ' name=rights[selectedsubscriber]['+idgroup+']>Overview<input type=radio CHECKED value=2 ' + ' name=rights[selectedsubscriber]['+idgroup+']>Read&nbsp;';

	}
	
}

/********************************************************************************************/


return '<table cellpadding="0" cellspacing="0" border="0"><tr ><td  nowrap>' + (this.n_depth ? a_offset.join('') + (this.a_children.length	? '<a href="javascript: trees[' + this.o_root.n_id + '].toggle(' + this.n_id + ')" onmouseover="trees[' + this.o_root.n_id + '].mover(' + this.n_id + ')" onmouseout="trees[' + this.o_root.n_id + '].mout(' + this.n_id + ')"><img src="' + this.get_icon(true) + '" border="0" align="absbottom" name="j_img' + this.o_root.n_id + '_' + this.n_id + '"></a>'	: '<img src="' + this.get_icon(true) + '" border="0" align="absbottom">') : '') + zelink + myimg + this.a_config[0] + '</a>' + check + '</td></tr></table>' + (this.a_children.length ? '<div id="i_div' + this.o_root.n_id + '_' + this.n_id + '" style="display:none"></div>' : '');
}



function item_get_icon (b_junction) {
	return this.o_root.a_tpl['icon_' + ((this.n_depth ? 0 : 32) + (this.a_children.length ? 16 : 0) + (this.a_children.length && this.b_opened ? 8 : 0) + (!b_junction && this.o_root.o_selected == this ? 4 : 0) + (b_junction ? 2 : 0) + (b_junction && this.is_last() ? 1 : 0))];
}



var index0= -1;
var trees = [];
get_element = document.all ?
function (s_id) 
{ return document.all[s_id] } :
function (s_id) 
{ return document.getElementById(s_id) };


