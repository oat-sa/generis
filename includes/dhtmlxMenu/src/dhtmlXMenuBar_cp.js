		/**  
          *     @desc: context menu object
          *     @type: public
		  *     @param: width - menu width
		  *     @param: height - menu height
	      *     @topic: 0  
          */ 
function dhtmlXContextMenuObject(width,height){
	this.menu=new dhtmlXMenuBarObject(document.body,width,height,name,1);
	this.menu.hideBar();
	this.menu.contextMenu=this;
	this.menu.enableWindowOpenMode(false);
	this.menu.setOnClickHandler(this._innerOnClick);
}

		/**  
          *     @desc: set onClick context menu handler
          *     @type: public
		  *     @param: func - user defined function
	      *     @topic: 0  
          */ 
dhtmlXContextMenuObject.prototype.setContextMenuHandler=function(func){ 
  if (typeof(func)=="function") this.onClickHandler=func; else this.onClickHandler=eval(func); 
  }

		/**  
          *     @desc: default menu onclick precessor
          *     @type: private
		  *     @param: id - clicked node id
	      *     @topic: 0  
          */ 
  
dhtmlXContextMenuObject.prototype._innerOnClick=function(id){
	var that=document.body.contextMenu; 
	if (document.body.onclick) document.body.onclick();
	if (that.onClickHandler) return that.onClickHandler(id,that.zoneId);
	return true;
}

		/**  
          *     @desc: set context zones
          *     @type: public
		  *     @param: htmlObject - id of html tag or html object
		  *     @param: zoneId - id of zone
	      *     @topic: 0  
          */
dhtmlXContextMenuObject.prototype.setContextZone=function(htmlObject,zoneId){
	if (typeof(htmlObject)!="object") 
		htmlObject=document.getElementById(htmlObject);
		
	htmlObject.contextOnclick=htmlObject.onmousedown;
	htmlObject.onmousedown=this._contextStart;
	htmlObject.contextMenu=this;
	htmlObject.contextMenuId=zoneId;
}
		/**  
          *     @desc: open context menu
          *     @type: private
		  *     @param: e - event object
	      *     @topic: 0  
          */
dhtmlXContextMenuObject.prototype._contextStart=function(e){
	if (!e) e=event; 
	if (document.body.onclick) document.body.onclick();
	
	if ((!e)||(e.button!=2))
	{
		if (this.contextOnclick) this.contextOnclick();
		return true;
	}
	else this.contextMenu.menu.showBar();
	
	var a=this.contextMenu.menu.topNod;
	a.style.position="absolute";
	a.style.left=e.clientX+document.body.scrollLeft;
	a.style.top=e.clientY+document.body.scrollTop;
	

	
	document.body.oncontextmenu=new Function("document.body.oncontextmenu=new Function('if (document.body.onclick) document.body.onclick(); return false;'); return false; ");		
	document.body.onclick=this.contextMenu._contextEnd;
	document.body.contextMenu=this.contextMenu;
	this.contextMenu.zoneId=this.contextMenuId;
	return false;	
}
		/**  
          *     @desc: close context menu
          *     @type: private
		  *     @param: e - event object
	      *     @topic: 0  
          */
dhtmlXContextMenuObject.prototype._contextEnd=function(e){
	var menu=this.contextMenu.menu;
	menu._closePanel(menu);
	menu.lastOpenedPanel="";
	menu.lastSelectedItem=0;
	menu.hideBar();
	document.body.onclick=null;
	return false;	
}
