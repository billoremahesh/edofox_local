<!-- Include Header -->
<?php include_once(APPPATH . "Views/header.php"); ?>

<!-- Custom CSS -->
<link href="<?php echo base_url('assets/css/tests/unsubmitted_tests_students.css?v=20210915'); ?>" rel="stylesheet">

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

            <div class="text-center">
                <label class="text-muted">There are total <span class="text-danger"><?= $unsubmitted_tests_students; ?></span> students who have not properly submitted their tests. Please submit them before generating results.</label>
            </div>

            <div class="row">
                <div class="col-xs-12">
                    <table class="table table-bordered edo-table" id="unsubmitted_tests_table">
                        <thead>
                            <tr>
                                <th> # </th>
                                <th> Test Name </th>
                                <th> Test Ended On </th>
                                <th> Unsubmitted </th>
                                <th> Actions </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 1;
                            if (!empty($testwise_unsubmitted_counts)) :
                                foreach ($testwise_unsubmitted_counts as $row) :
                                    $unsubmitted_students_count = $row['unsubmitted_students_count'];
                                    $test_id = $row['test_id'];
                                    $test_name = $row['test_name'];
                                    $test_end_date = $row['end_date'];
                                    $formatted_test_end_date = changeDateTimezone(date("d M Y, h:i A", strtotime($test_end_date)),"d M Y, h:i A");
                            ?>

                                    <tr>
                                        <td><?= $i ?></td>
                                        <td><?= $test_name ?></td>
                                        <td><?= $formatted_test_end_date ?></td>
                                        <td><?= $unsubmitted_students_count ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-danger" onclick="submitAllOngoingTests(<?= $test_id ?>, this)">Submit All Students' Tests</button>
                                            <span hidden class="reevaluate_finished_text">Reevaluation Finished</span>
                                            <span hidden class="reevaluate_error_text">ERROR</span>
                                            <i hidden class='fas fa-atom fa-spin fa-2x fa-fw' class="reevaluate_loading"></i>

                                            <div hidden class="btn_go_test_analysis">
                                                <a class="" href="results.php?test_id=<?= $test_id ?>" target="_blank">Go To Test Analysis</a>
                                            </div>
                                        </td>
                                    </tr>

                            <?php
                                    $i++;
                                endforeach;
                            endif;
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>


        </div>

    </div>
</div>
<!-- Include Footer -->
<?php include_once(APPPATH . "Views/footer.php"); ?>
<script src="<?php echo base_url('assets/js/manage_test.js'); ?>"></script>



<script>
    //To asynchronously submit all ongoing tests (STARTED to COMPLETED)
    function submitAllOngoingTests(test_id, buttonElement) {

        var result = confirm("WARNING! \nAre you sure you want to submit all unsubmitted students' tests? This cannot be undone.");
        if (result) {
            // Ok button clicked

            $(buttonElement).hide();
            $(buttonElement).siblings(".reevaluate_loading").show();


            // Submitting all tests for the test using promise
            submitOngoingTests(test_id).then(function(result) {
                    var response = JSON.parse(result);
                    // console.log("response", response);
                    if (response.statusCode == 200) {
                        // Reevaluating result using a async promise
                        revaluateResult(test_id)
                            .then(function(result) {
                                var response = JSON.parse(result);
                                // console.log("I AM IN PROMISE RETURN", response); // Code depending on result
                                if (response.status.statusCode == 200) {
                                    $(buttonElement).siblings(".reevaluate_loading").hide();
                                    $(buttonElement).siblings(".reevaluate_finished_text").show();
                                    $(buttonElement).siblings(".btn_go_test_analysis").show();

                                } else {
                                    $(buttonElement).siblings(".reevaluate_loading").hide();
                                    $(buttonElement).siblings(".reevaluate_error_text").show();
                                    $(buttonElement).siblings(".reevaluate_error_text").text("ERROR: " + response.status.responseText);
                                }

                            })
                            .catch(function(error) {
                                // An error occurred
                                console.log("An error occured.");
                                $(buttonElement).siblings(".reevaluate_loading").hide();
                                $(buttonElement).siblings(".reevaluate_error_text").show();
                                $(buttonElement).siblings(".reevaluate_error_text").text("ERROR: " + error);

                            });
                    } else {
                        // alert(response.responseText);
                        $(buttonElement).siblings(".reevaluate_loading").hide();
                        $(buttonElement).siblings(".reevaluate_error_text").show();
                        $(buttonElement).siblings(".reevaluate_error_text").text(response.responseText);
                    }

                })
                .catch(function(error) {
                    // An error occurred
                    alert("Exception: " + error);
                });

        }
    }


    function fetchUnsubmittedStudentsCountCall() {
        // Fetching count of unsubmitted students using Promise 
        fetchUnsubmittedStudentsCount(<?= $decrypt_instituteID ?>)
            .then(function(result) {
                // Parsing data 
                var responseData = JSON.parse(result.trim());
                // console.log("responseData", responseData);

                // Showing data in UI
                if (responseData.unsubmitted_students_count > 0) {
                    // Show count in UI
                    $("#unsubmitted-students-count").text(responseData.unsubmitted_students_count);
                } else {
                    // Redirect to the manage test page
                    alert("There are no past tests with any unsubmitted students. You will now be redirected back to the Manage Test page.");
                    window.location = base_url + "/tests";
                }
            })
            .catch(function(error) {
                // An error occurred
                alert("ERROR: " + error);
            });
    }
</script>


<script>
    $(document).ready(function() {

        var unsubmitted_tests_table = $('#unsubmitted_tests_table').DataTable({
            'processing': true,
            stateSave: true,
            "pageLength": 10,
            language: {
                search: ""
            }
        });
        $("#unsubmitted_tests_table_filter input").attr("placeholder", "Search");

    });
</script>