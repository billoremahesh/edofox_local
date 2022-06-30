<?php
$question_count = 1;

$parsed_data = array();
$subject_string = "";
if (!empty($questions_list)) :
    foreach ($questions_list as $row) :
        $question_id = $row['id'];
        $option1 = $row['option1'];
        $option2 = $row['option2'];
        $option3 = $row['option3'];
        $option4 = $row['option4'];
        $correct_answer = $row['correct_answer'];

        // Showing alternate answer if it is added
        $alt_answer = "";
        $alternate_answer_string = "";
        if (isset($row['alt_answer'])) {
            $alt_answer = $row['alt_answer'];

            if (isset($row["$alt_answer"])) {
                $alternate_answer_string = $row["$alt_answer"];
                $alternate_answer_string = " or " . str_replace(")", "", $alternate_answer_string);
            } else {
                $alternate_answer_string = " or " . $alt_answer;
            }
        }
        $question_type = $row['question_type'];
        $question_number_in_paper = $row['question_number'];
        $subject = $row['subject'];

        if ($subject_string != $subject) {
            $subject_string = $subject;

            if (!isset($parsed_data[$subject_string])) {
                $parsed_data[$subject_string] = array();
            }
        }

        if ($question_type == NULL) {
            $question_type = "SINGLE";
        }

        $each_question_answer = array();
        $each_question_answer["question_number"] = $question_number_in_paper;
        $each_question_answer["alternate_answer"] = $alternate_answer_string;

        $each_question_answer["answer"] = "";
        if ($question_type === 'SINGLE') {
            if (!empty($correct_answer)) {
                if (isset($row["$correct_answer"])) {
                    $answer_key_string = $row["$correct_answer"];
                    $answer_key_string = str_replace(")", "", $answer_key_string);
                    // echo $answer_key_string;
                    $each_question_answer["answer"] = $answer_key_string;
                } else {
                    $each_question_answer["answer"] = $correct_answer;
                    // echo $correct_answer;
                }
            }
        } elseif ($question_type === 'MULTIPLE' || $question_type === 'PASSAGE_MULTIPLE') {
            $correct_answer_array = explode(",", $correct_answer);
            $answer_key_array = array();
            foreach ($correct_answer_array as $each_correct_answer) {
                if (isset($row["$each_correct_answer"])) {
                    $answer_key_string = $row["$each_correct_answer"];
                    array_push($answer_key_array, str_replace(")", "", $answer_key_string));
                } else {
                    array_push($answer_key_array, $each_correct_answer);
                }
            }
            // echo implode(",", $answer_key_array);
            $each_question_answer["answer"] = implode(",", $answer_key_array);
        } else {
            $each_question_answer["answer"] = $correct_answer;
            // echo "<u>$correct_answer</u>";
        }

        array_push($parsed_data[$subject_string], $each_question_answer);
        $question_count++;
    endforeach;

    foreach ($parsed_data as $subject_name_key => $subject_wise_data) :
?>
        <div class="text-center"><b><?= $subject_name_key ?></b></div>
        <table class="table table-bordered table-condensed">
            <tbody>
                <?php
                foreach ($subject_wise_data as $question_count_key => $answer_data) :
                    if ($question_count_key == 0 || ($question_count_key % $columns == 0)) {
                        echo "<tr>";
                    }
                ?>
                    <td>
                        <?= $answer_data['question_number'] ?>: <b><?= $answer_data['answer'] ?> <?= $answer_data['alternate_answer'] ?></b>
                    </td>
                <?php
                    if (($question_count_key + 1) % $columns == 0) {
                        echo "</tr>";
                    }
                endforeach;
                ?>
            </tbody>
        </table>
<?php
    endforeach;
endif;
?>

<style>
    @media print {
        @page {
            size: A4;
            margin: 1cm;
            color: black;
            font-family: Karla, Roboto, sans-serif !important;
            margin-top: 0;
            margin-bottom: 0;
        }

        body {
            padding-top: 72px;
            padding-bottom: 72px;
        }
    }
</style>