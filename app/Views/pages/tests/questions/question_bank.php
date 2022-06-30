<!-- Include Header -->
<?php include_once(APPPATH . "Views/header.php"); ?>

<!-- Custom CSS -->
<link href="<?php echo base_url('assets/css/tests/realtime_overview.css?v=20210915'); ?>" rel="stylesheet">
<link href="<?php echo base_url('assets/css/tests/import_bulk_questions.css?v=20210915'); ?>" rel="stylesheet">

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

        <div style="text-align:right">

            <a class="btn btn-link" href="<?= base_url('tests/update_test_questions/' . $test_id); ?>">Check Questions in Test</a>

        </div>

        <div class="container-fluid bg-white shadow rounded p-3 mb-3">
            <div class="card_box">

                <div class="row justify-content-center">
                    <div class="col-2">
                        <div class="mb-2">
                            <label class="form-label"> Subjects </label>
                            <select class="form-control filter_data" id="subject_filter" onchange="getChapters(this.value)">
                                <option value=""></option>
                                <?php foreach ($subjects_list as $row) :
                                    echo "<option value=" . $row['subject_id'] . ">" . $row['subject'] . "</option>";
                                endforeach;
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="mb-2">
                            <label class="form-label"> Chapters </label>
                            <select class="form-control filter_data" id="chapters_dropdown_filter">
                                <option value=""></option>
                            </select>
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="mb-2">
                            <label class="form-label"> Difficulty level </label>
                            <select class="form-control filter_data" id="test_difficulty_filter">
                                <option value=""></option>
                                <option value="1"> 1 - Low </option>
                                <option value="2"> 2 - Low-Moderate </option>
                                <option value="3"> 3 - Moderate </option>
                                <option value="4"> 4 - Moderate-High </option>
                                <option value="5"> 5 - High </option>
                            </select>
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="mb-2">
                            <label class="form-label"> Question Type </label>
                            <select class="form-control filter_data" id="question_type_filter">
                                <option value=""></option>
                                <option value="SINGLE"> SINGLE </option>
                                <option value="MULTIPLE"> MULTIPLE </option>
                                <option value="NUMBER"> NUMBER </option>
                                <option value="MATCH"> MATCH </option>
                                <option value="PASSAGE_MULTIPLE"> PASSAGE MULTIPLE </option>
                                <option value="DESCRIPTIVE">Subjective Answer</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="mb-2">
                            <label class="form-label"> My Question bank </label>
                            <input type="checkbox" id="my_q_bank" name="my_q_bank" value="Y">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <i class='fas fa-atom fa-spin fa-2x fa-fw' id="loading_img_custom"></i>

                </div>
                <div class="text-center" id="selected_questions_count_div" style="margin: 8px auto;" hidden>
                    <span style="font-size: 16px; border-radius: 4px; border: 2px solid #2196f3; padding: 8px;"><span id="selected_questions_count" style="color: #2196f3; font-weight: bold">0</span> questions selected</span>
                </div>
                <div id="empty_option_div"></div>

                <hr/>
                
                <div class="d-none" id="questions_div"></div>

                <div style="margin-top: 16px;text-align:center;" class="d-none" id="load_more_btn_div">
                    <button class="btn btn-secondary" onclick="load_more_questions();">
                        Load More
                    </button>
                    <button class="btn btn-warning" onclick="checkAll('questions_div', true);" class="btn btn-secondary"><i class="fa fa-check-square-o"></i> Check All</button>
                    <button class="btn btn-danger" onclick="checkAll('questions_div', false);" class="btn btn-secondary"><i class="fa fa-square-o"></i> Uncheck All</button>
                </div>
                <input type="hidden" name="test_id" id="test_id" value="<?php echo $test_id; ?>" />
                <div style="margin-top: 16px; text-align: center;" class="d-none" id="submit_div">
                    <button class="custom_submit_btn" name="import_questions_submit" type="button" onclick="addModal()" disabled>
                        <i class="fa fa-cloud-download"></i> Import <span id="import_button_selected_count">0</span> questions
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>

<div id="addQuestionsModal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">

            <div class="modal-header">
                <h6 class="modal-title">
                    Import questions to <?= $test_details['test_name']; ?>
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <p id="importDetails"></p>
                <div class="form-group">
                    <label for="section">Section:</label>
                    <input type="text" class="form-control" id="section">
                </div>
                <div class="form-group">
                    <label for="section">Weightage:</label>
                    <input type="number" class="form-control" id="weightage">
                </div>
                <div class="form-group">
                    <label for="section">Negative marks:</label>
                    <input type="number" class="form-control" id="negative_marks">
                </div>
                <div class="form-group">
                    <label for="section">Add from Question number (offset):</label>
                    <input type="number" class="form-control" id="question_number" placeholder="By Default last question in the exam">
                    <p class="text-muted">By Default, the question numbers will be inserted after the latest previous question in the exam </p>
                </div>

                <div class="form-group">
                    <p style="color:red" id="import_error"></p>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" onclick="addQuestions()">Add</button>
            </div>
        </div>

    </div>
</div>

<!-- Include Footer -->
<?php include_once(APPPATH . "Views/footer.php"); ?>

<script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
<script>
    MathJax = {
        tex: {
            inlineMath: [
                ['$', '$'],
                ['\\(', '\\)']
            ]
        },
        startup: {
            ready: function() {
                MathJax.startup.defaultReady();
                document.getElementById('render').disabled = false;
            }
        }
    }
</script>
<script id="MathJax-script" defer src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>

<script>
    function applyMathJax(input, spanId) {
        output = document.getElementById(spanId);
        if (output == null) {
            // console.log("returning .." + spanId);
            return;
        }
        if (input == null || input.trim().length == 0) {
            output.innerHTML = "";
            return;
        }
        output.innerHTML = input;

        console.log("Reset math jax ..");
        MathJax.texReset();
        MathJax.typesetClear();
        MathJax.typesetPromise()
            .catch(function(err) {
                console.log("Error -- " + err.message);
            })
            .then(function() {
                // console.log("Done adding to == > " + spanId);
            });
    }


    function appendMathJax(input, spanId) {
        output = $("#"+spanId);
        if (output == null) {
            // console.log("returning .." + spanId);
            return;
        }
        if (input == null || input.trim().length == 0) {
            output.append("");
            return;
        }
        output.append(input);

        console.log("Reset math jax ..");
        MathJax.texReset();
        MathJax.typesetClear();
        MathJax.typesetPromise()
            .catch(function(err) {
                console.log("Error -- " + err.message);
            })
            .then(function() {
                // console.log("Done adding to == > " + spanId);
            });
    }

</script>

<script>
    $(document).ready(function() {
        $('#loading_img_custom').hide();

        $('#root').val(root);

        $("#chapters_dropdown_filter").change(function() {
            if (confirmFilterChange()) {
                get_questions_div();
            }
        });

        $("#test_difficulty_filter").change(function() {
            if (confirmFilterChange()) {
                get_questions_div();
            }
        });

        $("#question_type_filter").change(function() {
            if (confirmFilterChange()) {
                get_questions_div();
            }
        });

        $("#partial_marking_filter").change(function() {
            if (confirmFilterChange()) {
                get_questions_div();
            }
        });

        $("#my_q_bank").change(function() {
            if (confirmFilterChange()) {
                get_questions_div();
            }
        });

    });

    function addModal(subject_id) {

        //Fetch checked questions
        var checkedQuestions = 0;
        $('.chkboxQues_append:checkbox:checked').each(function() {
            var sThisVal = (this.checked ? $(this).val() : "");
            // console.log("Checked question => " + sThisVal);
            checkedQuestions++;
        });

        $("#importDetails").text("Import " + checkedQuestions + " selected questions into the exam");
        $("#addQuestionsModal").modal('show');

    }

    function addQuestions(subject_id) {

        if (!$("#section").val()) {
            $("#import_error").text("Please select a section where these questions will be added to");
            $("#section").focus();
            return;
        }
        if (!$("#weightage").val() || parseInt($("#weightage").val()) < 0) {
            $("#import_error").text("Please select a weightage to assign to these questions greater than 0");
            $("#weightage").focus();
            return;
        }
        if (!$("#negative_marks").val() || parseInt($("#negative_marks").val()) < 0) {
            $("#import_error").text("Please select negative marks to assign to these questions greater than 0");
            $("#negative_marks").focus();
            return;
        }


        // Question number offset validation in js
        // console.log("$(#question_number).val()", $("#question_number").val());
        var question_number = parseInt($("#question_number").val());
        // console.log("parseint question_number", question_number);
        // console.log("question_number < 0", parseInt(question_number) < 0);
        // console.log("typeof question_number", typeof question_number);

        if (Number.isInteger(question_number) && question_number <= 0) {
            $("#import_error").text("Please select Add From Question Number greater than 0");
            $("#question_number").focus();
            return;
        }
        if (isNaN(question_number)) {
            Snackbar.show({
                pos: 'top-center',
                text: 'Add From Question Number field is not a number. Saving next question number by default.'
            });
            question_number = "";
        }

        //Fetch checked questions
        var checkedQuestions = [];
        $('.chkboxQues_append:checkbox:checked').each(function() {
            var sThisVal = (this.checked ? $(this).val() : "");
            // console.log("Checked question => " + sThisVal);
            checkedQuestions.push(sThisVal);
        });

        if (checkedQuestions.length == 0) {
            $("#import_error").text("Please select at least one question");
            return;
        }

        $("#import_error").text("");
        $('#loading_img_custom').show();
        test_id = "<?php echo $test_id; ?>";
        institute_id = "<?php echo $instituteID; ?>";
        var form_data = {
            test_id: test_id,
            chkboxQues_append: checkedQuestions,
            weightage: $("#weightage").val(),
            negativeMarks: $("#negative_marks").val(),
            section: $("#section").val(),
            questionNumber: question_number
        };
        $.ajax({
            data: form_data,
            type: 'POST',
            url: base_url + '/tests/import_bulk_questions_submit',
            success: function(result) {
                $('#loading_img_custom').hide();
                // console.log("Result of add ==>", result);
                $("#addQuestionsModal").modal('hide');
                if (result.trim() == true) {

                    Snackbar.show({
                        pos: 'top-center',
                        text: 'Added questions successfully!'
                    });

                    get_questions_div();
                } else {


                    Snackbar.show({
                        pos: 'top-center',
                        text: 'Failed to add questions'
                    });

                }

            }
        });
    }

    function addMathJax() {
        MathJax.texReset();
        MathJax.typesetClear();
        MathJax.typesetPromise()
            .catch(function(err) {
                //
                //  If there was an internal error, put the message into the output instead
                //
                //output.innerHTML = '';
                //output.appendChild(document.createElement('pre')).appendChild(document.createTextNode(err.message));
                console.log("Error -- " + err.message);
            })
            .then(function() {
                // console.log("Done adding to == > " + spanId);
            });
    }
</script>

<script>
    function validate_checkbox() {
        var chks = document.getElementsByName('chkboxQues_append[]');
        var hasChecked = false;
        for (var i = 0; i < chks.length; i++) {
            if (chks[i].checked) {
                hasChecked = true;
                break;
            }
        }
        if (hasChecked == false) {
            var elem1 = document.getElementById('empty_option_div');
            elem1.style.color = '#F5484F';
            elem1.style.padding = '8px';

            $("#empty_option_div").html("Please select at least one option.");
            return false;
        }
        return true;
    }
</script>


<script>
    //To check whether any questions are selected or not
    function checkQuestionsSelected() {
        if ($('.chkboxQues_append:checkbox:checked').length < 1) {
            // No questions selected
            return false;
        } else {
            // At least one question selected
            return true;
        }
    }

    function confirmFilterChange() {
        if (checkQuestionsSelected()) {
            var result = confirm("You have selected questions. Click OK to change filter value and clear selected questions. Click Cancel if you want to import selected questions first.");
            if (result) {
                return true;
            } else {
                return false;
            }
        }
        return true;
    }

    function getChapters(subject_id) {
        if (confirmFilterChange()) {
            $('#loading_img_custom').show();
            test_id = "<?php echo $test_id; ?>";
            institute_id = "<?php echo $instituteID; ?>";
            var form_data = {
                subject_id: subject_id,
                institute_id: institute_id
            };
            $.ajax({
                type: 'GET',
                url: base_url + '/tests/append_test_chapters/' + subject_id,
                success: function(result) {
                    $('#loading_img_custom').hide();
                    document.getElementById("chapters_dropdown_filter").innerHTML = result;
                    get_questions_div();
                    calculateCheckedQuestionsCount();
                }
            });

        }

    }


    function getRequest(last_record) {
        var chapterId = null,
            level = null,
            type = null,
            lastIndex = 0;
        //if($("#chapters_dropdown_filter").val() != null) {
        chapterId = $("#chapters_dropdown_filter").val();
        //}
        //if($("#test_difficulty_filter").val() != null) {
        level = $("#test_difficulty_filter").val();
        //}
        //if($("#question_type_filter").val() != null) {
        type = $("#question_type_filter").val();
        //}
        if (last_record) {
            lastIndex = last_record;
        }

        var instituteId = null;
        if ($("#my_q_bank").prop("checked") == true) {
            //instituteId = parseInt(instituteId);
            instituteId = '<?= $instituteID ?>';
        }

        var request = {
            question: {
                chapter: {
                    chapterId: chapterId
                },
                level: level,
                type: type,
                subjectId: $('#subject_filter').val(),
                instituteId: instituteId,
                lastIndex: lastIndex,
                qn_id: '<?= $decrypt_test_id ?>'
            }
        }

        // console.log("request", request);
        return request;

    }


    var last_record = 0;

    function getResponse(test) {

        var html = "";
        if (test != null && test.test.length > 0) {
            last_record = test.test.length;
            test.test.forEach(function(q) {
                html = html + " <div style='display:flex;flex-direction:column; padding: 8px;'> <label> <div style='display:flex;flex-direction:row;'> " +
                    "<div> <input type='checkbox' class='chkboxQues_append' name='chkboxQues_append[]' value='" + q.id + "' /> </div> ";

                html = html + "<div class='question_check_div'>";
                if (q.question != null) {
                    html = html + "<div class='ques_div'>" + q.question + "</div>";
                }
                if (q.option1 != null && q.option1 != "") {
                    html = html + "<div class='ques_div'> 1) " + q.option1 + " 2) " + q.option2 + " 3) " + q.option3 + " 4) " + q.option4 + "</div>";
                }
                if (q.correctAnswer != null && q.correctAnswer != "") {
                    html = html + "<div class='ques_div' style='color:green'> Correct: " + q.correctAnswer + "</div>";
                }
                if (q.questionImageUrl != null && q.questionImageUrl != "") {
                    html = html + "<div class='ques_img_div'><img src='" + q.questionImageUrl + "' alt='no-image'class='img-fluid ques_imgs' style='max-width: 800px;' alt='no-image' /></div>";
                }
                html = html + "<div class='ques_desc'>Question Type: " + q.type + "</div></div></div></label></div>";
            });

        } else {
            html = html + "<div class='text-danger text-center m-3 fw-bold'>No questions found matching selected filters.</div>";

            $('#submit_div').addClass("d-none");
            $('#load_more_btn_div').addClass("d-none");
        }

        return html;

    }

    function get_questions_div() {

        $('#questions_div').addClass("d-none");

        //Load tokens first
        get_admin_token().then(function(result) {
                var resp = JSON.parse(result);
                if (resp != null && resp.status == 200 && resp.data != null && resp.data.admin_token != null) {
                    var request = getRequest();
                    // console.log(request);
                    $('#questions_div').html("<i class='fas fa-atom fa-spin fa-2x fa-fw'></i><span class='sr-only'>Loading...</span>");
                    $.ajax({
                        //url: "sql_operations/load_questions_data.php",
                        url: rootAdmin + "loadQuestionBank",
                        method: 'POST',
                        beforeSend: function(request) {
                            request.setRequestHeader("AuthToken", resp.data.admin_token);
                        },
                        dataType: 'json',
                        contentType: 'application/json',
                        data: JSON.stringify(request),
                        success: function(result) {
                            console.log("Result ==>", result);
                            if (result != null) {
                                $('#questions_div').removeClass("d-none");
                                $('#submit_div').removeClass("d-none");
                                $('#load_more_btn_div').removeClass("d-none");

                                $('#questions_div').css("max-height", "400px");
                                $('#questions_div').css("overflow-y", "auto");

                                // $('#questions_div').html(getResponse(result.test));

                                applyMathJax(getResponse(result.test), "questions_div");
                            }
                        }
                    });
                } else {
                    alert("Some error authenticating your request. Please clear your browser cache and try again.");
                }
            })
            .catch(function(error) {
                // An error occurred
                // alert("Exception: " + error);
            });
    }


    // To calculate the count of all the checked questions
    function calculateCheckedQuestionsCount() {
        $("#selected_questions_count_div").show();
        $("#selected_questions_count_div #selected_questions_count").html($('.chkboxQues_append:checkbox:checked').length);
        $("#import_button_selected_count").html($('.chkboxQues_append:checkbox:checked').length);

        //Toggling enable/disable to import button
        if (checkQuestionsSelected()) {
            $('button[name ="import_questions_submit"]').prop('disabled', false);
        } else {
            $('button[name ="import_questions_submit"]').prop('disabled', true);
        }
    }
</script>


<script>
    function load_more_questions() {

        //Load tokens first
        get_admin_token().then(function(result) {
                var resp = JSON.parse(result);
                if (resp != null && resp.status == 200 && resp.data != null && resp.data.admin_token != null) {
                    var request = getRequest(last_record);
                    var test_id = "<?php echo $test_id; ?>";
                    //var last_record = $('#last_record').val();
                    $('#load_more_div').html("");
                    $('#loading_img_custom').show();
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
                            // console.log("Result ==>", result);
                            if (result != null) {
                                // $('#questions_div').append(getResponse(result.test));
                                appendMathJax(getResponse(result.test), "questions_div");
                            }

                            $('#loading_img_custom').hide();
                            //$('#questions_div').append(result);
                            
                        }
                    });

                } else {
                    alert("Some error authenticating your request. Please clear your browser cache and try again.");
                }
            })
            .catch(function(error) {
                // An error occurred
                // alert("Exception: " + error);
            });

    }
</script>

<script>
    function checkAll(formname, checktoggle) {
        var checkboxes = new Array();
        checkboxes = document.getElementById(formname).getElementsByTagName('input');

        for (var i = 0; i < checkboxes.length; i++) {
            if (checkboxes[i].type == 'checkbox') {
                checkboxes[i].checked = checktoggle;
            }
        }

        calculateCheckedQuestionsCount();
    }

    $(document).on('change', '.chkboxQues_append', function() {
        // Does some stuff and logs the event to the console
        calculateCheckedQuestionsCount();
    });


    // To calculate the count of all the checked questions
    function calculateCheckedQuestionsCount() {
        $("#selected_questions_count_div").show();
        $("#selected_questions_count_div #selected_questions_count").html($('.chkboxQues_append:checkbox:checked').length);
        $("#import_button_selected_count").html($('.chkboxQues_append:checkbox:checked').length);

        //Toggling enable/disable to import button
        if (checkQuestionsSelected()) {
            $('button[name ="import_questions_submit"]').prop('disabled', false);
        } else {
            $('button[name ="import_questions_submit"]').prop('disabled', true);
        }
    }



    //To check whether any questions are selected or not
    function checkQuestionsSelected() {
        if ($('.chkboxQues_append:checkbox:checked').length < 1) {
            // No questions selected
            return false;
        } else {
            // At least one question selected
            return true;
        }
    }
</script>