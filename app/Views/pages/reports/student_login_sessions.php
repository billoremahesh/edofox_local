<!-- Include Header -->
<?php include_once(APPPATH . "Views/header.php"); ?>

<!-- Custom CSS -->
<link href="<?php echo base_url('assets/css/reports/student_login_sessions.css?v=20220511'); ?>" rel="stylesheet">

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
                <div class="text-center">
                    <badge class="badge bg-info fw-bold fs-6" id="total_student_login_sessions"></badge>
                </div>

                <div id="delete_token_modal_div" style="display: none;">
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#delete_student_token_modal">
                        Delete Tokens
                    </button>
                </div>

                <div class="d-flex flex-row-reverse">

                    <div class="input-group input-group-sm flex-nowrap search_input_wrap">

                        <span class="input-group-text" id="addon-search">
                            <i onclick="resetDatatable()" data-bs-toggle="tooltip" title="Reset saved table settings like hidden columns, search, etc" class='material-icons' id="refresh_icon">refresh</i>
                        </span>

                        <input class="form-control text-black-50" id='searchbox' autocomplete="off" style='width: 130px; color: blue; font-weight: bold;' placeholder="Search" name="srch-term" type="text" value="<?= (isset($_SESSION['student_login_session_search']) && !empty($_SESSION['student_login_session_search'])) ? $_SESSION['student_login_session_search'] : ''; ?>" placeholder="Search" aria-label="Search" aria-describedby="addon-search">

                    </div>


                </div>
            </div>


            <div class="table-responsive table_custom">
                <table class="table table-bordered table-hover table-sm" id="studentLoginSessionsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th> # </th>
                            <th> Name </th>
                            <th> Username </th>
                            <th> Created Date </th>
                            <th> Device Type </th>
                            <th> Device Info </th>
                            <th> Module </th>
                            <th class="not_to_export not_to_print"> <input type="checkbox" id="selectAll" /> Select All Visible </th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>



        </div>
    </div>
</div>


<div class="modal fade" id="delete_student_token_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"> Delete Student Tokens </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <?php echo form_open('students/delete_student_live_login_sessions'); ?>
            <div class="modal-body">
                <p> Are you sure, you want to delete selected student tokens?</p>
            </div>
            <div class="modal-footer">
                <input type="hidden" name="live_session_ids" id="live_session_ids" required />
                <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-danger"> Yes </button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>


<!-- Include Footer -->
<?php include_once(APPPATH . "Views/footer.php"); ?>

<script>
    var studentLoginSessionsTable;
    var institute_id = "<?= $instituteID; ?>";
    // To reset all the parameters in the datatable which are saved using stateSave
    function resetDatatable() {
        // Resetting the filter values
        $("#searchbox").val("");
        studentLoginSessionsTable.draw();


        // REF: https://datatables.net/forums/discussion/53726/table-state-clear-does-not-clear-saved-state-how-do-i-remove-specified-filters-sorts-here
        studentLoginSessionsTable.state.clear(); // 1a - Clear State
        studentLoginSessionsTable.destroy(); // 1b - Destroy

        setTimeout(function() {
            window.location.reload();
        }, 1000);
    }

    $(document).ready(function() {
        studentLoginSessionsTable = $('#studentLoginSessionsTable').DataTable({
            stateSave: true,
            "columnDefs": [{
                "targets": [7],
                "orderable": false,
            }, {
                "targets": -1,
                "class": 'btn_col'
            }],
            "order": [
                [0, "asc"]
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
                messageTop: "Student Login Sessions"
            }, {
                extend: 'print',
                exportOptions: {
                    columns: ':visible:not(.not_to_print)'
                },
                title: "Student Login Sessions ",
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
                'url': base_url + "/reports/load_student_sessions",
                "type": "POST",
                "data": function(d) {
                    d.searchbox = $("#searchbox").val(),
                        d.institute_id = institute_id
                }
            },
            language: {
                search: "",
                processing: '<i class="fas fa-atom fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>'
            },
            'drawCallback': function(data) {
                $('#total_student_login_sessions').html("Total Student Login Sessions: " + data.json.recordsFiltered);
                initializeTooltip();
            },
            "dataSrc": "Data",
            "fnRowCallback": function(nRow, aData, iDisplayIndex) {
                //console.log(userdatatable.page.info());
            }
        });

        $("#searchbox").keyup(function() {
            studentLoginSessionsTable.draw();
        });
    });



    function waitForElementToDisplay(selector, time, counter) {
        if (counter > 6) {
            return;
        }
        if (document.querySelector(selector) != null) {
            $("#studentLoginSessionsTable_filter").prepend($("#studentLoginSessionsTableExportGroup"));
            $("#studentLoginSessionsTableExportGroup").show();
            return;
        } else {
            setTimeout(function() {
                waitForElementToDisplay(selector, time, counter + 1);
            }, time);
        }
    }

    $(document).ready(function() {
        waitForElementToDisplay("#studentLoginSessionsTable_filter", 1000, 1);
    });
</script>

<script>
    // For selecting all blocked students
    $('#selectAll').click(function(e) {
        $(this).closest('table').find('td input:checkbox').prop('checked', this.checked);
        selected_student_login_sessions();
    });
</script>

<script>
    // Selected test count
    function selected_student_login_sessions() {
        var checkedValue = [];
        let length = $('.student_sessions_check:checkbox:checked').length;
        var arr = [];
        $.each($(".student_sessions_check:checkbox:checked"), function() {
            arr.push($(this).val());
        });
        var selected_students_sessions_ids = arr.join(", ");
        $("#live_session_ids").val(selected_students_sessions_ids);
        if (length > 0) {
            $("#delete_token_modal_div").show();
        } else {
            $("#delete_token_modal_div").hide();
        }
    }
</script>