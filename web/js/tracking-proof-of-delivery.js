(function($){
	$('[data-toggle="popover"]')
		.popover({
			trigger: 'manual',
			html: true,
			content: $('#pod').html(),
			template: "<div class='popover tracking-popover' role='tooltip'><div class='arrow'></div><h3 class='popover-title'></h3><div class='popover-content'></div></div>",
		})
		.on('click', function() {
			$(this).popover('toggle');
		})
		.on('blur', function() {
			$(this).popover('hide');
		});
})(jQuery);