<style>
    .ind_ques {
        padding: 16px;
        margin: 32px auto;
        background-color: #ffffff;
        border: 2px solid #c8e6c9;
        border-radius: 6px;
    }
</style>
<div class="">
    <form id="upload_questions_form" action="tests/add_test_bulk_images_submit" method="post" enctype="multipart/form-data">

        <?php


        $questionSingleDisplay = "";
        $questionOtherDisplay = "disabled='true' style='display:none;'";
        if ($quetionType != 'SINGLE') {
            $questionOtherDisplay = "";
            $questionSingleDisplay = "disabled='true' style='display:none;'";
        }

        //Setting options variables
        $options = array();
        if ($optionsType == "numeric") {
            array_push($options, "1)", "2)", "3)", "4)");
        } elseif ($optionsType == "letters") {
            array_push($options, "A)", "B)", "C)", "D)");
        }
        ?>

        <input type="hidden" id="institute_id" name="institute_id" value="<?php echo $instituteID; ?>" required>
        <input type="hidden" id="test_id" name="test_id" value="<?php echo $test_id; ?>" required />
        <input type="hidden" id="Que_offset" name="offset" value="<?php echo $offset; ?>" required />


        <div class="row" style="margin:8px auto; text-align:center">
            <h3>Please Upload <?= $noQues; ?> Question Images</h3>
            <input type="file" name="test_questions_files[]" id="test_questions_files" style="margin:8px auto; text-align:center" multiple required onchange="checkNumberOfImages();">
        </div>



        <?php

        for ($i = $offset; $i < ($offset + $noQues); $i++) {

            if (isset($template_id) && !empty($template_id)) {
                $section_template_rule = get_template_rule($template_id, 'SECTION_QUESTIONS', $i);
                if (!empty($section_template_rule)) {
                    $section = $section_template_rule['value'];
                }


                $weightage_template_rule = get_template_rule($template_id, 'SECTION_WEIGHTAGE', $i);
                if (!empty($weightage_template_rule)) {
                    $weightage = $weightage_template_rule['value'];
                }


                $negative_mark_template_rule = get_template_rule($template_id, 'SECTION_NEGATIVE_MARKS', $i);
                if (!empty($negative_mark_template_rule)) {
                    $negative_mark = $negative_mark_template_rule['value'];
                }

                $quetionType_template_rule = get_template_rule($template_id, 'SECTION_QUESTION_TYPE', $i);
                if (!empty($quetionType_template_rule)) {
                    $quetionType = $quetionType_template_rule['value'];
                }

                $subjectid_template_rule = get_template_rule($template_id, 'SECTION_SUBJECT', $i);
                if (!empty($subjectid_template_rule)) {
                    $subjectid = $subjectid_template_rule['value'];
                }

            }
        ?>

            <input type="hidden" id="Que_subject_id" name="Que_subject_id[]" value="<?php echo $subjectid; ?>" required />

            <div class="ind_ques">
                <div class="row" style="margin:8px auto;">

                    <div class="col-md-2">
                        <label>Question No:</label>
                    </div>
                    <div class="col-md-2">
                        <label><?= $i; ?></label>
                    </div>

                    <div class="col-md-2">
                        <label>Section</label>
                    </div>
                    <div class="col-md-2">
                        <input type="text" class="form-control" id="section" name="section[]" value="<?php echo $section; ?>" required />
                    </div>

                    <div class="col-md-2">
                        <label>Question type</label>
                    </div>
                    <div class="col-md-2">

                        <select class="form-control" id="Question_type" name="Question_type[]" onchange="changeCorrectAnswerInput(this)">
                            <option <?php if ($quetionType === "SINGLE") echo "selected"; ?> value="SINGLE">SINGLE</option>
                            <option <?php if ($quetionType === "MULTIPLE") echo "selected"; ?> value="MULTIPLE">MULTIPLE</option>
                            <option <?php if ($quetionType === "NUMBER") echo "selected"; ?> value="NUMBER">NUMBER</option>
                            <option <?php if ($quetionType === "MATCH") echo "selected"; ?> value="MATCH">MATCH</option>
                            <option <?php if ($quetionType === "PASSAGE_MULTIPLE") echo "selected"; ?> value="PASSAGE_MULTIPLE">PASSAGE MULTIPLE</option>
                            <option <?php if ($quetionType === "DESCRIPTIVE") echo "selected"; ?> value="DESCRIPTIVE">Subjective Answer</option>

                        </select>
                    </div>
                </div>

                <div class="row" style="margin:8px auto;">

                    <div class="col-md-2">
                        <label>Weightage</label>
                    </div>
                    <div class="col-md-2">
                        <input type="number" class="form-control" id="weightage" name="weightage[]" value="<?php echo $weightage; ?>" required />
                    </div>

                    <div class="col-md-2">
                        <label>Negative Mark</label>
                    </div>
                    <div class="col-md-2">
                        <input type="number" class="form-control" id="negative_mark" name="negative_mark[]" value="<?php echo $negative_mark; ?>" step=".01" required />
                    </div>

                    <div class="col-md-2">
                        <label>Partial Marking? </label>
                    </div>

                    <div class="col-md-2">
                        <select class="form-control" id="Que_partial_marking" name="Que_partial_marking[]">
                            <option></option>
                            <option value="N" <?php echo ($questionPartialMarking === "N") ? "selected" : "" ?>>NO</option>
                            <option value="Y" <?php echo ($questionPartialMarking === "Y") ? "selected" : "" ?>>YES</option>
                        </select>
                    </div>


                </div>

                <div class="row" style="margin:8px auto;">

                    <div class="col-md-2">
                        <label>Correct Answer: </label>
                    </div>

                    <div class="col-md-2">

                        <input type="text" class="form-control answer_input_text" id="Que_correct_ans" name="Que_correct_ans[]" placeholder="option1,option2,.../number/match" <?= $questionOtherDisplay ?> />

                        <select class="form-control answer_input_selector" id="Que_correct_ans" name="Que_correct_ans[]" <?= $questionSingleDisplay ?>>
                            <option value=""></option>
                            <option value="option1">option1</option>
                            <option value="option2">option2</option>
                            <option value="option3">option3</option>
                            <option value="option4">option4</option>
                        </select>
                    </div>

                </div>

                <div class="row" style="margin:8px auto; display:none;">

                    <div class="hidden">
                        <label>Option1</label>
                    </div>

                    <div class="hidden">
                        <input type="text" class="form-control" id="option1" name="option1[]" value="<?= $options[0] ?>" />
                    </div>

                    <div class="hidden">
                        <label>Option2</label>
                    </div>

                    <div class="hidden">
                        <input type="text" class="form-control" id="option2" name="option2[]" value="<?= $options[1] ?>" />
                    </div>
                </div>

                <div class="row" style="margin:8px auto; display:none;">
                    <div class="hidden">
                        <label>Option3</label>
                    </div>

                    <div class="hidden">
                        <input type="text" class="form-control" id="option3" name="option3[]" value="<?= $options[2] ?>" />
                    </div>

                    <div class="hidden">
                        <label>Option4</label>
                    </div>

                    <div class="hidden">
                        <input type="text" class="form-control" id="option4" name="option4[]" value="<?= $options[3] ?>" />
                    </div>
                </div>

                <div class="row" id="column_values_row" style="margin:8px auto; <?php if ($quetionType !== "MATCH") {
                                                                                    echo "display: none;";
                                                                                } ?>">
                    <div class="col-md-2">
                        <label>Column 1 letters</label>
                    </div>

                    <div class="col-md-4">
                        <input type="text" class="form-control" id="matchColumn1" name="matchColumn1[]" placeholder="Enter comma separated column 1 letters like a,b,c,d" />
                    </div>

                    <div class="col-md-2">
                        <label>Column 2 letters</label>
                    </div>

                    <div class="col-md-4">
                        <input type="text" class="form-control" id="matchColumn2" name="matchColumn2[]" placeholder="Enter comma separated column 2 letters like p,q,r,s" />
                    </div>
                </div>

            </div>
        <?php
        }
        ?>
        <input type="hidden" name="redirect" value="<?= $redirect; ?>" required />
        <input type="submit" id="add_bulk_questions_submit_button" class="btn btn-primary text-uppercase" value="Upload Multiple Questions in Test" name="frm_test_ques_submit" style="display: none;">
    </form>
</div>