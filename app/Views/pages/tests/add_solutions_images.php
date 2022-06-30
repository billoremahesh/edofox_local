<!-- Include Header -->
<?php include_once(APPPATH . "Views/header.php"); ?>
<style>
    .solution-image-found {
        border: 2px solid #5b51d8;
        margin: 8px 0px;
        border-radius: 4px;
        padding: 4px;
    }

    .no-image {
        border: 2px solid rgba(0, 0, 0, 0.1);
        margin: 8px 0px;
        padding: 4px;
        border-radius: 4px;
    }
</style>

<script>
    var testId = <?= $test_id  ?>;
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

        <div class="card p-4">
            <div style="text-align:right">


                <a class="btn btn-primary" href="<?= base_url('tests/update_test_questions/' . encrypt_string($test_id)); ?>">Check Questions in Test</a>

            </div>
            <div>
                <label class="text-muted"><b>Upload solution images in bulk</b></label>
            </div>

            <form id="upload_solutions_bulk" action="" method="post" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-2">
                            <label class="form-label">From Question Number</label>
                            <input class="form-control" type="number" id="from_question_no" name="from_question_no" placeholder="From Question" value="1" />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-2">
                            <label class="form-label">To Question Number</label>
                            <input class="form-control" type="number" id="to_question_no" name="to_question_no" placeholder="To Question" value="<?= $number_of_questions ?>" />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-2">
                            <label class="form-label">Section (Optional)</label>
                            <input class="form-control" type="text" id="section" name="section" placeholder="Section Name" value="" />
                        </div>
                    </div>
                </div>
                <br />
                <div class="row">
                    <div class="col-md-4">
                        <label class="form-label">Please select solution images</label>
                        <input type="file" name="solution_images_files[]" id="solution_images_files" multiple required onchange="checkNumberOfImages();">
                    </div>
                    <div class="col-xs-12 text-center mt-4">
                        <input type="hidden" name="test_id" value="<?= $test_id; ?>" />
                        <input type="hidden" name="institute_id" value="<?= $institute_id; ?>" />
                        <input type="submit" class="btn btn-success text-uppercase" id="btn_add_bulk_solutions_images_submit" value="Upload Bulk Solutions" name="add_bulk_solutions_images_submit">
                    </div>
                </div>
            </form>

            <hr style="margin: 48px auto;" />

            <div>
                <label class="text-muted"><b>OR Upload solution images one by one</b></label>
                <p class="text-muted small">(Questions with <span style="color:#5b51d8; font-weight: bold;">color border</span> have solutions uploaded)</p>
            </div>

            <form id="upload_solutions_form" action="" method="post" enctype="multipart/form-data">
                <?php
                for ($question_number = 1; $question_number <= $number_of_questions; $question_number++) {
                    $db = \Config\Database::connect();
                    $decrypted_test_id = $test_id;
                    $query_fetched_question = $db->query("SELECT test_questions.id, test_questions.correct_answer, test_questions.question_type, test_questions.solution_img_url
                    FROM test_questions_map
                    JOIN test_questions
                    ON test_questions.id=test_questions_map.question_id
                    WHERE test_questions_map.question_disabled = '0'
                    AND test_questions_map.test_id = '$decrypted_test_id' 
                    AND test_questions_map.question_number='$question_number'");
                    $result_fetched_question = $query_fetched_question->getRowArray();
                    $question_id = 0;
                    $correct_answer = $question_type = $solution_img_url = "";
                    $number_of_q_in_a_row = 4;
                    if (!empty($result_fetched_question)) :
                        $question_id = $result_fetched_question['id'];
                        $correct_answer = $result_fetched_question['correct_answer'];
                        $question_type = $result_fetched_question['question_type'];
                        $solution_img_url = $result_fetched_question['solution_img_url'];
                    endif;

                    if ($question_type == NULL) {
                        $question_type = "SINGLE";
                    }

                    if ($question_number == 1 || (($question_number - 1) % $number_of_q_in_a_row == 0)) {
                        echo "<div class='row' style='padding: 8px 0;border-bottom: 3px solid #fafafa;'>";
                    }
                ?>
                    <div class="col-md-3">
                        <div id="que<?= $question_id ?>" class="row <?php echo (isset($row_fetched_question['solution_img_url']) && !empty($row_fetched_question['solution_img_url'])) ? 'solution-image-found' : 'no-image'; ?>">
                            <input type="hidden" id="question_id" name="question_id[]" value="<?php echo $question_id; ?>" required />
                            <div class="col-xs-2">
                                <label><?= $question_number; ?>:</label>
                            </div>
                            <div class="col-xs-10">
                                <input type="file" class="solution_img_files" id="solution_img_file" name="solution_img_file[]" />
                            </div>
                        </div>
                    </div>
                <?php
                    if ($question_number % $number_of_q_in_a_row == 0 || $question_number == $number_of_questions) {
                        echo "</div>";
                    }
                } ?>

                <div class="text-center">
                    <input type="submit" class="btn btn-success" value="Upload Solution Images" name="add_onebyone_solutions_images_submit">
                </div>
            </form>
        </div>

    </div>



    <div class="modal fade" id="imageSizeModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <h6 class="modal-title">Error in images <i class="fa fa-exclamation-triangle" aria-hidden="true"></i></h6>
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
                    <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
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
                        <p><label>You can review the added solutions in Check/Update Questions in Test section.</label></p>
                    </div>
                </div>
                <div class="modal-footer" id="imageUploadingProgressModalFooter">

                    <a class='btn btn-primary' href="<?= base_url('tests/update_test_questions/' . $test_id); ?>"> Check Questions in Test </a>

                    <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

</div>


<!-- Include Footer -->
<?php include_once(APPPATH . "Views/footer.php"); ?>

<script>
    function updateQuestionBorders(questions) {
        if (questions != null) {
            questions.forEach(element => {
                if (element.id != null) {
                    $("#que" + element.id).attr("class", "row solution-image-found");
                }
            });
        }
    }

    var uploadImagesBulk = function(event) {
        console.log("Preventing submit!");
        event.preventDefault();

        if ($("#from_question_no").val() == '' || $("#to_question_no").val() == '') {
            alert("Offset or range is not provided.")
            return;
        }

        var offset = parseInt($("#from_question_no").val());
        var to = parseInt($("#to_question_no").val());
        var section = $("#section").val();

        var questions = [];

        var formData = new FormData();


        for (var index = offset; index <= to; index++) {
            var question = {
                questionNumber: offset
            }
            if (section != null && section != '') {
                question.section = section;
            }
            questions.push(question);
            offset++;

        }


        var totalfiles = document.getElementById('solution_images_files').files.length;

        if (totalfiles == 0) {
            alert("Please select files to upload");
            return;
        }

        for (var index = 0; index < totalfiles; index++) {
            formData.append("files", document.getElementById('solution_images_files').files[index]);
        }

        var request = {
            test: {
                id: testId,
                test: questions
            },
            requestType: 'Solution'
        }


        //formData.append('files', files.value);
        formData.append('request', JSON.stringify(request));

        // Showing uploading loader
        $('#imageUploadingProgressModal').modal({
            backdrop: 'static',
            keyboard: false
        });
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
                                //$("#offset").val(offset);
                                $("#uploading-finished-div").fadeIn("slow");
                                $('#uploading-div').hide();
                                updateQuestionBorders(data.test.test);
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



    var form = document.getElementById("upload_solutions_bulk");

    // attach event listener
    form.addEventListener("submit", uploadImagesBulk, true);

    var uploadSingle = function(event) {
        console.log("Preventing submit!");
        console.log("Uploading files one by one...");
        showImageSizeModal = false;
        var errorImages = [];
        var maxImageSizeForValidation = 200; // in KB

        event.preventDefault();

        //var files = document.getElementsByName('solution_img_file[]');

        var questionList = document.getElementsByName("question_id[]");

        var questions = [];

        var formData = new FormData();

        $('input[name="solution_img_file[]"]').each(function(index, value) {
            var file = value.files[0];
            if (file) {

                var imageSize = file.size / 1024

                // Making sure the images are less than strict upper limit
                if (file.size / 1024 > maxImageSizeForValidation) {
                    // Show the values in a modal of while file the user needs to compress
                    showImageSizeModal = true;
                    errorImages.push(file.name + " (" + Number(imageSize.toPrecision(5)) + "KB)");
                }

                formData.append("files", file);
                console.log("Adding file for " + index);
                questions.push({
                    id: questionList[index].value
                });
            }
        });

        if (questions.length == 0) {
            alert("Please select files to upload");
            return;
        }


        console.log("showImageSizeFlag: " + showImageSizeModal);
        if (showImageSizeModal) {
            //Show the modal with the data
            // Bootstrap 5 Modal show issue
            // https://stackoverflow.com/questions/62827002/bootstrap-v5-manually-call-a-modal-mymodal-show-not-working-vanilla-javascrip
            var imageSizeModal = new bootstrap.Modal(document.getElementById("imageSizeModal"), {});
            imageSizeModal.show();

            $("#listOfErrorImages").html("");
            $("#imageSizeText").html(maxImageSizeForValidation);
            for (var i = 0; i < errorImages.length; i++) {
                $("#listOfErrorImages").append("<li><b>" + errorImages[i] + "</b></li>")
            }

            return;
        }

        var request = {
            test: {
                id: testId,
                test: questions
            },
            requestType: 'Solution'
        }

        formData.append('request', JSON.stringify(request));


        // Showing uploading loader
        $('#imageUploadingProgressModal').modal({
            backdrop: 'static',
            keyboard: false
        });
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
                                //$("#offset").val(offset);
                                $("#uploading-finished-div").fadeIn("slow");
                                $('#uploading-div').hide();
                                //Update borders
                                updateQuestionBorders(questions);
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


    var singleForm = document.getElementById("upload_solutions_form");
    singleForm.addEventListener("submit", uploadSingle, true);

    console.log("Added form submit listener");

    function checkNumberOfImages() {
        var solution_images_files = document.getElementById("solution_images_files").files;
        var noQues = parseInt($("#to_question_no").val()) - parseInt($("#from_question_no").val()) + 1; // added 1 for exact no of questions

        var showImageSizeModal = false;
        // console.log("Images uploaded: " + solution_images_files.length + "... Questions in range: " + noQues);
        // console.log("Images uploaded: ", solution_images_files);

        if (solution_images_files.length != noQues) {
            alert("The number of solution images you selected should be equal to the number of questions in the range you entered above. \nYou uploaded " + solution_images_files.length + " files. No of questions =" + noQues);
            $("#btn_add_bulk_solutions_images_submit").hide();
            $("#solution_images_files").val("");
            return;
        } else {
            var errorImages = [];
            var maxImageSizeForValidation = 200; // in KB
            //Check the size of each image
            for (var i = 0; i < solution_images_files.length; i++) {
                var imageSize = solution_images_files[i].size / 1024
                // console.log(imageSize, solution_images_files[i].name);

                //Making sure the images are less than strict upper limit
                if (solution_images_files[i].size / 1024 > maxImageSizeForValidation) {
                    //Show the values in a modal of while file the user needs to compress
                    showImageSizeModal = true;
                    errorImages.push(solution_images_files[i].name + " (" + Number(imageSize.toPrecision(5)) + "KB)");
                }

            }



            if (showImageSizeModal) {
                //Show the modal with the data
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
                $("#btn_add_bulk_solutions_images_submit").hide();
                $("#solution_images_files").val("");

                return;
            }


            $("#btn_add_bulk_solutions_images_submit").show();

        }
    }
</script>