(function ($) {
	'use strict';

	$(function () {
		var frame;
		var $input = $('#bb-bible-study-asset-ids');
		var $list = $('#bb-bible-study-assets-list');

		$('#bb-bible-study-select-assets').on('click', function (event) {
			event.preventDefault();

			if (frame) {
				frame.open();
				return;
			}

			frame = wp.media({
				title: 'Select study assets',
				button: {
					text: 'Use selected assets'
				},
				multiple: true
			});

			frame.on('select', function () {
				var ids = [];
				var items = [];

				frame.state().get('selection').each(function (attachment) {
					var data = attachment.toJSON();
					ids.push(data.id);
					items.push('<li data-id="' + data.id + '">' + $('<div>').text(data.title || data.filename).html() + '</li>');
				});

				$input.val(ids.join(','));
				$list.html(items.join(''));
			});

			frame.open();
		});
	});
})(jQuery);
