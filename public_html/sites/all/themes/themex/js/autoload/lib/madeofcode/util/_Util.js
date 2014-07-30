function toggleDisplay(holder,hidden,close_class){
		
	if($(hidden + ' .close').size()===0){		
		$(hidden).append('<div class="'+ close_class +'"></div>');
	}	
	$(hidden).css({
		'visibility':'hidden',
		'display':'none'
	});		
	$(holder+', '+holder+' .'+close_class).click(function(e){
		//alert($(hidden).css('display'));
		if($(hidden).css('display')=='none'){
		
			$(hidden).css({
				'visibility':'visible',
				'display':'inline-block',
				'opacity':0
			});
			$(hidden).fadeTo(200,1);
		}else{		
			$(hidden).fadeTo(200,0, function(){
				$(hidden).css({
					'visibility':'hidden',
					'display':'none'
				});
			
			});		
		}	
	});
	
	$(holder).children(hidden).click(function(e) {
        e.stopPropagation();
   });	
}