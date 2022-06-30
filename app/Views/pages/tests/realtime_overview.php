<!-- Include Header -->
<?php include_once(APPPATH . "Views/header.php"); ?>

<!-- Custom CSS -->
<link href="<?php echo base_url('assets/css/tests/realtime_overview.css?v=20210915'); ?>" rel="stylesheet">

<div id="content">
    <div class="container-fluid mt-4">

        <div class="flex-container-column">
            <div>
                <label class="h5 text-gray-800 text-uppercase"> <?= $title; ?> </h5>
            </div>
            <div class="breadcrumb_div" aria-label="breadcrumb">
                <ol class="breadcrumb_custom">
                    <li class="breadcrumb_item"><a href="<?php echo base_url('/home'); ?>"> Home </a></li>
                    <li class="breadcrumb_item"><a href="<?php echo base_url('tests'); ?>"> Tests </a></li>
                    <li class="breadcrumb_item active" aria-current="page"> <?= $title; ?> </li>
                </ol>
            </div>
        </div>

        <div class="container-fluid">

            <div class="text-center mb-4">
                <h5><?= $test_details['test_name']; ?></h5>
            </div>
            <div class="row">
                <div class="col-md-2">
                    <div class="top-counts-block">
                        <div>
                            <img src="https://edofox-s3.s3.ap-south-1.amazonaws.com/public/statics/writing.png" style="width:32px" />
                        </div>

                        <div style="margin-left: 16px;">
                            <label class="counts-subtitle">Currently Active</label>
                            <h4 class="count-number">
                                <span id="active-students-count">
                                    <?php
                                    if (!empty($test_active_students)) :
                                        echo $test_active_students;
                                    else :
                                        echo 0;
                                    endif;
                                    ?>
                                </span>
                            </h4>
                        </div>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="top-counts-block">
                        <div>
                            <img src="https://edofox-s3.s3.ap-south-1.amazonaws.com/public/statics/police-siren-warning.png" style="width:32px" />
                        </div>

                        <div style="margin-left: 16px;">
                            <label class="counts-subtitle">STARTED by</label>
                            <h4 class="count-number">
                                <span id="test_started_count">
                                    <?php
                                    if (!empty($test_started_by)) :
                                        echo $test_started_by;
                                    else :
                                        echo 0;
                                    endif;
                                    ?>
                                </span>
                            </h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="top-counts-block">
                        <div>
                            <img src="https://edofox-s3.s3.ap-south-1.amazonaws.com/public/statics/verified-pad-check.png" style="width:32px" />
                        </div>

                        <div style="margin-left: 16px;">
                            <label class="counts-subtitle">COMPLETED by</label>
                            <h4 class="count-number">
                                <span id="test_completed_count">
                                    <?php
                                    if (!empty($test_completed_by)) :
                                        echo $test_completed_by;
                                    else :
                                        echo 0;
                                    endif;
                                    ?>
                                </span>
                            </h4>
                        </div>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="top-counts-block">
                        <div>
                            <img src="https://edofox-s3.s3.ap-south-1.amazonaws.com/public/statics/magnifying-glass-eye.png" style="width:32px" />
                        </div>

                        <div style="margin-left: 16px;">
                            <label class="counts-subtitle">Avg Visited Count</label>
                            <h4 class="count-number">
                                <span id="result_avg_visited_count">
                                    <?php
                                    if (!empty($test_avg_visited_count)) :
                                        echo $test_avg_visited_count;
                                    else :
                                        echo 0;
                                    endif;
                                    ?>
                                </span>
                            </h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="top-counts-block">
                        <div>
                            <img src="https://edofox-s3.s3.ap-south-1.amazonaws.com/public/statics/solved.png" style="width:32px" />
                        </div>

                        <div style="margin-left: 16px;">
                            <label class="counts-subtitle">Avg Solved Count</label>
                            <h4 class="count-number">
                                <span id="result_avg_solved_count">
                                    <?php
                                    if (!empty($test_avg_solved_count)) :
                                        echo $test_avg_solved_count;
                                    else :
                                        echo 0;
                                    endif;
                                    ?>
                                </span>
                            </h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="top-counts-block">
                        <div>
                            <img src="https://edofox-s3.s3.ap-south-1.amazonaws.com/public/statics/clock-time-left.png" style="width:32px" />
                        </div>

                        <div style="margin-left: 16px;">
                            <label class="counts-subtitle">Avg Time Left</label>
                            <h4 class="count-number"><?= $test_avg_time_left ?> Min</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">

                <div class="col-md-4">
                </div>
                <div class="col-md-4">
                    <div class="text-center">
                        <span id="toggle-device-info-btn" style="cursor: pointer;"> <i class="fa fa-toggle-off" aria-hidden="true"></i> Device Info</span>
                    </div>
                    <div class="real_time_overview_card p-2" id="device-info-chart-block">
                        <figure class="highcharts-figure">
                            <div id="pie-chart-div"></div>
                        </figure>
                    </div>
                </div>
                <div class="col-md-4"></div>
            </div>
        </div>


        <div class="card shadow p-4 mt-4">

            <div class="row">
                <?php
                if ($is_test_past) :
                    //To show the reevaluate result button after the test is in the past and when all the students have completed tests
                ?>
                    <div class="col-xs-12 text-end">
                        <a class="btn btn-success" href="<?= base_url('tests/revaluate_result/' . $test_id); ?>" id="reevaluate_test_button">Reevaluate Result</a>
                    </div>
                <?php
                endif;
                ?>

                <!-- Displaying started and completed tests count -->
                <div class="col-xs-4">
                    <div id="loading_div" style="display: none;">
                        <i class='fas fa-atom fa-spin fa-2x fa-fw'></i>
                    </div>
                </div>
            </div>



            <?php if (isset($test_details['accept_location']) && $test_details['accept_location'] == 1) : ?>
                <p><a style="float:right" target="_blank" href="<?= base_url('/tests/realtime_location_view/' . $test_id); ?>"><i class="fas fa-2x fa-map-marker-alt"></i></a></p>

            <?php endif; ?>

            <hr>

            <div class="row">
                <div class="col-md-12">
                    <label for="student"> Select Student </label>
                    <select class="form-control-user select_student_select_two" name="student_list" id="student_list" style="width: 100%;" onchange="searchstudent();">
                        <option value=''> Select </option>
                        <?php
                        if (!empty($students_list)) :
                            foreach ($students_list as $row) {
                                echo "<option value=" . $row['id'] . ">" . $row['name'] . "(" . $row['roll_no'] . ")</option>";
                            }
                        endif;
                        ?>
                    </select>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12" id="result_div"></div>
            </div>


            <?php
            if ($is_test_past) :
            ?>
                <hr />
                <div class="text-center">
                    <label class="text-success">You may now submit everyone's tests</label>
                    <br>
                    <button class="btn btn-danger" onclick="submitAllOngoingTests(<?= $decrypt_test_id ?>)">Submit everyone's tests</button>
                </div>
            <?php
            else :
            ?>
                <hr />
                <div class="text-center">
                    <label class="text-danger">As test has not yet ended, you cannot submit everyone's tests. Please refresh this page after test ends</label>
                </div>
            <?php
            endif;
            ?>


        </div>
    </div>
</div>

<!-- Include Footer -->
<?php include_once(APPPATH . "Views/footer.php"); ?>

<script src="<?php echo base_url('assets/js/manage_test.js'); ?>"></script>

<script>
    var encrypted_test_id = "<?= $test_id; ?>";
    $(document).ready(function() {
        $(".select_student_select_two").select2();
        fetchDeviceCount();
        $('#device-info-chart-block').hide();
    });
</script>

<script>
    // To show or hide device info chart
    $('#toggle-device-info-btn').click(function() {
        $("#device-info-chart-block").toggle();
        if ($(this).children("i").hasClass("fa-toggle-off")) {
            $(this).children("i").removeClass("fa-toggle-off");
            $(this).children("i").addClass("fa-toggle-on");
        } else {
            $(this).children("i").removeClass("fa-toggle-on");
            $(this).children("i").addClass("fa-toggle-off");
        }
    });
</script>

<script>
    function fetchDeviceCount() {

        $.ajax({
            url: base_url + "/tests/fetch_device_distribution",
            method: 'POST',
            data: {
                test_id: "<?= $test_id; ?>"
            },
            success: function(result) {
                console.log("result", result);
                var response = JSON.parse(result);
                if (response != null && response.length > 0) {
                    var app_count = 0;
                    var mobile_count = 0;
                    var web_count = 0;
                    response.forEach(function(row) {
                        console.log(row);
                        var devicesHtml = '';
                        if (row['device'] != 'null' && row['device'] != '' && row['device'] != null) {

                            if (row['device'] == 'app') {
                                // $("#appCount").text("Mobile App " + row[0]);
                                app_count = row['device_count'];
                            }
                            if (row['device'] == 'mobile') {
                                // $("#mobileCount").text("Mobile Browser " + row[0]);
                                mobile_count = row['device_count'];
                            }
                            if (row['device'] == 'web') {
                                // $("#webCount").text("Web " + row[0]);
                                web_count = row['device_count'];
                            }
                        }
                    });
                    generate_device_count_chart(app_count, mobile_count, web_count);
                }
            }
        });

    }


    //To asynchronously submit all ongoing tests (STARTED to COMPLETED)
    function submitAllOngoingTests(test_id) {
        var result = confirm("WARNING! \nAre you sure you want to submit all STARTED tests? This cannot be undone.");
        if (result) {
            submitOngoingTests(test_id)
                .then(function(result) {
                    var response = JSON.parse(result);
                    console.log("response", response);
                    if (response.statusCode == 200) {
                        Snackbar.show({
                            pos: 'top-center',
                            text: response.responseText + ".\n We will now REEVALUATE RESULT again for this test."
                        });
                        window.location = base_url + 'tests/revaluate_result/' + encrypted_test_id;
                    } else {
                        console.log(response.responseText);
                    }

                })
                .catch(function(error) {
                    // An error occurred
                    alert("Exception: " + error);
                });
        }


    }
</script>


<script>
    function searchstudent() {
        // will get Student id
        var student_id = $('#student_list').val();
        var test_id = "<?= $test_id ?>";

        if (student_id != "") {
            $.ajax({
                url: base_url + "/tests/realtime_student_overview",
                method: 'POST',
                data: {
                    student_id: student_id,
                    test_id: test_id
                },
                success: function(result) {
                    $('#result_div').html(result);
                }
            });
        }
    }
</script>





<script>
    function generate_device_count_chart(app_count, mobile_count, web_count) {
        console.log("generate chart");
        Highcharts.chart('pie-chart-div', {
            credits: {
                enabled: false
            },
            exporting: {
                enabled: false
            },
            chart: {
                spacingTop: 0,
                spacingBottom: 0,
                spacingLeft: 0,
                spacingRight: 0,
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                type: 'pie',
                height: '300px'
            },
            title: {
                text: 'Device Info'
            },
            tooltip: {
                pointFormat: '{series.name}: <b>{point.y}</b>'
            },
            accessibility: {
                point: {
                    valueSuffix: '%'
                }
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: false
                    },
                    showInLegend: true,
                }
            },
            series: [{
                name: 'Device',
                colorByPoint: true,
                data: [{
                    name: 'Mobile App = ' + Number(app_count),
                    y: Number(app_count),
                }, {
                    name: 'Mobile Browser = ' + Number(mobile_count),
                    y: Number(mobile_count)
                }, {
                    name: 'Web = ' + Number(web_count),
                    y: Number(web_count)
                }]
            }]
        });
    }
</script>