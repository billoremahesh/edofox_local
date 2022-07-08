<!-- Include Header -->
<?php include_once(APPPATH . "Views/header.php"); ?>

<!-- Custom CSS -->
<link href="<?php echo base_url('assets/css/tests/performance_report.css?v=20210915'); ?>" rel="stylesheet">

<div id="content">
    <div class="container-fluid mt-4">

        <div class="flex-container-column">
            <div>
                <label class="h5 text-gray-800 text-uppercase"> <?= $title; ?> </h5>
            </div>
            <div class="breadcrumb_div" aria-label="breadcrumb">
                <ol class="breadcrumb_custom">
                    <li class="breadcrumb_item"><a href="<?php echo base_url('/home'); ?>"> Home </a></li>
                    <li class="breadcrumb_item"><a href="<?php echo base_url('students'); ?>"> Students </a></li>
                    <li class="breadcrumb_item active" aria-current="page"> <?= $title; ?> </li>
                </ol>
            </div>
        </div>

        <div>
            <h4 class="text-center"><?= $student_details['name']; ?> : Attendance Report</h4>


            <div class="d-flex text-center p-4">
 
 <div class="d-none" id="loader"><img style="width: 64px;" src="<?= base_url('assets/img/loading.gif'); ?>" /></div>
</div>



            <div id="student_performance_data"></div>
        </div>

    </div>
</div>

<!-- Result Display Modal -->

<div id="resultDisplayModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title"><?= $student_details['name']; ?> Result</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="min-height: 600px;">
                <iframe style="width: 100%;min-height: 600px;border: 0;" src="" id="result_iframe"></iframe>
            </div>
        </div>
    </div>
</div>


<!-- Include Footer -->
<?php include_once(APPPATH . "Views/footer.php"); ?>
<script>
    var studentId = "<?= $student_details['student_id']; ?>";
    // console.log("Student ID is " + studentId);
</script>

<script>
    $(document).ready(function() {
        document.title = "<?= $student_details['name']; ?>" + " Performance";
        fetchPerformanceReport("TEST");
        // The date picker
        var input_to_picker_outlet = $("#input_to_picker_outlet");
        $('#input_from').pickadate({
            format: 'yyyy-mm-dd',
            closeOnSelect: true,
        });
        $('#input_to').pickadate({
            format: 'yyyy-mm-dd',
            closeOnSelect: true,
            // container: input_to_picker_outlet,
            // containerHidden: 'input_to_picker_outlet',

        });
    });


    // Fetching the performance report
    function fetchPerformanceReport(type) {
        var type = $("#test_type").val();
        var input_from = $("#input_from").val();
        var input_to = $("#input_to").val();
        // console.log("fetchPerformanceReport", type);
        var student_id = <?= json_encode($student_details['student_id']) ?>;
        var institute_id = <?= json_encode($decrypted_instituteID) ?>;

        // Clearing the previous data
        $("#student_performance_data").html("");
        $("#student_performance_data").empty();

        // Showing the loader
        $("#loader").toggleClass('d-none');

        // Calling the API
            var ajaxRequest = {
                student_id: student_id,
                institute_id: institute_id,
                performance_report_type: type,
                startTime: input_from,
                endTime: input_to,
            };

        $.ajax({
            url: base_url + "/students/student_attendance_performance_data",
            method: 'POST',
            data: ajaxRequest,
            success: function(result) {
                // console.log("Result ==>", result);
                $("#student_performance_data").html(result);
                // Hiding the loader
                $("#loader").toggleClass('d-none');
            }
        });

    }
    // On change of the dropdown, fetching new report
    function reloadPerformanceReport(value) {
        fetchPerformanceReport(value);
    }
</script>