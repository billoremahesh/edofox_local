<!-- Include Header -->
<?php include_once(APPPATH . "Views/header.php"); ?>

<!-- Custom CSS -->
<link href="<?php echo base_url('assets/css/question_bank/overview.css?v=20211028'); ?>" rel="stylesheet">

<div id="content">
    <div class="container-fluid mt-4">

        <div class="flex-container-column">
            <div>
                <label class="h5 text-gray-800 text-uppercase"> <?= $title; ?> </h5>
            </div>
            <div class="breadcrumb_div" aria-label="breadcrumb">
                <ol class="breadcrumb_custom">
                    <li class="breadcrumb_item"><a href="<?php echo base_url('/home'); ?>"> Home </a></li>
                    <li class="breadcrumb_item"><a href="<?php echo base_url('questionBank'); ?>"> Question Bank </a></li>
                    <li class="breadcrumb_item active" aria-current="page">
                        <a href="<?php echo base_url('questionBank/chapter_questions/' . $subject_id . '/' . $decrypted_chapter_id); ?>">
                            <?= $subject_detail['subject'] . ": " . $chapter_detail['chapter_name']; ?>
                        </a>
                    </li>
                </ol>
            </div>
        </div>


        <div class="bg-white shadow rounded p-4 mb-5">

            <h5 class="text-center">
                <?= $subject_detail['subject'] . ": " . $chapter_detail['chapter_name']; ?>
            </h5>

            <hr />

            <div class="row justify-content-center">
                <div class="col-lg-2 my-2">
                    <label class="form-label"> No. of Questions </label>
                    <input type="number" class="form-control" id="no_of_questions" autofocus />
                </div>
                <div class="col-lg-2 my-2">
                    <label class="form-label"> Difficulty level </label>
                    <select class="form-select" id="difficulty_level">
                        <option value=""></option>
                        <option value="1"> 1 - Low </option>
                        <option value="2"> 2 - Low-Moderate </option>
                        <option value="3"> 3 - Moderate </option>
                        <option value="4"> 4 - Moderate-High </option>
                        <option value="5"> 5 - High </option>
                    </select>
                </div>
                <div class="col-lg-2 my-2">
                    <label class="form-label"> Question Type </label>
                    <select class="form-select" id="question_type">
                        <option value=""></option>
                        <option value="SINGLE"> SINGLE </option>
                        <option value="MULTIPLE"> MULTIPLE </option>
                        <option value="NUMBER"> NUMBER </option>
                        <option value="MATCH"> MATCH </option>
                        <option value="PASSAGE_MULTIPLE"> PASSAGE MULTIPLE </option>
                        <option value="DESCRIPTIVE">Subjective Answer</option>
                    </select>
                </div>

                <div class="col-lg-2 my-2">
                    <label class="form-label">Options Type</label>
                    <select class="form-select" id="optionsType">
                        <option value="numeric">1) 2) 3) 4)</option>
                        <option value="letters">A) B) C) D)</option>
                    </select>
                </div>


            </div>
            <div class="row justify-content-center mt-2">
                <div class="col-lg-2 text-center">
                    <button type="button" id="add_questions" onclick="add_questions_div();" class="btn btn-primary"> Add Questions </button>
                </div>
            </div>



            <form action="" class="bulk_question_form" name="upload_questions_form" id="upload_questions_form" method="post" enctype="multipart/form-data">

                <div class="mt-4">
                    <input type="hidden" name="instituteId" id="instituteId" value="<?= $decrypted_institute_id; ?>" required />
                    <input type="hidden" name="subjectId" id="subjectId" value="<?= $decrypted_subject_id; ?>" required />
                    <input type="hidden" name="chapterId" id="chapterId" value="<?= $decrypted_chapter_id; ?>" required />
                    <input type="hidden" name="redirect" value="<?= $redirect; ?>" required />
                </div>

                <div id="questions_files_div">

                </div>

            </form>


            <!-- Uploaded Question View -->
            <div id="showQuestionUploadDiv">

            </div>

        </div>
    </div>
</div>



<div class="modal fade" id="imageSizeModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Error in images</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
                    <lottie-player src="<?= base_url('assets/img/animations/7877-uploading-to-cloud.json'); ?>" background="transparent" speed="1" style="width: 200px; margin:auto;" loop autoplay></lottie-player>
                    <label>Uploading... Please wait</label>
                </div>
                <div class="text-center" id="uploading-finished-div">
                    <img src="https://edofox-s3.s3.ap-south-1.amazonaws.com/public/statics/checked-circle.png" />
                    <br>
                    <label>Done!</label>
                    <p><label>You can review the added questions in Question Bank section.</label></p>
                </div>
            </div>
            <div class="modal-footer" id="imageUploadingProgressModalFooter">
                <a class='btn btn-info' href="<?= base_url('questionBank/chapter_questions/' . $subject_id . '/' . $decrypted_chapter_id); ?>"> Question Bank </a>
                <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


<!-- Include Footer -->
<?php include_once(APPPATH . "Views/footer.php"); ?>

<script>
    function add_questions_div() {
        noQues = $("#no_of_questions").val();
        quetionType = $("#question_type").val();
        difficulty_level = $("#difficulty_level").val();
        optionsType = $("#optionsType").val();
        var request = {
            "noQues": noQues,
            "quetionType": quetionType,
            "difficulty_level": difficulty_level,
            "optionsType": optionsType
        };
        $.ajax({
            url: base_url + "/questionBank/get_question_bank_uploads",
            method: 'POST',
            // dataType: 'json',
            contentType: 'application/json',
            data: JSON.stringify(request),
            success: function(response) {
                $('#questions_files_div').html(response);
            }
        });

    }
</script>

<script>
    function checkNumberOfImages() {
        var questions_files = document.getElementById("questions_files").files;
        var noQues = document.getElementById("no_of_questions").value;
        var add_bulk_questions_submit_button = document.getElementById("add_bulk_questions_submit_button");

        var showImageSizeModal = false;

        if (questions_files.length != noQues) {
            alert("The number of questions images you selected should be equal to the number of questions you entered above.");
            add_bulk_questions_submit_button.style.display = "none";
            return;
        } else {
            var errorImages = [];
            var maxImageSizeForValidation = 200; // in KB
            //Check the size of each image
            for (var i = 0; i < questions_files.length; i++) {
                var imageSize = questions_files[i].size / 1024;
                //Making sure the images are less than strict upper limit
                if (questions_files[i].size / 1024 > maxImageSizeForValidation) {
                    //Show the values in a modal of while file the user needs to compress
                    showImageSizeModal = true;
                    errorImages.push(questions_files[i].name + " (" + Number(imageSize.toPrecision(5)) + "KB)");
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
</script>


<script>
    var uploadImages = function(event) {
        console.log("Preventing submit!");
        event.preventDefault();
        var question_types = document.getElementsByName('question_types[]');
        var difficulty_levels = document.getElementsByName('difficulty_levels[]');
        var question_solution_checks = document.getElementsByName('question_solution_check[]');
        var instituteId = $("#instituteId").val();
        var subjectId = $("#subjectId").val();
        var chapterId = $("#chapterId").val();

        var questions = [];
        var correctAnswers = document.getElementsByName('Que_correct_ans[]');
        var correctAnswersArray = [];
        correctAnswers.forEach(function(ansInput) {
            if (!ansInput.disabled) {
                correctAnswersArray.push(ansInput);
            }
        });

        for (var i = 0; i < question_types.length; i++) {

            var questionType = question_types[i].value;
            var difficultyLevel = difficulty_levels[i].value;
            var correctAns = null;
            if (correctAnswersArray[i] != null) {
                correctAns = correctAnswersArray[i].value;
            }
            var solutionCheck = question_solution_checks[i];
            var solutionCheckVal = 'N';
            if (solutionCheck.checked) {
                solutionCheckVal = 'Y';
            }
            var question = {
                level: difficultyLevel,
                correctAnswer: correctAns,
                type: questionType,
                weightage: 0,
                negativeMarks: 0,
                subjectId: subjectId,
                chapter: {
                    chapterId: chapterId
                },
                instituteId: instituteId,
                partialCorrection: 'N',
                solution: solutionCheckVal
            }

            questions.push(question);
        }

        var request = {
            requestType: "QuestionBank",
            institute: {
                id: instituteId
            },
            test: {
                test: questions,
                currentQuestion: {
                    instituteId: instituteId
                }
            }
        }

        console.log("Request", request);

        var formData = new FormData();

        // Upload Question Files
        var totalfiles = document.getElementById('questions_files').files.length;
        for (var index = 0; index < totalfiles; index++) {
            formData.append("files", document.getElementById('questions_files').files[index]);
        }

        // Upload Solution Files
        var solutionFiles = document.getElementById('solution_files').files.length;
        for (var index = 0; index < totalfiles; index++) {
            formData.append("solutionsFiles", document.getElementById('solution_files').files[index]);
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
                        url: rootAdmin + "uploadQuestionBank",
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
                                $("#uploading-finished-div").fadeIn("slow");
                                $('#uploading-div').hide();
                                // Snakbar Message
                                Snackbar.show({
                                    pos: 'top-center',
                                    text: 'Questions uploaded successfully'
                                });
                            } else if (data != null && data.status != null) {
                                alert("Error! " + data.status.responseText);
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
                alert("Exception: " + error);
            });

    };
</script>

<script>
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
    // To check/uncheck the solutions checkboxes in one go
    function check_uncheck_all_solution_checkboxes(element) {
        // console.log(element.value);
        const dropdown_value = element.value;

        if (dropdown_value === '1') {
            $('.question_solution_checkbox').prop('checked', true);
        }
        if (dropdown_value === '0') {
            $('.question_solution_checkbox').prop('checked', false);
        }
    }
</script>