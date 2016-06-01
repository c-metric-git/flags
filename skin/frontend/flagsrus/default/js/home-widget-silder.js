jQuery( document ).ready(function() {	
  
  var viewport = jQuery(window).width();

  jQuery(function () {
    var owl11 = jQuery('#itemslider-widget-tab-1');
	    owl11.owlCarousel({
	        lazyLoad: true,
	        responsiveClass:true,
	        responsive: {0:{items:1},480:{items:3},960:{items:6}},
	        responsiveRefreshRate: 50,
	        slideSpeed: 200,
	        paginationSpeed: 500,
	        scrollPerPage: true,
	        stopOnHover: true,
	        loop: true,
	        rewindSpeed: 600,
	        pagination: false,
	        nav: true,
	        goToFirst: true,
	        navigationText: false
	    }); //end: owl

   }); 
     
   //jQuery(".acctab").click(function() {  
   	jQuery( ".carousel_tab" ).on( "click", function() {
      var widget_tab="itemslider-widget-tab-";
      var carousel_tab_id=jQuery(this).attr('id').split('-');
      var itemslider_widget_tab=widget_tab+carousel_tab_id[1];
      var owl = jQuery('#'+itemslider_widget_tab);
     
            owl.owlCarousel({
                lazyLoad: true,
                responsiveClass:true,
                responsive: {0:{items:1},480:{items:3},960:{items:6}},
                responsiveRefreshRate: 50,
                slideSpeed: 200,
                paginationSpeed: 500,
                scrollPerPage: true,
                stopOnHover: true,
                loop: true,
                rewindSpeed: 600,
                pagination: false,
                nav: true,
                goToFirst: true,
                navigationText: false
               }); //end: owl

      /*var tabsItem =jQuery('#'+itemslider_widget_tab+' div.owl-stage-outer div.owl-stage > *').length;
      if(viewport >= 960 && parseInt(tabsItem) < 7 )
		{  
	       hide_home_tabs(itemslider_widget_tab);
		}   */ 

    });

    
    /*jQuery(function () {
      var owl20 = jQuery('#itemslider-category');
           owl20.owlCarousel({
                lazyLoad: true,
                responsiveClass:true,
                responsive: {0:{items:1},480:{items:2},960:{items:4}},
                responsiveRefreshRate: 50,
                slideSpeed: 200,
                paginationSpeed: 500,
                scrollPerPage: true,
                stopOnHover: true,
                loop: true,
                rewindSpeed: 600,
                pagination: false,
                nav: true,
                goToFirst: true,
                navigationText: false
            }); //end: owl
     });*/

   
   /* var itemCount = jQuery("#owl-demo div").length;
    var category_item =jQuery('div#itemslider-bestseller- div.owl-stage-outer div.owl-stage > *').length;
    var new_product_item=jQuery('div#itemslider-featured-fadeba94d7567af0ad25873e7da67e01 div.owl-stage-outer div.owl-stage > *').length;
		if(viewport >= 960 && parseInt(category_item) < 5 )
		{ 
	       setTimeout(hideCategoryNav,1000);
		} 
	    if(viewport >= 960 && parseInt(new_product_item) < 7 )
		{ 
			console.log(tab_Byseason);
	       setTimeout(hidenew_product_item,1000);
		} */

});

jQuery(function($) {		
					
$( ".acctab" ).on( "click", function() {
      var widget_tab="itemslider-widget-tab-";
      var carousel_tab_id=jQuery(this).attr('id').split('-');
      var itemslider_widget_tab=widget_tab+carousel_tab_id[1];
      var owl = jQuery('#'+itemslider_widget_tab);
     setInterval(function(){owl.owlCarousel
            owl.owlCarousel({
                lazyLoad: true,
                responsiveClass:true,
                responsive: {0:{items:1},480:{items:3},960:{items:6}},
                responsiveRefreshRate: 50,
                slideSpeed: 200,
                paginationSpeed: 500,
                scrollPerPage: true,
                stopOnHover: true,
                loop: true,
                rewindSpeed: 600,
                pagination: false,
                nav: true,
                goToFirst: true,
                navigationText: false
               }); //end: owl
	 },300);
	});
            //Link to the reviews tab
            var tabOperator = {
                root : ''
                , $rootContainer : null
                , $tabsContainer : null
                , $panelsContainer : null
                //1 - tabs/accordion, 2 - accordion, 3 - tabs
                , mode : 1
                , threshold : 1024
                , initialAccIndex : 0
                , tabEffect : 'default'
                , accEffect : 'default'
                , init : function(root)
                {
                    //If no param, set default selector
                    tabOperator.root = root || '.gen-tabs';
                    tabOperator.$rootContainer      = $(tabOperator.root);
                    tabOperator.$tabsContainer      = tabOperator.$rootContainer.children('.tabs');
                    tabOperator.$panelsContainer    = tabOperator.$rootContainer.children('.tabs-panels');
                    //Activate tabs based on selected mode
                        tabOperator.initialAccIndex = null;
                    if (tabOperator.mode === 1)
                    {
                        //Initial value of the flag which indicates whether viewport was above the threshold
                        var previousAboveThreshold = $(window).width() >= tabOperator.threshold;
                        //Activate tabs or accordion
                        if (previousAboveThreshold)
                        {
                            //If above threshold - activate tabs
                            tabOperator.initTabs();
                        }
                        else
                        {
                            //If below threshold - activate accordion
                            tabOperator.initAccordion(tabOperator.initialAccIndex);
                        }
                        //On tab click
                        tabOperator.hookToAccordionOnClick();
                        //On window resize
                        $(window).on('themeResize', function (e, resizeEvent) {
                            if ($(window).width() < tabOperator.threshold)
                            {
                                if (previousAboveThreshold)
                                {
                                    //Now below threshold, previously above, so switch to accordion
                                    var api = tabOperator.$tabsContainer.data("tabs");
                                    var index = api.getIndex();
                                    api.destroy();
                                    tabOperator.initAccordion(index);
                                }
                                previousAboveThreshold = false;
                            }
                            else
                            {
                                if (!previousAboveThreshold)
                                {
                                    //Now above threshold, previously below, so switch to tabs
                                    var api = tabOperator.$panelsContainer.data("tabs");
                                    var index = api.getIndex();
                                    api.destroy();
                                    tabOperator.$rootContainer.removeClass("accor");
                                    tabOperator.initTabs(index);
                                }
                                previousAboveThreshold = true;
                            }
                        });
                    }
                    else if (tabOperator.mode === 2)
                    {
                        tabOperator.initAccordion(tabOperator.initialAccIndex);
                        //On tab click
                        tabOperator.hookToAccordionOnClick();
                    }
                    else
                    {
                        tabOperator.initTabs();
                    }
                } //end: init
                , initTabs : function(index)
                {
                    //If no param, set it to 0
                    if (typeof index === "undefined")
                    { 
                        index = 0;
                    }
                    tabOperator.$tabsContainer.tabs(".tabs-panels .panel", {effect: tabOperator.tabEffect, initialIndex: index});
                }
                , initAccordion : function(index)
                {
                    //If no param, set it to 0
                    if (typeof index === "undefined")
                    { 
                        index = 0;
                    }
                    tabOperator.$rootContainer.addClass("accor");
                    tabOperator.$panelsContainer.tabs(".tabs-panels .panel", {tabs: '.acctab', effect: tabOperator.accEffect, initialIndex: index});
                }
                , hookToAccordionOnClick : function()
                {
                    //Attach a handler to an event after a tab is clicked
                    tabOperator.$panelsContainer.bind("onClick", function(event, index) {
                        //Note: "this" is a reference to the DOM element of tabs
                        //var theTabs = this;
                        var target = event.target || event.srcElement || event.originalTarget;
                        //If viewport is lower than the item, scroll to that item
                        var itemOffsetTop = $(target).offset().top;
                        var viewportOffsetTop = jQuery(window).scrollTop();
                        if (itemOffsetTop < viewportOffsetTop)
                        {
                            $("html, body").delay(150).animate({scrollTop: (itemOffsetTop-50)}, 600, 'easeOutCubic');
                        }
                    }); //end: bind onClick
                }
                , openTab : function()
                {
                    if (tabOperator.$rootContainer.hasClass("accor"))
                    {
                        var $panels = tabOperator.$panelsContainer;
                        var indexOfTab = $panels.children(".acctab").index($("#acctab-tabreviews"));
                        $panels.data("tabs").click(indexOfTab);
                    }
                    else
                    {
                        var $tabs = tabOperator.$tabsContainer;
                        var indexOfTab = $tabs.children("#tab-tabreviews").index();
                        $tabs.data("tabs").click(indexOfTab);
                    }
                }
                , slideTo : function(target, offset)
                {
                    //Slide to tab (minus height of sticky menu)
                    var itemOffsetTop = $(target).offset().top - offset;
                    $("html, body").animate({scrollTop: itemOffsetTop}, 600, 'easeOutCubic');
                }
            };
            //Initialize tabs
            tabOperator.init('#product-tabs');

            $("#goto-reviews").click(function() {
                tabOperator.openTab();
                tabOperator.slideTo('#product-tabs', 50);
            }); //end: on click
            $("#goto-reviews-form").click(function() {
                tabOperator.openTab();
                tabOperator.slideTo('#review-form', 90);
            }); //end: on click
});



function hideCategoryNav() {
	jQuery('div#itemslider-bestseller- div.owl-controls .owl-nav').hide();
}
function hide_home_tabs(itemslider_widget_tab) {
	jQuery('#'+itemslider_widget_tab+' div.owl-controls .owl-nav').hide();
}
function hidenew_product_item() {
	jQuery('div#itemslider-featured-fadeba94d7567af0ad25873e7da67e01 div.owl-controls .owl-nav').hide();
}