<!-- Include Header -->
<?php include_once(APPPATH . "Views/header.php"); ?>

<!-- Custom CSS -->
<link href="<?php echo base_url('assets/css/tests/overview.css?v=20211215'); ?>" rel="stylesheet">

<div id="content">
    <div class="container-fluid mt-4">

        <div class="flex-container-column">
            <div>
                <label class="h5 text-gray-800 text-uppercase"> <?= $title; ?> </h5>
            </div>
            <div class="breadcrumb_div" aria-label="breadcrumb">
                <ol class="breadcrumb_custom">
                    <li class="breadcrumb_item"><a href="<?php echo base_url('/home'); ?>"> Home </a></li>
                    <li class="breadcrumb_item active" aria-current="page"> <?= $title; ?> </li>
                </ol>
            </div>
        </div>


        <div class="d-flex justify-content-between mb-4">

            <div class="tests_top_cards">
                <div class="top-counts-block">
                    <div>
                        <img src="https://edofox-s3.s3.ap-south-1.amazonaws.com/public/statics/writing.png" style="width:32px" />
                    </div>

                    <div style="margin-left: 16px;">
                        <label class="counts-subtitle">Active Students in last 5 min</label>
                        <h4 class="count-number">
                            <span id="active-students-count">
                                <?= $tests_active_students; ?>
                            </span>
                        </h4>
                    </div>
                </div>
            </div>

            <div class="tests_top_cards" id="unsubmitted-students-count-card">
                <a href="<?= base_url('/tests/unsubmitted_tests_students'); ?>" class="top-counts-block">
                    <div>
                        <img src="https://edofox-s3.s3.ap-south-1.amazonaws.com/public/statics/police-siren-warning.png" style="width:32px" />
                    </div>

                    <div style="margin-left: 16px;">
                        <label class="counts-subtitle">Unsubmitted Past Students</label>
                        <h4 class="count-number">
                            <span id="unsubmitted-students-count">
                                <?= $unsubmitted_tests_students; ?>
                            </span>
                        </h4>
                    </div>
                </a>
            </div>

            <div class="tests_top_cards">
                <div class="top-counts-block">
                    <div>
                        <img src="<?= base_url('assets/img/icons/exam.png'); ?>" style="width:32px" />
                    </div>

                    <div style="margin-left: 16px;">
                        <label class="counts-subtitle"> Total Tests </label>
                        <h4 class="count-number">
                            <span id="unsubmitted-students-count">
                                <?= $total_test_cnt; ?>
                            </span>
                        </h4>
                    </div>
                </div>
            </div>

            <div class="tests_top_cards">
                <a href="<?= base_url('/tests/deleted_tests'); ?>" class="top-counts-block">
                    <div>
                        <img src="<?= base_url('assets/img/icons/recycle.png'); ?>" style="width:32px" />
                    </div>

                    <div style="margin-left: 16px;">
                        <label class="counts-subtitle"> Deleted Tests </label>
                        <h4 class="count-number">
                            <span id="unsubmitted-students-count">
                                <?= $deleted_tests_count; ?>
                            </span>
                        </h4>
                    </div>
                </a>
            </div>

            <div class="col-md-3">
                <a href="<?= base_url('/testTemplates'); ?>" class="top-counts-block">
                    <div>
                        <img src="<?= base_url('assets/img/icons/template.png'); ?>" style="width:32px" />
                    </div>

                    <div style="margin-left: 16px;">
                        <label class="counts-subtitle"> Test Templates </label>
                        <h4 class="count-number">
                            <span id="unsubmitted-students-count">
                                <?= $test_templates_count; ?>
                            </span>
                        </h4>
                    </div>
                </a>
            </div>
        </div>
    </div>





    <div class="card">
        <div class="card-body">
            <div class="d-flex flex-row-reverse m-2">


                <?php if (in_array("manage_tests", session()->get('perms')) or in_array("all_perms", session()->get('perms'))) :  ?>
                    <a class='action_button_plus_custom custom_btn ripple-effect' href="<?= base_url('tests/add_new_test'); ?>">
                        <i class='action_button_plus_icon material-icons' data-bs-toggle="tooltip" title="Add New Test">add</i>
                    </a>
                <?php endif; ?>


                <button class="btn btn-sm text-black-50 d-flex align-items-center justify-content-center me-2" type="button" data-bs-toggle="collapse" data-bs-target="#testsFilterCollapse" aria-expanded="false" aria-controls="testsFilterCollapse">
                    <i class='material-icons'>filter_list</i>
                    <span>Apply Filters</span>
                </button>

            </div>



            <div class="collapse mb-2" id="testsFilterCollapse">
                <div class="row" style="border: 2px solid #eee; border-radius: 4px; padding: 4px;">

                    <div class="col-12">
                        <label class="text-muted text-uppercase font-weight-bold">Apply Filters:</label>
                    </div>

                    <div class="col-md-4 my-2">
                        <select class="form-select form-select-sm test_filters" name="subject_filter" id="test_filter_dropdown">
                            <option value="all">Show Exams + Assignments</option>
                            <option value="exams">Show Exams</option>
                            <option value="assignments">Show Assignments</option>
                        </select>
                    </div>
                    <!-- Classroom Filter -->

                    <div class="col-md-4 my-2">
                        <select class="form-select form-select-sm test_filters" id="test_package_filter_dropdown" name="test_package_filter">
                            <option value="all" selected>Show All Classrooms</option>
                            <?php
                            foreach ($classroom_list as $classroom) :
                            ?>
                                <option value="<?= $classroom['id']; ?>"><?= $classroom['package_name']; ?></option>
                            <?php
                            endforeach;
                            ?>
                        </select>
                    </div>
                    <div class="col-md-4 my-2" style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="input-group input-group-sm mx-1" data-bs-toggle="tooltip" title="Filter tests by date. Select FROM date.">
                            <span class="input-group-text bg-white" id="from-date-tests-filter"><i class="fa fa-filter" aria-hidden="true"></i></span>
                            <input type="text" class="form-control" id="filterstart_date" name="filterstart_date" placeholder="From Date" aria-label="Filter Tests From Date" aria-describedby="from-date-tests-filter">
                        </div>

                        <div class="input-group input-group-sm mx-1" data-bs-toggle="tooltip" title="Filter tests by date. Select TO date.">
                            <span class="input-group-text bg-white" id="to-date-tests-filter"><i class="fa fa-filter" aria-hidden="true"></i></span>
                            <input type="text" class="form-control" id="filterend_date" name="filterend_date" placeholder="To Date" aria-label="Filter Tests TO Date" aria-describedby="to-date-tests-filter">
                        </div>

                        <div class="mb-2">
                            <input type="hidden" id="formatted_filterstart_date" name="formatted_filterstart_date">
                            <input type="hidden" id="formatted_filterend_date" name="formatted_filterend_date">
                        </div>
                    </div>
                </div>
            </div>


            <div class='tabbed-data'>
                <?php

                //To show the active tab if saved the state in cookie 
                //This is to show whichever tab was previously clicked on
                $active_tests_tab_class = "";
                $active_tests_content_class = "";
                $upcoming_tests_tab_class = "";
                $upcoming_tests_content_class = "";
                $past_tests_tab_class = "";
                $past_tests_content_class = "";

                if (isset($_COOKIE['test_active_tab'])) {
                    if ($_COOKIE['test_active_tab'] == "ACTIVE") {
                        $active_tests_tab_class = "active";
                        $active_tests_content_class = " show active";
                    }

                    if ($_COOKIE['test_active_tab'] == "UPCOMING") {
                        $upcoming_tests_tab_class = "active";
                        $upcoming_tests_content_class = " show active";
                    }

                    if ($_COOKIE['test_active_tab'] == "PAST") {
                        $past_tests_tab_class = "active";
                        $past_tests_content_class = " show active";
                    }
                } else {
                    $active_tests_tab_class = "active";
                    $active_tests_content_class = " show active";
                }
                ?>

                <!-- Nav tabs -->
                <ul class="nav nav-pills nav-justified mb-3" role="tablist" id="test_list_tabs">
                    <li role="presentation" class="nav-item">
                        <a class="nav-link <?= $active_tests_tab_class ?>" aria-controls="active" role="tab" data-bs-toggle="tab" data-bs-target="#active" onclick="saveActiveTab(this)">ACTIVE</a>
                    </li>
                    <li role="presentation" class="nav-item">
                        <a class="nav-link <?= $upcoming_tests_tab_class ?>" aria-controls="upcoming" role="tab" data-bs-toggle="tab" data-bs-target="#upcoming" onclick="saveActiveTab(this)">UPCOMING</a>
                    </li>
                    <li role="presentation" class="nav-item">
                        <a class="nav-link <?= $past_tests_tab_class ?>" aria-controls="past" role="tab" data-bs-toggle="tab" data-bs-target="#past" onclick="saveActiveTab(this)">PAST</a>
                    </li>
                </ul>

                <!-- Tab panes -->
                <div class="tab-content">
                    <div id="loader" style="display:none;"><i class='fas fa-atom fa-spin fa-2x fa-fw'></i></div>
                    <div role="tabpanel" class="tab-pane fade <?= $active_tests_content_class ?>" id="active">
                        <table class="table table-condensed table-hover" id='manageTest'>
                            <thead class="d-none">
                                <tr>
                                    <th>Test Name</th>
                                    <th> </th>
                                </tr>
                            </thead>
                            <tbody id="active_test_tbody">
                                <tr>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div role="tabpanel" class="tab-pane fade <?= $upcoming_tests_content_class ?>" id="upcoming">
                        <table class="table table-condensed table-hover" id='upcomingTest'>
                            <thead class="d-none">
                                <tr>
                                    <th>Test Name</th>
                                    <th> </th>
                                </tr>
                            </thead>
                            <tbody id="upcoming_test_tbody">
                                <tr>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div role="tabpanel" class="tab-pane fade <?= $past_tests_content_class ?>" id="past">
                        <table class="table table-condensed table-hover" id='pastTest'>
                            <thead class="d-none">
                                <tr>
                                    <th>Test Name</th>
                                    <th> </th>
                                </tr>
                            </thead>
                            <tbody id="past_test_tbody">
                                <tr>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>


            <div id="manageTestExportGroup" class="export-icon-group" style="display: none;margin-right:15px;">
                <img class="export-icon" onclick='dtExport("manageTest_wrapper","excel");' src="<?= base_url('assets/img/download-excel-512x512.png'); ?>" alt='Excel' height='16' width='16'>
            </div>


        </div>
    </div>
</div>

<!-- Include Footer -->
<?php include_once(APPPATH . "Views/footer.php"); ?>
<script src="<?php echo base_url('assets/js/manage_test.js'); ?>"></script>



<script>
    function loader(show) {
        if (show) {
            $("#loader").attr("style", "text-align:center");
        } else {
            $("#loader").attr("style", "display:none");
        }
    }


    function loadTest(type) {

        loader(true);
        var test_filter_para = {
            instituteID: "<?= $instituteID ?>",
            requestType: type,
            durationType: $('#test_filter_dropdown').val(),
            start: $('#formatted_filterstart_date').val(),
            end: $('#formatted_filterend_date').val(),
            classroom: $('#test_package_filter_dropdown').val()
        };
        $.ajax({
            url: base_url + "/tests/load_tests",
            type: 'POST',
            dataType: "html",
            data: JSON.stringify(test_filter_para),
            contentType: 'application/json',
            success: function(response) {

                // loader(false);
                if (type == 'active') {

                    if ($.fn.DataTable.isDataTable('#manageTest')) {
                        $('#manageTest').dataTable().fnDestroy();
                    }
                    $("#active_test_tbody").html(response);
                    var manageTest = $('#manageTest').DataTable({
                        "lengthMenu": [
                            [10, 25, 50, -1],
                            [10, 25, 50, "All"]
                        ],
                        "pageLength": 25,
                        "columnDefs": [{
                            "targets": [1],
                            "orderable": false,
                        }],
                        "order": [],
                        stateSave: true
                    });
                    loader(false);
                } else if (type === 'upcoming') {
                    if ($.fn.DataTable.isDataTable('#upcomingTest')) {
                        $('#upcomingTest').dataTable().fnDestroy();
                    }
                    $("#upcoming_test_tbody").html(response);
                    var upcomingTest = $('#upcomingTest').DataTable({
                        "lengthMenu": [
                            [10, 25, 50, -1],
                            [10, 25, 50, "All"]
                        ],
                        "pageLength": 25,
                        "columnDefs": [{
                            "targets": [1],
                            "orderable": false,
                        }],
                        "order": [],
                        stateSave: true
                        // dom: 'Blfrtip',
                        // buttons: [ 'excel' ]
                    });
                    loader(false);
                } else {
                    if ($.fn.DataTable.isDataTable('#pastTest')) {
                        $('#pastTest').dataTable().fnDestroy();
                    }
                    $("#past_test_tbody").html(response);
                    var pastTest = $('#pastTest').DataTable({
                        "lengthMenu": [
                            [10, 25, 50, -1],
                            [10, 25, 50, "All"]
                        ],
                        "pageLength": 25,
                        "columnDefs": [{
                            "targets": [1],
                            "orderable": false,
                        }],
                        "order": [],
                        stateSave: true
                        // dom: 'Blfrtip',
                        // buttons: [ 'excel' ]
                    });
                    loader(false);
                }

                $('[data-toggle="tooltip"]').tooltip();
            }
        });
    }

    function loadTests() {
        $("#active_test_tbody").html("");
        $("#active_test_tbody").empty();
        $("#upcoming_test_tbody").html("");
        $("#upcoming_test_tbody").empty();
        $("#past_test_tbody").html("");
        $("#past_test_tbody").empty();

        loadTest('active');
        loadTest('upcoming');
        loadTest('past');
    }
    $(document).ready(function() {

        //Load tests
        loadTests();

        $('#test_filter_dropdown').change(function() {
            loadTests();
        });


        // classroom Filter
        $('#test_package_filter_dropdown').change(function() {
            loadTests();
        });

        $('#test_package_filter_dropdown').select2({
            width: "100%"
        });

        //Select date range with datepicker jquery
        //https://jqueryui.com/datepicker/#date-range
        var dateFormat = "yy-mm-dd",
            from = $("#filterstart_date")
            .datepicker({
                defaultDate: "+1w",
                changeMonth: true,
                changeYear: true,
                dateFormat: "yy-mm-dd",
                showAnim: "slideDown"
            })
            .on("change", function() {
                to.datepicker("option", "minDate", getDate(this));
            }),
            to = $("#filterend_date").datepicker({
                defaultDate: "+1w",
                changeMonth: true,
                changeYear: true,
                dateFormat: "yy-mm-dd",
                showAnim: "slideDown"
            })
            .on("change", function() {
                from.datepicker("option", "maxDate", getDate(this));
            });

        function getDate(element) {
            var date;
            try {
                date = $.datepicker.parseDate(dateFormat, element.value);
            } catch (error) {
                date = null;
            }

            return date;
        }




        $('#filterstart_date, #filterend_date').change(function() {
            if ($('#filterstart_date').val() && $('#filterend_date').val()) {
                // Formatting the date in required format before sending the values to the datatable for loading tests
                $("#formatted_filterstart_date").val($('#filterstart_date').val() + " 00:00");
                $("#formatted_filterend_date").val($('#filterend_date').val() + " 00:00");

                //Loading the filtered tests
                loadTests();
            }
        });



        //To solve select2 bug where search does not work in bootstrap modal
        $.fn.modal.Constructor.prototype.enforceFocus = function() {};
        //Initializing select2
        $('#add_test_package').select2({
            width: "100%"
        });


    });


    function waitForElementToDisplay(selector, time, counter) {
        if (counter > 6) {
            return;
        }
        if (document.querySelector(selector) != null) {
            $("#manageTest_filter").prepend($("#manageTestExportGroup"));
            //$("#manageTestExportGroup").show();
            return;
        } else {
            setTimeout(function() {
                waitForElementToDisplay(selector, time, counter + 1);
            }, time);
        }
    }

    function dtExport(sContainerName, sType) {
        var sButtonName = '';
        switch (sType) {
            case "excel":
                sButtonName = "buttons-excel";
                break;
            case "pdf":
                sButtonName = "buttons-pdf";
                break;
        }

        $("#" + sContainerName + " ." + sButtonName).click();
    }

    $(document).ready(function() {

        waitForElementToDisplay("#manageTest_filter", 1000, 1);

    });
</script>


<script type="text/javascript">
    $(".form_datetime1").datetimepicker({
        format: "yyyy-mm-dd hh:ii",
        autoclose: true,
        todayBtn: true,
        fontAwesome: 'font-awesome',
        pickerPosition: "bottom-left"
    });

    $(".form_datetime2").datetimepicker({
        format: "yyyy-mm-dd hh:ii",
        autoclose: true,
        todayBtn: true,
        fontAwesome: 'font-awesome',
        pickerPosition: "bottom-left"
    });

    /**@abstract
     * To toggle the test duration selector dropdown based on the TEST TYPE value
     */
    function toggleDurationSelect(value) {
        // alert(value);
        if (value === "DPP") {
            $(".test-duration-div").hide();
            $(".test-duration-div select").prop('required', false);
        } else {
            $(".test-duration-div").show();
            $(".test-duration-div select").prop('required', true);
        }
    }

    //To save the active tab state after coming back from another page
    function saveActiveTab(element) {
        //setting the cookie for donation
        createCookie("test_active_tab", element.innerText, 1);
    }
</script>