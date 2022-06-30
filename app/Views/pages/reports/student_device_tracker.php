<!-- Include Header -->
<?php include_once(APPPATH . "Views/header.php"); ?>

<!-- Custom CSS -->
<link href="<?php echo base_url('assets/css/reports/student_device_tracker.css?v=20220405'); ?>" rel="stylesheet">

<div id="content">
	<div class="container-fluid mt-4">

		<div class="flex-container-column">
			<div>
				<label class="h5 text-gray-800 text-uppercase"> <?= $title; ?> </h5>
			</div>
			<div class="breadcrumb_div" aria-label="breadcrumb">
				<ol class="breadcrumb_custom">
					<li class="breadcrumb_item"><a href="<?php echo base_url('/home'); ?>"> Home </a></li>
					<li class="breadcrumb_item" aria-current="page"><a href="<?php echo base_url('reports'); ?>">Reports</a></li>
					<li class="breadcrumb_item active" aria-current="page"> <?= $title; ?> </li>
				</ol>
			</div>
		</div>

		<div class="card shadow p-4">


			<div class="d-flex justify-content-between my-1">
				<div></div>

				<div class="d-flex flex-row-reverse">

					<div class="input-group input-group-sm flex-nowrap search_input_wrap">

						<span class="input-group-text" id="addon-search">
							<i onclick="resetDatatable()" data-bs-toggle="tooltip" title="Reset saved table settings like hidden columns, search, etc" class='material-icons' id="refresh_icon">refresh</i>
						</span>

						<input class="form-control text-black-50" id='searchbox' autocomplete="off" style='width: 130px; color: blue; font-weight: bold;' placeholder="Search"  value="<?= (isset($_SESSION['student_search_string']) && !empty($_SESSION['student_search_string'])) ? $_SESSION['student_search_string'] : ''; ?>" name="srch-term" type="text" placeholder="Search" aria-label="Search" aria-describedby="addon-search">

					</div>


					<div class="mx-2">
						<select id="access_filter" class="form-select" style="width: 100%;max-width:200px;">
							<option value="allowed">Allowed</option>
							<option value="blocked">Blocked</option>
							<option value="deleted">Deleted</option>
							<option value="">All</option>
						</select>
					</div>

				</div>
			</div>


			<div class="table-responsive table_custom p-2">
				<table id="studentListTable" class="table table-bordered">
					<thead>
						<tr>
							<th>#</th>
							<th>Student Name</th>
							<th>Username</th>
							<th>Token</th>
							<th>Created Date</th>
							<th>Device Type</th>
							<th>Device Info</th>
							<th>IP Address</th>
							<th>Access</th>
							<th class="not_to_export not_to_print"> Actions </th>
						</tr>
					</thead>
					<tbody>

					</tbody>
				</table>
			</div>
		</div>

	</div>


</div>


<!-- Include Footer -->
<?php include_once(APPPATH . "Views/footer.php"); ?>


<script>
	var userdatatable;

	// To reset all the parameters in the datatable which are saved using stateSave
	function resetDatatable() {
		// Resetting the filter values
		$("#searchbox").val("");
		$("#access_filter").val("");

		userdatatable.draw();


		// REF: https://datatables.net/forums/discussion/53726/table-state-clear-does-not-clear-saved-state-how-do-i-remove-specified-filters-sorts-here
		userdatatable.state.clear(); // 1a - Clear State
		userdatatable.destroy(); // 1b - Destroy

		setTimeout(function() {
			window.location.reload();
		}, 1000);
	}

	$(document).ready(function() {
		userdatatable = $('#studentListTable').DataTable({
			stateSave: true,
			"columnDefs": [{
				"targets": [9],
				"orderable": false,
			}, {
				"targets": -1,
				"class": 'btn_col'
			}],
			"order": [
				[4, "desc"]
			],
			dom: 'Bflrtip',
			buttons: [{
				extend: 'colvis',
				//https://datatables.net/forums/discussion/50751/hide-several-columns-for-colvis-button-list
				columns: ':gt(0)',
				text: "Toggle Columns"
			}, {
				"extend": 'excel',
				"titleAttr": 'Excel',
				"action": newexportaction,
				// not_export class is used to hide excel columns. 
				"exportOptions": {
					"columns": ':visible:not(.not_to_export)'
				},
				messageTop: "Classrooms"
			}, {
				extend: 'print',
				exportOptions: {
					columns: ':visible:not(.not_to_print)'
				},
				title: "Classrooms ",
				customize: function(win) {
					$(win.document.body).find('h1').css('text-align', 'center');
					$(win.document.body).css('font-size', '9px');
					$(win.document.body).find('td').css('padding', '0px');
					$(win.document.body).find('td').css('padding-left', '2px');
				}
			}],
			"searching": true,
			"paging": true,
			"pageLength": 25,
			"bLengthChange": false,
			"bInfo": false,
			"processing": true,
			"serverSide": true,
			"ajax": {
				url: base_url + "/students/load_student_tokens",
				type: "post",
				"data": function(d) {
					d.access_filter = $('#access_filter').val(),
					d.searchbox = $("#searchbox").val()
				}
			},
			"dataSrc": "Data",
			language: {
				search: "",
				processing: '<i class="fas fa-atom fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>'
			}
		});


		// Access Filter on change event
		$("#access_filter").change(function() {
			userdatatable.draw();
		});


        $("#searchbox").keyup(function() {
            userdatatable.draw();
        });

	});
</script>