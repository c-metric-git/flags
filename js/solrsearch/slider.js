function solrbridge_start(){
    $$('.block-layered-nav .form-button').each(function (e){
        e.observe('click', solrbridge_price_click_callback);
    });

    $$('.block-layered-nav .input-text').each(function (e){
        e.observe('focus', solrbridge_price_focus_callback);
        e.observe('keypress', solrbridge_price_click_callback);
    });
    
    $$('.solrbridge-slider-param').each(function (item) {
    	var param = item.value.split(':');
        solrbridge_slider(param[0], param[1], param[2], param[3], param[4], param[5], param[6]);
    });
} 

function solrbridge_price_click_callback(evt) {
	
    if (evt && evt.type == 'keypress' && 13 != evt.keyCode)
        return;
    
    var prefix = 'solrbridge-price';
    // from slider
    if (typeof(evt) == 'string'){
        prefix = evt;    
    } 
    else {
        var el = Event.findElement(evt, 'input');
        if (!Object.isElement(el)){
            el = Event.findElement(evt, 'button');
        }
        prefix = el.name;
    }  
    
    var a = prefix + '-from';
    var b = prefix + '-to';
    //var c = prefix + '-placeholder';
    var min = prefix + '-min';
    min = $(min).value;
    var max = prefix + '-max';
    max = $(max).value;
    
    var numFrom = solrbridge_price_format($(a).value);
    var numTo   = solrbridge_price_format($(b).value);
  
    if ((numFrom < 0.01 && numTo < 0.01) || numFrom < 0 || numTo < 0) {   
        return;   
    }
    var search = min+'TO'+max;
    var replace = numFrom+'+TO+'+numTo;
    var url =  $(prefix +'-url').value.gsub(a, numFrom).gsub(b, numTo);
    url = url.replace(search, replace);
    url = url.replace('MINPRICE', min).replace('MAXPRICE', max);
    solrbridge_set_location(url);
}

function solrbridge_price_focus_callback(evt){
    var el = Event.findElement(evt, 'input');
    if (isNaN(parseFloat(el.value))){
        el.value = '';
    } 
}


function solrbridge_price_format(num){
    num = parseFloat(num);

    if (isNaN(num))
        num = 0;
        

    return Math.round(num);
}


function solrbridge_slider(width, from, to, max_value, prefix, min_value, ratePP) {
	
	width = parseFloat(width);
	from = parseFloat(from);
	to = parseFloat(to);
	max_value = parseFloat(max_value);
	min_value = parseFloat(min_value);
	
	var slider = $(prefix);
      return new Control.Slider(slider.select('.handle'), slider, {
        range: $R(0, width),
        sliderValue: [from, to],
        restricted: true,
        //values: allowedVals,
        
        onChange: function (values){
          this.onSlide(values);  
          solrbridge_price_click_callback(prefix);  
        },
        onSlide: function(values) {
      	
      	var fromValue = Math.round(min_value + ratePP * values[0]);
      	var toValue = Math.round(min_value + ratePP * values[1]);
      	/*
      	 * Change current selection style
      	 */
       if ($(prefix + '-slider-bar')) {
    	  var barWidth = values[1] - values[0] - 1;
    	  if (values[1] == values[0]) {
    		  barWidth = 0;
    	  }
      	$(prefix + '-slider-bar').setStyle(
          {
            width : barWidth + 'px',
            left : values[0] + 'px'
          }
        );
       }
      	
      	
      	if ($(prefix+'-from')) {
  	        $(prefix+'-from').value = fromValue;
  	        $(prefix+'-to').value   = toValue;
      	}
      	
          if ($(prefix + '-from-slider')) {
  	        $(prefix + '-from-slider').update(fromValue);
  	        $(prefix + '-to-slider').update(toValue);
          }
        }
      });
    
    

}

function solrbridge_getQueryParams(url){
	var regex = /[?&]([^=#]+)=([^&#]*)/g,
    params = {},
    match;
	while(match = regex.exec(url)) {
	    params[match[1]] = match[2];
	}
	return params;
}

function solrbridge_set_location(url){
    if (typeof sbsajax_working != 'undefined'){
        return sbsajax_ajax_request(url);    
    }
    else
        return setLocation(url);
}


document.observe("dom:loaded", function() { solrbridge_start(); });