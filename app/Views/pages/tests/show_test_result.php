<!-- Include Header -->
<?php include_once(APPPATH . "Views/header.php"); ?>
<?php
echo "<script>localStorage.setItem('testid_result', $test_id);</script>";
?>
<!-- Custom CSS -->
<link href="<?php echo base_url('assets/css/tests/test_result.css?v=20220513'); ?>" rel="stylesheet">

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

        <div ng-app="app" ng-controller="testAnalysis">


            <h3 id="card_box_heading">Test Analysis</h3>

            <div class="container-fluid text-end">
                <a class="btn btn-outline-success" href="<?= base_url('tests/import_offline_results/' . $encrypted_test_id); ?>"> Import Offline Result </a>
                <a ng-if="test.id" class="btn btn-outline-primary d-none" href="<?= base_url('tests/fire_bulk_sms/' . $encrypted_test_id); ?>"> Check Result in NTA Format <i class="fa fa-arrow-right" aria-hidden="true"></i></a>
            </div>

            <div class="container-fluid" id="main_div">

                <i hidden class='fas fa-atom fa-spin fa-2x fa-fw' id="test-analysis-loading"></i>

                <div class="row" id="test-analysis-overview-block" style="margin-bottom: 32px;">
                    <div class="col-md-12 test-details" style="margin-bottom: 32px;">
                        <div class="test-name">{{test.name | uppercase}}</div>
                        <div class="test-details-badges">
                            <span class="test-questions-badge">{{test.noOfQuestions}} Questions</span>
                            <span>{{test.duration| toMinSec}}</span>
                            <span>{{test.totalMarks}} Total Marks</span>
                        </div>
                    </div>



                    <div class="col-sm-2">
                        <div class="test-analysis-counts-card">
                            <div class="count-text"><i class="fas fa-users"></i> {{test.analysis.studentsAppeared}}</div>
                            <div class="count-subtitle">Students Present</div>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="test-analysis-counts-card">
                            <div class="count-text"><i class="fas fa-users-slash"></i> {{test.analysis.studentsAbsent}}</div>
                            <div class="count-subtitle">Students Absent</div>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="test-analysis-counts-card">
                            <div class="count-text"><i class="fas fa-tachometer-alt"></i> {{test.analysis.averageScore | number : 0}}</div>
                            <div class="count-subtitle">Average Score</div>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="test-analysis-counts-card">
                            <div class="count-text"><i class="fas fa-check-circle"></i> {{test.analysis.averageCorrect | number : 0}}</div>
                            <div class="count-subtitle">Average Correct</div>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="test-analysis-counts-card">
                            <div class="count-text"><i class="fas fa-times-circle"></i> {{test.analysis.averageAttempted - test.analysis.averageCorrect | number : 0}}</div>
                            <div class="count-subtitle">Average Wrong</div>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="test-analysis-counts-card">
                            <div class="count-text"><i class="fas fa-pen-alt"></i> {{test.analysis.averageAttempted | number : 0}}</div>
                            <div class="count-subtitle">Average Attempted</div>
                        </div>
                    </div>
                </div>


                <div class="row">

                    <div class="col-md-4">
                        <!-- Top 10 Students Graph -->
                        <div id="top_score_students_graph_div"></div>
                    </div>

                    <div class="col-md-4">
                        <!-- Test Summary Graph -->
                        <div id="test_summary_graph_div"></div>
                    </div>

                    <div class="col-md-4">
                        <!-- Students Presenty Graph -->
                        <div id="students_present_graph_div"></div>
                    </div>

                </div>

                <hr />

                <ul class="nav nav-pills nav-fill mb-3" id="pills-tab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="showStudents-tab" data-bs-toggle="pill" data-bs-target="#showStudents" type="button" role="tab" aria-controls="showStudents" aria-selected="true">Students Analysis</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="showQuestions-tab" data-bs-toggle="pill" data-bs-target="#showQuestions" type="button" role="tab" aria-controls="showQuestions" aria-selected="true">Questions Analysis</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="showSubjectwiseAnalysis-tab" data-bs-toggle="pill" data-bs-target="#showSubjectwiseAnalysis" type="button" role="tab" aria-controls="showSubjectwiseAnalysis" aria-selected="true">Subjectwise Analysis</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="absentStudents-tab" data-bs-toggle="pill" data-bs-target="#absentStudents" type="button" role="tab" aria-controls="absentStudents" aria-selected="true">Absent Students</button>
                    </li>
                </ul>

                <div class="tab-content" id="pills-tabContent">
                    <div id="showStudents" class="tab-pane fade show active">
                        <i hidden class='fas fa-atom fa-spin fa-2x fa-fw' id="student-list-loading"></i>

                        <div class="d-flex" id="show-details-student-list-div">
                            <button type="button" class="btn btn-default" data-bs-toggle="modal" data-bs-target="#settingsModal">
                                <i class="fa fa-cog" aria-hidden="true"></i>
                            </button>
                            <div>
                                <label><input type="checkbox" ng-model="showDetails" ng-click="toggleDetails()"> Show details</label>
                                <label><input type="checkbox" value="SHOW_ABSENT" ng-model="showAbsent" ng-click="showStudents()"> Show Absent students</label>
                            </div>
                        </div>
                        <div style="float: right; margin: 8px;">
                            <a class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#sendNotificationModal" style="cursor:pointer"><i class="fa fa-lg fa-envelope" aria-hidden="true"></i> SEND RESULT SMS/EMAIL </a>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm w-100" id="test_student_table">
                                <!-- <thead>
                                    <th>Rank</th>
                                    <th>Roll No</th>
                                    <th>Name</th>
                                    <th>Username</th>
                                    <th>Mobile</th>
                                    <th>District</th>
                                    <th>Cat</th>
                                    <th>Classroom</th>
                                    <th>Division</th>
                                    <th ng-repeat="subject in subjects" title="Score">{{subject}}</th>
                                    <th ng-repeat="subject in subjects">{{subject}} solved</th>
                                    <th ng-repeat="subject in subjects">{{subject}} unsolved</th>
                                    <th ng-repeat="subject in subjects">{{subject}} correct</th>
                                    <th ng-repeat="subject in subjects">{{subject}} incorrect</th>
                                    <th ng-repeat="subject in subjects">{{subject}} deductions</th>
                                    <th>Total</th>
                                    <th>%</th>
                                    <th>Correct</th>
                                    <th>Wrong</th>
                                    <th>Solved</th>
                                    <th title="Not Attempted">NA</th>
                                </thead> -->


                            </table>
                        </div>
                    </div>

                    <div id="showQuestions" class="tab-pane fade">
                        <i hidden class='fas fa-atom fa-spin fa-2x fa-fw' id="question-analysis-loading"></i>

                        <table ng-if="test.test.length > 0" class="table table-bordered table-sm" id="test_ques_table">
                            <thead>
                                <tr>
                                    <th>Q.No.</th>
                                    <th>Question</th>
                                    <th>Subject</th>
                                    <th>Option 1 %</th>
                                    <th>Option 2 %</th>
                                    <th>Option 3 %</th>
                                    <th>Option 4 %</th>
                                    <th>Correct %</th>
                                    <th>Wrong %</th>
                                    <th>Attempted %</th>
                                    <th>Unattempted %</th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr ng-repeat="q in test.test">
                                    <td>{{$index + 1}}</td>
                                    <td class="questionRow"><span>{{q.question}}</span>
                                        <img ng-if="q.questionImageUrl != null" src="{{q.questionImageUrl}}" style="max-height:600px;max-width:400px;">
                                    </td>
                                    <td>{{q.subject}}</td>
                                    <td>{{q.analysis.option1percent}}</td>
                                    <td>{{q.analysis.option2percent}}</td>
                                    <td>{{q.analysis.option3percent}}</td>
                                    <td>{{q.analysis.option4percent}}</td>
                                    <td>{{q.analysis.correctPercent}}</td>
                                    <td>{{q.analysis.wrongPercent}}</td>
                                    <td>{{q.analysis.attemptedPercent}}</td>
                                    <td>{{q.analysis.unattemptedPercent}}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>


                    <div id="showSubjectwiseAnalysis" class="tab-pane fade">
                        <i hidden class='fas fa-atom fa-spin fa-2x fa-fw' id="subjectwise-analysis-loading"></i>

                        <!-- Subjectwise analysis block -->
                        <div class="row" id="subjectwise-analysis-div" style="margin-top: 32px;">

                            <!-- Showed settings modal button here too -->
                            <div class="col-xs-12">
                                <button type="button" class="btn btn-default" data-bs-toggle="modal" data-bs-target="#settingsModal">
                                    <i class="fa fa-cog" aria-hidden="true"></i>
                                </button>
                            </div>

                            <div ng-repeat="subAnalysisObj in subjectAnalysis" class="col-md-4">

                                <label>{{subAnalysisObj.subject}}:</label>

                                <table class="table">
                                    <tbody>
                                        <tr class="table-info">
                                            <td>Average Solved</td>
                                            <td>{{subAnalysisObj.analysis.avgSolved}}</td>
                                        </tr>
                                        <tr class="table-info">
                                            <td>Average UnSolved</td>
                                            <td>{{subAnalysisObj.analysis.avgUnsolved}}</td>
                                        </tr>
                                        <tr class="table-info">
                                            <td>Average Correct Solved</td>
                                            <td>{{subAnalysisObj.analysis.avgCorrect}}</td>
                                        </tr>
                                        <tr class="table-info">
                                            <td>Average Wrong Solved</td>
                                            <td>{{subAnalysisObj.analysis.avgWrong}}</td>
                                        </tr>
                                        <tr class="table-info">
                                            <td>Subject Average Marks</td>
                                            <td>{{subAnalysisObj.analysis.subAvg}} ({{subAnalysisObj.analysis.aboveSubjectAverageScoringCount}} Stud)</td>
                                        </tr>
                                        <tr class="table-info">
                                            <td>Subject Topper Marks</td>
                                            <td>{{subAnalysisObj.analysis.subjectTopper}}</td>
                                        </tr>
                                        <tr class="table-info">
                                            <td>Total Marks</td>
                                            <td>{{subAnalysisObj.analysis.subjectTotal}}</td>
                                        </tr>
                                        <tr class="table-info">
                                            <td>No of Questions </td>
                                            <td>{{subAnalysisObj.analysis.typeCount}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="col-xs-12">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Subject</th>
                                                <th>Attempted %</th>
                                                <th>UnAttempted %</th>
                                                <th>Correct %</th>
                                                <th>Wrong %</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr ng-repeat="subAnalysisObj in subjectAnalysis">
                                                <td><label>{{subAnalysisObj.subject}}:</label></td>
                                                <td>{{((subAnalysisObj.analysis.avgSolved||0) * 100/subAnalysisObj.analysis.typeCount).toFixed(2)}}</td>
                                                <td>{{((subAnalysisObj.analysis.avgUnsolved||0) * 100/subAnalysisObj.analysis.typeCount).toFixed(2)}}</td>
                                                <td>
                                                    <span ng-if="subAnalysisObj.analysis.avgSolved">{{((subAnalysisObj.analysis.avgCorrect||0) * 100/subAnalysisObj.analysis.avgSolved).toFixed(2)}}</span>
                                                    <span ng-if="!subAnalysisObj.analysis.avgSolved">-</span>
                                                </td>
                                                <td>
                                                    <span ng-if="subAnalysisObj.analysis.avgSolved">{{((subAnalysisObj.analysis.avgWrong||0) * 100/subAnalysisObj.analysis.avgSolved).toFixed(2)}}</span>
                                                    <span ng-if="!subAnalysisObj.analysis.avgSolved">-</span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div id="absentStudents" class="tab-pane fade">
                        <!-- To display absent students list using ajax -->
                        <div id="absent-students-div"></div>

                    </div>
                </div>

            </div>

            <div id="sendNotificationModal" class="modal fade" role="dialog">
                <div class="modal-dialog">
                    <!-- Modal content-->
                    <div class="modal-content">
                        <div class="modal-header">


                            <h5 class="modal-title" id="sendNotificationLabel">Send Exam notification</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

                        </div>

                        <div class="modal-body">

                            <div class="col-6 mb-2">
                                <label class="form_label" for="gender">Type of notification <span class="req_color"></span></label>
                                <select name="exam_notification_type" id="exam_notification_type" class="form-control" required ng-model="notificationType">
                                    <option value="Results" selected>Result Notification</option>
                                    <option value="AbsentStudent">Absent student notification</option>
                                </select>
                            </div>

                            <div id="result_notification_preview">
                                Hi {name}, your <?= $institute_details['institute_name']; ?> <?= $test_details['test_name']; ?> score is {score} Your final rank is {rank} out of {totalStudents}
                            </div>

                            <div id="absent_notification_preview" style="display:none">
                            Hello {name}, This is to inform you that you have been absent for test <?= $test_details['test_name']; ?> 
                            conducted by <?= $institute_details['institute_name']; ?> on <?= date_format_custom($test_details['start_date'], "d-m-Y"); ?>. You can check your attendance report at {LINK} - MTRSFT
                            </div>

                            <!-- <div class="mb-2">
                                <label class="form-label" for="additional_msg">Additional custom message </label>
                                <textarea class="form-control" id="additional_msg" placeholder="Some additional content"></textarea>
                            </div> -->

                            <p id="sendProgress" class="text-danger">{{sendProgress}}</p>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success" name="import_excel" ng-click="sendNotification()">Send</button>
                        </div>

                    </div>
                </div>
            </div>


            <!-- Test Analysis Settings Modal -->
            <div class="modal fade" id="settingsModal" tabindex="-1" role="dialog" aria-labelledby="settingsModalLabel">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="settingsModalLabel">Results Settings </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>


                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="mb-2">
                                        <label for="subjects-rank-order-dropdown">Select Subjects in order you want RANK and Subject Analysis to be sorted (this resets when you reload the page):</label>
                                        <select id="subjects-rank-order-dropdown" ng-model="subjectsOrderDropdown" class="form-control subjects-order-dropdown" aria-placeholder="Subjects Ranking Order" placeholder="Subjects Ranking Order" multiple>
                                            <option ng-repeat="sub in subjects">{{sub}}</option>
                                        </select>
                                    </div>
                                    <div class="mb-2">
                                        <p class="text-center text-danger">{{testAnalysisSettingsError}}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" ng-click="saveTestAnalysisSettings()">Save</button>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>

<!-- Include Footer -->
<?php include_once(APPPATH . "Views/footer.php"); ?>
<script>
    var title = "<?= $title; ?>";
    var test_date = "<?= date_format_custom($test_details['start_date'], "d-m-Y"); ?>";
    var institute_name = "<?= $institute_details['alias_name']; ?>";
    var test_name = "<?= $test_details['test_name']; ?>";
    var institute_logo_url = "<?= $institute_details['logo_path']; ?>";
    var tableTopData = "<div style='display: flex;flex-direction:row;'><div style='flex-grow: 2'><img class='img-fluid' src='" + institute_logo_url + "' alt='institute logo' style='width:100px;' /></div><div style='flex-grow: 8;'><div style='display:center;align-self: center;justify-content: center;'><h1>" + institute_name + "</h1><h3>Examination Result</h3><h5>" + title + " (" + test_date + ")</h5></div></div></div>";
    var messageTopDataExcel = institute_name + "\n" + title;
</script>


<script src="<?php echo base_url('assets/js/result.js'); ?>"></script>
<script src="<?php echo base_url('assets/js/mathjax-config.js'); ?>"></script>
<!--  Cache images -->
<script src="https://rawgit.com/danialfarid/ng-file-upload/5.1.0/dist/ng-file-upload.min.js"></script>
<script src="<?php echo base_url('assets/js/angular-img-dl.min.js'); ?>"></script>

<script>
    // high chart common settings
    Highcharts.setOptions({
        colors: ['#C55A11', '#0070C0', '#555ABE', '#71601C', '#338B8A', '#CA762F', '#AF1A3E', '#D78942', '#ED5E5E', '#B77351', '#FFD301', '#FE0D27', '#3A546A', '#E871FE', '#00FEDE', '#1AFE00'],
        lang: {
            thousandsSep: ','
        }
    });
</script>

<script>
    $(document).ready(function() {
        testid_result = localStorage.getItem("testid_result");


        $('#subjects-rank-order-dropdown').select2({
            width: "100%"
        });


        // Ref: https://stackoverflow.com/questions/31431197/select2-how-to-prevent-tags-sorting
        $(".subjects-order-dropdown").on("select2:select", function(evt) {
            var element = evt.params.data.element;
            var $element = $(element);

            $element.detach();
            $(this).append($element);
            $(this).trigger("change");
        });

        //
        $("#exam_notification_type").change(function() {
            var value = $('option:selected', this).val();
            if(value == 'AbsentStudent') {
                $("#absent_notification_preview").attr("style", "");
                $("#result_notification_preview").attr("style", "display:none");
            } else {
                $("#absent_notification_preview").attr("style", "display:none");
                $("#result_notification_preview").attr("style", "");
            }
        });


        //To fetch absent students from a test

        $.post(base_url + "/tests/ajax_fetch_test_absent_students", {
                test_id: "<?= $encrypted_test_id; ?>"
            },
            function(data) {
                // alert("Data: " + data + "\nStatus: " + status);
                // console.log(data);

                $("#absent-students-div").html(data);


                //Adding datatable functionality to the table
                $('#absent-students-table').DataTable({
                    'columnDefs': [{
                        'targets': 0,
                        'searchable': true,
                        'orderable': true,
                    }],
                    dom: 'Blfrtip',
                    buttons: [
                        'excel'
                    ],
                    'paging': false,
                });
            });


            

    });


</script>