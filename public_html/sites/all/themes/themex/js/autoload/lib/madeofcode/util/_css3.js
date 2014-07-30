(function($){
CSS3=function(){
	//alert(this.checkSupport(document.getElementById('wrapper')));
	this.support=this.checkSupport(document.getElementById('wrapper'));	
	this.setProperties();
};
CSS3.prototype={
	checkSupport:function(elm){
		var supported = false,
				animationstring = 'animation',
				keyframeprefix = '',
				domPrefixes = 'Webkit Moz O ms Khtml'.split(' '),
				pfx  = '';
				//alert(this.holder.dom);

		if( elm.style.animationName ) { supported = true; }   

		if( supported === false ) {
			for( var i = 0; i < domPrefixes.length; i++ ) {
				if( elm.style[ domPrefixes[i] + 'AnimationName' ] !== undefined ) {
					pfx = domPrefixes[ i ];
					animationstring = pfx + 'Animation';
					keyframeprefix = '-' + pfx.toLowerCase() + '-';
					supported = true;
					break;
					}
				}
			}
		
		return supported;
	},
	setProperties:function(){
		this.name='ImageSlider';
		//alert(this.effects);
		this.easing={
			easeInQuad:'cubic-bezier(0.55, 0.085, 0.68, 0.53)',
			easeOutQuad:'cubic-bezier(0.25, 0.46, 0.45, 0.94)',
			easeInCubic:'cubic-bezier(0.55, 0.055, 0.675, 0.19)',
			easeOutCubic:'cubic-bezier(0.215, 0.61, 0.355, 1)',
			
			easeInQuint:'cubic-bezier(0.755, 0.05, 0.855, 0.06)',
			easeOutQuint:'cubic-bezier(0.23, 1, 0.32, 1)'
		};
		
		
	},
	apply:function(css,dom){	
		this.dom=dom;	
		this.effects(css,dom,this);
		if(css.opacity){
		}
	},
	getStyle:function (oElm, strCssRule){
			var strValue = "";
			if(document.defaultView && document.defaultView.getComputedStyle){
				strValue = document.defaultView.getComputedStyle(oElm, "").getPropertyValue(strCssRule);
			}
			else if(oElm.currentStyle){
				strCssRule = strCssRule.replace(/\-(\w)/g, function (strMatch, p1){
					return p1.toUpperCase();
				});
				strValue = oElm.currentStyle[strCssRule];
			}
			return strValue;
	},
	effects:function(css,dom){	
		var self=this;
		var _this=this.effects;				
		//var properties=_this.properties={};
		_this.vendors=[];
		_this.values=[];
		_this.options=[];		
		_this._style=$(dom).attr('style');
		var fx=_this;
		fx.register=function(){
			
			
			
		};
		fx.apply=function(vendors,value){		
			//var vendor;
			for (var i=0; i < vendors.length; i++) {
				
				_this.vendors.push(vendors[i]);
				_this.values.push(value);
			}				
		};
		fx.transition=function(){
		//	alert(this.effects.options)		
			options=['properties','speed','easing','delay'];
			vendors=['transition','-moz-transition','-webkit-transition','-o-transition'];
			var value='';
			for (var i=0; i < options.length; i++) {			
				if(css[options[i]]!==undefined){
					var _value=css[options[i]];
					if(options[i]=='speed'||options[i]=='delay'){		
						_value+='s';	
					}
					var easing=self.easing[css[options[i]]];
					if(options[i]=='easing' && easing !==undefined){			
						_value=easing;
					}						
					value+=' '+_value+' ';
				}				
			}
		//	alert(value);
			this.apply(vendors,value); 
		};
		fx.opacity=function(){
			vendors=['-webkit-opacity','opacity'];
			this.apply(vendors,css.opacity); 
		};
		fx.animation=function(){
			
			//alert('gi');
			options=['name','speed','easing','delay','iterations','direction','play_state'];
			vendors=['animation','-webkit-animation','-moz-animation','-o-animation'];
			var value='';
			for (var i=0; i < options.length; i++) {			
				if(css[options[i]]!==undefined){
					value+=' '+css[options[i]];
					if(options[i]=='speed'||options[i]=='delay' && value!==0 && value!=='') value+='s';	
					value+=' ';
				}				
			}
			
			
			this.apply(vendors,value); 
		};

		if(css.properties=='opacity'){
			
			//$(dom).css({'display':'block'});	
			$(dom).css({opacity:css.from});	
			if(!$(dom).hasClass('css3Opacity')){
				$(dom).addClass('css3Opacity');
			}else{		

			}			
		}
		
		//alert('gu');
				
		if(typeof(this.effects[css.effect])!==undefined){this.effects[css.effect]();}
		var _css={};
		for (var i=0; i < _this.vendors.length; i++) {			
			var vendor=_this.vendors[i].replace(/^\s\s*/, '').replace(/\s\s*$/, '');
			//var value= _this.values[i];		
			_css[vendor]=_this.values[i];
		}
		$(dom).css(_css);

		if(css.properties=='opacity'){
			setTimeout(function(){
				$('#'+dom.id).css({opacity:css.to});
			},50);
		}
		
	}
	
};
})(jQuery);