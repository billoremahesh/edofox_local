<!-- Include Header -->
<?php include_once(APPPATH . "Views/header.php"); ?>

<!-- Custom CSS -->
<link href="<?php echo base_url('assets/css/tests/update_test_questions.css?v=20220201'); ?>" rel="stylesheet">


<script type="text/javascript" src="<?php echo base_url('assets/js/mathjax-config.js'); ?>"></script>



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

        <div class="">


            <?php

            $data1 = array(
                "id" => $test_sess_id
            );
            $data = array(
                "test" => $data1
            );
            $data_string = json_encode($data);
            // echo $data_string;

            // Initiate curl
            $ch = curl_init();
            // Disable SSL verification
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            // Will return the response, if false it print the response
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            // Username and Password
            // curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);
            // POST ROW data
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data_string),
                'AuthToken: ' . decrypt_cipher($_SESSION['admin_token'])
            ));
            // Set the url
            // Include Service URLs Parameters File
            include_once(APPPATH . "Views/service_urls.php");
            curl_setopt($ch, CURLOPT_URL, $fetchTestDataUrl);
            // Execute
            $objTestString = curl_exec($ch);
            // Closing
            curl_close($ch);
            // echo $objTestString;
            $objTest = json_decode($objTestString, true);


            /**
             * Fetching each question if the test has questions
             */
            if (isset($objTest["test"]["test"])) {
            ?>



                <div class="container-fluid" id="main_div">
                    <div class="card_box">
                        <div style="display: flex; justify-content: space-between;">

                            <a data-bs-toggle='tooltip' class='btn btn-warning' href="<?= base_url('tests/add_test_solutions/' . $test_id); ?>"> Add Solutions </a>


                            <a data-bs-toggle='tooltip' class='btn btn-primary' href="<?= base_url('tests/add_test_img_questions/' . $test_id); ?>"> Add Questions in Bulk </a>

                            <a data-bs-toggle='tooltip' class='btn btn-info' href="<?= base_url('tests/add_answer_key/' . $test_id); ?>"> Add Answer Key </a>

                            <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#TestInstructionModal">
                                Add Custom Instruction
                            </button>


                            <div class="btn-group">
                                <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    ADD SINGLE QUESTION
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item" href="<?php echo base_url('tests/add_test_question/1/Physics/' . $test_id); ?>"> Add Physics Question</a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="<?php echo base_url('tests/add_test_question/2/Mathematics/' . $test_id); ?>"> Add Math Question </a>
                                    </li>

                                    <li>

                                        <a class="dropdown-item" href="<?php echo base_url('tests/add_test_question/3/Chemistry/' . $test_id); ?>"> Add Chemistry Question </a>

                                    </li>

                                    <li>
                                        <a class="dropdown-item" href="<?php echo base_url('tests/add_test_question/4/Biology/' . $test_id); ?>"> Add Biology Question </a>

                                    </li>

                                    <li>

                                        <a class="dropdown-item" href="<?php echo base_url('tests/add_test_question/5/GK/' . $test_id); ?>"> Add GK Question </a>

                                    </li>
                                </ul>
                            </div>


                            <a class="btn btn-secondary" href="<?= base_url('tests/reset_question_numbers/' . $test_id); ?>">
                                Reset Question Numbers
                            </a>

                            <?php
                            if ($test_details['exam_conduction'] == "Offline" && !empty($test_details['paper_pdf_url'])) {
                            ?>
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#TestPdfPaperModal">
                                    PDF Paper
                                </button>
                            <?php
                            }
                            ?>
                        </div>
                        <?php
                        // Subject And Section Cards
                        $subject_cards_arr = array();
                        foreach ($objTest["test"]["test"] as $question) {
                            // Check Unique Subjects
                            if (isset($question['subject']) && !empty($question['subject'])) {
                                if (!array_key_exists($question['subject'], $subject_cards_arr)) {
                                    $subject_cards_arr[$question['subject']]['subject_count'] = 1;
                                } else {
                                    $subject_cards_arr[$question['subject']]['subject_count'] += 1;
                                }

                                // Check Section Key exists
                                if (!array_key_exists('sections', $subject_cards_arr[$question['subject']])) {
                                    $subject_cards_arr[$question['subject']]['sections'] = array();
                                }

                                // Check Unique Sections
                                if (isset($question['section']) && !empty($question['section'])) {
                                    if (!array_key_exists($question['section'], $subject_cards_arr[$question['subject']]['sections'])) {
                                        $subject_cards_arr[$question['subject']]['sections'][$question['section']]['section_count'] = 1;
                                    } else {
                                        $subject_cards_arr[$question['subject']]['sections'][$question['section']]['section_count'] += 1;
                                    }
                                }
                            }
                        }


                        ?>

                        <?php
                        if (!empty($subject_cards_arr)) {
                        ?>
                            <div class='row subject_section_card_block'>
                                <?php
                                foreach ($subject_cards_arr as $key => $subject_cards) {
                                ?>
                                    <div class='col-sm-3'>
                                        <div class='subjects-counts-block'>
                                            <div class='top-counts-flexdiv'>
                                                <label class='counts-title'><?= $key; ?></label>
                                                <span class='count-number custom_count_badge'><?= $subject_cards['subject_count']; ?></span>
                                            </div>
                                            <?php
                                            if (!empty($subject_cards['sections'])) {
                                            ?>
                                                <span class="section_card_label">Sections :</span>
                                                <?php
                                                foreach ($subject_cards['sections'] as $section_key => $section_cards) {
                                                ?>
                                                    <div class='top-counts-flexdiv'>
                                                        <label class='counts-subtitle'><?= $section_key; ?></label>
                                                        <span class='count-subnumber custom_count_badge'><?= $section_cards['section_count']; ?></span>
                                                    </div>
                                            <?php
                                                }
                                            }
                                            ?>
                                        </div>
                                    </div>
                                <?php
                                }
                                ?>
                            </div>
                        <?php
                        }
                        ?>

                        <!-- Showing the index of question numbers to jump directly to them -->
                        <div style="display: flex; overflow-x: auto; margin-top:16px; padding: 4px; border-radius: 4px;">
                            <?php
                            $qnno_i = 1;
                            foreach ($objTest["test"]["test"] as $question) :

                                $que_id = $question['qn_id'];

                                if (isset($question['questionNumber'])) {
                                    $question_number = $question['questionNumber'];
                                } else {
                                    $question_number = $qnno_i;
                                    $qnno_i++;
                                }
                            ?>
                                <button class="btn btn-secondary" onclick="scrollToQuestionCard('<?= $que_id ?>')" style="margin: 0px 4px;"><?= $question_number ?></button>
                            <?php endforeach; ?>
                        </div>

                        <div>


                            <?php
                            $options_array = array("option1", "option2", "option3", "option4", "option5", "BONUS", "cancel");
                            $qnno_i = 1;
                            foreach ($objTest["test"]["test"] as $question) {

                                $que_id = $question['qn_id'];
                                if (isset($question['questionNumber'])) {
                                    $question_number = $question['questionNumber'];
                                } else {
                                    $question_number = $qnno_i;
                                    $qnno_i++;
                                }

                                $question_test_section = $question['section'];

                                if (isset($question['question'])) {
                                    $questionText = $question['question'];
                                    $questionText = str_replace('$$', '$', $questionText);
                                } else {
                                    $questionText = "";
                                }

                                if (isset($question['option1'])) {
                                    $option1 = str_replace('$$', '$', $question['option1']);
                                } else {
                                    $option1 = "";
                                }

                                if (isset($question['option2'])) {
                                    $option2 = str_replace('$$', '$', $question['option2']);
                                } else {
                                    $option2 = "";
                                }

                                if (isset($question['option3'])) {
                                    $option3 = str_replace('$$', '$', $question['option3']);
                                } else {
                                    $option3 = "";
                                }

                                if (isset($question['option4'])) {
                                    $option4 = str_replace('$$', '$', $question['option4']);
                                } else {
                                    $option4 = "";
                                }


                                if (isset($question['option5'])) {
                                    $option5 = str_replace('$$', '$', $question['option5']);
                                } else {
                                    $option5 = "";
                                }

                                if (isset($question['correctAnswer'])) {
                                    $correct_answer = $question['correctAnswer'];
                                } else {
                                    $correct_answer = "";
                                }

                                if (isset($question['alternateAnswer'])) {
                                    $alternate_answer = $question['alternateAnswer'];
                                } else {
                                    $alternate_answer = "";
                                }

                                if (isset($question['subject'])) {
                                    $subject_name = $question['subject'];
                                } else {
                                    $subject_name = "";
                                }

                                if (isset($question['chapter']['chapterId'])) {
                                    $chapter_id = $question['chapter']['chapterId'];
                                    $chapter_name = $question['chapter']['chapterName'];
                                } else {
                                    $chapter_id = "";
                                    $chapter_name = "Chapter";
                                }

                                if (isset($question['level'])) {
                                    $level = $question['level'];
                                } else {
                                    $level = "Difficulty";
                                }

                                if (isset($question['solution'])) {
                                    $solutionText = $question['solution'];
                                } else {
                                    $solutionText = "";
                                }

                                if (isset($question['weightage'])) {
                                    $positive_marks = $question['weightage'];
                                } else {
                                    $positive_marks = "";
                                }

                                if (isset($question['negativeMarks'])) {
                                    $negative_marks = $question['negativeMarks'];
                                } else {
                                    $negative_marks = "";
                                }

                                if (isset($question['meta_data'])) {
                                    $meta_data = $question['meta_data'];
                                } else {
                                    $meta_data = "";
                                }

                                if (isset($question['type'])) {
                                    $question_type = $question['type'];
                                    if ($question_type === NULL) {
                                        $question_type = "SINGLE";
                                    } else if ($question_type === "PASSAGE_MULTIPLE") {
                                        $question_type = "PASSAGE MULTIPLE";
                                    }
                                } else {
                                    $question_type = "";
                                }

                                if (isset($question['partialCorrection'])) {
                                    $partial_marking = $question['partialCorrection'];
                                    if (empty($partial_marking) || $partial_marking === "N") {
                                        $partial_marking = "NO";
                                    } else if ($partial_marking === "Y") {
                                        $partial_marking = "YES";
                                    }
                                } else {
                                    $partial_marking = "NO";
                                }

                                if (isset($question['meta_data_img_url'])) {
                                    $meta_data_imgUrl = $question['meta_data_img_url'];

                                    if ($meta_data_imgUrl != "") {
                                        $meta_data_imgUrlTag = "<img src='$meta_data_imgUrl' class='img-fluid ques_imgs' alt='no-image'>";
                                    } else {
                                        $meta_data_imgUrlTag = "";
                                    }
                                } else {
                                    $meta_data_imgUrlTag = "";
                                }

                                if (isset($question['questionImageUrl'])) {
                                    $question_imgUrl = $question['questionImageUrl'];

                                    if ($question_imgUrl != "") {
                                        $question_imgUrlTag = "<img data-src='$question_imgUrl' class='img-fluid ques_imgs lazy' style='width:100%;max-width: 800px;' alt='no-image'>";
                                    } else {
                                        $question_imgUrlTag = "";
                                    }
                                } else {
                                    $question_imgUrlTag = "";
                                }


                                if (isset($question['solutionImageUrl'])) {
                                    $solution_imgUrl = $question['solutionImageUrl'];

                                    if ($solution_imgUrl != "") {
                                        $solution_imgUrlTag = "<img src='$solution_imgUrl' class='img-fluid ques_imgs' alt='Solution image'>";
                                    } else {
                                        $solution_imgUrlTag = "";
                                    }
                                } else {
                                    $solution_imgUrlTag = "";
                                }


                                echo "<div class='question_display_card' id='question_display_card_$que_id'>";
                                echo "<div class='top_title_div'>
                    ";
                            ?>

                                <input type='checkbox' name='question_ids[]' class='bulk_questions_select' value="<?php echo $que_id; ?>">


                                <!-- Subjects dropdown -->
                                <p class="subject_name">
                                    <span data-bs-toggle="tooltip" title="Subject" class="editable-blocks" onclick="showSubjectDropdown(this, '<?= $subject_name ?>')"><?= $subject_name; ?></span>

                                    <select onchange="updateSubject('<?php echo $que_id; ?>', this)" class="subject-dropdown d-none">


                                    </select>
                                </p>



                                <!-- Chapters dropdown -->
                                <p>
                                    <span data-bs-toggle="tooltip" title="Chapter" class="editable-blocks" onclick="showChapterDropdown(this, '<?= $subject_name ?>', '<?= $chapter_id ?>')"><?php echo $chapter_name; ?></span>

                                    <select onchange="updateChapter('<?php echo $que_id; ?>', this)" class="chapter-dropdown d-none">

                                    </select>
                                </p>



                                <!-- Difficulty dropdown -->
                                <p>
                                    <span data-bs-toggle="tooltip" title="Difficulty Level 1-5" class="editable-blocks" onclick="showDifficultyDropdown(this)"><?php echo $level; ?></span>
                                    <select class="d-none" onchange="updateQuestionDifficulty('<?php echo $que_id; ?>', this)">
                                        <option value="">Difficulty</option>
                                        <option value="1" <?php if ($level == "1") { ?> selected="selected" <?php } ?>>1 - Low</option>
                                        <option value="2" <?php if ($level == "2") { ?> selected="selected" <?php } ?>>2 - Low-Moderate</option>
                                        <option value="3" <?php if ($level == "3") { ?> selected="selected" <?php } ?>>3 - Moderate</option>
                                        <option value="4" <?php if ($level == "4") { ?> selected="selected" <?php } ?>>4 - Moderate-High</option>
                                        <option value="5" <?php if ($level == "5") { ?> selected="selected" <?php } ?>>5 - High</option>

                                    </select>
                                </p>




                                <p>
                                    <span data-bs-toggle="tooltip" title="Question Type" class="editable-blocks" onclick="showQuestionTypeDropdown(this)"><?php echo $question_type; ?></span>
                                    <select class="d-none" onchange="updateQuestionType('<?php echo $que_id; ?>', this)">
                                        <option value="SINGLE" <?php if ($question_type == "SINGLE") { ?> selected="selected" <?php } ?>>SINGLE</option>
                                        <option value="MULTIPLE" <?php if ($question_type == "MULTIPLE") { ?> selected="selected" <?php } ?>>MULTIPLE</option>
                                        <option value="NUMBER" <?php if ($question_type == "NUMBER") { ?> selected="selected" <?php } ?>>NUMBER</option>
                                        <option value="MATCH" <?php if ($question_type == "MATCH") { ?> selected="selected" <?php } ?>>MATCH</option>
                                        <option value="PASSAGE_MULTIPLE" <?php if ($question_type == "PASSAGE_MULTIPLE") { ?> selected="selected" <?php } ?>>PASSAGE MULTIPLE</option>
                                        <option value="DESCRIPTIVE" <?php if ($question_type == "DESCRIPTIVE") { ?> selected="selected" <?php } ?>>Subjective Answer</option>

                                    </select>
                                </p>

                                <p>
                                    <span data-bs-toggle="tooltip" title="Partial Marking" class="editable-blocks" onclick="showPartialMarkingDropdown(this)"><?php echo $partial_marking; ?></span>
                                    <select class="d-none" onchange="updatePartialMarking('<?php echo $que_id; ?>', this)">
                                        <option value="N" <?php if ($partial_marking == "NO") { ?> selected="selected" <?php } ?>>NO</option>
                                        <option value="Y" <?php if ($partial_marking == "YES") { ?> selected="selected" <?php } ?>>YES</option>

                                    </select>
                                </p>

                                <p>
                                    <span data-bs-toggle="tooltip" title="Question Section" class="editable-blocks" onclick="showQuestionTestSectionInput(this)"><?php echo $question_test_section; ?></span>
                                    <input type="text" class="d-none" onfocusout="updateQuestionTestSection('<?php echo $que_id; ?>', this)" value="<?php echo $question_test_section; ?>" />
                                </p>


                                <div>
                                    <?php
                                    if (isset($question['questionImageUrl']) and !empty($question['questionImageUrl'])) {
                                    ?>
                                        <button class='btn btn-sm' onclick="replace_question_image(<?= $que_id; ?>);" data-bs-toggle='tooltip' title='Update Question Image'>
                                            <i class='material-icons material-icon-small text-muted'>edit</i>
                                        </button>

                                    <?php
                                    }
                                    ?>
                                    <button type="button" class="btn" style="background-color: transparent; color: red;" title="Delete Question From Test" data-bs-toggle="modal" data-bs-target="#disableQuestionModal" onclick="disableQuestion(<?= $que_id ?>, <?= $test_sess_id ?>)">
                                        <i class='material-icons material-icon-small text-danger' data-bs-toggle='tooltip' title='Remove Question'>delete</i>
                                    </button>
                                </div>



                                <?php
                                echo "</div>";
                                ?>

                                <!-- Display Message if not found Question text and image -->
                                <?php
                                if (isset($question['dummyQuestion']) && $question['dummyQuestion'] == 1) {
                                ?>

                                    <div>
                                        <span>
                                            <span data-bs-toggle="tooltip" title="Question Number" class="editable-blocks" onclick="showQuestionTestNumberInput(this)"><?php echo $question_number; ?></span>
                                            <input class="d-none" type="text" onfocusout="updateQuestionTestNumber('<?php echo $que_id; ?>', this)" value="<?php echo $question_number; ?>" />
                                        </span>
                                        :
                                    </div>

                                    <div class="my-2">
                                        Question details not available. Please refer uploaded question paper PDF
                                    </div>
                                <?php
                                } else {

                                    echo "<div class='metadata-block'>";
                                ?>
                                    <span>
                                        <span data-bs-toggle="tooltip" title="Question Metadata" style="padding: 4px; cursor: pointer;" onclick="showMetadataTextInput(this)"><?php echo $meta_data; ?> (Click to edit metadata)</span>
                                        <textarea class="d-none" style="width: 100%;" onfocusout="updateMetadataTestText('<?php echo $que_id; ?>', this)"><?php echo $meta_data; ?></textarea>
                                    </span>
                                    <?php
                                    echo "<div>$meta_data_imgUrlTag</div>
                    </div>";

                                    ?>

                                    <div>
                                        <span>
                                            <span data-bs-toggle="tooltip" title="Question Number" class="editable-blocks" onclick="showQuestionTestNumberInput(this)"><?php echo $question_number; ?></span>
                                            <input class="d-none" type="text" onfocusout="updateQuestionTestNumber('<?php echo $que_id; ?>', this)" value="<?php echo $question_number; ?>" />
                                        </span>

                                        :
                                        <span>
                                            <span data-bs-toggle="tooltip" title="Question Text" style="padding: 4px; cursor: pointer;" onclick="showQuestionTextInput(this)"><?php echo $questionText; ?> (Click to edit question text)</span>
                                            <textarea class="d-none" style="width: 100%;" onfocusout="updateQuestionTestText('<?php echo $que_id; ?>', this)"><?php echo $questionText; ?></textarea>
                                        </span>

                                    </div>



                                    <?php
                                    echo "<p>$question_imgUrlTag</p>";
                                    echo "<ol>
                    <li>";
                                    ?>

                                    <span>
                                        <span style="padding: 4px; cursor: pointer;" onclick="showOption1TextInput(this)"><?php echo $option1; ?> (Click to edit)</span>
                                        <textarea class="d-none" style="width: 100%;" onfocusout="updateOption1TestText('<?php echo $que_id; ?>', this)"><?php echo $option1; ?></textarea>
                                    </span>
                                    <?php

                                    echo "
                    </li>
                    <li>";
                                    ?>
                                    <span>
                                        <span style="padding: 4px; cursor: pointer;" onclick="showOption2TextInput(this)"><?php echo $option2; ?> (Click to edit)</span>
                                        <textarea class="d-none" style="width: 100%;" onfocusout="updateOption2TestText('<?php echo $que_id; ?>', this)"><?php echo $option2; ?></textarea>
                                    </span>
                                    <?php

                                    echo "
                    </li>
                    <li>";
                                    ?>
                                    <span>
                                        <span style="padding: 4px; cursor: pointer;" onclick="showOption3TextInput(this)"><?php echo $option3; ?> (Click to edit)</span>
                                        <textarea class="d-none" style="width: 100%;" onfocusout="updateOption3TestText('<?php echo $que_id; ?>', this)"><?php echo $option3; ?></textarea>
                                    </span>
                                    <?php

                                    echo "</li>";
                                    echo "<li>";
                                    ?>
                                    <span>
                                        <span style="padding: 4px; cursor: pointer;" onclick="showOption4TextInput(this)"><?php echo $option4; ?> (Click to edit)</span>
                                        <textarea class="d-none" style="width: 100%;" onfocusout="updateOption4TestText('<?php echo $que_id; ?>', this)"><?php echo $option4; ?></textarea>
                                    </span>
                                    <?php

                                    echo "</li>";

                                    echo  "<li>";
                                    ?>
                                    <span>
                                        <span style="padding: 4px; cursor: pointer;" onclick="showOption5TextInput(this)"><?php echo $option5; ?> (Click to edit)</span>
                                        <textarea class="d-none" style="width: 100%;" onfocusout="updateOption5TestText('<?php echo $que_id; ?>', this)"><?php echo $option5; ?></textarea>
                                    </span>
                                <?php
                                    echo "
                    </li>
                </ol>";
                                }

                                ?>


                                <div class='row'>
                                    <div class='col-sm-4'>
                                        <p class="correct-answer-block">Correct Answer:
                                            <span>
                                                <span style="padding: 4px; cursor: pointer;" onclick="showCorrectAnswerTextInput(this)"><?php echo $correct_answer; ?> (Click to edit)</span>

                                                <?php if ($question_type == "SINGLE") : ?>
                                                    <!-- Showing dropdown for SINGLE type question -->
                                                    <select class="d-none" onchange="updateCorrectAnswerTestText('<?php echo $que_id; ?>', this)">
                                                        <option value="">Select Correct Answer</option>
                                                        <option value="option1" <?php if ($correct_answer == "option1") { ?> selected="selected" <?php } ?>>Option 1</option>
                                                        <option value="option2" <?php if ($correct_answer == "option2") { ?> selected="selected" <?php } ?>>Option 2</option>
                                                        <option value="option3" <?php if ($correct_answer == "option3") { ?> selected="selected" <?php } ?>>Option 3</option>
                                                        <option value="option4" <?php if ($correct_answer == "option4") { ?> selected="selected" <?php } ?>>Option 4</option>
                                                        <option value="option5" <?php if ($correct_answer == "option5") { ?> selected="selected" <?php } ?>>Option 5</option>
                                                        <option value="BONUS" <?php if ($correct_answer == "BONUS") { ?> selected="selected" <?php } ?>>BONUS</option>
                                                        <option value="cancel" <?php if ($correct_answer == "cancel") { ?> selected="selected" <?php } ?>>cancel</option>
                                                    </select>

                                                <?php elseif ($question_type == "MULTIPLE" || $question_type == "PASSAGE MULTIPLE") :
                                                    $correct_answer_array = explode(",", $correct_answer);
                                                ?>
                                                    <!-- Showing multiselect dropdown for MULTIPLE type question -->
                                                    <select class="correct_answer_dropdown d-none" multiple onchange="updateCorrectAnswerTestText('<?php echo $que_id; ?>', this)">
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
                                                    <select class="correct_answer_dropdown d-none" multiple onchange="updateCorrectAnswerTestText('<?php echo $que_id; ?>', this)">
                                                        <option value="">Select Correct Matches</option>
                                                        <!-- Fetching the comma separated values in correct answer and showing in select2 -->
                                                        <?php foreach ($match_options_array as $option) : ?>

                                                            <option value="<?= $option ?>" <?php if (in_array($option, $correct_answer_array)) : echo 'selected="selected"';
                                                                                            endif;  ?>><?= $option ?></option>

                                                        <?php endforeach; ?>
                                                    </select>

                                                <?php elseif ($question_type == "NUMBER") : ?>
                                                    <input type="text" class="d-none" onfocusout="updateCorrectAnswerTestText('<?php echo $que_id; ?>', this)" value="<?php echo $correct_answer; ?>" />
                                                <?php endif; ?>

                                            </span>
                                        </p>
                                    </div>

                                    <div class='col-sm-4'>
                                        <p>Alternate Answer:
                                            <span>
                                                <span style="padding: 4px; cursor: pointer;" onclick="showAlternateAnswerTextInput(this)"><?php echo $alternate_answer; ?> (Click to edit)</span>
                                                <input type="text" class="d-none" onfocusout="updateAlternateAnswerText('<?php echo $que_id; ?>', this)" value="<?php echo $alternate_answer; ?>" />
                                            </span>
                                        </p>
                                    </div>


                                    <div class='col-sm-4'>
                                        <p>Positive Marks:
                                            <span>
                                                <span class="editable-blocks" onclick="showPositiveMarksTestInput(this)"><?php echo $positive_marks; ?></span>
                                                <input type="text" class="d-none" onfocusout="updatePositiveMarksTest('<?php echo $test_sess_id; ?>','<?php echo $que_id; ?>', this)" value="<?php echo $positive_marks; ?>" />
                                            </span>

                                            &emsp; Negative Marks:

                                            <span>
                                                <span class="editable-blocks" onclick="showNegativeMarksTestInput(this)"><?php echo $negative_marks; ?></span>
                                                <input type="text" class="d-none" onfocusout="updateNegativeMarksTest('<?php echo $test_sess_id; ?>','<?php echo $que_id; ?>', this)" value="<?php echo $negative_marks; ?>" />
                                            </span>
                                        </p>
                                    </div>

                                    <?php if (!empty($solutionText) || !empty($solution_imgUrlTag)) : ?>

                                        <div class='col-xs-12'>
                                            <div class='text-muted'>
                                                <u>Solution: </u>
                                                <button class='btn btn-sm' onclick="replace_solution_image(<?= $que_id; ?>);" data-bs-toggle='tooltip' title='Update Solution Image'>
                                                    <i class='material-icons material-icon-small text-muted'>edit</i>
                                                </button>
                                            </div>

                                            <div><?= $solutionText ?></div>
                                            <div><?= $solution_imgUrlTag ?> </div>

                                        </div>
                                    <?php else : ?>
                                        <div class='col-xs-12'>
                                            <button class='btn btn-outline-secondary btn-sm' onclick="replace_solution_image(<?= $que_id; ?>);">
                                                Add Solution Image
                                            </button>
                                        </div>
                                <?php endif;
                                    echo "</div>";
                                    echo "</div>";
                                }
                            } else {
                                /**
                                 * The test has no questions yet.
                                 * Prompt to add questions
                                 */
                                ?>
                                <div class="text-center">
                                    <img src="https://edofox-s3.s3.ap-south-1.amazonaws.com/public/statics/error-page.png" style="width: 96px; margin-top: 32px;" />

                                    <p class="text-danger">No questions here. Please add some questions first and then come here to check them.</p>
                                    <a class="btn btn-primary text-uppercase" href="<?= base_url('/tests/add_test_img_questions/' . $test_id); ?>">Add Questions Now</a>
                                </div>
                            <?php
                            }
                            ?>
                                </div>

                        </div>

                    </div>



                    <!-- Modal -->
                    <div id="disableQuestionModal" class="modal fade" role="dialog">
                        <div class="modal-dialog">

                            <!-- Modal content-->
                            <div class="modal-content">
                                <div class="modal-header">

                                    <h6 class="modal-title">Delete Question?</h6>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="to_disable_question_id" id="to_disable_question_id" value="" required>
                                    <input type="hidden" name="to_disable_test_id" id="to_disable_test_id" value="" required>
                                    <p>Are you sure you want to delete this question from this test?</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary" onclick="disableQuestionFromTest();">Yes, Delete</button>
                                </div>
                            </div>

                        </div>
                    </div>






                    <!-- Modal -->
                    <div class="modal fade" id="TestInstructionModal" tabindex="-1" role="dialog" aria-labelledby="TestInstructionModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">

                                    <h6 class="modal-title">Add Custom Instruction</h6>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>


                                </div>
                                <?php
                                $attributes = ['class' => 'cmxform', 'id' => 'myform'];
                                ?>
                                <?php echo form_open_multipart('tests/add_instruction_submit', $attributes); ?>

                                <div class="modal-body">

                                    <div class="col-md-12">
                                        <div class="mb-2">
                                            <?php
                                            $custom_instructions = "";
                                            if (isset($objTest['test']['instructions']) && !empty($objTest['test']['instructions'])) {
                                                $custom_instructions = html_entity_decode($objTest['test']['instructions']);
                                            }
                                            ?>
                                            <div class="document-editor">
                                                <div class="document-editor__toolbar"></div>
                                                <div class="document-editor__editable-container" style="border: 1px solid #B8B9C2;">
                                                    <div class="document-editor__editable">
                                                        <?= $custom_instructions ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <textarea style="visibility: hidden;" id="add_instruction" name="add_instruction" required></textarea>
                                        </div>
                                    </div>

                                </div>
                                <div class="modal-footer">
                                    <input type="hidden" name="test_id" value="<?= $test_id; ?>" required />
                                    <input type="hidden" name="redirect" value="<?= $redirect; ?>" required />
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary" id="add_instruction_submit" name="add_instruction_submit">Add</button>
                                </div>
                                <?php echo form_close(); ?>
                            </div>
                        </div>
                    </div>




                    <!-- Upload Test PDF Paper Modal -->
                    <div class="modal fade" id="TestPdfPaperModal" tabindex="-1" role="dialog" aria-labelledby="TestPdfPaperModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h6 class="modal-title">Test PDF Paper</h6>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form action="#" method="post" id="add_test_pdf_paper">
                                    <div class="modal-body">
                                        <div class="col-md-12">
                                            <div class="my-4">
                                                <?php
                                                if ($test_details['exam_conduction'] == "Offline" && !empty($test_details['paper_pdf_url'])) {
                                                ?>

                                                    <a href="<?= $test_details['paper_pdf_url']; ?>" target="_blank">View PDF Paper</a>
                                                <?php
                                                }
                                                ?>
                                            </div>
                                            <div class="my-4">
                                                <label class="form-label" for="pdf_file">Choose PDF File *</label>
                                                <input type="file" class="form-control" id="pdf_file" name="pdf_file" required />
                                            </div>

                                            <p id="uploadError" class="text-danger"></p>
                                            <p id="uploadStatus" class="text-success"></p>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-success submitBtn" id="upload_exam_paper" name="upload_exam_paper" onclick="upload_exam_paper()">Replace</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>




                    <!-- Go to top button -->
                    <button onclick="topFunction()" id="btn-go-to-top" title="Go to top"><i class="fa fa-chevron-up" aria-hidden="true"></i></button>




                    <!-- The actual snackbar -->
                    <div id="snackbar">Some text some message..</div>



                    <div id="imageReplaceModal" class="modal fade" role="dialog">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h6 class="modal-title">Update Image for <span id="replace-image-modal-title"></span></h6>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="file" name="uploaded_file" id="uploaded_file" style="display: none;" accept="image/*">
                                    <!-- Drag and Drop container-->
                                    <div class="upload-area" id="uploadfile">
                                        <h6 id="drag_title">Drag and Drop file here<br />Or<br />Click to select file</h6>
                                        <img src="" style="display: none;" class="w-100 img-fluid" id="preview_upload">
                                        <canvas style="border:1px solid grey;" class="w-100" id="mycanvas">
                                    </div>
                                </div>

                                <div class="modal-footer"> <button type="button" class="btn btn-secondary" onclick="clearCanvas(true)">Close</button>
                                    <button type="button" class="btn btn-warning" onclick="clearCanvas(false)">Reset</button>
                                    <button type="button" id="upload_question_image" class="btn btn-primary d-none" onclick="upload_question_image()"> Save</button>
                                    <button type="button" id="upload_solution_image" class="btn btn-primary d-none" onclick="upload_solution_image()"> Save</button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
        </div>
    </div>


    <!-- Delete Bulk Questions Modal -->
    <div id="delete_bulk_questions_modal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"> Delete Bulk Questions </h4>
                </div>
                <?php echo form_open('tests/bulk_delete_question_ids'); ?>
                <div class="modal-body">
                    <input type="hidden" name="bulk_delete_question_ids" id="bulk_delete_question_ids" required>
                    <p id="delete_bulk_message" class="text-danger">

                    </p>
                    <div class="form-check">
                        <input class="form-check-input" name="reset_question_sequence" type="checkbox" value="1" id="reset_question_sequence">
                        <label class="form-check-label" for="reset_question_sequence">
                            Update question numbers after delete
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="redirect" value="<?= '/tests/update_test_questions/' . $test_id ?>" required />
                    <input type="hidden" name="test_id" value="<?= $test_id ?>" required />
                    <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger"> Yes </button>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>

    <!-- Counts of selected checkboxes -->
    <div id="hovering-checkboxes-count-block" style="position: fixed; bottom: 16px; left: 50%; transform: translateX(-50%); display: flex; justify-content: center;">
        <div class="d-none" id="active-questions-checkboxes-count-block" style="background-color: #2196f3; padding: 8px; border-radius: 4px; color: white; margin: 0px 8px" onclick="show_bulk_questions_delete_modal()">
            <span id="active_questions_checkboxes_count">0</span> questions selected for delete
        </div>
    </div>

    <!-- Include Footer -->
    <?php include_once(APPPATH . "Views/footer.php"); ?>

    <script src="<?php echo base_url('assets/js/img_replace.js'); ?>"></script>

    <script>
        var test_id = "<?= $test_sess_id; ?>";
        var encrypted_test_id = "<?= $test_id; ?>";
        //To show or hide the question subject text and dropdown
        function showSubjectDropdown(currentElement, subjectName) {
            $(currentElement).siblings().toggleClass('d-none').focus();
            $(currentElement).toggleClass('d-none');
            loadAllSubjects(subjectName);
        }
        //To show or hide the question test type text and dropdown
        function showChapterDropdown(currentElement, subjectName, chapterId) {
            $(currentElement).siblings().toggleClass('d-none').focus();
            $(currentElement).toggleClass('d-none');
            loadAllChapters(subjectName, chapterId);
        }
        //To show or hide the question difficulty level and dropdown
        function showDifficultyDropdown(currentElement) {
            $(currentElement).siblings().toggleClass('d-none').focus();
            $(currentElement).toggleClass('d-none');
        }
        //To show or hide the question chapter name text and dropdown
        function showQuestionTypeDropdown(currentElement) {
            $(currentElement).siblings().toggleClass('d-none').focus();
            $(currentElement).toggleClass('d-none');
        }
        //To show or hide the question partial marking text and dropdown
        function showPartialMarkingDropdown(currentElement) {
            $(currentElement).siblings().toggleClass('d-none').focus();
            $(currentElement).toggleClass('d-none');
        }
        //To show or hide the question test section text and input
        function showQuestionTestSectionInput(currentElement) {
            $(currentElement).siblings().toggleClass('d-none').focus();
            $(currentElement).toggleClass('d-none');
        }
        //To show or hide the question test NUMBER text and input
        function showQuestionTestNumberInput(currentElement) {
            $(currentElement).siblings().toggleClass('d-none').focus();
            $(currentElement).toggleClass('d-none');
        }

        //To show or hide the METADATA TEXT and input
        function showMetadataTextInput(currentElement) {
            $(currentElement).siblings().toggleClass('d-none').focus();
            $(currentElement).toggleClass('d-none');
        }

        //To show or hide the question text and input
        function showQuestionTextInput(currentElement) {
            $(currentElement).siblings().toggleClass('d-none').focus();
            $(currentElement).toggleClass('d-none');
        }

        //To show or hide the OPTION 1 and input
        function showOption1TextInput(currentElement) {
            $(currentElement).siblings().toggleClass('d-none').focus();
            $(currentElement).toggleClass('d-none');
        }

        //To show or hide the OPTION 2 and input
        function showOption2TextInput(currentElement) {
            $(currentElement).siblings().toggleClass('d-none').focus();
            $(currentElement).toggleClass('d-none');
        }

        //To show or hide the OPTION 3 and input
        function showOption3TextInput(currentElement) {
            $(currentElement).siblings().toggleClass('d-none').focus();
            $(currentElement).toggleClass('d-none');
        }

        //To show or hide the OPTION 4 and input
        function showOption4TextInput(currentElement) {
            $(currentElement).siblings().toggleClass('d-none').focus();
            $(currentElement).toggleClass('d-none');
        }

        //To show or hide the OPTION 5 and input
        function showOption5TextInput(currentElement) {
            $(currentElement).siblings().toggleClass('d-none').focus();
            $(currentElement).toggleClass('d-none');
        }

        //To show or hide the CORRECT ANSWER and input
        function showCorrectAnswerTextInput(currentElement) {
            $(currentElement).siblings().toggleClass('d-none').focus();
            $(currentElement).toggleClass('d-none');

            //Checking if the dropdown is multiselect, and if yes, then initializing select2 over it
            if ($(currentElement).siblings().first().hasClass("correct_answer_dropdown")) {
                $(currentElement).siblings().select2({
                    closeOnSelect: false
                });
            }
        }

        //To show or hide the ALTERNATE ANSWER and input
        function showAlternateAnswerTextInput(currentElement) {
            $(currentElement).siblings().toggleClass('d-none').focus();
            $(currentElement).toggleClass('d-none');
        }

        //To show or hide the POSITIVE MARKS and input
        function showPositiveMarksTestInput(currentElement) {
            $(currentElement).siblings().toggleClass('d-none').focus();
            $(currentElement).toggleClass('d-none');
        }

        //To show or hide the NEGATIVE MARKS and input
        function showNegativeMarksTestInput(currentElement) {
            $(currentElement).siblings().toggleClass('d-none').focus();
            $(currentElement).toggleClass('d-none');
        }

        //To asynchronously update the question type from dropdown
        function updateQuestionType(question_id, updatedSectionInputElement) {
            var updatedSection = updatedSectionInputElement.value;
            var dataString = 'update=qType' + '&newType=' + encodeURIComponent(updatedSection) + '&question_id=' + question_id + '&test_id=' + test_id;
            $.ajax({
                type: 'POST',
                data: dataString,
                url: base_url + '/tests/update_test_question_properties',
                success: function(data) {
                    //Show the text format and hide the input 
                    $(updatedSectionInputElement).siblings().toggleClass('d-none');
                    $(updatedSectionInputElement).siblings().text(updatedSectionInputElement.value);
                    $(updatedSectionInputElement).toggleClass('d-none');

                    // Get the snackbar DIV
                    var snackbarDiv = document.getElementById("snackbar");

                    // Add the "show" class to DIV                        
                    snackbarDiv.innerText = data;
                    snackbarDiv.className = "show";

                    // After 3 seconds, remove the show class from DIV
                    setTimeout(function() {
                        snackbarDiv.className = snackbarDiv.className.replace("show", "");
                    }, 3000);
                    window.location.reload(true);

                }
            });
        }

        //To asynchronously update the question partial marking from dropdown
        function updatePartialMarking(question_id, updatedSectionInputElement) {
            var updatedSection = updatedSectionInputElement.value;
            var dataString = 'update=partialMarking' + '&newMarking=' + encodeURIComponent(updatedSection) + '&question_id=' + question_id + '&test_id=' + test_id;
            $.ajax({
                type: 'POST',
                data: dataString,
                url: base_url + '/tests/update_test_question_properties',
                success: function(data) {
                    //Show the text format and hide the input 
                    $(updatedSectionInputElement).siblings().toggleClass('d-none');
                    $(updatedSectionInputElement).siblings().text(updatedSectionInputElement.value);
                    $(updatedSectionInputElement).toggleClass('d-none');

                    // Get the snackbar DIV
                    var snackbarDiv = document.getElementById("snackbar");

                    // Add the "show" class to DIV                        
                    snackbarDiv.innerText = data;
                    snackbarDiv.className = "show";

                    // After 3 seconds, remove the show class from DIV
                    setTimeout(function() {
                        snackbarDiv.className = snackbarDiv.className.replace("show", "");
                    }, 3000);
                }
            });
        }


        //To asynchronously update the question test section
        function updateQuestionTestSection(question_id, updatedSectionInputElement) {
            var updatedSection = updatedSectionInputElement.value;
            var dataString = 'update=section' + '&newSection=' + encodeURIComponent(updatedSection) + '&question_id=' + question_id + '&test_id=' + test_id;
            $.ajax({
                type: 'POST',
                data: dataString,
                url: base_url + '/tests/update_test_question_properties',
                success: function(data) {
                    //Show the text format and hide the input 
                    $(updatedSectionInputElement).siblings().toggleClass('d-none');
                    $(updatedSectionInputElement).siblings().text(updatedSectionInputElement.value);
                    $(updatedSectionInputElement).toggleClass('d-none');

                    // Get the snackbar DIV
                    var snackbarDiv = document.getElementById("snackbar");

                    // Add the "show" class to DIV                        
                    snackbarDiv.innerText = data;
                    snackbarDiv.className = "show";

                    // After 3 seconds, remove the show class from DIV
                    setTimeout(function() {
                        snackbarDiv.className = snackbarDiv.className.replace("show", "");
                    }, 3000);
                }
            });
        }

        //To asynchronously update the question test NUMBER
        function updateQuestionTestNumber(question_id, updatedNumberInputElement) {
            var updatedNumber = updatedNumberInputElement.value;
            var dataString = 'update=number' + '&newNumber=' + encodeURIComponent(updatedNumber) + '&question_id=' + question_id + '&test_id=' + test_id;
            $.ajax({
                type: 'POST',
                data: dataString,
                url: base_url + '/tests/update_test_question_properties',
                success: function(data) {
                    //Show the text format and hide the input 
                    $(updatedNumberInputElement).siblings().toggleClass('d-none');
                    $(updatedNumberInputElement).siblings().text(updatedNumberInputElement.value);
                    $(updatedNumberInputElement).toggleClass('d-none');

                    // Get the snackbar DIV
                    var snackbarDiv = document.getElementById("snackbar");

                    // Add the "show" class to DIV                        
                    snackbarDiv.innerText = data;
                    snackbarDiv.className = "show";

                    // After 3 seconds, remove the show class from DIV
                    setTimeout(function() {
                        snackbarDiv.className = snackbarDiv.className.replace("show", "");
                    }, 3000);
                }
            });
        }

        //To asynchronously update the METADATA TEXT
        function updateMetadataTestText(question_id, updatedMetadataTextInputElement) {
            var updatedMetadataText = updatedMetadataTextInputElement.value;
            var dataString = 'update=metadataText' + '&newText=' + encodeURIComponent(updatedMetadataText) + '&question_id=' + question_id + '&test_id=' + test_id;
            $.ajax({
                type: 'POST',
                data: dataString,
                url: base_url + '/tests/update_test_question_properties',
                success: function(data) {
                    //Show the text format and hide the input 
                    $(updatedMetadataTextInputElement).siblings().toggleClass('d-none');
                    $(updatedMetadataTextInputElement).siblings().text(updatedMetadataTextInputElement.value);
                    $(updatedMetadataTextInputElement).toggleClass('d-none');

                    // Get the snackbar DIV
                    var snackbarDiv = document.getElementById("snackbar");

                    // Add the "show" class to DIV                        
                    snackbarDiv.innerText = data;
                    snackbarDiv.className = "show";

                    // After 3 seconds, remove the show class from DIV
                    setTimeout(function() {
                        snackbarDiv.className = snackbarDiv.className.replace("show", "");
                    }, 3000);
                }
            });
        }

        //To asynchronously update the QUESTION TEXT
        function updateQuestionTestText(question_id, updatedQuestionTextInputElement) {
            var updatedQuestionText = updatedQuestionTextInputElement.value;
            var dataString = 'update=questionText' + '&newText=' + encodeURIComponent(updatedQuestionText) + '&question_id=' + question_id + '&test_id=' + test_id;
            $.ajax({
                type: 'POST',
                data: dataString,
                url: base_url + '/tests/update_test_question_properties',
                success: function(data) {
                    //Show the text format and hide the input 
                    $(updatedQuestionTextInputElement).siblings().toggleClass('d-none');
                    $(updatedQuestionTextInputElement).siblings().text(updatedQuestionTextInputElement.value);

                    //If empty string entered, then show the click to edit text again
                    if (updatedQuestionTextInputElement.value.trim() == "") {
                        $(updatedQuestionTextInputElement).siblings().text("(Click to edit)");
                    }

                    $(updatedQuestionTextInputElement).toggleClass('d-none');

                    // Get the snackbar DIV
                    var snackbarDiv = document.getElementById("snackbar");

                    // Add the "show" class to DIV                        
                    snackbarDiv.innerText = data;
                    snackbarDiv.className = "show";

                    // After 3 seconds, remove the show class from DIV
                    setTimeout(function() {
                        snackbarDiv.className = snackbarDiv.className.replace("show", "");
                    }, 3000);
                }
            });
        }

        //To asynchronously update the OPTION 1 Text
        function updateOption1TestText(question_id, updatedOption1TextInputElement) {
            var updatedOption1Text = updatedOption1TextInputElement.value;
            var dataString = 'update=option1Text' + '&newText=' + encodeURIComponent(updatedOption1Text) + '&question_id=' + question_id + '&test_id=' + test_id;
            $.ajax({
                type: 'POST',
                data: dataString,
                url: base_url + '/tests/update_test_question_properties',
                success: function(data) {
                    //Show the text format and hide the input 
                    $(updatedOption1TextInputElement).siblings().toggleClass('d-none');
                    $(updatedOption1TextInputElement).siblings().text(updatedOption1TextInputElement.value);
                    $(updatedOption1TextInputElement).toggleClass('d-none');

                    // Get the snackbar DIV
                    var snackbarDiv = document.getElementById("snackbar");

                    // Add the "show" class to DIV                        
                    snackbarDiv.innerText = data;
                    snackbarDiv.className = "show";

                    // After 3 seconds, remove the show class from DIV
                    setTimeout(function() {
                        snackbarDiv.className = snackbarDiv.className.replace("show", "");
                    }, 3000);
                }
            });
        }

        //To asynchronously update the OPTION 2 Text
        function updateOption2TestText(question_id, updatedOption2TextInputElement) {
            var updatedOption2Text = updatedOption2TextInputElement.value;
            var dataString = 'update=option2Text' + '&newText=' + encodeURIComponent(updatedOption2Text) + '&question_id=' + question_id + '&test_id=' + test_id;
            $.ajax({
                type: 'POST',
                data: dataString,
                url: base_url + '/tests/update_test_question_properties',
                success: function(data) {
                    //Show the text format and hide the input 
                    $(updatedOption2TextInputElement).siblings().toggleClass('d-none');
                    $(updatedOption2TextInputElement).siblings().text(updatedOption2TextInputElement.value);
                    $(updatedOption2TextInputElement).toggleClass('d-none');

                    // Get the snackbar DIV
                    var snackbarDiv = document.getElementById("snackbar");

                    // Add the "show" class to DIV                        
                    snackbarDiv.innerText = data;
                    snackbarDiv.className = "show";

                    // After 3 seconds, remove the show class from DIV
                    setTimeout(function() {
                        snackbarDiv.className = snackbarDiv.className.replace("show", "");
                    }, 3000);
                }
            });
        }

        //To asynchronously update the OPTION 3 Text
        function updateOption3TestText(question_id, updatedOption3TextInputElement) {
            var updatedOption3Text = updatedOption3TextInputElement.value;
            var dataString = 'update=option3Text' + '&newText=' + encodeURIComponent(updatedOption3Text) + '&question_id=' + question_id + '&test_id=' + test_id;
            $.ajax({
                type: 'POST',
                data: dataString,
                url: base_url + '/tests/update_test_question_properties',
                success: function(data) {
                    //Show the text format and hide the input 
                    $(updatedOption3TextInputElement).siblings().toggleClass('d-none');
                    $(updatedOption3TextInputElement).siblings().text(updatedOption3TextInputElement.value);
                    $(updatedOption3TextInputElement).toggleClass('d-none');

                    // Get the snackbar DIV
                    var snackbarDiv = document.getElementById("snackbar");

                    // Add the "show" class to DIV                        
                    snackbarDiv.innerText = data;
                    snackbarDiv.className = "show";

                    // After 3 seconds, remove the show class from DIV
                    setTimeout(function() {
                        snackbarDiv.className = snackbarDiv.className.replace("show", "");
                    }, 3000);
                }
            });
        }

        //To asynchronously update the OPTION 4 Text
        function updateOption4TestText(question_id, updatedOption4TextInputElement) {
            var updatedOption4Text = updatedOption4TextInputElement.value;
            var dataString = 'update=option4Text' + '&newText=' + encodeURIComponent(updatedOption4Text) + '&question_id=' + question_id + '&test_id=' + test_id;
            $.ajax({
                type: 'POST',
                data: dataString,
                url: base_url + '/tests/update_test_question_properties',
                success: function(data) {
                    //Show the text format and hide the input 
                    $(updatedOption4TextInputElement).siblings().toggleClass('d-none');
                    $(updatedOption4TextInputElement).siblings().text(updatedOption4TextInputElement.value);
                    $(updatedOption4TextInputElement).toggleClass('d-none');

                    // Get the snackbar DIV
                    var snackbarDiv = document.getElementById("snackbar");

                    // Add the "show" class to DIV                        
                    snackbarDiv.innerText = data;
                    snackbarDiv.className = "show";

                    // After 3 seconds, remove the show class from DIV
                    setTimeout(function() {
                        snackbarDiv.className = snackbarDiv.className.replace("show", "");
                    }, 3000);
                }
            });
        }


        //To asynchronously update the OPTION 5 Text
        function updateOption5TestText(question_id, updatedOption5TextInputElement) {
            var updatedOption5Text = updatedOption5TextInputElement.value;
            var dataString = 'update=option5Text' + '&newText=' + encodeURIComponent(updatedOption5Text) + '&question_id=' + question_id + '&test_id=' + test_id;
            $.ajax({
                type: 'POST',
                data: dataString,
                url: base_url + '/tests/update_test_question_properties',
                success: function(data) {
                    //Show the text format and hide the input 
                    $(updatedOption5TextInputElement).siblings().toggleClass('d-none');
                    $(updatedOption5TextInputElement).siblings().text(updatedOption5TextInputElement.value);
                    $(updatedOption5TextInputElement).toggleClass('d-none');

                    // Get the snackbar DIV
                    var snackbarDiv = document.getElementById("snackbar");

                    // Add the "show" class to DIV                        
                    snackbarDiv.innerText = data;
                    snackbarDiv.className = "show";

                    // After 3 seconds, remove the show class from DIV
                    setTimeout(function() {
                        snackbarDiv.className = snackbarDiv.className.replace("show", "");
                    }, 3000);
                }
            });
        }

        //To asynchronously update the CORRECT ANSWER Text
        function updateCorrectAnswerTestText(question_id, updatedCorrectAnswerTextInputElement) {
            var updatedCorrectAnswerText = $(updatedCorrectAnswerTextInputElement).val();

            if (typeof updatedCorrectAnswerText === "object") {
                //if the values are in array, then convert them into comma separated values
                updatedCorrectAnswerText = updatedCorrectAnswerText.join();
            }

            var dataString = 'update=correctAnswerText' + '&newText=' + encodeURIComponent(updatedCorrectAnswerText) + '&question_id=' + question_id + '&test_id=' + test_id;
            $.ajax({
                type: 'POST',
                data: dataString,
                url: base_url + '/tests/update_test_question_properties',
                success: function(data) {
                    //Show the text format and hide the input 
                    $(updatedCorrectAnswerTextInputElement).siblings().toggleClass('d-none');
                    $(updatedCorrectAnswerTextInputElement).siblings().text(updatedCorrectAnswerText);

                    //Checking if the dropdown is multiselect, and if yes, then destroying it before hiding it to solve bug of repeated selected values visible
                    if ($(updatedCorrectAnswerTextInputElement).hasClass("correct_answer_dropdown")) {
                        $(updatedCorrectAnswerTextInputElement).select2('destroy');
                    }
                    $(updatedCorrectAnswerTextInputElement).toggleClass('d-none');

                    //If empty string entered, then show the click to edit text again
                    if (updatedCorrectAnswerText.trim() == "") {
                        $(updatedCorrectAnswerTextInputElement).siblings().text("(Click to edit)");
                    }
                    // Get the snackbar DIV
                    var snackbarDiv = document.getElementById("snackbar");

                    // Add the "show" class to DIV                        
                    snackbarDiv.innerText = data;
                    snackbarDiv.className = "show";

                    // After 3 seconds, remove the show class from DIV
                    setTimeout(function() {
                        snackbarDiv.className = snackbarDiv.className.replace("show", "");
                    }, 3000);
                }
            });
        }

        //To asynchronously update the ALTERNATE ANSWER Text
        function updateAlternateAnswerText(question_id, updatedAlternateAnswerTextInputElement) {
            var updatedAlternateAnswerText = updatedAlternateAnswerTextInputElement.value;
            var dataString = 'update=alternateAnswerText' + '&newText=' + encodeURIComponent(updatedAlternateAnswerText) + '&question_id=' + question_id + '&test_id=' + test_id;
            $.ajax({
                type: 'POST',
                data: dataString,
                url: base_url + '/tests/update_test_question_properties',
                success: function(data) {
                    //Show the text format and hide the input 
                    $(updatedAlternateAnswerTextInputElement).siblings().toggleClass('d-none');
                    $(updatedAlternateAnswerTextInputElement).siblings().text(updatedAlternateAnswerTextInputElement.value);
                    $(updatedAlternateAnswerTextInputElement).toggleClass('d-none');

                    // Get the snackbar DIV
                    var snackbarDiv = document.getElementById("snackbar");

                    // Add the "show" class to DIV                        
                    snackbarDiv.innerText = data;
                    snackbarDiv.className = "show";

                    // After 3 seconds, remove the show class from DIV
                    setTimeout(function() {
                        snackbarDiv.className = snackbarDiv.className.replace("show", "");
                    }, 3000);
                }
            });
        }

        //To asynchronously update the POSITIVE MARKS 
        function updatePositiveMarksTest(test_id, question_id, updatedPositiveMarksInputElement) {
            var updatedPositiveMarksNumber = updatedPositiveMarksInputElement.value;
            var dataString = 'update=positiveMarks' + '&newText=' + encodeURIComponent(updatedPositiveMarksNumber) + '&question_id=' + question_id + '&test_id=' + test_id;
            $.ajax({
                type: 'POST',
                data: dataString,
                url: base_url + '/tests/update_test_question_properties',
                success: function(data) {
                    //Show the text format and hide the input 
                    $(updatedPositiveMarksInputElement).siblings().toggleClass('d-none');
                    $(updatedPositiveMarksInputElement).siblings().text(updatedPositiveMarksInputElement.value);
                    $(updatedPositiveMarksInputElement).toggleClass('d-none');

                    // Get the snackbar DIV
                    var snackbarDiv = document.getElementById("snackbar");

                    // Add the "show" class to DIV                        
                    snackbarDiv.innerText = data;
                    snackbarDiv.className = "show";

                    // After 3 seconds, remove the show class from DIV
                    setTimeout(function() {
                        snackbarDiv.className = snackbarDiv.className.replace("show", "");
                    }, 3000);
                }
            });
        }

        //To asynchronously update the NEGATIVE MARKS 
        function updateNegativeMarksTest(test_id, question_id, updatedNegativeMarksInputElement) {
            var updatedNegativeMarksNumber = updatedNegativeMarksInputElement.value;
            var dataString = 'update=negativeMarks' + '&newText=' + encodeURIComponent(updatedNegativeMarksNumber) + '&question_id=' + question_id + '&test_id=' + test_id;
            $.ajax({
                type: 'POST',
                data: dataString,
                url: base_url + '/tests/update_test_question_properties',
                success: function(data) {
                    //Show the text format and hide the input 
                    $(updatedNegativeMarksInputElement).siblings().toggleClass('d-none');
                    $(updatedNegativeMarksInputElement).siblings().text(updatedNegativeMarksInputElement.value);
                    $(updatedNegativeMarksInputElement).toggleClass('d-none');

                    // Get the snackbar DIV
                    var snackbarDiv = document.getElementById("snackbar");

                    // Add the "show" class to DIV                        
                    snackbarDiv.innerText = data;
                    snackbarDiv.className = "show";

                    // After 3 seconds, remove the show class from DIV
                    setTimeout(function() {
                        snackbarDiv.className = snackbarDiv.className.replace("show", "");
                    }, 3000);
                }
            });
        }





        // To Disable question from this test
        function disableQuestion(q_id, test_id) {
            $("#to_disable_question_id").val(q_id);
            $("#to_disable_test_id").val(test_id);
        }

        //To asynchronously Disable question from this test 
        function disableQuestionFromTest() {
            var question_id = $("#to_disable_question_id").val();
            var test_id = $("#to_disable_test_id").val();

            var dataString = 'update=disableQuestion' + '&question_id=' + question_id + '&test_id=' + test_id;
            $.ajax({
                type: 'POST',
                data: dataString,
                url: base_url + '/tests/update_test_question_properties',
                success: function(data) {

                    $("#disableQuestionModal").modal('hide');

                    // Get the snackbar DIV
                    var snackbarDiv = document.getElementById("snackbar");

                    // Add the "show" class to DIV 
                    if (data.trim() == "DELETED") {
                        snackbarDiv.innerText = "Question deleted successfully from this test.";
                        // Hiding the question div
                        $("#question_display_card_" + question_id).slideUp();
                    } else {
                        snackbarDiv.innerText = data;
                    }
                    snackbarDiv.className = "show";

                    // After 3 seconds, remove the show class from DIV
                    setTimeout(function() {
                        snackbarDiv.className = snackbarDiv.className.replace("show", "");
                        window.location.reload(true);
                    }, 3000);
                }
            });
        }




        //To asynchronously load subjects in dropdown
        function loadAllSubjects(subjectName) {
            // console.log("loadAllSubjects " + subjectName + " - "+ chapterId)
            var dataString = 'update=loadSubjects' + '&subjectName=' + subjectName + '&instituteId=<?= $decrypted_instituteID ?>' + '&test_id=' + test_id;
            $.ajax({
                type: 'POST',
                data: dataString,
                url: base_url + '/tests/update_test_question_properties',
                success: function(data) {
                    console.log("loadAllSubjects data ", data);
                    $('.subject-dropdown').html(data);
                }
            });
        }




        //To asynchronously load chapters in dropdown
        function loadAllChapters(subjectName, chapterId) {
            // console.log("loadAllChapters " + subjectName + " - "+ chapterId)
            var dataString = 'update=loadChapters' + '&subjectName=' + subjectName + '&chapterId=' + chapterId + '&instituteId=<?= $decrypted_instituteID ?>' + '&test_id=' + test_id;
            $.ajax({
                type: 'POST',
                data: dataString,
                url: base_url + '/tests/update_test_question_properties',
                success: function(data) {
                    $('.chapter-dropdown').html(data);
                }
            });
        }




        //To asynchronously update the question subject from dropdown
        function updateSubject(question_id, updatedSectionInputElement) {
            var updatedSection = updatedSectionInputElement.value;
            var dataString = 'update=subjectId' + '&newSubjectId=' + encodeURIComponent(updatedSection) + '&question_id=' + question_id + '&test_id=' + test_id;
            $.ajax({
                type: 'POST',
                data: dataString,
                url: base_url + '/tests/update_test_question_properties',
                success: function(data) {
                    //Show the text format and hide the input 
                    $(updatedSectionInputElement).siblings().toggleClass('d-none');
                    $(updatedSectionInputElement).siblings().text(updatedSectionInputElement.options[updatedSectionInputElement.selectedIndex].text);
                    $(updatedSectionInputElement).toggleClass('d-none');

                    // Get the snackbar DIV
                    var snackbarDiv = document.getElementById("snackbar");

                    // Add the "show" class to DIV                        
                    snackbarDiv.innerText = data;
                    snackbarDiv.className = "show";

                    // After 3 seconds, remove the show class from DIV
                    setTimeout(function() {
                        snackbarDiv.className = snackbarDiv.className.replace("show", "");
                    }, 3000);
                }
            });
        }




        //To asynchronously update the question chapter from dropdown
        function updateChapter(question_id, updatedSectionInputElement) {
            var updatedSection = updatedSectionInputElement.value;
            var dataString = 'update=chapterId' + '&newChapterId=' + encodeURIComponent(updatedSection) + '&question_id=' + question_id + '&test_id=' + test_id;
            $.ajax({
                type: 'POST',
                data: dataString,
                url: base_url + '/tests/update_test_question_properties',
                success: function(data) {
                    //Show the text format and hide the input 
                    $(updatedSectionInputElement).siblings().toggleClass('d-none');
                    $(updatedSectionInputElement).siblings().text(updatedSectionInputElement.options[updatedSectionInputElement.selectedIndex].text);
                    $(updatedSectionInputElement).toggleClass('d-none');

                    // Get the snackbar DIV
                    var snackbarDiv = document.getElementById("snackbar");

                    // Add the "show" class to DIV                        
                    snackbarDiv.innerText = data;
                    snackbarDiv.className = "show";

                    // After 3 seconds, remove the show class from DIV
                    setTimeout(function() {
                        snackbarDiv.className = snackbarDiv.className.replace("show", "");
                    }, 3000);
                }
            });
        }



        //To asynchronously update the question difficulty level from dropdown
        function updateQuestionDifficulty(question_id, updatedDifficultyDropdownElement) {
            var updatedSection = updatedDifficultyDropdownElement.value;
            var dataString = 'update=level' + '&newDiffi=' + encodeURIComponent(updatedSection) + '&question_id=' + question_id + '&test_id=' + test_id;
            $.ajax({
                type: 'POST',
                data: dataString,
                url: base_url + '/tests/update_test_question_properties',
                success: function(data) {
                    //Show the text format and hide the input 
                    $(updatedDifficultyDropdownElement).siblings().toggleClass('d-none');
                    $(updatedDifficultyDropdownElement).siblings().text(updatedDifficultyDropdownElement.value);
                    $(updatedDifficultyDropdownElement).toggleClass('d-none');

                    // Get the snackbar DIV
                    var snackbarDiv = document.getElementById("snackbar");

                    // Add the "show" class to DIV                        
                    snackbarDiv.innerText = data;
                    snackbarDiv.className = "show";

                    // After 3 seconds, remove the show class from DIV
                    setTimeout(function() {
                        snackbarDiv.className = snackbarDiv.className.replace("show", "");
                    }, 3000);
                }
            });
        }
    </script>

    <script type="text/javascript" src="<?= base_url('assets/js/lazy.js'); ?>"></script>

    <script>
        function lazyLoad() {
            $('.lazy').Lazy({
                beforeLoad: function(element) {
                    // called before an elements gets handled
                    //console.log("Before loading");
                },
                afterLoad: function(element) {
                    // called after an element was successfully handled
                    //console.log("Image loaded properly");

                },
                onError: function(element) {
                    // called whenever an element could not be handled
                    console.log("ERROR in loading image");

                },
                onFinishedAll: function() {
                    // called once all elements was handled
                    //console.log("Images all loaded properly");
                }
            });
        }

        $(document).ready(function() {

            // console.log("Loading lazy ...");
            lazyLoad();

            //Initializing tooltip
            $('[data-bs-toggle="tooltip"]').tooltip();

            // https://stackoverflow.com/questions/33749223/javascript-scroll-current-views-element-id
            // To maintain the scroll position of the page even after reload
            $(window).on('scroll', function() {
                var Wscroll = $(this).scrollTop();
                $('.top_title_div').each(function() {
                    var ThisOffset = $(this).closest('.question_display_card').offset();
                    if (Wscroll > ThisOffset.top && Wscroll < ThisOffset.top + $(this).closest('.question_display_card').outerHeight(true)) {
                        // Saving which div is visible and saving its id
                        // console.log($(this).closest('.question_display_card').attr('id'));
                        sessionStorage.activeScrolledDiv = $(this).closest('.question_display_card').attr('id');
                    }
                });

                // When the user scrolls down 20px from the top of the document, show the button
                scrollFunction();
            });



            // console.log("sessionStorage.activeScrolledDiv", sessionStorage.activeScrolledDiv);
            // To maintain the scroll position of the page even after reload
            if (typeof sessionStorage.activeScrolledDiv !== "undefined") {
                // Scrolling the previously scrolled div into view
                $('html, body').animate({
                    scrollTop: $("#" + sessionStorage.activeScrolledDiv).offset().top
                }, 1000);
            }

        });
    </script>


    <!-- CKEditor 5  -->
    <script src="https://cdn.ckeditor.com/ckeditor5/20.0.0/decoupled-document/ckeditor.js"></script>

    <script>
        // Editor configuration.
        DecoupledEditor.defaultConfig = {
            toolbar: {
                items: [
                    'heading',
                    '|',
                    'fontFamily',
                    'fontSize',
                    '|',
                    'bold',
                    'italic',
                    'strikeThrough',
                    'underline',
                    'blockQuote',
                    'code',
                    'link',
                    '|',
                    'alignment',
                    '|',
                    'bulletedList',
                    'numberedList',
                    '|',
                    'highlight',
                    '|',
                    'imageUpload',
                    '|',
                    'insertTable',
                    '|',
                    'mediaEmbed',
                    '|',
                    'undo',
                    'redo'
                ]
            },
            image: {
                toolbar: [
                    'imageStyle:full',
                    'imageStyle:side',
                    '|',
                    'imageTextAlternative'
                ]
            },
            table: {
                contentToolbar: [
                    'tableColumn',
                    'tableRow',
                    'mergeTableCells'
                ]
            },
            colorButton_enable: true,
            // This value must be kept in sync with the language defined in webpack.config.js.
            language: 'en'
        };
    </script>
    <script>
        DecoupledEditor
            .create(document.querySelector('.document-editor__editable'), {})
            .then(editor => {
                const toolbarContainer = document.querySelector('.document-editor__toolbar');
                toolbarContainer.appendChild(editor.ui.view.toolbar.element);
                window.editor = editor;
            })
            .catch(err => {
                console.error(err);
            });


        // Assuming there is a <button id="submit">Submit</button> in your application.
        document.querySelector('#add_instruction_submit').addEventListener('click', () => {
            const editorData = editor.getData();
            document.getElementById("add_instruction").value = editorData;
        });
    </script>

    <script>
        /** To scroll to the question card on click of top indexed buttons */
        function scrollToQuestionCard(question_id) {
            // console.log(question_id);
            // Scrolling the previously scrolled div into view
            $('html, body').animate({
                scrollTop: $("#question_display_card_" + question_id).offset().top
            }, 0);
        }

        /**
         * Based on the window scroll position, show or hide the GO TO TOP button
         *
         * @return void
         * @author Hemant K <hemant.kulkarni@mattersoft.xyz>
         */
        function scrollFunction() {
            if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
                $("#btn-go-to-top").show();
            } else {
                $("#btn-go-to-top").hide();
                sessionStorage.activeScrolledDiv = null;
            }
        }

        /**
         * To scroll the window to the top
         * When the user clicks on the button, scroll to the top of the document
         *
         * @return void
         * @author Hemant K <hemant.kulkarni@mattersoft.xyz>
         */
        function topFunction() {
            //   document.body.scrollTop = 0;
            //   document.documentElement.scrollTop = 0;
            $('html, body').animate({
                scrollTop: $("body").offset().top
            }, 500);
        }
    </script>


    <script>
        var selectedQuestion = null;
        var testId = "<?= $test_sess_id; ?>";

        function replace_question_image(que_id) {
            selectedQuestion = que_id;
            clearCanvas(false, false);
            $("#imageReplaceModal").modal("show");
            $("#upload_solution_image").addClass('d-none');
            $("#upload_question_image").removeClass('d-none');
            $("#replace-image-modal-title").html("Question");
        }

        function replace_solution_image(que_id) {
            selectedQuestion = que_id;
            clearCanvas(false, false);
            $("#imageReplaceModal").modal("show");
            $("#upload_solution_image").removeClass('d-none');
            $("#upload_question_image").addClass('d-none');
            $("#replace-image-modal-title").html("Solution");
        }

        function upload_question_image() {
            console.log("File:", uploadedImage);
            if (uploadedImage == null || uploadedImage == undefined) {
                alert("Please select a file to upload!");
                return;
            }



            get_admin_token().then(function(result) {
                    var resp = JSON.parse(result);
                    if (resp != null && resp.status == 200 && resp.data != null && resp.data.admin_token != null) {

                        var dataObj = {
                            question: {
                                id: selectedQuestion,
                                type: "Q",
                                questionImageUrl: uploadedImage
                            }
                        };
                        dataString = JSON.stringify(dataObj);

                        Snackbar.show({
                            pos: 'top-center',
                            text: 'Uploading image... please wait'
                        });

                        $.ajax({
                            type: 'POST',
                            data: dataString,
                            contentType: "application/json",
                            url: rootAdmin + 'uploadQuestionImageBase64',
                            beforeSend: function(request) {
                                request.setRequestHeader("AuthToken", resp.data.admin_token);
                            },
                            success: function(response) {
                                if (response.status.statusCode == 200) {
                                    Snackbar.show({
                                        pos: 'top-center',
                                        text: 'Question Image updated successfully'
                                    });

                                    window.location.reload();
                                } else {
                                    console.log(response.status.responseText);
                                    Snackbar.show({
                                        pos: 'top-center',
                                        text: response.status.responseText
                                    });
                                }
                            }
                        });
                        selectedQuestion = null;
                        $("#imageReplaceModal").modal("hide");
                    } else {
                        alert("Some error authenticating your request. Please clear your browser cache and try again.");
                    }
                })
                .catch(function(error) {
                    // An error occurred
                    console.log("Error: ", error);
                });

        }


        function upload_solution_image() {
            console.log("File:", uploadedImage);
            if (uploadedImage == null || uploadedImage == undefined) {
                alert("Please select a file to upload!");
                return;
            }



            get_admin_token().then(function(result) {
                    var resp = JSON.parse(result);
                    if (resp != null && resp.status == 200 && resp.data != null && resp.data.admin_token != null) {

                        var dataObj = {
                            question: {
                                id: selectedQuestion,
                                type: "S",
                                questionImageUrl: uploadedImage
                            }
                        };
                        dataString = JSON.stringify(dataObj);

                        Snackbar.show({
                            pos: 'top-center',
                            text: 'Uploading image... please wait'
                        });

                        $.ajax({
                            type: 'POST',
                            data: dataString,
                            contentType: "application/json",
                            url: rootAdmin + 'uploadQuestionImageBase64',
                            success: function(response) {
                                if (response.status.statusCode == 200) {
                                    Snackbar.show({
                                        pos: 'top-center',
                                        text: 'Solution image updated successfully'
                                    });

                                    window.location.reload();
                                } else {
                                    console.log(response.status.responseText);
                                    Snackbar.show({
                                        pos: 'top-center',
                                        text: response.status.responseText
                                    });
                                }
                            }
                        });
                        selectedQuestion = null;
                        $("#imageReplaceModal").modal("hide");
                    } else {
                        alert("Some error authenticating your request. Please clear your browser cache and try again.");
                    }
                })
                .catch(function(error) {
                    // An error occurred
                    console.log("Error: ", error);
                });


        }
    </script>

    <script>
        // Onchange Select question checkbox
        $('.bulk_questions_select').change(function() {
            selected_checkboxes_count();
        });

        // To show selected checkboxes count
        function selected_checkboxes_count() {
            var active_questions_selected = 0;
            $(".bulk_questions_select").each(function() {
                if (this.checked) {
                    active_questions_selected++;
                }

                $("#active-questions-checkboxes-count-block #active_questions_checkboxes_count").html(active_questions_selected);
            });

            if (active_questions_selected === 0) {
                $("#active-questions-checkboxes-count-block").addClass("d-none");
            } else {
                $("#active-questions-checkboxes-count-block").removeClass("d-none");
            }
            console.log("Selected Checkboxes:", active_questions_selected);
        }

        // Delete Multiple Question Modal
        function show_bulk_questions_delete_modal() {

            var checkboxes = document.getElementsByClassName('bulk_questions_select');
            var checkedOne = Array.prototype.slice.call(checkboxes).some(x => x.checked);
            if (!checkedOne) {
                alert("Select aleast one student.");
            } else {

                // Check selected checkboxes
                var checkedValue = [];
                var inputElements = document.getElementsByClassName('bulk_questions_select');

                var inputElementsLength = 0;

                for (var i = 0; inputElements[i]; ++i) {
                    if (inputElements[i].checked) {
                        checkedValue.push(inputElements[i].value);
                        inputElementsLength = inputElementsLength + 1;
                    }
                }
                $('#bulk_delete_question_ids').val(checkedValue.toString());
                $("#delete_bulk_message").html("Are you sure, you want to delete " + inputElementsLength + " questions? They will NOT be able to recover.");
                // Open Modal
                $("#delete_bulk_questions_modal").modal('show');
            }
        }
    </script>



    <script>
        $("#add_test_pdf_paper").submit(function(evt) {

            var files = document.getElementById('pdf_file').files[0];
            if (files == null || files == undefined) {
                alert("Please select a file to upload!");
                return;
            }
            var fd = new FormData();
            fd.append("file", files);

            $(".submitBtn").attr("disabled", true);
            evt.preventDefault();


            var request = {
                requestType: 'OFFLINE_PDF_UPLOAD',
                test: {
                    id: test_id
                }
            }

            fd.append("request", JSON.stringify(request));
            get_admin_token().then(function(result) {
                var resp = JSON.parse(result);
                if (resp != null && resp.status == 200 && resp.data != null && resp.data.admin_token != null) {

                    token = resp.data.admin_token;
                    $.ajax({
                        url: rootAdmin + "uploadTestPdf",
                        beforeSend: function(request) {
                            request.setRequestHeader("AuthToken", token);
                        },
                        type: "POST",
                        data: fd,
                        success: function(msg) {
                            // console.log("Response", msg);
                            if (msg != null && msg.status != null && msg.status.statusCode == 200) {
                                Snackbar.show({
                                    pos: 'top-center',
                                    text: 'Test paper uploaded successfully'
                                });
                                window.location = base_url + "/tests/update_test_questions/" + encrypted_test_id;
                            } else {
                                $(".submitBtn").attr("disabled", false);
                                $("#uploadError").html("Some error occured in uploaded test paper");
                            }

                        },
                        error: function(XMLHttpRequest, textStatus, errorThrown) {
                            $(".submitBtn").attr("disabled", false);
                            ("#uploadError").html("Error in service call");
                        },
                        cache: false,
                        contentType: false,
                        processData: false
                    });
                }
            });
        });
    </script>