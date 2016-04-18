$(document).ready(function(){
	// BANNERS
    if(jQuery('#dyntable_banners').length > 0) {
        var oTable = jQuery('#dyntable_banners').dataTable({
            "bProcessing"       : true,
            "bServerSide"       : true,
            "iDisplayLength"    : 1,
			"bLengthChange"		: false,
            "sAjaxSource"       : "dynamic/dyn_banners.php",
            "aoColumnDefs"      : [{ "bSortable": false, "aTargets": [ 0] }],
            "bFilter"           : false,
			"bStateSave"		: true,
			"iCookieDuration"	: 10,
			"fnDrawCallback"	: function(oSettings) {
				jQuery(".dataTables_header").parent('div').hide();
				jQuery("#dyntable_banners thead").hide();
				jQuery("#dyntable_banners tbody tr td").addClass('modifyDatatable');
				jQuery(".dataTable").addClass('noTopMargin');
			}        
        });		
    }
	
	// CUSTOM FORM FIELDS
    if(jQuery('#dyntable_custom_form_fields').length > 0) {
        jQuery('#dyntable_custom_form_fields').dataTable({
            "bProcessing"       : true,
            "bServerSide"       : true,
			"iDisplayLength"    : 1,
			"bLengthChange"     : false, // disable rows per page
			"sAjaxSource"       : "dynamic/dyn_custom_form_fields.php",
			"aoColumnDefs"      : [{ "bSortable": false, "aTargets": [ 0] }],
			"bFilter"           : false,
			"bStateSave"		: true,
			"iCookieDuration"	: 10,
            "fnDrawCallback"    : function(oSettings) {
				jQuery("#dyntable_custom_form_fields thead").hide();
				jQuery("#dyntable_custom_form_fields tbody tr td").addClass('modifyDatatable');
				jQuery(".dataTable").addClass('noTopMargin');
            }
        });
    }
	
	// FAQ
    if(jQuery('#dyntable_faq').length > 0) {
        var oTable = jQuery('#dyntable_faq').dataTable({
            "bProcessing"       : true,
            "bServerSide"       : true,
            "iDisplayLength"    : 1,
			"bLengthChange"		: false,
            "sAjaxSource"       : "dynamic/dyn_faq.php",
            "aoColumnDefs"      : [{ "bSortable": false, "aTargets": [ 0] }],
            "bFilter"           : false,
			"bStateSave"		: true,
			"iCookieDuration"	: 10,
			"fnDrawCallback"	: function(oSettings) {

				jQuery("#dyntable_faq thead").hide();
				jQuery("#dyntable_faq tbody tr td").addClass('modifyDatatable');
				jQuery(".dataTable").addClass('noTopMargin');
			}        
        });		
    }
	
	// MARKETING GROUPS
    if(jQuery('#dyntable_groups').length > 0) {
        var oTable = jQuery('#dyntable_groups').dataTable({
            "bProcessing"       : true,
            "bServerSide"       : true,
            "iDisplayLength"    : 1,
			"bLengthChange"		: false,
            "sAjaxSource"       : "dynamic/dyn_groups.php",
            "aoColumnDefs"      : [{ "bSortable": false, "aTargets": [ 0] }],
            "bFilter"           : false,
			"bStateSave"		: true,
			"iCookieDuration"	: 10,
			"fnDrawCallback"	: function(oSettings) {

				jQuery("#dyntable_groups thead").hide();
				jQuery("#dyntable_groups tbody tr td").addClass('modifyDatatable');
				jQuery(".dataTable").addClass('noTopMargin');
			}        
        });
    }
	
	// HTML TEMPLATES
    if(jQuery('#dyntable_htmlads').length > 0) {
        var oTable = jQuery('#dyntable_htmlads').dataTable({
            "bProcessing"       : true,
            "bServerSide"       : true,
            "iDisplayLength"    : 1,
			"bLengthChange"		: false,
            "sAjaxSource"       : "dynamic/dyn_htmlads.php",
            "aoColumnDefs"      : [{ "bSortable": false, "aTargets": [ 0] }],
            "bFilter"           : false,
			"bStateSave"		: true,
			"iCookieDuration"	: 10,
			"fnDrawCallback"	: function(oSettings) {
				jQuery(".dataTables_header").parent('div').hide();
				jQuery("#dyntable_htmlads thead").hide();
				jQuery("#dyntable_htmlads tbody tr td").addClass('modifyDatatable');
				jQuery(".dataTable").addClass('noTopMargin');
				$(".fancy-page").fancybox();
			}        
        });		
    }
	
	// LIGHTBOXES
    if(jQuery('#dyntable_lightboxes').length > 0) {
        var oTable = jQuery('#dyntable_lightboxes').dataTable({
            "bProcessing"       : true,
            "bServerSide"       : true,
            "iDisplayLength"    : 1,
			"bLengthChange"		: false,
            "sAjaxSource"       : "dynamic/dyn_lightboxes.php",
            "aoColumnDefs"      : [{ "bSortable": false, "aTargets": [ 0] }],
            "bFilter"           : false,
			"bStateSave"		: true,
			"iCookieDuration"	: 10,
			"fnDrawCallback"	: function(oSettings) {
				jQuery(".dataTables_header").parent('div').hide();
				jQuery("#dyntable_lightboxes thead").hide();
				jQuery("#dyntable_lightboxes tbody tr td").addClass('modifyDatatable');
				jQuery(".dataTable").addClass('noTopMargin');
				
				
			}        
        });		
    }
	
	// NOTES
    if(jQuery('#dyntable_notes').length > 0) {
        var oTable = jQuery('#dyntable_notes').dataTable({
            "bProcessing"       : true,
            "bServerSide"       : true,
            "iDisplayLength"    : 1,
			"bLengthChange"		: false,
            "sAjaxSource"       : "dynamic/dyn_notes.php",
            "aoColumnDefs"      : [{ "bSortable": false, "aTargets": [ 0] }],
            "bFilter"           : false,
			"bStateSave"		: true,
			"iCookieDuration"	: 10,
			"fnDrawCallback"	: function(oSettings) {
				jQuery(".dataTables_header").parent('div').hide();
				jQuery("#dyntable_notes thead").hide();
				jQuery("#dyntable_notes tbody tr td").addClass('modifyDatatable');
				jQuery(".dataTable").addClass('noTopMargin');
				
			}        
        });		
    }
	
	// PAGE PEELS
    if(jQuery('#dyntable_peels').length > 0) {
        var oTable = jQuery('#dyntable_peels').dataTable({
            "bProcessing"       : true,
            "bServerSide"       : true,
            "iDisplayLength"    : 1,
			"bLengthChange"		: false,
            "sAjaxSource"       : "dynamic/dyn_peels.php",
            "aoColumnDefs"      : [{ "bSortable": false, "aTargets": [ 0] }],
            "bFilter"           : false,
			"bStateSave"		: true,
			"iCookieDuration"	: 10,
			"fnDrawCallback"	: function(oSettings) {
				jQuery(".dataTables_header").parent('div').hide();
				jQuery("#dyntable_peels thead").hide();
				jQuery("#dyntable_peels tbody tr td").addClass('modifyDatatable');
				jQuery(".dataTable").addClass('noTopMargin');
			}			
        });		
    }
	
	 // TESTIMONIALS APPROVED
    if(jQuery('#dyntable_testi_approved').length > 0) {
        var oTable = jQuery('#dyntable_testi_approved').dataTable({
            "bProcessing"       : true,
            "bServerSide"       : true,
            "iDisplayLength"    : 1,
			"bLengthChange"		: false,
            "sAjaxSource"       : "dynamic/dyn_testi_approved.php",
            "aoColumnDefs"      : [{ "bSortable": false, "aTargets": [ 0] }],
            "bFilter"           : false,
			"bStateSave"		: true,
			"iCookieDuration"	: 10,
			"fnDrawCallback"	: function(oSettings) {
		 		
				jQuery("#dyntable_testi_approved thead").hide();
				jQuery("#dyntable_testi_approved tbody tr td").addClass('modifyDatatable');
				jQuery(".dataTable").addClass('noTopMargin');
			}        
        });		
    }
	
    // TESTIMONIALS PENDING
    if(jQuery('#dyntable_testi_pending').length > 0) {
        var oTable = jQuery('#dyntable_testi_pending').dataTable({
            "bProcessing"       : true,
            "bServerSide"       : true,
            "iDisplayLength"    : 1,
			"bLengthChange"		: false,
            "sAjaxSource"       : "dynamic/dyn_testi_pending.php",
            "aoColumnDefs"      : [{ "bSortable": false, "aTargets": [ 0] }],
            "bFilter"           : false,
			"bStateSave"		: true,
			"iCookieDuration"	: 10,
			"fnDrawCallback"	: function(oSettings) {

				jQuery("#dyntable_testi_pending thead").hide();
				jQuery("#dyntable_testi_pending tbody tr td").addClass('modifyDatatable');
				jQuery(".dataTable").addClass('noTopMargin');
				
			}        
        });		
    }
	
	// TEXT ADS
    if(jQuery('#dyntable_textads').length > 0) {
        var oTable = jQuery('#dyntable_textads').dataTable({
            "bProcessing"       : true,
            "bServerSide"       : true,
            "iDisplayLength"    : 1,
			"bLengthChange"		: false,
            "sAjaxSource"       : "dynamic/dyn_textads.php",
            "aoColumnDefs"      : [{ "bSortable": false, "aTargets": [ 0] }],
            "bFilter"           : false,
			"bStateSave"		: true,
			"iCookieDuration"	: 10,
			"fnDrawCallback"	: function(oSettings) {
				jQuery(".dataTables_header").parent('div').hide();
				jQuery("#dyntable_textads thead").hide();
				jQuery("#dyntable_textads tbody tr td").addClass('modifyDatatable');
				jQuery(".dataTable").addClass('noTopMargin');
			}        
        });		
    }
	
    // EMAIL TEMPLATES
    if(jQuery('#dyntable_email_templates').length > 0) {
        var oTable = jQuery('#dyntable_email_templates').dataTable({
            "bProcessing"       : true,
            "bServerSide"       : true,
            "iDisplayLength"    : 1,
			"bLengthChange"		: false,
            "sAjaxSource"       : "dynamic/dyn_email_templates.php",
            "aoColumnDefs"      : [{ "bSortable": false, "aTargets": [ 0] }],
            "bFilter"           : false,
			"bStateSave"		: true,
			"iCookieDuration"	: 10,
			"fnDrawCallback"	: function(oSettings) {
				jQuery(".dataTables_header").parent('div').hide();
				jQuery("#dyntable_email_templates thead").hide();
				jQuery("#dyntable_email_templates tbody tr td").addClass('modifyDatatable');
				jQuery(".dataTable").addClass('noTopMargin');
			}        
        });		
    }
});