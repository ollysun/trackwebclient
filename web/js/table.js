$(document).ready(function(){
	$('.dataTable').dataTable({
		"columnDefs": [{
			targets: "datatable-nosort",
			orderable: false,

		}],
		"paging":   false,
		"searching": false,
		"scrolling": false,
		"info":     false
	});
});