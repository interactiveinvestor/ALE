(function($){
	
	$.support.selectstart = "onselectstart" in document.createElement("div");
	$.fn.disableSelection = function() {
	    return this.bind( ( $.support.selectstart ? "selectstart" : "mousedown" ) +
	        ".ui-disableSelection", function( event ) {
	        event.preventDefault();
	    });
	};
	
Scrollbar=function(element,collapsedContent){
	
	if($(element).length==0) return false;
	
	if(collapsedContent===undefined){
		collapsedContent=false;
	}else{
		this.collapsedContent=collapsedContent;
	}
	
	var self=this;
	
	this.holder=$(element);
	this.insertElements();
	this.setProperties();
	self.getProperties();
	this.refresh();
	
	$(self.scrollbar).css({'visibility':'hidden'});
	
	// setTimeout(function(){		
	// 	if($(self.holder).height()<self.contentHeight){
	// 		$(self.scrollbar).css({'visibility':'hidden'});
	// 	}else{
	// 		$(self.scrollbar).css({'visibility':'visible'});
	// 	}
	// },500);
	
	self.setListeners();

	var resizeEndInterval=null;
	$(window).resize(function(){
		clearTimeout(resizeEndInterval);
		resizeEndInterval = setTimeout(function(){self.resizeEnd()}, 1000);
		self.resize();
	});
	
}

Scrollbar.prototype={
	setProperties:function(){
			$(this.holder).css({'overflow':'hidden'});
	},
	getProperties:function(){		
		var self=this;		
		var diferrential=30;		
		
		//$(self.scrollbar).css({'visibility':'hidden'});
		
		if(this.collapsedContent===true){
			this.contentHeight=$(this.holder).children('.scrollable').height();
		}else{
			this.contentHeight=$(this.holder).children('.scrollable')[0].scrollHeight;
		}
		if($(this.holder).height()>this.contentHeight){
			$(this.scrollbar).css({'display':'none'});
		}else{
			$(this.scrollbar).css({'display':'block'});
		}
		
		this.scrollHeight=this.scrollBg.height();		
		this.dragHeightRatio=this.contentHeight/this.scrollHeight;
		this.dragHeight=this.scrollHeight/this.dragHeightRatio;
		$(this.drag).height(this.dragHeight);		
		this.dragScrollableHeight=this.scrollHeight-$(this.drag).height();
		this.contentScrollableHeight=this.contentHeight-$(this.holder).height();		
		//this.i.update('contentHeight',this.contentHeight);
		this.maxY=this.scrollHeight-$(this.drag).height();	
		// var top=-Math.round((parseInt($(this.drag).css('top'),10)*this.contentScrollableHeight)/this.dragScrollableHeight);		
		// 
		var dragTop=(parseInt($(self.scrollable).css('top'),10) * this.dragScrollableHeight)/this.contentScrollableHeight;
		//this.i.update('dragTop',dragTop);
		
		
		if(self.maxY==dragTop){
			dragTop=self.maxY;
			
		}
		
		if(self.maxY==dragTop){
			//$(this.scrollable).css({'top':this.contentScrollableHeight});
		}
		
		$(this.drag).css({'top':Math.abs(dragTop)});
		
		if($(self.holder).height()<self.contentHeight){
			//$(self.scrollbar).css({'visibility':'visible'});
		}
		
		// $(this.scrollable).css({'top':0});
		// $(this.drag).css({'top':0});
		if($(self.holder).height()<self.contentHeight){
				$(self.scrollbar).css({'visibility':'visible'});
			}
	},
	insertElements:function(){
		
		$(this.holder).wrapInner('<div class="scrollable" />');
		this.scrollable=$(this.holder).children('.scrollable');
		
		//$(this.scrollable).css({'overflow':'hidden'});
		
		$(this.holder).append('<div class="scroll-bar"><div class="scroll-bg"><div class="scroll-drag"></div></div></div>');
		
		this.scrollbar=$(this.holder).find('.scroll-bar');
		this.scrollBg=$(this.holder).find('.scroll-bg');
		this.drag=$(this.holder).find('.scroll-drag');
	},
	resize:function(){
		this.getProperties();
	},
	resizeEnd:function(){
		
	},
	refresh:function(){
		
		var self=this;
		setTimeout(function(){
			clearInterval(self.refreshInterval);
		},8000);
		
		
		this.refreshInterval=setInterval(function(){
			self.getProperties();
			
		},2000);
		
	},
	setListeners:function(){
		
		var self=this;
		
		$(this.drag).mousedown(function(e){
			

			
			var offsetX = e.pageX - this.offsetLeft;
			var offsetY = e.pageY - this.offsetTop;
			$(self.holder).disableSelection();
			
			$(window).bind('mousemove',function(e){			
				var y=e.pageY-offsetY;
				if(y<0) y=0;			
				if(y>self.maxY) y=self.maxY;
				$(self.drag).css({'top':y});		
				self.contentTop=-Math.round((y*self.contentScrollableHeight)/self.dragScrollableHeight);		
				$(self.scrollable).css({'top':self.contentTop});

			});	
		});
		
		
		$(window).mouseup(function(e){
			$(window).unbind('mousemove');
			$(self.holder).enableSelection();
		});
		
		var initY=null;
		var initContentY=null;
		
		$(this.holder).bind('mousewheel', function(e, delta) {
			
			if($(self.scrollbar).css('display')=='none'){
				return false;
			}
			
			var offsetY = e.pageY ;
			if(initY===null){
				initY=offsetY;
				initContentY=parseInt($(self.scrollable).css('top'),10);
			} 
			var distance=initY-offsetY;			
			var _top=initContentY+distance;	
			
							
			_top=Math.round(parseInt($(self.scrollable).css('top'),10)+(delta*15));
			
			// if(_top>0 && $(self.scrollable).css('top')==0 && delta > 0 ){
			// 	return false;
			// }
			
			//self.i.update('delta',delta);
			
			if(_top>0){_top=0};
			if(_top<-self.contentScrollableHeight){_top=-self.contentScrollableHeight};
			
			$(self.scrollable).css({'top':_top});		
			self.dragTop=-Math.round((_top*self.dragScrollableHeight)/self.contentScrollableHeight);		
			$(self.drag).css({'top':self.dragTop});
			
			
				clearTimeout($.data(this, 'timer'));
			  $.data(this, 'timer', setTimeout(function() {
			     initY=null;
			  }, 250));
			
			// $('#contentBox').css('top', parseInt($('#contentBox').css('top')) + (delta > 0 ? 40 : -40));
			// return false;
		});
	}

	
}

})(jQuery);