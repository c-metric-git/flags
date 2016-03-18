var SolrBridgeSearch = function(el, options){
  this.el = $(el);
  this.id = this.el.identify();
  this.el.setAttribute('autocomplete','off');
  this.suggestions = [];
  this.suggestionsPrice = [];
  this.specialPrices = [];
  this.suggestionsProductIds = [];
  this.suggestBrands = [];
  this.suggestCategories = [];
  this.suggestKeywords = [];
  this.productTypes = [];
  this.suggestKeywordsRaw = [];
  this.ajaxBaseUrl = null;
  this.queryFields = null;  
  this.SolrBridgeSearchmessage = null;
  this.timestamp = 0;
  this.data = [];
  this.badQueries = [];
  this.selectedIndex = -1;
  this.selectedItemIndex = 0;
  this.selectedProductId = null;
  this.currentValue = this.el.value;
  this.currentKeyword = null;
  this.intervalId = 0;
  this.cachedResponse = [];
  this.itemCount = 0;
  this.action = false;
  this.instanceId = null;
  this.onChangeInterval = null;
  this.ignoreValueChange = false;
  this.serviceUrl = options.serviceUrl;
  this.options = {
    autoSubmit:false,
    minChars:1,
    maxHeight:300,
    deferRequestBy:0,
    width:0,
    container:null,
    allowFilter:0,
	currencySign: '$',
    displayThumb:0
  };
  if(options){ Object.extend(this.options, options); }
  if(SolrBridgeSearch.isDomLoaded){
    this.initialize();
  }else{
    Event.observe(document, 'dom:loaded', this.initialize.bind(this), false);
  }
};

SolrBridgeSearch.instances = [];
SolrBridgeSearch.isDomLoaded = false;

SolrBridgeSearch.getInstance = function(id){
  var instances = SolrBridgeSearch.instances;
  var i = instances.length;
  while(i--){ if(instances[i].id === id){ return instances[i]; }}
};

SolrBridgeSearch.highlight = function(value, re){
	value = value.toString();
	return value.replace(re, function(match){ return '<strong>' + match + '<\/strong>' });
};

SolrBridgeSearch.prototype = {

  killerFn: null,

  initialize: function() {
    var me = this;
    this.killerFn = function(e) {
      if (!$(Event.element(e)).up('.autocomplete')) {
        me.killSuggestions();
        me.disableKillerFn();
      }
    } .bindAsEventListener(this);

    if (!this.options.width) { this.options.width = this.el.getWidth(); }
    
    //Create a div element
    this.box = new Element('div', { style: 'position:absolute;display:none;z-index:99999' });
    //Put some children div into parent div
    var divInner = new Element('div', {id: 'sbs_'+this.id+'_autocomplete_box'}).addClassName('sbs_autocomplete_inner');
    var divInnerRight = new Element('div', {id: 'sbs_'+this.id+'_autocomplete_right'}).addClassName('sbs_autocomplete_inner_right');
    divInner.appendChild(divInnerRight);
    
    var divInnerLeft = new Element('div', {id: 'sbs_'+this.id+'_autocomplete_left'}).addClassName('sbs_autocomplete_inner_left');
    divInner.appendChild(divInnerLeft);
    
    var divCloseButton = new Element('div', {id: 'sbs_'+this.id+'_closed_button'}).addClassName('sbs_autocomplete_close_button').update('&nbsp;');
    divInner.appendChild(divCloseButton);
    
    this.box.appendChild(divInner);
    
    //Append all div to body tag
    this.options.container = $(this.options.container);
    document.body.appendChild(this.box);
    
    //Get the div ID
    this.divId = this.box.identify();
    this.rightSideBar = $('sbs_'+this.id+'_autocomplete_right');
    this.container = $('sbs_'+this.id+'_autocomplete_box');
    this.closebutton = $('sbs_'+this.id+'_closed_button');
    this.leftSideBar = $('sbs_'+this.id+'_autocomplete_left');
    
    if (this.options.sideBarWidth) { this.leftSideBar.setStyle({width:'100%'}); }
    if (this.options.boxWidth) { this.container.setStyle({width:(this.options.boxWidth)+'px'}); }
    this.container.setStyle({padding:'0'});
    
    if(this.options.allowFilter == 1){
    	this.leftSideBar.show();
    }else{
    	this.leftSideBar.remove();
    }
    
    //This function called to set some css attributes to the parent div
    this.fixPosition();
    
    Event.observe(this.el, window.opera ? 'keypress':'keydown', this.onKeyPress.bind(this));
    Event.observe(this.el, 'keyup', this.onKeyUp.bind(this));
    Event.observe(this.el, 'click', this.onClick.bind(this));
    Event.observe(this.el, 'blur', this.enableKillerFn.bind(this));
    Event.observe(this.el, 'focus', this.fixPosition.bind(this));
    Event.observe(this.closebutton, 'click', this.closeAll.bind(this));
    Event.observe(window, "resize", this.fixPosition.bind(this));
    this.instanceId = SolrBridgeSearch.instances.push(this) - 1;
  },
  closeAll: function(){
	this.hide();
  },
  hide: function(){
	  this.box.hide();
  },
  show: function()
  {
	  this.box.show();
  },
  fixPosition: function() {
    var offset = this.el.cumulativeOffset();
    var top = offset.top + this.el.getHeight();
    
    var windowSize = document.viewport.getDimensions();
	var windowWidth = windowSize.width;
	
	var boxWidth = this.el.getWidth() - 5;
	
	if (parseInt(this.options.boxWidth) > 0 && parseInt(windowWidth) >= parseInt(this.options.boxWidth)) {
		boxWidth = this.options.boxWidth;
		var left = offset.left - parseInt(this.options.boxWidth) + this.el.getWidth() - 10;
	}else{
		boxWidth = this.el.getWidth() - 5;
		var left = offset.left;
	}
	var x = windowWidth - boxWidth;

	if(x < 0) {
		boxWidth = this.el.getWidth() - 5;
		var left = offset.left;
	}
	this.container.setStyle({width:(boxWidth)+'px'});
	$(this.box).setStyle({ top: (top) + 'px', left: (left) + 'px' });
    this.closebutton.setStyle({ top: '-10px', left: (boxWidth - 12)+ 'px' });
  },

  enableKillerFn: function() {
    Event.observe(document.body, 'click', this.killerFn);
  },

  disableKillerFn: function() {
    Event.stopObserving(document.body, 'click', this.killerFn);
  },

  killSuggestions: function() {
    this.stopKillSuggestions();
    this.intervalId = window.setInterval(function() { this.hide(); this.stopKillSuggestions(); } .bind(this), 1);
  },

  stopKillSuggestions: function() {
    window.clearInterval(this.intervalId);
  },
  onKeyPress: function(e) {
    if (!this.enabled) { return; }
    // return will exit the function
    // and event will not fire
    switch (e.keyCode) {
      case Event.KEY_ESC:
        this.el.value = this.currentValue;
        this.hide();
        break;
      case Event.KEY_TAB:
      case Event.KEY_RETURN:
    	  if (this.selectedIndex === -1) {
          this.hide();
          return;
        }
        this.enterSelect(this.selectedItemIndex);
        if (e.keyCode === Event.KEY_TAB) { return; }
        break;
      case Event.KEY_UP:
        this.moveUp();
        break;
      case Event.KEY_DOWN:
        this.moveDown();
        break;
      default:
        return;
    }
    Event.stop(e);
  },

  onKeyUp: function(e) {
    switch (e.keyCode) {
      case Event.KEY_UP:
      case Event.KEY_DOWN:
        return;
    }
    clearInterval(this.onChangeInterval);
    if (this.currentValue !== this.el.value) {
      if (this.options.deferRequestBy > 0) {
        // Defer lookup in case when value changes very quickly:
        this.onChangeInterval = setInterval((function() {
          this.onValueChange();
        }).bind(this), this.options.deferRequestBy);
      } else {
        this.onValueChange();
      }
    }
  },

  onValueChange: function() {
    clearInterval(this.onChangeInterval);
    this.currentValue = this.el.value;
    this.selectedIndex = -1;
    if (this.ignoreValueChange) {
      this.ignoreValueChange = false;
      return;
    }
    this.suggestions = [];
    if (this.currentValue === '' || this.currentValue.length < this.options.minChars) {
    	this.hide();
    } else {
    	this.getSuggestions();
    }
  },
  onClick: function(){
	  this.suggestions = [];
	  if (this.currentValue === '' || this.currentValue.length < this.options.minChars) {
	    	this.hide();
	    } else {
	    	this.getSuggestions();
	    }
  },
  getSuggestions: function() {
	if(this.currentValue == this.options.searchTextPlaceHolder) {
		return false;
	}
	
	var timestamp = new Date().getTime();
	var requestParams = {
			'q':this.currentValue,
			'storeid':this.options.storeid,
			'customergroupid':this.options.customergroupid,
			'storetimestamp':this.options.storetimestamp,
			'currencycode':this.options.currencycode,
			'timestamp': timestamp
	};
	this.timestamp = timestamp;
  	this.doRequest(this.options.ajaxBaseUrl+'/sb.php',requestParams);
  },
  doRequest: function(url,params)
  {
	  var me = this;
	  var ajaxRequest = new Ajax.Request(url, {
		  method:'get',
		  evalJSON:'force',
		  parameters: params,
		  loaderArea: false,
		  onSuccess: function(transport){
			  //Error occur
			  if(transport.responseJSON === undefined)
			  {
				  //alert(transport.responseText);
				  return;
			  }
			  me.processResponse(transport.responseJSON);
		   }
		});
  },

  isBadQuery: function(q) {
    var i = this.badQueries.length;
    while (i--) {
      if (q.indexOf(this.badQueries[i]) === 0) { return true; }
    }
    return false;
  },
  formatPrice: function(price)
  {
	  	var formattedPrice = price;
		
		if ((price !== undefined) && (price !== null)) {
		if(this.options.currencyPos == 'before'){
			formattedPrice = this.options.currencySign+price;
		}else{
			formattedPrice = price+this.options.currencySign;
		}
		}else{
			formattedPrice = '&nbsp;';
		}
		return formattedPrice;
  },
  suggest: function() {
    if (this.suggestions.length === 0 && this.currentValue.length == 0) {
      this.hide();
      this.container.hide();
      return;
    }
    var content = [];
    
    //var re = new RegExp('\\b' + this.currentKeyword.match(/\w+/g).join('|\\b'), 'gi');

    var i = -1;
    for(key in this.suggestions)
	{
    	if(!isNaN(key)){
    		var value = this.suggestions[key];
    		//var value = rawvalue.replace(/(<([^>]+)>)/ig,"").replace('"','&quot;');
    		var price = this.suggestionsPrice[key];
    		var specialPrice = this.specialPrices[key];
    		
    		var formattedPrice = this.formatPrice(price);
    		var formattedSpecialPrice = this.formatPrice(specialPrice);
    		
    		if(this.productTypes[key] == 'bundle'){
    			formattedPrice = this.options.fromPriceText+'&nbsp;'+formattedPrice;
    			formattedSpecialPrice = this.options.fromPriceText+'&nbsp;'+formattedPrice;
    		}
    		
    		var elementId = 'sbs_'+this.id+'_suggest_index_'+key;
    		var elementClassName = (this.selectedIndex === i) ? 'product suggested-item' : 'product suggested-item';
    		var itemImage = '<div class="sbs_search_suggest_thumb"><img src="'+this.options.ajaxBaseUrl+'/media/catalog/product/sb_thumb/'+this.suggestionsProductIds[key]+'.jpg"/></div>';
    		
    		//Load product prices by Ajax
    		if( undefined != this.options.ajaxprice && this.options.ajaxprice > 0 ) {
    			var itemPrice = '<span class="sbs_search_suggest_item_subtitle special" id="sbs_search_special_price_'+this.suggestionsProductIds[key]+'">&nbsp;</span><br/>';
    			itemPrice += '<span class="origin"><span class="sbs_search_suggest_item_subtitle old-price" id="sbs_search_regular_price_'+this.suggestionsProductIds[key]+'"><img src="/js/solrsearch/ajax-loader.gif" /></span></span>';
    		} else {
    			if(parseInt(specialPrice) > 0)
        		{
        			var itemPrice = '<span class="sbs_search_suggest_item_subtitle old-price">'+formattedPrice+'</span>';
        			itemPrice += '<span class="sbs_search_suggest_item_subtitle special-price">'+formattedSpecialPrice+'</span>';
        		}else{
        			var itemPrice = '<span class="sbs_search_suggest_item_subtitle">'+formattedPrice+'</span>';
        		}
    		}
    		
    		
    		var itemTitle = '<span class="sbs_search_suggest_item_title">'+value+'</span>';
    		
        	if(this.options.displayThumb == 1){
        		var itemDiv = new Element('div', {'id':elementId, 'title':value}).addClassName(elementClassName).update(itemImage+itemTitle+'<br/>'+itemPrice+'<br/>');
        		Event.observe(itemDiv, 'click', this.select.bind(this, key, itemDiv));
        		Event.observe(itemDiv, 'mouseout', this.onMouseOut.bind(this, itemDiv));
        		Event.observe(itemDiv, 'mouseover', this.onMouseOver.bind(this, itemDiv));
        		content.push(itemDiv);
        	}else{
        		var itemDiv = new Element('div', {'id':elementId, 'title':value}).addClassName(elementClassName).update(itemTitle+'<br/>'+itemPrice+'<br/>');
        		Event.observe(itemDiv, 'click', this.select.bind(this, key, itemDiv));
        		Event.observe(itemDiv, 'mouseout', this.onMouseOut.bind(this, itemDiv));
        		Event.observe(itemDiv, 'mouseover', this.onMouseOver.bind(this, itemDiv));
        		content.push(itemDiv);
        	}
    		
        	i++;
        	this.itemCount++;
    	}    	
	}

    this.enabled = true;
    this.container.setStyle('display:block');
    if(this.suggestions.length > 0){
    	this.rightSideBar.update('<div class="suggest_product_items suggest_divider sbs_autocomplete_message">'+this.SolrBridgeSearchmessage+'</div>');
    	this.itemCount++;
    	//Keywords
    	if(this.options.displaykeywordsuggestion && this.suggestKeywords.length > 0)
    	{
    		//this.rightSideBar.appendChild(new Element('div').addClassName('suggest_category_items suggest_divider').update(this.options.keywordsText));
        	for(var key_word in this.suggestKeywords)
        	{
        		if(!isNaN(key_word)){
        			var keywordString = this.suggestKeywords[key_word];
        			
        			var keywordStringRaw = this.suggestKeywordsRaw[key_word];
        			
        			var keywordItem = new Element('div',{id:'sbs_'+this.id+'_keyword_index_'+key_word,style:'cursor:pointer;',onclick:'SolrBridgeSearch.instances['+this.instanceId+'].select('+key_word+', this)',onmouseover:'$(this).addClassName("selected")',onmouseout:'$(this).removeClassName("selected")'}).addClassName('keywords suggested-item');
        			keywordItem.update('<span class="sbs_search_suggest_item_title">'+keywordString+'</span>');    		
    	    		this.rightSideBar.appendChild(keywordItem);
    	    		this.itemCount++;
        		}
        	}
        	this.rightSideBar.appendChild(new Element('div').addClassName('suggest_category_items suggest_divider').update(this.options.productsText));
        	this.itemCount++;
    	}
    	
    	//Products
    	var rightSideBarDiv = this.rightSideBar;
    	content.each(function(item){
    		rightSideBarDiv.appendChild(item);
    	});
    	
    	//Brands
    	if(parseInt(this.options.showBrand) > 0 && this.suggestBrands.length > 0){
    		var viewAllBrandUrl = this.options.searchResultUrl+'/by/brand';
    		var searchByBrand = new Element('span').addClassName('view-all-brand').update('<a href="'+viewAllBrandUrl+'">'+this.options.viewAllBrandsText+'</a>');
    		var brandDivider = new Element('div').addClassName('suggest_category_items suggest_divider');
    		var brandDividerText = new Element('span').update(this.options.brandText);
    		brandDivider.appendChild(brandDividerText);
    		brandDivider.appendChild(searchByBrand);
    		this.rightSideBar.appendChild(brandDivider);
    		this.itemCount++;
    		var brandIndex = 1;
    		for(key_brand in this.suggestBrands)
        	{
    			if(!isNaN(key_brand)){
        			var brandString = this.suggestBrands[key_brand][0];
        			
        			var productCount = this.suggestBrands[key_brand][1];
        			var productCountFormatted = productCount+'&nbsp;'+this.options.productText;
        			if(parseInt(productCount) > 1)
        			{
        				productCountFormatted = productCount+'&nbsp;'+this.options.productsText;
        			}
        			var reg = new RegExp('\\b' + this.currentKeyword.match(/\w+/g).join('|\\b'), 'gi');
        			var brandStringFormatted = SolrBridgeSearch.highlight(brandString, reg);
        			var brandItem = new Element('div',{id:'sbs_'+this.id+'_brand_index_'+key_brand,style:'cursor:pointer;',onclick:'SolrBridgeSearch.instances['+this.instanceId+'].select('+key_brand+', this)',onmouseover:'$(this).addClassName("selected")',onmouseout:'$(this).removeClassName("selected")'}).addClassName('brand suggested-item');
        			brandItem.update('<span class="sbs_search_suggest_item_title">'+brandStringFormatted+'</span><br/><span class="sbs_search_suggest_item_subtitle">'+productCountFormatted+'</span>');
    	    			    		
    	    		this.rightSideBar.appendChild(brandItem);
    	    		
    	    		if(brandIndex >= parseInt(this.options.brandLimit)) break;
    	    		
    	    		brandIndex++;
    	    		this.itemCount++;
        		}
        	}
    	}

    	//Category
    	if(this.suggestCategories.length > 0){
    		var viewAllCategoryUrl = this.options.searchResultUrl+'/by/category';
    		var searchByCategory = new Element('span').addClassName('view-all-category').update('<a href="'+viewAllCategoryUrl+'">'+this.options.viewAllCategoryText+'</a>');
    		var categoryDivider = new Element('div').addClassName('suggest_category_items suggest_divider');
    		var categoryDividerText = new Element('span').update(this.options.categoryText);
    		categoryDivider.appendChild(categoryDividerText);
    		categoryDivider.appendChild(searchByCategory);
    		this.rightSideBar.appendChild(categoryDivider);
    		this.itemCount++;
        	var catIndex = 1;
    		for(key_cat in this.suggestCategories)
        	{
        		if(!isNaN(key_cat)){
        			var categoryString = this.suggestCategories[key_cat][0];
        			
        			var categoryArray = categoryString.split('/');
        			
        			var catPathArray = [];
        			
        			for (var index = 0; index < categoryArray.length; ++index) {
        				if( (index%2) == 0)
        				{
        					catPathArray.push(categoryArray[index]);
        				}
        			}   	    	
        	    	
        	    	catPath = catPathArray.join('&nbsp;>&nbsp;');
        	    	
        	    	catPath = catPath.replace(/_._._/g,"/");
        	    	
        	    	var reg = new RegExp('\\b' + this.currentKeyword.match(/\w+/g).join('|\\b'), 'gi');
        			var catPathFormatted = SolrBridgeSearch.highlight(catPath, reg);
        	    	
        	    	var productCount = this.suggestCategories[key_cat][1];
        			var productCountFormatted = productCount+'&nbsp;'+this.options.productText;
        			if(parseInt(productCount) > 1)
        			{
        				productCountFormatted = productCount+'&nbsp;'+this.options.productsText;
        			}
        	    	
        			var categoryItem = new Element('div',{id:'sbs_'+this.id+'_category_index_'+key_cat,style:'cursor:pointer;',onclick:'SolrBridgeSearch.instances['+this.instanceId+'].select('+key_cat+', this)',onmouseover:'$(this).addClassName("selected")',onmouseout:'$(this).removeClassName("selected")'}).addClassName('category suggested-item');
    	    		categoryItem.update('<span class="sbs_search_suggest_item_title">'+catPathFormatted+'</span><br/><span class="sbs_search_suggest_item_subtitle">'+productCountFormatted+'</span>');
    	    			    		
    	    		this.rightSideBar.appendChild(categoryItem);
    	    		
    	    		if(catIndex >= parseInt(this.options.categoryLimit)) break;
    	    		
    	    		catIndex++;
    	    		this.itemCount++;
        		}
        	}
    	}
    	
    	if(!document.getElementById('sbs_'+this.id+'_view_all_link')){
    		
    		var bottomDiv = new Element('div').addClassName('sbs_search_autocomplete_box_bottom');
        	bottomDiv.update('<span id="sbs_'+this.id+'_view_all_link"></span>');
        	
        	this.container.appendChild(bottomDiv);
    	}
    	
    	
    	var viewAllLink = this.options.searchResultUrl+'?q='+this.currentKeyword;
    	  
    	$('sbs_'+this.id+'_view_all_link').update('<a href="'+encodeURI(viewAllLink)+'">'+this.options.viewAllResultText.replace('%s', '<b>'+this.currentKeyword+' >></b>'));
    	
    	this.show();
    	
    	//Load AJAX prices
    	if( undefined != this.options.ajaxprice && this.options.ajaxprice > 0 ) {
    		var productIds = this.suggestionsProductIds;
    		var url = this.options.searchResultUrl+'/ajax/prices?ids='+productIds.join(',');
    		var me = this;
    		var ajaxRequest = new Ajax.Request(url, {
    			  method:'get',
    			  evalJSON:'force',
    			  parameters: {},
    			  loaderArea: false,
    			  onSuccess: function(transport){
    				  //Error occur
    				  if(transport.responseJSON === undefined) {
    					  alert(transport.responseText);
    					  return;
    				  }
    				
    				  Object.keys(transport.responseJSON).each(function(k){
    					var item = transport.responseJSON[k];
    					$('sbs_search_regular_price_'+k).update(me.formatPrice(item.price));
    					if(parseInt(item.special_price) < parseInt(item.price)) {
    						if(document.getElementById('sbs_search_special_price_'+k)){
    							$('sbs_search_special_price_'+k).update(me.formatPrice(item.special_price));
    						}
    					}else {
    						if(document.getElementById('sbs_search_special_price_'+k)){
    							$('sbs_search_special_price_'+k).next().remove();
    							$('sbs_search_special_price_'+k).remove();
    						}
    						var parent = $('sbs_search_regular_price_'+k).removeClassName('old-price').up();
    						$(parent).removeClassName('origin');
    					}
    				});
    			  }
    		});
    	}
    	
    	return;
    }else{
    	this.hide();
		return;
    }
  },

  processResponse: function(response) {
	  this.suggestions = [];
	  if(typeof response === 'undefined' && this.currentValue.length == 0){
		  this.hide();
		  return;
	  }
	  if(response && (response.response.docs.length < 1 || this.currentValue.length == 0 || response.responseHeader.params.timestamp != this.timestamp)){
		  this.hide();
		  return;
	  }
	  
	  /**
	   * Loop to push doc name into suggestions array
	   */
	  var i = 0;
	  if(response && response.responseHeader.params.q){	  
		  var keyword = response.responseHeader.params.q;
		  
		  //Collect product list
		  this.suggestions = [];
		  
		  this.suggestionsProductIds = [];
		  for (var index = 0; index < response.response.docs.length; ++index)
		  {	
			product_id = response.response.docs[index].products_id;			
			this.suggestions[i] = response.response.docs[index].name_varchar;
			
			this.suggestionsPrice[i] = response.response.docs[index].price_decimal;
			
			this.specialPrices[i] = response.response.docs[index].special_price_decimal;
			
			this.productTypes[i] = response.response.docs[index].product_type_static;
			
			this.suggestionsProductIds[i] = product_id;
			i++;
			if(i >= 20){
				break;
			}
		  }
		  this.SolrBridgeSearchmessage = this.options.displayResultOfText.replace('%s', '<b>'+response.responseHeader.params.q+'</b>');
		  if(response.responseHeader.params.q != this.currentValue.toLowerCase()){
			  this.SolrBridgeSearchmessage = this.options.displayResultOfInsteadText.replace('%s', '<b>'+response.responseHeader.params.q+'</b>');
		  }
		  this.currentKeyword = response.responseHeader.params.q;
		  
		  //Collection categories
		  this.suggestCategories = [];
		  var cats = response.facet_counts.facet_fields.category_path;		  
		  var index = 0;		  
		  //var re = new RegExp('\\b' + this.currentKeyword.match(/\w+/g).join('|\\b'), 'gi');
		  
		  for(key in cats) {
	    	if(cats[key] < 1 || isNaN(cats[key])){
	    		continue;
	    	}  
	    	this.suggestCategories[i] = [key,cats[key]];
	    	i++;
	    	
	    	if(index >= 5){
	    		break;
	    	}
	    	index++;
		  }
		  
		  //Collect keywords
		  this.suggestKeywords = [];
		  this.suggestKeywords = response.keywordssuggestions;
		  this.suggestKeywordsRaw = [];
		  this.suggestKeywordsRaw = response.keywordsraw;
		  
		  //Collect brands
		  if(this.options.showBrand && typeof response.facet_counts.facet_fields !== 'undefined'){
			  
			  this.suggestBrands = [];
			  
			  for(key in response.facet_counts.facet_fields) {
				if(key == this.options.showBrandAttributeCode+'_facet')
				{
					var brands = response.facet_counts.facet_fields[key];
					var i = 0;
					
					for(key in brands) {
				    	if(brands[key] < 1 || isNaN(brands[key]) ){
				    		continue;
				    	}  
				    	this.suggestBrands[i] = [key,brands[key]];
				    	i++;
					 }
					break;
				}
			  }
		  }
		  
	  }
	  
	  this.suggest();
  },
  redirectToUrl: function(url){
	  window.location = url;
  },
  redirectToProduct: function(productid){
	  window.location=this.options.searchResultUrl+'/ajax/product?productid='+productid+'&currency='+this.options.currencycode;
  },
  redirectToBrand: function(brand){
	  var brandLink = this.options.searchResultUrl+'?q='+this.currentKeyword+'&fq['+this.options.showBrandAttributeCode+']='+encodeURIComponent(brand);
	  window.location = brandLink;
	  return true;
  },
  redirectToKeyword: function(keyword){
	  var keywordLink = this.options.searchResultUrl+'?q='+encodeURIComponent(keyword);
	  window.location = keywordLink;
	  return true;
  },
  redirectToCategory: function(category){
	  var start = 0;
	  var end = category.lastIndexOf("/");
	  var categoryString = category.substring(start, end);
	  var currentCatName = categoryString.substring(categoryString.lastIndexOf("/") + 1,categoryString.length);
	  var currentCatId = category.substring(category.lastIndexOf("/") + 1,category.length);
	  if(parseInt(this.options.categoryRedirect) > 0){
		  window.location=this.options.searchResultUrl+'/ajax/category?cat_id='+currentCatId;
		  return true;
	  }else{
		  var catLink = this.options.searchResultUrl+'?q='+this.currentKeyword+'&fq[category]='+currentCatName+'&fq[category_id]='+currentCatId;
		  window.location = catLink;
	  }
	  return true;
  },
  activate: function(index) 
  {
    var divs = this.rightSideBar.childNodes;
    var activeItem;
    
    //this.action = false meaning move down and vice verse
    if( ((divs.length) - 1) === this.selectedIndex && !this.action) return;
    if( this.selectedIndex < 1 && this.action) return;
    
    // Clear previous selection:
    if (this.selectedIndex !== -1 && (divs.length) > this.selectedIndex)
    {
    	if(!divs[this.selectedIndex].hasClassName('suggest_divider')){
    		var classnames = divs[this.selectedIndex].className + ' suggested-item';
        	classnames = classnames.split(' ');
        	classnames = classnames.uniq();
        	divs[this.selectedIndex].className = classnames.join(' ');
    	}	
    	$(divs[this.selectedIndex]).removeClassName('selected');
    }
    if(typeof divs[index] === 'undefined'){
    	return;
    }
	this.selectedIndex = index;
    if (this.selectedIndex !== -1 && divs.length > this.selectedIndex)
    {
      activeItem = divs[this.selectedIndex]
      var tempclassnames = activeItem.className + ' selected';
      tempclassnames = tempclassnames.split(' ');
      tempclassnames = tempclassnames.uniq();
      activeItem.className = tempclassnames.join(' ');
    }
    
    return activeItem;
  },
  deactivate: function(div, index)
  {
    div.removeClassName('selected');
    if (this.selectedIndex === index) { this.selectedIndex = -1; }
  },
  select: function(i, obj)
  {
	var divs = this.rightSideBar.childNodes;
	var index = parseInt(i)+1;	
	var selectedValue = this.suggestions[i];
	
	var itemId = obj.id;	
	
	if ($(itemId).hasClassName('product')){
		//this.redirectToUrl(productPath);
		var productid = this.suggestionsProductIds[i];
		this.redirectToProduct(productid);
	}else if ($(itemId).hasClassName('category')){
		var selectedValue = this.suggestCategories[i][0];
		this.redirectToCategory(selectedValue);
	}else if ($(itemId).hasClassName('brand')){
		var selectedValue = this.suggestBrands[i][0];
		this.redirectToBrand(selectedValue);
	}else if ($(itemId).hasClassName('keywords')){
		var selectedValue = this.suggestKeywordsRaw[i];
		this.redirectToKeyword(selectedValue);
	}else{
		if($(itemId) != undefined) {
			var productid = this.suggestionsProductIds[i];
			this.redirectToProduct(productid);
			return;
		}else{
			return;
		}		
	}
	return true;
  },
  enterSelect: function( selectedIndex ) {
	  var divs = this.rightSideBar.childNodes;
	  selectedNode = divs[this.selectedIndex];
	  
	  if(typeof selectedNode.id !== 'undefined' && selectedNode.id.length > 0)
	  {
		  var end = selectedNode.id.length;
		  var start = selectedNode.id.lastIndexOf("_");
		  var index = selectedNode.id.substring((start+1), end);
		  
		  this.select(index, selectedNode);
	  }
	  return true;
  },
  moveUp: function() {
	this.action = true;
	var num = this.itemCount;
    this.adjustScroll(this.selectedIndex - 1);
  },

  moveDown: function()
  {
	this.action = false;
	var num = this.itemCount;
    this.adjustScroll(this.selectedIndex + 1);
  },

  adjustScroll: function(i) {	
    var container = this.rightSideBar;
    var activeItem = this.activate(i);
    if(typeof activeItem === 'undefined') return;
    var offsetTop = activeItem.offsetTop;
    var upperBound = container.scrollTop;
    var lowerBound = upperBound + this.options.maxHeight - 25;
    if (offsetTop < upperBound) {
      container.scrollTop = offsetTop;
    } else if (offsetTop > lowerBound) {
      container.scrollTop = offsetTop - this.options.maxHeight + 25;
    }
  },

  onSelect: function(i) {
    (this.options.onSelect || Prototype.emptyFunction)(this.suggestions[i], this.data[i]);
  },
  onMouseOut: function(obj){
	  $(obj).removeClassName('selected').addClassName('suggested-item');
  },
  onMouseOver: function(obj){
	  $(obj).addClassName('selected');
  }

};
function sbsexpandmenu(parent) 
{
	var mode = parent.getElementsByTagName("ul")[0].getAttribute("expanded");
	mode = mode == 1;
	(mode) ? sbscollapse(parent) : sbsexpand(parent) ;
}
function sbsexpand(parent) 
{
	parent.getElementsByTagName("ul")[0].style.display = "block";
	parent.getElementsByTagName("span")[0].style.backgroundPosition = "right center";
	parent.getElementsByTagName("ul")[0].setAttribute("expanded", "1");
}
function sbscollapse(parent) 
{
	parent.getElementsByTagName("ul")[0].style.display = "none";
	parent.getElementsByTagName("span")[0].style.backgroundPosition = "left center";
	parent.getElementsByTagName("ul")[0].setAttribute("expanded", "0");
}
Event.observe(document, 'dom:loaded', function(){ SolrBridgeSearch.isDomLoaded = true; }, false);