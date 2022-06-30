<!-- Include Header -->
<?php include_once(APPPATH . "Views/header.php"); ?>

<!-- Custom CSS -->
<link href="<?php echo base_url('assets/css/tests/realtime_overview.css?v=20210915'); ?>" rel="stylesheet">

<div id="content">
    <div class="container-fluid mt-4">

        <div class="flex-container-column">
            <div>
                <label class="h5 text-gray-800 text-uppercase"><?= $student_details['name']; ?> - <?= $title; ?> </h5>
            </div>
            <div class="breadcrumb_div" aria-label="breadcrumb">
                <ol class="breadcrumb_custom">
                    <li class="breadcrumb_item"><a href="<?php echo base_url('/home'); ?>"> Home </a></li>
                    <li class="breadcrumb_item"><a href="<?php echo base_url('students'); ?>"> Students </a></li>
                    <li class="breadcrumb_item active" aria-current="page"> <?= $title; ?> </li>
                </ol>
            </div>
        </div>


        <div class="text-center mb-2">
            <select class="form-control" onchange="reloadPerformanceReport(this.value);" style="max-width: 350px; display: inline-block">
                <option value="TEST">Show Only TESTS Performance</option>
                <option value="DPP">Show Only Assignments Performance</option>
                <option value="ALL">Show TESTS+Assignments Performance</option>
            </select>

            <div id="loader" hidden><i class='fas fa-atom fa-spin fa-2x fa-fw'></i></div>
        </div>


        <div id="student_performance_data"></div>


    </div>
</div>



<!-- Result Display Modal -->
<div id="resultDisplayModal" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document" style="min-width: 992px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h6 class="modal-title"><?= $student_details['name'] ?> Result</h6>
            </div>
            <div class="modal-body" style="min-height: 600px;">
                <iframe style="width: 100%;min-height: 600px;border: 0;" src="" id="result_iframe"></iframe>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- Include Footer -->
<?php include_once(APPPATH . "Views/footer.php"); ?>


<script>
    $(document).ready(function() {
        document.title = "<?= $student_details['name'] ?>" + " Performance";
        fetchPerformanceReport("TEST");
    });


    // Fetching the performance report
    function fetchPerformanceReport(type) {
        // console.log("fetchPerformanceReport", type);
        var student_id = <?= json_encode($studentID) ?>;
        var institute_id = <?= json_encode($instituteID) ?>;

        // Clearing the previous data
        $("#student_performance_data").html("");
        $("#student_performance_data").empty();

        // Showing the loader
        $("#loader").show();

        $.ajax({
            type: 'post',
            url: base_url + '/students/student_performance_data',
            data: {
                student_id: student_id,
                institute_id: institute_id,
                type: type
            },
            success: function(result) {
                // console.log(result);
                // Setting the data
                $("#student_performance_data").html(result);
                // Hiding the loader
                $("#loader").hide();
            }
        });
    }



    // On change of the dropdown, fetching new report
    function reloadPerformanceReport(value) {
        fetchPerformanceReport(value);
    }
</script>