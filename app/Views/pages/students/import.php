<?php include_once(APPPATH . "Views/header.php"); ?>
<!-- Students View -->

<!-- Custom CSS -->
<link href="<?php echo base_url('assets/css/students/overview.css?v=20220420'); ?>" rel="stylesheet" />


<div id="content">
	<div class="container-fluid mt-4">
		<div class="flex-container-column">
			<div>
				<label class="h5 text-gray-800 text-uppercase"><?= $title; ?></label>
			</div>
			<div class="breadcrumb_div" aria-label="breadcrumb">
				<ol class="breadcrumb_custom">
					<li class="breadcrumb_item"><a href="<?php echo base_url('/home'); ?>"> Home </a></li>
					<li class="breadcrumb_item"><a href="<?php echo base_url('/students'); ?>"> Your Students </a></li>
					<li class="breadcrumb_item active" aria-current="page"> <?= $title; ?> </li>
				</ol>
			</div>
		</div>

		<div class="bg-white w-100 shadow rounded p-4 mb-5" style="max-width: 900px;margin:auto;">

			<div class="text-center">
				<a class="btn btn-link" href="<?= base_url('assets/templates/student_import.xlsx'); ?>"> <i class='fa fa-download fa-fw' aria-hidden='true'></i> Download Excel template</a>
			</div>

			<div class="mt-2">
				<label class="form-label" for="student_import_package">Classroom *</label>
				<select class="form-control" name="student_import_package" id="student_import_package" required>
					<option value=""></option>
					<?php
					foreach ($all_classrooms_array as $row) {
						$package_id = $row['id'];
						$package_name = $row['package_name'];
						echo "<option value='$package_id'>$package_name</option>";
					}
					?>
				</select>
			</div>
			<br />
			<div class="mb-2">
				<label class="form-label" for="excel_file">Choose Excel File *</label>
				<input type="file" class="form-control" id="excel_file" name="excel_file" required />
			</div>
			<br />
			<div class="mb-2">
				<input class="form-label" type="checkbox" id="update_check" name="update_check" value="ADD_PKG" />
				<label for="update_check">Only Update classrooms (Don't add new students)</label>
			</div>

			<div class="mb-2">
				<input class="form-label" type="checkbox" id="overwrite_check" name="overwrite_check" value="OVERWRITE_PKG" />
				<label for="overwrite_check">Overwrite student classrooms </label>
			</div>


			<div class="mb-2">
				<input class="form-label" type="checkbox" id="update_info" name="update_info" value="UPDATE_INFO" />
				<label for="update_info">Overwrite student information (Roll No, Name etc) </label>
			</div>

			<div class="mb-2">
				<input class="form-label" type="checkbox" id="smsMessageCheck" name="smsMessageCheck" value="1" />
				<label for="smsMessageCheck"> Send username/password on SMS/Email </label>
			</div>


			<p id="uploadError" class="text-danger"></p>
			<p id="uploadStatus" class="text-success"></p>


			<input type="hidden" name="institute_id" value="<?= $instituteID; ?>" required />
			<input type="hidden" name="redirect" value="<?= $redirect; ?>" required />
			<button type="submit" class="btn btn-success" name="import_excel" onclick="importStudents()">Import</button>


			<hr />
			<div class='text-center'>
				<h5>Student Results : </h5>
			</div>
			<div id="students_result"></div>

			<div class="student_failed_data my-2">
				<div class='text-center'>
					<h5>Failed Student Entries</h5>
				</div>
				<table class="table table-bordered" id="students_failed_table">
					<thead>
						<tr>
							<td> Username </td>
							<td> Reason </td>
						</tr>
					</thead>
					<tbody id="students_failed_result">

					</tbody>
				</table>
			</div>

		</div>




	</div>

</div>


<!-- Include Footer -->
<?php include_once(APPPATH . "Views/footer.php"); ?>

<script>
	var unique_identifier = generate_unique_identifier(12);
	// Call async every  5 sec to check no of students revaluated 
	var intervalId = window.setInterval(function() {
		getStudentsImported();
	}, 5000);

	$(".student_failed_data").hide();

	function getStudentsImported() {
		$.ajax({
			url: base_url + "/routineResults/fetch_results",
			type: "POST",
			data: {
				unique_identifier: unique_identifier
			},
			success: function(result) {
				$("#students_result").html(format_result(result));
			}
		});
	}


	function getFailedStudentsImported() {
		$.ajax({
			url: base_url + "/routineResults/fetch_failed_results",
			type: "POST",
			data: {
				unique_identifier: unique_identifier
			},
			success: function(result) {
				$("#students_failed_result").append(format_failed_result(result));

				var stu_custom_data_tble = document.getElementById("students_failed_table");
				if (stu_custom_data_tble != null) {
					$("#students_failed_table").DataTable({
						order: [0, "asc"],
						dom: "Bflrtip",
						buttons: ["excel"],
						pageLength: 10,
						"bLengthChange": false,
						"bInfo": false
					});
				}
			}
		});
	}

	function format_result(data) {
		var html = "";
		if (data != null) {
			data = JSON.parse(data);
			html = html + "<div class='d-flex justify-content-center'>";
			for (var i = 0; i < data.length; i++) {
				if (data[i]['success_status'] == 'Success') {
					html = html + "<div class='badge bg-success badge_custom mx-2'>" + data[i]['success_status'] + ": " + data[i]['cnt'] + "</div>";
				} else {
					html = html + "<div class='badge bg-danger badge_custom'>" + data[i]['success_status'] + ": " + data[i]['cnt'] + "</div>";
				}
			}
			html = html + "</div>";
		}
		return html;
	}

	function format_failed_result(data) {
		var html = "";
		if (data != null) {
			data = JSON.parse(data);
			for (var i = 0; i < data.length; i++) {
				html = html + "<tr>";
				html = html + "<td>" + data[i]['student_username'] + "</td>";
				html = html + "<td>" + data[i]['result'] + "</td>";
				html = html + "</tr>";
			}
		}
		return html;
	}

	function importStudents() {

		var f = document.getElementById('excel_file').files[0];
		// console.log("File:", f);
		if (f == null || f == undefined) {
			$("#uploadError").text("Please select a file to upload");
			return;
		}


		// Looping through the classrooms array
		// Then importing the excel data for each classroom
		// classroomIdsArray.forEach(function(classroomId) {
		// console.log("Importing students for classroomid:", classroomId);

		var requestType;
		if ($("#update_info").is(":checked")) {
			requestType = $("#update_info").val();
			// console.log("Request type set as " + requestType);
		}

		if (!requestType) {
			var classroomIdsArray = $("#student_import_package").val();

			if (!classroomIdsArray || !classroomIdsArray.length) {
				$("#uploadError").text("Please select a classroom to import students into");
				return;
			}
		}


		if ($("#update_check").is(":checked")) {
			requestType = $("#update_check").val();
			// console.log("Request type set as " + requestType);
		}

		if ($("#overwrite_check").is(":checked")) {
			requestType = $("#overwrite_check").val();
			// console.log("Request type set as " + requestType);
		}

		var classroomIdsArray = $("#student_import_package").val();

		if (!classroomIdsArray || !classroomIdsArray.length) {
			if (requestType != 'UPDATE_INFO') {
				$("#uploadError").text("Please select a classroom to import students into");
				return;
			}
		}

		var fd = new FormData();
		fd.append('data', f);

		//  Send username password on SMS,Email
		var smsMessageCheck = 'N';
		if (document.getElementById('smsMessageCheck').checked) {
			smsMessageCheck = 'Y';
		}

		var request = {
			institute: {
				id: <?= $decryptedinstituteID; ?>
			},
			requestType: requestType,
			classrooms: classroomIdsArray,
			smsMessage: smsMessageCheck,
			sortFilter: unique_identifier
		}


		fd.append('request', JSON.stringify(request));

		$("#uploadStatus").text("Uploading data ..");
		$("#uploadError").text("");
		// console.log("Uploading ..", fd);
		//Load tokens first
		get_admin_token().then(function(result) {
				var resp = JSON.parse(result);
				if (resp != null && resp.status == 200 && resp.data != null && resp.data.admin_token != null) {
					$.ajax({
						type: "POST",
						url: rootAdmin + "uploadStudentsExcel",
						beforeSend: function(fd) {
							fd.setRequestHeader("AuthToken", resp.data.admin_token);
						},
						data: fd,
						processData: false,
						contentType: false,
						success: function(data) {
							// show response from the php script.
							// console.log(data);
							$("#uploadStatus").text("");
							if (data != null) {
								if (data.status.statusCode == 200) {

									// Snakbar Message
									Snackbar.show({
										pos: 'top-center',
										text: 'Uploaded successfully'
									});

									clearInterval(intervalId);
									$(".student_failed_data").show();
									getStudentsImported();
									getFailedStudentsImported();
									// window.location = base_url + "/students";
								} else {
									$("#uploadError").text(data.status.responseText);
								}
							} else {
								$("#uploadError").text("Some error while connecting to the server ..");
							}

							//alert(data);
						},
						error: function(err) {
							$("#uploadError").text("Some error while connecting to the server ..");
						}
					});
				} else {
					alert("Some error authenticating your request. Please clear your browser cache and try again.");
				}
			})
			.catch(function(error) {
				// An error occurred
				// alert("Exception: " + error);
			});

	}
</script>