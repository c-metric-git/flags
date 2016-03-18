jQuery( document ).ready(function() {
jQuery('input:[class="super_attribute"]radio,.super-attribute-select').on('change', function() {
	var otherinfo = jQuery(this).attr('otherinfo');
	var adddata = JSON.parse(otherinfo);
	var parent_regular_price = adddata["parent_regular_price"];
	var parent_special_price = adddata["parent_special_price"];
	if(adddata[jQuery(this).attr('value')])
	{
		var parentid = adddata[jQuery(this).attr('value')]["parentid"];
		var childsku = adddata[jQuery(this).attr('value')]["sku"];
		var childqty = adddata[jQuery(this).attr('value')]["qty"];
		var product_id = adddata[jQuery(this).attr('value')]["product_id"];
		var regular_price = adddata[jQuery(this).attr('value')]["regular_price"];
		var special_price = adddata[jQuery(this).attr('value')]["special_price"];
		jQuery('.sku-'+parentid).text("ID: "+childsku);
		jQuery('.qty-'+parentid).text("Qty in Stock: "+childqty);
		var optionsize = jQuery('.qty-select-'+parentid+' option').size();
		if(regular_price!=null && special_price!=null)
		{
			if(regular_price != special_price && special_price!=0)
			{
				eval('optionsPrice'+parentid).changePrice('config', {'price': special_price, 'oldPrice': regular_price});
			}
			else{
				eval('optionsPrice'+parentid).changePrice('config', {'price': regular_price, 'oldPrice': regular_price});
			}
			eval('optionsPrice'+parentid).reload();
		}
		else
		{
			if(parent_regular_price > parent_special_price)
			{
		  jQuery('#old-price-'+parentid).text(eval('optionsPrice'+parentid).formatPrice(parent_regular_price));
				jQuery('#product-price-'+parentid).text(eval('optionsPrice'+parentid).formatPrice(parent_special_price));
			}
			else
			{
				jQuery('#old-price-'+parentid).innerHTML = eval('optionsPrice'+parentid).formatPrice(parent_regular_price);
				jQuery('#product-price-'+parentid).innerHTML = eval('optionsPrice'+parentid).formatPrice(parent_regular_price);
			}
		}
		if(optionsize > childqty)
		{
			for(i=optionsize;i>childqty;i--)
			{
				jQuery(".qty-select-"+parentid+" option[value='"+i+"']").remove();
			} 
		}
		else
		{
			for(i=optionsize+1;i<=childqty;i++)
			{
				jQuery('.qty-select-'+parentid).append("<option value='"+i+"'>"+i+"</option>");
			} 
		}
	}
	else
	{
		var parentid = adddata["parent_id"];
		if(parent_regular_price > parent_special_price)
		{
	  jQuery('#old-price-'+parentid).text(eval('optionsPrice'+parentid).formatPrice(parent_regular_price));
			jQuery('#product-price-'+parentid).text(eval('optionsPrice'+parentid).formatPrice(parent_special_price));
		}
		else
		{
			jQuery('#old-price-'+parentid).innerHTML = eval('optionsPrice'+parentid).formatPrice(parent_regular_price);
			jQuery('#product-price-'+parentid).innerHTML = eval('optionsPrice'+parentid).formatPrice(parent_regular_price);
		}
	}
  });
	jQuery('input:[class="super_attribute"]radio:checked').each(function () {
		var otherinfo = jQuery(this).attr('otherinfo');
		var adddata = JSON.parse(otherinfo);
		if(adddata[jQuery(this).attr('value')])
		{
			var parentid = adddata[jQuery(this).attr('value')]["parentid"];
			var childsku = adddata[jQuery(this).attr('value')]["sku"]
			var childqty = adddata[jQuery(this).attr('value')]["qty"]
			jQuery('.sku-'+parentid).text("ID: "+childsku);
			jQuery('.qty-'+parentid).text("Qty in Stock: "+childqty);
			for(i=2;i<=childqty;i++)
			{
			jQuery('.qty-select-'+parentid).append("<option value='"+i+"'>"+i+"</option>");
			}
		}		
	 });
  });