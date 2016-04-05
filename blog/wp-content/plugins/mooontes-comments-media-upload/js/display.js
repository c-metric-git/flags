(function($) {
	$(document).ready(function() {
		$("form#commentform")
			.attr("enctype", "multipart/form-data")
			.attr("encoding", "multipart/form-data");
	});
})(jQuery);