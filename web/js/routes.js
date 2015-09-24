(function($){
	$('#myModal, #editModal').on('hidden.bs.modal', function() {
		$(this).find('[name="route_name"], [name="id"], [name="branch_id"]').val('');
	});

	$('button[data-target="#editModal"]').on('click.CP.routes', function(e) {
		var btn = $(this),
		    modal = $('#editModal');
		var route_id = btn.attr('data-id'),
		    branch_id = btn.attr('data-branch-id'),
		    route_name = $('.n'+route_id).html();

		modal.find('[name="route_name"]').val(route_name);
		modal.find('[name="id"]').val(route_id);
		modal.find('[name="branch_id"]').val(branch_id);
	});
})(jQuery);