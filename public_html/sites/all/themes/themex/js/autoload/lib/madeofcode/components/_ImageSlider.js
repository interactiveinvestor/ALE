(function($){

ImageSlider= function(options){
	
	this.name='ImageSlider';
	this.setOptions(options);		
	this.setCss();
	this.setProperties();
};
ImageSlider.prototype={	
	setOptions:function(userOptions){
			var cssId='controls_holder';
		this.defaultOptions={
		
			controls:{
				all:false,
				html:'<div class="'+cssId+'-holder"><div id="'+cssId+'"></div></div>',
				play:false,
				autoplay:true,
				playHtml:'<div class="play_pause"><a href="JavaScript:void(0)"></a></div>',
				TogglePlayHandler:'.play_pause',
				prevNext:false,
				prevNextHtml:'<div class="prevSlide" title="Previous Slide"><a href="JavaScript:void(0)"></a></div><div title="Next Slide" class="nextSlide"><a href="JavaScript:void(0)"></a></div>',
				nextHandler:'.nextSlide',
				prevHandler:'.prevSlide',
				index:false,
				indexHandlers:'.slideIndex li',
				placement:undefined,
				cssId:cssId			
			},
			instanceName:'madeofcodeGallery'+ Math.random(1,1000),
			holder:'a',
			items:undefined,
			delta:'d',
			css3:true,
			imageClass:'sliderImage',
			children:'s',
			dimOpacity:0.6,
			dimSpeed:400,
			imgId:'_slide',
			holderId:'_slideHolder',
			holderClass:'slideHolder',
			imagesSrc:undefined,
			mode:'dynamic',
			fadeSpeed:0.6,
			fadeEasing:'linear',
			moveDelay:2,
			moveSpeed:1.7,
			direction:'horizontal',
			restartInterval:4,
			restartDelay:2,
			minSpeed:2,
			speedLimit:80,
			styles:undefined,
			titles:undefined
		};		
		this.options=_object.gatherOptions(this.defaultOptions,userOptions);		
	},
	gatherOptions:function(_default,_options){
		var options={};
		for(var opt in _default){			
			if(typeof(_default[opt])==='object'){
				var obj=_default[opt];			
				options[opt]=obj;				
				for(var prop in obj){		
					
					if(_options[opt]!==undefined){
						if(_options[opt][prop]!==undefined){					
							options[opt][prop]=_options[opt][prop];					
						}else{
							options[opt][prop]=_default[opt][prop];
						}
					}else{
						//alert(opt);
					}			
				
				}	
			}else{
				if(_options[opt]===undefined) options[opt]=_default[opt];
				else options[opt]=_options[opt];			
			}		
		}
		return options;
	},
	_controls:function(){
		var self=this;
		var c=this.options.controls;

		if(c.all===true){
			c.play=true;
			c.prevNext=true;
			c.index=true;
		}
		if(c.play===true||c.prevNext===true||c.index===true){
			if(c.placement!==undefined){
				$(c.placement).append(c.html);
							
			}else{
				$(this.holder.dom).parent().append(c.html);				
			}
		}
		var controls=document.getElementById(c.cssId);
		if(c.play===true){
			$(controls).append(c.playHtml);
			if(c.play===true && self.options.controls.autoplay===true){
				$('.play_pause').addClass('pause');
			}
		}	
		if(c.prevNext===true) $(controls).append(c.prevNextHtml);	
		if(c.index===true){
			var indexHtml='<ul class="slideIndex">';
			for (var i=0; i < this.images.length; i++) {
				indexHtml+='<li>'+(i+1)+'</li>';
			}
			indexHtml+='</ul>';
			$(controls).append(indexHtml);
		}		
	},
	setProperties:function(){
		//this.info=new InfoBox();
		var options=this.options;
		this.dropping=false;
		this.currentIndex=0;
		this.prevIndex=-1;
		this.imagesLoaded=0;	
		//return false;
		this.holder=this.getHolder();
		
		//this.css3;
		if(this.options.css3===true){
			this.css3=new CSS3();
		}
		if(this.css3.support!==undefined){
			this.ccs3Support=this.css3.support;	
		}else{
			this.ccs3Support=false;
		}
		
		this.imgId=options.imgId;
		
		this.images=[];
		this.getImages(this.images);		
		this._controls();
		this.counter=0;		
		this.loaded=0;
		this.interval=undefined;
		this.dropTimeOut=undefined;
		this.eventsSet=false;

	},
	getHolder:function(holder){
		var dom;	
		if(holder===undefined){
			holder=this.options.holder;		
			if(holder.indexOf('#')===0){
				dom=document.getElementById(this.options.holder.replace(/#/gi,''));
			}		
			else if(holder.indexOf('.')===0){
				dom=$(dom);
			}
			holder={
				dom:dom,
				width:$(dom).width(),
				height:$(dom).height()
			};
			return holder;
		}
		else if(typeof(holder)=='object'){
			holder.width=$(holder.dom).width();
			holder.height=$(holder.dom).height();
			return holder;
		}		
	},
	getImages:function(){
		
		var self=this;	
		var holder=this.holder.dom;	
		var items=$(holder).children();
		var i = 0;
		var preloadImages=function(image){
			
			$(image.dom).load(function() {
				
				image.width=$(image.dom).width();
				
				image.height=$(image.dom).height();								
				imageLoaded(image);				
				for (var i=image.index+1; i < self.images.length; i++) {				
					if(self.images[i].loaded===false){					
						preloadImages(self.images[i]);
						break;
					}				
				}
			});
			
			$(image.dom).attr('src', image.src);
		};
		imageLoaded =function(image){
			
			$(image.dom).parent().css({'display':'block'});
			image.width=$(image.dom).width();
			image.height=$(image.dom).height();
			
			
			if(image.index===self.currentIndex){
				self.fitImage(self.holder,image);
				//alert('hi');
				self.init();
				
				
				//$(image.holder).css({opacity:0});					
			}else{
				
			}
			//$(image.holder).css({opacity:0,'display':'none'});
			image.loaded=true;	
			self.imagesLoaded++;
			$(image.dom).parent().css({'display':'none',opacity:0});
			//self.info.update('loaded',self.imagesLoaded);
		};
		
		var imgDom;
		
		if(this.options.styles!==undefined){
			
			//var _height=window.screen.availHeight;
			var _width=window.screen.availWidth;
			this.style=this.getStyle(this.options.styles,_width*2,'width');
			var images=this.style.images;
			//alert(this.holder.dom);
			for(i = 0 ; i < images.length ; i++){	
				
				$(this.holder.dom).append('<li id="'+self.options.holderId+i+'"><img id="'+self.options.imgId+i+'" class="'+self.options.imageClass+'" src="" alt=""/><span class="bg-left"></span><span class="bg-right"></span></li>');
				images[i].id=self.options.imgId+i;
				imgDom=document.getElementById(self.options.imgId+i);	
				

				
				//alert($(imgDom).parent().css('opacity'));
				//alert($(imgDom).width());
				var image={
					holder:document.getElementById(self.options.holderId+i),
					dom:imgDom,
					index:i,
					id:self.options.imgId+i,
					src:images[i],
					//width:$(imgDom).width(),
					//height:$(imgDom).height(),
					top:0,
					left:0,
					loaded:false
				};
				//alert(imgDom);

				this.images.push(image);
				
			}
			//alert(self.options.titles.length);
			for (i=0; i < this.options.titles.length; i++) {			
				$(this.holder.dom).children().eq(i).append('<div class="title">'+this.options.titles[i]+'</div>');
				
			}
			
		}
		else{
			
			for(i = 0 ; i < items.length ; i++){	
				items[i].id=this.imgId+i;
				imgDom=$(items[i]).children('img');	
			
				var _image={
					holder:items[i],
					dom:imgDom,
					index:i,
					id:this.imgId+i,
					src:$(imgDom).attr('src'),
					width:$(imgDom).width(),
					height:$(imgDom).height(),
					top:0,
					left:0,
					// width:100,
					// height:100,
					loaded:false
				};
				this.images.push(_image);
			}
		}
		/*
		if(!$.browser.mozilla) image.src+="?" + new Date().getTime();	
			alert(self.images.length);
			for (i=0; i < self.images.length; i++) {
				if(self.images[i].width>0){
					imageLoaded(self.images[i]);
				}
			}
			for (var i=0; i < self.images.length; i++) {
				if(self.images[i].loaded===false){*/			
				preloadImages(self.images[0]);
					/*break;
				}			
			}*/
	},
	setCss:function(){
		$(this.options.imageClass).css({'display':'none'});
		// $(this.holder.dom).css({display:'none'});
	},
	init: function(){

		//var self=this;
		this.currentImage=this.images[this.currentIndex];	
		this.changeItem();		
		this.setEventListeners();

	},
	setEventListeners:function(){
		var self=this;
		this.eventsSet=true;
		if(this.options.mode=='dynamic') this.onResize();
		$(this.options.nextHandlers).click(function(){
			self.next();
		});
		
		$(this.options.imageClass).mousedown(function(e){
			e.preventDefault();
			self.stop();
			self.drag(e);
			
			$(document).bind('mouseleave',function(e){
				self.drop();			
			});			
		});
		
		$(this.options.imageClass).mouseup(function(e){
			self.drop();
			$(document).unbind('mouseleave');
		});
		//alert(this.options.controls.nextHandler);
		$(this.options.controls.nextHandler).click(function(){
			//alert('hi');
			self.next();
		});
		$(this.options.controls.prevHandler).click(function(){
			self.previous();
		});
		
		$(this.options.controls.TogglePlayHandler).click(function(){
			if(self.moving===true){
				$(this).removeClass('pause');	
				self.moving=false;
				self.stop();
			}else{
				self.moving=true;
				self.move();
				$(this).addClass('pause');
			}	
		});
		
		$('.slideIndex li').click(function(){
			self.changeItem($(this).index());
		});
		
		
	},
	setImageListeners:function(){

	},
	controls:function(){
		
		//var cn=this.controls;
		this.play=function(){
			
		};
		this.pause=function(){
			
		};
		this.next=function(){
			
		};
		this.previous=function(){
			
		};
		
	},
	cropImage: function(holder,image){
		var obj=this;
		var windowHeight= $(window).height();
		var windowWidth= $(window).width();	
		var imgWidth=(obj.bgImgOriginalWidth * windowHeight)/obj.bgImgOriginalHeight;
		var imgHeight=windowHeight;
		var marginTop=0;
		var marginLeft=-(imgWidth-windowWidth)/2; 
		if(imgWidth <windowWidth){				
			imgWidth=windowWidth;
			imgHeight=Math.round((obj.bgImgOriginalHeight * windowWidth)/obj.bgImgOriginalWidth) ;
			marginTop=-(imgHeight-windowHeight)/2;
			marginLeft=0;			
		}	

		$('.bg-container').children('img').css({
			'width':imgWidth ,
			'height':imgHeight,
			'margin-top': marginTop,
			'margin-left': marginLeft
		});
	},
	changeItem: function(index){
		// alert(this.currentIndex);
		var self=this;
		if(index!==this.currentIndex){
			self.stop();
			//alert('hi');
			$(document).unbind('mousemove');
			if(this.options.controls.index===true){
				$('.slideIndex li').eq(this.currentIndex).removeClass('selected');
				$('.slideIndex li').eq(index).addClass('selected');
			}
			if(this.interval!==undefined){
				clearInterval(this.interval);
				this.interval=null;
			}
			if(index!==undefined){
				this.prevIndex=this.currentIndex;		
				this.previousImage=this.currentImage;
				setTimeout(function(){
					$(self.previousImage.holder).css({'display':'none'});
				},self.options.fadeSpeed*1000);
				this.fade(
					this.previousImage.holder,{
						from:1,
						to:0,
						duration:self.options.fadeSpeed,
						easing:self.options.fadeEasing
					}
				);
				$(this.previousImage.holder).removeClass('currentSlide');
				this.currentIndex=index;
				this.currentImage=this.images[index];		
			}
			setTimeout(function(){self.move();},self.options.moveDelay*1000);
			$(this.currentImage.holder).addClass('currentSlide');
			$(this.currentImage.holder).addClass('moveVSlideshow');		
			this.fitImage(this.holder,this.currentImage);
			if(this.direction=='vertical'){
					$(this.currentImage.holder).css({
						'top':0,
						'display':'block'
					});
			}
			else if(this.direction=='horizontal'){
				$(this.currentImage.holder).css({
					'left':0,
					'display':'block'
				});
			}	
			//$(this.currentImage.holder).css({'opacity':0});		
		//	alert(this.currentImage.holder);
			//$(this.currentImage.holder).css({'visibility':'none'});	
			this.fade(
				this.currentImage.holder,
				{
					from:0,
					to:1,
					duration:self.options.fadeSpeed,
					easing:self.options.fadeEasing
				}
			);
		}
	},
	next:function(){
		var index=this.currentIndex;
		index++;
		if(index>=this.images.length) index=0;
		this.changeItem(index);
	},
	previous:function(){
		var index=this.currentIndex;
		index--;
		if(index<0) index=this.images.length-1;
		this.changeItem(index);
	},
	onResize:function(){	
		var self=this;
		var endInterval=null;	
		//var moving=this.moving;
		
		
		$(window).resize(function(){
			self.stop();	
			
			clearTimeout(endInterval);
			// endInterval=null;
			endInterval = setTimeout(function(){self.onResizeEnd();}, 1000);
			
			self.fitImage(self.holder,self.currentImage);			
			if(self.moving===false){
				if(self.direction=='vertical'){				
					var top=parseInt($(self.currentImage.holder).css('top'),10);
					if(self.currentImage.currentHeight+top<$(window).height()){
						self.currentImage.holder.style.top='0px';
					}
				}
				else if(self.direction=='horizontal'){
					var left=parseInt($(self.currentImage.holder).css('left'),10);
					if(self.currentImage.currentWidth+left<$(window).width()){
						self.currentImage.holder.style.left='0px';
					}
				}
			}
		});	
	},
	onResizeEnd:function(){
		//alert('hi');
		//if(this.moving===true){
		//this.move();
		//}		
		//if(moving==true){
			var self=this;
			setTimeout(function(){
				self.move();
			},self.options.restartDelay*100);
		//}
		
	},
	fitImage: function(holder,image){
		// var =>
		var self=this,
		_height=0,
		_width=0,
		_marginLeft=0,
		_left=0,
		_marginTop=0,
		_top=parseInt($(image.holder).css('top'),10);
		//info=this.info,
		//_holderTop=0,
		//_holderLeft=0;
		if(this.options.mode==='dynamic'){
			
			holder.width=$(window).width();
		}
			
		//end
		// Horizontal => 
		if(image.width>image.height){	
			
			holder.height=$(window).height()-150;
			$('#slider-holder').css({'top':-50});
			this.direction='horizontal';		
			if(image.height>holder.height){
				_marginTop=0;
				_height=holder.height;
				_width=Math.round((image.width * _height)/image.height);
				// this.info.update('_width',_width);
				// this.info.update('_height',_height);				
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
		
			_marginTop=0;
				
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
		// Info=> 
		// info.update('holder.height ',_height-holder.height+_top );
		// info.update('_height',_height);
		// info.update('_width',_width);
		// info.update('_top',_top);
		// info.update('_left',_left);
		// info.update('_marginLeft',_marginLeft);
		// info.update('_marginTop',_marginTop);
		//end
		// Set =>
		$(image.dom).css({
			'height':_height,
			'width':_width,
			'margin-left':_marginLeft,
			'margin-top':_marginTop,
			'top':_top ,
			'left':_left 
		});		
		image.currentHeight=_height;
		image.currentWidth=_width;
		image.currentMarginLeft=_marginLeft;
		image.currentLeft= _left;
		image.currentTop= _top;
		image.currentMarginTop=_marginTop;		
		//end
	},
	move:function(){
		
		//return false;		
		var self=this;
		//var imageHolder=this.currentImage.holder;		
		this.moving=true;
		
		/*
		//CSS3 =>
			alert(this.currentImage.holder);
			$(imageHolder).css({'display':'block'});
			if(this.ccs3Support){		
				var _css={
					effect:'animation',
					name:'moveSlideshow',
					speed:60,
					easing:'linear',
					iterations:'infinite'
					
					// play_state:'running',
					// direction:'alrernate'			
				}
				//css3.apply(_css,imageHolder);	
			}else{			
				$(imageHolder).fadeTo(this.options.fadesSpeed,to);
			}
		//end
		self.interval=undefined;
		//alert(self.interval);
		if(this.interval!=="") window.clearInterval(this.interval);			
		if(interval!=="") clearInterval(interval);	*/
		
		var step = function(){
		
			self.direction='horizontal';
			var dom=self.currentImage.holder;
			if(self.direction=='vertical'){				
				$(dom).css({'top':parseInt($(dom).css('top'),10)-self.options.moveSpeed});
				
				if(self.moving===true ){
					
					var top=parseInt($(dom).css('top'),10);		
					if(top<=$(window).height()-self.currentImage.currentHeight){
						self.stop();
						self.next();
					}
				}
				
			}
			else if(self.direction=='horizontal'){
	
				$(dom).css({'left':parseInt($(dom).css('left'),10)-self.options.moveSpeed});
				if(self.moving===true){
					var left=parseInt($(dom).css('left'),10);				
					if(left<=self.holder.width-self.currentImage.currentWidth){			
						self.stop();
						self.next();
					}
				}
			}		
		};
		
		if(self.interval === undefined){
			self.interval=setInterval(function(){step();},30);
		}
	},
	stop:function(){
		var self=this;
	
		self.moving=false;	
		if(this.interval!==undefined){
			//alert('hi');
				clearInterval(self.interval);
			self.interval = undefined;
		}
	},
	fade:function(imageHolder,_options){
		var defaultOptions={
			opacity:1,
			duration:1,
			easing:'linear',
			from:0,
			to:0
		};
		var options=this.gatherOptions(defaultOptions,_options);			
		//var self=this;
		$(imageHolder).css({'display':'block'});
		
		if(this.ccs3Support){
			var _css={
				effect:'transition',
				properties:'opacity',
				speed:options.duration,
				easing:options.easing,
				delay:options.delay,
				from:options.from,
				to:options.to
				//opacity:options.opacity
			};
			this.css3.apply(_css,imageHolder);
					
		//this.info.update('image',imageHolder);		
		}else{		
			//alert(self.options.fadeSpeed*1000);	
			$(imageHolder).fadeTo(this.options.fadeSpeed*1000,options.to);
		}	
		if(options.to===0){
			setTimeout(function(){
				$(imageHolder).css({'display':'none'});
				
			},this.options.fadeSpeed*1000);
		}
		
	},
	setImageProperties:function(image,index){		
		//console.time('setImageProperties');		
		this.imageWidths[index]=image.width();
		this.imageHeights[index]=image.height();					
		$(image).bind('dragstart', function(event) { return false; });		
		$(image).parent().css({'opacity':0});
		//this.fitImage(image);		
		this.dragDrop(image);
		//console.timeEnd('setImageProperties');		
	},
	drag:function(e){	
		this.stop();
		var self=this,
				//_this=this.drag,
				image=this.currentImage,
				//i =this.info,
				initX=e.pageX,
				initY=e.pageY,
				imageTop=parseInt(image.holder.style.top,10),
				imageLeft=parseInt(image.holder.style.left,10),
				previousCoordinate;
				this.speed=0;
								
				if(this.dropTimeOut!==undefined){
					clearTimeout(this.dropTimeOut);
					this.dropTimeOut=undefined;
				}						
				var move=	function(e){
					if(self.direction==='vertical'){
						var imageY=-(initY-e.pageY-imageTop);
						if(imageY>0) imageY=0;
						if(image.currentHeight+imageY<self.holder.height){
							imageY=-(image.currentHeight-self.holder.height);
						}					
						image.holder.style.top=imageY+'px';				
						if(previousCoordinate!==undefined){
							self.speed=imageY-previousCoordinate;
						}								
						previousCoordinate=imageY;
					}
					else if(self.direction==='horizontal'){
						var imageX=-(initX-e.pageX-imageLeft);
						if(imageX>0) imageX=0;
						if(image.currentWidth+imageX<self.holder.width){
							imageX=-(image.currenWidth-self.holder.width);
						}
						image.holder.style.left=imageX+'px';	
						if(previousCoordinate!==undefined){
							self.speed=imageX-previousCoordinate;
						}								
						previousCoordinate=imageX;			
					}
				};
				$(document).bind('mousemove',function(e){move(e);});

	},
	drop:function(){
		var self=this,
				minSpeed=this.options.minSpeed,
				animateIterations=0,
				//i=this.info,
				//top=parseInt(self.currentImage.holder.style.top,10),
				//left=parseInt(self.currentImage.holder.style.left,10),
				image=this.currentImage;
				
		if(this.dropInterval!==undefined){
			clearInterval(this.dropInterval);
		}	
		this.dropInterval=undefined;	
		if(self.direction=='vertical'){
			if(this.speed>this.options.speedLimit) this.speed=this.options.speedLimit;
			if(this.speed<-this.options.speedLimit) this.speed=-this.options.speedLimit;
		}
		
		var stopLimit=0.6;
		
		var animate=function(){
			self.dropping=true;
			animateIterations++;
			var difference=(self.speed/2)/(animateIterations/6);

			if(self.direction=='vertical'){
				var _top=difference;
				if(animateIterations<5){
					_top=self.speed/2;
				}
				
				var currentTop=parseInt(image.holder.style.top,10);
				if (currentTop>0){
						_top=0;
						image.holder.style.top=0+'px';
				}
				if(image.currentHeight+currentTop<self.holder.height){
					_top=0;
					image.holder.style.top=-(image.currentHeight-self.holder.height)+'px';					
				}
				
				if(Math.abs(_top)<stopLimit){
					_top=0;
				}
				
				if(_top===0){
					
					clearInterval(self.dropInterval);
					self.dropInterval=null;
					setTimeout(function(){self.move();self.dropping=false;},5000);
				}

				image.holder.style.top=parseInt(image.holder.style.top,10)+_top+'px';
			}else if(self.direction=='horizontal'){
				
				var _left=difference;
				
				var currentLeft=parseInt(image.holder.style.left,10);
				if (currentLeft>0){
						_left=0;
						image.holder.style.left=0+'px';
				}
				if(image.currentWidth+currentLeft<self.holder.width){
					_left=0;
					image.holder.style.left=-(image.currentWidth-self.holder.width)+'px';					
				}
				if(Math.abs(_left)<stopLimit ){
					_left=0;
				}				
				if(_left===0){
					clearInterval(self.dropInterval);
					self.dropInterval=null;
					setTimeout(function(){self.move();},5000);
				}	
				image.holder.style.left=parseInt(image.holder.style.left,10)+_left+'px';
			}
		};
		
					
		$(document).unbind('mousemove');				
		if(Math.abs(this.speed)>minSpeed){	
			//this.info.update('speed',this.speed);
			if(this.dropInterval === undefined){
				this.dropInterval=setInterval(function(){animate();},20);
				//setTimeout(function(){clearInterval(self.dropInterval)},5000);
			}
		}else{
			if(this.dropTimeOut===undefined){
				this.dropTimeOut=setTimeout(function(){
					self.move();
				},5000);
			}			
		}
		
		
		
		
	},
	getStyle:function(styles,length,propertyName){
		
		var lower=[];
		var higher=[];
		var equal=[];
		var i=0;
				
		for(var style in styles){		
			if(styles[style][propertyName]>length){
				higher.push(styles[style][propertyName]);
			}else if(styles[style][propertyName]<length){
				lower.push(styles[style][propertyName]);
			}else{
				equal.push(styles[style][propertyName]);
			}	
		}
		
		var closest;
		
		if(equal[0]!==undefined){
			closest=equal[0];
		}else{
			for (i=0; i < higher.length; i++) {
				if(closest===undefined){
					closest=higher[i];
				}else if(closest>higher[i]){
					closest=higher[i];
				}	
			}

			for (i=0; i < lower.length; i++) {
				if(closest===undefined){
					closest=lower[i];
				}else if(closest<lower[i]){
					closest=lower[i];
				}	
			}
		}
		
		for(style in styles){
			//alert(styles[style][propertyName]);
			if(styles[style][propertyName]==closest){
				return styles[style];
			}
		}
	},
	watchAnimation: function(image){
		//console.time('watch');
		var self=this;
		var offset=$(image).offset();
		
		if($(image).width()>$(image).height()){	
			
			if(offset.left>0){
				$(image).stop();
				clearInterval(self.watch);
				$(image).css({'top':0});				
			}
			
			if(offset.left < - ($(image).width()-$(window).width()) ){
				$(image).stop();
				clearInterval(self.watch);
				$(image).css({'left':- ($(image).width()-$(window).width()) });				
			}
			
		}else{
			
			if(offset.top>0){
				$(image).stop();
				clearInterval(self.watch);
				$(image).css({'top':0});				
			}
			
			if(offset.top < - ($(image).height()-$(window).height()) ){
				$(image).stop();
				clearInterval(self.watch);
				$(image).css({'top':- ($(image).height()-$(window).height()) });				
			}
		}
		//console.timeEnd('watch');		
		//$('#y').html(Math.round(offset.top));
		
	}	
};
Image=function(){
};
Image.prototype={
};




})(jQuery);
//new