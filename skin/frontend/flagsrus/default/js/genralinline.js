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
	 //var wid2 =  eval("wid1 + 20");
	 var wid2 =  eval("wid1");
	 jQuery(this).width(wid2);  
	});
	jQuery("#width_tmp_option").html("All");
	var wid1 = jQuery("#width_tmp_select").width();
	 //var wid2 =  eval("wid1 + 20");
	 var wid2 =  eval("wid1");
	jQuery('#resizing_select').width(wid2);  
	
	//Page lazyload
	jQuery("img.lazy").lazyload({});
	
	/*Responsive filter scroll*/
	jQuery( ".res-filter" ).click(function(e) {
		jQuery("html, body").animate({ scrollTop: jQuery('.block-layered-nav').offset().top }, 1000);
	});
});
/*jQuery(function() {
    jQuery(".nav-panel-inner img.lazy").lazyload({});
});*/
jQuery(function() {
    /*jQuery(".nav-panel-inner img.lazy").lazyload({});*/
	/*jQuery(".nav-panel-inner img.lazy").lazyload({
        event : "sporty"
    });
	var timeout = setTimeout(function() { jQuery(".nav-panel-inner img.lazy").trigger("sporty") }, 500);*/
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