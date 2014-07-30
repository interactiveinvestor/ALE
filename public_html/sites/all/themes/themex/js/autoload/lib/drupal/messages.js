(function($){
_Drupal=function(){
	var self=this;
	this.drupalMessages='.messages-holder .message';
	$(document).ready(function(){
		self.panelMessages();
	});
};

_Drupal.prototype={
	
	panelMessages:function(){
		var self=this;
		var init = function(holder){
			var messages=$(holder).children('ul').children('li');
			if($(messages).length>0){
				
				$(messages).each(function(i,e){	
					var krumo=$(e).children('.krumo-root');		
					if($(krumo).length>0){
						$(e).addClass('krumo-message');					
						$(krumo).addClass('hidden-message');
						$(krumo).hide();
						$(e).prepend('<div class="_krumoName messageExcerpt"><a href="JavaScript:void(0)">'+$(e).find('.krumo-name').html()+'</a></div>');
					}else{
						var html=$(e).html();
						$(e).html('');
						$(e).prepend('<div class="messageExcerpt"><a href="JavaScript:void(0)">'+html.replace(/(<([^>]+)>)/ig,"")	.substring(0, 25)+' ...</a></div>');
						$(e).append('<div class="hidden-message">'+html+'</div>');
					}
				});
			}else{
				messages=$(holder);
				var html=$(holder).html();
				$(messages).html('');
				$(messages).prepend('<ul><li><div class="messageExcerpt"><a href="JavaScript:void(0)">'+html.replace(/(<([^>]+)>)/ig,"")	.substring(0, 25)+' ...</a></div><div class="hidden-message">'+html+'</div></li></ul>');
			}
			
		};
		var toggleDisplay = function(holder,hidden,handlers){
			$(hidden).css({
				'visibility':'hidden',
				'display':'none'
			});				
			$(handlers).click(function(e){
				//alert($(hidden).css('display'));
				resetDisplay();
				var _hidden=$(this).parent().find(hidden);
				
				if($(_hidden).css('display')=='none'){
					$(_hidden).css({
						'visibility':'visible',
						'display':'inline-block',
						'opacity':0,
						'left':0,
						//'top':0,
						'bottom':'-100%',
						'z-index':100
					});
					$(e).css({				
						'z-index':100
					});
					$(_hidden).fadeTo(200,1);
				}else{		
					$(_hidden).fadeTo(200,0, function(){
						$(_hidden).css({
							'visibility':'hidden',
							'display':'none',
							'z-index':-1
						});

						$(e).css({'z-index':-1});
					});	
				}	
			});			
			$(holder).children(hidden).click(function(e) {e.stopPropagation();});	
		};
		var resetDisplay=function(){
			
			$(self.drupalMessages+' > ul > li').each(function(i,e){
				var message=$(e).children('.hidden-message');
				//var trigger=$(e).children('.messageExcerpt');
				if($(message).css('display')=='block'){
					
					$(message).hide(1000);
				}
			});
			
		};
		$(self.drupalMessages).each(function(i,e){
			init(e);	
		});
		
		
		toggleDisplay(self.drupalMessages+' > ul > li','.hidden-message','.messageExcerpt');
		$(document).click(function(event) { 
			if($(event.target).parents().index($(self.drupalMessages).parent()) == -1) {	resetDisplay();}        
		});
	}

};
drupal= new _Drupal();
})(jQuery);