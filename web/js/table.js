$(document).ready(function(){
	$('.dataTable').dataTable({
		"columnDefs": [{
			targets: "datatable-nosort",
			orderable: false,

		}],
		"paging":   false,
		"searching": false,
		"scrolling": false,
		"info":     false,
	});
	/*$("input[type=submit], button[type=submit]").unbind('click').on('click',function(){
		$(this).attr('type', 'button').addClass('disabled');
		/!*$("input[type=submit], button[type=submit]").each(function(v, i){
			$(v).attr('type', 'button').addClass('disabled');
		});*!/
	});*/
	$("form").on('submit',function(){
		$(this).find("button[type=submit]").attr('type', 'button').addClass('disabled');
		$(this).find("input[type=submit]").attr('type', 'button').addClass('disabled');
	});
});