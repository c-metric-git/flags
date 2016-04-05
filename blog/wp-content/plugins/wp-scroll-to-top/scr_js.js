//Copyright : Umar bajwa
//Email : umar2bajwa@gmail.com
// wpscrolltotop.blogspot.com

jQuery(document).ready(function(){
var doc_height = jQuery(document).height();
var height_half = doc_height /6;
jQuery(window).scroll(function() {
  
  
   if(jQuery(this).scrollTop()>= height_half) {
       jQuery('#scr_wrapper').show(400);
       jQuery('#scr_wrapper').click(function(){
       //jQuery('html , body').animate({scrollTop:0},200);
       jQuery("#scr_wrapper").slideUp(300);
       });return false;
   }
    else{
   jQuery('#scr_wrapper').slideUp(200); }


   
  

});



});
