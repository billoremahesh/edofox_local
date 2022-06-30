<!-- Include Header -->
<?php include_once(APPPATH . "Views/header.php"); ?>
<!-- Students View -->

<!-- Custom CSS -->
<link href="<?php echo base_url('assets/css/students/overview.css?v=20220331'); ?>" rel="stylesheet" />

<div id="content">
    <div class="container-fluid mt-4">
        <div class="flex-container-column">
            <div>
                <label class="h5 text-gray-800 text-uppercase"><?= $title; ?></label>
            </div>
            <div class="breadcrumb_div" aria-label="breadcrumb">
                <ol class="breadcrumb_custom">
                    <li class="breadcrumb_item"><a href="<?php echo base_url('/home'); ?>"> Home </a></li>
                    <li class="breadcrumb_item active" aria-current="page"> <?= $title; ?> </li>
                </ol>
            </div>
        </div>

        <div class="bg-white w-100 shadow rounded p-2 mb-5">

            <div>
                <div class="d-flex flex-row-reverse mb-2">

                    <div class="input-group input-group-sm flex-nowrap search_input_wrap">
                        <span class="input-group-text" id="addon-search">
                            <i onclick="resetDatatable()" data-bs-toggle="tooltip" title="Reset saved table settings like hidden columns, search, etc" class='material-icons' id="refresh_icon">refresh</i>
                        </span>


                        <input class="form-control text-black-50" id='searchbox' autocomplete="off" style='width: 130px; color: blue; font-weight: bold;' placeholder="Search" name="srch-term" type="text" value="<?= (isset($_SESSION['student_search_string']) && !empty($_SESSION['student_search_string'])) ? $_SESSION['student_search_string'] : ''; ?>" placeholder="Search" aria-label="Search" aria-describedby="addon-search">

                    </div>




                    <button type="button" class="btn btn-sm text-black-50 d-flex align-items-center justify-content-center me-1" type="button" data-bs-toggle="collapse" data-bs-target="#studentsTableSettingsCollapse" aria-expanded="false" aria-controls="studentsTableSettingsCollapse">
                        <i class='material-icons' data-bs-toggle="tooltip" title="Table Settings">tune</i>
                        <span>Table Settings</span>
                    </button>

                    <button type="button" class="btn btn-sm text-black-50 d-flex align-items-center justify-content-center me-1" type="button" data-bs-toggle="collapse" data-bs-target="#studentsListFilterCollapse" aria-expanded="false" aria-controls="studentsListFilterCollapse">
                        <i class='material-icons' data-bs-toggle="tooltip" title="Apply Filters">filter_list</i>
                        <span>Apply Filters</span>
                    </button>

                    <button type="button" class='btn btn-sm text-black-50 d-flex align-items-center justify-content-center me-1' onclick="show_edit_modal('modal_div','signup_link','students/student_signup_modal/<?= $instituteID; ?>');" data-bs-toggle="tooltip" title="Send Sign Up Link to Students">

                        <i class="material-icons">share</i>
                        <span>Sign up Link</span>

                    </button>

                    <!-- Send Notification -->
                    <button type="button" class='btn btn-sm text-black-50 d-flex align-items-center justify-content-center me-1' onclick="show_edit_modal('modal_div','student_notification_modal','students/student_notification_modal');" data-bs-toggle="tooltip" title="Send SMS/Email notification">
                        <i class='material-icons'>mail</i>
                        <span>Notify</span>
                    </button>

                    <?php if (in_array("manage_students", session()->get('perms')) or in_array("all_perms", session()->get('perms'))) :  ?>


                        <div class="btn-group">
                            <button type="button" class="btn btn-outline-success btn-sm d-flex align-items-center justify-content-center me-1 dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="material-icons">upload_file</i>
                                Add Students
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" onclick="show_edit_modal('modal_div','add_student_modal','students/add_student_modal');">Add Single Student</a></li>

                                <li><a class="dropdown-item" href="<?= base_url('/students/import_bulk_students'); ?>">Import Students from Excel</a></li>
                            </ul>
                        </div>



                    <?php endif; ?>


                </div>
            </div>

            <div class="collapse" id="studentsListFilterCollapse">
                <div class="row my-2">

                    <div class="col-12">
                        <label class="text-muted text-uppercase font-weight-bold">Apply Filters:</label>
                    </div>

                    <div class="col-md-3">
                        <div class="m-1">
                            <?php
                            $filtertype_values_array = array("" => "Show All", "student" => "Show Students", "Disabled" => "Show Blocked", "Deleted" => "Show Deleted", "UnPaid" => "UnPaid", "Paid" => "Paid");
                            ?>
                            <select class="form-select form-select-sm student_list_filter" id="filtertype" name="filtertype">
                                <?php foreach ($filtertype_values_array as $key => $filtertype_value_display) : ?>
                                    <option value='<?= $key ?>' <?= (isset($_SESSION['student_list_filtertype']) && $_SESSION['student_list_filtertype'] === $key) ? "selected" : "" ?>><?= $filtertype_value_display ?></option>
                                <?php endforeach; ?>
                            </select>

                        </div>
                    </div>


                    <div class="col-md-9">
                        <!-- Classroom Filter -->
                        <div class="m-1">
                            <select class="form-select form-select-sm student_list_filter" id="package_filter_dropdown" name="package_filter_dropdown[]" multiple="multiple">
                                <option value="all">Show All Classrooms</option>
                                <?php
                                foreach ($classroom_list as $classroom) {
                                    if (isset($_SESSION['student_list_classroom_filter']) && is_array($_SESSION['student_list_classroom_filter']) && !empty($_SESSION['student_list_classroom_filter']) && in_array($classroom['id'], $_SESSION['student_list_classroom_filter'])) {
                                        // Showing selected filter values from session
                                        echo "<option value='" . $classroom['id'] . "' selected>" . $classroom['package_name'] . "</option>";
                                    } else {
                                        echo "<option value='" . $classroom['id'] . "'>" . $classroom['package_name'] . "</option>";
                                    }
                                }
                                ?>

                            </select>
                        </div>
                    </div>


                </div>
            </div>

            <div class="collapse" id="studentsTableSettingsCollapse">
                <div id="dataTables_search_box_div" class="d-flex justify-content-end my-2"></div>
            </div>



            <h6 class="text-center my-3">
                <badge class="badge bg-info" id="total_students"></badge>
            </h6>

            <div class="table-responsive table_custom">
                <table class="table w-100" id="studentListTable">
                    <thead>
                        <tr>
                            <th>Student ID </th>
                            <th>#</th>
                            <th>Roll No</th>
                            <th>Student Name</th>
                            <th>Mobile</th>
                            <th>Classroom Enrolled</th>
                            <th>Username</th>
                            <th data-bs-toggle="tooltip" title="Tests Taken/Total Tests">Tests</th>
                            <th>Joined on</th>
                            <th>Email</th>
                            <th>Gender</th>
                            <th>Category</th>
                            <th>PrevMarks</th>
                            <th>Parent Mob</th>
                            <th>Pic</th>
                            <th>Acc.Type</th>
                            <th>Addtional Details</th>
                            <th class="not_to_export not_to_print">...</th>

                        </tr>
                    </thead>
                </table>
            </div>




        </div>
    </div>
</div>
<!-- Include Footer -->
<?php include_once(APPPATH . "Views/footer.php"); ?>


<script>
    var studentListTable;
    var instituteId = <?= $institute_details['id']; ?>;
    // To reset all the parameters in the datatable which are saved using stateSave
    function resetDatatable() {
        // Resetting the filter values
        $("#filtertype").val("");
        $("#package_filter_dropdown").val("");
        // $("#studentListTable_filter input").val("");
        $("#searchbox").val("");
        studentListTable.draw();


        // REF: https://datatables.net/forums/discussion/53726/table-state-clear-does-not-clear-saved-state-how-do-i-remove-specified-filters-sorts-here
        studentListTable.state.clear(); // 1a - Clear State
        studentListTable.destroy(); // 1b - Destroy

        setTimeout(function() {
            // Reload after few seconds
            window.location.reload();
        }, 1000);
    }


    $(document).ready(function() {
        var classrooms_selected = $("#package_filter_dropdown").find(':selected').text();
        if (classrooms_selected == "") {
            classrooms_selected = "All Classrooms";
        }
        var institute_name = "<?= $institute_details['alias_name']; ?>";
        var institute_logo_url = "<?= $institute_details['logo_path']; ?>";
        var tableTopData = "<div style='position:relative;'><img src='" + institute_logo_url + "' alt='institute logo' style='width:40px;position:absolute;left:1cm;' /><h1>" + institute_name + "</h1><h3>" + classrooms_selected + "</h3><h5>Student List<h5></div>";
        var messageTopDataExcel = institute_name + "\n" + classrooms_selected + "\n Student List";
        studentListTable = $('#studentListTable').DataTable({
            stateSave: true,
            "columnDefs": [{
                    "targets": [0, 7, 9, 10, 11, 12, 13, 14],
                    "visible": false,
                },
                {
                    "targets": [0, 1, 10],
                    "orderable": false,
                }, {
                    "targets": -1,
                    "class": 'btn_col'
                }
            ],
            "order": [
                [2, "asc"]
            ],
            dom: 'Bflrtip',
            "lengthMenu": [
                [10, 25, 50, 100, 150, 200],
                [10, 25, 50, 100, 150, 200]
            ],
            buttons: [{
                "extend": 'excel',
                "titleAttr": 'Excel',
                "action": newexportaction,
                // not_export class is used to hide excel columns. 
                "exportOptions": {
                    "columns": ':visible:not(.not_to_export)'
                },
                messageTop: messageTopDataExcel
            }, {
                extend: 'colvis',
                //https://datatables.net/forums/discussion/50751/hide-several-columns-for-colvis-button-list
                columns: ':gt(0)',
                text: "Toggle Columns"
            }, {
                extend: 'print',
                exportOptions: {
                    columns: ':visible:not(.not_to_print)'
                },
                messageTop: tableTopData,
                customize: function(doc) {
                    $(doc.document.body).find('h1').css('font-size', '15pt');
                    $(doc.document.body).find('h1').css('text-align', 'center');
                    $(doc.document.body).find('h3').css('font-size', '13pt');
                    $(doc.document.body).find('h3').css('text-align', 'center');
                    $(doc.document.body).find('h5').css('font-size', '11pt');
                    $(doc.document.body).find('h5').css('text-align', 'center');
                    $(win.document.body).css('font-size', '9px');
                    $(win.document.body).find('td').css('padding', '0px');
                    $(win.document.body).find('td').css('padding-left', '2px');
                }
            }],
            "searching": true,
            "paging": true,
            "pageLength": 50,
            "bInfo": false,
            "processing": true,
            "serverSide": true,
            "ajax": {
                'url': base_url + "/students/student_list",
                "type": "POST",
                "data": function(d) {
                    d.filtertype = $('#filtertype').val(),
                        d.package = $('#package_filter_dropdown').val(),
                        d.searchbox = $("#searchbox").val()
                }
            },
            language: {
                search: "",
                processing: '<i class="fas fa-atom fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>'
            },
            'drawCallback': function(data) {
                console.log(data.json);
                $('#total_students').html(data.json.recordsFiltered + " students found");
            },
            "dataSrc": "Data",
            "fnRowCallback": function(nRow, aData, iDisplayIndex) {
                //console.log(userdatatable.page.info());
                var info = studentListTable.page.info();
                var index = iDisplayIndex + 1 + info.page * info.length;
                $('td:eq(0)', nRow).html(index);
                return nRow;
            }
        });

        // Moved Datatable Search box and Page length option
        // And other table settings
        $("#dataTables_search_box_div").append($(".dt-buttons"));
        $("#dataTables_search_box_div").append($("#studentListTable_length"));

        $("#studentListTable_length select").removeClass('form-select')
        $(".dt-buttons .buttons-colvis").removeClass('btn-secondary').addClass('btn-sm btn-outline-secondary mx-2');
        $(".dt-buttons .buttons-print").removeClass('btn-secondary').addClass('btn-sm btn-outline-secondary mx-2');

        // $("#studentListTable_filter input").attr('placeholder', 'Search');
        // $("#studentListTable_filter").addClass('py-2 px-1');


        $("#searchbox").keyup(function() {
            studentListTable.draw();
        });

        // Onchange of custom filters
        $(".student_list_filter").change(function() {
            studentListTable.draw();
        });

    });


    function waitForElementToDisplay(selector, time, counter) {
        if (counter > 6) {
            return;
        }
        if (document.querySelector(selector) != null) {
            $("#studentListTable_filter").prepend($("#studentListTableExportGroup"));
            $("#studentListTableExportGroup").show();
            return;
        } else {
            setTimeout(function() {
                waitForElementToDisplay(selector, time, counter + 1);
            }, time);
        }
    }

    $(document).ready(function() {
        waitForElementToDisplay("#studentListTable_filter", 1000, 1);
    });
</script>

<script>
    // When the filter collapse opens, then apply select2 properties for some bugs solution
    // Otherwise the placeholder text does not show up
    var studentsListFilterCollapse = document.getElementById('studentsListFilterCollapse')
    studentsListFilterCollapse.addEventListener('shown.bs.collapse', function() {
        // do something...
        $('#package_filter_dropdown').select2({
            closeOnSelect: false,
            placeholder: "Select Classrooms",
            allowClear: true,
            tags: true,
            width: "100%"
        });

        // https://stackoverflow.com/questions/62571420/select2-jquery-remove-searching-term-after-selecting-an-item?rq=1
        $("#package_filter_dropdown").on('select2:select', function(e) {
            $('.select2-search__field').val('');
        });

    })
</script>


<script>
    function send_account_invite(username) {

        var request = {
            institute: {
                id: instituteId
            },
            student: {
                rollNo: username
            },
            requestType:'InviteActivate'
        };

        callAdminServiceJSONPost("sendNotification", request).then(function(response) {
                if (response.status.statusCode > 0) {
                    Snackbar.show({
                        pos: 'top-center',
                        text: 'Account invite sent successfully'
                    });
                } else {
                    Snackbar.show({
                        pos: 'top-center',
                        text: 'Some error occured in sending the invite notification'
                    });
                }
            })
            .catch(function(error) {
                Snackbar.show({
                    pos: 'top-center',
                    text: 'Error in service call'
                });

            });
    }
</script>


<script>
    function send_whatsapp_invite(student_id) {

        var request = {
            institute: {
                id: instituteId
            },
            student: {
                id: student_id,
                whatsappOptIn: 1
            },
            requestType:'WHATSAPP_OPT_IN'
        };

        callAdminServiceJSONPost("updateStudentInfo", request).then(function(response) {
                if (response.statusCode > 0) {
                    Snackbar.show({
                        pos: 'top-center',
                        text: 'WhatsApp invite sent successfully'
                    });
                } else {
                    Snackbar.show({
                        pos: 'top-center',
                        text: 'Some error occured in sending the invite notification'
                    });
                }
            })
            .catch(function(error) {
                Snackbar.show({
                    pos: 'top-center',
                    text: 'Error in service call'
                });

            });
    }
</script>