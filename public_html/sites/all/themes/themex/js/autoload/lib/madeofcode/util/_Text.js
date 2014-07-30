Text=function(){};
Text.prototype={
	trim:function(texts,size,moreText,lessText){		
		if(moreText===undefined){
			moreText='Read More';
		}		
		if(lessText===undefined){
			lessText='Show Less';
		}
		var button='<span class="trim-button">'+moreText+'</span>';			
		var replaceText =function(e,trimmed,full){		
			$(e).html(trimmed);				
			$(e).next().click(function(){
				if($(e).html()==trimmed){
					$(e).html(full);
					$(e).next().html(lessText);
				}else{
					$(e).html(trimmed);
					$(e).next().html(moreText);
				}
			});	
		};
	
		$(texts).each(function(i,e){		
			var fullText=$(e).html();			
			if(fullText.length>size){
				var trimmedText=fullText.substring(0, size);
				trimmedText+='...';	
				$(button).insertAfter($(e));
				replaceText($(e),trimmedText,fullText);
			}
							
		});	
	},
	machineName:function(text,separator){		
		if(separator===undefined) separator='_';
		var name=text.toLowerCase().replace(/\s/gi, "_").replace(/[^a-zA-Z0-9\_]/g,'');		
		return name;
	},
	trimString:function(textHolder,_length){
		
		var truncate=function (str, limit) {
			var bits, i;
			bits = str.split('');
			if (bits.length > limit) {
				for (i = bits.length - 1; i > -1; --i) {
					if (i > limit) {
						bits.length = i;
					}
					else if (' ' === bits[i]) {
						bits.length = i;
						break;
					}
				}
				bits.push('...');
			}
			return bits.join('');
		};
		
		var linkMore='<br/><a class="trim-link" href="JavaScript:void(0)">Continue reading</a>';
		var linkLess='<br/><a class="trim-link" href="JavaScript:void(0)">Back to top</a>';
		var fullText=textHolder.html();
		
		
		if(fullText.length>_length){
			
			var trimmedText=truncate(fullText,_length);		
			textHolder.html(trimmedText+linkMore);
			$(textHolder).addClass('trimmed');
			
			$('.trim-link').live('click',function(){
				if(!$(textHolder).hasClass('trimmed')){
					$(textHolder).addClass('trimmed');
					$(textHolder).html(trimmedText+linkMore);
				}else{
					$(textHolder).removeClass('trimmed');
					$(textHolder).html(fullText+linkLess);
					$("html, body").animate({ scrollTop: 0 }, "slow");
				}



			});
			
		}
		
		
		
		
	},
	
	

};