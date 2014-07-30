(function($){	
		
var css3,www,_System;
$(document).ready(function(){
	_System=new System();
	www=new www();			
});

www=function(){
	var self=this;
	this.initialise();
};

www.prototype={
	initialise:function(){
		this.isIpad=navigator.userAgent.match(/iPad/i) != null;
		if(this.isIpad) $('body').addClass('ipad-site');
		
		this.resizeFunctions=[];
		this.resizeEndFunctions=[];
		this.scrollEndFunctions=[];
		var self=this;
		this.setProperties();
		//this.i=info=new _InfoBox();	
		var resizeEndInterval=null;
		$(window).resize(function(){
			clearTimeout(resizeEndInterval);
			resizeEndInterval = setTimeout(function(){self.resizeEnd();}, 500);
			self.resize();
		});
		
		var scrollEndInterval=null;
		$(window).scroll(function(){
			clearTimeout(scrollEndInterval);
			scrollEndInterval = setTimeout(function(){self.scrollEnd();}, 500);
		});
		
		this.pdfView=false;
		
		
		var url=window.location.href.toString();
		if(url.indexOf('pdf-view=true')!==-1) this.pdfView=true;
		
		if(this.pdfView===false){	
			var accordionOptions={
				holder:'.accordion',
				collapsables:'ul',
				speed:500,
				easeIn:'easeInSine',
				easeOut:'easeOutSine',
				handler:'a'
			}		
			if(typeof(google)!="undefined" ){
				accordionOptions.handler_function=function(self){				
					setTimeout(function(){
						google.maps.event.trigger(googlemap, 'resize',function(){
								googlemap.fitBounds(googlemap.getBounds());
						});
					},self.options.speed,self.options.easeIn);		
				}
			}
			var accordion=new Accordion(accordionOptions);			
		}
		this.adminMenu();
		this.resize();
		this.scrollDown();
		this.browserFixes();
		this.resizeEnd();
		this.mobile();		
		this.highlights();
		this.mobileHighlights();
		this.advancedSearch();
		
		var popup=new Popup({holder:'.popup'});
		
		//this.desktop();
		// this.backToTop();
	},
	advancedSearch:function(){
		if($('.property-search').length > 0){
			$('.search-states').append('<li><input type="checkbox" id="all-states" value="western_australia"><label for="all-states">All</label></li>');
			
			$('#all-states').change(function(){
				if($(this).attr('checked')===true){
					$(this).parent().parent().find('input').attr('checked',true);				
				}			
			});
						
			$('.search-states input').change(function(){
				if($(this).attr('checked')===false && $('.search-states #all-states').attr('checked')===true){
					$('.search-states #all-states').attr('checked',false);
				}
			});
			// if($('.property-search-results').find('table').length==0){
			// 	$('.page-sub-menu ul').css('display','block');
			// }
		}
	},
	highlights:function(){		
		var self=this;		
		if($('.page-holder .highlights').length > 0){			
			var html='<ul class="highlight-hovers">';			
			$('.page-holder .highlights li').each(function(i,e){
				var group=$(e).find('.group-body-holder');
				//group.remove();
				html+='<li>'+$(group).html()+'</li>';
			});		
			html+='</ul>';
			$('.highlights-note').after(html);
		}

		$('.desktop-highlights .hilight li').live('mouseover',function(){
			if(self.isIpad){
				$(this).trigger('click');
			}
			$(this).find('.group-body-holder').css({'display':'none'});
		});
		
		$('.desktop-highlights .hilight li').click(function(){
			close();
			open($(this).index());
			$(this).addClass('hilight-active');
		});
		function open(index){
			var element=$('.highlight-hovers li').eq(index);	
			$(element).css({position:'absolute'});	
			element.css({
				display:'block',
				opacity:0,
				//position:'absolute'
				position:'relative'
			});
			self.resizeEnd();
			if($.browser.msie) {
				element.animate({
					opacity:1
				},1000,'easeOutQuart');
			 }else{
				element.transition({
					opacity:1
				},1000,'easeOutQuart');
			}
			
		}
		
		function close(){
			$('.highlight-hovers li').each(function(i,e){
				if($(e).css('display')=='block'){				
					$('.hilight li').eq(i).removeClass('hilight-active');
					$(e).css({
						position:'absolute',
						'top':0
					});	
					
					if($.browser.msie) {
						$(e).animate({
							opacity:0
						},1000,'easeOutQuart');
					 }else{
						$(e).transition({
							opacity:0
						},1000,'easeOutQuart');
					}
					
					
					setTimeout(function(){
						$(e).css({display:'none'});
					},1000);
				}
			});
		}
		
	},
	mobileHighlights:function(){
		
		$('.mobile-highlights .hilight li').live('click',function(){
			var element=$(this).find('.group-body-holder');
			if($(element).css('display')=='block'){
				close(element);
			}else{
				open(element);
			}
		});

		function open(element){
			
			$('.mobile-highlights .group-body-holder').each(function(i,e){
				if($(e).css('display')=='block'){
					close(e);
				}
			});
			
			element.css({
				display:'block',
				opacity:0
			});
			
			if($.browser.msie) {
			    element.animate({
						opacity:1
					},1000,'easeOutQuart');
			 }else{
				element.transition({
					opacity:1
				},1000,'easeOutQuart');
			}
			
		}

		function close(element){
			if($.browser.msie){
			    $(element).animate({
						opacity:0
					},1000,'easeOutQuart');
			 }else{
				$(element).transition({
					opacity:0
				},1000,'easeOutQuart');
			}
			setTimeout(function(){
				$(element).css({display:'none'});
			},1000);

		}		
	
	},
	setProperties:function(){		
		var self=this;		
		this.isiPad = navigator.userAgent.match(/iPad/i) != null;				
	},	
	adminMenu:function(){		
		var elements=['#wrapper','.page-holder > .page-title','.interactive-map-holder'];
		var tops=[];
		for (var i=0; i < elements.length; i++) {
			if(elements[i]=='.page-holder > .page-title') tops.push(15);
			else tops.push(parseInt($(elements[i]).css('padding-top'),10));
		}
		if($('body').hasClass('admin-menu')){		
			this.resizeFunctions.push(function(){
				var menuHeight=$('#admin-menu').height();	
				for (var i=0; i < elements.length; i++) {
					$(elements[i]).css('padding-top',menuHeight+tops[i]+1);
				}
			});
		}		
	},
	mobile:function(){	
		$('.mobile-site .menu-property-information a').click(function(e){
			if($('.menu-property-information .ac-expanded').length>0){
				window.location=$('.menu-property-information > a').attr('href');
			}		
			e.preventDefault();
		});
		this.mobileAccordion=new Accordion({
			holder:'.nav-mobile',
			collapsables:'ul',
			speed:500,
			easeIn:'easeInSine',
			easeOut:'easeOutSine',
			handler:'a'
		});	
		this.searchAccordion=new Accordion({
			holder:'.mobile-page-sub-menu',
			collapsables:'ul',
			speed:500,
			easeIn:'easeInExpo',
			easeOut:'easeOutExpo',
			handler:'label',
			collapseOnExpand:true
		});
		this.dcAccordion=new Accordion({
			holder:'.mobile-tabs',
			collapsables:'ul',
			speed:500,
			easeIn:'easeInExpo',
			easeOut:'easeOutExpo',
			handler:'handler',
			collapseOnExpand:true
		});
	},
	backToTop:function(){
		var self=this;
		$('body').append('<div title="Back to top" class="back-to-top"></div>');
		
		var backBtn=$('.back-to-top');
		
		backBtn.click(function(){
			$("html, body").animate({ scrollTop: 0 }, {duration:1200,easing:'easeOutQuint'});
		});
		
		backBtn.css({'display':'none'});
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
		};
		
		$(window).scroll(function(){_scroll();});		
		_scroll();
		if($(window).scrollTop()>0){
			$("html, body").animate({ scrollTop: $(window).scrollTop()-50 }, {duration:0});
		}
	},
	scrollDown:function(){
		$('body').append('<a href="JavaScript:void(0)" title="Scroll Down" class="scroll-down"></a>');
		$('body').append('<a href="JavaScript:void(0)" title="Scroll to Top" class="scroll-top"></a>');
		var topButton=$('.scroll-top');
		$(topButton).css('display','none');
		var button=$('.scroll-down');
		
		$(button).click(function(){
			var scrolled=$(window).scrollTop();
			$('html,body').animate({
				scrollTop:scrolled+$(window).height()
			},{
				duration:1000,
				easing:'easeOutExpo',
				complete:function(){
					scrollEnd();
				}
			});
		});
		
		$(topButton).click(function(){
			var scrolled=$(window).scrollTop();
			$('html,body').animate({
				scrollTop:0
			},{
				duration:1000,
				easing:'easeOutExpo'
			});
		});
		
		
		var scrollEnd=function(){
			if($(document).height()-$(window).scrollTop()==$(window).height()){
				$(button).animate({
					opacity:0
				},{
					duration:500,
					easing:'easeOutQuint',
					complete:function(){
						$(this).css('display','none');
					}
				});	
				$(topButton).css({'display':'block','opacity':0});
				$(topButton).animate({
					opacity:1
				},{
					duration:500,
					easing:'easeOutQuint'
				});
							
			}
			else{
				if($(button).css('display')=='none'){
					$(button).css('display','block');
					$(button).animate({
						opacity:1
					},{
						duration:500,
						easing:'easeOutQuint'
					});
				}	
				
				if($(topButton).css('display')=='block'){
					$(topButton).animate({
						opacity:0
					},{
						duration:500,
						easing:'easeOutQuint',
						complete:function(){
							$(this).css('display','none');
						}
					});
				}
							
			} 			
		}
		this.scrollEndFunctions.push(scrollEnd);
		
		var fixVisibility=function(){
			if($(document).height() <= $(window).height()) $(button).css('display','none');
			else{
				$(button).css('display','block');			
			} 
		}	
		this.resizeEndFunctions.push(fixVisibility);
	},
	scrollEnd:function(){
		var self=this;		
		for(var _function in this.scrollEndFunctions){
			this.scrollEndFunctions[_function]();
		}
	},
	resize:function(){
		var self=this;		
		for(var _function in this.resizeFunctions){
			this.resizeFunctions[_function]();
		}	
		
		if($('body').width()<760 && $('body').hasClass('desktop-site')){
			$('body').addClass('mobile-site').removeClass('desktop-site');
		}
		
		if($('body').width()>=760 && $('body').hasClass('mobile-site')){
			$('body').removeClass('mobile-site').addClass('desktop-site');
		}
		
	
		
	},
	resizeEnd:function(){
		var self=this;		
		
		var self=this;		
		for(var _function in this.resizeEndFunctions){
			this.resizeEndFunctions[_function]();
		}
				
		if($('body').width()<760){
			$('.download-centre .tabs').addClass('mobile-tabs');
			$('.page-sub-menu').addClass('mobile-page-sub-menu');
			$('.highlights').addClass('mobile-highlights').removeClass('desktop-highlights');		
			$('.highlight-hovers').css({'display':'none'});			
			$('nav').addClass('nav-mobile').removeClass('nav-desktop');
			
		}
		else{
			$('.download-centre .tabs').removeClass('mobile-tabs');
			$('.page-sub-menu').removeClass('mobile-page-sub-menu');
			$('.highlights').removeClass('mobile-highlights').addClass('desktop-highlights');
			$('nav').removeClass('nav-mobile').addClass('nav-desktop');
			$('.highlight-hovers').css({'display':'block'});
		}
		
		
	},
	cropBg:function(){
		var holderHeight=this.bgHolder.height();
		_width=this.bgHolder.width();
		_height=(this.bgImageHeight*_width)/this.bgImageWidth;	
		if(_height<holderHeight){		
			_height=holderHeight;
			_width=(this.bgImageWidth*_height)/this.bgImageHeight;
		}	
		_marginTop=(holderHeight-_height)/2;
		_marginLeft=(this.bgHolder.width()-_width)/2;	

		$('#bg-holder img').css({
			'height':_height,
			'width':_width,
			'margin-top':_marginTop,
			'left':	_marginLeft
		});
	},
	ajaxLoadNode:function(){
		var ajaxHtml='<div class="preloader"></div>';		
		$('body').append(ajaxHtml);	
		var ajaxHodler=$('#page');
		var preloader=$('.preloader');
		var post=$('.page-holder');		
		var currentPage='';
		var homeImage=$('#home-bg');
		var isLanding=true;

		$(window).hashchange(function(){
			if(isLanding===true) findLandingPage();		
		});

		var loadPage=function(obj){	
			var href='';
			var action = $(obj).attr('action');	
			if(action===undefined){
				typeof(obj)==='string' ? href='/'+obj: href=$(obj).attr('href');
				if(href.indexOf('ajax=true')===-1) href+='?ajax=true';
				href=fixUrl(href);
			}else{
				var searchValue=$(obj).find('.form-text').attr('value');
				
				href=BaseUrl+'/search/'+searchValue+'?ajax=true';		
			}		
			currentPage=href.replace('?ajax=true','');
			href=$.trim(href);
			$("html, body").animate({ scrollTop: 0 }, {duration:500,easing:'easeOutQuint'});
			
			$.ajax({
				cache:false,
				dataType:'text',
				type: 'POST',
				url: href,
				beforeSend:function(){
					// this is where we append a loading image
				},
				success:function(data){
					
					
					$('nav li,.top-menu ul li').removeClass('nav-selected');

					$(obj).parent().find('a').each(function(i,e){
						if(currentPage==$(e).attr('href')){
							$(e).parents('li').addClass('nav-selected');		
						}							
					});

					var hash=currentPage.replace(BaseUrl+'/','');
					window.location.hash=hash;
					
					_gaq.push(['_trackPageview', currentPage]);
					


					setTimeout(function(){
						if(homeImage.css('opacity')!==0){
							homeImage.animate({opacity:0},500);
							$('#footer').addClass('footer-not-home');
						}
						preloader.animate({opacity:0},{
							duration:1000,
							easing:'linear',
							complete:function(){}
						});					
						post.css({'visibility':'hidden'});
						post.html(data);
						post.css({'visibility':'visible',opacity:0});	
						post.animate({opacity:1},{duration:700,easing:'easeInQuint'});
					},0);

				},
				error:function(){

					// failed request; give feedback to user
					//alert('There was a problem loading the page. Please try again later.');
					window.location=href.replace('?ajax=true','');
				}	

			});
		};
		var preloadPage=function(e,_this){
			isLanding=false;					
			e.preventDefault();
			var obj=_this;
			
			if(currentPage==$(obj).attr('href') || $(obj).attr('href')=='JavaScript:void(0)'){
				return false;
			}	

			if(ajaxHodler.css('display')=='block'){			
				preloader.css({'display':'block',opacity:1});			
				post.animate({opacity:0},{
					duration:1000,
					easing:'easeOutQuint',
					complete:function(){
						loadPage(obj);
					}
				});
			}else{
				preloader.css({'display':'block',opacity:1});
				post.html('');
				ajaxHodler.css({display:'block',opacity:0});			
				ajaxHodler.animate({opacity:1},{duration:400,easing:'easeInQuint'});
				loadPage(_this);
			}
		};
		var findLandingPage=function(){
			var hash=window.location.hash.replace('#','');
			if(hash!==''){
				$('nav a').each(function(i,e){
					var page=$(e).attr('href');
					if(page!==undefined){
						page=page.replace(BaseUrl+'/','');
						if(page==hash){
							loadPage($(e));
							return false;
						}
					}
				});
				
				if(hash.indexOf('search/')!==-1){
					loadPage(hash);
				}
			}
		};
		var fixUrl=function(url){
			
			var baseUrlPaths=BaseUrl.replace('http://','').split('/');
			var add='';
			for(var path in baseUrlPaths){			
				if(url.indexOf(baseUrlPaths[path])===-1){
					if(add==='') add+='http:/';
					add+='/'+baseUrlPaths[path];
				}
			}		
			return add+url;
		};
		
		findLandingPage();
		$('nav a, .top-menu  ul ul a, .pager-item a,.search-result a').live('click',function(e){preloadPage(e,this);});
		$('.search,#search-api-page-search-form').live('submit',function(e){
			if($(this).find('.form-text').attr('value')==''){
				return false;
			}
			preloadPage(e,this);
		});
	},
	browserFixes:function(){
		if ($.browser.msie && $.browser.version.substr(0,1) == 8){
			$('a').each(function(i,e){
				if($(e).attr('target')=='_blank'){
					$(e).attr('target','');
				}
			});
		}
	}
};



})(jQuery);