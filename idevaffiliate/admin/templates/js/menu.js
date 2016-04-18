function animated_menu(){
	var timeShow = 200;
	
	//first level
	$(".menu > li").bind('mouseenter',function(){
		$(this).children('ul').each(function(){
			$(this).children('li').each(function(){
				$(this).animate({ marginTop: "50px"},0);
				$(this).show();
				$(this).animate({ marginTop: "0"},timeShow);
				timeShow = timeShow + 200;
			});
		});
		timeShow = 0;
	});
	$(".menu > li").bind('mouseleave',function(){
		$(this).children('ul').each(function(){
			$(this).children('li').each(function(){
				$(this).stop();
				$(this).fadeToggle(250);
			});
		});
	});
	
	//second level
	$(".menu > li > ul > li").bind('mouseenter',function(){
		$(this).children('ul').each(function(){
			$(this).children('li').each(function(){
				$(this).animate({ marginTop: "50px"},0);
				$(this).show();
				$(this).animate({ marginTop: "0"},timeShow);
				timeShow = timeShow + 200;
			});
		});
		timeShow = 0;
	});
	$(".menu > li > ul > li").bind('mouseleave',function(){
		$(this).children('ul').each(function(){
			$(this).children('li').each(function(){
				$(this).stop();
				$(this).fadeToggle(250);
			});
		});
	});
	
	//third level
	$(".menu > li > ul > li > ul > li").bind('mouseenter',function(){
		$(this).children('ul').each(function(){
			$(this).children('li').each(function(){
				$(this).animate({ marginTop: "50px"},0);
				$(this).show();
				$(this).animate({ marginTop: "0"},timeShow);
				timeShow = timeShow + 200;
			});
		});
		timeShow = 0;
	});
	$(".menu > li > ul > li > ul > li").bind('mouseleave',function(){
		$(this).children('ul').each(function(){
			$(this).children('li').each(function(){
				$(this).stop();
				$(this).fadeToggle(250);
			});
		});
	});
	
	$(".menu > li").bind('mouseenter',function(){
		$(this).children('.contact').show(300);
	});
	$(".menu > li").bind('mouseleave',function(){
		$(this).children('.contact').fadeToggle(200);
	});
}

