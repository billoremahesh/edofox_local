<!-- Include Header -->
<?php include_once(APPPATH . "Views/header.php"); ?>

<!-- Custom CSS -->
<link href="<?php echo base_url('assets/css/tests/test_result.css?v=20210915'); ?>" rel="stylesheet">

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

        <div class="text-center p-4" id="reevalute_result_loading_div" style="display: none;">
            <div class=" y-1">
                <h4>Generating result, please wait</h4>
            </div>
            <img src="<?= base_url('/assets/img/statics/progress.jpg'); ?>" class="img-fluid" alt="reevalute student result" style="max-width: 600px;" />
        </div>

        <div class="card shadow p-4" id="student_result_content">

            <div class="text-center y-1">
                <h4><?= $test_details['test_name']; ?></h4>
            </div>

            <div class="d-flex justify-content-between">
                <div>
                    <b>Student Name: <?= $student_details['name']; ?> <br />
                        Roll Number: <?= $student_details['roll_no']; ?></b>
                </div>
                <div>
                    <?php
                    if ($test_details['exam_conduction'] == 'Offline') {
                    ?>
                        <a class='btn btn-outline-primary my-1' href="<?= $student_test_status['omr_answer_sheet'] ?>" target="_blank"> Student OMR </a>
                        <br />
                        <button class="btn btn-primary" onclick="revaluateStudentResult();"> Reevalute Result </button>
                    <?php
                    }
                    ?>
                </div>
            </div>



            <table class="table table-bordered table-condensed my-2">
                <thead>
                    <tr>
                        <th> Q.No.</th>
                        <th> Option Selected </th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (!empty($student_test_options)) {
                        $i = 1;
                        foreach ($student_test_options as $student_option) {
                            if ($i == 1) {
                                echo "<tr>";
                            }
                    ?>

                            <td><?= $student_option['question_number']; ?></td>
                            <?php
                            if ($test_details['exam_conduction'] == 'Offline') {
                                $correct_answer = $student_option['option_selected'];
                            ?>
                                <td>
                                    <select class="form-control update_student_test_option">
                                        <option value="">Select Correct Answer</option>
                                        <option value="option1" <?php if ($correct_answer == "option1") { ?> selected="selected" <?php } ?>>Option 1</option>
                                        <option value="option2" <?php if ($correct_answer == "option2") { ?> selected="selected" <?php } ?>>Option 2</option>
                                        <option value="option3" <?php if ($correct_answer == "option3") { ?> selected="selected" <?php } ?>>Option 3</option>
                                        <option value="option4" <?php if ($correct_answer == "option4") { ?> selected="selected" <?php } ?>>Option 4</option>
                                        <option value="BONUS" <?php if ($correct_answer == "BONUS") { ?> selected="selected" <?php } ?>>BONUS</option>
                                        <option value="cancel" <?php if ($correct_answer == "cancel") { ?> selected="selected" <?php } ?>>cancel</option>
                                    </select>

                                    <input type="hidden" class="form-control" value="<?= $student_option['id']; ?>" />
                                </td>
                            <?php
                            } else {
                            ?>
                                <td><?= $student_option['option_selected']; ?></td>
                            <?php
                            }
                            ?>
                    <?php
                            if ($i == 4) {
                                echo "</tr>";
                                $i = 1;
                            } else {
                                $i++;
                            }
                        }
                    }
                    ?>
                </tbody>

            </table>
        </div>

    </div>
</div>

<!-- Include Footer -->
<?php include_once(APPPATH . "Views/footer.php"); ?>
<script src="<?php echo base_url('assets/js/manage_test.js'); ?>"></script>

<script>
    $("#reevalute_result_loading_div").hide();
    var test_id = "<?= $test_id; ?>";
    var test_name = "<?= $test_details['test_name']; ?>";
    var student_id = "<?= $student_details['id']; ?>";
    var student_name = "<?= $student_details['name']; ?>";
    $('.update_student_test_option').change(function() {
        var option_selected = $(this).val();
        var row_id = $(this).siblings().val();
        var dataString = 'test_id=' + test_id + '&test_name=' + test_name + '&student_id=' + student_id + '&student_name=' + student_name + '&row_id=' + row_id + '&option_selected=' + option_selected;
        $.ajax({
            type: 'POST',
            data: dataString,
            url: base_url + '/tests/update_student_test_option',
            success: function(data) {
                Snackbar.show({
                    pos: 'top-center',
                    text: 'Successfully updated student selected option'
                });
            }
        });
    });
</script>

<script>
    function revaluateStudentResult() {
        $("#student_result_content").hide();
        $("#reevalute_result_loading_div").show();
        revaluateResult(test_id, student_id)
            .then(function(result) {
                var response = JSON.parse(result);
                if (response.status.statusCode == 200) {
                    $(".spinner-container-with-cancel").hide();
                    localStorage.setItem("testid_result", test_id);
                    localStorage.setItem("testIdAnalysis", test_id);
                    window.location = base_url + "/tests/show_test_result/0/" + test_id;
                } else {
                    console.error("Error:", response.status.responseText);
                }
            })
            .catch(function(error) {
                console.error("Error:", error);
            });
    }
</script>