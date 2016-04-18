$(document).ready(function(){
    // COMMISSIONS DELAYED
    if($('#dyntable_commissions_delayed').length > 0) {
        $('#dyntable_commissions_delayed').dataTable({
            "bProcessing"	: true,
            "bServerSide"	: true,
            "sAjaxSource"	: "dynamic/dyn_commissions_delayed.php",
            "aaSorting"		: [[ 3, "desc"]],
            "aoColumns"		: [
				{ "bSortable": false },
				null,
				null,
				null,
				null,
				{ "bSortable": false },
				null,
				null
			],   
			"fnDrawCallback"	: function (oSetting){
				$(".bulk-div").remove();
				if( oSetting['_iRecordsTotal']  > 0){
					$("#dyntable_commissions_delayed").before('<div class="form-actions bulk-div"><img src="templates/images/level-icon.png" class="lvl"><input type="submit" value="Process Now" name="q_process" class="btn btn-primary"> </div>');
				}
				
				var search_input = $(this).closest('.dataTables_wrapper').find('div[id$=_filter] input');
				if (search_input.parent().hasClass('input-group')) return;
				search_input.addClass('form-control')
				search_input.wrap('<div class="input-group"></div>');
				search_input.parent().prepend('<span class="input-group-addon"><i class="icon-search"></i></span>');
			}
        });
	}
	
	// COMMISSIONS PENDING
    if($('#dyntable_commissions_pending').length > 0) {
        $('#dyntable_commissions_pending').dataTable({
            "bProcessing"	: true,
            "bServerSide"	: true,
            "sAjaxSource"	: "dynamic/dyn_commissions_pending.php",
            "aaSorting"		: [[ 3, "desc" ]],
			"aoColumns"		: [
				{ "bSortable": false },
				{ "bSortable": false },
				null,
				null,
				null,
				{ "bSortable": false },
				null,
				{ "bSortable": false }
			],
			"fnDrawCallback"	: function (oSetting){
				$(".bulk-div").remove();
				if( oSetting['_iRecordsTotal']  > 0){
					$("#dyntable_commissions_pending").before('<div class="form-actions bulk-div"><img src="templates/images/level-icon.png" class="lvl"><input type="submit" value="Bulk Approve" name="q_approve" class="btn btn-primary"> <input type="submit" value="Bulk Decline" class="btn btn-danger" name="q_decline"></div>');
				}
				
				var search_input = $(this).closest('.dataTables_wrapper').find('div[id$=_filter] input');
				if (search_input.parent().hasClass('input-group')) return;
				search_input.addClass('form-control')
				search_input.wrap('<div class="input-group"></div>');
				search_input.parent().prepend('<span class="input-group-addon"><i class="icon-search"></i></span>');
			}
        });
    }
	
	$(document).on("click", '.select-all-rows', function (){
		if($(this).is(":checked")){
			$(".chk").prop('checked', true);
		}else {
			$(".chk").prop('checked', false);
		}
	});
});