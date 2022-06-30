<!-- Include Header -->
<?php include_once(APPPATH . "Views/header.php"); ?>

<!-- Custom CSS -->
<link href="<?php echo base_url('assets/css/question_bank/pdf_parse.css?v=20211028'); ?>" rel="stylesheet">
<link rel="stylesheet" href="<?php echo base_url('assets/css/jcrop.min.css'); ?>">

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


        <div class="card shadow p-4" id="main_div" ng-app="app" ng-controller="pdfParser">

            <h4 class="text-center">
                <?= $subject_detail['subject'] . ": " . $chapter_detail['chapter_name']; ?>
            </h4>

            <div class="card_box">
                <div style="text-align:right">
                    <a class="btn btn-secondary" onclick="show_add_modal('modal_div','instructions','Tests/show_instructions_modal')">Read instructions</a>
                </div>
                <br />
                <div class="row">

                    <div class="col-12 mt-4">
                        <input type="hidden" name="instituteId" id="instituteId" value="<?= $decrypted_institute_id; ?>" required />
                        <input type="hidden" name="subjectId" id="subjectId" value="<?= $decrypted_subject_id; ?>" required />
                        <input type="hidden" name="chapterId" id="chapterId" value="<?= $decrypted_chapter_id; ?>" required />
                        <input type="hidden" name="section" id="section" value="section" required />
                        <input type="hidden" name="weightage" id="weightage" value="0" required />
                        <input type="hidden" name="negativeMarks" id="negativeMarks" value="0" required />
                        <input type="hidden" name="redirect" value="<?= $redirect; ?>" required />
                    </div>
                    <!-- {{request}} -->

                    <div class="col-2">
                        <div class="mb-2">
                            <label>Choose PDF</label>
                            <br>
                            <a href="<?= base_url('assets/templates/pdf_template.dotx'); ?>">Download Template <i class='fa fa-download fa-fw' aria-hidden='true'></i></a>
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="mb-2">
                            <input type="file" class="form-control" id="pdfToParse">
                        </div>
                    </div>
                    <div class="col-2">
                        <label>Buffer</label>
                    </div>
                    <div class="col-2">
                        <input type="text" class="form-control" min="1" value="5" id="buffer" ng-model="request.buffer">
                    </div>
                    <div class="col-2">
                        <label>PDF format</label>
                    </div>
                    <div class="col-2">
                        <select class="form-control" ng-model="request.pdfType">
                            <option value="SINGLE_COL">One column</option>
                            <option value="TWO_COL">Two columns</option>
                        </select>
                    </div>
                </div>

                <div class="row" style="padding: 16px;">

                    <div class="col-md-2">
                        <label>From Page no</label>
                    </div>
                    <div class="col-md-2">
                        <input type="number" class="form-control" id="from_question" placeholder="Default first" ng-model="request.fromPage">
                    </div>


                    <div class="col-md-2">
                        <label>To Page no</label>
                    </div>
                    <div class="col-md-2">
                        <input type="number" class="form-control" id="to_question" placeholder="Default last" ng-model="request.toPage">
                    </div>

                </div>
                <br />

                <br />

                <div class="row">
                    <div class="col-2">
                        <label>Question Number Prefix </label>
                    </div>
                    <div class="col-2">
                        <input type="text" class="form-control" placeholder="e.g. Q etc" id="question_prefix" ng-model="request.questionPrefix">
                    </div>
                    <div class="col-2">
                        <label>Question Number Suffix </label>
                    </div>
                    <div class="col-2">
                        <input type="text" class="form-control" placeholder="e.g. . or : or ) etc" id="question_prefix" ng-model="request.questionSuffix">
                    </div>
                    <div class="col-md-2">
                        <label>Solution Prefix (optional) </label>
                    </div>
                    <div class="col-md-2">
                        <input type="text" class="form-control" placeholder="Default ':Solution:'" id="solution_prefix" ng-model="request.solutionPrefix">
                    </div>

                </div>

                <br />
                <div class="row">
                    <div class="col-2">
                        <label>From Question no</label>
                    </div>
                    <div class="col-2">
                        <input type="number" class="form-control" id="from_question" placeholder="Default first" ng-model="request.fromQuestion">
                    </div>
                    <div class="col-2">
                        <label>To Question no</label>
                    </div>
                    <div class="col-2">
                        <input type="number" class="form-control" id="to_question" placeholder="Default last" ng-model="request.toQuestion">
                    </div>

                    <div class="col-2">
                        <label>Width Cropping Style </label>
                    </div>
                    <div class="col-2">
                        <select class="form-control" ng-model="request.cropWidth">
                            <option value="CROP_LEFT">Crop left side of the question only</option>
                            <option value="CROP_BOTH">Crop both sides of the question</option>
                            <option value="NO_CROP">No Cropping of width</option>
                        </select>
                    </div>

                </div>
                <div class="row" style="padding: 16px;">
                    <div class="col-md-2 d-none">
                        <label>Is PDF Watermarked </label>
                    </div>
                    <div class="col-md-2 d-none">
                        <label><input type="checkbox" ng-model="request.watermark" value="Y" ng-true-value="'Y'" ng-false-value="'N'"></label>
                    </div>
                </div>
                <hr>

                <div class="row">
                    <div class="alert alert-danger alert-dismissable" id="failure-alert" ng-if="responseText != null && responseText != 'OK'">
                        <i class="icon fa fa-ban"></i>{{responseText}}
                    </div>
                    <div class="alert alert-success alert-dismissable" id="success-alert" ng-if="responseText == 'OK'">
                        <i class="icon fa fa-check"></i> Parsing successful. Please scroll down to review the extracted questions
                    </div>
                </div>
                <div class="row text-center">
                    <div class="col-3">
                        <button class="btn btn-primary m-3 text-uppercase" ng-click="parsePdf()"><span ng-if="test == null">PARSE</span><span ng-if="test != null">PARSE AGAIN</span></button>
                    </div>
                    <div ng-if="test != null" class="col-3">
                        <button class="btn btn-success m-3 text-uppercase" ng-click="saveExam()">SAVE QUESTION PAPER</button>
                    </div>
                    <div ng-if="test != null" class="col-3">
                        <button ng-click="applyChanges(true)" class="btn btn-warning m-3 text-uppercase">ReApply filters</button>
                    </div>

                    <div ng-if="test != null" class="col-3 d-none">
                        <button ng-click="bulkApplyModal()" class="btn btn-warning m-3 text-uppercase">Bulk Apply filters</button>
                    </div>
                </div>
                <hr>
            </div>



            <div class="row" ng-if="test.test.length > 0">

                <h3 class="text-center text-uppercase">Preview extracted questions</h3>

                <div class="col-md-12 parsed_question_div" id="que_div_{{q.questionNumber}}" ng-repeat="q in test.test track by $index">

                    <div class="question_image_div">
                        <img src="{{q.questionImageUrl}}" id="que_img_{{q.questionNumber}}"  class="img-fluid parsed_question_image">

                        <span class="btn question_crop_button" ng-click="cropImageModal(q);" data-toggle="tooltip" title="Crop this image"><i class="fa fa-crop" aria-hidden="true"></i></span>

                        <a class="btn question_replace_button" ng-click="replaceImageModal(q)" data-toggle="tooltip" title="Replace this image"><i class="fa fa-retweet" aria-hidden="true"></i></a>

                    </div>


                    <div class="row" style="padding-top: 8px;">

                        <div class="col-md-2">
                            <label>Question Number </label>
                        </div>
                        <div class="col-md-2">
                            <input type="text" class="form-control" ng-model="q.questionNumber">
                        </div>

                        <div class="col-md-2">
                            <label>Type </label>
                        </div>
                        <div class="col-md-2">
                            <select value="{{q.type}}" id="questionType{{q.questionNumber}}" class="form-control" ng-model="q.type" ng-change="typeChanged(q)">
                                <option value="SINGLE">SINGLE</option>
                                <option value="MULTIPLE">MULTIPLE</option>
                                <option value="MATCH">MATCH</option>
                                <option value="NUMBER">NUMBER</option>
                                <option value="DESCRIPTIVE">Subjective Answer</option>

                            </select>
                        </div>

                        <div class="col-2">
                            <label> Difficulty level </label>
                        </div>
                        <div class="col-2">
                            <select value="{{q.level}}" id="questionLevel{{q.questionNumber}}" class="form-select" ng-model="q.level">
                                <option value=""></option>
                                <option value="1"> 1 - Low </option>
                                <option value="2"> 2 - Low-Moderate </option>
                                <option value="3"> 3 - Moderate </option>
                                <option value="4"> 4 - Moderate-High </option>
                                <option value="5"> 5 - High </option>
                            </select>
                        </div>

                    </div>

                    <div class="row" style="margin-top: 16px;">

                        <div class="col-md-2">
                            <label>Correct answer </label>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-2 form-inline">
                                <!--<input type="email" class="form-control" id="exampleInputEmail1" placeholder="Email">-->
                                <select id="questionAnswer{{q.questionNumber}}" class="form-control" ng-model="q.correctAnswer" ng-if="q.type == 'SINGLE'">
                                    <option value="option1">Option 1</option>
                                    <option value="option2">Option 2</option>
                                    <option value="option3">Option 3</option>
                                    <option value="option4">Option 4</option>

                                </select>

                                <input type="text" class="form-control" ng-model="q.correctAnswer" placeholder="option1,option2...or numeric" ng-if="q.type == 'NUMBER'">

                                <select id="questionAnswerMultiple{{q.questionNumber}}" class="form-control" style="width:100%" ng-model="q.correctAnswer" ng-if="q.type == 'MULTIPLE'" multiple>
                                    <option value="option1">Option 1</option>
                                    <option value="option2">Option 2</option>
                                    <option value="option3">Option 3</option>
                                    <option value="option4">Option 4</option>

                                </select>

                                <div ng-if="q.type == 'MATCH'">
                                    <p ng-if="q.type == 'MATCH'">Define columns -</p>
                                    <input type="text" ng-blur="columnChanged(q)" id="questionCol1{{q.questionNumber}}" class="form-control" ng-model="q.option1" placeholder="Column 1 list like a,b,c,d .. etc" ng-if="q.type == 'MATCH'">

                                    <input type="text" ng-blur="columnChanged(q)" id="questionCol2{{q.questionNumber}}" class="form-control" ng-model="q.option2" placeholder="Column 2 list like 1,2,3,4 ..etc" ng-if="q.type == 'MATCH'">

                                    <p>Answer key (Please add left right column values first)</p>
                                    <!-- <input type="text" class="form-control" ng-model="q.correctAnswer" placeholder="p-2,q-3..etc" ng-if="q.type == 'MATCH'"> -->

                                    <select id="questionAnswerMatch{{q.questionNumber}}" class="form-control" style="width:100%" ng-model="q.correctAnswer" ng-show="q.type == 'MATCH'" multiple>
                                        <option value="{{opt}}" ng-repeat="opt in q.matchOptions">{{opt}}</option>


                                    </select>
                                </div>


                            </div>
                        </div>




                    </div>

                    <div class="row" style="display:none">
                        <div class="col-md-2">
                            <label><input type="checkbox" ng-model="q.partialCorrection" value="Y" ng-true-value="'Y'" ng-false-value="'N'"> Partial correction</label>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12" ng-if="q.solutionImageUrl != null">
                            <img src="{{q.solutionImageUrl}}" class="img-fluid parsed_solution_image">
                        </div>
                    </div>

                </div>



            </div>


            <div id="imageReplaceModal" class="modal fade" role="dialog">
                <div class="modal-dialog">


                    <div class="modal-content">

                        <div class="modal-header">
                            <h6 class="modal-title">Replace Image for Q{{selectedQuestion.questionNumber}}</h6>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <div class="modal-body">
                            <input type="file" name="uploaded_file" id="uploaded_file" style="display: none;">
                            <!-- Drag and Drop container-->
                            <div class="upload-area" id="uploadfile">
                                <h6 id="drag_title">Drag and Drop file here<br />Or<br />Click to select file</h6>

                                <img src="" style="display: none;" id="preview_upload">

                                <canvas style="border:1px solid grey;" id="mycanvas">
                            </div>


                        </div>
                        <p>{{imageReplaceProgress}}</p>
                        <div class="modal-footer"> <button type="button" class="btn btn-secondary" onclick="clearCanvas(true)">Close</button>
                            <button type="button" class="btn btn-warning" onclick="clearCanvas(false)">Reset</button>
                            <button type="button" class="btn btn-primary" ng-click="uploadImage()"> Save</button>
                        </div>
                    </div>

                </div>
            </div>


            <div id="imageCropModal" class="modal fade" role="dialog">
                <div class="modal-dialog">


                    <div class="modal-content">

                        <div class="modal-header">
                            <h6 class="modal-title">Crop Image for Q{{selectedQuestion.questionNumber}}</h6>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <div class="modal-body">
                            <!-- Drag and Drop container-->
                            <div id="jcrop">
                                <h6 id="drag_title">Click and drag to Crop the image</h6>

                            </div>
                            <canvas id="source_canvas" style="display: none;"></canvas>
                            <canvas id="canvas" style="display: none;"></canvas>
                            <input id="png" type="hidden" />

                        </div>
                        <p>{{imageCropProgress}}</p>
                        <div class="modal-footer"> 
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" >Close</button>
                            <button type="button" class="btn btn-primary" ng-click="saveCrop()"> Save</button>
                        </div>
                    </div>

                </div>
            </div>

            

        </div>




    </div>
</div>



<div class="spinner-container-with-cancel" style="left: 0%;top:10%;" id="loader">
    <div class="spinner-sub-container">
        <h3 class="message" id="loader_text" style="top: 15%;font-size:16px;"> Parsing PDF .. Please wait...</h3>
        <br>
        <div class="spinner" style="display: block;margin-top: 17%;">
            <div class="rect1"></div>
            <div class="rect2"></div>
            <div class="rect3"></div>
            <div class="rect4"></div>
            <div class="rect5"></div>
        </div>
    </div>
</div>

<!-- Include Footer -->
<?php include_once(APPPATH . "Views/footer.php"); ?>
<script type="text/javascript">
    var testId = 0;
    var staffId = "<?= $staff_id; ?>";
    var teacherID = 0;
    var instituteID = "<?= $decrypted_institute_id; ?>";
</script>
<script src="<?php echo base_url('assets/js/jcrop.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/js/admin.js?v=20220418'); ?>"></script>
<script src="<?php echo base_url('assets/js/img_replace.js'); ?>"></script>

<script>
    $(document).ready(function() {
        //Initializing tooltip
        $('[data-bs-toggle="tooltip"]').tooltip();
    });
</script>


<script>
    $(".correct_answer_dropdown").select2();
    $(".correct_answer_dropdown_multiple").select2({
        closeOnSelect: false
    });
</script>