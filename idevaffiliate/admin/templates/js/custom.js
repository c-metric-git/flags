/*
 * custom.js
 *
 * Place your code here that you need on all your pages.
 */
"use strict";
$(document).ready(function(){
	//===== Sidebar Search (Demo Only) =====//
	$('.sidebar-search').submit(function (e) {
		//e.preventDefault(); // Prevent form submitting (browser redirect)
		$('.sidebar-search-results').slideDown(200);
		return false;
	});
	$('.sidebar-search-results .close').click(function() {
		$('.sidebar-search-results').slideUp(200);
	});
	//===== .row .row-bg Toggler =====//
	$('.row-bg-toggle').click(function (e) {
		e.preventDefault(); // prevent redirect to #
		$('.row.row-bg').each(function () {
			$(this).slideToggle(200);
		});
	});
	//===== Sparklines =====//
	$("#sparkline-bar").sparkline('html', {
		type: 'bar',
		height: '35px',
		zeroAxis: false,
		barColor: App.getLayoutColorCode('red')
	});
	$("#sparkline-bar2").sparkline('html', {
		type: 'bar',
		height: '35px',
		zeroAxis: false,
		barColor: App.getLayoutColorCode('green')
	});
	//===== Refresh-Button on Widgets =====//
	$('.widget .toolbar .widget-refresh').click(function() {
		var el = $(this).parents('.widget');
		App.blockUI(el);
		window.setTimeout(function () {
			App.unblockUI(el);
			noty({
				text: '<strong>Widget updated.</strong>',
				type: 'success',
				timeout: 1000
			});
		}, 1000);
	});
	//===== Fade In Notification (Demo Only) =====//
	setTimeout(function() {
		$('#sidebar .notifications.demo-slide-in > li:eq(1)').slideDown(500);
	}, 3500);
	setTimeout(function() {
		$('#sidebar .notifications.demo-slide-in > li:eq(0)').slideDown(500);
	}, 7000);
	
	//===== Lightbox Implementation===========//

	$("body").on("click",".fancy-image",function (e){
		e.preventDefault();
		$.fancybox({"href":$(this).attr('href')});
		
	});
	$("body").on("click",".fancy-video",function (e){
		e.preventDefault();
		$.fancybox({"href":$(this).attr('href')});
	});
	$("body").on("click",".fancy-page",function (e){
		e.preventDefault();
		$.fancybox({"href":$(this).attr('href')});
	});

	
	//===== Video lightbox===================//
	$("a[rel='video']").fancybox({
		'padding'		: 0,
		'cyclic'        : true,
		'autoScale'		: false,
		'transitionIn'	: 'elastic',
		'transitionOut'	: 'elastic',
		'width'			: 640,
		'height'		: 360,	
		'showNavArrows' : false,
		'titlePosition' : 'inside',
		'titleFormat'	: formatTitle
	});
	
	/* make the crumb bar fixed*/
	stickyCrumb();
	
	$("#geolocate").click(function(){
		var ip	= $("#ip2geo").val(),
			rgx = /^(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$/;
			
		if ($.trim(ip) != "" ) {
			if (rgx.test(ip) == true) {
				$.post('includes/geoip.php', {ip:ip}, function(response){
					var obj = jQuery.parseJSON(response);
					if (obj.error != "") {
						alert(obj.error);
					}else{
						$("#ipcountry").val(obj.success.country_name);
					}
				});	
			}else{
				alert('IP Address not valid.');
			}			
		}else{
			alert('Please enter IP Address.');
		}		
	});
	
	$("#EmailForm").submit(function (e){
		
		var subBtn= $(this).find('input[type="submit"]');
		if (subBtn.hasClass('ajaxEmail')) {
			e.preventDefault();
			var subText= $("input[name='subject']").val();
			var mess = $("textarea[name='message']").val();
			var choice = $("select[name='choice']").val();
			$.post('../templates/email/mail.bulk.count.php', {subject : subText, message : mess, choice : choice, emailAuth : "yes"}, function (response){
				if (response.error != "") {
					$(".page-header").after('<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button>'+response.error+'</div>')
				}else{
					var current = 0,
						total 	= 0,
						total_emails = response.success;
					
					if (total_emails > 0 ) {
						$(".email_progress").fancybox({
							padding : 0,
							margin : 0,
							hideOnOverlayClick : false,
							showCloseButton 	:false,
							showNavArrows	: false,
							centerOnScroll : true,
							type : 'ajax',
							onComplete : function (){
								$(".total_emails").html(total_emails);
								sendEmail(subText, mess, choice, current, total_emails, total);		
							}
						});
						$(".email_progress").trigger('click');
					}
				}
			}, 'json');
		}
	});
	// do a ajax request to check session
	// and if response is timeout then redirect user
	// to login page. 
	setInterval(function (){
		$.post("includes/validate.session.php", function (data){
			if (data == "timeout") {
				window.location.href = 'index.php?timeout=true';
			}
		});
	}, 5000);
});
$(window).resize(function (){
	stickyCrumb();
});
function sendEmail(subText, mess, choice, current, total_emails, total){
	
	$.ajax({
		url : '../templates/email/mail.send_bulk.php',
		type : 'POST',
		data: {subject : subText, message : mess, choice : choice, offset : current, emailAuth : "yes"},
		success: function(data) {
			current++;
			total = (current/total_emails)*100; 
			$(".mail_sent").html(current);
			$(".total_percent").html(Math.floor(total));
			$(".progress_success").css("width", total + "%");
			if (current != total_emails) {
				sendEmail(subText, mess, choice, current, total_emails, total);				
			}else{
				if(current == total_emails){
					$.fancybox.close();					
				}
			}
		}
	});
}
function formatTitle(title, currentArray, currentIndex, currentOpts) {
	return '<div class="videoNav clearfix"><a href="javascript:;" onclick="$.fancybox.next();" class="prev">prev</a> <span class="title">' + title + '</span> <a href="javascript:;" onclick="$.fancybox.prev();" class="next">next</a></div>';
}
function stickyCrumb()
{
	var windowWidth = $(window).width();
	var sidebarWidth= $("#sidebar").outerWidth(true);
	$(".container.crumbFix > .crumbs").width(windowWidth-sidebarWidth);
}

if (flowplayer.support.firstframe) {
flowplayer(function (api, root) {
    // show poster when video ends
    api.bind("resume finish", function (e) {
      root.toggleClass("is-poster", /finish/.test(e.type));
    });
});
}
