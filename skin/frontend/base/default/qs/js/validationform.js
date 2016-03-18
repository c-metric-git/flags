FreaksForm = Class.create();
FreaksForm.prototype = new VarienForm();

FreaksForm.prototype.initialize = (function(superConstructor) {
    return function(formId,validateurl,firstFieldFocus,street,street2,city,region_id,region,postcode,country_id,button_id,fieldsetid) {
        superConstructor.call(this, formId, firstFieldFocus);
        if (this.form) {
		
		    this.fieldsetid = fieldsetid;
            
			// fields of form
			this.formId = formId;
		    this.button_id = button_id;
			this.street = street;
			this.street2 = street2;
			this.city = city;
			this.region_id = region_id;
			this.region = region;
			this.postcode = postcode;
            this.country_id = country_id;
			
			// fields and respons map
			var  correctingitems = new Object();
				 correctingitems['street'] = this.street;
			   //correctingitems['street2'] = this.street2;
				 correctingitems['city'] = this.city;
				 correctingitems['region'] = this.region;
				 correctingitems['region_id'] = this.region_id;
				 correctingitems['postcode'] = this.postcode;
				 correctingitems['country_id'] = this.country_id;
				 correctingitems['country'] = this.country_id;
            this.correctingitems = correctingitems;
			
		    //save form button click action
		    this.buttonaction = $(this.button_id).readAttribute('onclick');
			
			//remove button click action
			$(this.button_id).writeAttribute('onclick','');

			//url of validate controller
		    this.validateurl = validateurl;
			
			//set button click action
			var buttonId = 'validation_advice_button_'+formId;
                 /*notice popup*/
			     $(buttonId).observe('click', this.submitStrong.bindAsEventListener(this));
			
			var ERRORbuttonId ='validation-popup-address-validation-button_'+formId;
			     /*error popup*/
			     $(ERRORbuttonId).observe('click', this.submitError.bindAsEventListener(this));
			 
			if(this.buttonaction != null){
			    $(this.button_id).observe('click', this.submit.bindAsEventListener(this));
			}
			else{//set submit form aciton 
			    this.form.observe('submit', this.submit.bindAsEventListener(this));
			}
        }
    };
})(VarienForm.prototype.initialize);

FreaksForm.prototype.submitError = function(e){
	Event.stop(e);
	

	if_validate = $$('input:checked[type="radio"][name="address_validate_error_'+this.formId+'"]').pluck('value');
	if (if_validate=='continue'){
				if(this.buttonaction != null){
				   script = "<script>"+this.buttonaction+"</script>";
				   script.evalScripts();
	            }else{
                   
				this.form.submit();
	            }
	}else{
	//this._submit(this.validateurl);
	}
    return false;
};

FreaksForm.prototype.submitStrong = function(e){
    /* after notice popup */
	Event.stop(e);
	var submitAction = true;
	
    /*radio button value*/
	if_validate = $$('input:checked[type="radio"][name="address_validate_notice_'+this.formId+'"]').pluck('value');
	
	
	if (if_validate=='continue'){/*do nothing*/
	    var script_to = $('address_validate_notice_continue_'+this.formId+'').getAttribute("script");
		
	}
	else if(if_validate=='change'){/*enter new address and validate more*/
	    var script_to = $('address_validate_notice_change_'+this.formId+'').getAttribute("script");
	    submitAction = false;
	}
	else{/*use suggested address*/
	   $(''+this.street2+'').setValue("");
	    var script_to = $('address_validate_notice_validate_'+this.formId+'').getAttribute("script");
	}
	
	
	
	    script_validate = "<script>"+script_to+"</script>"; 
	    script_validate.evalScripts();
				   

	
	if (!$(this.fieldsetid).visible())/*go to next step*/
	{
	   script = "<script>"+this.buttonaction+"</script>";
				   script.evalScripts();
				   return false;
	}
	
	
    if(this.validator && this.validator.validate()) {
	if(submitAction){
	//if($('address_validation_is_validate'+this.form.id+''))
	//{$('address_validation_is_validate'+this.form.id+'').remove();}
	
				if(this.buttonaction != null){
				   script = "<script>"+this.buttonaction+"</script>";
				   script.evalScripts();
	            }else{
                   
				this.form.submit();
	            }
				}
    }	
	else{
	}
	
    return false;
	
	
};



FreaksForm.prototype.submit = function(e){
   // alert('form submit');
	Event.stop(e);
	//var elements = Form.getElements(this.form);
	
	if (!$(this.fieldsetid).visible())
	{
	   script = "<script>"+this.buttonaction+"</script>";
				   script.evalScripts();
				   return false;
	}
	
    if(this.validator && this.validator.validate()) {
	
	 //   if(($('address_validation_is_validate'+this.form.id+'')) && ($('address_validation_is_validate'+this.form.id+'').value = 1)){
	 if(false){
		//alert('validate');
		if($('address_validation_is_validate'+this.form.id+'')){$('address_validation_is_validate'+this.form.id+'').remove();}
				if(this.buttonaction != null){
				   script = "<script>"+this.buttonaction+"</script>";
				   script.evalScripts();
	            }else{
                   this.form.submit();
	            }
	    }else{
            this._submit(this.validateurl);
	    }
    }	
	else{

	}
    return false;
};

function ucwords( str ) {   

    return str.replace(/^(.)|\s(.)/g, function ( $1 ) { return $1.toUpperCase ( ); } );

}

FreaksForm.prototype._submit = function(url) { 
           //get values of form elements
             var formarray = new Object();
			 
			   $(this.street).setValue(ucwords($(this.street).value.toLowerCase()));
			   $(this.street2).setValue(ucwords($(this.street2).value.toLowerCase()));
			   $(this.city).setValue(ucwords($(this.city).value.toLowerCase()));
			   $(this.region_id).setValue(ucwords($(this.region_id).value.toLowerCase()));
			   $(this.region).setValue(ucwords($(this.region).value.toLowerCase()));
			   $(this.postcode).setValue(ucwords($(this.postcode).value.toLowerCase()));
			   $(this.country_id).setValue(ucwords($(this.country_id).value.toLowerCase()));
	
			   
				 formarray['street'] = $(this.street).value.replace(/^\s+/, "").replace(/\s+$/, "") +' '+ $(this.street2).value.replace(/^\s+/, "").replace(/\s+$/, "");;
				 formarray['city'] = $(this.city).value;
				 formarray['region_id'] = $(this.region_id).value;
				 formarray['region'] = $(this.region).value;
				 formarray['postcode'] = $(this.postcode).value;
				 formarray['country_id'] = $(this.country_id).value;

                 this.formarray = formarray;
				 
		/*street to one line*/
		
				// $(this.street).setValue(formarray['street']);	 
				// $(''+this.street2+'').setValue("");
				 
               
			
			

                var region = $(this.region_id);
			//  alert($(this.region_id).value);
			//	alert($(this.region_id).label);
			//	alert(region);
				
				 
              var value_of_region = region.value;
				value_of_region = region.select('option[value="'+value_of_region+'"]')[0].innerHTML;
             
				var oldAddress = formarray['street'].replace(/^\s+/, "").replace(/\s+$/, "")+
				', '+$(this.city).value+
				', '+value_of_region+   //', '+region[region.value].label+
                //', '+$(this.region).value+
				//', '+$(this.country_id).value+
                ', '+$(this.postcode).value;
				 
				oldAddressContent = "<div id = \"old_address_content_"+this.form.id+"\">"+oldAddress+"</div>";
				
				oldAddressContent =  ucwords(oldAddressContent.toLowerCase());  

				if($('old_address_content_'+this.form.id+'')){$('old_address_content_'+this.form.id+'').remove();} 
				
				$('old_address_'+this.formId+'').insert(oldAddressContent);
				
				
				
				
				
				oldAddressErrorContent = "<div id = \"old_error_address_content_"+this.form.id+"\">"+oldAddress+"</div>";
				
				oldAddressErrorContent =  ucwords(oldAddressErrorContent.toLowerCase());  
				
				if($('old_error_address_content_'+this.form.id+'')){$('old_error_address_content_'+this.form.id+'').remove();} 
				
				$('old_error_address_'+this.formId+'').insert(oldAddressErrorContent);
				
				
		//sens ajax request		
            	 new Ajax.Request(url, {
            	 method: this.form.getAttribute('method') || 'post',
                //parameters: this.form.serialize(),
				parameters: this.formarray,
				
            	onComplete: this._processResult.bind(this),
                onFailure: function() {
          
            	}
            	});
    }
	
//ajax response processing
	
FreaksForm.prototype._processResult = function(transport){
    var response = '';
    try {
        response = transport.responseText.evalJSON();
		
		/*
		//if first time validation
		if(!$('address_validation_is_validate'+this.form.id+'')){
		this.form.insert("<input type=\"hidden\" name=\"is validate"+this.form.id+"\" id=\"address_validation_is_validate"+this.form.id+"\" value=\"1\" />" );
	    }
		*/
		
		this.processNormalizeResult(response.notices,response.address,this.correctingitems);
		
		if(response.notices != ''){   
		    
		 
			//$('new_address_'+this.formId+'').insert(newAddress);
			
		     this.processValidationResult(this.form.id,response.notices,response.address,this.correctingitems);
			 window.scrollTo(0,0);
		}
		else if(response.normalized==false){
            $('window-overlay_'+this.form.id+'').show();
		    $('validation-popup-address-validation_'+this.form.id+'').show();
			window.scrollTo(0,0);
		}
		else
		{
		/*if adress is good*/		
		if($('address_validation_is_validate'+this.form.id+'')){$('address_validation_is_validate'+this.form.id+'').remove();} 
		
		 		if(this.buttonaction != null){
				   script = "<script>"+this.buttonaction+"</script>";
				   script.evalScripts();
	            }else{
                   this.form.submit();
	            }
	    // this.submit();
		//  this.form.submit();
		}
     } catch (e) {
        response = transport.responseText;
    }

};
	FreaksForm.prototype.processNormalizeResult = function(adress_notice,true_address,correctingitems){
    if (adress_notice == ''){
		var valid_address= $H(true_address); 
									valid_address.each(function(valid_address_item) {
									if (valid_address_item.key != 'region' && valid_address_item.key != 'country' && valid_address_item.key != 'street'){
											 adress_field_value = valid_address_item.value;
											 $(correctingitems[valid_address_item.key]).setValue(adress_field_value);
										 }
			                        });
	}
	else{
		/*var notices = $H(adress_notice); 
		notices.each(function(notice) {
		var valid_address= $H(true_address); 
									valid_address.each(function(valid_address_item) {
									if(valid_address_item.key != notice.key){
									if (valid_address_item.key != 'region' && valid_address_item.key != 'country'){
										 adress_field_value = valid_address_item.value;
										 $(correctingitems[valid_address_item.key]).setValue(adress_field_value);
										 }
			}});
		});*/
	}
	};
	
FreaksForm.prototype.processValidationResult = function(formid,adress_notice,true_address,correctingitems){
		  
	var notices = $H(adress_notice); 
	var advice_action = '';
	var advice_no_action ='';
    var adress_field_value;
	var street_field_value;
    var newAddress = '';

    var valid_address= $H(true_address);
    valid_address.each(function(valid_address_item) {
        if(valid_address_item.key != 'country' && valid_address_item.key != 'region_id'&&valid_address_item.key != 'country_id'){
            if(notices.keys().indexOf(valid_address_item.key) !== -1) {
                newAddress = newAddress +' <span class="suggest">'+valid_address_item.value+'</span>';
            } else {
                newAddress = newAddress +' '+valid_address_item.value;
            }
            if (valid_address_item.key != 'postcode') { newAddress += ','; }
        }
    });

    newAddress_ = "<div id = \"new_address_content_"+formid+"\">"+newAddress+"</div>";
    if($('new_address_content_'+formid+'')){$('new_address_content_'+formid+'').remove();}
    $('new_address_'+formid+'').insert(newAddress_);

    notices.each(function (notice) {

        valid_address.each(function (valid_address_item) {
		if(valid_address_item.key == 'street'){
		street_field_value = valid_address_item.value;
		}
            if (notice.key == valid_address_item.key) {
                adress_field_value = valid_address_item.value;
            }
        });

        if (notice.key == 'region' || notice.key == 'country') {
            element = $(correctingitems[notice.key]);

            key = notice.key + '_id'; // for example : region = region_id
            //correctingitems[key];
            //alert(correctingitems[key]);

            advice = "<div id=\"" + correctingitems[key] + "_advice\" onClick=\"FreaksForm.prototype.correctingItem('" + correctingitems[key] + "','" + true_address[key] + "',this)\"  class=\"validation-advice\">" + notice.value + "</div>";
           // Validation.insertAdvice(element, advice);
		   /*
            advice_action = advice_action + "FreaksForm.prototype.correctingItem('" + correctingitems[key] + "','" + true_address[key] + "'); $('" + correctingitems[key] + "_advice').hide();";
            advice_no_action = advice_no_action + "$('" + correctingitems[key] + "_advice').hide();";
        */
		advice_action = advice_action + "FreaksForm.prototype.correctingItem('" + correctingitems[key] + "','" + true_address[key] + "'); ";
            advice_no_action = advice_no_action ;
		}

		 /*else if (notice.key == 'street') {
		   element = $(correctingitems[notice.key]);

           advice = "<div id=\"" + correctingitems[notice.key] + "_advice\" onClick=\"FreaksForm.prototype.correctingItem('" + correctingitems[notice.key] + "','" + adress_field_value + "',this)\"  class=\"validation-advice\">" + notice.value + "</div>";
		   
		   advice_action = advice_action + "FreaksForm.prototype.correctingItem('" + correctingitems[notice.key] + "','" + adress_field_value + "');";
           advice_no_action = advice_no_action ;
		 
		 }*/
        else {
            element = $(correctingitems[notice.key]);

            advice = "<div id=\"" + correctingitems[notice.key] + "_advice\" onClick=\"FreaksForm.prototype.correctingItem('" + correctingitems[notice.key] + "','" + adress_field_value + "',this)\"  class=\"validation-advice\">" + notice.value + "</div>";
           // Validation.insertAdvice(element, advice);
		   
           /* 
		    advice_action = advice_action + "FreaksForm.prototype.correctingItem('" + correctingitems[notice.key] + "','" + adress_field_value + "'); $('" + correctingitems[notice.key] + "_advice').hide();";
            advice_no_action = advice_no_action + "$('" + correctingitems[notice.key] + "_advice').hide();";
           */
		   
		   advice_action = advice_action + "FreaksForm.prototype.correctingItem('" + correctingitems[notice.key] + "','" + adress_field_value + "'); ";
           advice_no_action = advice_no_action ;
        }
        advice_action = advice_action+"FreaksForm.prototype.correctingItem('" + correctingitems['street'] + "','" + street_field_value + "'); ";
		
        $('address_validate_notice_continue_' + formid + '').removeAttribute("onclick");
        $('address_validate_notice_validate_' + formid + '').removeAttribute("onclick");


        $('address_validate_notice_continue_' + formid + '').setAttribute("script", "" + advice_no_action + " ");
        $('address_validate_notice_validate_' + formid + '').setAttribute("script", "" + advice_action + " ");

        $('window-overlay_'+formid+'').show();
        $('validation-popup-address-notice_' + formid + '').show();

    });

};

			 
FreaksForm.prototype.correctingItem = function(elementId, value, advice){
       // alert(elementId);

        elementSelect = $$('select#'+elementId+'').first();
        
		if (elementSelect) {
            $$('select#'+elementId+' option').each(function(option) {
              if(option.readAttribute('value') == value) {
                option.selected = true;
                throw $break; 
              }
            });         
        } 
		else{        
		elementInput = $(elementId);
        if (elementInput) {
            elementInput.setValue(value);    
            }
        }      
    };	 
	