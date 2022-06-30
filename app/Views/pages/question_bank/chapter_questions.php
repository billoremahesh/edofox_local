<!-- Include Header -->
<?php include_once(APPPATH . "Views/header.php"); ?>

<!-- Custom CSS -->
<link href="<?php echo base_url('assets/css/question_bank/overview.css?v=202112061722'); ?>" rel="stylesheet">

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
                    <li class="breadcrumb_item"><a href="<?php echo base_url('questionBank'); ?>"> Question Bank </a></li>
                    <li class="breadcrumb_item active" aria-current="page"> <?= $title; ?> </li>
                </ol>
            </div>
        </div>

        <div class="d-flex justify-content-end">

            <div class="dropdown m-2">
                <button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                    Add Questions
                </button>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                    <li><a class="dropdown-item" href="<?= base_url('questionBank/add_bulk_questions/' . $subject_id . '/' . encrypt_string($chapter_id)); ?>">Add Bulk Questions from Images</a></li>
                    <li><a class="dropdown-item" href="<?= base_url('questionBank/bulk_pdf_parse/' . $subject_id . '/' . encrypt_string($chapter_id)); ?>">Add Questions by Parsing PDF</a></li>
                </ul>
            </div>


            <button class="btn btn-link btn-sm m-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasChapterList" aria-controls="offcanvasChapterList"> Change Chapter </button>

        </div>


        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasChapterList" aria-labelledby="offcanvasChapterListLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title m-2" id="offcanvasChapterListLabel">Chapters</h5>

                <button type="button" class="btn text-white" data-bs-dismiss="offcanvas" aria-label="Close">
                    <span class="material-icons text-white-50">close</span>
                </button>
            </div>

            <div class="offcanvas-body p-0">
                <?php if (!empty($chapters_data)) : ?>
                    <div class="list-group list-group-flush mb-4">

                        <?php
                        foreach ($chapters_data as $chapter) :
                        ?>
                            <a href="<?= base_url('questionBank/chapter_questions/' . $subject_id . '/' . $chapter['id']); ?>" class="list-group-item list-group-item-action"><?= $chapter['chapter_name']; ?></a>
                        <?php endforeach; ?>

                    </div>
                <?php else : ?>
                    <h4 class="text-center">No questions found. Start by adding some questions.</h4>
                <?php endif; ?>

            </div>
        </div>


        <div class="bg-white shadow rounded p-4 mb-5">


            <h5 class="text-center ">
                <?= $subject_detail['subject'] . ": " . $chapter_detail['chapter_name']; ?>
            </h5>

            <hr />

            <div class="row justify-content-center">

                <input type="hidden" id="chapterId" value="" />
                <input type="hidden" id="subjectId" value="<?= $decrypt_subject_id; ?>" />
                <input type="hidden" id="instituteId" value="<?= $institute_id; ?>" />

                <div class="col-md-2">

                    <label for="question_difficulty_filter" class="form-label"> Difficulty level </label>
                    <select class="form-select filter_data" onchange="load_chapter_questions(0);" id="question_difficulty_filter">
                        <option value="">Select Difficulty Level</option>
                        <option value="1"> 1 - Low </option>
                        <option value="2"> 2 - Low-Moderate </option>
                        <option value="3"> 3 - Moderate </option>
                        <option value="4"> 4 - Moderate-High </option>
                        <option value="5"> 5 - High </option>
                    </select>

                </div>
                <div class="col-md-2">

                    <label for="question_type_filter" class="form-label"> Question Type </label>
                    <select class="form-select filter_data" onchange="load_chapter_questions(0);" id="question_type_filter">
                        <option value="">Select Question Type</option>
                        <option value="SINGLE"> SINGLE </option>
                        <option value="MULTIPLE"> MULTIPLE </option>
                        <option value="NUMBER"> NUMBER </option>
                        <option value="MATCH"> MATCH </option>
                        <option value="PASSAGE_MULTIPLE"> PASSAGE MULTIPLE </option>
                        <option value="DESCRIPTIVE">Subjective Answer</option>
                    </select>

                </div>

                <div class="col-md-2">
                    <label for="verifiedQuesFilter" class="form-label"> Verified/Unverified </label>
                    <select class="form-select filter_data" onchange="load_chapter_questions(0);" id="verifiedQuesFilter">
                        <option value="All"> Show Verified+Unverified </option>
                        <option value="Verified"> Show Verified Only </option>
                        <option value="Unverified"> Show Unverified Only</option>
                    </select>
                </div>

            </div>

        </div>


        <div id="questions_div"></div>
        <div id="custom_loader"></div>


        <div class="text-center my-4" style="display:none;" id="load_more_btn_div">
            <button class="btn btn-outline-secondary btn-sm" onclick="load_chapter_questions();">
                Load More
            </button>
        </div>

        <div class="text-center my-4" id="question_result_message">

        </div>
    </div>

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

<!-- Include Footer -->
<?php include_once(APPPATH . "Views/footer.php"); ?>
<script src="<?php echo base_url('assets/js/manage_questions.js'); ?>"></script>
<script src="<?php echo base_url('assets/js/img_replace.js'); ?>"></script>

<script>
    $(document).ready(function() {
        load_chapter_questions();
    });
</script>

<script>
    var last_record = 0;
    // To change the content list on click via ajax
    function load_chapter_questions(lastIndex) {

        if (lastIndex >= 0) {
            last_record = lastIndex;
        }
        if(last_record == 0){
            $('#questions_div').html("");
        }

        if (last_record < 0) {
            return;
        }

        var chapter_id = "<?= $chapter_id; ?>";
        // Saving the ids in session for fetching clicked chapters content
        sessionStorage.setItem("chapter_id", chapter_id);

        // Setting the chapter id field in add new resource modal for dynamic addition
        $("#chapterId").val(chapter_id);


        toggle_custom_loader(true, "custom_loader");
        document.getElementById('load_more_btn_div').style.display = 'none';


        get_admin_token().then(function(result) {
                var resp = JSON.parse(result);
                if (resp != null && resp.status == 200 && resp.data != null && resp.data.admin_token != null) {
                    var request = getRequest(last_record);
                    console.log("inside load questions");
                    console.log(request);
                    $.ajax({

                        url: rootAdmin + "loadQuestionBank",
                        method: 'POST',
                        beforeSend: function(request) {
                            request.setRequestHeader("AuthToken", resp.data.admin_token);
                        },
                        dataType: 'json',
                        contentType: 'application/json',
                        data: JSON.stringify(request),
                        success: function(result) {
                            toggle_custom_loader(false, "custom_loader");
                            // console.log("Result ==>", result);
                            if (result != null && result.test != null && result.test.test != null && result.test.test.length > 0) {
                                // old code formatted using javascript
                                // $('#questions_div').html(getResponse(result.test));
                                last_record = last_record + result.test.test.length;
                                console.log(last_record);

                                $('#questions_div').append(getResponse(result.test));
                                initializeTooltip();
                                document.getElementById('load_more_btn_div').style.display = 'block';


                            } else {
                                if (last_record > 0) {
                                    $("#question_result_message").html("No more questions");
                                } else {
                                    $("#question_result_message").html("No questions available for this chapters");
                                }

                                last_record = -1;
                            }
                        }
                    });

                } else {
                    console.log("Some error authenticating your request. Please clear your browser cache and try again.");
                }
            })
            .catch(function(error) {
                console.log("Error ==>", error);
            });


    }
</script>



<script>
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

    //To asynchronously update the question type from dropdown
    function updateQuestionType(question_id, updatedSectionInputElement) {
        var updatedSection = updatedSectionInputElement.value;
        var dataString = 'update=qType' + '&newType=' + encodeURIComponent(updatedSection) + '&question_id=' + question_id;
        $.ajax({
            type: 'POST',
            data: dataString,
            url: base_url + '/tests/update_test_question_properties',
            success: function(data) {
                //Show the text format and hide the input 
                $(updatedSectionInputElement).siblings().toggleClass('d-none');
                $(updatedSectionInputElement).siblings().text(updatedSectionInputElement.value);
                $(updatedSectionInputElement).toggleClass('d-none');

                // Snakbar Message
                Snackbar.show({
                    pos: 'top-center',
                    text: data
                });
                // It is required for question anwser div based on this field
                window.location.reload(true);

            }
        });
    }

    //To asynchronously update the question difficulty level from dropdown
    function updateQuestionDifficulty(question_id, updatedDifficultyDropdownElement) {
        var updatedSection = updatedDifficultyDropdownElement.value;
        var dataString = 'update=level' + '&newDiffi=' + encodeURIComponent(updatedSection) + '&question_id=' + question_id;
        $.ajax({
            type: 'POST',
            data: dataString,
            url: base_url + '/tests/update_test_question_properties',
            success: function(data) {
                //Show the text format and hide the input 
                $(updatedDifficultyDropdownElement).siblings().toggleClass('d-none');
                $(updatedDifficultyDropdownElement).siblings().text(updatedDifficultyDropdownElement.value);
                $(updatedDifficultyDropdownElement).toggleClass('d-none');
                // Snakbar Message
                Snackbar.show({
                    pos: 'top-center',
                    text: data
                });

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

        var dataString = 'update=correctAnswerText' + '&newText=' + encodeURIComponent(updatedCorrectAnswerText) + '&question_id=' + question_id;
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

                // Snakbar Message
                Snackbar.show({
                    pos: 'top-center',
                    text: data
                });

            }
        });
    }

    // Verify Question
    function verifyQuestion(question_id) {
        var dataString = 'update=verifyQuestion' + '&question_id=' + question_id;
        $.ajax({
            type: 'POST',
            data: dataString,
            url: base_url + '/tests/update_test_question_properties',
            success: function(data) {
                // Snakbar Message
                Snackbar.show({
                    pos: 'top-center',
                    text: data
                });
                // It is required for question anwser div based on this field
                window.location.reload(true);
            }
        });
    }
</script>

<script>
    var selectedQuestion = null;

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
        // console.log("File:", uploadedImage);
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
                        success: function(response) {
                            if (response.status.statusCode == 200) {
                                Snackbar.show({
                                    pos: 'top-center',
                                    text: 'Question Image updated successfully'
                                });

                                // Replace the existing image with new image source
                                $("#question_card_" + selectedQuestion + " .question-image-tag").attr("src", uploadedImage);
                            } else {
                                console.log(response.status.responseText);
                                Snackbar.show({
                                    pos: 'top-center',
                                    text: response.status.responseText
                                });
                            }
                            selectedQuestion = null;
                        }
                    });
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
        // console.log("File:", uploadedImage);
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
                            // console.log("solution image response for " + selectedQuestion, response);
                            if (response.status.statusCode == 200) {
                                Snackbar.show({
                                    pos: 'top-center',
                                    text: 'Solution image updated successfully'
                                });

                                // Replace the existing image with new image source
                                $("#question_card_" + selectedQuestion + " .solution-image-tag").attr("src", uploadedImage);
                            } else {
                                console.log(response.status.responseText);
                                Snackbar.show({
                                    pos: 'top-center',
                                    text: response.status.responseText
                                });
                            }
                            selectedQuestion = null;

                        }
                    });
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