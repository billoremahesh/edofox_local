<!-- Include Header -->
<?php include_once(APPPATH . "Views/header.php"); ?>

<!-- Custom CSS -->
<link href="<?php echo base_url('assets/css/tests/omr_test_evalution.css?v=20220326'); ?>" rel="stylesheet">

<script>

    var noOfQuestions = null;
    var testCode = null;
    try {
        noOfQuestions = <?=$test_details['no_of_questions']?>;
        <?php if(isset($test_details['test_code'])) { ?>
        testCode = <?=$test_details['test_code']?>;
        <?php } ?>
        console.log("found no of questions as " + noOfQuestions + " and " + testCode);
    } catch (e) {
        console.log(e);
    }

</script>

<div id="content">
    <div class="container-fluid mt-4">

        <div class="flex-container-column">
            <div>
                <label class="h5 text-gray-800 text-uppercase"> <?= $test_details['test_name']; ?> </h5>
            </div>
            <div class="breadcrumb_div" aria-label="breadcrumb">
                <ol class="breadcrumb_custom">
                    <li class="breadcrumb_item"><a href="<?php echo base_url('/home'); ?>"> Home </a></li>
                    <li class="breadcrumb_item"><a href="<?php echo base_url('tests'); ?>"> Tests </a></li>
                    <li class="breadcrumb_item active" aria-current="page"> <?= $title; ?> </li>
                </ol>
            </div>
        </div>

        <div class="card p-4">

            <div class="text-center">
                <h4> <?= $test_details['test_name']; ?></h4>
                <?php if(isset($omr_template)) : ?>
                    <p class="text-info">Omr Template selected for evaluation: <b><?=$omr_template['omr_template_name']?></b></p>
                <?php else: ?>
                    <p class="text-danger">No Omr Template selected for evaluation. Default Edofox template will be used. Please update the OMR template from Update Test Properties section.</p>
                <?php endif; ?>
            </div>
            <div class="row">

                <div class="col-3 mb-3">
                    <label for="accessType" class="form-label"> Student Identifier </label>
                    <select class="form-control" id="accessType">
                        <option value="RollNo"> RollNo </option>
                        <option value="Username"> Username </option>
                    </select>
                </div>

                <div class="col-3 mb-3">
                    <label for="prefix" class="form-label"> Prefix </label>
                    <input type="text" class="form-control" type="prefix" id="prefix" maxlength="10">
                </div>

                <div class="col-4 mb-3">
                    <label for="multiple_omr_sheets" class="form-label"> Upload Multiple OMR Sheets </label>
                    <input class="form-control" type="file" id="multiple_omr_sheets" multiple>
                </div>

                <div class="col-2 mb-3 mt-4">
                    <button type="button" class="btn btn-primary" onclick="checkOmr('multiple_omr_sheets');"> Evaluate </button>
                </div>

                <div class="col-4 mb-3 mt-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="1" id="preview_result_check">
                        <label class="form-check-label" for="preview_result_check">
                            Only Preview Result (Result will not be saved)
                        </label>
                    </div>
                </div>
                <div class="col-4 mb-3 mt-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox"  id="compress_files">
                        <label class="form-check-label" for="compress_files">
                            Compress files(Recommended Only for mobile uploads)
                        </label>
                    </div>
                </div>
                <div class="col-4 mb-3 mt-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox"  id="scanned_output">
                        <label class="form-check-label" for="scanned_output">
                            Get Scanned Output
                        </label>
                    </div>
                </div>

                <div class="col-4 mb-3">
                    <button type="button" class="btn btn-primary" onclick="generateCodeModal();"> Generate Exam code  </button>
                    <label class="form-check-label">(For OMR Desktop/Android app)</label>
                </div>
            </div>

            <div class="my-2">
                <div id="uploaded_file_count"></div>
                <div class="progress" id="file_progress_bar">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" id="bulk_file_progress_bar"></div>
                </div>
                <div id="status_count">
                    <span id="success_count" class="badge bg-success"></span>
                    <span id="failed_count" class="badge bg-danger"></span>
                </div>
            </div>


            <div class="my-1" id="show_result_div" style="display: none;">
                <a class="btn btn-primary" target='_blank' href="<?= base_url('tests/show_test_result/1/' . $encrypted_test_id); ?>"> Show Test Result/Analysis </a>
            </div>

            <div class="my-1" id="result_div" style="display: none;">

                <table class="table table-bordered table-condensed" id="omr_uploaded_data">
                    <thead>
                        <tr id="tbl_headers">
                            <th> Username </th>
                            <th> Name </th>
                            <th> Roll Number </th>
                            <th> Solved Count </th>
                            <th> File Name</th>
                            <th> Uploaded File </th>
                            <th> Reupload File </th>
                            <th> Scanned OMR </th>
                        </tr>
                    </thead>
                    <tbody id="tbl_body">
                    </tbody>
                </table>

            </div>


            <div id="import_file_div" style="display: none;">
                <hr />
                <div class="text-center mb-2">
                    <h5>Upload OMR Result Manually</h5>
                </div>

                <div class="mb-2">
                    <label>Step 1: Download Excel from the table </label>
                </div>

                <div class="mb-2">
                    <label>Step 2: Update relavant fields if required </label>
                </div>

                <div class="mb-2">
                    <form action="" method="post" enctype="multipart/form-data" id="import_offline_results">
                        <label>Step 3: Upload the updated Excel File here (.xlsx): *</label>
                        <input type="file" id="excel_file" name="excel_file" required />
                        <div class="text-center" style="margin-top: 16px;">
                            <button type="submit" class="btn btn-primary submitBtn">Import Results</button>
                        </div>
                    </form>
                </div>
            </div>


            <div id="uploaded_file_progress" style="display: none;">
                <div class="my-2">
                    <div id="uploaded_file_count2"></div>
                    <div class="progress" id="file_progress_bar1">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" id="bulk_file_progress_bar1"></div>
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

            <div class="text-center" id="success_div" style="display: none;">
                <h4 class="text-success">Imported Offline Results Successfully</h4>
                <a class='btn btn-success' href="<?= base_url('tests/revaluate_result/' . $encrypted_test_id); ?>" target='_blank'>Generate Result</a>
            </div>

        </div>

    </div>
</div>


<div id="reupload_omr_modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"> Reupload OMR </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-8 mb-3">
                        <label for="single_omr_sheet" class="form-label"> Upload OMR Sheet </label>
                        <input class="form-control" type="file" id="single_omr_sheet">
                        <input id="reupload_hidden_id" type="hidden" value="">
                    </div>
                </div>
                <p id="upload_warning" style="color:red"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" onclick="checkOmr('single_omr_sheet');"> Reupload </button>
            </div>
        </div>
    </div>
</div>

<div id="generate_exam_code_modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"> Generate Exam Code </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <p><span id="test_code_preview"></span>&nbsp;<i style="cursor:pointer" onclick="copyTestCode()" class="fa fa-clipboard" aria-hidden="true"></i></p>
                </div>
                <p id="generate_code_progress"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" onclick="generateCode()"> Generate </button>
            </div>
        </div>
    </div>
</div>

<!-- Include Footer -->
<?php include_once(APPPATH . "Views/footer.php"); ?>
<script src="<?php echo base_url('assets/js/utils.js'); ?>"></script>
<script src="<?php echo base_url('assets/js/omr.js?ver=20220704'); ?>"></script>
<script src="<?php echo base_url('assets/js/common.js?ver=20220627'); ?>"></script>

<script>
    var omr_datatable = null;
    var in_progress = false;

    function createDatatable(input_type) {
        if (omr_datatable != null) {
            if(input_type == 'single_omr_sheet') {
                return;
            }
            omr_datatable.destroy();
            //return;
        }
        omr_datatable = $('#omr_uploaded_data').DataTable({
            // "order": [
            //     [0, "asc"]
            // ],
            dom: 'Bflrtip',
            buttons: [{
                extend: 'colvis',
                //https://datatables.net/forums/discussion/50751/hide-several-columns-for-colvis-button-list
                columns: ':gt(0)',
                text: "Toggle Columns"
            }, {
                "extend": 'excel',
                "titleAttr": 'Excel',
                // "action": newexportaction,
                // not_export class is used to hide excel columns. 
                "exportOptions": {
                    "columns": ':visible:not(.not_to_export)'
                },
                messageTop: "OMR Result"
            }, {
                extend: 'print',
                exportOptions: {
                    columns: ':visible:not(.not_to_print)'
                },
                title: "OMR Result ",
                customize: function(win) {
                    $(win.document.body).find('h1').css('text-align', 'center');
                    $(win.document.body).css('font-size', '9px');
                    $(win.document.body).find('td').css('padding', '0px');
                    $(win.document.body).find('td').css('padding-left', '2px');
                }
            }],
            "searching": true,
            "paging": true,
            "scrollX": true,
            "pageLength": 200,
            "bLengthChange": false,
            "bInfo": false,
            "ordering": false
        });
    }
</script>

<script>
    var FILE_SIZE_LIMIT = 256 * 1024; //500 KB
    $("#result_div").hide();
    $("#file_progress_bar").hide();
    $("#show_result_div").hide();

    var addedHeaders = false;

    async function checkOmr(file_input) {
        $("#result_div").show();
        $("#reupload_omr_modal").modal('hide');
        var files = document.getElementById(file_input).files;
        if (files == null || files == undefined) {
            alert("Please select a file to upload!");
            return;
        }
        var test_id = "<?= $test_id; ?>";
        var instituteId = "<?= $instituteId; ?>";
        var accessType = $("#accessType").val();
        var prefix = $("#prefix").val();
        var preview = false;
        var timeout = 20000;
        if(file_input != 'single_omr_sheet') {
            if (omr_datatable != null) {
                omr_datatable.destroy();
                omr_datatable = null;
            }
            $("#omr_uploaded_data tbody").html("");
            timeout = 10000;
            success = 0;
            failed = 0;
            $("#success_count").text("");
            $("#failed_count").text("");
            console.log("Updated counts ..");
            
        } else {
            var id = "tr" + $("#reupload_hidden_id").val();
            console.log("Evaluating " + id);
            $('td:first', $("#" + id)).text("Reuploading ..");
        }
        console.log(test_id);
        $("#file_progress_bar").show();
        in_progress = true;
        for (var i = 0; i < files.length; i++) {
            var current_file_count = i + 1;
            var file_upload_percent = Math.round(current_file_count * 100 / files.length);
            $("#uploaded_file_count").html("Uploading " + current_file_count + " of " + files.length);
            var requestType = null;
            if ($("#preview_result_check").is(":checked")) {
                requestType = 'PREVIEW';
                preview = true;
            }
            var skipCompress = true;
            var scannedOutput = false;
            if(document.getElementById("compress_files").checked) {
                skipCompress = false;
            }
            if(document.getElementById("scanned_output").checked) {
                scannedOutput = true;
            }

            var result = await parse_omr(test_id, files[i], accessType, prefix, instituteId, requestType, timeout, skipCompress, scannedOutput);
            var file_name = files[i].name;
            if ($("#preview_result_check").is(":checked") && !addedHeaders) {
                //Add Headers
                var headersHtml = "";
                if (result)
                    $("#tbl_headers").append(
                        format_omr_headers(result)
                    );
                addedHeaders = true;
            }
            if(file_input == 'single_omr_sheet') {
                var id = "tr" + $("#reupload_hidden_id").val();
                console.log("Appending result to " + id);
                var omr_response = format_omr_response_as_array(result, $("#reupload_hidden_id").val(), file_name, preview, true);
                $("#" + id).attr("style", "");

                var row = omr_datatable.row('#' + id);
                row.data(omr_response);
                //Update datatable as the date for row is updated..otherwise export doesn't work
                //omr_datatable.fnUpdate(omr_response, $('tr#' + id), undefined, false);

            } else {
                $("#omr_uploaded_data tbody").append(
                    format_omr_response(result, i, file_name, preview, false)
                );
            }
            
            $("#bulk_file_progress_bar").css('width', file_upload_percent + '%').attr('aria-valuenow', file_upload_percent);
        }
        createDatatable(file_input);
        $("#file_progress_bar").hide();
        $("#uploaded_file_count").html("Uploaded " + files.length + " files");
        $("#show_result_div").show();
        in_progress = false;
        if (preview) {
            $("#import_file_div").show();
        } else {
            $("#import_file_div").hide();
        }
    }

    function reuplod_omr_modal(id) {
        $('#reupload_hidden_id').val(id);
        $("#reupload_omr_modal").modal('show');
        if(in_progress) {
            $("#upload_warning").text("Please wait for the existing process to finish before you re upload");
        } else {
            $("#upload_warning").text("");
            $("#single_omr_sheet").val("");
        }
    }

    function generateCodeModal() {
        $("#test_code_preview").text(testCode);
        $("#generate_code_progress").text("");
        $("#generate_exam_code_modal").modal('show');
    }

    function copyTestCode() {
        copyValueToClipboard($("#test_code_preview").text());
        $("#generate_code_progress").text("Copied to clipboard!");
    }

    function generateCode() {
        var request = {
            test: {
                id: test_id
            }
        };

        $("#generate_code_progress").text("Generating new exam code ..");

        callAdminServiceJSONPost("generateTestCode", request).then(function(response) {
                if (response.status.statusCode > 0) {
                    $("#generate_code_progress").text("Generated new code successfully ..");
                    $("#test_code_preview").text(response.test.testCode);
                } else {
                    $("#generate_code_progress").text("Error in generating new code ..");
                }
            })
            .catch(function(error) {
                

            });
    }
</script>


<script>


    var instituteId = "<?= $instituteId ?>";
    var test_id = "<?= $test_id ?>";
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
</script>

<script>
    async function process_excel_data(data) {
        if (data != null) {
            data = JSON.parse(data);
            for (var i = 3; i < data.length; i++) {
                var current_file_count = i-2;
                var file_upload_percent = Math.round(i * 100 / data.length);
                $("#uploaded_file_count2").html("Processing " + current_file_count + " of " + (data.length-3));
                var row_data = data[i];
                var result = await import_excel_data(row_data);
                $("#student_data_tbody").append(format_result_data(result));
                $("#bulk_file_progress_bar1").css('width', file_upload_percent + '%').attr('aria-valuenow', file_upload_percent);
            };
            $("#file_progress_bar1").hide();
            $("#uploaded_file_count2").html("Uploaded " + (data.length-3) + " records");
            $(".submitBtn").attr("disabled", false);
            $("#success_div").show();
        }
    }
</script>
<script>
    function import_excel_data(row_data) {
        var test_id = "<?= $test_id ?>";
        return new Promise(function(resolve, reject) {
            var xhr = $.ajax({
                    type: "POST",
                    data: {
                        row_data: row_data,
                        test_id: test_id,
                        omr_check: "1"
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