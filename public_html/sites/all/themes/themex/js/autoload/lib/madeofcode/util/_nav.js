(function($){
	
Nav=function(){

	var self=this;
	
	var resizeEndInterval=null;
	$(window).resize(function(){
		clearTimeout(resizeEndInterval);
		resizeEndInterval = setTimeout(function(){self.resizeEnd();}, 300);
		self.resize();
	});
	
};

Nav.prototype={
	setProperties:function(){
	},
	resize:function(){
		var self=this;
	},
	resizeEnd:function(){
		var self=this;				
	},
	mainNav:function(){
		var menuHtml='';
		// $('nav ul ul').each(function(i,e){
		// 	menuHtml+='<ul class="secondary-menu'+i+'">'+$(e).html()+'</ul>';
		// 	$(e).remove();
		// });
		//$('<div class="secondary-menu">'+menuHtml+'</div>').insertAfter('#header');
		
		$('.secondary-menu ul').css({'opacity':0,'display':'block'});
		
		$('nav ul > li').click(function(){
			
			
			
			var expandable=$('.secondary-menu .secondary-menu'+$(this).index());
			if(expandable.length>0){
				$('nav ul > li').removeClass('selected-nav');
				$(this).addClass('selected-nav');
				var wait=0;
				
				$('.secondary-menu ul').each(function(i,e){
					if($(e).css('opacity')!==0){
						wait=1000;
						$(e).css({'z-index':0});
						$(e).animate({
							opacity:0
						},{
							duration:200,
							easing:'easeOutQuad',
							complete:function(){
								//$(this).css({'display':'none'});
							}
						});
					}				
				});
				
				$(expandable).animate({opacity:1},{duration:200,easing:'easeOutQuad'});
				$(expandable).css({'z-index':1});
				
				
			}
		});

	},
	setSelected:function(){
		var _location=window.location.href.toString().split(window.location.host)[1];
		$('nav ul > li').each(function(i,e){
			//var link=.split('#')[0];			
			if(_location.split('#')[0]==$(e).children('a').attr('href')){
				$(this).addClass('selected-nav');
			}
		});
	},
	backToTop:function(){
		var self=this;
		$('body').append('<div title="Back to top" class="back-to-top"></div>');
		
		var backBtn=$('.back-to-top');
		var ghostMenu=$('.ghost-menu');
		
		backBtn.click(function(){
			$("html, body").animate({ scrollTop: 0 }, {duration:1200,easing:'easeOutQuint'});
		});
		
		backBtn.css({'display':'none'});
		ghostMenu.css({'display':'none'});
		var _scroll=function(){
			var scrollTop=$(window).scrollTop();
			
			if(scrollTop > 200 && backBtn.css('display')=='none'){
				backBtn.stop();
				backBtn.css({'display':'block'});
				backBtn.animate({opacity:1},1000);
			}
			
			if(scrollTop < 100 && backBtn.css('display')=='block'){
				backBtn.stop();
				backBtn.animate({opacity:0},{
					duration:1000,
					complete:function(){
						backBtn.css({'display':'none'});
					}
				});
			}
			
			if(scrollTop > 200 && ghostMenu.css('display')=='none'){
				ghostMenu.stop();
				ghostMenu.css({'display':'block'});
				ghostMenu.animate({opacity:1},400);
			}
			
			if(scrollTop < 200 && ghostMenu.css('display')=='block'){
				ghostMenu.stop();
				ghostMenu.animate({opacity:0},{
					duration:400,
					complete:function(){
						ghostMenu.css({'display':'none'});
					}
				});
			}
		};
		
		$(window).scroll(function(){_scroll();});		
		_scroll();
		if($(window).scrollTop()>0){
			$("html, body").animate({ scrollTop: $(window).scrollTop()-50 }, {duration:0});
		}
	},
	topMenu:function(holder){
		
		var mainNav='<ul>'+$(holder+':eq(0)').html()+$(holder+':eq(1)').html()+'</ul>';
		var secondaryNav='<ul class="secondary-ghost">'+$('.secondary-nav').html()+'</ul>';
		var ghostMenu='<div class="ghost-menu"><ul><li><a href="JavaScript:void(0)">Contents</a>'+mainNav+'</li>';
		if($('.secondary-nav').length>0){
			ghostMenu+='<li><a href="JavaScript:void(0)">In this section</a>'+secondaryNav+'</li>';
		}		
		ghostMenu+='</ul></div>';
		
		$('body').append(ghostMenu);
		
		$('.secondary-ghost a,.secondary-nav a,.link-anchor').click(function(){
			setTimeout(function(){
				var scrollTop=$(window).scrollTop();
				
				$("html, body").animate({ scrollTop: scrollTop -40 }, 0);
				
			},50);
			
		});
		
		
	},

};

})(jQuery);