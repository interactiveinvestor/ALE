(function($){
		
Popup = function(userOptions){
	var self=this;
	this.gatherOptions(userOptions);
	this.initialise();
};


Popup.prototype={

	initialise:function(){
		var self=this;
		$(this.options.holder+' '+ this.options.content).append("<div class='"+this.options.closeBtnClass+"'></div>");
		$(this.options.holder+' '+ this.options.content).css({'display':'none'});
		this.setListeners();
	},
	gatherOptions:function(userOptions){
		this.defaultOptions={
			holder:'',
			content:'.content',
			speed:400,
			easeIn:'easeOutExpo',
			easeOut:'easeOutQuad',
			handler:'.handler',
			closeBtnClass:'close-btn'
		};
		this.options=_object.gatherOptions(this.defaultOptions,userOptions);	
	},
	resize:function(){},
	setListeners:function(){
		var self=this;
		$(this.options.holder+' '+this.options.handler).click(function(){
			var content=$(this).parent().find(self.options.content);			
			if($(content).css('display')=='none') self.show(content);
			else self.hide(content);
		});
		
		$(this.options.holder + ' .' + this.options.closeBtnClass).click(function(){
			self.hide($(this).parent());
		});		
	},
	show:function(el){
		var self=this;
		$(el).css({display:'block',opacity:0});
		if($.browser.msie) {
		  $(el).animate({opacity:1},self.options.speed,self.options.easeIn);
		 }else{
			$(el).transition({opacity:1},self.options.speed,self.options.easeIn);
		}
		
	},
	hide:function(el){
		// var holder=$(el).parents(this.options.holder);
		// $(holder).removeClass('Popup-open');
		// $(holder).addClass('Popup-closed');
		var self=this;
		if($.browser.msie) {
			$(el).animate({opacity:0},self.options.speed,'linear',function(){
				$(el).css({display:'none'});
			});
		 }else{
			$(el).transition({opacity:0},self.options.speed,'linear',function(){
				$(el).css({display:'none'});
			});
		}
		
	}
};

})(jQuery);