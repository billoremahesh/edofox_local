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


        <div class="card shadow p-4">

            <div class="d-flex my-2">

                <!-- <a class="btn btn-primary" href="tests/proctoring_sessions" style='width:300px;'>Review proctoring sessions</a> -->

                <button class="btn btn-primary" onclick="calculateProctoringScore();" > Calculate Proctoring Score </button>
            </div>

            <div id="error_msg"></div>

            <!-- Table -->
            <table id='proctoring_analysis_table' class='table table-bordered'>

                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Roll no</th>
                        <th>Solved</th>
                        <th>Correct</th>
                        <th>Score</th>
                        <th>Proctor Score</th>
                        <th>Remarks</th>
                        <th>Action</th>
                    </tr>
                </thead>

            </table>

        </div>


    </div>
</div>

<!-- Include Footer -->
<?php include_once(APPPATH . "Views/footer.php"); ?>

<script src="<?php echo base_url('assets/js/manage_test.js'); ?>"></script>
<script>
    var test_id = <?= $test_id; ?>;
    var instituteID = <?= $instituteID; ?>;
 
    $(document).ready(function() {

        function initializeDatatable(testID) {
            // console.log("Initializing table with id:", testID);

            var table = $('#proctoring_analysis_table').DataTable({
                'pageLength': 50,
                'processing': true,
                'serverSide': true,
                stateSave: true,
                "lengthMenu": [
                    [10, 25, 50, -1],
                    [10, 25, 50, "All"]
                ],
                dom: 'Bflrtip',
                buttons: [{
                        extend: 'colvis',
                        //https://datatables.net/forums/discussion/50751/hide-several-columns-for-colvis-button-list
                        columns: ':gt(0)',
                        text: "Toggle Columns"
                    },
                    {
                        extend: 'excel',
                        exportOptions: {
                            columns: ':visible',
                        }
                    }
                ],
                'serverMethod': 'post',
                'ajax': {
                    'url': base_url + '/tests/ajax_fetch_proctoring_analysis',
                    "data": {
                        "testID": test_id
                    }
                },
                'columns': [{
                        data: 'name'
                    },
                    {
                        data: 'roll_no'
                    },
                    {
                        data: 'solved'
                    },
                    {
                        data: 'correct'
                    },
                    {
                        data: 'score'
                    },
                    {
                        data: 'proctor_score'
                    },
                    {
                        data: 'remarks'
                    },
                    {
                        data: 'more_button'
                    }
                ],
            });

        }

        initializeDatatable(test_id);
    });
</script>

<script>
    function calculateProctoringScore() {
        var request = {
            institute: {
                id: instituteID
            },
            test: {
                id: test_id
            }
        };
        callAdminServiceJSONPost("calculateProctoringScore", request).then(function(response) {
                if (response.status.statusCode > 0) {
                    Snackbar.show({
                        pos: 'top-center',
                        text: 'Proctoring score calculation started successfully, please check the scores after some time'
                    });
                } else {
                    $("error_msg").html("Some error occured in calculating proctoring score");
                }
            })
            .catch(function(error) {
                $("error_msg").html("Error in service call");
            });
    }
</script>