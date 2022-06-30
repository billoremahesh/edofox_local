<?php
if (!empty($question_arr)) {
    $options_array = array("option1", "option2", "option3", "option4", "BONUS", "cancel");
    foreach ($question_arr['test']['test'] as $ques_details) {
?>
        <div class='bg-white rounded shadow my-5 p-2' id="question_card_<?= $ques_details['id']; ?>">
            <div class="d-flex justify-content-between">
                <?php
                if (!empty($ques_details['verifiedDate'])) {
                    $formattedVerifiedDate = changeDateTimezone(date("d M Y, h:i A", $ques_details['verifiedDate'] / 1000),"d M Y, h:i A");
                    echo "<div><span class='material-icons text-primary fs-4 p-2' data-bs-toggle='tooltip' title='Verified by " . $ques_details['moderatorName'] . " on " . $formattedVerifiedDate . "'>verified</span></div>";
                } else {
                ?>

                    <div class="mb-3">
                        <?php if (in_array("question_bank_supervisor", session()->get('perms')) or in_array("all_perms", session()->get('perms'))) :  ?>
                            <div class="form-check" onclick="verifyQuestion(<?= $ques_details['id']; ?>);">
                                <input class="form-check-input" type="checkbox" value="1" name="verifiedCheck" id="verifiedCheck">
                                <label class="form-check-label" for="verifiedCheck">
                                    Verify
                                </label>
                            </div>
                        <?php endif; ?>
                    </div>

                <?php
                }
                ?>

                <div class='d-flex'>
                    <?php
                    if (!empty($ques_details['createdDate'])) :

                        $formattedCreatedDate = changeDateTimezone(date("d M Y, h:i A", $ques_details['createdDate'] / 1000),"d M Y, h:i A");
                        $adminName = "";
                        if (isset($ques_details['adminName'])) {
                            $adminName = $ques_details['adminName'];
                        }
                    ?>

                        <div> <span class='material-icons text-muted p-2' data-bs-toggle='tooltip' title='Question Added by <?= $adminName ?> on <?= $formattedCreatedDate ?>'>person_add</span> </div>


                    <?php
                    endif;

                    if (!empty($ques_details['updatedDate'])) :
                        $formattedUpdatedDate = changeDateTimezone(date("d M Y, h:i A", $ques_details['updatedDate'] / 1000),"d M Y, h:i A");
                        $updatorName = "";
                        if (isset($ques_details['updatorName'])) {
                            $updatorName = $ques_details['updatorName'];
                        }
                    ?>

                        <div> <span class='material-icons text-muted p-2' data-bs-toggle='tooltip' title='Question Updated by <?= $updatorName ?> on <?= $formattedUpdatedDate ?>'>drive_file_rename_outline</span> </div>

                    <?php endif; ?>
                </div>
            </div>

            <hr class="m-0" />



            <div class='question_check_div'>
                <?php
                if (!empty($ques_details['question'])) {
                    echo "<div class='ques_div'>" . $ques_details['question'] . "</div>";
                }
                ?>
                <div class='d-flex justify-content-between'>

                    <?php
                    $question_type = "Type";
                    if (isset($ques_details['type'])) {
                        $question_type = $ques_details['type'];
                    }
                    $questionLevel = "Difficulty";
                    if (isset($ques_details['level'])) {
                        $questionLevel = $ques_details['level'];
                    }
                    ?>
                    <div>
                        <span data-bs-toggle="tooltip" title="Question Type" class="editable-blocks" onclick="showQuestionTypeDropdown(this)"><?php echo $question_type; ?></span>
                        <select class="d-none" onchange="updateQuestionType('<?php echo $ques_details['id']; ?>', this)">
                            <option value="SINGLE" <?php if ($question_type == "SINGLE") { ?> selected="selected" <?php } ?>>SINGLE</option>
                            <option value="MULTIPLE" <?php if ($question_type == "MULTIPLE") { ?> selected="selected" <?php } ?>>MULTIPLE</option>
                            <option value="NUMBER" <?php if ($question_type == "NUMBER") { ?> selected="selected" <?php } ?>>NUMBER</option>
                            <option value="MATCH" <?php if ($question_type == "MATCH") { ?> selected="selected" <?php } ?>>MATCH</option>
                            <option value="PASSAGE_MULTIPLE" <?php if ($question_type == "PASSAGE_MULTIPLE") { ?> selected="selected" <?php } ?>>PASSAGE MULTIPLE</option>
                            <option value="DESCRIPTIVE" <?php if ($question_type == "DESCRIPTIVE") { ?> selected="selected" <?php } ?>>Subjective Answer</option>
                        </select>
                    </div>


                    <!-- Difficulty dropdown -->
                    <div>
                        <span class='ques_desc d-inline-block me-2 editable-blocks' data-bs-toggle="tooltip" title="Difficulty Level 1-5" onclick="showDifficultyDropdown(this)"><?= $questionLevel; ?></span>
                        <select class="d-none" onchange="updateQuestionDifficulty('<?php echo $ques_details['id']; ?>', this)">
                            <option value="">Difficulty</option>
                            <option value="1" <?php if ($questionLevel == "1") { ?> selected="selected" <?php } ?>>1 - Low</option>
                            <option value="2" <?php if ($questionLevel == "2") { ?> selected="selected" <?php } ?>>2 - Low-Moderate</option>
                            <option value="3" <?php if ($questionLevel == "3") { ?> selected="selected" <?php } ?>>3 - Moderate</option>
                            <option value="4" <?php if ($questionLevel == "4") { ?> selected="selected" <?php } ?>>4 - Moderate-High</option>
                            <option value="5" <?php if ($questionLevel == "5") { ?> selected="selected" <?php } ?>>5 - High</option>

                        </select>
                    </div>

                    <div>
                        <span>
                            <button class='btn btn-sm' onclick="show_edit_modal('modal_div','delete_question_modal','questionBank/delete_question_modal/<?= $ques_details['id']; ?>');" data-bs-toggle='tooltip' title='Delete Question'>
                                <i class='material-icons material-icon-small text-danger'>delete</i>
                            </button>
                        </span>
                    </div>
                </div>


                <?php if (isset($ques_details['question']) && $ques_details['question'] != "") : ?>
                    <div class='ques_div'><?= $ques_details['question']; ?></div>
                <?php endif; ?>

                <?php
                $correct_answer = "";
                if (!empty($ques_details['correctAnswer'])) {
                    $correct_answer = $ques_details['correctAnswer'];
                }
                ?>


                <div class='col-sm-4'>
                    <div class="correct-answer-block" data-bs-toggle='tooltip' title='Correct Answer'>Correct Answer:
                        <span>
                            <span style="padding: 4px; cursor: pointer;" onclick="showCorrectAnswerTextInput(this)"><?php echo $correct_answer; ?> (Click to edit)</span>

                            <?php if ($question_type == "SINGLE") : ?>
                                <!-- Showing dropdown for SINGLE type question -->
                                <select class="d-none" onchange="updateCorrectAnswerTestText('<?php echo $ques_details['id']; ?>', this)">
                                    <option value="">Select Correct Answer</option>
                                    <option value="option1" <?php if ($correct_answer == "option1") { ?> selected="selected" <?php } ?>>Option 1</option>
                                    <option value="option2" <?php if ($correct_answer == "option2") { ?> selected="selected" <?php } ?>>Option 2</option>
                                    <option value="option3" <?php if ($correct_answer == "option3") { ?> selected="selected" <?php } ?>>Option 3</option>
                                    <option value="option4" <?php if ($correct_answer == "option4") { ?> selected="selected" <?php } ?>>Option 4</option>
                                    <option value="BONUS" <?php if ($correct_answer == "BONUS") { ?> selected="selected" <?php } ?>>BONUS</option>
                                    <option value="cancel" <?php if ($correct_answer == "cancel") { ?> selected="selected" <?php } ?>>cancel</option>
                                </select>

                            <?php elseif ($question_type == "MULTIPLE" || $question_type == "PASSAGE MULTIPLE") :
                                $correct_answer_array = explode(",", $correct_answer);
                            ?>
                                <!-- Showing multiselect dropdown for MULTIPLE type question -->
                                <select class="correct_answer_dropdown d-none" multiple onchange="updateCorrectAnswerTestText('<?php echo $ques_details['id']; ?>', this)">
                                    <option value="">Select Correct Answer</option>
                                    <!-- Fetching the comma separated values in correct answer and showing in select2 -->
                                    <?php foreach ($options_array as $option) : ?>

                                        <option value="<?= $option ?>" <?php if (in_array($option, $correct_answer_array)) : echo 'selected="selected"';
                                                                        endif;  ?>><?= $option ?></option>

                                    <?php endforeach; ?>
                                </select>

                            <?php elseif ($question_type == "MATCH") :
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

                                <!-- Showing multiselect dropdown for MATCH type question -->
                                <select class="correct_answer_dropdown d-none" multiple onchange="updateCorrectAnswerTestText('<?php echo $ques_details['id']; ?>', this)">
                                    <option value="">Select Correct Matches</option>
                                    <!-- Fetching the comma separated values in correct answer and showing in select2 -->
                                    <?php foreach ($match_options_array as $option) : ?>

                                        <option value="<?= $option ?>" <?php if (in_array($option, $correct_answer_array)) : echo 'selected="selected"';
                                                                        endif;  ?>><?= $option ?></option>

                                    <?php endforeach; ?>
                                </select>

                            <?php elseif ($question_type == "NUMBER") : ?>
                                <input type="text" class="d-none" onfocusout="updateCorrectAnswerTestText('<?php echo $ques_details['id']; ?>', this)" value="<?php echo $correct_answer; ?>" />
                            <?php endif; ?>

                        </span>
                    </div>
                </div>



                <?php if (isset($ques_details['questionImageUrl']) && $ques_details['questionImageUrl'] != "") : ?>
                    <h5 class='text-muted fw-bold text-center'>
                        <span class='badge bg-primary'>Question:</span>

                        <span>
                            <button class='btn btn-sm' onclick="replace_question_image(<?= $ques_details['id']; ?>);" data-bs-toggle='tooltip' title='Replace Question Image'>
                                <i class='material-icons material-icon-small text-secondary'>edit</i>
                            </button>
                        </span>
                    </h5>
                    <div class='ques_img_div text-center'>
                        <img src="<?= $ques_details['questionImageUrl']; ?>" alt='Question Image' class='img-fluid ques_imgs border border-primary border-2 rounded w-100 question-image-tag' style='max-width: 600px;' />
                    </div>
                <?php endif; ?>


                <hr />



                <?php if (isset($ques_details['solutionImageUrl']) && $ques_details['solutionImageUrl'] != "") : ?>
                    <h5 class='text-muted fw-bold text-center'>
                        <span class='badge bg-secondary'>Solution:</span>

                        <button class='btn btn-sm' onclick="replace_solution_image(<?= $ques_details['id']; ?>);" data-bs-toggle='tooltip' title='Replace Solution Image'>
                            <i class='material-icons material-icon-small text-secondary'>edit</i>
                        </button>
                    </h5>


                    <div class='soln_img_div text-center'>
                        <img src="<?= $ques_details['solutionImageUrl']; ?>" alt='Solution Image' class='img-fluid soln_imgs border border-secondary border-2 rounded w-100 solution-image-tag' style='max-width: 600px;' />
                    </div>

                <?php else : ?>

                    <div class='text-center'>
                        <button class='btn btn-outline-secondary btn-sm' onclick="replace_solution_image(<?= $ques_details['id']; ?>);" data-bs-toggle='tooltip' title='Add Solution Image'>
                            Add Solution Image
                        </button>
                    </div>
                    <div class='soln_img_div text-center'>
                        <img src="" class='img-fluid soln_imgs border-2 rounded w-100 solution-image-tag' style='max-width: 600px;' />
                    </div>

                <?php endif; ?>


            </div>
        </div>
<?php
    }
} else {
    echo "<hr/><div class='text-danger text-center fw-bold m-0'>No questions match selected filters.</div>";
}
?>