jQuery(document).ready(function ($) {
	$("#blp_br_allowed_memberships").select2({
		placeholder: "",
		allowClear: true,
		ajax: {
			url: ajaxurl,
			dataType: "json",
			delay: 250,
			data: function (params) {
				return {
					search_key: params.term,
					search_type: "memberpress",
					action: "blp_br_get_pages",
				};
			},
			processResults: function (data) {
				var options = [];
				if (data) {
					$.each(data, function (index, text) {
						options.push({ id: text[0], text: text[1] });
					});
				}
				return {
					results: options,
				};
			},
			cache: true,
		},
	});
});
