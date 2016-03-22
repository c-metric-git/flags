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
		jQuery('.sku-val').text(childsku);
		jQuery('.instock-qty').text("Quantity in Stock: "+childqty);
		var exp_qty = adddata[jQuery(this).attr('value')]["expected_qty"];
		var exp_doa = adddata[jQuery(this).attr('value')]["expected_doa"];
		if(exp_qty!=null && exp_doa!=null && exp_qty>0)
		{
			jQuery('.expected-qty').text('('+exp_qty+' expected in stock on '+exp_doa+')');
		}
		else{
			jQuery('.expected-qty').text('');
		}
		var optionsize = jQuery('.qty-select-'+parentid+' option').size();
		if(regular_price!=null && special_price!=null)
		{
			if(regular_price != special_price && special_price!=0)
			{
				eval('optionsPrice').changePrice('config', {'price': special_price, 'oldPrice': regular_price});
			}
			else{
				eval('optionsPrice').changePrice('config', {'price': regular_price, 'oldPrice': regular_price});
			}
			eval('optionsPrice').reload();
		}
		else
		{
			if(parent_regular_price > parent_special_price)
			{
		  jQuery('#old-price-'+parentid).text(eval('optionsPrice').formatPrice(parent_regular_price));
				jQuery('#product-price-'+parentid).text(eval('optionsPrice').formatPrice(parent_special_price));
			}
			else
			{
				jQuery('#old-price-'+parentid).innerHTML = eval('optionsPrice').formatPrice(parent_regular_price);
				jQuery('#product-price-'+parentid).innerHTML = eval('optionsPrice').formatPrice(parent_regular_price);
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
	  jQuery('#old-price-'+parentid).text(eval('optionsPrice').formatPrice(parent_regular_price));
			jQuery('#product-price-'+parentid).text(eval('optionsPrice').formatPrice(parent_special_price));
		}
		else
		{
			jQuery('#old-price-'+parentid).innerHTML = eval('optionsPrice').formatPrice(parent_regular_price);
			jQuery('#product-price-'+parentid).innerHTML = eval('optionsPrice').formatPrice(parent_regular_price);
		}
	}
  });
	jQuery('input:[class="super_attribute"]radio:checked').each(function () {
		var otherinfo = jQuery(this).attr('otherinfo');
		var adddata = JSON.parse(otherinfo);
		if(adddata[jQuery(this).attr('value')])
		{
			var parentid = adddata[jQuery(this).attr('value')]["parentid"];
			var childsku = adddata[jQuery(this).attr('value')]["sku"];
			var childqty = adddata[jQuery(this).attr('value')]["qty"];
			var exp_qty = adddata[jQuery(this).attr('value')]["expected_qty"];
			var exp_doa = adddata[jQuery(this).attr('value')]["expected_doa"];
			if(exp_qty!=null && exp_doa!=null && exp_qty>0)
			{
				jQuery('.expected-qty').text('('+exp_qty+' expected in stock on '+exp_doa+')');
			}
			else{
				jQuery('.expected-qty').text('');
			}
			jQuery('.sku-val').text(childsku);
			jQuery('.instock-qty').text("Quantity in Stock: "+childqty);
			for(i=2;i<=childqty;i++)
			{
			jQuery('.qty-select-'+parentid).append("<option value='"+i+"'>"+i+"</option>");
			}
		}		
	 });
  });