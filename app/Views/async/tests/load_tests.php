<?php
$sr_no = 1;
$current_date = changeDateTimezone(date('d-m-Y H:i:s'));
$todays_date = changeDateTimezone(date('d-m-Y H:i:s'));
$htmlData = '';

foreach ($exams_data as $row) :

    $test_id = $row['test_id'];
    $encrypted_test_id = encrypt_string($row['test_id']);
    $test_name = $row['test_name'];
    $test_ui = $row['test_ui'];
    $test_no_of_questions = $row['no_of_questions'];
    $test_total_marks = $row['total_marks'];
    $test_duration = $row['duration'];
    $test_status = $row['status'];
    $classroom_name = $row['package_list'];
    $questionsAddedCount = $row['questionsAdded'];
    $totalQuestionsInTest = $row['no_of_questions'];
    $exam_conduction = $row['exam_conduction'];

    $test_show_results = $row['show_result'];
    $paper_pdf_url = $row['paper_pdf_url'];
    $paper_pdf_url = str_replace("/var/www/edofoxlatur.com/public_html", "..", $paper_pdf_url);
    $solutions_pdf_url = $row['solutions_pdf_url'];
    $solutions_pdf_url = str_replace("/var/www/edofoxlatur.com/public_html", "..", $solutions_pdf_url);
    $test_duration_in_hours_min = gmdate("H \h\\r:i \m\i\\n", (int)$test_duration);
    $start_date = $row['start_date'];
    $formatted_test_start_date = changeDateTimezone(date("d M Y, h:i A", strtotime($start_date)), "d M Y, h:i A");
    $end_date = $row['end_date'];
    $formatted_test_end_date = changeDateTimezone(date("d M Y, h:i A", strtotime($end_date)), "d M Y, h:i A");

    $test_start_date_only_date = changeDateTimezone(date("d-m-Y H:i:s", strtotime($start_date)), "d-m-Y H:i:s");
    $active = null;
    $non_active_test_styling = 'color: #ff0000 !important;';
    $non_active_test_row_style = 'background-color: #f5f5f5 !important;';
    if (strtotime($todays_date) >= strtotime($test_start_date_only_date) && strtotime($todays_date) <= strtotime($end_date)) {
        $active = "(ACTIVE)";
        $non_active_test_row_style = 'background-color: #fff !important;';
        $non_active_test_styling = null;
    }

    if ($questionsAddedCount == null) {
        $questionsAddedCount = 0;
    }

    $textStylingForNoOfQuestions = null;
    if ($questionsAddedCount !== $totalQuestionsInTest) {
        $textStylingForNoOfQuestions = 'color: #FD1D1D; font-weight: bold;';
    } else {
        $textStylingForNoOfQuestions = 'color: #25D366; font-weight: bold;';
    }

    $test_random_questions = $row['random_questions'];
    $random_questions_tooltip = "";
    if ($test_random_questions != "N") {
        $active_icon_class = "active-icon";
        $random_questions_tooltip = "Questions in this test will be shuffled";
    } else {
        $active_icon_class = "";
        $random_questions_tooltip = "Questions shuffling is not enabled";
    }

    $test_random_questions_html = "";
    if (in_array("manage_tests", session()->get('perms')) or in_array("all_perms", session()->get('perms'))) :
        $test_random_questions_html = "<div class='test-properties $active_icon_class' onclick=toggleTestValue(this,'shuffleQuestions',$test_id)><i class='fas fa-random' aria-hidden='true' data-bs-toggle='tooltip' title='$random_questions_tooltip'></i></div>";
    endif;

    $video_proctoring_check = $row['video_proctoring'];
    $image_proctoring_check = $row['img_proctoring'];

    $show_result = $row['show_result'];
    $show_result_tooltip = "";
    if ($show_result != "N") {
        $active_icon_class = "active-icon";
        $show_result_tooltip = "Showing the result immediately after student submits the test";
    } else {
        $active_icon_class = "";
        $show_result_tooltip = "NOT Showing the result immediately after student submits the test";
    }
    $show_result_html = "";
    if (in_array("manage_tests", session()->get('perms')) or in_array("all_perms", session()->get('perms'))) :
        $show_result_html = "<div class='test-properties $active_icon_class' onclick=toggleTestValue(this,'showResult',$test_id)><i class='far fa-calendar-check' aria-hidden='true' data-bs-toggle='tooltip' title='$show_result_tooltip'></i></div>";
    endif;

    $time_constraint = $row['time_constraint'];
    $time_constraint_tooltip = "";
    if ($time_constraint != "0") {
        $active_icon_class = "active-icon";
        $time_constraint_tooltip = "Test timing is strict and aligned with test start time";
    } else {
        $active_icon_class = "";
        $time_constraint_tooltip = "Test timing is not aligned with start time and is flexible for students";
    }

    $time_constraint_html = "";
    if (in_array("manage_tests", session()->get('perms')) or in_array("all_perms", session()->get('perms'))) :
        $time_constraint_html = "<div class='test-properties $active_icon_class' onclick=toggleTestValue(this,'testTimeConstraint',$test_id) data-bs-toggle='tooltip' title='$time_constraint_tooltip'><i class='far fa-clock' aria-hidden='true' ></i> <i class='fas fa-play' aria-hidden='true' ></i></div>";
    endif;

    $student_time_constraint = $row['student_time_constraint'];
    $student_time_constraint_tooltip = "";
    if ($student_time_constraint != "0") {
        $active_icon_class = "active-icon";
        $student_time_constraint_tooltip = "Test time is aligned with student's test start time";
    } else {
        $active_icon_class = "";
        $student_time_constraint_tooltip = "Test time is flexible and not aligned with student's start time";
    }
    $student_time_constraint_html = "";
    if (in_array("manage_tests", session()->get('perms')) or in_array("all_perms", session()->get('perms'))) :
        $student_time_constraint_html = "<div class='test-properties $active_icon_class' onclick=toggleTestValue(this,'studentTimeConstraint',$test_id) data-bs-toggle='tooltip' title='$student_time_constraint_tooltip'><i class='far fa-clock' aria-hidden='true'></i> <i class='far fa-user-circle' aria-hidden='true' ></i></div>";
    endif;

    $show_question_paper = $row['show_question_paper'];
    $show_question_paper_tooltip = "";
    if ($show_question_paper == "Y") {
        $active_icon_class = "active-icon";
        $show_question_paper_tooltip = "Showing full question paper after test submit";
    } else {
        $active_icon_class = "";
        $show_question_paper_tooltip = "Not Showing question paper after test submit";
    }

    $show_question_paper_html = "";
    if (in_array("manage_tests", session()->get('perms')) or in_array("all_perms", session()->get('perms'))) :
        $show_question_paper_html = "<div class='test-properties $active_icon_class' data-bs-toggle='tooltip' title='$show_question_paper_tooltip' onclick=toggleTestValue(this,'showQPaper',$test_id)><i class='fas fa-file' aria-hidden='true' ></i></div>";
    endif;

    $pause_timeout_seconds = $row['pause_timeout_seconds'];
    $pause_timeout_seconds_tooltip = "";
    if (isset($pause_timeout_seconds)) {
        $active_icon_class = "active-icon";
        $pause_timeout_seconds_tooltip = "Seconds allowed outside the test window is set to " . $pause_timeout_seconds . " seconds";
    } else {
        $active_icon_class = "";
        $pause_timeout_seconds_tooltip = "Pause timeout is not set for this set";
    }
    $pause_timeout_seconds_html = "";
    if (in_array("manage_tests", session()->get('perms')) or in_array("all_perms", session()->get('perms'))) :
        $pause_timeout_seconds_html = "<div class='test-properties $active_icon_class' data-bs-toggle='tooltip' title='$pause_timeout_seconds_tooltip' onclick='updateTest($test_id);'><b>$pause_timeout_seconds</b> <i class='fas fa-sign-out-alt' aria-hidden='true'></i></div>";
    endif;

    $max_allowed_test_starts = $row['max_allowed_test_starts'];
    $max_allowed_test_starts_tooltip = "";
    if (isset($max_allowed_test_starts)) {
        $active_icon_class = "active-icon";
        $max_allowed_test_starts_tooltip = "Number of times test can be started is set to " . $max_allowed_test_starts;
    } else {
        $active_icon_class = "";
        $max_allowed_test_starts_tooltip = "No restriction on number of times test can be logged into";
    }

    $max_allowed_test_starts_html = "";
    if (in_array("manage_tests", session()->get('perms')) or in_array("all_perms", session()->get('perms'))) :
        $max_allowed_test_starts_html = "<div class='test-properties $active_icon_class' data-bs-toggle='tooltip' title='$max_allowed_test_starts_tooltip' onclick='updateTest($test_id);'><b>$max_allowed_test_starts</b> <i class='fas fa-step-forward' aria-hidden='true'></i></div>";
    endif;

    $offline_conduction = $row['offline_conduction'];
    $offline_conduction_tooltip = "";
    if ($offline_conduction != "0") {
        $active_icon_class = "active-icon";
        $offline_conduction_tooltip = "Test will be conducted in optimal network mode";
    } else {
        $active_icon_class = "";
        $offline_conduction_tooltip = "Optimal network mode is off";
    }

    $offline_conduction_html = "";
    if (in_array("manage_tests", session()->get('perms')) or in_array("all_perms", session()->get('perms'))) :
        $offline_conduction_html = "<div class='test-properties $active_icon_class' onclick=toggleTestValue(this,'offlineConduction',$test_id)><i class='fas fa-wifi' aria-hidden='true' data-bs-toggle='tooltip' title='$offline_conduction_tooltip'></i></div>";
    endif;

    $get_students_geolocation = $row['accept_location'];
    $get_students_geolocation_toolip = "";
    if ($get_students_geolocation == "1") {
        $active_icon_class = "active-icon";
        $get_students_geolocation_toolip = "Accepting students location before starting the test";
    } else {
        $active_icon_class = "";
        $get_students_geolocation_toolip = "Not accepting students location";
    }
    $get_students_geolocation_html = "";
    if (in_array("manage_tests", session()->get('perms')) or in_array("all_perms", session()->get('perms'))) :
        $get_students_geolocation_html = "<div class='test-properties $active_icon_class' onclick=toggleTestValue(this,'getGeoLocation',$test_id)><i class='fas fa-map-marker-alt' aria-hidden='true' data-bs-toggle='tooltip' title='$get_students_geolocation_toolip'></i></div>";
    endif;

    // Image Proctoring Icon
    $img_proctoring_val = $row['img_proctoring'];
    $img_proctoring_tooltip = "";
    if (isset($img_proctoring_val) && $img_proctoring_val == 1) {
        $active_icon_class = "active-icon";
        $img_proctoring_tooltip = "Image Proctoring Enabled";
    } else {
        $active_icon_class = "";
        $img_proctoring_tooltip = "Image Proctoring Disabled";
    }
    $img_proctoring_html = "";
    if (in_array("manage_tests", session()->get('perms')) or in_array("all_perms", session()->get('perms'))) :
        $img_proctoring_html = "<div class='test-properties $active_icon_class' data-toggle='tooltip' title='$img_proctoring_tooltip' onclick=toggleTestValue(this,'getImgProctoringValue',$test_id) ><i class='fas fa-camera' aria-hidden='true'></i></div>";
    endif;


    // Video Proctoring Icon
    $video_proctoring_val = $row['video_proctoring'];
    $video_proctoring_tooltip = "";
    if (isset($video_proctoring_val) && $video_proctoring_val == 1) {
        $active_icon_class = "active-icon";
        $video_proctoring_tooltip = "Video Proctoring Enabled";
    } else {
        $active_icon_class = "";
        $video_proctoring_tooltip = "Video Proctoring Disabled";
    }
    $video_proctoring_html = "";
    if (in_array("manage_tests", session()->get('perms')) or in_array("all_perms", session()->get('perms'))) :
        $video_proctoring_html = "<div class='test-properties $active_icon_class' data-toggle='tooltip' title='$video_proctoring_tooltip' onclick=toggleTestValue(this,'getVideoProctoringValue',$test_id)><i class='fas fa-video'></i></div>";
    endif;

    $editTestSection = "";
    $otherOptions = "";
    $addQuestionSection = "";
    $solutionSection = "";
    $resultSection = "";



    $htmlData .= "<tr style='$non_active_test_row_style'>";
    $htmlData .= "<td> <p style='font-weight: bold;color: #5b51d8; $non_active_test_styling;'>$sr_no. $test_name $active </p>

    <div class='small'>

    <div><label>$formatted_test_start_date <i class='fas fa-arrows-alt-h' aria-hidden='true'></i> $formatted_test_end_date</label></div>
    <div>Duration: <label title='$test_duration Sec'> $test_duration_in_hours_min</label><i class='far fa-circle text-muted' aria-hidden='true'></i> Total Marks: <label> $test_total_marks </label> <i class='far fa-circle text-muted' aria-hidden='true'></i> Questions Added: <label style='$textStylingForNoOfQuestions'> $questionsAddedCount/$totalQuestionsInTest</label> <i class='far fa-circle' aria-hidden='true'></i> Classroom: <label> $classroom_name</label> </div>
    </div>

    </td>";
    if (in_array('manage_tests', $perms) || in_array("all_perms", session()->get('perms'))) :

        $addQuestionsHtml = "<li><a data-bs-toggle='tooltip' class='dropdown-item' href='" . base_url('tests/add_test_img_questions/' . $encrypted_test_id) . "'> Add Questions in Test from images </a></li><li><a data-bs-toggle='tooltip' class='dropdown-item' href='" . base_url('tests/generate_chapter_wise_test/' . $encrypted_test_id) . "'> Auto-create exam chapter-wise from question bank </a></li><li><a data-bs-toggle='tooltip' class='dropdown-item' href='" . base_url('tests/parse_pdf/' . $encrypted_test_id) . "'> Import PDF question paper </a></li><li><a data-bs-toggle='tooltip' class='dropdown-item' href='" . base_url('tests/question_bank/' . $encrypted_test_id) . "'> Pick questions from question bank </a></li>";
        if ($exam_conduction == "Offline") {
            $addQuestionsHtml = $addQuestionsHtml . "<li><a data-bs-toggle='tooltip' class='dropdown-item' href='" . base_url('tests/update_exam_pdf_paper/' . $encrypted_test_id) . "'> Update Offline Test Paper </a></li>";
        }
        $addQuestionsHtml = $addQuestionsHtml . "<li><hr class='dropdown-divider'></li>";

        $addQuestionSectionencode =  htmlspecialchars($addQuestionsHtml);

        $addQuestionSectiondecode = htmlspecialchars_decode($addQuestionSectionencode);
        $addQuestionSection = $addQuestionSectiondecode;

    endif;

    

    if ((in_array('manage_tests', $perms)) || in_array("all_perms", session()->get('perms'))) :


        $solutionSection_encode =  htmlspecialchars("<li><a data-bs-toggle='tooltip' class='dropdown-item' href='" . base_url('tests/add_answer_key/' . $encrypted_test_id) . "'> Add Answer Key </a></li>");

        $solutionSection_decode = htmlspecialchars_decode($solutionSection_encode);
        $solutionSection .= $solutionSection_decode;

    endif;

    $solutionSection_encode2 =  htmlspecialchars("<li><a data-bs-toggle='tooltip' class='dropdown-item' href='" . base_url('tests/print_answer_key/' . $encrypted_test_id) . "'> Print Answer Key </a></li><li><a data-bs-toggle='tooltip' class='dropdown-item' href='" . base_url('tests/add_test_solutions/' . $encrypted_test_id) . "'> Add Solutions </a></li><li><hr class='dropdown-divider'></li>");

    $solutionSection_decode2 = htmlspecialchars_decode($solutionSection_encode2);
    $solutionSection .= $solutionSection_decode2;


    if ((in_array('manage_tests', $perms)) ||  in_array("all_perms", session()->get('perms'))) :

        $editTestSectionencode =  htmlspecialchars("<li><a data-bs-toggle='tooltip' class='dropdown-item' href='" . base_url('tests/update_test_questions/' . $encrypted_test_id) . "'> Check/Update Questions in Test </a></li>");
        $editTestSectiondecode = htmlspecialchars_decode($editTestSectionencode);
        $editTestSection .= $editTestSectiondecode;

        $editTestSection .= '<li><hr class="dropdown-divider"></li>';
    endif;



    if (in_array("manage_tests", session()->get('perms')) or in_array("all_perms", session()->get('perms'))) :

        $update_test_option_encode =  htmlspecialchars("<li><a data-bs-toggle='tooltip' class='dropdown-item' href='" . base_url('tests/update_test_details/' . $encrypted_test_id) . "'> Update Test Properties</a></li><li><hr class='dropdown-divider'></li>");

        $update_test_option_decode = htmlspecialchars_decode($update_test_option_encode);
        $editTestSection .= $update_test_option_decode;

    endif;


    $analysisSection = "";
    if ($row['exam_conduction'] == "Offline" && !empty($row['paper_pdf_url'])) {
        $analysisSection = "";
    } else {
        $analysisSectionencode =  htmlspecialchars("<li><hr class='dropdown-divider'></li><li><a data-bs-toggle='tooltip' class='dropdown-item' href='" . base_url('tests/realtime_overview/' . $encrypted_test_id) . "'> Realtime Test Overview </a></li><li><hr class='dropdown-divider'></li>");
        $analysisSectiondecode = htmlspecialchars_decode($analysisSectionencode);
        $analysisSection = $analysisSectiondecode;
    }


    if ($test_ui == "DESCRIPTIVE") {


        $resultSectionencode =  htmlspecialchars("<li><a data-bs-toggle='tooltip' class='dropdown-item' href='" . base_url('tests/evaluate_subjective_answers/' . $encrypted_test_id) . "'> Evaluate Subjective Answers </a></li><li><hr class='dropdown-divider'></li>");
        $resultSectiondecode = htmlspecialchars_decode($resultSectionencode);
        $resultSection = $resultSectiondecode;
    }

    if (in_array("all_perms", session()->get('perms'))) :

        $resultSectionencode1 =  htmlspecialchars("<li><a data-bs-toggle='tooltip' class='dropdown-item' target='_blank' href='" . base_url('tests/revaluate_result/' . $encrypted_test_id) . "'> Generate Result </a></li>");
        $resultSectiondecode1 = htmlspecialchars_decode($resultSectionencode1);
        $resultSection .= $resultSectiondecode1;


    endif;

    if (in_array('view_result', $perms) || in_array("all_perms", session()->get('perms'))) :

        $resultSectionencode2 =  htmlspecialchars("<li><a data-bs-toggle='tooltip' class='dropdown-item' target='_blank' href='" . base_url('tests/show_test_result/1/' . $encrypted_test_id) . "'> Show Test Result/Analysis </a></li>");
        $resultSectiondecode2 = htmlspecialchars_decode($resultSectionencode2);
        $resultSection .= $resultSectiondecode2;

    endif;

    // OMR Evalution Option
    $OmrTestEvalution = "";
    if ($exam_conduction == "Offline" && (in_array("manage_tests", session()->get('perms')) or in_array("all_perms", session()->get('perms')))) {
        $OmrTestEvalutionEncode =  htmlspecialchars("<li><a data-bs-toggle='tooltip' class='dropdown-item' href='" . base_url('tests/omr_test_evalution/' . $encrypted_test_id) . "'> OMR Evalution </a></li>");
        $OmrTestEvalution = htmlspecialchars_decode($OmrTestEvalutionEncode);
    }
    $resultSection .= $OmrTestEvalution;

    // Proctoring Analysis Condition if test_ui = 'PROCTORING' then we will show button
    $proctoringResultSection = "";

    if ($image_proctoring_check == '1') :
        $proctoringResultSection_encode =  htmlspecialchars("<li><a data-bs-toggle='tooltip' class='dropdown-item' href='" . base_url('tests/proctoring_analysis/' . $encrypted_test_id . '/1') . "'> Proctoring Analysis </a></li><li><hr class='dropdown-divider'></li>");
        $proctoringResultSection_decode = htmlspecialchars_decode($proctoringResultSection_encode);
        $proctoringResultSection = $proctoringResultSection_decode;
    endif;


    // Video Proctoring 
    $video_proctoring_section = "";
    if ($video_proctoring_check == '1') :
        $video_proctoring_section_encode =  htmlspecialchars("<li><a data-bs-toggle='tooltip' class='dropdown-item' href='" . base_url('tests/video_proctoring_section/' . $encrypted_test_id . '/1') . "'> Video Proctoring Section </a></li>");
        $video_proctoring_section = htmlspecialchars_decode($video_proctoring_section_encode);
    endif;

    if ($row['exam_conduction'] == "Offline" && !empty($row['paper_pdf_url'])) {

        $printOptions_encode =  htmlspecialchars("<li><a data-bs-toggle='tooltip' class='dropdown-item' href='" . $row['paper_pdf_url'] . "' target='_blank'> Download Test Paper </a></li>");
        $printOptions_decode = htmlspecialchars_decode($printOptions_encode);
        $printOptions = $printOptions_decode;
    } else {

        $printOptions_encode =  htmlspecialchars("<li><a data-bs-toggle='tooltip' class='dropdown-item' href='" . base_url('tests/print_test_paper/' . $encrypted_test_id . '/1') . "'> Print Test Paper with Solutions </a></li>");
        $printOptions_decode = htmlspecialchars_decode($printOptions_encode);
        $printOptions = $printOptions_decode;

        $printOptionswithoutsolutions_encode =  htmlspecialchars("<li><a data-bs-toggle='tooltip' class='dropdown-item' href='" . base_url('tests/print_test_paper/' . $encrypted_test_id . '/0') . "'> Print Test Paper without Solutions </a></li>");
        $printOptionswithoutsolutions_decode = htmlspecialchars_decode($printOptionswithoutsolutions_encode);
        $printOptions .= $printOptionswithoutsolutions_decode;
    }


    if (in_array("manage_tests", session()->get('perms')) or in_array("all_perms", session()->get('perms'))) :

        $delete_test_option_encode =  htmlspecialchars("<li><a data-bs-toggle='tooltip' title='Update'  class='dropdown-item' onclick=" . "show_edit_modal('modal_div','delete_test_modal','tests/delete_test_modal/" . $encrypted_test_id . "');" . "> Delete Test </a></li>");
        $delete_test_option_decode = htmlspecialchars_decode($delete_test_option_encode);
        $deleteTestOption = $delete_test_option_decode;

        $clone_test_option_encode =  htmlspecialchars("<li><hr class='dropdown-divider'></li><li><a data-bs-toggle='tooltip' title='Update'  class='dropdown-item' onclick=" . "show_edit_modal('modal_div','clone_test_modal','tests/clone_test_modal/" . $encrypted_test_id . "');" . "> Clone test into another </a></li><li><hr class='dropdown-divider'></li>");
        $clone_test_option_decode = htmlspecialchars_decode($clone_test_option_encode);
        $clone_test_option = $clone_test_option_decode;

        $template_save_option = "";
        if ($questionsAddedCount == $totalQuestionsInTest) {
            $template_save_option_encode =  htmlspecialchars("<li><a data-bs-toggle='tooltip' title='Update'  class='dropdown-item' onclick=" . "show_edit_modal('modal_div','add_template_modal','/testTemplates/add_template/" . $encrypted_test_id . "');" . "> Save as template </a></li>");
            $template_save_option_decode = htmlspecialchars_decode($template_save_option_encode);
            $template_save_option = $template_save_option_decode;
        }



        $otherOptions = $clone_test_option . '<li>' . $template_save_option . '</li>' . '<li>' . $deleteTestOption . '</li>';

    endif;




    $htmlData .= '<td>
        <div style="display: flex; justify-content: flex-end;">
        ' . $show_result_html . '
        ' . $show_question_paper_html . '
        ' . $test_random_questions_html . '
        ' . $time_constraint_html . '
        ' . $student_time_constraint_html . '
        ' . $pause_timeout_seconds_html . '
        ' . $max_allowed_test_starts_html . '
        ' . $offline_conduction_html . '
        ' . $get_students_geolocation_html . '
        ' . $img_proctoring_html . '
        ' . $video_proctoring_html . '
            <div class="dropdown">
                <button class="btn btn-default dropdown-toggle more_option_button" type="button" id="testDropdownMenu" data-bs-toggle="dropdown"  data-bs-auto-close="outside" aria-expanded="false">
                    <i class="fas fa-ellipsis-h" aria-hidden="true"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="testDropdownMenu">
                '
        . $addQuestionSection
        . $editTestSection
        . $solutionSection
        . $resultSection
        . $proctoringResultSection
        . $video_proctoring_section
        . $analysisSection
        . $printOptions
        . $otherOptions .

        '
                </ul>
            </div>
        </div>
    </td>';
    $sr_no++;
    $htmlData .= "</tr>";
endforeach;
echo $htmlData;
