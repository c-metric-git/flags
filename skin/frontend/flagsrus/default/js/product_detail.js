jQuery( document ).ready(function() {	
	setTimeout(function() {
	var reviewtxt = jQuery('.sa_jump_to_reviews').text();
	//jQuery('.sa_jump_to_reviews').text('('+reviewtxt+')');
	jQuery('.sa_jump_to_reviews').parent().html('<a href="#tab-tabreviews" class="sa_jump">('+reviewtxt+')</a>');
	//jQuery('.sa_jump_to_reviews').attr('href',"#");
	//jQuery('.sa_jump_to_reviews').attr("onclick","return false");
	jQuery('.sa_jump').on( "click",function(e){
		e.preventDefault();
		jQuery( "#tab-tabreviews > a" ).trigger( "click" );
		jQuery('body').animate({
		scrollTop: jQuery("#tab-tabreviews").offset().top
		},'slow');
	});

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