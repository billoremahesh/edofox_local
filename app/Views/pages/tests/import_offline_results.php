<!-- Include Header -->
<?php include_once(APPPATH . "Views/header.php"); ?>

<!-- Custom CSS -->
<link href="<?php echo base_url('assets/css/tests/overview.css?v=20210915'); ?>" rel="stylesheet">

<div id="content">
    <div class="container-fluid mt-4">

        <div class="flex-container-column">
            <div>
                <label class="h5 text-gray-800 text-uppercase"> <?= $title; ?></h5>
            </div>
            <div class="breadcrumb_div" aria-label="breadcrumb">
                <ol class="breadcrumb_custom">
                    <li class="breadcrumb_item"><a href="<?php echo base_url('/home'); ?>"> Home </a></li>
                    <li class="breadcrumb_item"><a href="<?php echo base_url('tests'); ?>"> Tests </a></li>
                    <li class="breadcrumb_item active" aria-current="page"> <?= $title; ?> </li>
                </ol>
            </div>
        </div>

        <div class="card p-4 fw-bold"" style=" max-width: 1200px;margin:auto;">


            <div id="import_file_div">
                <div class="mb-2">
                    <label>Step 1: Download Excel Template Below. It has the students with USERNAMES who were absent for ONLINE exam (only for reference) and question numbers in columns: </label>
                    <br />
                    <!-- Giving button to export answer key for current exam in excel -->
                    <a class="btn btn-outline-primary mt-2 mb-2" href="<?= base_url('tests/download_offline_test_result_template/' . $test_id); ?>" target="_blank">Download Import Offline Result Template</a>
                </div>

                <div class="mb-2">
                    <label>Step 2: Update the 1st "STUDENT USERNAME" column and the "Q.No." columns in the downloaded template excel EXACTLY as per the given format of answer. </label>
                </div>

                <div class="mb-2">
                    <form action="" method="post" enctype="multipart/form-data" id="import_offline_results">
                        <label>Step 3: Upload the updated Offline RESULT Excel File here (.xlsx): *</label>
                        <input type="file" id="excel_file" name="excel_file" required />
                        <div class="text-center" style="margin-top: 16px;">
                            <button type="submit" class="btn btn-primary submitBtn">Import Results</button>
                        </div>
                    </form>
                </div>
            </div>

            <div id="uploaded_file_progress">
                <div class="my-2">
                    <div id="uploaded_file_count"></div>
                    <div class="progress" id="file_progress_bar">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" id="bulk_file_progress_bar"></div>
                    </div>
                </div>
                <div id="student_data">
                    <table class="table table-bordered table-condensed">
                        <thead>
                            <tr>
                                <th>Student Username</th>
                                <th>Student Name</th>
                                <th>Successful Questions Imported</th>
                                <th>Errors</th>
                            </tr>
                        </thead>
                        <tbody id="student_data_tbody">

                        </tbody>
                    </table>

                </div>
            </div>

            <div class="text-center" id="success_div">
                <h4 class="text-success">Imported Offline Results Successfully</h4>
                <a class='btn btn-success' href="<?= base_url('tests/revaluate_result/' . $test_id); ?>" target='_blank'>Generate Result</a>
            </div>

        </div>
    </div>

    <!-- Include Footer -->
    <?php include_once(APPPATH . "Views/footer.php"); ?>


    <script>
        $("#uploaded_file_progress").hide();
        $("#success_div").hide();

        var instituteId = "<?= $decrypt_institute_id ?>";
        var test_id = "<?= $decrypt_test_id ?>";
        $('#import_offline_results').submit(function(evt) {
            $("#uploaded_file_progress").show();
            $(".submitBtn").attr("disabled", true);
            evt.preventDefault();
            var f = document.getElementById('excel_file').files[0];
            var fd = new FormData();
            fd.append("file", f);
            $.ajax({
                url: base_url + "/tests/import_offline_results_async",
                type: "POST",
                data: fd,
                success: function(result) {
                    Snackbar.show({
                        pos: 'top-center',
                        text: 'File uploaded successfully, please wait while we process uploaded data...'
                    });
                    process_excel_data(result);
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    $(".submitBtn").attr("disabled", false);
                    $("#error").text("Error in uploading .. Please try again ..");
                },
                cache: false,
                contentType: false,
                processData: false
            });
        });

        async function process_excel_data(data) {
            if (data != null) {
                data = JSON.parse(data);
                for (var i = 1; i < data.length; i++) {
                    var current_file_count = i;
                    var file_upload_percent = Math.round(current_file_count * 100 / data.length);
                    $("#uploaded_file_count").html("Processing " + current_file_count + " of " + data.length);
                    var row_data = data[i];
                    var result = await import_excel_data(row_data);
                    $("#student_data_tbody").append(format_result_data(result));
                    $("#bulk_file_progress_bar").css('width', file_upload_percent + '%').attr('aria-valuenow', file_upload_percent);
                };
                $("#file_progress_bar").hide();
                $("#uploaded_file_count").html("Uploaded " + data.length + " files");
                $(".submitBtn").attr("disabled", false);
                $("#success_div").show();
            }
        }

        function import_excel_data(row_data) {
            var test_id = "<?= $decrypt_test_id ?>";
            return new Promise(function(resolve, reject) {
                var xhr = $.ajax({
                        type: "POST",
                        data: {
                            row_data: row_data,
                            test_id: test_id,
                            omr_check: "0"
                        },
                        url: base_url + "/tests/process_excel_data",
                    })
                    .done(function(response) {
                        resolve(response);
                    })
                    .fail(function(jqXHR) {
                        reject(jqXHR.responseText);
                    });
            });
        }
    </script>

    <script>
        function format_result_data(data) {
            var html = "";
            if (data != null) {
                data = JSON.parse(data);
                if (data.length == 0) {
                    html = html + "<tr><td colspan='4'> Student data not found ...</td></tr>";
                }
                html = html + "<tr>";
                html = html + "<td>" + data.user_name + "</td>";
                html = html + "<td>" + data.student_name + "</td>";
                html = html + "<td>" + data.no_of_successful_entries + "</td>";
                html = html + "<td>" + data.errors + "</td>";
                html = html + "</tr>";
            }
            return html;
        }
    </script>