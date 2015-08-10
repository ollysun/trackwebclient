$(document).ready(function(){
	$('.dataTable').dataTable({
		"columnDefs": [{
			targets: "datatable-nosort",
			orderable: false
		}]
	});
});