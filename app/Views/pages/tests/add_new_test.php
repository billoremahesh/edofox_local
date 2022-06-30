<!-- Include Header -->
<?php include_once(APPPATH . "Views/header.php"); ?>

<!-- Custom CSS -->
<link href="<?php echo base_url('assets/css/tests/realtime_overview.css?v=20210915'); ?>" rel="stylesheet">

<div id="content">
    <div class="container mt-4">

        <div class="flex-container-column">
            <div>
                <label class="h5 text-gray-800"> <?= $title; ?> </h5>
            </div>
            <div class="breadcrumb_div" aria-label="breadcrumb">
                <ol class="breadcrumb_custom">
                    <li class="breadcrumb_item"><a href="<?php echo base_url('/home'); ?>"> Home </a></li>
                    <li class="breadcrumb_item"><a href="<?php echo base_url('tests'); ?>"> Tests </a></li>
                    <li class="breadcrumb_item active" aria-current="page"> <?= $title; ?> </li>
                </ol>
            </div>
        </div>

        <div class="card shadow border border-1 p-4">
            <?php
            $attributes = ['class' => 'cmxform', 'id' => 'add_test_form'];
            ?>
            <?php echo form_open_multipart('tests/add_new_test_submit', $attributes); ?>


            <div class="row">

                <div class="col-md-8">
                    <label class="form_label" for="add_test_name">Test Name</label>
                    <input type="text" class="form-control" name="add_test_name" id="add_test_name" required>
                </div>


                <div class="col-md-4 mb-2">
                    <label class="form_label" for="template_id">Test Templates</label>
                    <select class="form-select" aria-describedby="templateHelp" name="template_id" id="template_id" onchange="render_template_data();">
                        <option value=""></option>
                        <?php
                        if (!empty($test_templates)) {
                            foreach ($test_templates as $template_data) {
                        ?>
                                <option value="<?= $template_data['id']; ?>"> <?= $template_data['template_name']; ?></option>
                        <?php
                            }
                        }
                        ?>
                    </select>
                    <div id="templateHelp" class="form-text">You can select existing template to fill up all entities</div>
                </div>

                <div class="col-md-4 mb-2">
                    <label class="form_label" for="exam_conduction"> Mode of Conduction </label>
                    <select class="form-select" name="exam_conduction" id="exam_conduction" aria-describedby="exam_conductionHelp" onchange="render_omr_template();" required>

                        <?php
                        $mode_of_conduction = array();
                        if (session()->get('exam_feature') == 1) {
                            $mode_of_conduction = array('Online');
                        }

                        if (session()->get('exam_feature') == 2) {
                            $mode_of_conduction = array('Offline');
                        }
                        if (session()->get('exam_feature') == 3) {
                            $mode_of_conduction = array('Online', 'Offline', 'Hybrid');
                        }

                        if (!empty($mode_of_conduction)) {
                            foreach ($mode_of_conduction as $moc) {
                                echo "<option value='$moc'>$moc</option>";
                            }
                        }
                        ?>



                    </select>
                    <div id="exam_conductionHelp" class="form-text">Choose Offline when exam is not going be solved online by any student. Choose Hybrid to keep both options available</div>
                </div>

                <div class="col-md-4 mb-2" id="omr_template_check" style="display: none;">
                    <label class="form_label" for="omr_template"> OMR Template </label>
                    <select class="form-select" name="omr_template" id="omr_template">

                    </select>
                </div>

                <div class="col-md-4 mb-2">
                    <label class="form_label" for="add_test_ui">Test Interface</label>
                    <select class="form-select" name="add_test_ui" id="add_test_ui" required onchange="toggleDurationSelect(this.value)">
                        <option value="JEE">MH CET</option>
                        <option value="JEEM">JEE</option>
                        <option value="NEET">NEET</option>
                        <option value="DPP">DPP/Assignment</option>
                        <option value="MOBILE">Generic</option>
                        <option value="DESCRIPTIVE">Subjective type exam</option>
                        <!-- <option value="PROCTORING">Remote proctoring (Beta)</option>
                                <option value="VIDEO_PROCTOR">Video proctoring (Beta)</option> -->
                        <option value="TAIT">TAIT - Teacher Aptitude and Intelligence Test</option>
                    </select>
                </div>



                <div class="col-md-12 mb-2">
                    <label class="form_label" for="add_test_package">Classrooms</label> (You can select multiple classrooms)
                    <select class="form-select add_package_multiple" id="add_test_package" name="add_test_package[]" multiple="multiple" required>
                        <?php
                        foreach ($classroom_list as $row) {
                            $package_id = $row['id'];
                            $package_name = $row['package_name'];
                            echo "<option value='$package_id'>$package_name</option>";
                        }
                        ?>
                    </select>
                    <div class="form-text">
                        (The test will be visible only to the students added to the classrooms you select above.)
                    </div>
                </div>

                <div class="col-md-4 mb-2">
                    <label class="form_label" for="add_test_no_questions">No. of Questions</label>
                    <input type="number" class="form-control" name="add_test_no_questions" id="add_test_no_questions" required>
                </div>

                <div class="col-md-4 mb-2">
                    <label class="form_label" for="add_test_total_marks">Total Marks</label>
                    <input type="number" class="form-control" name="add_test_total_marks" id="add_test_total_marks" required>
                </div>
            </div>



            <div class="row">

                <?php
                if (isset($_SESSION['timezone']) && $_SESSION['timezone'] != "" && $_SESSION['timezone'] != "Asia/Kolkata") :
                ?>
                    <div class="col-md-12">
                        <div class="badge bg-primary text-break">
                            You are adding test with timezone <?= $_SESSION['timezone']; ?>
                        </div>
                    </div>
                <?php
                endif;
                ?>

                <div class="col-md-4 mb-2">
                    <label for="start_date">Test Start Time</label>
                    <div class="input-append date form_datetime1">
                        <input size="16" type="text" id="start_date" name="start_date" required autocomplete="off">
                        <span class="add-on"><i class="icon-th"></i></span>
                    </div>
                </div>

                <div class="col-md-4 mb-2">
                    <label class="form_label" for="end_date">Test End Time</label>
                    <div class="input-append date form_datetime2">
                        <input size="16" type="text" id="end_date" name="end_date" required autocomplete="off">
                        <span class="add-on"><i class="icon-th"></i></span>
                    </div>
                </div>

                <div class="col-md-4 mb-2 test-duration-div">

                    <div>
                        <label class="form_label" for="add_test_duration">Test Duration </label>
                    </div>
                    <select class="form-select" name="add_test_duration_hours" id="add_test_duration_hours" style="display: inline;max-width: 45%;" required>
                        <option value=""></option>
                        <option value="0">0 Hour</option>
                        <option value="1">1 Hour</option>
                        <option value="2">2 Hours</option>
                        <option value="3">3 Hours</option>
                        <option value="4">4 Hours</option>
                    </select>
                    <select class="form-select" name="add_test_duration_minutes" id="add_test_duration_minutes" style="display: inline;max-width: 45%;" required>
                        <option value=""></option>
                        <?php
                        for ($i = 0; $i < 60; $i++) {
                            echo "<option value='$i'>$i Min</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>


            <div class="row">

                <!-- This sets the timeout value for the test where if student stays out of the test window for this value, then the test will be autosubmitted -->
                <div class="col-md-4 mb-2 online_test_opions">
                    <label class="form_label" for="add_test_timeout_value">Test Timeout in seconds <i class="far fa-question-circle" aria-hidden="true" data-bs-toggle="tooltip" title="If you want to auto submit test after student leaves TEST window for a certain time, to avoid copying, select a suitable value in seconds"></i></label>
                    <select class="form-select" name="test_timeout_value" id="add_test_timeout_value" required>
                        <option value="ALLOW">Allow INFINITE Timeout</option>
                        <?php
                        for ($i = 1; $i <= 12; $i++) {
                            $seconds = $i * 5;
                            echo "<option value='$seconds'>$seconds Sec</option>";
                        }
                        ?>
                    </select>
                </div>

                <!-- This sets the maximum number of times student can blur and focus, leave and reenter exam, exit and start exam. After this, the test will be blocked and has to be activated from admin side -->
                <div class="col-md-4 mb-2 online_test_opions">
                    <label class="form_label" for="add_max_allowed_test_starts">Test START Maximum times <i class="far fa-question-circle" aria-hidden="true" data-bs-toggle="tooltip" title="This sets the maximum number of times student can blur and focus, leave and reenter exam, exit and start exam. After this, the test will be submitted to avoid copying"></i></label>
                    <select class="form-select" name="max_allowed_test_starts" id="add_max_allowed_test_starts" required>
                        <option value="ALLOW">INFINITE times</option>
                        <?php
                        for ($i = 1; $i <= 20; $i++) {
                            echo "<option value='$i'>$i times</option>";
                        }
                        ?>
                    </select>
                </div>



                <div class="col-md-12 mb-2 online_test_opions">
                    <input type="checkbox" name="add_random_questions" id="add_random_questions" value="Y">
                    <label for="add_random_questions">Show Random Questions <i class="far fa-question-circle" aria-hidden="true" data-bs-toggle="tooltip" title="If you want to show random ordering of questions in test, please check this."></i></label>
                </div>

                <div class="col-md-12 mb-2">
                    <input type="checkbox" name="add_show_results" id="add_show_results" value="Y">
                    <label for="add_show_results">Show Result After Test <i class="far fa-question-circle" aria-hidden="true" data-bs-toggle="tooltip" title="This shows student's result after test with marks and correct answers. Check this only if you have added test ANSWER KEY first."></i></label>
                </div>


                <!-- This will show or hide question paper after test and question summary on result page and in app -->
                <div class="col-md-12 mb-2">
                    <input type="checkbox" name="show_ques_paper_post_test" id="add_show_ques_paper_post_test" value="Y">
                    <label for="add_show_ques_paper_post_test">Show Question Paper After Test <i class="far fa-question-circle" aria-hidden="true" data-bs-toggle="tooltip" title="If this is checked, students will be able to see question paper after test is finished and also their test summary with questions."></i></label>
                </div>

                <div class="col-md-12 mb-2 online_test_opions">
                    <input class="form-label" type="checkbox" name="align_test_time" id="align_test_time" value="1">
                    <label for="align_test_time">Align test time with TEST start time <i class="far fa-question-circle" aria-hidden="true" data-bs-toggle="tooltip" title="If this is checked, Test duration will be given based on TEST Start Time."></i></label>
                </div>

                <div class="col-md-12 mb-2 online_test_opions">
                    <input class="form-label" type="checkbox" name="align_with_student_time" id="align_with_student_time" value="1">
                    <label for="align_with_student_time">Align test time with STUDENT start time <i class="far fa-question-circle" aria-hidden="true" data-bs-toggle="tooltip" title="If this is checked, Test duration will be given based on when STUDENT Starts the test."></i></label>
                </div>




                <div class="col-md-12 mb-2 text-right">
                    <a href="#advancedOptions" class="btn" data-bs-toggle="collapse">Advanced Options <i class="fa fa-cog" aria-hidden="true"></i></a>
                </div>

                <div id="advancedOptions" class="col-md-12 mb-2 collapse">
                    <hr>
                    <div class="mb-2 online_test_opions">
                        <input class="form-label" type="checkbox" name="offline_conduction" id="offline_conduction_add" value="0">
                        <label for="offline_conduction_add">Optimal network use <i class="far fa-question-circle" aria-hidden="true" data-bs-toggle="tooltip" title="Setting this option will reduce students' dependency on internet but also reduces reliability of Admin getting realtime exam updates. Select this option only when students' network connectivity is too low or internet bandwidth is divided between students."></i></label>
                    </div>

                    <div class="mb-2 online_test_opions">
                        <input class="form-label" type="checkbox" name="get_students_geolocation" id="get_students_geolocation_add" value="0">
                        <label for="get_students_geolocation_add">Get Students' Geo-locations <i class="far fa-question-circle" aria-hidden="true" data-bs-toggle="tooltip" title="Checking this will force students to send their geolocation so that admin can track where the students are sitting while giving the exam. Please note that the locations may not be accurate and are dependent on user device and network"></i></label>
                    </div>

                    <div class="mb-2">
                        <input class="form-label" type="checkbox" name="show_student_rank" id="show_rank_add" value="1" checked>
                        <label for="show_rank_add">Show Rank in result <i class="far fa-question-circle" aria-hidden="true" data-bs-toggle="tooltip" title="Show rank in result page. If checked, rank is shown. Only applies when the Show Result is checked"></i></label>
                    </div>


                    <div class="mb-2 online_test_opions">
                        <input class="form-label" type="checkbox" name="image_proctoring_check" id="image_proctoring_check" value="0">
                        <label for="image_proctoring_check">Image Proctoring<i class="far fa-question-circle" aria-hidden="true" data-bs-toggle="tooltip" title="Image Proctoring"></i></label>
                    </div>

                    <?php
                    $disabled_check = "";
                    if (session()->get('live_count') == 0) :
                        $disabled_check = "disabled";
                    endif;
                    ?>
                    <div class="mb-2 online_test_opions">
                        <input class="form-label" type="checkbox" name="video_proctoring_check" id="video_proctoring_check" value="0" <?= $disabled_check; ?>>
                        <label for="video_proctoring_check">Video Proctoring<i class="far fa-question-circle" aria-hidden="true" data-bs-toggle="tooltip" title="Video Proctoring"></i></label>
                    </div>

                    <div class="mb-2 online_test_opions">
                        <input class="form-label" type="checkbox" name="random_pool_check" id="random_pool_check" value="0">
                        <label for="random_pool_check">Test Question Paper is from multiple sets<i class="far fa-question-circle" aria-hidden="true" data-bs-toggle="tooltip" title="Test Question Paper is from multiple sets"></i></label>
                    </div>

                </div>
            </div>

            <input type="hidden" name="institute_id" value="<?= $instituteID; ?>" required />
            <input type="hidden" name="redirect" value="<?= $redirect; ?>" required />

            <div class="d-grid mt-3">
                <button type="submit" class="btn btn-success" id="add_new_test_button" name="add_package_form_submit">Add</button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<!-- Include Footer -->
<?php include_once(APPPATH . "Views/footer.php"); ?>

<script>
    $().ready(function() {
        render_omr_template();
    });
</script>

<script src="<?php echo base_url('assets/js/jquery.validate.js'); ?>"></script>

<script>
    // bootstrap 5 select2 not working in modal bug
    // https://stackoverflow.com/questions/18487056/select2-doesnt-work-when-embedded-in-a-bootstrap-modal/33884094#33884094
    // Initializing select2
    $('.add_package_multiple').select2({
        width: "100%"
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
</script>

<script>
    $().ready(function() {
        // validate form
        $("#add_test_form").validate({
            rules: {
                add_test_name: "required",
                add_test_package: "required",
                add_test_no_questions: {
                    required: true,
                    digits: true
                },
                add_test_total_marks: {
                    required: true,
                    digits: true
                },
            },
            messages: {
                add_test_name: "Please enter the test name",
                add_test_package: "Please add atleast one classroom",
                add_test_no_questions: "No of questions should be greater than 0",
                add_test_total_marks: "No of questions should be greater than 0"
            },
            submitHandler: function(form) {
                $('#add_new_test_button').prop('disabled', true);
                form.submit();
            }
        });

        jQuery.validator.addMethod("lettersonly", function(value, element) {
            return this.optional(element) || /^[a-z]+$/i.test(value);
        }, "Letters only please");

    });
</script>


<script>
    function render_template_data() {
        var templateID = $('#template_id').val();
        var request = {
            template_id: templateID,
        };
        if (templateID != "") {
            var fetch_template_url = base_url + "/testTemplates/get_template_data";
            // Search template data 
            fetch_template(fetch_template_url, request)
                .then(function(result) {
                    // console.log(result);
                    format_template_data(result);
                })
                .catch(function(error) {
                    // An error occurred
                    console.log("Exception: " + error);
                });
        }
    }

    // Fetch Template
    function fetch_template(fetch_template_url, request) {
        return new Promise(function(resolve, reject) {
            $.ajax({
                    type: "POST",
                    data: JSON.stringify(request),
                    url: fetch_template_url,
                    contentType: "application/json",
                })
                .done(function(response) {
                    // console.log("Response", response);
                    resolve(response);
                })
                .fail(function(jqXHR) {
                    reject(jqXHR.responseText);
                });
        });
    }

    // Format template data and append 
    function format_template_data(data) {
        if (data != null) {
            data = JSON.parse(data);
            $.each(data, function(objIndex, obj) {
                if (obj.rule_name == "TOTAL_MARKS") {
                    $("#add_test_total_marks").val(obj.value);
                }

                if (obj.rule_name == "NO_OF_QUESTIONS") {
                    $("#add_test_no_questions").val(obj.value);
                }

                if (obj.rule_name == "TEST_DURATION") {
                    if (obj.value != "") {
                        d = Number(obj.value);
                        var hDisplay = Math.floor(d / 3600);
                        var mDisplay = Math.floor(d % 3600 / 60);
                        $("#add_test_duration_hours").val(hDisplay);
                        $("#add_test_duration_minutes").val(mDisplay);
                    }
                }

                if (obj.rule_name == "TEST_INTERFACE") {
                    $("#add_test_ui").val(obj.value);
                }

                if (obj.rule_name == "EXAM_CONDUCTION") {
                    $("#exam_conduction").val(obj.value);
                    render_omr_template();
                }

                if (obj.rule_name == "RANDOM_QUESTIONS") {
                    if (obj.value == "Y") {
                        $("#add_random_questions").prop('checked', true);
                    } else {
                        $('#add_random_questions').prop('checked', false);
                    }
                }


                if (obj.rule_name == "SHOW_RESULT") {
                    if (obj.value == "Y") {
                        $('#add_show_results').prop('checked', true);
                    } else {
                        $('#add_show_results').prop('checked', false);
                    }
                }

                if (obj.rule_name == "SHOW_QUESTION_PAPER" && obj.value == "Y") {
                    if (obj.value == "Y") {
                        $('#add_show_ques_paper_post_test').prop('checked', true);
                    } else {
                        $('#add_show_ques_paper_post_test').prop('checked', false);
                    }
                }

                if (obj.rule_name == "ALIGN_TIME_TEST") {
                    if (obj.value == "1") {
                        $('#align_test_time').prop('checked', true);
                    } else {
                        $('#align_test_time').prop('checked', false);
                    }
                }

                if (obj.rule_name == "ALIGN_TIME_STUDENT") {
                    if (obj.value == "1") {
                        $('#align_with_student_time').prop('checked', true);
                    } else {
                        $('#align_with_student_time').prop('checked', false);
                    }
                }

                if (obj.rule_name == "SHOW_RANK") {
                    if (obj.value == "1") {
                        $('#show_rank_add').prop('checked', true);
                    } else {
                        $('#show_rank_add').prop('checked', false);
                    }
                }

            });
        }
    }
</script>


<script>
    function render_omr_template() {
        var mode_of_conduction = $("#exam_conduction").val();
        if (mode_of_conduction == "Offline") {
            $("#omr_template_check").show();
            $.ajax({
                url: base_url + "/tests/get_omr_templates",
                method: "POST",
                data: {},
                success: function(result) {
                    $("#omr_template").html(format_omr_templates(result));
                }
            });
        } else {
            $("#omr_template_check").hide();
        }
        hide_online_test_options(mode_of_conduction);
    }



    // Hide Online Test Options
    function hide_online_test_options(mode_of_conduction) {
        if (mode_of_conduction == "Offline") {
            $(".online_test_opions").hide();
        } else {
            $(".online_test_opions").show();
        }
    }

    // Format OMR Templates
    function format_omr_templates(data) {
        var html = "";
        if (data != null && data.length > 0) {
            data = JSON.parse(data);
            html = html + "<option></option>";
            for (var i = 0; i < data.length; i++) {
                html = html + "<option value='" + data[i].id + "'>" + data[i].omr_template_name + "</option>";
            };
        } else {
            html = html + "<option></option>";
        }

        return html;
    }
</script>