(function($){
	
	System=function(){
		this.isIE = document.all?true:false;
	};
	System.prototype={
		getMouseX:function(){
			var e = (window.event) ? window.event : evt;
			if(!this.isIE) return e.pageX;
			if(this.isIE) return e.clientX + document.body.scrollTop;
		},
		getMouseY:function(){
			var e = (window.event) ? window.event : evt;
			if (!this.isIE) return e.pageY;
			if (this.isIE) return e.clientY + document.body.scrollTop;
		},
		getMouse:function(){
			return [this.getMouseX(),this.getMouseY()];
		}

	};
	
})(jQuery);