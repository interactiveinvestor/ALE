(function($){
	
var Carousel=function(options){
	var self=this;	
	this.setOptions(options);		
	this.setProperties();		
	
	var endInterval=null;	

	if(this.options.displayed=='dynamic'){		
		this.dynamic=true;		
		$(window).resize(function(){
			clearTimeout(endInterval);
			endInterval = setTimeout(function(){self.resizeEnd();}, 1000);
			self.resize();
		});
	}		
	setTimeout(function(){self.resize();},500);
};
Carousel.prototype={
	setOptions:function(userOptions){
		this.defaultOptions={
			carousel:undefined,
			orientation:'horizontal',
			displayed:'dynamic',
			topHolder:undefined,
			indexItemWidth:30
		};		
		this.options=_object.gatherOptions(this.defaultOptions,userOptions);		
	},
	setProperties:function(){		
			var self=this;		
			this.carousel=$(this.options.carousel);	
			this.initialPosition=0;
			this.counter=0;	
			this.numChildren=this.carousel.children().length;		
			this.interval=false;
			this.resizeRegistry=[];
			this.carousel.wrap('<div class="carousel-holder" />');
			this.carouselHolder=$(this.carousel).parent().parent();
			this.indexHolder=$(this.carousel).parent();
			this.carouselHolder.prepend('<div class="btn-prev carousel-btn"></div>');
			this.carouselHolder.append('<div class="btn-next carousel-btn"></div>');
			this.buttons=[this.carouselHolder.children('.btn-prev'),this.carouselHolder.children('.btn-next')];
			if(self.orientation=='vertical'){

			}
			else{

				var buttonWidth=parseInt($(self.buttons[0]).css('width'),10);
				var buttonMargin=parseInt($(self.buttons[0]).css('margin-left'),10)+parseInt($(self.buttons[0]).css('margin-left'),10);
				self.buttonOffset=(buttonWidth+buttonMargin) * 2;
				self.initialOffset=0;
				self.elementStyle='left';

				var setDynamicProperties=function(){
					self.holderLength=self.options.topHolder.width()-(self.buttonOffset)-1;
					self.carouselLength=self.carousel.children().length * self.options.indexItemWidth;
					self.displayed=Math.floor(self.holderLength/self.options.indexItemWidth);
					var holderLength=self.displayed*self.options.indexItemWidth;
					var holderMargin=Math.floor((self.holderLength-holderLength)/2);
					self.holderLength=holderLength;
					self.offsetLimit=self.carouselLength-self.holderLength;
					self.indexHolder.css({
						'width': self.holderLength,
						'overflow': 'hidden',
						'margin-left':holderMargin,
						'margin-right':holderMargin,
						height:20
					});									
					if(self.carousel.width()<self.holderLength){				
						self.carousel.css({'left': (self.holderLength-self.carousel.width())/2});
					}else{
						self.carousel.css({'left': 0});
					}				
				};
				self.resizeRegistry.push(setDynamicProperties);							
				$(self.carousel).children().css({
					'position':'relative',
					'cursor' : 'pointer',	
					'overflow':'hidden',
					'width':self.options.indexItemWidth,
					'text-align':'center',
					padding:0,
					'float':'left'
				});
				$(self.carousel).css({
					'width': self.carouselLength,
					'position' : 'absolute'
				});				
				setDynamicProperties();
				this.setButtons();
			}	
	},
	prepareMove: function(direction){
		var self=this;
		var offset;
		this.direction=direction;		
		if(this.interval===false){			
			if(this.options.orientation=='vertical'){				
				offset=parseInt(this.carousel.css('top'),10) -this.initialOffset;	
				this.initialPosition= offset;
				this.carousel.css({'top':offset + "px"});
			}else{

				offset=parseInt(this.carousel.css('left'),10) -this.initialOffset;	
				this.initialPosition= offset;
				this.carousel.css({'left':offset + "px"});
			}			
			this.interval =	setInterval(function(){self.move()}, 20);						
		}	
	},	
	move: function(){
		this.buttons[1].removeClass('btn-right-inactive');
		this.buttons[0].removeClass('btn-left-inactive');
		var style=this.elementStyle;		
		this.counter+=1;	
		spot=parseInt(this.carousel.css(style),10);
		var movement_equation= 4 / Math.sqrt(0.005 * this.counter,7);					
		this.direction===0 ? spot+=movement_equation : spot-=movement_equation;		
		this.carousel.css(style, Math.round(spot));		
		var carouselOffset=parseInt(this.carousel.css(style),10);

		if(carouselOffset>=0){
			this.carousel.css(style, 0);
			$(this.buttons[0]).addClass('btn-left-inactive');
			this.stopMove();
		}
		else if(carouselOffset<=-this.offsetLimit){
			this.carousel.css(style, -this.offsetLimit);
			$(this.buttons[1]).addClass('btn-right-inactive');
			this.stopMove();		
		}
		else if(carouselOffset<this.initialPosition-this.holderLength){
			this.carousel.css(style, this.initialPosition - this.holderLength);				
			this.stopMove();		
		}		
		else if(carouselOffset>this.initialPosition +this.holderLength){			
			this.carousel.css(style, this.initialPosition +this.holderLength);		
			this.stopMove();
		}	
	},	
	stopMove: function(){
		clearInterval(this.interval);		
		this.interval=false;
		this.counter=0;
	},	
	unsetButton: function(button){	
		$(button).addClass('btn-right-inactive');		
	},	
	setButton: function(button){
		$(button).removeClass('btn-right-inactive');
	},	
	resize:function(){
		var self=this;
		for(var _function in this.resizeRegistry){
			this.resizeRegistry[_function]();
		}
	},
	resizeEnd:function(){
		this.setButtons();
	},
	setButtons:function(){
		var self=this;
		$(this.buttons[0]).unbind('click');
		$(this.buttons[1]).unbind('click');
		if(this.numChildren>this.displayed){
			$(this.buttons[0]).click(function(){ self.prepareMove(0); });
			$(this.buttons[1]).click(function(){ self.prepareMove(1);});	
			$(this.buttons[0]).removeClass('carousel-left-disabled');
			$(this.buttons[1]).removeClass('carousel-right-disabled');
		}else{
			$(this.buttons[0]).addClass('carousel-left-disabled');
			$(this.buttons[1]).addClass('carousel-right-disabled');
		}
	},	
	preloadImages: function(holder,callback){
		var loadedSize=0;
		var self=this;
		var size=$(holder).size();
		$(holder).each(function(i,element){			
			var image=$(element).children('img');
			$(image).attr('src', $(image).attr('src')).load(function() {  
				loadedSize++;			
				if(loadedSize==size){

					callback.call();	

				}
			});
		});	
	}
};

_Gallery= function(options){
	if(options!==undefined){
		this.setOptions(options);		
		this.setCss();	
		this.setProperties();	
	}
};
_Gallery.prototype={	
	setOptions:function(userOptions){
		var cssId='gallery-controls';
		this.defaultOptions={		
			controls:{
				all:false,
				html:'<div class="_controls" id="'+cssId+'-holder"><div id="'+cssId+'"></div></div>',
				play:false,
				autoplay:true,
				playInterval:5000,
				playHtml:'<div class="play_pause"><a href="JavaScript:void(0)"></a></div>',
				TogglePlayHandler:'.play_pause',
				prevNext:false,
				prevNextHtml:'<div class="prevSlide" title="Previous Slide"><a href="JavaScript:void(0)"></a></div><div title="Next Slide" class="nextSlide"><a href="JavaScript:void(0)"></a></div>',
				nextHandler:'.nextSlide',
				prevHandler:'.prevSlide',
				index:false,
				indexHandlers:'.slideIndex li',
				indexType:'html',
				indexItemHTML:'',
				indexCarousel:false,
				placement:undefined,
				cssId:cssId,
				cssClass:'_controls',
				showControlsOnHover:false,
				swipe:{
					enabled:false,
					responseLimit:50
				}
			},
			animation:{
				direction:'left',
				transition:'fade',
				transitionSpeed:0.9,
				transitionInEasing:'easeOutExpo',
				transitionOutEasing:'easeOutExpo',
				fadeSpeed:1,
				fadeEasing:'linear'
			},
			images:{
				drag:false,
				items:undefined,
				imageClass:'galleryImage',
				imgId:'_slide',
				fitMode:'frame',
				fitCallback:undefined,
				fitAlign:'center',
				captions:false,
				captionsPlacement:undefined
			},
			resize:true,		
			instanceName:'madeofcodeGallery'+ Math.random(1,1000),
			holder:'a',
			css3:true,
			mode:'dynamic',
			styles:undefined		
		};		
		this.options=_object.gatherOptions(this.defaultOptions,userOptions);	
		if(this.options.images.captionsPlacement===undefined){
			this.options.images.captionsPlacement=$(this.options.holder).parent();
		}	
	},
	captions:function(){
		$(this.options.images.captionsPlacement).append('<div class="captions"><p></p></div>');
		this.captionsHolder=$(this.options.images.captionsPlacement).children('.captions');
	},
	_controls:function(){
		var self=this;
		var c=this.options.controls;
		if(c.all===true) c.play=c.prevNext=c.index=true;	
		if(c.play===true||c.prevNext===true||c.index===true){
			if(c.placement!==undefined){
				$(c.placement).append(c.html);				
			}else{
				$(this.holder.dom).parent().append(c.html);				
			}
			
			if(this.options.showControlsOnHover===true){
				$('._controls').css({'opacity':0});

				$(this.holder.dom).parent().mouseenter(function(){
					$('._controls').clearQueue();
					$('._controls').animate({'opacity':1},500);
				});

				$(this.holder.dom).parent().mouseleave(function(){
					$('._controls').clearQueue();
					$('._controls').animate({'opacity':0},500);
				});
			}					
		}
		
		var controls=document.getElementById(c.cssId);		
		if(c.play===true || c.prevNext===true) $(controls).append('<div class="play-prev-next-holder"></div>');	
		if(c.play===true) $('.play-prev-next-holder').append(c.playHtml);		
		if(c.prevNext===true) $('.play-prev-next-holder').append(c.prevNextHtml);
		
		if(c.index===true){
			var indexHtml='<div class="index-holder"><ul class="slideIndex">';
			for (var i=0; i < this.images.length; i++) {
				if(i===0) indexHtml+='<li class="currentIndex">';
				else indexHtml+='<li>';
				if(this.options.controls.indexType==='numeric') indexHtml+=(i+1);
				else  indexHtml+=this.options.controls.indexItemHTML;
				indexHtml+='</li>';
			}
			indexHtml+='</ul></div>';
			$(controls).append(indexHtml);
			if(this.options.controls.indexCarousel===true){
				var carousel=new Carousel({
					carousel:$('.slideIndex'),
					topHolder:$('.node')
				});
			}
			
		}		
	},
	setProperties:function(){
		var options=this.options;
		this.currentIndex=0;
		this.prevIndex=-1;
		this.imagesLoaded=0;	
		this.changing=false;
		this.holder=this.getHolder();
		this.imgId=options.images.imgId;			
		this.images=[];
		this.getImages(this.images);	
		this.adjustHolder();
		//alert('gi');
		if(this.images.length>1) this._controls();
		if(this.options.images.captions===true) this.captions();
		this.counter=0;		
		this.loaded=0;
		this.playInterval=undefined;
		this.dropTimeOut=undefined;
		this.eventsSet=false;
		this.playing=false;
	},
	getHolder:function(holder){
		var dom;	
		if(holder===undefined){
			holder=this.options.holder;		
			if(holder.indexOf('#')===0){			
				dom=document.getElementById(holder.replace(/#/gi,''));
			}		
			else if(holder.indexOf('.')===0) dom=$(holder);
			
			$(dom).parent().css({'display':'block'});	
			holder={
				dom:dom,
				width:$(dom).width(),
				height:$(dom).height(),
				originalWidth:$(dom).width(),
				originalHeight:$(dom).height()
			};

			$(dom).parent().attr('id',this.options.holder.replace(/#/gi,'')+'-holder');
		}
		else if(typeof(holder)=='object'){
			holder.width=$(holder.dom).width();
			holder.height=$(holder.dom).height();		
		}				
		return holder;
	},
	adjustHolder:function(){
	
		var rehide=false;
		if($(this.holder.dom).parent().css('display')=='none'){
			rehide=true;
			$(this.holder.dom).parent().css({'display':'block'});
		}
	
		this.holder.width=$(this.holder.dom).width();	
		this.holder.height=((this.holder.originalHeight*this.holder.width)/this.holder.originalWidth)+30;
		$(this.holder.dom).height(this.holder.height);
	
		if(rehide===true){
			$(this.holder.dom).parent().css({'display':'none'});
		}
	},
	getImages:function(){	
		var self=this;	
		var image,imgDom,i;

		var holder=this.holder.dom;	
		var items=$(holder).children();
		var getMaxDimensions=function(){
			var maxWidth=0;
			var maxHeight=0;
			for(var image in self.images){
				if(self.images[image].width>maxWidth){
					maxWidth=self.images[image].width;
				}				
				if(self.images[image].height>maxHeight){
					maxHeight=self.images[image].height;
				}				
			}		
			self.holder.originalHeight=maxHeight;
			self.holder.originalWidth=maxWidth;			
			setTimeout(function(){			
				self.fitAllImages();
				self.init();
			},500);	
		};
		var preloadImages=function(image){	
			if(image.width!==null){
				$(image.dom).attr('src', image.src).load(function() {
					$(self.holder.dom).parent().css({'display':'block'});
					if($(image.holder).css('display')=='none'){
						$(image.holder).css({'display':'block'});
					}
					image.width=$(image.dom).width();
					image.height=$(image.dom).height();	
					$(self.holder.dom).parent().css({'display':'none'});				
					imageLoaded(image);
					/*$(image.holder).css({'display':'none'});
					for (var i=image.index+1; i < self.images.length; i++) {
						if(self.images[i].loaded===false){
							preloadImages(self.images[i]);
						}				
					}*/
				});
			}	
			
		};
		var imageLoaded =function(image){											
			if(image.index===self.currentIndex) //self.init();					
			image.loaded=true;	
			self.imagesLoaded++;	
			if(self.imagesLoaded==self.images.length && self.options.mode=='dynamic') getMaxDimensions();
		};
		
		if($(items).length===0){						
			//if(this.options.images!==undefined)
			if(this.options.styles!==undefined && typeof(this.options.styles)=='object'){								
				$(this.holder.dom).parent().css({'display':'block'});
				this.style=this.getStyle(this.options.styles,$(this.options.holder).width());
				$(this.holder.dom).parent().css({'display':'none'});			
				for (i=0; i < this.style.images.length; i++) {	
					var imageObj;
					imageObj.src=this.style.images[i];
					$(this.options.holder).append('<li class="'+this.options.images.imageClass+'" id="'+this.options.images.imgId+"-holder"+i+'" ><img  id="'+this.options.images.imgId+i+'" src="" alt=""/></li>');					
					imageObj = new Image();
					imageObj.src=this.style.images[i];				
					imgDom=document.getElementById(this.options.images.imgId+i);				
					image={
						holder:document.getElementById(this.options.images.imgId+"-holder"+i),
						dom:imgDom,
						index:i,
						id:this.imgId+i,
						src:this.style.images[i],
						width:$(imgDom).width(),
						height:$(imgDom).height(),
						top:0,
						left:0,
						loaded:false
					};					
					this.images.push(image);				
				}	
			}
		}
		else{		
			for(i = 0 ; i < items.length ; i++){	
				items[i].id=this.imgId+i;
				imgDom=$(items[i]).find('img');									
				image={
					holder:items[i],
					dom:imgDom,
					index:i,
					id:this.imgId+i,
					src:$(imgDom).attr('src'),
					width:$(imgDom).width(),
					height:$(imgDom).height(),
					top:0,
					left:0,
					loaded:false
				};
				this.images.push(image);
			}
		}		
		for (i=0; i < self.images.length; i++) {					
			if(self.images[i].width>0) imageLoaded(self.images[i]);
			else preloadImages(self.images[i]);
		}
	},
	setCss:function(){		
		$(this.options.holder+' .'+this.options.images.imageClass).css({
			'display':'none',
			'opacity':0,
			'position':'absolute'
		});
	},
	init: function(){
		var self=this;
		this.currentImage=this.images[this.currentIndex];	
		self.changeItem();	
		this.setEventListeners();
		
	},
	setEventListeners:function(){
		var self=this;
		this.eventsSet=true;
		var controls=this.options.controls;
		var images=$(this.holder.dom).find('img');
		if(this.options.mode==='dynamic') this.onResize();
		$(this.options.nextHandlers).click(function(){self.next();});		
		$(images).mousedown(function(e){e.preventDefault();});
		$(images).parent().click(function(e){e.preventDefault();});	
		$(controls.nextHandler).live('click',function(){self.next();});
		$(controls.prevHandler).live('click',function(){self.previous();});		
		$(controls.closeHandler).click(function(){self.close();});		
		$(controls.openHandler).click(function(){self.open();});		
		$(controls.indexHandlers).live('click',function(){self.changeItem($(this).index());});	
		$(controls.TogglePlayHandler).live('click',function(){
			if(self.playing===true) self.pause();
			else self.play();
		});				
		if(controls.autoplay===true) self.play();		
		if(controls.play===true || controls.autoplay===true){		
			$(self.holder.dom).parent().mouseenter(function(){				
				if(self.playInterval!==undefined){
					clearInterval(self.playInterval);
					self.playInterval = undefined;
				}
			});		
			$(self.holder.dom).parent().mouseleave(function(){
				if(self.playing===true) self.play();
			});
		}	

		if(controls.swipe.enabled===true){
			$(images).bind('mousedown touchstart',function(e){
				self.swipe(e,this);
			});
			$(images).bind('touchmove',function(e){
				e.preventDefault();
			});
		}		
	},
	swipe:function(e,el){
		var self=this;
		var options=this.options.controls.swipe;
		var initX=0,initY=0,finalX=0,finalY=0;
		var initCoordinates=self.pointerEventToXY(e);
		initX = initCoordinates.x;
    initY = initCoordinates.y;

		$(el).bind('mouseup touchend',function(e){
			var finalCoordinates=self.pointerEventToXY(e);
			var finalX=finalCoordinates.x;
			var finalY=finalCoordinates.y;			
			var swipeX=finalX-initX;
			var swipeY=initY-finalY;			
			var swiped=swipeY;
			var orientation='vertical';
			var direction='';
			if(Math.abs(swipeX) >=	Math.abs(swipeY)){
				swiped=swipeX;
				orientation='horizontal';
			} 
			if(Math.abs(swiped)>options.responseLimit){			
							
				$(el).parents('a').click(function(e){		
					e.preventDefault();
					$(this).unbind('click');
				});	
				if(self.changing===false){
					if(orientation=='vertical'){
						swiped>0 ? direction='bottom' : direction = 'top';
					}else{
						swiped>0 ? direction='right' : direction = 'left';
					}			
					if(swiped>0) self.previous(false);
					else self.next(false);
					self.transitions('slide','out',self.previousImage,direction);
					self.transitions('slide','in',self.currentImage,direction);
				}				
						
			}			
			$(this).unbind('mouseup touchend');	
		});
		
	},
	pointerEventToXY: function(e){
    var out = {x:0, y:0};
    if(e.type == 'touchstart' || e.type == 'touchmove' || e.type == 'touchend' || e.type == 'touchcancel'){
      var touch = e.originalEvent.touches[0] || e.originalEvent.changedTouches[0];
      out.x = touch.pageX;
      out.y = touch.pageY;
    } else if (e.type == 'mousedown' || e.type == 'mouseup' || e.type == 'mousemove' || e.type == 'mouseover'|| e.type=='mouseout' || e.type=='mouseenter' || e.type=='mouseleave') {
      out.x = e.pageX;
      out.y = e.pageY;
    }
    return out;
  },
	transitions:function(transition,type,item,direction){
		var self=this;
		var options=this.options.animation;
		var transitions=this.transitions;	
		var easing=options.transitionInEasing;
		if(type=='out') easing=options.transitionOutEasing;
		if(direction===undefined) direction=self.options.animation.direction;
		
		item.holder.style.display='block';
		
		transitions.slide=function(){
			
			var value=0;		
			var property='';
			if(direction==='top' || direction==='bottom') property='top';
			else property='left';
			
			if(type=='in'){
				if(direction==='left'){
					item.holder.style.top='0';
					item.holder.style.left='100%';
				} 
				if(direction==='right'){
					item.holder.style.top='0';
					item.holder.style.left='-100%';
				} 
				if(direction==='top'){
					item.holder.style.top='-100%';
					item.holder.style.left='0';
				} 
				if(direction==='bottom'){
					item.holder.style.top='100%';
					item.holder.style.left='0';
				} 
			}
			if(type=='out'){
				if(direction==='left') value='-100%';
				if(direction==='right') value='100%';
				if(direction==='top') value='100%';
				if(direction==='bottom') value='-100%';
			} 
			var animationOptions={};			
			animationOptions[property]=value;		
			if($.browser.msie) {
				$(item.holder).animate(animationOptions, options.transitionSpeed * 1000 , easing );
			}else{
				$(item.holder).transition(animationOptions, options.transitionSpeed * 1000 , easing );
			}
			
		};
		
		transitions.fade=function(){	
			var _opacity;		
			if(type=='in'){		
				$(item.holder).css({opacity:0,display:'block'});
				_opacity=1;
			}
			if(type=='out'){
				_opacity=0;
				setTimeout(function(){
					item.holder.style.display='none';
				}, options.transitionSpeed * 1000 );
			}
			$(item.holder).transition({ opacity:_opacity }, options.transitionSpeed * 1000 , easing );
		};
		
		if(typeof(transitions[transition])==='function') transitions[transition]();
	},
	changeItem: function(index,animate){
		if(animate===undefined) animate=true;
		if(index!==this.currentIndex && this.changing===false){			
			if(this.options.controls.index===true){
				$(this.options.controls.indexHandlers).removeClass('currentIndex');
				var slideIndex=index;
				if(slideIndex===undefined) slideIndex=0;
				$(this.options.controls.indexHandlers).eq(slideIndex).addClass('currentIndex');
			}
			
			var self=this;
			this.changing=true;
			if(index!==undefined){
				this.prevIndex=this.currentIndex;		
				this.previousImage=this.currentImage;
				if(animate===true) this.transitions(this.options.animation.transition,'out',this.previousImage);
				
				$(this.previousImage.holder).removeClass('curSlide');
				
				this.currentIndex=index;
				this.currentImage=this.images[index];		
			}
			$(this.currentImage.holder).addClass('curSlide');	
			if(animate===true) this.transitions(this.options.animation.transition,'in',this.currentImage);						
			if(this.options.images.captions===true){
				var caption=$(this.currentImage.dom).attr('title');			
				if(caption!==''){
					this.captionsHolder.css('display','block');
					this.captionsHolder.children('p').html(caption);
				}else{
					this.captionsHolder.css('display','none');
				}
			}
			setTimeout(function(){
				self.changing=false;
			},self.options.animation.fadeSpeed*1000);
		}
	},
	next:function(animate){
		if(animate===undefined) animate=true;
		var index=this.currentIndex;
		index++;
		if(index>=this.images.length) index=0;
		this.changeItem(index,animate);
	},
	play:function(){		
		var self=this;
		if(this.playInterval===undefined){
			self.playing=true;
			$('.play_pause').addClass('pause');
			this.playInterval=setInterval(function(){			
				self.next();						
			},this.options.controls.playInterval);
		}
	},
	pause:function(){
		var self=this;
		$('.play_pause').removeClass('pause');
		self.playing=false;
		$(self.holder.dom).unbind('mouseleave');
		$(self.holder.dom).unbind('mouseenter');
		self.moving=false;	
		if(this.playInterval!==undefined){
			//alert('hi');
			clearInterval(self.playInterval);
			self.playInterval = undefined;
		}
	},
	previous:function(animate){
		if(animate===undefined) animate=true;
		var index=this.currentIndex;
		index--;
		if(index<0) index=this.images.length-1;
		this.changeItem(index,animate);
	},
	onResize:function(){	
		var self=this;
		var endInterval=null;		
		$(window).resize(function(){			
			clearTimeout(endInterval);
			endInterval = setTimeout(function(){self.onResizeEnd();}, 1000);			
			//self.fitImage(self.holder,self.currentImage,self.options.images.fitMode);		
			self.fitAllImages();	
		});	
	},
	onResizeEnd:function(){
		this.fitAllImages();
	},
	fitAllImages:function(){
		for(var _image in this.images){
			var image= this.images[_image];
			this.fitImage(this.holder,image,this.options.images.fitMode);
		}
	},
	fitImage: function(holder,image,fitMode){
		
		// var =>
		var _this=this.fitImage;
		var fit=_this;
		var self=this,
				_height=0,
				_width=0,
				_marginLeft=0,
				_left=0,
				_marginTop=0,
				_top=0,
				//_holderTop=0,
				//_holderLeft=0,
				_paddingRight=0;
		if(this.options.mode==='dynamic') self.adjustHolder();

		fit.slider=function(self){
			//holder.height=holder.height-550;
			//alert(	holder.height);
			// Horizontal => 
			if(image.width>image.height){
				self.direction='horizontal';		
				if(image.height>holder.height){
					_marginTop=0;
					_height=holder.height;
					_width=Math.round((image.width * _height)/image.height);			
					if(_width<holder.width){
						_width=holder.width;
						_height=Math.round( (image.height * holder.width)/image.width);
						_marginTop= Math.round(( holder.height - _height )/2);	
					}
				}
				else if(image.height<holder.height){
					_marginTop=Math.round( (holder.height-image.height)/2);	
					_height=image.height;
					_width=image.width;		
				}
				if(image.width+image.left < holder.width){
					$(image.holder).css({
						'left':holder.width - _width
					});
				}	

			}
			//end
			// Vertical => 
			else{
				this.direction='vertical';
				if(image.width>holder.width){	
					_marginLeft=0;
					_width=holder.width;
					_height=Math.round((image.height * _width)/image.width);				
					if(_height<holder.height){	
						_height=holder.height;				
						_width=Math.round((image.width * image.height)/_height);
						_marginLeft=Math.round(( holder.width - image.width )/2);		
					}
				}
				else if(image.width<holder.width){
					_marginLeft=Math.round((holder.width-image.width)/2);
					_height=image.height;
					_width=image.width;
				}		
				if(_height<= holder.height){
					//info.update('yes','yes');
					_height=holder.height;
					_width=Math.round((image.width*_height)/image.height);	
					_marginLeft=Math.round((holder.width-_width)/2);
				}
				if(_height-holder.height+_top<0){self.stop();}			
				//info.update('yes',);
			}
			//end
		};
		
		fit.frame=function(self){	
			_height=holder.height;
			_width=(image.width*_height)/image.height;
			if(_width>holder.width){			
				_width=holder.width;
				_height=(image.height*_width)/image.width;			
				_marginTop=(holder.height - _height)/2;				
			}	
			
			if(image.width<_width){
				_width=image.width;
				_height=image.height;
				self.holder.height=self.holder.originalHeight;
				$(self.holder.dom).height(self.holder.originalHeight);
			}
			
		};
		
		fit.crop=function(self){
			var holderHeight=$(holder.dom).parent().height();
			_width=holder.width;
			_height=(image.height*_width)/image.width;	

			if(_height<holderHeight){
				
				_height=holderHeight;
				_width=(image.width*_height)/image.height;
			}
			
			_marginTop=(holderHeight-_height)/2;
		};
		
		if(typeof(fit[fitMode])==='function') fit[fitMode](self);
		
		if(self.options.images.fitAlign=='bottom'){
				_marginTop=holder.height-_height;
		}
		if(self.options.images.fitAlign=='top'){
				_marginTop=0;
		}
		if(self.options.images.fitAlign=='center'){
				_marginLeft=($(holder.dom).width()-_width)/2;
				_marginTop=(holder.height-_height)/2;
		}
		if(typeof(this.options.images.fitCallback)=='function'){
			var properties=this.options.images.fitCallback({_width:_width,_height:_height,_marginTop:_marginTop,_marginLeft:_marginLeft,_top:_top,_left:_left,_paddingRight:_paddingRight},image,holder);
			for(var prop in properties){
				prop=properties[prop];
				//eval(prop+'='+properties[prop]+';');
			}
		}
		
		// Set =>			
		// image.dom.style.height=_height+'px';
		// image.dom.style.width=_width+'px';
		// image.dom.style.marginLeft=_marginLeft+'px';
		// image.dom.style.marginTop=_marginTop+'px';
		// image.dom.style.top=_top+'px';
		// image.dom.style.left=_left+'px';
		// image.dom.style.paddingRight=_paddingRight+'px';
		$(image.dom).css({
			'height':_height,
			'width':_width,
			'margin-left':_marginLeft,
			'margin-top':_marginTop,
			'top':_top ,
			'left':_left,
			'padding-right':_paddingRight 
		});		
		image.currentHeight=_height;
		image.currentWidth=_width;
		image.currentMarginLeft=_marginLeft;
		image.currentLeft= _left;
		image.currentTop= _top;
		image.currentMarginTop=_marginTop;		
		//end
	},
	close:function(){
		var self=this;
		this.fade($(this.options.holder).parent(),{
				duration:self.options.animation.fadeSpeed,
				from:1,
				to:0,
				easing:'linear'
		});
	},
	open:function(){		
		$(this.options.holder).parent().css({
			'opacity':0,
			'display':'block'			
		});					
		this.fade($(this.options.holder).parent(),{
				duration:500,
				from:0,
				to:1
		});	
	},
	setImageProperties:function(image,index){				
		this.imageWidths[index]=image.width();
		this.imageHeights[index]=image.height();					
		$(image).bind('dragstart', function() { return false; });		
		$(image).parent().css({'opacity':0});
		this.dragDrop(image);		
	},
	getStyle:function(styles,holderWidth){
		var i,style;
		//var widthDifference=[];
		//var _style;
		//var loadedImages=0;
		var styleWidths=[];	
		for(style in styles){
			styleWidths.push(styles[style].width);
		}

		var bigger=[];
		var smaller=[];
		//var equal=[];
		var size;	
		for (i=0; i < styleWidths.length; i++) {	
			if(holderWidth-styleWidths[i]===0){
				size=styleWidths[i];
				break;
			}		
			if(holderWidth-styleWidths[i]<0){
				bigger.push(styleWidths[i]);
			}		
			if(holderWidth-styleWidths[i]>0){
				smaller.push(styleWidths[i]);
			}
		}

		if(size===undefined){
			if(bigger.length!==0){
				var closestBigger;
				for (i=0; i < bigger.length; i++) {				
					if(closestBigger!==undefined){
						if(bigger[i]<closestBigger) closestBigger=bigger[i];
					}				
					if(closestBigger===undefined) closestBigger=bigger[i];					
				}
				size=closestBigger;		
			}		
		}

		if(size===undefined){
			if(smaller.length!==0){
				var closestSmaller=null;
				for (i=0; i < smaller.length; i++) {				
					if(closestSmaller!==undefined){
						if(smaller[i]>closestSmaller) closestSmaller=smaller[i];
					}				
					if(closestSmaller===undefined) closestSmaller=smaller[i];									
				}
				size=closestSmaller;		
			}
		}

		for(var _style in styles){
			if(styles[_style].width==size){
				style=styles[_style];
			}
		}
		return style;
	},
	destroy:function(){
		clearInterval(this.playInterval);		
		var self=this;
		$(this.options.nextHandlers).unbind('click');		
		$(this.options.images.imageClass).unbind('mousedown');
		$(this.options.controls.nextHandler).die('click');		
		$(this.options.controls.prevHandler).die('click');		
		$(this.options.controls.closeHandler).unbind('click');
		$(this.options.controls.openHandler).unbind('click');		
		$(this.options.controls.indexHandlers).die('click');	
		$(this.options.controls.TogglePlayHandler).die('click');
		$(self.holder.dom).parent().unbind('mouseenter');
		$(self.holder.dom).parent().unbind('mouseleave');
	}
};
})(jQuery);