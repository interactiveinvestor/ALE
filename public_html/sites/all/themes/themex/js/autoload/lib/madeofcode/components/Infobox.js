(function($){
	
	_InfoBox=function(){
		this.html="<li id='[id]'>[name] : <span>[value]</span></li>";
		this.id=$('.info-box').length + 1;
		if($('.info-box').length==0){
			$('body').append("<ul class='info-box' id='info-box-"+this.id+"'></ul>");		
		}		
	};
	_InfoBox.prototype={
		getId: function(name){
			return name.toLowerCase().replace(/\s/g,'-').match(/[a-z -]+/i);
		},
		add: function(name,value){
			var id=name.toLowerCase().replace(/\s/g,'-').match(/[a-z -]+/i);
			var html=this.html.replace(/\[id\]/gi,id).replace(/\[name\]/gi,name).replace(/\[value\]/gi,value);		
			$('#info-box-'+this.id).append(html);	
		},
		update: function(name,value){

			var id=name.toLowerCase().replace(/\s/g,'-').match(/[a-z -]+/i);
			if($('#info-box-'+this.id+' #'+id).size()==0){
				this.add(name,value);

			}else{
				$('#info-box-'+this.id+' #'+id).children('span').html(value);
			}
		}
	};
	
})(jQuery);