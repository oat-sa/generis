		/**  
          *     @desc: protobar object
          *     @param: func - user defined function
          *     @type: private	
          *     @topic: 0  
          */
		function dhtmlXProtobarObject(){
			return this;
		}
		
		/**  
          *     @desc: set action hadler on menu showing
          *     @param: func - user defined function
          *     @type: public
          *     @topic: 2  
          */ 		
		dhtmlXProtobarObject.prototype.setOnShowHandler=function(func){
				  if (typeof(func)=="function") this.onShow=func; else this.onShow=eval(func); 
		};
						
		/**  
          *     @desc: return item index in collection by id
          *     @type: private
		  *     @param: id - item id
		  *     @topic: 3
          */		
		dhtmlXProtobarObject.prototype._getItemIndex=function(id){
			for (var i=0; i<this.itemsCount; i++)
			{
				if (this.items[i].id==id) return i;
			};		
			return -1;
		};
		/**  
          *     @desc: set path to system images
          *     @param: path - relative path to images
          *     @type: public
          *     @topic: 2 
          */ 		
		dhtmlXProtobarObject.prototype.setGfxPath=function(path){
				this.sysGfxPath=path;
		};
				
		/**  
          *     @desc: set action hadler on menu hiding
          *     @param: func - user defined function
          *     @type: public
          *     @topic: 2 
          */ 		
		dhtmlXProtobarObject.prototype.setOnHideHandler=function(func){
			  if (typeof(func)=="function") this.onHide=func; else this.onHide=eval(func); 		
		};
		/**  
          *     @desc: set item individual action
          *     @param: id - item identificator
          *     @param: action  - js function called on item selection
          *     @type: public
          *     @topic: 2  
          */ 			
		dhtmlXProtobarObject.prototype.setItemAction=function(id,action){
			var z=this._getItemIndex(id);
			if (z>=0){
				this.items[z].setSecondAction(action);
			};
		};
				/**  
          *     @desc: return item object by id
          *     @type: public
		  *     @param: itemId - item id
	      *     @topic: 4
          */		
		dhtmlXProtobarObject.prototype.getItem=function(itemId){
			var z=this._getItemIndex(itemId);
			if (z>=0) 	return this.items[z];
		};
		/**  
          *     @desc: hide menu buttons
          *     @type: public
		  *     @param: idList - list of items's ids, separated by comma
	      *     @topic: 4
          */			
		dhtmlXProtobarObject.prototype.hideButtons=function(idList){
			if (!idList){
			for (var i=0; i<this.itemsCount; i++){
				this.items[i].getTopNode().style.display="none";
				this.items[i].hide=1;
				}
			return 0;
			}
			
			var temp=idList.split(",");
			for (var i=0; i<temp.length;  i++)
			{
			this.hideItem(temp[i]);
			};
		};
		/**  
          *     @desc: show menu buttons
          *     @type: public
		  *     @param: idList - list of items's ids, separated by comma
	      *     @topic: 4
          */			
		dhtmlXProtobarObject.prototype.showButtons=function(idList){
			if (!idList){
			for (var i=0; i<this.itemsCount; i++){
				this.items[i].getTopNode().style.display="";
				this.items[i].hide=0;
				}
			return 0;
			}
			
			var temp=idList.split(",");
			for (var i=0; i<temp.length;  i++)
			{
			this.showItem(temp[i]);
			};
		};		
				/**  
          *     @desc: disable menu button
          *     @type: public
		  *     @param: itemId - item id
	      *     @topic: 4
          */			
		dhtmlXProtobarObject.prototype.disableItem=function(itemId){
		var z=this.getItem(itemId);
			if (z) { if (z.disable) z.disable();  }
		};
		/**  
          *     @desc: enable menu button
          *     @type: public
		  *     @param: itemId - item id
	      *     @topic: 4
          */			
		dhtmlXProtobarObject.prototype.enableItem=function(itemId){
		var z=this.getItem(itemId);
			if (z) { if (z.enable) z.enable();  }
		};		
		
		/**  
          *     @desc: hide menu button
          *     @type: public
		  *     @param: itemId - item id
	      *     @topic: 4
          */			
		dhtmlXProtobarObject.prototype.hideItem=function(itemId){
			var z=this.getItem(itemId);
			if (z) { z.getTopNode().style.display="none";  z.hide=1; }
		}
/**  
          *     @desc: show menu button
          *     @type: public
		  *     @param: id - item id
	      *     @topic: 4
          */				
		dhtmlXProtobarObject.prototype.showItem=function(id){
			var z=this.getItem(id);
			if (z) {  z.getTopNode().style.display=""; z.hide=0; }
		}
		/**  
          *     @desc: set default action
          *     @type: public
		  *     @param: action - set default action
	      *     @topic: 2
          */				
		dhtmlXProtobarObject.prototype.setOnClickHandler=function(func){
		  if (typeof(func)=="function") this.defaultAction=func; else this.defaultAction=eval(func); 
		};
		/**  
          *     @desc: set menu tittle
          *     @type: public
		  *     @param: name - new tittle, shown on menu
	      *     @topic: 0
          */		
		dhtmlXProtobarObject.prototype.setTitleText=function(newText){
			this.tname=newText;
			this.nameCell.innerHTML=newText;
			this.preNameCell.innerHTML=newText;
		};
				/**  
          *     @desc: set menu size
          *     @type: public
		  *     @param: width - menu width
		  *     @param: height - menu height
	      *     @topic: 0
          */
		  
		dhtmlXProtobarObject.prototype.setBarSize=function(width,height){
			if(width) this.topNod.width=width;
			if(height) this.topNod.height=height;		
		};
				/**  
          *     @desc: hide all items, show only items which ids in list
          *     @type: public
		  *     @param: idList - list of id's, separated by comma
	      *     @topic: 4
          */
	dhtmlXProtobarObject.prototype.resetBar=function(idList){
		for (var i=0; i<this.itemsCount;  i++)
		{
			this.hideItem(this.items[i].id);
			this.items[i].persAction=0;
		};
		var temp=idList.split(",");
		for (var i=0; i<temp.length;  i++)
		{
			this.showItem(temp[i]);
		};
	};

		/**  
          *     @desc: load XML from file
          *     @type: public
		  *     @param: file - file name
	      *     @topic: 0
          */
		dhtmlXProtobarObject.prototype.loadXML=function(file){ this.xmlUnit.loadXML(file); };

		/**  
          *     @desc: load XML from string
          *     @type: public
		  *     @param: xmlString - string contining XML
	      *     @topic: 0
          */
		dhtmlXProtobarObject.prototype.loadXMLString=function(xmlString){ this.xmlUnit.loadXMLString(xmlString); };			

		/**  
          *     @desc: show menu
          *     @type: public
	      *     @topic: 0 
          */
		dhtmlXProtobarObject.prototype.showBar=function(){ this.topNod.style.display=""; if (this.onShow) this.onShow(); };
		/**  
          *     @desc: hide menu
          *     @type: public
	      *     @topic: 0
          */
		dhtmlXProtobarObject.prototype.hideBar=function(){ this.topNod.style.display="none"; if (this.onHide) this.onHide(); };
				/**  
          *     @desc: set menu buttons alignment (allowed - 'left','center','right','top','middle','bottom')
		  *		@param: align - buttons alignment
          *     @type: public
	      *     @topic: 0
          */
		dhtmlXProtobarObject.prototype.setBarAlign=function(align){
			if ((align=="left")||(align=="top")) { 	this.preNameCell.innerHTML="";
													this.preNameCell.style.display="none";
													this.nameCell.style.display="";	
													this.nameCell.width="100%";		
													this.nameCell.innerHTML=this.tname;
																									
												};
			if ((align=="center")||(align=="middle")){ 
													this.preNameCell.style.display="";	
													this.preNameCell.width="50%";														
													this.nameCell.style.display="";	
													this.nameCell.width="50%";			
													this.nameCell.innerHTML=this.tname;	
													this.preNameCell.innerHTML=this.tname;												
												};
			if ((align=="right")||(align=="bottom"))	{ 
													this.nameCell.innerHTML="";
													this.nameCell.style.display="none";
													this.preNameCell.style.display="";	
													this.preNameCell.width="100%";	
													this.preNameCell.innerHTML=this.tname;													
												};
		};
	
		dhtmlXProtobarObject.prototype.dummyFunc=function(){ return true; };
		dhtmlXProtobarObject.prototype.badDummy=function(){return false; };						
		
		

		/**  
          *     @desc: image button prototype 
          *     @type: private
          *     @topic: 0  
          */ 	
function dhtmlXButtonPrototypeObject(){ 
	return this;
};
		/**  
          *     @desc: set default action, action function take one parametr - item id
          *     @type: public
		  *     @param: func - js function
		  *     @topic: 2
          */	
	dhtmlXButtonPrototypeObject.prototype.setAction=function(func){
		 if (typeof(func)=="function") this.action=func; else this.action=eval(func); 		
		}
		/**  
          *     @desc: set personal onClick action (action must return false for preventing calling default action after personal), action function take one parametr - item id
		  *		@param: func - js function
          *     @type: public
          *     @topic: 2  
          */
	dhtmlXButtonPrototypeObject.prototype.setSecondAction=function(func){
		 if (typeof(func)=="function") this.persAction=func; else this.persAction=eval(func); 		
		};		
		/**  
          *     @desc: enable object
          *     @type: public
          *     @topic: 4  
          */		
		dhtmlXButtonPrototypeObject.prototype.enable=function(){
			if (this.disableImage) this.imageTag.src=this.src;
			else		
				if (!this.className)
					this.topNod.className=this.objectNode.className;	
				else 
					this.topNod.className=this.className;	

				if (this.textTag)					
					this.textTag.className=this.textClassName;
	
		this.topNod.onclick=this._onclickX;
		this.topNod.onmouseover=this._onmouseoverX;
		this.topNod.onmouseout=this._onmouseoutX;
		this.topNod.onmousedown=this._onmousedownX;			
		this.topNod.onmouseup=this._onmouseupX;		
		};			
		/**  
          *     @desc: disable object
          *     @type: public
          *     @topic: 4  
          */
		dhtmlXButtonPrototypeObject.prototype.disable=function(){
			if (this.disableImage) 
				{
				this.imageTag.src=this.disableImage;
				}
			else this.topNod.className="iconGray";
			
			if (this.textTag)					
					this.textTag.className="buttonTextDisabled";
					
					
		this.topNod.onclick=this.dummy;
		this.topNod.onmouseover=this.dummy;
		this.topNod.onmouseout=this.dummy;
		this.topNod.onmousedown=this.dummy;			
		this.topNod.onmouseup=this.dummy;		
		};

		/**  
          *     @desc: inner onclick handler
          *     @type: private
          *     @topic: 2 
          */			
		dhtmlXButtonPrototypeObject.prototype._onclickX=function(e,that){
			if (!that) that=this.objectNode;
			if (that.topNod.dstatus) return;
			if ((!that.persAction)||(that.persAction()))
				if (that.action) { that.action(that.id); }
		};
		/**  
          *     @desc: set innerHTML of button
		  *		@param: htmlText - new text
          *     @type: public
          *     @topic: 4  
          */			
		dhtmlXButtonPrototypeObject.prototype.setHTML=function(htmlText){
			this.topNod.innerHTML=htmlText;
		};
		/**  
          *     @desc: set alt text of button image
          *     @type: public
		  *     @param: imageText - new alt image text
		  *     @topic: 4
          */					
		dhtmlXButtonPrototypeObject.prototype.setAltText=function(imageText){
			this.imageTag.alt=imageText;		
		};		
		/**  
          *     @desc: set image href
          *     @type: public
		  *     @param: imageSrc - new image href		  
		  *     @param: disabledImageSrc - new image href		  		  
		  *     @topic: 4
          */
		dhtmlXButtonPrototypeObject.prototype.setImage=function(imageSrc,disabledImageSrc){
			this.src=imageSrc;		
			if (disabledImageSrc) this.disableImage=disabledImageSrc;
			
			if (this.topNod.onclick==this.dummy)
				{ if (disabledImageSrc) this.imageTag.src=disabledImageSrc;	 }
			else
				this.imageTag.src=imageSrc;		
		};
		
		dhtmlXButtonPrototypeObject.prototype.dummy=function(){};	
		/**  
          *     @desc: return HTML top node
          *     @type: private
		  *     @topic: 4
          */			
		dhtmlXButtonPrototypeObject.prototype.getTopNode=function(){ return this.topNod;  }		
		/**  
          *     @desc: onmouseover handler
          *     @type: private
		  *     @topic: 2
          */				
		dhtmlXButtonPrototypeObject.prototype._onmouseoverY=function() { if (this.topNod.className!=this.className+'Over')  this.topNod.className=this.className+'Over'; return true; };
		/**  
          *     @desc: onmouseout handler
          *     @type: private
		  *     @topic: 2
          */				
		dhtmlXButtonPrototypeObject.prototype._onmouseoutY=function()	 {  this.topNod.className=this.className; return true; };
		/**  
          *     @desc: onmousedown handler
          *     @type: private
		  *     @topic: 2
          */				
		dhtmlXButtonPrototypeObject.prototype._onmousedownX=function() { this.className=this.objectNode.className+'Down'; return true; };
		/**  
          *     @desc: onmouseup handler
          *     @type: private
		  *     @topic: 2
          */				
		dhtmlXButtonPrototypeObject.prototype._onmouseupX=function() { this.className=this.objectNode.className; return true; };		


		
		dhtmlXButtonPrototypeObject.prototype._onmouseoutX=function(e){
			if (!e) e=event; 
			//e.cancelBubble=true;
			if (this.timeoutop) clearTimeout(this.timeoutop);
			this.timeoutop=setTimeout( this.objectNode._delayedTimerCall(this.objectNode,"_onmouseoutY"),100);
		};
		
		dhtmlXButtonPrototypeObject.prototype._onmouseoverX=function(e){
			if (!e) e=event; 
			//e.cancelBubble=true;		
			if (this.timeoutop) clearTimeout(this.timeoutop);
			this.timeoutop=setTimeout( this.objectNode._delayedTimerCall(this.objectNode,"_onmouseoverY"),50);
		};
			
		dhtmlXButtonPrototypeObject.prototype._delayedTimerCall=function(object,functionName,time){
			this.callFunc=function(){
				eval("object."+functionName+"();");
			}
			return this.callFunc;
		}
