jQuery( document ).ready(function() {	
	jQuery(".collapsable_head").click(function(){		
		var collapsable_id=jQuery(this).attr('id');	
			jQuery("."+collapsable_id).slideToggle(400);		
			if(jQuery(this).hasClass("collapsable_head-up"))	
				  { 			
					  jQuery(this).removeClass("collapsable_head-up");
					  jQuery(this).addClass("collapsable_head-down");	
			      }
		 	else {			
		 		      jQuery(this).removeClass("collapsable_head-down");	
		 		      jQuery(this).addClass("collapsable_head-up");
		 		 }	
		 	});


   jQuery('.link-wishlist').click(function(){
       var productId=jQuery(this).parent().parent().parent().parent().attr('id');
       console.log(productId);
       wishlistProductid(productId);
      
    });

   
   jQuery('.view_wishlist_link').click(function(){
       var productId=jQuery(this).attr('data-id');
       console.log(productId);
       wishlistProductid(productId);
      
    });
   jQuery(".btn-remove").each(function() {  
	   skutext =jQuery(this).attr('id');
	   if(skutext!= null)
	   {
		   skus=skutext.split('-');  
		   if (jQuery('.product_addtocart_form_'+skus[0])[0])
		   {	
				jQuery('.product_addtocart_form_'+skus[0]).attr('action',jQuery(this).attr('href'));	
				jQuery('.'+skus[0]+'-addto').hide();	
				jQuery('.'+skus[0]+'-addremove').show();  
			}
	   }
	});

	//solar search dropdown
	jQuery('#resizing_select').change(function(){
	 jQuery("#width_tmp_option").html(jQuery('#resizing_select option:selected').text());
	 var wid1 = jQuery("#width_tmp_select").width();
	 var wid2 =  eval("wid1 + 20");
	 jQuery(this).width(wid2);  
	});
	/*jQuery("#width_tmp_option").html("All");
	var wid1 = jQuery("#width_tmp_select").width();
	var wid2 =  eval("wid1 + 20");
	jQuery('#resizing_select').width(wid2);  */

	jQuery('.fp-product-wrapper .fp-product-cust').mouseover(function(){
	  jQuery(this).find('.rating-and-qty-block').show();
	  jQuery(this).find('.add-to-links').show();	  
	});

	jQuery('.fp-product-wrapper .fp-product-cust').mouseout(function(){
	  jQuery(this).find('.rating-and-qty-block ').hide();
	  jQuery(this).find('.add-to-links').hide();
	});

	jQuery('a.link-wishlist,a.link-compare').mouseover(function(){
	jQuery(this).find('span.fa').css('color','#FFFFFF');
	});

	jQuery('.toolbar-bottom .catg-detail-head').remove();
   
    /* product detail page Tabbing */
    jQuery(document).off('click.tab.data-api');
    jQuery(document).on('click.tab.data-api', '[data-toggle="tab"]', function (e) {
        e.preventDefault();
        var tab =jQuery(jQuery(this).attr('href'));
        var activate = !tab.hasClass('active');
        jQuery('div.tab-content>div.tab-pane.active').removeClass('active');
        jQuery('ul.nav.nav-tabs>li.active').removeClass('active');
        if (activate) {
            jQuery(this).tab('show')
        }
    });
    /* hompepage */
    jQuery('dl#narrow-by-list dd.Color').prepend(jQuery('dl#narrow-by-list dd.Color_Types'));

});
jQuery( window ).load(function() {
	

	var timeout = setTimeout(	function() { 
		jQuery(".nav-panel-inner img.lazy").lazyload({
        event : "sporty"
    });
	jQuery(".nav-panel-inner img.lazy").trigger("sporty") }, 100);
});
function  wishlistProductid(productId)
{

	         jQuery('#wishlist_productId').remove();
			 jQuery('<input>').attr({
			    type: 'hidden',
			    id: 'wishlist_productId',
			    name: 'wishlist_productId',
			    value: productId
			}).prependTo('#magestore-sociallogin-form'); 
}



