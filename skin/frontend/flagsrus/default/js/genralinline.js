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


})

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