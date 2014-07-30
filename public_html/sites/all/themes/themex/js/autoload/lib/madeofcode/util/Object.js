_Object=function(){};
_Object.prototype={
	gatherOptions:function(_default,_options){
	
		var options={};
		for(var opt in _default){		
			
			if(typeof(_default[opt])==='object'){
				var obj=_default[opt];			
				options[opt]=obj;				
				for(var prop in obj){						
					if(_options[opt]!==undefined&&_options[opt][prop]!==undefined){					
						options[opt][prop]=_options[opt][prop];					
					}else{
						options[opt][prop]=_default[opt][prop];
					}						
				}	
			}else{
				if(_options[opt]===undefined) options[opt]=_default[opt];
				else options[opt]=_options[opt];			
			}		
		}
		return options;
	}
};

_object=new _Object();