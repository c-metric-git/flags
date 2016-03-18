var sbsajax_working  = false;
var sbsajaxblocks   = {};

function sbsajax_init(){
    $$('div.block-layered-nav a:not(.sb-more-less-button)', sbsajax_toolbar_selector + ' a').
        each(function(e){
            e.onclick = function(){
                var s = this.href;
                if (s.indexOf('#') > 0){
                	s = s.substring(0, s.indexOf('#'))
                }
                sbsajax_ajax_request(s);
                return false;
            };
        });
    
    $$('div.block-layered-nav select', sbsajax_toolbar_selector + ' select').
    each(function(e){
        e.onchange = 'return false';
        Event.observe(e, 'change', function(e){
            sbsajax_ajax_request(this.value);
            Event.stop(e);
        });
    });
    
    if (typeof(sbsajax_external) != 'undefined'){
        sbsajax_external();    
    }
}

function sbsajax_get_created_container()
{
    var element = document.getElementById('solr_search_result_page_container');
    return element;
    
}

function sbsajax_get_container()
{
	var createdElement = sbsajax_get_created_container();
	if (!createdElement) {
		var container_element = null;
		
		var elements = $$('div.category-products');
		if (elements.length == 0) {
			container_element = sbsajax_get_empty_container();
		} else {
			container_element = elements[0];
		}
		
		if (!container_element) {
            alert('Please add the <div class="solrbridge-page-container"> to the list template as per installtion guide. Enable template hints to find the right file if needed.');
        }	
        
		container_element.wrap('div', { 'class': 'solrbridge-page-container', 'id' : 'solr_search_result_page_container' });
		
		
		createdElement = sbsajax_get_created_container();
	}
	
	var elements = document.getElementsByClassName('solrbridge-overlay');
	if(elements.length < 1)
	{
		var overlayElement = new Element('div').addClassName('solrbridge-overlay').update('<div></div>').hide();
		createdElement.appendChild(overlayElement);
	}
	
	return createdElement;
}

function sbsajax_get_empty_container()
{
	var notes = document.getElementsByClassName('note-msg');
	if (notes.length == 1) {
		return notes[0];
	}
}

/*
 * Get location object from string 
 */
var sbsajax_get_location_from_string = function(href) {
    var l = document.createElement("a");
    l.href = href;
    return l;
};



function sbsajax_ajax_request(url){
    
	if (sbsajax_use_hash) {
		
		sbsajax_skip_hash_change = true;
		var url = url.replace(window.top.location.protocol + '//' + window.top.location.host, '');
		window.top.location.hash = encodeURIComponent(url); 
						
		
		if (typeof amscroll_object != 'undefined') {
			amscroll_object.setHashParam('page', null);
			amscroll_object.setHashParam('top', null);
		}
			
		if (typeof amscroll_object != 'undefined') {
			var tmpUrl = window.top.location.protocol + '//' + window.top.location.host + url;
			amscroll_params.url = tmpUrl;
			amscroll_object.setUrl(tmpUrl);
		}
	}
	
    var block = sbsajax_get_container();
    
    //scroll top;
    window.scrollTo(0, 0);

    sbsajax_working = true;
    
    $$('div.solrbridge-overlay').each(function(e){
        e.show();
    });
    
    var request = new Ajax.Request(url,{
            method: 'get',
            parameters:{'is_ajax':1},
            onSuccess: function(response){
            	var data = '';
                data = response.responseText;
                
                if(!data.isJSON()){
                    setLocation(url);
                }
                
                data = data.evalJSON();
                
                if (!data.page || !data.blocks)
                {
                    setLocation(url);
                }
                //console.log(data);
                sbsajax_ajax_update(data);
                sbsajax_working = false;
                sbsajax_skip_hash_change = false;
                //sbsajax_init();
            },
            onFailure: function(){
                sbsajax_working = false;
                setLocation(url);
            }
        }
    );
}

function sbsajax_get_first_descendant(element) {
	
	var targetElement = element.firstChild;
	if(typeof element.firstDescendant != "undefined") {
		targetElement = element.firstDescendant();
	}
	return targetElement;
}

function sbsajax_ajax_update(data){

    //update category (we need all category as some filters changes description)
    var tmp = document.createElement('div');
    tmp.innerHTML = data.page;
    
    
    var block = sbsajax_get_container();
    if (block) {
    	var targetElement = sbsajax_get_first_descendant(tmp);
    	block.parentNode.replaceChild(targetElement, block);
    }

    var blocks = data.blocks;
    for (var id in blocks){
        var html   = blocks[id];
        if (html){
            tmp.innerHTML = html;
        }
        
        block = $(id);
        if (html){
            if (block){
            	var targetElement = sbsajax_get_first_descendant(tmp);
            	block.parentNode.replaceChild(targetElement, block);
            }
        }
        else { // no filters returned, need to remove
            if (block){
                var empty = document.createTextNode('');
                sbsajax_blocks[id] = empty; // remember the block in the DOM structure
                block.parentNode.replaceChild(empty, block);        
            }
        }  
    }
    sbsajax_init();
    solrbridge_start();
}

function sbsajax_request_required()
{	
	if (typeof amscroll_object != 'undefined') {
		
		if (sbsajax_use_hash && window.top.location.hash) {
			var hash = amscroll_object.getUrlParam();
			for (var item in hash) {
				if (!hash.hasOwnProperty(item)) {
				  continue;
				}
				if (item != 'page' && item != 'top') {
					return true;
				}
			}
		}
	} else {
		if (sbsajax_use_hash && window.top.location.hash) {
			return true;
		}
	}
	return false;	
}


document.observe("dom:loaded", function(event) {
	
	sbsajax_init();
	
	if (sbsajax_request_required()) {
		var hash = decodeURIComponent(window.top.location.hash.substr(1));
		var url = window.top.location.protocol + '//' + window.top.location.host;
		
		url = url + hash;
		
		sbsajax_ajax_request(url);
	} 
});

var sbsajax_toolbar_selector = 'div.toolbar';
var sbsajax_scroll_to_products = true;
var sbsajax_use_hash = false;
var sbsajax_skip_hash_change = false;

var AnchorChecker = {
		initialize: function(){
			this.location = window.top.location.hash;
		    this.interval = setInterval(function(){
		    	
		     if (this.location != window.top.location.hash) 
		     {
		    	 if (this.location != '') { 
		    		 this.anchorAltered();
		    	 }
		    	 this.location = window.top.location.hash;
		     }
		   }.bind(this), 500); // check every half second
		 },
		 anchorAltered: function(){
			 if (!sbsajax_skip_hash_change) {
				 sbsajax_ajax_request(decodeURIComponent(window.top.location.hash.substr(1)));
			 }
		 }
	};
if (sbsajax_use_hash) {
		AnchorChecker.initialize();
}

function sbsajax_external(){
    //add here all external scripts for page reloading
    // like igImgPreviewInit(); 
	if (typeof amscroll_object != 'undefined') {
		amscroll_object.init(amscroll_params); 
		amscroll_object.bindClick();
	}
	
	if (typeof sbsajax_demo != 'undefined') {
		sbsajax_demo();
	}
}