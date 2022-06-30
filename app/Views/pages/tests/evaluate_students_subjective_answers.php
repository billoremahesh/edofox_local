<!-- Include Header -->
<?php include_once(APPPATH . "Views/header.php"); ?>

<!-- Custom CSS -->
<link href="<?php echo base_url('assets/css/tests/result.css?v=20210915'); ?>" rel="stylesheet">
<link href="<?php echo base_url('assets/css/tests/evaluate_students_subjective_answers.css?v=20210915'); ?>" rel="stylesheet">

<div id="content" ng-app="app" ng-controller="testResults">
    <div class="container-fluid mt-4">

        <div class="flex-container-column">
            <div>
                <label class="h5 text-gray-800 text-uppercase"> <?= $title; ?> </h5>
            </div>
            <div class="breadcrumb_div" aria-label="breadcrumb">
                <ol class="breadcrumb_custom">
                    <li class="breadcrumb_item"><a href="<?php echo base_url('/home'); ?>"> Home </a></li>
                    <li class="breadcrumb_item"><a href="<?php echo base_url('tests'); ?>"> Tests </a></li>
                    <li class="breadcrumb_item"><a href="<?php echo base_url('tests/evaluate_subjective_answers/'.$encrypted_test_id); ?>"> Evaluate Subjective Answers </a></li>
                    <li class="breadcrumb_item active" aria-current="page"> <?= $title; ?> </li>
                </ol>
            </div>
        </div>


        <div class="card p-4">

            <div id="printable_div2">
                <div style="text-align: center;">
                    <h4 ng-if="!isLoading && response.test.solvedCount">Evaluate Answers by
                        <?php
                        if (isset($student_details)) :
                            echo $student_details["name"];
                        endif;
                        ?></h4>

                    <div ng-if="isLoading"><img style="width: 96px;" src="<?= base_url('assets/img/loading.gif'); ?>" /></div>

                    <h3>{{response.test.name}}</h3>

                    <div class="flex-container">

                        <div>{{response.test.solvedCount}}
                            <p>Solved</p>
                        </div>
                        <div>{{response.test.correctCount || '-'}}
                            <p>Correct</p>
                        </div>
                        <div>{{response.test.noOfQuestions}}
                            <p>Total Questions</p>
                        </div>
                        <div style="color: #5b51d8">{{response.test.score != null ? response.test.score : calculateMarks()}} / {{response.test.totalMarks}}
                            <p>Total Score</p>
                        </div>


                    </div>

                    <div class="flex-container" ng-if="response.test.analysis.mcqSolved > 0">

                        <div>{{response.test.analysis.mcqSolved}}
                            <p>MCQs Attempted</p>
                        </div>
                        <div>{{response.test.analysis.mcqCorrect}}
                            <p>MCQs Correct</p>
                        </div>
                        <div>{{response.test.analysis.mcqWrong}}
                            <p>MCQs wrong</p>
                        </div>
                        <div>{{response.test.analysis.mcqScore}}
                            <p>MCQ Score</p>
                        </div>


                    </div>

                    <button class="btn btn-primary text-uppercase" ng-click="updateScoreModal()">{{response.test.score != null ? 'Update Final score' : 'Save Final score'}}</button>
                </div>

            </div>

        </div>

        <hr class="primary-divider">

        <button type="button" class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#marks-settings-modal">
            <i class="fas fa-sliders-h" aria-hidden="true" data-toggle="tooltip" title="Marks Settings"></i>
        </button>

        <h4 class="text-center">Uploaded answers</h4>

        <!-- Old UI -->
        <div ng-if="response.test.answerFiles.length > 0">
            <div ng-repeat="q in response.test.answerFiles">

                <div class="row individual_answers_div">

                    <p class="bookmark-question-number">{{$index + 1}}</p>

                    <div class="row mt-2">
                        <div class="col-md-6">
                            <label data-toggle="tooltip" title="Marks for this image content">Marks: </label>

                            <input type="number" ng-blur="updateEvaluation(q)" class="form-control" id="marks{{q.id}}" placeholder="Enter Marks" ng-model="q.correctionMarks" ng-change="calculateMarks()" data-toggle="tooltip" title="Marks for this image content" style="display: inline; width: auto;">
                        </div>

                    </div>



                    <div class="col-md-6">
                        <div ng-style="{'box-shadow':'none'} || (q.marks > 0) && {'box-shadow':'0px 0px 10px #4CAF50'} || (q.marks <= 0 && q.answer.length > 0) && {'box-shadow':'0px 0px 10px #F44336'}">

                            <!--  style="max-height: 300px; overflow-y: scroll;" -->
                            <div class="text-center" ng-if="q.fileUrl.length > 0">
                                <img crossorigin="anonymous" class="img-fluid img_que_url lazy answer-image" id="source{{q.id}}" data-src="{{q.fileUrl}}" src="{{q.fileUrl}}" alt="question image" onclick="annotate(this);" />
                            </div>

                            <!-- <button class="btn btn-success annotate btn-annotate" id="btn{{q.id}}" onclick="annotate(this)">Annotate/Correction</button> -->

                        </div>



                        <div class="row">
                            <div class="col-md-6">
                                <label data-toggle="tooltip" title="Marks for this image content">Marks: </label>

                                <input type="number" ng-blur="updateEvaluation(q)" class="form-control" id="marks{{q.id}}" placeholder="Enter Marks" ng-model="q.correctionMarks" ng-change="calculateMarks()" data-toggle="tooltip" title="Marks for this image content" style="display: inline; width: auto;">
                            </div>

                        </div>
                    </div>

                    <div class="col-md-6 text-center" id="outputDiv{{q.id}}" [ngStyle]="{{q.correctionUrl != null ? '' : 'display:none'}}">
                        <img crossorigin="anonymous" class="img-fluid img_que_url lazy" data-src="{{q.correctionUrl}}" src="{{q.correctionUrl}}" id="output{{q.id}}" alt="filtered image" />

                    </div>
                    <div class="col-md-6" id="outputDivPlaceholder{{q.id}}" [ngStyle]="{{q.correctionUrl == null ? '' : 'display:none'}}">
                        <div class="jumbotron text-center text-faded">
                            <h4>Corrected/Annotated image will appear here</h4>
                        </div>
                    </div>




                </div>

            </div>
        </div>

        <!-- New UI -->

        <div ng-if="response.test.answerFiles == null || response.test.answerFiles.length == 0">

            <div class="text-center">
                <button class="btn btn-default" ng-click="previousEvaluation()" data-toggle="tooltip" title="Previous Question"><i class="fa fa-arrow-left" aria-hidden="true"></i></button>
                <button class="btn btn-default" ng-click="nextEvaluation()" data-toggle="tooltip" title="Next Question"><i class="fa fa-arrow-right" aria-hidden="true"></i></button>
            </div>

            <div>
                <hr />
                <div><label>All Answers:</label></div>
                <div style="display: flex; overflow-x: auto;">
                    <span ng-repeat="qno in response.test.test | filter:descriptiveFilter track by $index">
                        <button class="btn {{evaluationStatus(qno)}}" ng-click="jumpToQuestionEvaluation(qno)" style="margin: 0px 2px">{{qno.questionNumber}}</button>
                    </span>

                    <!-- ------------------------ -->
                    <!-- TODO: change class to btn-success as below when the images are checked/marks given -->
                    <!-- <button class="btn btn-success" style="margin: 0px 2px">99</button> -->
                    <!-- ------------------------ -->

                </div>
                <hr />
            </div>

            <div class="row">
                <div class="col-md-12 text-center">
                    <div style="display: inline-block; border: 2px solid #9c27b0; border-radius: 5px; padding: 8px;">
                        <label data-toggle="tooltip" title="Marks for the question">Question Q {{question.questionNumber}} Marks = <span style="color: #5b51d8">{{question.marks}}</span> Out of <b>{{question.weightage}}</b></label>

                        <!-- <input type="number" class="form-control" id="marks{{q.id}}" placeholder="Enter Marks" ng-model="question.marks" data-toggle="tooltip" title="Marks for this image content" style="display: inline; width: auto;"> -->

                    </div>
                </div>

            </div>


            <div>

                <!-- ------------------------ -->
                <!-- only show when no images uploaded -->
                <div class="text-center text-danger" ng-if="question.answerFiles == null || question.answerFiles.length == 0">
                    <b>No Answers Uploaded for this Question.</b>
                </div>
                <!-- ------------------------ -->



                <div class="row" ng-repeat="q in question.answerFiles track by $index">
                    <div class="row individual_answers_div">

                        <div class="col-xs-12 text-center">
                            <img crossorigin="anonymous" class="img-fluid lazy {{q.correctionUrl != null ? 'annotated-answer-image' : 'answer-image'}}" id="source{{q.id}}" data-src="{{q.correctionUrl != null ? q.correctionUrl : q.fileUrl}}" src="{{q.correctionUrl != null ? q.correctionUrl : q.fileUrl}}" alt="question image" onclick="annotate(this);" />

                            <!-- <img crossorigin="anonymous" class="img-fluid lazy annotated-answer-image" data-src="{{q.correctionUrl}}" src="{{q.correctionUrl}}" id="output{{q.id}}" alt="filtered image" onclick="annotate(this);" style="{{q.correctionUrl != null && q.correctionUrl != '' ? '' : 'display:none'}}" /> -->
                        </div>



                        <div class="col-xs-12 text-center" style="margin: 16px auto;">
                            <label data-toggle="tooltip" title="Marks for this image content">Marks: </label>

                            <select ng-change="updateEvaluation(q)" ng-model="q.correctionMarks" class="form-control" aria-placeholder="Marks" placeholder="Marks" ng-options="n for n in range(0, question.weightage, stepForMarks) " style="max-width: 100px;display: inline-block; background-color: gold;">
                            </select>
                        </div>



                        <div class="col-md-6">
                            <!-- <div ng-style="{'box-shadow':'none'} || (q.marks > 0) && {'box-shadow':'0px 0px 10px #4CAF50'} || (q.marks <= 0 && q.answer.length > 0) && {'box-shadow':'0px 0px 10px #F44336'}">

                                <div class="text-center" ng-if="q.fileUrl.length > 0">
                                    <img crossorigin="anonymous" class="img-fluid img_que_url lazy answer-image" id="source{{q.id}}" data-src="{{q.fileUrl}}" src="{{q.fileUrl}}" alt="question image" onclick="annotate(this);" />
                                </div>


                            </div> -->




                        </div>

                        <!-- <div class="col-md-6" id="outputDiv{{q.id}}" style="{{q.correctionUrl != null ? '' : 'display:none'}}">
                            <img crossorigin="anonymous" class="img-fluid img_que_url lazy annotated-answer-image" data-src="{{q.correctionUrl}}" src="{{q.correctionUrl}}" id="output{{q.id}}" alt="filtered image" />
                        </div> -->

                        <!-- <div class="col-md-6" id="outputDivPlaceholder{{q.id}}" style="{{q.correctionUrl == null ? '' : 'display:none'}}">
                            <div class="jumbotron text-center text-faded">
                                <h4>Corrected/Annotated image will appear here</h4>
                            </div>
                        </div> -->

                    </div>

                    <div ng-if="$index !=  question.answerFiles.length - 1" class="arrow_indicator_div"><i class="fa fa-arrow-circle-down" aria-hidden="true"></i></div>

                </div>


                <div class="text-center" style="margin: 16px auto 32px;">
                    <button class="btn btn-default" ng-click="previousEvaluation()" data-toggle="tooltip" title="Previous Question"><i class="fa fa-arrow-left" aria-hidden="true"></i></button>
                    <button class="btn btn-default" ng-click="nextEvaluation()" data-toggle="tooltip" title="Next Question"><i class="fa fa-arrow-right" aria-hidden="true"></i></button>
                </div>
            </div>

        </div>



        <div id="updateScoreModal" class="modal fade" role="dialog">
            <div class="modal-dialog">

                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
                        <h5 class="modal-title" id="exampleModalLabel"> Update score for

                            <?php
                            if (isset($student)) :
                                echo  $student["name"];
                            endif;
                            ?></h5>

                    </div>


                    <div class="modal-body">

                        <p>Total questions solved</p>
                        <p><input type="text" class="form-control" ng-model="response.test.solvedCount" placeholder="No of questions solved"></p>
                        <p>Total questions correct</p>
                        <p><input type="text" class="form-control" ng-model="response.test.correctCount" placeholder="No of questions correct solved"></p>
                        <p>Total score</p>
                        <p><input type="text" class="form-control" ng-model="response.test.score" placeholder="Total score"></p>


                    </div>
                    <p>{{updateScoreProgress}}</p>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" ng-if="revaluateProgress.status != 200" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" ng-if="revaluateProgress.status != 200" ng-click="updateScore()"> Update </button>
                    </div>
                </div>

            </div>
        </div>


        <div id="updateScoreModal" class="modal fade" role="dialog">
            <div class="modal-dialog">

                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
                        <h5 class="modal-title" id="exampleModalLabel"> Update score for
                            <?php
                            if (isset($student)) :
                                echo $student["name"];
                            endif;
                            ?>
                        </h5>

                    </div>


                    <div class="modal-body">

                        <p>Total questions solved</p>
                        <p><input type="text" class="form-control" ng-model="response.test.solvedCount" placeholder="No of questions solved"></p>
                        <p>Total questions correct</p>
                        <p><input type="text" class="form-control" ng-model="response.test.correctCount" placeholder="No of questions correct solved"></p>
                        <p>Total score</p>
                        <p><input type="text" class="form-control" ng-model="response.test.score" placeholder="Total score"></p>


                    </div>
                    <p>{{updateScoreProgress}}</p>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" ng-if="revaluateProgress.status != 200" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" ng-if="revaluateProgress.status != 200" ng-click="updateScore()"> Update </button>
                    </div>
                </div>

            </div>
        </div>


        <!-- Modal for settings of negative marks and steps for marks -->
        <div class="modal fade" tabindex="-1" role="dialog" id="marks-settings-modal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h6 class="modal-title"> Marks Settings</h6>
                    </div>

                    <div class="modal-body">
                        <div class="form-group">
                            <label>Steps between marks (Default = 1)</label>
                            <select ng-model="stepForMarks" class="form-control" ng-change="saveStepsBetweenMarks(stepForMarks)">
                                <option value=""></option>
                                <option value="0.1">0.1</option>
                                <option value="0.25">0.25</option>
                                <option value="0.5">0.5</option>
                                <option value="1">1</option>
                            </select>
                        </div>

                        <!-- <div class="form-group">
                            <label>Do you want negative marks?</label>
                            <select ng-model="showNegativeMarks" class="form-control">
                                <option value="0">NO</option>
                                <option value="1">YES</option>
                            </select>
                        </div>

                        <div ng-if="showNegativeMarks == 1" class="form-group">
                            <label>Lowest negative marks</label>

                            <select ng-model="lowestNegativeMarks" class="form-control">
                                <?php
                                for ($i = 0; $i >= -10; $i--) : ?>
                                    <option value="<?= $i ?>"><?= $i ?></option>
                                <?php endfor; ?>
                            </select>

                        </div> -->
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Close</button>
                    </div>

                </div>
            </div>
        </div>
    </div>

</div>




<!-- Include Footer -->
<?php include_once(APPPATH . "Views/footer.php"); ?>

<!-- Set admin flags -->
<script>
    localStorage.setItem("resultUrl", "Admin");
</script>

<script>
    localStorage.studentId = <?= $student_id ?>;
    localStorage.testId = <?= $test_id ?>;
    var teacherID = <?= $adminId ?>;
    //console.log("Student " + studentId + " and test " + testId);
</script>

<script src="<?php echo base_url('assets/js/result.js'); ?>"></script>


<script src="<?php echo base_url('assets/js/jQuery.loadScroll.js'); ?>"></script>
<script src="https://unpkg.com/markerjs2/markerjs2.js"></script>
<script src="<?php echo base_url('assets/plugins/snackbar-material/snackbar.min.js'); ?>"></script>

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


<!-- SweetAlert CSS and JavaScript files-->
<script src="<?php echo base_url('assets/js/sweetalert-2.1.2.min.js'); ?>"></script>

<style>
    .__markerjs2_ {
        overflow-y: scroll;
    }
</style>
<script>
    function printContent(el) {
        var restorepage = document.body.innerHTML;
        var printcontent = document.getElementById(el).innerHTML;
        document.body.innerHTML = printcontent;
        window.print();
        document.body.innerHTML = restorepage;
    }

    $('img.lazy').loadScroll(500);
</script>

<script>
    function base64ToBlob(base64, mime) {
        mime = mime || 'image/png';
        var sliceSize = 1024;
        var byteChars = window.atob(base64);
        var byteArrays = [];

        for (var offset = 0, len = byteChars.length; offset < len; offset += sliceSize) {
            var slice = byteChars.slice(offset, offset + sliceSize);

            var byteNumbers = new Array(slice.length);
            for (var i = 0; i < slice.length; i++) {
                byteNumbers[i] = slice.charCodeAt(i);
            }

            var byteArray = new Uint8Array(byteNumbers);

            byteArrays.push(byteArray);
        }

        return new Blob(byteArrays, {
            type: mime
        });
    }

    function saveCorrection(id, file) {
        if ($('#marks' + id).val() == null && $('#marks' + id).val() == '') {
            alert("Please enter marks (0 or above) for this answer");
            return;
        }

        var fd = new FormData();
        var marks = 0;
        if ($('#marks' + id).val() != null && $('#marks' + id).val() != '') {
            marks = $('#marks' + id).val();
        }
        fd.append('marks', marks);
        fd.append('answerId', id);
        if (teacherID != null && teacherID > 0) {
            console.log("Adding teacher ID as " + teacherID);
            fd.append('evaluator', teacherID);
            fd.append('accessType', 'Admin');
        }
        //var f = document.getElementById("output" + id).files[0];
        // console.log("File:", f);
        var base64ImageContent = file.replace(/^data:image\/(png|jpg);base64,/, "");
        var blob = base64ToBlob(base64ImageContent, 'image/png');

        fd.append('file', blob);

        //Load tokens first
        get_admin_token().then(function(result) {
                var resp = JSON.parse(result);
                if (resp != null && resp.status == 200 && resp.data != null && resp.data.admin_token != null) {
                    $.ajax({
                        url: rootAdmin + "uploadEvaluation",
                        type: "POST",
                        data: fd,
                        beforeSend: function(fd) {
                            fd.setRequestHeader("AuthToken", resp.data.admin_token);
                        },
                        success: function(msg) {
                            console.log("Response", msg);
                            if (msg != null && msg.status != null && msg.status.statusCode == 200) {
                                //alert("Done!");
                            } else {
                                alert("Could not save the correction. Please try again.");
                            }

                        },
                        error: function(XMLHttpRequest, textStatus, errorThrown) {
                            alert("Error!");
                        },
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

    }

    // When the user clicks on the button, scroll to the top of the document
    function topFunction() {
        document.body.scrollTop = 0; // For Safari
        document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera
    }

    function annotate(element) {
        //var id = element.id.substring(3, element.id.length);
        // var container = new easyannotation.AnnotatorContainer(document.querySelector('#source' + id));
        // container.show(function(res) {
        //     console.log("Response!");
        //     //process result when user press Save
        //     document.querySelector('#output' + id).src = res;
        //     $("#outputDiv" + id).attr("style", "");
        //     $("#outputDivPlaceholder" + id).attr("style", "display:none");
        //     container.clear();
        //     saveCorrection(id, res);
        // });

        // console.log("Element is ", element);
        var id = element.id.substring(6, element.id.length);
        // console.log("Selected ID is " + id);
        const markerArea = new markerjs2.MarkerArea(element);
        markerArea.settings.displayMode = 'popup';
        // markerArea.addRenderEventListener((imgURL) => target.src = imgURL);
        markerArea.show();

        markerArea.addRenderEventListener((imgURL, state) => {
            document.querySelector('#source' + id).src = imgURL;
            $("#source" + id).attr('class', 'img-fluid lazy annotated-answer-image');

            //$("#outputDiv" + id).attr("style", "");

            //console.log("listening ID is " + id);

            // $("#outputDivPlaceholder" + id).attr("style", "display:none");


            //$("#source" + id).attr("style", "display:none");

            // save the state of MarkerArea
            maState = state;
            // console.log("Saving " + id + " as " + maState);
            saveCorrection(id, imgURL);
        });

    }



    $(document).ready(function() {

        //Setting tooltip
        $('[data-toggle="tooltip"]').tooltip();

        // in another js file, far, far away
        $('#inst_uploaded_logo').on('classChange', function() {
            // do stuff
            console.log("class changed");
        });

        // var $div = $("#inst_navbar_header");
        // var observer = new MutationObserver(function(mutations) {
        //     mutations.forEach(function(mutation) {
        //         if (mutation.attributeName === "class") {
        //             var attributeValue = $(mutation.target).prop(mutation.attributeName);
        //             console.log("Class attribute changed to:", attributeValue);
        //         }
        //     });
        // });
        // observer.observe($div[1], {
        //     attributes: true
        // });

    });
</script>