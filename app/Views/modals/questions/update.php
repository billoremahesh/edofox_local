<div class="modal fade" id="update_question_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"> <?= $title; ?> </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <?php echo form_open('questionBank/update_question_submit'); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label"> Difficulty level </label>
                            <select class="form-select update_form_dropdowns" name="update_difficulty_level" required>
                                <option value=""></option>
                                <option value="1" <?php if ($question_detail['level'] == "1") : echo "selected";
                                                    endif; ?>> 1 - Low </option>
                                <option value="2" <?php if ($question_detail['level'] == "2") : echo "selected";
                                                    endif; ?>> 2 - Low-Moderate </option>
                                <option value="3" <?php if ($question_detail['level'] == "3") : echo "selected";
                                                    endif; ?>> 3 - Moderate </option>
                                <option value="4" <?php if ($question_detail['level'] == "4") : echo "selected";
                                                    endif; ?>> 4 - Moderate-High </option>
                                <option value="5" <?php if ($question_detail['level'] == "5") : echo "selected";
                                                    endif; ?>> 5 - High </option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label"> Question Type </label>
                            <select class="form-select update_form_dropdowns" name="update_question_type" onchange="change_answer_type(this.value);" required>
                                <option value=""></option>
                                <option value="SINGLE" <?php if ($question_detail['question_type'] == "SINGLE") : echo "selected";
                                                        endif; ?>> SINGLE </option>
                                <option value="MULTIPLE" <?php if ($question_detail['question_type'] == "MULTIPLE") : echo "selected";
                                                            endif; ?>> MULTIPLE </option>
                                <option value="NUMBER" <?php if ($question_detail['question_type'] == "NUMBER") : echo "selected";
                                                        endif; ?>> NUMBER </option>
                                <option value="MATCH" <?php if ($question_detail['question_type'] == "MATCH") : echo "selected";
                                                        endif; ?>> MATCH </option>
                                <option value="PASSAGE_MULTIPLE" <?php if ($question_detail['question_type'] == "PASSAGE_MULTIPLE") : echo "selected";
                                                                    endif; ?>> PASSAGE MULTIPLE </option>
                                <option value="DESCRIPTIVE" <?php if ($question_detail['question_type'] == "DESCRIPTIVE") : echo "selected";
                                                            endif; ?>>Subjective Answer</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label"> Correct Answer </label>
                        <?php
                        $options_array = array("option1", "option2", "option3", "option4", "BONUS", "cancel");
                        $question_type = $question_detail['question_type'];
                        $correct_answer = $question_detail['correct_answer'];
                        ?>
                        <div id="correct_ans_single_div">
                            <!-- Showing dropdown for SINGLE type question -->
                            <select class="form-control" name="correct_ans_single" id="correct_ans_single">
                                <option value="">Select Correct Answer</option>
                                <option value="option1" <?php if ($correct_answer == "option1") { ?> selected="selected" <?php } ?>>Option 1</option>
                                <option value="option2" <?php if ($correct_answer == "option2") { ?> selected="selected" <?php } ?>>Option 2</option>
                                <option value="option3" <?php if ($correct_answer == "option3") { ?> selected="selected" <?php } ?>>Option 3</option>
                                <option value="option4" <?php if ($correct_answer == "option4") { ?> selected="selected" <?php } ?>>Option 4</option>
                                <option value="BONUS" <?php if ($correct_answer == "BONUS") { ?> selected="selected" <?php } ?>>BONUS</option>
                                <option value="cancel" <?php if ($correct_answer == "cancel") { ?> selected="selected" <?php } ?>>cancel</option>
                            </select>
                        </div>
                        <?php
                        $correct_answer_array = explode(",", $correct_answer);
                        ?>
                        <div id="correct_ans_multiple_div">
                            <!-- Showing multiselect dropdown for MULTIPLE type question -->
                            <select class="correct_answer_dropdown" name="correct_ans_multiple[]" id="correct_ans_multiple" multiple>
                                <option value="">Select Correct Answer</option>
                                <!-- Fetching the comma separated values in correct answer and showing in select2 -->
                                <?php foreach ($options_array as $option) : ?>

                                    <option value="<?= $option ?>" <?php if (in_array($option, $correct_answer_array)) : echo 'selected="selected"';
                                                                    endif;  ?>><?= $option ?></option>

                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php

                        if (isset($question_detail['option1'])) {
                            $option1 = str_replace('$$', '$', $question_detail['option1']);
                        } else {
                            $option1 = "";
                        }

                        if (isset($question_detail['option2'])) {
                            $option2 = str_replace('$$', '$', $question_detail['option2']);
                        } else {
                            $option2 = "";
                        }

                        if (isset($question_detail['option3'])) {
                            $option3 = str_replace('$$', '$', $question_detail['option3']);
                        } else {
                            $option3 = "";
                        }

                        if (isset($question_detail['option4'])) {
                            $option4 = str_replace('$$', '$', $question_detail['option4']);
                        } else {
                            $option4 = "";
                        }

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
                        <div id="correct_ans_multiple_matches_div">
                            <!-- Showing multiselect dropdown for MATCH type question -->
                            <select class="correct_answer_dropdown_matches" name="correct_ans_multiple[]" id="correct_ans_multiple_matches" multiple>
                                <option value="">Select Correct Matches</option>
                                <!-- Fetching the comma separated values in correct answer and showing in select2 -->
                                <?php foreach ($match_options_array as $option) : ?>

                                    <option value="<?= $option ?>" <?php if (in_array($option, $correct_answer_array)) : echo 'selected="selected"';
                                                                    endif;  ?>><?= $option ?></option>

                                <?php endforeach; ?>
                            </select>
                        </div>



                        <div id="correct_ans_text_div">
                            <input type="text" name="correct_ans_text" id="correct_ans_text" value="<?php echo $correct_answer; ?>" />
                        </div>
                    </div>

                    <div class="col-md-12 mb-3">
                        <div id="column_values_row">
                            <div>
                                <label class="form-label">Column 1 letters</label>
                                <input type="text" class="form-control" id="matchColumn1" name="matchColumn1[]" placeholder="Enter comma separated column 1 letters like a,b,c,d" />
                            </div>

                            <div>
                                <label class="form-label">Column 2 letters</label>
                                <input type="text" class="form-control" id="matchColumn2" name="matchColumn2[]" placeholder="Enter comma separated column 2 letters like p,q,r,s" />
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <?php
                        $verifiedCheckFlag = "";
                        if (isset($question_detail['verified_date']) && $question_detail['verified_date'] != "") :
                            $verifiedCheckFlag = "checked";
                        endif;
                        ?>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1" name="verifiedCheck" id="verifiedCheck" <?= $verifiedCheckFlag; ?>>
                                <label class="form-check-label" for="verifiedCheck">
                                    Verified
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <input type="hidden" name="question_id" value="<?= $question_id; ?>" required />
                <input type="hidden" name="updater" value="<?= $updater; ?>" required />
                <input type="hidden" name="verified_date" value="<?= $question_detail['verified_date']; ?>" required />
                <input type="hidden" name="redirect" value="<?= $redirect; ?>" required />
                <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-success"> Update </button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>



<script>
    $(document).ready(function() {
        $('#correct_ans_text_div').css("display", "none");
        $('#correct_ans_multiple_matches_div').css("display", "none");
        $('#correct_ans_multiple_div').css("display", "none");
        $('#correct_ans_single_div').css("display", "none");
        $('#column_values_row').css("display", "none");
        question_type = "<?= $question_detail['question_type']; ?>";
        change_answer_type(question_type);
    });
</script>

<script>
    function change_answer_type(question_type) {
        if (question_type == 'SINGLE') {
            $('#column_values_row').css("display", "none");
            $('#correct_ans_text_div').css("display", "none");
            $('#correct_ans_multiple_matches_div').css("display", "none");
            $('#correct_ans_multiple_div').css("display", "none");
            $('#correct_ans_single_div').css("display", "block");
        }

        if (question_type == "MULTIPLE" || question_type == "PASSAGE MULTIPLE") {
            $('#column_values_row').css("display", "none");
            $('#correct_ans_text_div').css("display", "none");
            $('#correct_ans_multiple_matches_div').css("display", "none");
            $('#correct_ans_multiple_div').css("display", "block");
            $('#correct_ans_single_div').css("display", "none");
            $('.correct_answer_dropdown').select2({
                width: "100%",
                dropdownParent: $("#update_question_modal")
            });
        }

        if (question_type == 'MATCH') {
            $('#correct_ans_text_div').css("display", "none");
            $('#correct_ans_multiple_matches_div').css("display", "block");
            $('#column_values_row').css("display", "block");
            $('#correct_ans_multiple_div').css("display", "none");
            $('#correct_ans_single_div').css("display", "none");

            $('.correct_answer_dropdown_matches').select2({
                width: "100%",
                dropdownParent: $("#update_question_modal")
            });
        }

        if (question_type == 'NUMBER') {
            $('#column_values_row').css("display", "none");
            $('#correct_ans_text_div').css("display", "block");
            $('#correct_ans_multiple_matches_div').css("display", "none");
            $('#correct_ans_multiple_div').css("display", "none");
            $('#correct_ans_single_div').css("display", "none");
        }

        if (question_type == 'DESCRIPTIVE') {
            $('#column_values_row').css("display", "none");
            $('#correct_ans_text_div').css("display", "none");
            $('#correct_ans_multiple_matches_div').css("display", "none");
            $('#correct_ans_multiple_div').css("display", "none");
            $('#correct_ans_single_div').css("display", "none");
        }
    }
</script>