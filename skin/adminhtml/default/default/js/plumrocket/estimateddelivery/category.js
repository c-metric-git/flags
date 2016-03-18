document.observe('dom:loaded', refreshEstimatedData);

function refreshEstimatedData()
{
	var $ = pjQuery_1_9
		, types = {
			'delivery': 1, 
			'shipping': 1
		}
		, fields = {
			'enable': 1, 
			'days_from': 1, 
			'days_to': 1, 
			'date_from': 1, 
			'date_to': 1, 
			'text': 1
		};

	function obj(_type, param) {
		var obj = $('#estimated_' + _type + '_' + param);
		if (!obj.length) {
			var obj = $('#group_estimated_' + _type + '_' + param);
		}
		return obj;
	}

	function tr(_type, param)
	{
		return obj(_type, param).parent().parent();
	}

	for (var t in types) {
		obj(t, 'enable')
			.data('est_type', t)
			.change(function() {
				$this = $(this);
				var _type = $this.data('est_type');

				for (k in fields) {
					tr(_type, k).hide(0);
				}
				tr(_type, 'enable').show(0);
				$this.parent().parent().parent().find('.est_del').remove();

				switch (parseInt($this.val(), 10)) {
					case 0:
					case 1:
						// nothing to do
						break;
					case 2:
						obj(_type, 'days_from').removeAttr('style');
						obj(_type, 'days_to').removeAttr('style');
						var $master_tr = tr(_type, 'days_from').show(0);

						var $master = $master_tr.next().find('.value:first');
						obj(_type, 'days_to').appendTo($master);
						break;
					case 3:
						tr(_type, 'days_from').show(0);

						var $master = obj(_type, 'days_from').attr('style', 'width: 45% !important;');
						obj(_type, 'days_to').insertAfter($master).attr('style', 'width: 45% !important;');
						$('<span class="est_del"> — </span>').insertAfter($master);
						break;

					case 4:
						var $master_tr = tr(_type, 'date_from').show(0);

						var $master = $master_tr.next().find('.value:first');
						obj(_type, 'date_to').appendTo($master);
						obj(_type, 'date_to_trig').appendTo($master);
						break;

					case 5:
						tr(_type, 'date_from').show(0);

						var $master = obj(_type, 'date_from').parent();
						$('<span class="est_del"> — </span>').appendTo($master);
						obj(_type, 'date_to').appendTo($master);
						obj(_type, 'date_to_trig').appendTo($master);
						break;

					case 6:
						tr(_type, 'text').show(0);
						break;
				}
			})
			.change();
	}
}