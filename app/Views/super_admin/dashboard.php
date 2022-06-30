<!-- Include Header -->
<?php include_once(APPPATH . "Views/header.php"); ?>

<!-- Custom CSS -->
<link href="<?php echo base_url('assets/css/super_admin_dashboard.css?v=20210829'); ?>" rel="stylesheet">

<div class="container-fluid mt-4">


    <div class="row">

        <div class="col-lg-4 mb-4 d-flex flex-column">
            <div class="chart chart-sm shadow bg-white rounded w-100" id="weekly_tests_chart_div">

            </div>

            <div class="chart chart-sm shadow bg-white rounded w-100 my-2" id="weekly_student_logins_chart_div">

            </div>
        </div>
        <div class="col-lg-8">
            <div class="w-100">
                <div class="row">
                    <div class="col-6 mb-4">
                        <a class="card_box_link" href="<?= base_url('/institutes'); ?>">
                            <div class="p-3 bg-white rounded shadow d-flex align-items-center h-100">
                                <div>
                                    <img src="<?= base_url('assets/img/icons/calendar.png'); ?>" style="width: 48px;" />
                                </div>

                                <div style="margin-left: 16px;">
                                    <label class="counts-subtitle"> Total Institutes </label>
                                    <h4 class="count-number" id="total_videos_count_text"><?= indian_number_format($institute_cnt); ?></h4>
                                </div>
                            </div>
                        </a>
                    </div>


                    <div class="col-6  mb-4">
                        <div class="p-3 bg-white rounded shadow d-flex align-items-center h-100">
                            <div>
                                <img src="<?= base_url('assets/img/icons/calendar.png'); ?>" style="width: 48px;" />
                            </div>

                            <div style="margin-left: 16px;">
                                <label class="counts-subtitle"> Total Test Submissions </label>
                                <h4 class="count-number" id="total_videos_count_text"><?= indian_number_format($total_submission_cnt); ?></h4>
                            </div>
                        </div>
                    </div>

                    <div class="col-6  mb-4">
                        <div class="p-3 bg-white rounded shadow d-flex align-items-center h-100">
                            <div>
                                <img src="<?= base_url('assets/img/icons/teamwork.png'); ?>" style="width: 48px;" />
                            </div>

                            <div style="margin-left: 16px;">
                                <label class="counts-subtitle"> Total Active Students </label>
                                <h4 class="count-number" id="total_videos_count_text"><?= indian_number_format($total_student_cnt); ?></h4>
                            </div>
                        </div>
                    </div>


                    <div class="col-6  mb-4">
                        <div class="p-3 bg-white rounded shadow d-flex align-items-center h-100">
                            <div>
                                <img src="<?= base_url('assets/img/icons/teamwork.png'); ?>" style="width: 48px;" />
                            </div>

                            <div style="margin-left: 16px;">
                                <label class="counts-subtitle"> Ongoing Student Count </label>
                                <h4 class="count-number" id="total_videos_count_text"><?= indian_number_format($test_ongoing_stu_cnt); ?></h4>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 mb-4">
                        <div class="card rounded shadow">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3" style="position: relative;">
                                    <div class="d-flex flex-column align-items-center gap-1">
                                        <h2 class="mb-2"> <?= indian_number_format($total_test_cnt) ?> </h2>
                                        <span>Total Tests </span>
                                    </div>
                                </div>
                                <ul class="p-0 m-0">
                                    <li class="d-flex mb-1 pb-1">
                                        <div class="avatar flex-shrink-0 me-3">
                                            <span class="avatar-initial rounded bg-label-primary">
                                                <img src="<?= base_url('assets/img/icons/calendar.png'); ?>" style="width: 28px;" />
                                            </span>
                                        </div>
                                        <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                            <div class="me-2">
                                                <h6 class="mb-0">Todays Test</h6>
                                            </div>
                                            <div class="user-progress">
                                                <small class="fw-semibold"> <?= indian_number_format($todays_test_cnt) ?> </small>
                                            </div>
                                        </div>
                                    </li>

                                    <li class="d-flex mb-1 pb-1">
                                        <div class="avatar flex-shrink-0 me-3">
                                            <span class="avatar-initial rounded bg-label-primary">
                                                <img src="<?= base_url('assets/img/icons/calendar.png'); ?>" style="width: 28px;" />
                                            </span>
                                            </span>
                                        </div>
                                        <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                            <div class="me-2">
                                                <h6 class="mb-0">Tomorrows Test</h6>
                                            </div>
                                            <div class="user-progress">
                                                <small class="fw-semibold"> <?= indian_number_format($tomorrows_cnt) ?> </small>
                                            </div>
                                        </div>
                                    </li>


                                    <li class="d-flex mb-1 pb-1">
                                        <div class="avatar flex-shrink-0 me-3">
                                            <span class="avatar-initial rounded bg-label-primary">
                                                <img src="<?= base_url('assets/img/icons/calendar.png'); ?>" style="width: 28px;" />
                                            </span>
                                            </span>
                                        </div>
                                        <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                            <div class="me-2">
                                                <h6 class="mb-0">Planned Test</h6>
                                            </div>
                                            <div class="user-progress">
                                                <small class="fw-semibold"> <?= indian_number_format($total_planned_test_cnt) ?> </small>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 mb-4">
                        <div class="card rounded shadow">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3" style="position: relative;">
                                    <div class="d-flex flex-column align-items-center gap-1">
                                        <h2 class="mb-2"> <?= indian_number_format($total_student_cnt) ?> </h2>
                                        <span> Total Students </span>
                                    </div>
                                </div>
                                <ul class="p-0 m-0">
                                    <li class="d-flex mb-1 pb-1">
                                        <div class="avatar flex-shrink-0 me-3">
                                            <span class="avatar-initial rounded bg-label-primary">
                                                <img src="<?= base_url('assets/img/icons/teamwork.png'); ?>" style="width: 28px;" />
                                            </span>
                                        </div>
                                        <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                            <div class="me-2">
                                                <h6 class="mb-0"> Expected attendance of Todays tests </h6>
                                            </div>
                                            <div class="user-progress">
                                                <small class="fw-semibold"> <?= indian_number_format($todays_test_stu_cnt) ?> </small>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="d-flex mb-1 pb-1">
                                        <div class="avatar flex-shrink-0 me-3">
                                            <span class="avatar-initial rounded bg-label-primary">
                                                <img src="<?= base_url('assets/img/icons/teamwork.png'); ?>" style="width: 28px;" />
                                            </span>
                                        </div>
                                        <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                            <div class="me-2">
                                                <h6 class="mb-0"> Expected attendance of Tomorrows tests </h6>
                                            </div>
                                            <div class="user-progress">
                                                <small class="fw-semibold"> <?= indian_number_format($tomorrows_test_stu_cnt) ?> </small>
                                            </div>
                                        </div>
                                    </li>
                                    <li class="d-flex mb-1 pb-1">
                                        <div class="avatar flex-shrink-0 me-3">
                                            <span class="avatar-initial rounded bg-label-primary">
                                                <img src="<?= base_url('assets/img/icons/teamwork.png'); ?>" style="width: 28px;" />
                                            </span>
                                        </div>
                                        <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                            <div class="me-2">
                                                <h6 class="mb-0"> Expected attendance of Planned tests </h6>
                                            </div>
                                            <div class="user-progress">
                                                <small class="fw-semibold"> <?= indian_number_format($total_stu_planned_test_cnt) ?> </small>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card p-4 m-2">

        <h2 class="text-center"> Institute Wise Count</h2>
        <div class="d-flex justify-content-between my-2">
            <select class="form-select date_filter" id="test_filter" style="max-width: 250px;">
                <option value="today" selected> Today's Test </option>
                <option value="tommrow"> Tomorrows Test </option>
                <option value="yesterday"> Yesterday's test </option>
                <option value="planned"> Planned Test </option>
            </select>



            <div class="text-center">
                <badge class="badge bg-info fw-bold fs-6">
                    Total Tests: <span id="total_tests"></span>
                </badge>
            </div>

        </div>

        <div class="d-flex justify-content-between my-2">

            <!-- Moved Datatable Page Length Menu -->
            <div id="dataTables_length_div"></div>

            <!-- Moved Datatable Search box -->
            <div id="dataTables_search_box_div"></div>
        </div>

        <table class="table table-bordered table-condensed" id="test_custom_data_tble">
            <thead>
                <tr>
                    <th> # </th>
                    <th> Institute Name </th>
                    <th> Tests </th>
                    <th> External Login </th>
                </tr>
            </thead>

        </table>

    </div>

</div>

<!-- Include Footer -->
<?php include_once(APPPATH . "Views/footer.php"); ?>

<script src="<?php echo base_url('assets/js/super_admin_dashboard.js?v=20220421'); ?>"></script>


<script>
    var test_custom_data_tble;
    $(document).ready(function() {
        $('[data-toggle="tooltip"]').tooltip();
        test_custom_data_tble = $('#test_custom_data_tble').DataTable({
            stateSave: true,
            "columnDefs": [{
                "targets": [3],
                "orderable": false,
            }, {
                "targets": -1,
                "class": 'btn_col'
            }],
            "order": [
                [2, "asc"]
            ],
            dom: 'Bflrtip',
            "lengthMenu": [
                [10, 25, 50, 100],
                [10, 25, 50, 100]
            ],
            buttons: [{
                extend: 'colvis',
                //https://datatables.net/forums/discussion/50751/hide-several-columns-for-colvis-button-list
                columns: ':gt(0)',
                text: "Toggle Columns"
            }, {
                extend: 'print',
                exportOptions: {
                    columns: ':visible:not(.not_export)'
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
            "pageLength": 50,
            "bInfo": false,
            "processing": true,
            "serverSide": true,
            "ajax": {
                'url': base_url + "/tests/load_institutes_wise_tests_count",
                "type": "POST",
                "data": function(d) {
                    d.test_filter = $('#test_filter').val()
                }
            },
            language: {
                search: "",
                processing: '<i class="fas fa-atom fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>'
            },
            'drawCallback': function(data) {
                // console.log(data.json);
                $('#total_tests').html(data.json.recordsFiltered);
            },
            "dataSrc": "Data",
            "fnRowCallback": function(nRow, aData, iDisplayIndex) {
                //console.log(userdatatable.page.info());
            }
        });

        // Onchange of custom filters
        $(".date_filter").change(function() {
            test_custom_data_tble.draw();
        });

    });


    // To reset all the parameters in the datatable which are saved using stateSave
    function resetDatatable() {
        test_custom_data_tble.draw();
        // REF: https://datatables.net/forums/discussion/53726/table-state-clear-does-not-clear-saved-state-how-do-i-remove-specified-filters-sorts-here
        test_custom_data_tble.state.clear(); // 1a - Clear State
        test_custom_data_tble.destroy(); // 1b - Destroy
        setTimeout(function() {
            // Reload after few seconds
            window.location.reload();
        }, 1000);
    }
</script>