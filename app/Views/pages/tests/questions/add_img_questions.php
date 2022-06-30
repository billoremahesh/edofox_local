<!-- Include Header -->
<?php include_once(APPPATH . "Views/header.php"); ?>

<!-- Custom CSS -->
<link href="<?php echo base_url('assets/css/tests/overview.css?v=20210915'); ?>" rel="stylesheet">
<link href="<?php echo base_url('assets/css/tests/add_test_questions.css?v=20210915'); ?>" rel="stylesheet">

<script>
    var teacherId = <?= decrypt_cipher(session()->get('login_id')); ?>;
    // console.log("Teacher ID is ", teacherId);
</script>

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


        <div class="d-flex mb-4">
            <a class='btn btn-primary' href="<?= base_url('tests/update_test_questions/' . $test_id); ?>"> Check Questions in Test </a>
        </div>

        <div class="card shadow p-4">
            <div class="row mb-4">

                <div class="col-md-2">
                    <label>Offset</label>
                </div>

                <?php
                $q_offset = 0;
                if (!empty($test_offset)) {
                    if ($test_offset['cnt'] != 0) {
                        $q_offset = $test_offset['qno'] + 1;
                    } else {
                        $q_offset = 1;
                    }
                }
                ?>
                <div class="col-md-2">
                    <input type="number" class="form-control" id="offset" value="<?php echo $q_offset; ?>">
                </div>

                <div class="col-md-2">
                    <label>Test No. of Questions</label>
                </div>
                <div class="col-md-2">
                    <input type="number" class="form-control" id="no_of_ques" name="no_of_ques" />
                </div>

                <div class="col-4">
                    <?php
                    if (isset($test_details) && !empty($test_details)) {
                        if (!empty($test_details['template_id'])) {
                    ?>
                            <div class="mb-2">
                                <input class="form-label" type="checkbox" name="template_check" id="template_check" value="<?= $test_details['template_id']; ?>" checked>
                                <label for="template_check"> Fetch values from Test Template <i class="far fa-question-circle" aria-hidden="true" data-bs-toggle="tooltip" title="Following values will be fetched from the test template linked with the test"></i></label>
                            </div>
                    <?php
                        }
                    }
                    ?>
                </div>

            </div>

            <div class="row mb-4">

                <div class="col-md-2">
                    <label>Section</label>
                </div>
                <div class="col-md-2">
                    <input type="text" class="form-control template_check" id="section" value="" required>
                </div>

                <div class="col-md-2">
                    <label>Weightage</label>
                </div>
                <div class="col-md-2">
                    <input type="number" class="form-control template_check" id="weightage" value="1" required />
                </div>

                <div class="col-md-2">
                    <label>Negative Mark</label>
                </div>
                <div class="col-md-2">
                    <input type="number" class="form-control template_check" id="negative_mark" value="0" step=".01" required />
                </div>


            </div>

            <div class="row mb-4">
                <div class="col-md-2">
                    <label>Subject</label>
                </div>
                <div class="col-md-2">
                    <select class="form-control template_check" id="subjectid">
                        <?php if (!empty($subject_details)) :
                            foreach ($subject_details as $subject) :
                        ?>
                                <option value="<?= $subject['subject_id']; ?>"><?= $subject['subject']; ?></option>
                        <?php
                            endforeach;
                        endif;
                        ?>
                    </select>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-2">
                    <label>Options Type</label>
                </div>
                <div class="col-md-2">
                    <select class="form-control" id="optionsType">
                        <option value="numeric">1) 2) 3) 4)</option>
                        <option value="letters">A) B) C) D)</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label>Question Type</label>
                </div>
                <div class="col-md-2">
                    <select class="form-control template_check" id="question_type" name="question_type">
                        <option value="SINGLE">SINGLE</option>
                        <option value="MULTIPLE">MULTIPLE</option>
                        <option value="NUMBER">NUMBER</option>
                        <option value="MATCH">MATCH</option>
                        <option value="PASSAGE_MULTIPLE">PASSAGE MULTIPLE</option>
                        <option value="DESCRIPTIVE">Subjective Answer</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label>Partial Marking</label>
                </div>
                <div class="col-md-2">
                    <select class="form-control" id="question_partial_marking" name="question_partial_marking">
                        <option value="N">NO</option>
                        <option value="Y">YES</option>
                    </select>
                </div>


                <input type="hidden" id="instituteid" value="<?php echo $instituteID; ?>" required>
                <input type="hidden" id="test_id" value="<?php echo $test_id; ?>" required />
                <div class="col-xs-12 text-center" style="margin-top: 16px;">
                    <button class="btn btn-success" onclick="addTestQuestions();">Add Questions</button>
                </div>
            </div>


            <hr />
            <div id="showQuestionUploadDiv"></div>


        </div>



        <div class="modal fade" id="imageSizeModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h5 class="modal-title" id="myModalLabel">Error in images <i class="fa fa-exclamation-triangle" aria-hidden="true"></i></h5>
                    </div>
                    <div class="modal-body">
                        The below images have size larger than <span id="imageSizeText"></span>KB. Please compress their size and reupload for faster test experience to students. We recommend you keep each file size below 50KB.
                        <div id="listOfErrorImages"></div>

                        <hr />
                        <div class="text-center">
                            <a class="btn btn-warning" href="https://tinyjpg.com/" target="_blank">Compress Images at TinyJPG.com <i class="fa fa-external-link" aria-hidden="true"></i></a>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>


        <!-- Modal to show loader while uploading images -->
        <div class="modal fade" id="imageUploadingProgressModal" tabindex="-1" role="dialog" aria-labelledby="imageUploadingProgressModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="text-center" id="uploading-div">
                            <lottie-player src="../dist/img/animations/7877-uploading-to-cloud.json" background="transparent" speed="1" style="width: 200px; margin:auto;" loop autoplay></lottie-player>
                            <label>Uploading... Please wait</label>
                        </div>
                        <div class="text-center" id="uploading-finished-div">
                            <img src="https://edofox-s3.s3.ap-south-1.amazonaws.com/public/statics/checked-circle.png" />
                            <br>
                            <label>Done!</label>
                            <p><label>You can review the added questions in Check/Update Questions in Test section.</label></p>
                        </div>
                    </div>
                    <div class="modal-footer" id="imageUploadingProgressModalFooter">

                        <a class='btn btn-info' href="<?= base_url('tests/update_test_questions/' . $test_id); ?>"> Check Questions in Test </a>


                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
<!-- Include Footer -->
<?php include_once(APPPATH . "Views/footer.php"); ?>

<script>
    function addTestQuestions() {

        var noQues = document.getElementById("no_of_ques").value;
        var institute_id = <?= $decrypt_institute_id; ?>;
        var test_id = "<?= $decrypt_test_id; ?>";
        var subject_id = document.getElementById("subjectid").value;
        var section = document.getElementById("section").value;
        var weightage = document.getElementById("weightage").value;
        var negative_mark = document.getElementById("negative_mark").value;
        var offset = document.getElementById("offset").value;
        var optionsType = document.getElementById("optionsType").value;
        var quetionType = document.getElementById("question_type").value;
        var questionPartialMarking = document.getElementById("question_partial_marking").value;
        var template_id = "";
        test_template_check = document.getElementById('template_check');
        if (test_template_check) {
            if (test_template_check.checked) {
                template_id = $("#template_check").val();
            } else {
                template_id = "";
            }
        }

        // Validation on input field
        if (offset == "") {
            document.getElementById("showQuestionUploadDiv").innerHTML = "";
            alert("Offset cannot be empty, negative or 0");
            return;
        }

        if (noQues < 0 || noQues == 0 || noQues == "") {
            document.getElementById("showQuestionUploadDiv").innerHTML = "";
            alert("Number of questions cannot be empty, negative or 0");
            return;
        }

        if (section == "" && template_id == "") {
            document.getElementById("showQuestionUploadDiv").innerHTML = "";
            alert("Section cannot be empty");
            return;
        }

        if ((weightage < 0 || weightage == 0 || weightage == "") && template_id == "") {
            document.getElementById("showQuestionUploadDiv").innerHTML = "";
            alert("Weightage cannot be empty, negative or 0");
            return;
        }

        if (negative_mark == "" && template_id == "") {
            document.getElementById("showQuestionUploadDiv").innerHTML = "";
            alert("Negative mark cannot be empty");
            return;
        }

        $.ajax({
            type: "POST",
            data: {
                noQues: noQues,
                institute_id: institute_id,
                test_id: test_id,
                subject_id: subject_id,
                section: section,
                weightage: weightage,
                negative_mark: negative_mark,
                offset: offset,
                optionsType: optionsType,
                quetionType: quetionType,
                questionPartialMarking: questionPartialMarking,
                template_id: template_id
            },
            url: base_url + "/tests/get_multiple_questions_div",
            success: function(data) {
                // console.log(data);
                document.getElementById("showQuestionUploadDiv").innerHTML = data;

                // Animate the UI to scroll down to the upload button
                $('html, body').animate({
                    scrollTop: $("#showQuestionUploadDiv").offset().top
                }, 1000);
            },
        });



    }
</script>

<!--  New script added to upload files to CDN using Java service -->
<script>
    var uploadImages = function(event) {
        console.log("Preventing submit!");
        event.preventDefault();

        var subjects = document.getElementsByName('Que_subject_id[]');

        if (subjects == null || subjects.length == 0) {
            alert("Invalid input! Please try again!");
            return;
        }

        var offset = $("#Que_offset").val();
        var testId = "<?= $decrypt_test_id; ?>";
        var instituteId = "<?= $decrypt_institute_id; ?>";

        var types = document.getElementsByName('Question_type[]');
        var sections = document.getElementsByName('section[]');
        var weightages = document.getElementsByName('weightage[]');
        var negativeMarks = document.getElementsByName('negative_mark[]');
        var partials = document.getElementsByName('Que_partial_marking[]');
        var correctAnswers = document.getElementsByName('Que_correct_ans[]');
        var matchCol1 = document.getElementsByName('matchColumn1[]');
        var matchCol2 = document.getElementsByName('matchColumn2[]');
        //var files = document.getElementsByName('test_questions_files[]');

        var correctAnswersArray = [];
        correctAnswers.forEach(function(ansInput) {
            if (!ansInput.disabled) {
                correctAnswersArray.push(ansInput);
                //console.log("Added to array");
            }
        });

        //console.log("Answers", correctAnswersArray);

        var questions = [];

        for (var i = 0; i < subjects.length; i++) {
            var marks = weightages[i].value;
            if (marks == '') {
                marks = null;
            }
            var negative = negativeMarks[i].value;
            if (negative == '') {
                negative = null;
            }
            var questionType = types[i].value;
            var op1 = null,
                op2 = null;
            if (questionType != null && questionType == 'MATCH') {
                op1 = matchCol1[i].value;
                op2 = matchCol2[i].value;
            }
            var correctAns = null;
            if (correctAnswersArray[i] != null) {
                correctAns = correctAnswersArray[i].value;
            }
            var question = {
                subjectId: subjects[i].value,
                section: sections[i].value,
                weightage: marks,
                negativeMarks: negative,
                partialCorrection: partials[i].value,
                correctAnswer: correctAns,
                type: questionType,
                questionNumber: offset,
                instituteId: instituteId

            }
            if (op1 != null) {
                question.option1 = op1;
            }
            if (op2 != null) {
                question.option2 = op2;
            }
            if (teacherId != null && teacherId != 0) {
                question.teacher = {
                    id: teacherId
                }
            }
            offset++;
            questions.push(question);

        }

        var request = {
            test: {
                id: testId,
                test: questions,
                currentQuestion: {
                    instituteId: instituteId
                }
            }
        }

        console.log("Request", request);

        var formData = new FormData();

        var totalfiles = document.getElementById('test_questions_files').files.length;
        for (var index = 0; index < totalfiles; index++) {
            formData.append("files", document.getElementById('test_questions_files').files[index]);
        }

        //formData.append('files', files.value);
        formData.append('request', JSON.stringify(request));

        // Showing uploading loader
        // https://stackoverflow.com/questions/62827002/bootstrap-v5-manually-call-a-modal-mymodal-show-not-working-vanilla-javascrip
        var imageUploaderModal = new bootstrap.Modal(document.getElementById("imageUploadingProgressModal"), {
            backdrop: 'static',
            keyboard: false
        });
        imageUploaderModal.show();

        $('#imageUploadingProgressModalFooter').hide();
        $('#uploading-finished-div').hide();

        //Load tokens first
        get_admin_token().then(function(result) {
                var resp = JSON.parse(result);
                if (resp != null && resp.status == 200 && resp.data != null && resp.data.admin_token != null) {

                    $.ajax({
                        url: rootAdmin + 'uploadQuestionImages',
                        type: 'POST',
                        beforeSend: function(formData) {
                            formData.setRequestHeader("AuthToken", resp.data.admin_token);
                        },
                        xhr: function() {
                            var myXhr = $.ajaxSettings.xhr();
                            return myXhr;
                        },
                        success: function(data) {

                            $('#imageUploadingProgressModalFooter').show();

                            console.log("Upload response", data);
                            if (data != null && data.status != null && data.status.statusCode == 200) {
                                document.getElementById("showQuestionUploadDiv").innerHTML = "";
                                $("#offset").val(offset);
                                $("#uploading-finished-div").fadeIn("slow");
                                $('#uploading-div').hide();

                                // Snakbar Message
                                Snackbar.show({
                                    pos: 'top-center',
                                    text: 'Questions uploaded successfully'
                                });

                            } else if (data != null && data.status != null) {
                                alert("Error! " + data.responseText);
                            } else {
                                alert("There was some error in uploading test. Please try again.");
                            }


                        },
                        data: formData,
                        cache: false,
                        contentType: false,
                        processData: false
                    });

                } else {
                    alert("Some error authenticating your request. Please clear your browser cache and try again.");
                }
            })
            .catch(function(error) {
                // An error occurred
                // alert("Exception: " + error);
            });

    };


    function checkNumberOfImages() {
        var test_questions_files = document.getElementById("test_questions_files").files;
        var noQues = document.getElementById("no_of_ques").value;
        var add_bulk_questions_submit_button = document.getElementById("add_bulk_questions_submit_button");

        var showImageSizeModal = false;
        // console.log("Images uploaded: " + test_questions_files.length + "... Questions in div: " + noQues);
        // console.log("Images uploaded: ", test_questions_files);

        if (test_questions_files.length != noQues) {
            alert("The number of questions images you selected should be equal to the number of questions you entered above.");
            add_bulk_questions_submit_button.style.display = "none";
            return;
        } else {
            var errorImages = [];
            var maxImageSizeForValidation = 200; // in KB
            //Check the size of each image
            for (var i = 0; i < test_questions_files.length; i++) {
                var imageSize = test_questions_files[i].size / 1024
                // console.log(imageSize, test_questions_files[i].name);

                //Making sure the images are less than strict upper limit
                if (test_questions_files[i].size / 1024 > maxImageSizeForValidation) {
                    //Show the values in a modal of while file the user needs to compress
                    showImageSizeModal = true;
                    errorImages.push(test_questions_files[i].name + " (" + Number(imageSize.toPrecision(5)) + "KB)");
                }

            }



            if (showImageSizeModal) {
                // Bootstrap 5 Modal show issue
                // https://stackoverflow.com/questions/62827002/bootstrap-v5-manually-call-a-modal-mymodal-show-not-working-vanilla-javascrip
                var imageSizeModal = new bootstrap.Modal(document.getElementById("imageSizeModal"), {});
                imageSizeModal.show();
                $("#listOfErrorImages").html("");
                $("#imageSizeText").html(maxImageSizeForValidation);
                for (var i = 0; i < errorImages.length; i++) {
                    $("#listOfErrorImages").append("<li><b>" + errorImages[i] + "</b></li>")
                }


                // Hide the button
                add_bulk_questions_submit_button.style.display = "none";
                return;
            }


            add_bulk_questions_submit_button.style.display = "block";
            add_bulk_questions_submit_button.style.margin = "auto";
        }

        var form = document.getElementById("upload_questions_form");

        // attach event listener
        form.addEventListener("submit", uploadImages, true);
        console.log("Added form submit listener");

    }

    function changeCorrectAnswerInput(selectInputElement) {
        var selectInputValue = selectInputElement.value;
        var answerInputSelector = $(selectInputElement).parent().parent().parent().find(".answer_input_selector");
        var answerInputText = $(selectInputElement).parent().parent().parent().find(".answer_input_text");
        var columnValuesRowElement = $(selectInputElement).parent().parent().parent().find("#column_values_row");

        switch (selectInputValue) {
            case "SINGLE":
                answerInputSelector.prop("disabled", false);
                answerInputSelector.show();
                answerInputText.val("");
                answerInputText.hide();
                answerInputText.prop("disabled", true);
                columnValuesRowElement.hide();
                break;
            case "MULTIPLE":
                answerInputText.show();
                answerInputText.prop("disabled", false);
                answerInputSelector.hide();
                answerInputSelector.prop("disabled", true);
                columnValuesRowElement.hide();
                break;
            case "NUMBER":
                answerInputText.show();
                answerInputText.prop("disabled", false);
                answerInputSelector.hide();
                answerInputSelector.prop("disabled", true);
                columnValuesRowElement.hide();
                break;
            case "MATCH":
                answerInputText.show();
                answerInputText.prop("disabled", false);
                answerInputSelector.hide();
                answerInputSelector.prop("disabled", true);
                columnValuesRowElement.show();
                break;
            case "PASSAGE_MULTIPLE":
                answerInputText.show();
                answerInputText.prop("disabled", false);
                answerInputSelector.hide();
                answerInputSelector.prop("disabled", true);
                columnValuesRowElement.hide();
                break;
            case "DESCRIPTIVE":
                answerInputText.hide();
                answerInputText.css("display", 'none');
                answerInputSelector.hide();
                answerInputSelector.prop("disabled", true);
                columnValuesRowElement.hide();
                break;

            default:
        }
    }
</script>


<script>
    // Check template check/uncheck
    $(document).ready(function() {
        test_template_check = document.getElementById('template_check');
        if (test_template_check) {
            if (test_template_check.checked) {
                $(".template_check").attr("disabled", true);
                $('#section').removeAttr('required');
                $('#weightage').removeAttr('required');
                $('#negative_mark').removeAttr('required');
            } else {
                $(".template_check").attr("disabled", false);
                $("#section").prop('required', true);
                $("#weightage").prop('required', true);
                $("#negative_mark").prop('required', true);
            }
        }
        $("#template_check").click(function() {
            if (this.checked) {
                $(".template_check").attr("disabled", true);
                $('#section').removeAttr('required');
                $('#weightage').removeAttr('required');
                $('#negative_mark').removeAttr('required');
            } else {
                $(".template_check").attr("disabled", false);
                $("#section").prop('required', true);
                $("#weightage").prop('required', true);
                $("#negative_mark").prop('required', true);
            }
        });
    });
</script>