<!-- Include Header -->
<?php include_once(APPPATH . "Views/header.php"); ?>

<!-- Custom CSS -->
<link href="<?php echo base_url('assets/css/tests/generate_chapter_test.css'); ?>" rel="stylesheet">

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

        <div class="p-4">
            <div class="row">
                <div class="col-4">
                    <div class="test_card_box">
                        <p class="text-muted">Overview:</p>
                        <table class="table borderless">
                            <thead>
                                <th>#</th>
                                <th>Chapter Name</th>
                                <th class='text-right'>Count</th>
                            </thead>
                            <tbody>
                                <?php
                                $questionsAddedCount = $number_of_questions['questionsAddedCount'];
                                $totalQuestionsInTest = $number_of_questions['no_of_questions'];

                                if ($questionsAddedCount == null) {
                                    $questionsAddedCount = 0;
                                }


                                $rowCount = 1;
                                foreach ($chapterwise_questions as $rowChapterCount) {
                                    $chapter_name = $rowChapterCount["chapter_name"];
                                    if ($chapter_name == null) {
                                        $chapter_name = "Miscellaneous";
                                        $chapter_name = strtoupper($chapter_name);
                                    }
                                    $chapter_count = $rowChapterCount["chapter_count"];
                                    $chapter_id = $rowChapterCount["chapter"];

                                    echo "<tr>
                                <td>$rowCount </td>
                                <td>$chapter_name </td>
                                <td class='text-right'><b>$chapter_count</b></td>
                                </tr>";

                                    $rowCount++;
                                }

                                echo "<tr>
                            <td> </td>
                            <td><b class='text-muted'>Total Questions added</b></td>
                            <td class='text-right'><b>$questionsAddedCount</b></td>
                            </tr>";

                                echo "<tr>
                            <td> </td>
                            <td>Out of</td>
                            <td class='text-right'><b>$totalQuestionsInTest</b></td>
                            </tr>";



                                ?>

                            </tbody>
                        </table>

                        <table class="table borderless">
                            <thead>
                                <th>Question type</th>
                                <th class='text-right'>Count</th>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($type_of_questions as $rowChapterCount) :
                                    $typeCount = $rowChapterCount["typeCount"];
                                    $type = $rowChapterCount["question_type"];
                                    if ($type != null) {
                                        if ($type == 'NUMBER') {
                                            $type = 'Numeric';
                                        } else if ($type == 'SINGLE') {
                                            $type = 'Single correct';
                                        } else if ($type == 'MULTIPLE') {
                                            $type = 'Multiple correct';
                                        } else if ($type == 'MATCH') {
                                            $type = 'Match the columns';
                                        } else {
                                            $type = 'Other';
                                        }
                                        echo "<tr>
                                            <td> $type</td>
                                            <td class='text-right'><b>$typeCount</b></td>
                                          </tr>";
                                    }
                                endforeach;
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-8">
                    <div class="test_card_box">
                        <div class="row form_div">
                            <div class="col-md-4">
                                <label for="test_name"> Test Name </label>
                            </div>
                            <div class="col-md-8">
                                <input type="text" class="form-control" id="test_name" value="<?php echo $test_details['test_name']; ?>" readonly>
                            </div>
                        </div>


                        <div class="row form_div">
                            <div class="col-md-4">
                                <label for="test_subject">Subject</label>
                            </div>
                            <div class="col-md-8">
                                <select class="form-control" id="test_subject" onchange="changeChapter(this.value)">
                                    <option value=""></option>
                                    <?php
                                    foreach ($test_subjects  as $data) {
                                        $subj_id = $data['subject_id'];
                                        $subj_name = $data['subject'];
                                        echo "<option value='$subj_id'> $subj_name </option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div id="loading-chapters-div" class="col-xs-12" style="display:none;">
                                <p class="text-muted text-center">Loading chapters...</p>
                            </div>
                        </div>

                        <div class="row form_div">
                            <div class="col-md-4">
                                <label for="test_chapter">Chapter</label>
                            </div>

                            <div class="col-md-8">
                                <select class="form-control" id="test_chapter">
                                    <option value=""></option>
                                </select>
                            </div>

                        </div>

                        <div class="row form_div">
                            <div class="col-md-4">
                                <label for="test_type_hard"> Add questions of type </label>
                            </div>
                            <div class="col-md-8">
                                <select class="form-control" id="question_type">
                                    <option value="">Select question type</option>
                                    <option value="NUMBER">Numeric</option>
                                    <option value="SINGLE">Single correct</option>
                                    <option value="MULTIPLE">Multiple correct</option>
                                    <option value="MATCH">Match the columns</option>

                                </select>
                                <br>

                            </div>
                        </div>

                        <div class="row form_div">
                            <div class="col-md-4">
                                <label for="test_type_easy"> Add from question </label>
                            </div>
                            <div class="col-md-8">
                                <input type="number" class="form-control" id="test_starting_question" value="1" />
                            </div>
                        </div>

                        <div class="row form_div">
                            <div class="col-md-4">
                                <label for="test_type_easy"> Add to Section </label>
                            </div>
                            <div class="col-md-8">
                                <input type="text" class="form-control" id="test_section" placeholder="Section name" />
                            </div>
                        </div>



                        <div class="row form_div">
                            <div class="col-md-4">
                                <label for="test_type_easy"> Easy </label>
                            </div>
                            <div class="col-md-8">
                                <input type="number" class="form-control" id="test_type_easy" placeholder="No of easy difficulty questions to pick" onchange="changeTotal();" />
                            </div>
                        </div>


                        <div class="row form_div">
                            <div class="col-md-4">
                                <label for="test_type_medium"> Medium </label>
                            </div>
                            <div class="col-md-8">
                                <input type="number" class="form-control" id="test_type_medium" placeholder="No of medium difficulty questions to pick" onchange="changeTotal();" />
                            </div>
                        </div>

                        <div class="row form_div">
                            <div class="col-md-4">
                                <label for="test_type_hard"> Hard </label>
                            </div>
                            <div class="col-md-8">
                                <input type="number" class="form-control" id="test_type_hard" placeholder="No of hard difficulty questions to pick" onchange="changeTotal();" />
                            </div>
                        </div>


                        <div class="row form_div">
                            <div class="col-md-4">
                                <label for="test_que_total"> Total </label>
                            </div>
                            <div class="col-md-8">
                                <input type="number" class="form-control" id="test_que_total" />
                            </div>
                        </div>


                        <div class="row form_div">
                            <div class="col-md-4">
                                <label for="test_weightage"> Weightage </label>
                            </div>
                            <div class="col-md-8">
                                <input type="number" class="form-control" id="test_weightage" />
                            </div>
                        </div>


                        <div class="row form_div">
                            <div class="col-md-4">
                                <label for="test_negative_mark"> Negative Mark </label>
                            </div>
                            <div class="col-md-8">
                                <input type="number" class="form-control" id="test_negative_mark" />
                            </div>
                        </div>

                        <div class="row form_div">
                            <div class="col-md-4">
                                <button class="btn btn-primary" onclick="generateChapterTest()"> Generate Test</button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="spinner-container-with-cancel" style="left: 0%;top:10%;" id="loader">
        <div class="spinner-sub-container">
            <h3 class="message" style="top: 15%;font-size:16px;"> Generating Test. Please wait...</h3>
            <div class="spinner" style="display: block;margin-top: 17%;">
                <div class="rect1"></div>
                <div class="rect2"></div>
                <div class="rect3"></div>
                <div class="rect4"></div>
                <div class="rect5"></div>
            </div>
            <div id="btn-close-spinner" style="margin-top: 10px;" class="spinner-btn-close">Cancel</div>
        </div>
    </div>
</div>

<!-- Include Footer -->
<?php include_once(APPPATH . "Views/footer.php"); ?>

<script>
    var institute_id = "<?php echo $instituteID; ?>";

    function changeChapter(val) {
        if (val.length == 0) {
            return;
        } else {
            var test_chapter_dropdown = document.getElementById("test_chapter");
            $("#loading-chapters-div").show();
            test_chapter_dropdown.disabled = true;
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    test_chapter_dropdown.innerHTML = this.responseText;
                    test_chapter_dropdown.disabled = false;
                    $("#loading-chapters-div").hide();
                }
            };
            xmlhttp.open("GET", base_url + "/tests/append_test_chapters/" + val, true);
            xmlhttp.send();
        }
    }
</script>

<script>
    function changeTotal() {
        var test_type_easy_cnt = document.getElementById("test_type_easy").value;
        var test_type_medium_cnt = document.getElementById("test_type_medium").value;
        var test_type_hard_cnt = document.getElementById("test_type_hard").value;
        document.getElementById("test_que_total").value = Math.round(Number(test_type_easy_cnt) + Number(test_type_medium_cnt) + Number(test_type_hard_cnt));
    }
</script>

<script>
    function toggleSpinner(toggle) {
        var x = document.getElementById("loader");
        if (toggle === "show") {
            x.style.display = "block";
        } else {
            x.style.display = "none";
        }
    }

    function generateChapterTest() {
        toggleSpinner('show');
        var test_id = localStorage.getItem("testIdAnalysis");
        var encrypt_test_id = "<?= $test_id; ?>";
        var test_name = document.getElementById("test_name").value;
        var test_subject_id = document.getElementById("test_subject").value;
        var test_chapter_id = document.getElementById("test_chapter").value;
        var test_que_total = document.getElementById("test_que_total").value
        var test_type_easy_cnt = document.getElementById("test_type_easy").value;
        var test_type_medium_cnt = document.getElementById("test_type_medium").value;
        var test_type_hard_cnt = document.getElementById("test_type_hard").value;
        var test_weightage = document.getElementById("test_weightage").value;
        var test_negative_mark = document.getElementById("test_negative_mark").value;
        var question_type = document.getElementById("question_type").value;
        var test_section = document.getElementById("test_section").value;
        var test_starting_question = document.getElementById("test_starting_question").value;

        if (test_subject_id == "" || test_chapter_id == "" || test_que_total == "" ||
            test_type_easy_cnt == "" || test_type_medium_cnt == "" || test_type_hard_cnt == "" ||
            test_weightage == "" || test_negative_mark == "") {
            toggleSpinner('hide');
            alert("One of the field id empty, please check all fields.");
        } else {

            var obj = {
                "test": {
                    "id": <?= $decrypt_test_id; ?>,
                    "name": test_name
                },
                "question": {
                    "subjectId": test_subject_id,
                    "chapter": {
                        "chapterId": test_chapter_id
                    },
                    "analysis": {
                        "hardQuestionsCount": test_type_hard_cnt,
                        "mediumQuestionsCount": test_type_medium_cnt,
                        "easyQuestionsCount": test_type_easy_cnt,
                        "questionType": question_type
                    },
                    "weightage": test_weightage,
                    "negativeMarks": test_negative_mark,
                    "section": test_section
                },
                "firstQuestion": test_starting_question
            };
            var myJSON1 = JSON.stringify(obj);

            //Load tokens first
            get_admin_token().then(function(result) {
                    var resp = JSON.parse(result);
                    if (resp != null && resp.status == 200 && resp.data != null && resp.data.admin_token != null) {

                        var url = rootAdmin + "autoCreateExam";
                        fetch(url, {
                                method: 'POST',
                                body: myJSON1,
                                headers: {
                                    'Content-Type': 'application/json',
                                    "AuthToken": resp.data.admin_token
                                }
                            }).then(res => res.json())
                            .then(response => {
                                console.log('Success:', response);
                                Snackbar.show({
                                    pos: 'top-center',
                                    text: 'Chapter wise test generated successfully'
                                });

                                toggleSpinner('hide');
                                if (response.status.statusCode < 0) {
                                    alert(response.status.responseText);
                                } else {
                                    //alert(response.status);
                                    window.location = base_url + '/tests/generate_chapter_wise_test/' + encrypt_test_id;
                                }

                                //
                            }).catch(error => console.error('Error:', error));

                    } else {
                        alert("Some error authenticating your request. Please clear your browser cache and try again.");
                    }
                })
                .catch(function(error) {
                    // An error occurred
                    // alert("Exception: " + error);
                });

        }
    }
</script>