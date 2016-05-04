jQuery( document ).ready(function() {	
	setTimeout(function() {
	var reviewtxt = jQuery('.sa_jump_to_reviews').text();
	jQuery('.sa_jump_to_reviews').text('('+reviewtxt+')');
},2000);

if (jQuery(window).width() > 768)
{
	jQuery(".rating-and-qty-block").hide();
	jQuery('.products-grid  .item').on('mouseover', function() {
		var pid = jQuery(this).attr('id');
		jQuery('.rating-and-qty-block'+pid).show();
	}).on('mouseout', function() {
		var pid = jQuery(this).attr('id');
		jQuery('.rating-and-qty-block'+pid).hide();
	});
}else{
	jQuery(".rating-and-qty-block").show();
}

});