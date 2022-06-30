<!-- Include Header -->
<?php include_once(APPPATH . "Views/header.php"); ?>

<!-- Custom CSS -->
<link href="<?php echo base_url('assets/css/tests/revalute_result.css?v=20220415'); ?>" rel="stylesheet">

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

        <div class="card shadow p-4" style="max-width: 900px;margin:auto;">


            <div class="text-center">
                <div class="progress">
                    <svg class="progress-circle" width="200px" height="200px">
                        <circle class="progress-circle-back" cx="80" cy="80" r="74"></circle>
                        <circle class="progress-circle-prog" cx="80" cy="80" r="74"></circle>
                    </svg>
                    <div class="progress-text" data-progress="0">0%</div>
                </div>
            </div>

            <div class="my-2 text-center font-weight-bold" id="reevalute_student_count"></div>

            <div class="text-center text-muted" id="revalute_progrees_msg_div">
                Please wait, regenerating the result..
            </div>

            <div class="text-center my-2" id="show_result_div" style="display: none;">
                <a class="btn btn-primary" href="<?= base_url($redirect); ?>"> Show Result </a>
            </div>

        </div>

    </div>
</div>
<!-- Include Footer -->
<?php include_once(APPPATH . "Views/footer.php"); ?>


<script src="<?php echo base_url('assets/js/manage_test.js'); ?>"></script>

<script>
    var test_id = "<?= $test_id ?>";
    var evaluation_date = "<?= $test_status_data['evaluation_date'] ?>";
    var total_students = "<?= $test_status_data['student_cnt'] ?>";
    localStorage.setItem("testid_result", test_id);
    localStorage.setItem("testIdAnalysis", test_id);
    revaluateTestResult(test_id);
    var percent_progress = 0;
    // Call async every  2 sec to check no of students revaluated 
    var intervalId = window.setInterval(function() {
        getStudentsRevaluated(percent_progress);
    }, 5000);


    function revaluateTestResult(str) {
        var test_id = str;
        revaluateResult(test_id)
            .then(function(result) {
                var response = JSON.parse(result);
                console.log("I AM IN PROMISE RETURN", response);
                if (response.status.statusCode == 200) {
                    $("#show_result_div").show();
                    $("#revalute_progrees_msg_div").hide();
                    getStudentsRevaluated(100);
                    clearInterval(intervalId);
                } else {
                    console.error("Error:", response.status.responseText);
                }
            })
            .catch(function(error) {
                console.error("Error:", error);
            });
    }

    function getStudentsRevaluated(percent_progress) {
        console.log("get revaluted students count");
        $.ajax({
            url: base_url + "/tests/get_tests_revaluated_students",
            type: "POST",
            data: {
                evaluation_date: evaluation_date,
                test_id: test_id
            },
            success: function(result) {
                var current_student_count = result;
                var student_reevalute_percent = Math.round(current_student_count * 100 / total_students);
                $("#reevalute_student_count").html("Students reevaluted: " + current_student_count + " of " + total_students);
                if (student_reevalute_percent == 100) {
                    student_reevalute_percent = 90;
                }
                if (percent_progress != 0) {
                    student_reevalute_percent = percent_progress;
                }
                fill_circle(student_reevalute_percent);
            }
        });
    }

    function fill_circle(rand) {
        var x = document.querySelector('.progress-circle-prog');
        x.style.strokeDasharray = (rand * 4.65) + ' 999';
        var el = document.querySelector('.progress-text');
        var from = $('.progress-text').data('progress');
        $('.progress-text').data('progress', rand);
        var start = new Date().getTime();

        setTimeout(function() {
            var now = (new Date().getTime()) - start;
            var progress = now / 700;
            result = rand > from ? Math.floor((rand - from) * progress + from) : Math.floor(from - (from - rand) * progress);
            el.innerHTML = progress < 1 ? result + '%' : rand + '%';
            if (progress < 1) setTimeout(arguments.callee, 10);
        }, 10);
    }
</script>