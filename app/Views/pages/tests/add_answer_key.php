<!-- Include Header -->
<?php include_once(APPPATH . "Views/header.php"); ?>

<!-- Custom CSS -->
<link href="<?php echo base_url('assets/css/tests/realtime_overview.css?v=20210915'); ?>" rel="stylesheet">

<div id="content">
    <div class="container-fluid mt-4">

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

        <div class="card p-4">
            <div class="row">
                <div class="col-6">
                    <div>
                        <button class="btn btn-secondary" type="button" data-bs-toggle="modal" data-bs-target="#importKeyModal"> Import Key from Excel</button>
                    </div>
                </div>
                <div class="col-6">
                    <a href="<?= base_url('/tests/print_answer_key/' . $test_id) ?>" class="btn btn-primary">Print Answer Key</a>
                </div>
            </div>
            <br />
            <form action="<?= base_url('/Tests/add_bulk_answer_key_submit'); ?>" method="post" enctype="multipart/form-data">
                <?php
                if (!empty($questions_list)) :
                    $question_count = 1;
                    $number_of_q_in_a_row = 4;
                    $options_array = array("option1", "option2", "option3", "option4", "option5", "BONUS", "cancel");
                    $answer_key_data_export = array();
                    foreach ($questions_list as $row) :
                        $question_id = $row['id'];
                        $option1 = $row['option1'];
                        $option2 = $row['option2'];
                        $correct_answer = $row['correct_answer'];
                        $question_type = $row['question_type'];
                        $question_number_in_paper = $row['question_number'];
                        if ($question_type == NULL) {
                            $question_type = "SINGLE";
                        }
                        if ($question_count == 1 || (($question_count - 1) % $number_of_q_in_a_row == 0)) {
                            echo "<div class='row' style='padding: 8px 0;border-bottom: 3px solid #fafafa;'>";
                        }
                        //Generating an object with single question's data
                        $single_row_data = array(
                            "question_number" => $question_number_in_paper,
                            "correct_answer" => $correct_answer,
                            "question_type" => $question_type
                        );

                        // Pushing the single question array into the larger array which will be used to generate excel as template
                        array_push($answer_key_data_export, $single_row_data);
                ?>
                        <div class="col-md-3">
                            <input type="hidden" id="question_id" name="question_id[]" value="<?php echo $question_id; ?>" required />

                            <div class="col-xs-6"><label><?php echo $question_number_in_paper . "-" . $question_type; ?>:</label></div>
                            <div class="col-xs-6">
                                <?php if ($question_type === 'SINGLE') : ?>
                                    <select class="form-control correct_answer_dropdown" name='<?= "Que_correct_ans_" . $question_id ?>'>
                                        <option value=""></option>
                                        <!-- Fetching the comma separated values in correct answer and showing in select2 -->
                                        <?php foreach ($options_array as $option) : ?>
                                            <option value="<?= $option ?>" <?php if ($option == $correct_answer) : echo 'selected="selected"';
                                                                            endif;  ?>><?= $option ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <!-- Showing the multiselect dropdown for multiple type -->
                                <?php elseif ($question_type === 'MULTIPLE' || $question_type === 'PASSAGE_MULTIPLE') :
                                    $correct_answer_array = explode(",", $correct_answer);
                                ?>
                                    <select class="form-control correct_answer_dropdown_multiple" name='<?= "Que_correct_ans_" . $question_id . "[]" ?>' multiple>
                                        <option value=""></option>
                                        <!-- Fetching the comma separated values in correct answer and showing in select2 -->
                                        <?php foreach ($options_array as $option) : ?>
                                            <option value="<?= $option ?>" <?php if (in_array($option, $correct_answer_array)) : echo 'selected="selected"';
                                                                            endif;  ?>><?= $option ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                <?php elseif ($question_type === "MATCH") :
                                    $correct_answer_array = explode(",", $correct_answer);
                                    $option1_array = explode(",", $option1);
                                    $option2_array = explode(",", $option2);

                                    $match_options_array = array();

                                    // Looping thorugh option 1 array eg. a,b,c,d and p,q,r,s to create options array to display in dropdown
                                    foreach ($option1_array as $option1_value) {
                                        foreach ($option2_array as $option2_value) {
                                            array_push($match_options_array, "$option1_value-$option2_value");
                                        }
                                    }

                                    array_push($match_options_array, "BONUS");
                                    array_push($match_options_array, "cancel");
                                ?>
                                    <select class="form-control correct_answer_dropdown_multiple" name='<?= "Que_correct_ans_" . $question_id . "[]" ?>' multiple>
                                        <option value=""></option>
                                        <!-- Fetching the comma separated values in correct answer and showing in select2 -->
                                        <?php foreach ($match_options_array as $option) : ?>
                                            <option value="<?= $option ?>" <?php if (in_array($option, $correct_answer_array)) : echo 'selected="selected"';
                                                                            endif;  ?>><?= $option ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                <?php else : ?>
                                    <input class="form-control" name='<?= "Que_correct_ans_" . $question_id ?>' value="<?php echo $correct_answer ?>" />
                                <?php endif; ?>
                            </div>
                        </div>
                <?php
                        if ($question_count % $number_of_q_in_a_row == 0) {
                            echo "</div>";
                        }
                        $question_count++;
                    endforeach;
                endif;
                ?>

                <div class="row">
                    <div class="col-4 offset-4 text-center">
                        <br />
                        <input type="hidden" id="test_id" name="test_id" value="<?php echo $test_id; ?>" />
                        <input type="submit" id="add_bulk_answer_key_submit_button" class="btn btn-success" value="Add Answer Key" name="add_bulk_answer_key_submit">
                    </div>
                </div>
            </form>


            <div class="modal fade" id="importKeyModal" tabindex="-1" role="dialog" aria-labelledby="importKeyModalLabel" aria-hidden="true">

                <div class="modal-dialog">
                    <!-- Modal content-->
                    <div class="modal-content">
                        <div class="modal-header">
                            <h6 class="modal-title"> Import Answer Key from Excel </h6>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-2">
                                        <label for="excel_file">Step 1: Download Excel Template from here: </label>
                                        <!-- Giving button to export answer key for current exam in excel -->
                                        <form action="<?= base_url('tests/ajax_download_answer_key_template'); ?>" method="post">
                                            <?php
                                            // Looping through the $answer_key_data_export array to generate inputs
                                            // This is to send array in form post to process
                                            if (!empty($answer_key_data_export)) :
                                                foreach ($answer_key_data_export as $single_question) {
                                                    echo '<input type="hidden" name="question_numbers[]" value="' . $single_question['question_number'] . '">';
                                                    echo '<input type="hidden" name="correct_answers[]" value="' . $single_question['correct_answer'] . '">';
                                                    echo '<input type="hidden" name="question_type[]" value="' . $single_question['question_type'] . '">';
                                                }
                                            endif;
                                            ?>

                                            <div class="my-2">
                                                <label for="correct_anwser_format"> FORMAT OF CORRECT ANSWER </label>
                                                <select name="correct_anwser_format" class="form-select" id="correct_anwser_format" onchange="check_option_format()" required>
                                                    <option value="1 2 3 4">1 2 3 4</option>
                                                    <option value="A B C D"> A B C D</option>
                                                </select>
                                            </div>

                                            <button class="btn btn-success" type="submit" name="export_submit">Download Answer Key Template <i class='fa fa-download fa-fw' aria-hidden='true'></i></button>
                                        </form>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-2">
                                        <label for="excel_file">Step 2: Update the 2nd "CORRECT ANSWER" column in the downloaded excel only as per the given format: </label>
                                    </div>
                                </div>
                                <form action="<?= base_url('tests/import_excel_answer_key'); ?>" method="post" enctype="multipart/form-data">
                                    <input type="hidden" class="hidden" name="import_institute_id" id="import_institute_id" value="<?php echo $instituteID; ?>" required>
                                    <input type="hidden" class="hidden" name="import_test_id" id="import_test_id" value="<?php echo $test_id; ?>" required>
                                    <input type="hidden" class="hidden" name="correct_anwser_format_excel" id="correct_anwser_format_excel" required>
                                    <div class="col-md-12">
                                        <div class="mb-2">
                                            <label class="form-label" for="excel_file">Step 3: Upload the updated Excel File here (.xlsx): *</label>
                                            <input type="file" id="excel_file" name="excel_file" required />
                                        </div>
                                    </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" name="import_excel_answer_key_submit">Import</button>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include Footer -->
<?php include_once(APPPATH . "Views/footer.php"); ?>
<script>
    $(document).ready(function() {
        $(".correct_answer_dropdown_multiple").select2({
            closeOnSelect: false
        });
    });
</script>

<script>
    function check_option_format() {
        var format_val = $('#correct_anwser_format').val();
        $("#correct_anwser_format_excel").val(format_val);
    }
</script>