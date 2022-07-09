<style>
    mjx-math {
        white-space: pre-wrap !important;
    }
</style>
<?php
if ($columns == 1) {
    $columns_class = "col-12";
} elseif ($columns == 2) {
    $columns_class = "col-6";
}

$height_per_page = "";
$row_height = "";
$img_height = "";

if (!empty($no_of_que_per_page)) {
    $height_per_page = "297";
    $row_height = round($height_per_page / ($no_of_que_per_page / $columns), 0) . "mm";
    $img_height = round(($height_per_page / ($no_of_que_per_page / $columns)) - 10, 0) . "mm";
}
/**
 * For Test Data
 */

$data1 = array(
    "id" => $test_id
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
// echo $JsonString;
$objTestData = json_decode($objTestString, true);
// var_dump($objTestData);

$test_name = $objTestData["test"]["name"];
$totalMarks = $objTestData["test"]["totalMarks"];
$duration = $objTestData["test"]["duration"];
$noOfQuestions = $objTestData["test"]["noOfQuestions"];

if ($duration < 3600) {
    $test_duration_in_hours_min = gmdate("i \m\i\\n", (int)$duration);
} else {
    $test_duration_in_hours_min = gmdate("H \h\\r:i \m\i\\n", (int)$duration);
}
?>


<div class="text-center">
    <button class="print-button my-1" onclick="printContent('print_div')">Print Paper</button>
    <p class="small text-muted">Please click print only after all the questions are loaded properly.</p>
</div>

<div class="card shadow p-4">
    <div id="print_div">
        <div id="print_content">
            <div id="print_header">
                <div class="row">
                    <div class="col-12">
                        <div style="text-align: center;position:relative;height:100px;">
                            <h4><b><?= $instituteName ?></b></h4>
                            <h5><?= $test_name ?></h5>

                            <div style="text-align: center;position:absolute;top:4px;">
                                <?php if (isset($_SESSION['logo_path'])) {
                                    $logoUrl = $_SESSION['logo_path'];
                                    $logo_prefix = "../";
                                    if (strpos($logoUrl, 'http') >= 0) {
                                        $logo_prefix = "";
                                    }
                                ?>
                                    <img alt="logo-img" width="80" height="80" class="img-fluid" src="<?= $logo_prefix . $logoUrl; ?>">
                                <?php
                                } ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-12" style="margin-top:8px;">
                        <div style="display: flex; justify-content: space-between; border-bottom: 2px solid #aaa;">
                            <p>Total Marks: <b><?= $totalMarks ?></b></p>
                            <p>Questions: <b><?= $noOfQuestions ?></b></p>
                            <p>Duration: <b><?= $test_duration_in_hours_min ?></b></p>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            $questionNumber = 1;
            $no_of_que_per_page_cnt = 1;
            $i = 1;
            foreach ($objTestData["test"]["test"] as $question) {

                if (!empty($no_of_que_per_page)) {
                    if ($no_of_que_per_page == $no_of_que_per_page_cnt) {
                        echo "<br clear='page_break' style='page-break-before:always' />";
                        $no_of_que_per_page_cnt = 1;
                    } else {
                        $no_of_que_per_page_cnt++;
                    }
                }

                if ($i == 1) {
                    echo "<div class='row'  id='row_custom'>";
                }


                if (isset($question['question'])) {
                    $questionText = $question['question'];
                    $questionText = str_replace('$$', '$', $questionText);
                } else {
                    $questionText = "";
                }

                if (isset($question['questionImageUrl'])) {
                    $question_imgUrl = $question['questionImageUrl'];
                    $question_imgUrl = str_replace("/var/www/test.edofox.com/public_html", "https://test.edofox.com", $question_imgUrl);
                    if ($question_imgUrl != "") {
                        $question_imgUrlTag = "<a href='$question_imgUrl' target='_blank'><img src='$question_imgUrl' class='img-fluid img_custom' alt='Question Image'></a>";
                    } else {
                        $question_imgUrlTag = "";
                    }
                } else {
                    $question_imgUrlTag = "";
                }

                if (isset($question["option1"])) {
                    $option1Text = $question["option1"];
                    if ($option1Text == "1)") {
                        $option1TextTag = null;
                    } else {
                        $option1Text = str_replace('$$', '$', $option1Text);
                        $option1TextTag = "<p>1) $option1Text</p>";
                    }
                } else {
                    $option1TextTag = null;
                }

                if (isset($question["option2"])) {
                    $option2Text = $question["option2"];
                    if ($option2Text == "2)") {
                        $option2TextTag = null;
                    } else {
                        $option2Text = str_replace('$$', '$', $option2Text);
                        $option2TextTag = "<p>2) $option2Text</p>";
                    }
                } else {
                    $option2TextTag = null;
                }

                if (isset($question["option3"])) {
                    $option3Text = $question["option3"];
                    if ($option3Text == "3)") {
                        $option3TextTag = null;
                    } else {
                        $option3Text = str_replace('$$', '$', $option3Text);
                        $option3TextTag = "<p>3) $option3Text</p>";
                    }
                } else {
                    $option3TextTag = null;
                }

                if (isset($question["option4"])) {
                    $option4Text = $question["option4"];
                    if ($option4Text == "4)") {
                        $option4TextTag = null;
                    } else {
                        $option4Text = str_replace('$$', '$', $option4Text);
                        $option4TextTag = "<p>4) $option4Text</p>";
                    }
                } else {
                    $option4TextTag = null;
                }

                if (isset($question["correctAnswer"])) {
                    $correctAnswerText = $question["correctAnswer"];

                    $correctAnswerText = str_replace('$$', '$', $correctAnswerText);
                    $correctAnswerText = str_replace('option1', '1)', $correctAnswerText);
                    $correctAnswerText = str_replace('option2', '2)', $correctAnswerText);
                    $correctAnswerText = str_replace('option3', '3)', $correctAnswerText);
                    $correctAnswerText = str_replace('option4', '4)', $correctAnswerText);
                    $correctAnswerTextTag = "<p><b>Correct Answer: </b> $correctAnswerText</p>";
                } else {
                    $correctAnswerTextTag = null;
                }

                if (isset($question["solution"])) {
                    $solutionText = $question["solution"];
                    $solutionText = str_replace('$$', '$', $solutionText);

                    if ($solutionText == "") {
                        $solutionTextTag = null;
                    } else {
                        $solutionTextTag = "<p><b>Solution: </b>$solutionText</p>";
                    }
                } else {
                    $solutionTextTag = null;
                }

                if (isset($question['solutionImageUrl'])) {
                    $solutionImageUrl = $question['solutionImageUrl'];
                    $solutionImageUrl = str_replace("/var/www/test.edofox.com/public_html", "https://test.edofox.com", $solutionImageUrl);
                    if ($solutionImageUrl != "") {
                        $solutionImageUrlTag = "<a href='$solutionImageUrl' target='_blank'><img src='$solutionImageUrl' class='img-fluid img_custom' alt='Solution Image'></a>";
                    } else {
                        $solutionImageUrlTag = null;
                    }
                } else {
                    $solutionImageUrlTag = null;
                }

            ?>


                <div class="<?= $columns_class ?> each_question">

                    <div style="display: flex;flex-direction:row;">
                        <div><b>Q.<?= $questionNumber ?>)</b></div>
                        <div><?= $questionText ?></div>
                        <div><?= $question_imgUrlTag ?></div>
                    </div>





                    <div style="display: flex;flex-direction:column;">
                        <?php
                        if ($show_options) {
                        ?>
                            <div><?= $option1TextTag ?></div>
                            <div><?= $option2TextTag ?></div>
                            <div><?= $option3TextTag ?></div>
                            <div><?= $option4TextTag ?></div>
                        <?php
                        }
                        ?>
                        <?php
                        if ($show_solutions) {
                        ?>
                            <div><?= $correctAnswerTextTag ?></div>
                            <div><?= $solutionTextTag ?></div>
                            <div><?= $solutionImageUrlTag ?></div>
                        <?php
                        }
                        ?>
                    </div>

                </div>
            <?php
                if ($i == $columns) {
                    echo "</div>";
                    $i = 1;
                } else {
                    $i++;
                }
                $questionNumber++;
            }
            ?>
            <style type="text/css">
                @media print {
                    @page {
                        size: A4;
                        margin: 1cm;
                    }


                    @page {
                        margin-top: 0;
                        margin-bottom: 0;
                    }

                    body {
                        padding-top: 72px;
                        padding-bottom: 72px;
                    }

                    html,
                    /* body {
                        width: 210mm;
                        height: 297mm;
                    } */

                    #row_custom {
                        break-inside: avoid !important;
                        page-break-inside: avoid !important;
                        margin-bottom: 8px;
                        margin-top: 8px;
                        padding: 0px 10px !important;
                        /* overflow: hidden; */
                    }

                    .img_custom {
                        width: 100%;
                    }

                    p {
                        font-size: 12px;
                    }

                    mjx-math {
                        white-space: pre-wrap !important;
                    }
                }
            </style>

            <?php
            if (!empty($row_height) && !empty($img_height)) {
            ?>
                <style type="text/css">
                    @media print {
                        #row_custom {
                            height: <?= $row_height; ?>
                        }

                        .img_custom {
                            max-height: <?= $img_height; ?>
                        }
                    }
                </style>
            <?php
            }
            ?>
            <div class="text-center mt-4" id="print_footer">
                <p>Powered By Edofox</p>
                <img class="img-fluid" style="height:25px;" src="<?= base_url('assets/img/edofox-name-logo-black.png'); ?>" alt="edofox-logo" />
            </div>

        </div>
    </div>
</div>


<div class="text-center">
    <button class="print-button my-1" onclick="printContent('print_div')">Print Paper</button>
    <p class="small text-muted">Please click print only after all the questions are loaded properly.</p>
</div>