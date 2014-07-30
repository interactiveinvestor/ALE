(function($){
		
Accordion = function(userOptions){
	var self=this;
	//if($(userOptions.holder).length==0) return false;
	this.gatherOptions(userOptions);
	

	this.initialise();
	var accordion=this.accordion;
	var collapsables=this.collapsables;
		
	$(accordion).find(this.options.handler).live('click',function(){

		if(userOptions.handler_function!==undefined){
			userOptions.handler_function(self);
		}
		var index=$(this).next().index(accordion+' '+collapsables);				
		if(parseInt($(this).next(collapsables).css('height'),10)>2){						
			if(self.options.closeIfOpen===true){
				self.collapseElement($(this),self.options.speed,self.options.easeIn);
			}						
		}else{				
			if(self.options.collapseOnExpand===true){			
				$(this).parent().parent().find('.expanded').each(function(i,e){
					self.collapseElement($(e),self.options.speed,self.options.easeOut);
				});
			}	
			self.expandElement($(this),self.options.speed,self.options.easeOut,index);
		}	
	});
};


Accordion.prototype={

	initialise:function(){
	
		var self=this;
		var resizeEndInterval=null;
		this.w_width=$(window).width();
		$(window).resize(function(){
			clearTimeout(resizeEndInterval);
			resizeEndInterval = setTimeout(function(){
				if(self.w_width!==$(window).width()) self.resizeEnd();
				self.w_width=$(window).width();
			}, 1000);
			self.resize();
		});
				
		this.scroll=this.options.scroll;
		this.collapsables=this.options.collapsables;
		this.accordion=this.options.holder;
		this.dimensions=[];
		this.currentIndex=null;
		
		setTimeout(function(){self.getProperties();},500);
		
		var accordion=this.accordion;
		var collapsables=this.collapsables;

		$(accordion).addClass('accordion');
		$(accordion).find(collapsables).css({'visibility':'hidden','height':0});
		$(accordion + ' > * >'+this.options.handler).addClass('accordion-handler');
		$(accordion + ' > '+this.options.handler).addClass('accordion-handler');
		
	},
	gatherOptions:function(userOptions){
		this.defaultOptions={
			holder:'',
			collapsables:'',
			scroll:undefined,
			collapseOnExpand:false,
			speed:500,
			easeIn:'easeInSine',
			easeOut:'easeOutSine',
			closeIfOpen:true,
			handler:'a',
			handler_function:null
		};
		

		this.options=_object.gatherOptions(this.defaultOptions,userOptions);	
	},
	resize:function(){},
	resizeEnd:function(){		
		var self=this;		
		var toShow=[];		
		$(self.accordion).find(self.collapsables).each(function(i,e){			
			if($(e).height()!==0 && $(e)[0].style.height!=='' ){
				toShow.push(i);
			}
		});			
		this.getProperties();		
		$(self.accordion).find(self.collapsables).each(function(i,e){
			if($.inArray( i, toShow )!==-1){
				$(e).css({
					'visibility':'visible',
					height:'auto'
				});
			}
		});

		if(!$(this.options.holder).hasClass('accordion')){	
			this.initialise();
		}
		
	},
	getProperties:function(){
		
		var self=this;
		self.dimensions=[];
		$(self.accordion).find(self.collapsables).css({
			'display':'block',
			'visibility':'visible',
			'height':'auto'				
		});
		$(self.accordion).find(self.collapsables).each(function(i,e){
			$(e).find('ul').css({'display':'none'});
			self.dimensions.push($(e).outerHeight());		
			$(e).find('ul').css({'display':'block'});
		});
		
		$(self.accordion).find(self.collapsables).css({
			'visibility':'hidden',
			'height':0				
		});

	},
	setListeners:function(){
		
	},
	collapseElement:function(el,speed,_easing){
		var self=this;
		

		el.parent().find('.icon').html('&#9658;');
		el.next(self.collapsables).animate(
			{
				height:0
			},
			{
				duration:speed,
				easing:_easing,
				complete:function(){
					$('body').height($('body').height());
					el.removeClass('expanded');
					$(el).next().removeClass('ac-expanded');
					el.parent().find('.expanded').removeClass('expanded');
					$(el).parent().find('.expanded').next().removeClass('expanded');
					if(self.scroll!==undefined){
						//alert(self.scroll);					
						self.scroll.getProperties();
					}
					$(this).css({'visibility':'hidden'});						
					$(this).find(self.collapsables).css({
						'visibility':'hidden',
						'height':0
					});
				}
			}
		);
	},
	expandElement:function(el,speed,_easing,index){
		var self=this;
		el.addClass('expanded');
		$(el).next().addClass('ac-expanded');			
		el.children('.icon').html('&#9660');			
		el.next(self.collapsables).css({'visibility':'visible'});
        
		el.next(self.collapsables).animate(
			{
				height:self.dimensions[index]
			},
			{
				step:function(){
					
				},
				duration:speed,
				easing:_easing,
				complete:function(){
					$('body').height($('body').height());
					if(self.scroll!==undefined){
						self.scroll.getProperties();
					}
					$(this).css({'height':'auto'});
				}
			}
		);
	}
};

})(jQuery);