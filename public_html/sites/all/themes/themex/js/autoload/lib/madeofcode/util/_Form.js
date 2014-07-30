Form=function(form){
	var obj=this;
	this.form=form;
	this.validationPassed=false;
	this.validationMessages=[];
	$(form).find('.submit').click(function(){
		obj.validate(form,obj);		
		if(obj.validationPassed===false){
			return false;
		}
	});
};
Form.prototype={
	RadioTame:function(holder){	
		$(holder).find(':radio').each(function(i,e){		
			$(e).css({'display':'none'});
			$(e).parent().prepend('<div class="radio-tamed"></div>');		
		});
		
		//var button1=$(holder).find('.radio-tamed');
		var label=$(holder).find('.productAttributeValue').find('label');

		$(label).click(function(e){
			var globalLabel=$(this).parents('.productAttributeConfigurablePickListProduct').children('.productAttributeLabel').find('.selected-tiem');		
			$(globalLabel).html($(this).children('span').html());			
			e.preventDefault();
			$(holder).find('.radio-tamed').removeClass('radio-on');
			$(this).parent().find('.radio-tamed').addClass('radio-on');
			$(this).parent().find('input').attr('checked',true);
		});
		
		var hidden=$(holder).find('.productOptionViewProductPickList');
		
		$(hidden).css({'display':'block'});			
		$(hidden).jScrollPane({verticalDragMaxHeight:60,verticalDragMinHeight:30,showArrows:false,mouseWheelSpeed:60});
		$(hidden).css({'display':'none'});
		
		var bgs='<div class="bg-top"></div>';
		bgs+='<div class="bg-middle"></div>';
		bgs+='<div class="bg-bottom"></div>';
		bgs+='</ul></div>';	
		//$(holder).find('ul').addClass('jspScrollable');	
		$(holder).find('.jspScrollable').prepend(bgs);
		
		

		if($(holder).find(':radio:checked').size()>0){		
			$(holder).find(':radio:checked').parent().find('.radio-tamed').addClass('radio-on');		
		}
	},
	CheckboxTame:function(holder){	

		$(holder).find(':checkbox').each(function(i,e){		
			$(e).css({
				'display':'none',
				'visibility':'hidden'			
			});
			$(e).parent().prepend('<div class="checkbox-tamed"></div>');		
		});

		$(holder).find('.checkbox-tamed').click(function(e){		
			if( $(this).hasClass('checkbox-on') ){
				$(this).removeClass('checkbox-on');	
				$(this).parent().find('input').attr('checked',false);		
			}else{
				$(this).addClass('checkbox-on');
				$(this).parent().find('input').attr('checked',true);
			}		
		});

		$(holder).find('label').click(function(e){		
			e.preventDefault();		
			if( $(this).parent().find('.checkbox-tamed').hasClass('checkbox-on') ){
				$(this).parent().find('.checkbox-tamed').removeClass('checkbox-on');	
				$(this).parent().find('input').attr('checked',false);		
			}else{
				$(this).parent().find('.checkbox-tamed').addClass('checkbox-on');
				$(this).parent().find('input').attr('checked',true);
			}
		});

		if($(holder).find(':checkbox:checked').size()>0){	
			$(holder).find(':checkbox:checked').parent().find('.checkbox-tamed').addClass('checkbox-on');
		}	
	},
	SelectTame :function(_holder,_label){
		//var holder=_holder;
		//var label=_label;
		//var _this=this.SelectTame;
		var self=this;
		this.tameSelect= function(holder,label){
			//holder=holder;
			$(holder).css({'display':'none'});	
			var tamed='<div class="select-tamed">';
			var selectedVal=$(holder).children('option:selected').html();		
			if(label!==undefined){
				var labelText=$(label).html();
				$(label).remove();
				tamed+='<div class="label">'+labelText+'</div>';
			}
			tamed+='<div class="selected">'+selectedVal+'</div><ul>';
			$(holder).children('option').each(function(i,e){
				tamed+='<li>'+$(e).html()+'</li>';
			});
			$(holder).parent().append(tamed);
			var tamedHolder=$(holder).parent().find('.select-tamed');
			var selected=$(holder).parent().find('.selected');
			var click=function(el){
				if($(el).next('ul').css('display')=='none'){			
					$(el).next('ul').css({
						'display':'block',
						'opacity':0
					});
					$(el).next('ul').fadeTo(300,1);
				}else{
					$(el).next('ul').fadeTo(300,0,function(){
						$(el).next('ul').css({'display':'none'});
					});

				}
			};

			$(selected,label).click(function(e){
				click(this);
			});



			$(tamedHolder).children('ul').children('li').click(function(e){
				$(selected).next('ul').fadeTo(300,0,function(){
					$(selected).next('ul').css({'display':'none'});
				});
				$(selected).html($(this).html().replace(/(^\s+|\s+$)/g,' '));
				$(holder).val($(holder).children().eq($(this).index()).val().replace(/(^\s+|\s+$)/g,' '));	
			});

			//return false;
			checkValues(holder);	
			$(tamedHolder).children('ul').css({'display':'block'});			
			$(tamedHolder).children('ul').jScrollPane({verticalDragMaxHeight:60,verticalDragMinHeight:30,showArrows:false,mouseWheelSpeed:60});
			$(tamedHolder).children('ul').css({'display':'none'});
			var bgs='<div class="bg-top"></div>';
			bgs+='<div class="bg-middle"></div>';
			bgs+='<div class="bg-bottom"></div>';
			bgs+='</ul></div>';		
			$(tamedHolder).find('.jspScrollable').prepend(bgs);
			$(document).click(function(event) { 
				if($(event.target).parents().index($(holder).parent()) == -1) {		
					if($(selected).next('ul').css('display')=='block') {
						$(selected).next('ul').fadeTo(300,0,function(){
							$(selected).next('ul').css({'display':'none'});
						});
					}
				}        
			});	

			checkValues = function(holder){
				if($(holder).val()!==''){
					$(holder).children().each(function(i,e){
						if($(e).val()==$(holder).val()){
							$(selected).html($(e).html().replace(/(^\s+|\s+$)/g,' '));
						}
					});
				}
			};
		};
		$(_holder).each(function(i,e){
			self.tameSelect(e,_label);
		});	
	},
	validate:function(form,validator){
		validator.validationMessages.length=0;	
		var requiredPassed=true;
		$(form).find('.required').each(function(i, element){	
						
			if($(element).attr('type')=='text' || $(element).prop("tagName")=='TEXTAREA' ){
				
				//var labelColor=$(element).prev('label').css('color');	
							
				if($(element).attr('value')===''){
					$(element).prev('label').addClass('form-error-label-style');
					requiredPassed=false;
					 
				}else{	
					$(element).prev('label').removeClass('form-error-label-style');
				}
			}	
						
			if($(element).hasClass('dropdown-single') ){
				if ($(element).children("select").children('option:selected').val()==='') {
					$(element).children('.label').addClass('form-error-label-style');
					requiredPassed=false;
					
				}else if($(element).children('.label').hasClass('form-error-label-style')){
					
					$(element).children('.label').removeClass('form-error-label-style');
				}			
			}		
				
			if($(element).hasClass('form-check') ){				
				if($(element).children('span').hasClass('checked')){
					if($(element).children('label').hasClass('form-error-label-style')){
						$(element).children('label').removeClass('form-error-label-style');
					}					
				}
				else{
					requiredPassed=false;
					$(element).children('label').addClass('form-error-label-style');
				}
			}
		});
		$(form).find('.pre-validate').each(function(i, element){
			if($(element).val()!==''){
				var pre_arr = new Array('first name', 'last name');
				var oVal = $(element).val();
				var lcVal = oVal.toLowerCase();
				if(jQuery.inArray(lcVal, pre_arr) >= 0) {
					validator.validationMessages.push('Please provide '+ lcVal);
					validator.validationPassed=false;
				}
			}
		
		});
		$(form).find('.email-validate').each(function(i, element){
			if($(element).val()!==''){
				var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
				var address = $(element).val();
				if(reg.test(address) === false) {
					$(element).prev('label').addClass('form-error-label-style');
					validator.validationMessages.push('The email address must conform to a valid email address syntax.');
					validator.validationPassed=false;
				}else{
					$(element).prev('label').removeClass('form-error-label-style');
				}
			}		
		});
		$(form).find('.phone-validate').each(function(i, element){
			
			if($(element).val()!==''){
				//var reg = /^\({0,1}((0|\+61)(2|4|3|7|8)){0,1}\){0,1}(\ |-){0,1}[0-9]{2}(\ |-){0,1}[0-9]{2}(\ |-){0,1}[0-9]{1}(\ |-){0,1}[0-9]{3}$/; removed 9/8/12 - author ads
				var reg = /^\({0,1}((0|\+61)(2|4|3|7|8))\){0,1}(\ |-){0,1}[0-9]{2}(\ |-){0,1}[0-9]{2}(\ |-){0,1}[0-9]{1}(\ |-){0,1}[0-9]{3}$/; // added 9/8/12 - author ads
				var tel = $(element).val();
				if(reg.test(tel) === false) {
					$(element).prev('label').addClass('form-error-label-style');
					validator.validationMessages.push('The phone number must be a valid phone number.');
					validator.validationPassed=false;
				}else{
					$(element).prev('label').removeClass('form-error-label-style');
				}
			}
		
		});
		
		//5 Limes 15/08 - new test for If by phone field		
		if($('.form-preferred-contact').children("select").children('option:selected').val() == 'Phone') {
			//test for required field now
			if($('.form-if-by-phone').children("select").children('option:selected').val() === '') {
				requiredPassed=false;
			}		
		}
		
		
		if(requiredPassed===false){
			validator.validationMessages.push('All required fields must be completed.');
		}
		
		$(validator.form).find('.error-messages').html('');
		validator.renderMessages(validator);
	},
	renderMessages:function(validator){
		
		if(validator.validationMessages.length===0){
			validator.validationPassed=true;
			$(validator.form).find('.error-messages').hide();
			return false;
		}
		$(validator.form).find('.error-messages').show();
		var message='<ul>';		
		for (var i=0; i < validator.validationMessages.length; i++) {
			message+='<li>'+validator.validationMessages[i]+'</li>';
		}
		message+='</ul>';
		$(validator.form).find('.error-messages').append(message);
		
		if($(validator.form).hasClass('pop-up-form') && !$(validator.form).hasClass('static-form')){
			
			if(jQuery('.fancybox-inner').size()>0){
				nav.setScroll(validator.form);
			}else{
				nav.setIframeScroll();	
			}
		}
		
	},
	setInputDimensions:function(){
	//Calculates width of input field based on label width for site forms
		$('.text-input').each(function(i,element){				
			//var inputWidth=$(element).width();
			//var labelWidth=$(element).children('label').width();			
			$(element).children('label').css({
				'line-height':'20px',
				'padding-right': '6px'
			});
			$(element).children('input').css({
				//'float':'left',
				//'width':inputWidth-labelWidth -9,
				'margin-left':'2px',
				'height':20
			});
		});	
	},
	getHeight:function(hlHolder){
		hlHolder.css('height', 'auto');
		var ttlH = hlHolder.height();
		//hlHolder.css('height', ttlH);
		//console.log("current height - "+ttlH)
		return ttlH;
	},
	toggleLabel: function(element, defaultValue){	
		$(element).focus(function(){
			if($(element).val()==defaultValue){
				$(element).val('');
			}		
		});

		$(element).focusout(function(){
			if($(element).val()===''){
				$(element).val(defaultValue);
			}
		});	
	}
};
