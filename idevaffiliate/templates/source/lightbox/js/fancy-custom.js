$(function (){
//Unless using the CDN hosted version, update the URL to the Flash SWF  
_V_.options.flash.swf = "video-js.swf";
  
    
    /*Fancy Image*/
    $(".fancy-image").fancybox(
        {
            minWidth     : 1,
            minHeight    : 1,
			padding 	 : 0,
			openEffect      : 'elastic',
            closeEffect     : 'elastic'
        }
    );
    
    $(".fancy-video").fancybox(
        {
            minWidth     : 1,
            minHeight    : 1
        }
    );
	
    $(".fancy-page").fancybox(
        {
    type : 'iframe',
    autoScale : false,
    autoDimensions : true

        }
    );
    
    $(".fancy-inline-youtube").fancybox(
        {
            minWidth     : 1,
            minHeight    : 1
        }
    );
    
    
    $(".fancy-SWF").each(function () {
        
        $(this).fancybox(
            {
                minWidth    : 1,
                minHeight   : 1,
                width       : $(this).attr('data-swf-width'),
                height      : $(this).attr('data-swf-height'),
                openEffect  : 'none',
                closeEffect : 'none',
                prevEffect  : 'none',
                nextEffect  : 'none',
                
                
                autoSize    : false,
                type        : 'swf',
                scrolling   : 'no', 
                autoCenter    : true,
                swf            : {
                    wmode: 'opaque',
                    allowfullscreen   : 'true',
                    allowscriptaccess : 'always'
                },

                arrows : false
                
                
            }
        );
        
    });
    
        
    
    /*Fancy Youtube */
    $('.fancy-youtube')
        .attr('rel', 'media-gallery')
        .fancybox({
            minWidth    : 1,
            minHeight   : 1,
        
            openEffect  : 'none',
            closeEffect : 'none',
            prevEffect  : 'none',
            nextEffect  : 'none',

            arrows      : false,
            helpers     : {
                media   : {},
                buttons : {}
            }
        });
});  





