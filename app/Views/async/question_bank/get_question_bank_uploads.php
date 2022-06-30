<hr />

<div class="d-flex justify-content-center mb-4">
    <div class="rounded-3 border border-4 border-warning p-3 text-center">
        <input type="file" name="questions_files[]" id="questions_files" multiple onchange="checkNumberOfImages();">
        <label>Please Upload <?= $noQues; ?> Question Images</label>
    </div>
</div>

<hr />

<div class="d-flex justify-content-center">
    <select id="select-solutions-checkboxes-dropdown" class="form-select form-select-sm" onchange="check_uncheck_all_solution_checkboxes(this)" style="max-width: 400px;">
        <option value="">Check/Uncheck solution checkboxes</option>
        <option value="1">All questions have solutions (Check all checkboxes for uploading solutions)</option>
        <option value="0">No questions have solutions (Check none of the checkboxes for uploading solutions)</option>
    </select>
</div>



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
}
if ($optionsType == "letters") {
    array_push($options, "A)", "B)", "C)", "D)");
}
?>

<?php

for ($i = 1; $i <= $noQues; $i++) {
?>

    <div class="row my-4 justify-content-center">

        <div class="col-md-1 d-flex align-items-end my-2">
            <h4><span class="badge rounded-pill bg-secondary"><?= $i ?></span></h4>
        </div>

        <div class="col-md-3 my-2">
            <label for="question_difficulty_level" class="form-label">Difficulty level</label>
            <select class="form-select" id="question_difficulty_level" name="difficulty_levels[]">
                <option value=""></option>
                <option value="1" <?php if ($difficulty_level === "1") echo "selected"; ?>> 1 - Low </option>
                <option value="2" <?php if ($difficulty_level === "2") echo "selected"; ?>> 2 - Low-Moderate </option>
                <option value="3" <?php if ($difficulty_level === "3") echo "selected"; ?>> 3 - Moderate </option>
                <option value="4" <?php if ($difficulty_level === "4") echo "selected"; ?>> 4 - Moderate-High </option>
                <option value="5" <?php if ($difficulty_level === "5") echo "selected"; ?>> 5 - High </option>
            </select>
        </div>

        <div class="col-md-3 my-2">
            <label for="question_types" class="form-label">Question type</label>
            <select class="form-select" id="question_types" name="question_types[]" onchange="changeCorrectAnswerInput(this)">
                <option <?php if ($quetionType === "SINGLE") echo "selected"; ?> value="SINGLE">SINGLE</option>
                <option <?php if ($quetionType === "MULTIPLE") echo "selected"; ?> value="MULTIPLE">MULTIPLE</option>
                <option <?php if ($quetionType === "NUMBER") echo "selected"; ?> value="NUMBER">NUMBER</option>
                <option <?php if ($quetionType === "MATCH") echo "selected"; ?> value="MATCH">MATCH</option>
                <option <?php if ($quetionType === "PASSAGE_MULTIPLE") echo "selected"; ?> value="PASSAGE_MULTIPLE">PASSAGE MULTIPLE</option>
                <option <?php if ($quetionType === "DESCRIPTIVE") echo "selected"; ?> value="DESCRIPTIVE">Subjective Answer</option>
            </select>
        </div>


        <div class="col-md-3 my-2">
            <label class="form-label">Correct Answer</label>

            <input type="text" class="form-control answer_input_text" id="Que_correct_ans" name="Que_correct_ans[]" placeholder="option1,option2,.../number/match" <?= $questionOtherDisplay ?> />

            <select class="form-select answer_input_selector" id="Que_correct_ans" name="Que_correct_ans[]" <?= $questionSingleDisplay ?>>
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

    <div class="row justify-content-center" id="column_values_row" style="<?php if ($quetionType !== "MATCH") {
                                                                                echo "display: none;";
                                                                            } ?>">
        <div class="col-md-4">
            <label class="form-label">Column 1 letters</label>
            <input type="text" class="form-control" id="matchColumn1" name="matchColumn1[]" placeholder="Enter comma separated column 1 letters like a,b,c,d" />
        </div>



        <div class="col-md-4">
            <label class="form-label">Column 2 letters</label>
            <input type="text" class="form-control" id="matchColumn2" name="matchColumn2[]" placeholder="Enter comma separated column 2 letters like p,q,r,s" />
        </div>
    </div>


    <div class="row justify-content-end">
        <div class="col-md-3 d-flex align-items-end my-2">
            <div class="form-check">
                <input class="question_solution_checkbox" name="question_solution_check[]" id="has_solution_checkbox_<?= $i ?>" type="checkbox" value="1">
                <label class="form-check-label" for="has_solution_checkbox_<?= $i ?>">
                    Has Solution
                </label>
            </div>
        </div>
    </div>

    <hr />
<?php
}
?>

<div class="d-flex justify-content-center align-items-center mt-4">
    <div class="rounded-3 border border-4 border-warning p-3 text-center">
        <input type="file" name="solution_files[]" id="solution_files" multiple onchange="checkNumberOfImages();">
        <label>Please Upload Solution Images</label>
    </div>
</div>

<hr />


<input type="hidden" name="redirect" value="<?= $redirect; ?>" required />
<input type="submit" id="add_bulk_questions_submit_button" class="btn btn-primary text-uppercase mt-4" value="Upload Multiple Questions" name="add_bulk_questions_submit_button" style="display: none;">