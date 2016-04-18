$(document).ready(function(){
    // ACCOUNTS APPROVED
    if($('#dyntable_approved').length > 0) {
        $('#dyntable_approved').dataTable({
            "bProcessing"	: true,
            "bServerSide"	: true,
            "sAjaxSource"	: "dynamic/dyn_accounts_approved.php",
			"aaSorting"		: [[ 0, "asc" ]],
			"aoColumns"		: [
				null,
				null,
				null,
				null,
				{ "bSortable": false }
			]
        });
    }
    
	// ACCOUNTS DECLINED
    if($('#dyntable_declined').length > 0) {
        $('#dyntable_declined').dataTable({
            "bProcessing"	: true,
            "bServerSide"	: true,
            "sAjaxSource"	: "dynamic/dyn_accounts_declined.php",
            "aaSorting"		: [[ 0, "asc" ]],
			"aoColumns"		: [
				null,
				null,
				null,
				{ "bSortable": false }
			]
        });
    }
	
	// ACCOUNTS PENDING
    if($('#dyntable_pending').length > 0) {
        $('#dyntable_pending').dataTable({
            "bProcessing"	: true,
            "bServerSide"	: true,
            "sAjaxSource"	: "dynamic/dyn_accounts_pending.php",
            "aaSorting"     : [[ 0, "desc" ]],
			"aoColumns"		: [
				null,
				null,
				{ "bSortable": false }
			]
        });
    }
	
	// ACCOUNTS SUSPENDED
    if($('#dyntable_suspended').length > 0) {
        $('#dyntable_suspended').dataTable({
            "bProcessing"       : true,
            "bServerSide"       : true,
            "sAjaxSource"       : "dynamic/dyn_accounts_suspended.php",
            "aaSorting"         : [[ 0, "asc" ]],
            "aoColumns"		: [
				null,
				null,
				{ "bSortable": false }
			],
        });
    }
	
	// ADMIN LOGS
    if($('#dyntable_admin_logs').length > 0) {
        $('#dyntable_admin_logs').dataTable({
            "bProcessing"       : true,
            "bServerSide"       : true,
            "sAjaxSource"       : "dynamic/dyn_admin_logs.php",
            "aaSorting"         : [[ 0, "desc" ]],
            });
    }
	
	// FRAUD BANNED EMAILS
    if($('#dyntable_email_signup_blocks').length > 0) {
        $('#dyntable_email_signup_blocks').dataTable({
            "bProcessing"       : true,
            "bServerSide"       : true,
            "sAjaxSource"       : "dynamic/dyn_banned_email.php",
            "aaSorting"         : [[ 0, "asc" ]],
            "aoColumns"		: [
				null,
				{ "bSortable": false }
			]
        });
    }

	// FRAUD BANNED IPS
    if($('#dyntable_ip_signup_blocks').length > 0) {
        $('#dyntable_ip_signup_blocks').dataTable({
            "bProcessing"       : true,
            "bServerSide"       : true,
            "sAjaxSource"       : "dynamic/dyn_banned_ip.php",
            "aaSorting"         : [[ 0, "asc" ]],
            "aoColumns"			: [
				null,
				{ "bSortable": false }
			]
        });
    }
	
	// COMMISSION BLOCKS
    if($('#dyntable_commission_blocks').length > 0) {
        $('#dyntable_commission_blocks').dataTable({
            "bProcessing"       : true,
            "bServerSide"       : true,
            "sAjaxSource"       : "dynamic/dyn_commission_blocks.php",
            "aaSorting"         : [[ 0, "asc" ]],
			"aoColumns"			: [
				null,
				{ "bSortable": false }
			]
            
        });
    }
	
	// COMMISSIONS APPROVED
    if($('#dyntable_commissions_approved').length > 0) {
        $('#dyntable_commissions_approved').dataTable({
            "bProcessing"	: true,
            "bServerSide"	: true,
            "sAjaxSource"	: "dynamic/dyn_commissions_approved.php",
            "aaSorting"		: [[ 2, "desc" ]],
    		"aoColumns"		: [
				null,
				null,
				null,
				null,
				{ "bSortable": false },
				null,
				{ "bSortable": false }
			]        
        });
    }
	
	// COUPON CODES
    if($('#dyntable_coupons').length > 0) {
        $('#dyntable_coupons').dataTable({
            "bProcessing"       : true,
            "bServerSide"       : true,
            "sAjaxSource"       : "dynamic/dyn_coupons.php",
            "aaSorting"         : [[ 0, "asc" ]],
            "aoColumns"		: [
				null,
				null,
				null,
				null,
				{ "bSortable": false },
				{ "bSortable": false }
			]
			});
    }
	    // COUPON CODES PENDING
    if($('#dyntable_coupons_pending').length > 0) {
        $('#dyntable_coupons_pending').dataTable({
            "bProcessing"       : true,
            "bServerSide"       : true,
            "sAjaxSource"       : "dynamic/dyn_coupons_pending.php",
            "aaSorting"         : [[ 0, "desc" ]],
            "aoColumns"		: [
				null,
				null,
				null,
				null,
				{ "bSortable": false }
			]
			});
    }
	// NON-COMMISSONED SALES LOG
    if($('#dyntable_general_sales').length > 0) {
        $('#dyntable_general_sales').dataTable({
            "bProcessing"       : true,
            "bServerSide"       : true,
            "sAjaxSource"       : "dynamic/dyn_general_sales.php",
            "aaSorting"         : [[ 0, "desc" ]],
            "aoColumns"		: [
				null,
				null,
				null,
				null,
				null,
				{ "bSortable": false }
			]
			});
    }
	// LOGOS APPROVED
    if($('#dyntable_logos_approved').length > 0) {
        $('#dyntable_logos_approved').dataTable({
            "bProcessing"       : true,
            "bServerSide"       : true,
            "sAjaxSource"       : "dynamic/dyn_logos_approved.php",
            "aaSorting"         : [[ 0, "desc" ]],
            "aoColumns"		: [
				null,
				null,
				null,
				{ "bSortable": false }
			],
			"fnDrawCallback"	: function (){
				$(".fancy-image").fancybox();
				var search_input = $(this).closest('.dataTables_wrapper').find('div[id$=_filter] input');
				if (search_input.parent().hasClass('input-group')) return;
				search_input.addClass('form-control')
				search_input.wrap('<div class="input-group"></div>');
				search_input.parent().prepend('<span class="input-group-addon"><i class="icon-search"></i></span>');
			}
        });
    }
	
	// LOGOS PENDING
    if($('#dyntable_logos_pending').length > 0) {
        $('#dyntable_logos_pending').dataTable({
            "bProcessing"       : true,
            "bServerSide"       : true,
            "sAjaxSource"       : "dynamic/dyn_logos_pending.php",
            "aaSorting"         : [[ 0, "desc"]],
            "aoColumns"		: [
				null,
				null,
				null,
				{ "bSortable": false }
			],
			"fnDrawCallback"	: function (){
				$(".fancy-image").fancybox();
				var search_input = $(this).closest('.dataTables_wrapper').find('div[id$=_filter] input');
				if (search_input.parent().hasClass('input-group')) return;
				search_input.addClass('form-control')
				search_input.wrap('<div class="input-group"></div>');
				search_input.parent().prepend('<span class="input-group-addon"><i class="icon-search"></i></span>');
			}
        });
    }
	
	// OVERRIDE COMMISSIONS
    if($('#dyntable_overrides').length > 0) {
        $('#dyntable_overrides').dataTable({
            "bProcessing"       : true,
            "bServerSide"       : true,
            "sAjaxSource"       : "dynamic/dyn_overrides.php",
            "aaSorting"         : [[ 0, "asc" ]],
            "aoColumns"		: [
				null,
				null,
				null,
				{ "bSortable": false },
				null,
				{ "bSortable": false }
			]
			});
    }
	
	// PAID COMMISSIONS
    if($('#dyntable_paid').length > 0) {
        $('#dyntable_paid').dataTable({
            "bProcessing"       : true,
            "bServerSide"       : true,
            "sAjaxSource"       : "dynamic/dyn_paid.php",
            "aaSorting"         : [[ 0, "asc" ]],
           	"aoColumns"		: [
				null,
				null,
                { "bSortable": false },
				null,
				null,
				{ "bSortable": false }				
			]
        });
    }
	
	// PAY LIST
    if($('#dyntable_pay_list').length > 0) {
        $('#dyntable_pay_list').dataTable({
            "bProcessing"       : true,
            "bServerSide"       : true,
            "sAjaxSource"       : "dynamic/dyn_pay_list.php",
            "aaSorting"         : [[ 0, "asc" ]],
            "aoColumns"		: [
				null,
				null,
				null,
				null,
				{ "bSortable": false }				
			]
        });
    }
	
	// PAYPAL ACTIVE
    if($('#dyntable_paypal_active').length > 0) {
        $('#dyntable_paypal_active').dataTable({
            "bProcessing"       : true,
            "bServerSide"       : true,
            "sAjaxSource"       : "dynamic/dyn_paypal_active.php",
            "aaSorting"         : [[ 0, "asc" ]],
            "aoColumns"		: [
				null,
				null,
				null,
				null,
				null,
				null				
			]
			});
    }
	
	// PAYPAL CANCELLED
    if($('#dyntable_paypal_cancelled').length > 0) {
        $('#dyntable_paypal_cancelled').dataTable({
            "bProcessing"       : true,
            "bServerSide"       : true,
            "sAjaxSource"       : "dynamic/dyn_paypal_cancelled.php",
            "aaSorting"         : [[ 0, "asc" ]],
            "aoColumns"		: [
				null,
				null,
				null,
				null,
				null,
				null				
			]
			});
    }
	// PDF MARKETING DOCS
    if($('#dyntable_pdf_marketing').length > 0) {
        $('#dyntable_pdf_marketing').dataTable({
            "bProcessing"       : true,
            "bServerSide"       : true,
            "sAjaxSource"       : "dynamic/dyn_pdf_marketing.php",
            "aaSorting"         : [[ 0, "asc" ]],
            "aoColumns"		: [
				null,
				null,
				{ "bSortable": false }				
			]
        });
    }
	
	// PDF TRAINING DOCS
    if($('#dyntable_pdf_training').length > 0) {
        $('#dyntable_pdf_training').dataTable({
            "bProcessing"       : true,
            "bServerSide"       : true,
            "sAjaxSource"       : "dynamic/dyn_pdf_training.php",
            "aaSorting"         : [[ 0, "asc" ]],
           	"aoColumns"		: [
				null,
				null,
				{ "bSortable": false }				
			]
        });
    }
	// PER-PRODUCT
    if($('#dyntable_products').length > 0) {
        $('#dyntable_products').dataTable({
            "bProcessing"       : true,
            "bServerSide"       : true,
            "sAjaxSource"       : "dynamic/dyn_products.php",
            "aaSorting"         : [[ 0, "asc" ]],
            "aoColumns"		: [
				null,
				null,
				null,
				{ "bSortable": false }				
			]
			});
    }
	
	// PROMO
    if($('#dyntable_promo').length > 0) {
        $('#dyntable_promo').dataTable({
            "bProcessing"       : true,
            "bServerSide"       : true,
            "sAjaxSource"       : "dynamic/dyn_promo.php",
            "aaSorting"         : [[ 0, "asc" ]],
            "aoColumns"		: [
				null,
				null,
				null,
				null,
				null,
				{ "bSortable": false },
				{ "bSortable": false }
			]
			});
    }
	// RECURRING COMMISSIONS
    if($('#dyntable_recurring').length > 0) {
        $('#dyntable_recurring').dataTable({
            "bProcessing"       : true,
            "bServerSide"       : true,
            "sAjaxSource"       : "dynamic/dyn_recurring.php",
            "aaSorting"         : [[ 0, "asc" ]],
            "aoColumns"		: [
				null,
				null,
				null,
				null,
				null,
				null,
				null,
				{ "bSortable": false }
			]
        });
    }
	// TEXT LINKS
    if($('#dyntable_textlinks').length > 0) {
        $('#dyntable_textlinks').dataTable({
            "bProcessing"       : true,
            "bServerSide"       : true,
            "sAjaxSource"       : "dynamic/dyn_textlinks.php",
            "aaSorting"         : [[ 0, "asc" ]],
            "aoColumns"		: [
				null,
				null,
				{ "bSortable": false }
			]
        });
    }
	// TRAFFIC LOGS
    if($('#dyntable_traffic_logs').length > 0) {
        $('#dyntable_traffic_logs').dataTable({
            "bProcessing"       : true,
            "bServerSide"       : true,
            "sAjaxSource"       : "dynamic/dyn_traffic_logs.php",
            "aaSorting"         : [[ 4, "desc" ]],
            "aoColumns"		: [
				null,
				null,
				null,
				null,
				null,
				null,
				null				
			]
			});
    }
	// TRAINING VIDEOS
    if($('#dyntable_videos').length > 0) {
        $('#dyntable_videos').dataTable({
            "bProcessing"       : true,
            "bServerSide"       : true,
            "sAjaxSource"       : "dynamic/dyn_videos.php",
            "aaSorting"         : [[ 0, "asc" ]],
            "aoColumns"		: [
				null,
				null,
				{ "bSortable": false }
			],
			"fnDrawCallback"	: function (oSetting){
				$(".fancy-video").fancybox();
				var search_input = $(this).closest('.dataTables_wrapper').find('div[id$=_filter] input');
				if (search_input.parent().hasClass('input-group')) return;
				search_input.addClass('form-control')
				search_input.wrap('<div class="input-group"></div>');
				search_input.parent().prepend('<span class="input-group-addon"><i class="icon-search"></i></span>');
			}
        });
    }
	
	// COMMISSIONS DECLINED
    if($('#dyntable_commissions_declined').length > 0) {
        $('#dyntable_commissions_declined').dataTable({
            "bProcessing"       : true,
            "bServerSide"       : true,
            "sAjaxSource"       : "dynamic/dyn_commissions_declined.php",
            "aaSorting"         : [[ 5, "desc" ]],
            "aoColumns"		: [
				null,
				null,
				null,
				null,
				{ "bSortable": false },
				null,
				{ "bSortable": false }
			],
        });
    }
	
	// PAY LIST BY DATE
    if($('#dyntable_pay_list_by_date').length > 0) {
        $('#dyntable_pay_list_by_date').dataTable({
            "bProcessing"       : true,
            "bServerSide"       : true,
            "sAjaxSource"       : "dynamic/dyn_pay_list_by_date.php",
            "aaSorting"         : [[ 0, "asc" ]],
            "aoColumns"		: [
				null,
				null,
				{ "bSortable": false },
				{ "bSortable": false },
				{ "bSortable": false },
				null,
				{ "bSortable": false }
			],
			"fnServerParams"	: function ( aoData ) {
				aoData.push(
					{ 
						"name" 	: "ajx_start_date",
						"value"	: $("input[name='ajx_start_date']").val()
					},
					{ 
						"name" 	: "ajx_end_date",
						"value"	: $("input[name='ajx_end_date']").val()
					}
				);
			}
        });
    }
});