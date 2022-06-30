<!-- Include Header -->
<?php include_once(APPPATH . "Views/header.php"); ?>
<?php
// Include Service URLs Parameters File
include_once(APPPATH . "Views/service_urls.php");
?>
<!-- Custom CSS -->
<link href="<?php echo base_url('assets/css/doubts/overview.css?v=20210902'); ?>" rel="stylesheet">
<div id="content" ng-app="app" ng-controller="doubtDetails">
    <div class="container-fluid">

        <div class="flex-container-column">
            <div>
                <label class="h5 text-gray-800 text-uppercase"> <?= $title; ?> </h5>
            </div>
            <div class="breadcrumb_div" aria-label="breadcrumb">
                <ol class="breadcrumb_custom">
                    <li class="breadcrumb_item"><a href="<?php echo base_url('/home'); ?>"> Home </a></li>
                    <li class="breadcrumb_item"><a href="<?php echo base_url('doubts'); ?>"> Doubts </a></li>
                    <li class="breadcrumb_item active" aria-current="page"> <?= $title; ?> </li>
                </ol>
            </div>
        </div>



        <div class="card shadow p-2 w-100 m-auto mb-2" style="max-width: 800px;">
            <div class="text-center my-2 d-none" id="loading-div">
                <i class='fas fa-atom fa-spin fa-2x fa-fw'></i>
            </div>

            <h4><span id="questionText">{{currentRecord.test.currentQuestion.question != null ? currentRecord.test.currentQuestion.question : currentRecord.test.currentQuestion.feedback.sourceVideoName}}</span></h4>

            <div class="text-center" ng-if="currentRecord.test.currentQuestion.questionImageUrl != null">
                <img class='img-fluid d-block m-auto doubt-image' src="{{currentRecord.test.currentQuestion.questionImageUrl}}" data-bs-toggle="tooltip" title="Question Image">
                <label class="fw-bold text-muted">Question Image</label>
            </div>

            <!-- Show question Image or Video -->
            <iframe id="watchVideo" style="display:none" class="video-frame" frameborder="0" allow="fullscreen" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
        </div>
        <div class="p-2 w-100 m-auto mb-4" style="max-width: 800px;">

            <div class="alert alert-success alert-dismissable" id="success-alert" ng-if="errorMessage != ''">
                {{errorMessage}}
            </div>





            <ul class="nav nav-pills justify-content-center mb-4" id="pills-tab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="pills-pending-doubts-tab" data-bs-toggle="pill" data-bs-target="#pills-pending-doubts" type="button" role="tab" aria-controls="pills-pending-doubts" aria-selected="true">Pending Doubts</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="pills-resolved-doubts-tab" data-bs-toggle="pill" data-bs-target="#pills-resolved-doubts" type="button" role="tab" aria-controls="pills-resolved-doubts" aria-selected="false">Resolved Doubts</button>
                </li>
            </ul>
            <div class="tab-content" id="pills-tabContent">
                <div class="tab-pane fade show active" id="pills-pending-doubts" role="tabpanel" aria-labelledby="pills-pending-doubts-tab">

                    <div class="text-end mt-2 mb-4">
                        <input type="text" placeholder="Search" ng-model="unresolvedDoubtSearch">
                    </div>

                    <table id="unresolved_doubt_details_tbody" class="table" ng-if="unresolved.length > 0">
                        <tbody id="unresolved_doubts_tbody">
                            <tr class="card shadow" ng-repeat="record in unresolved | filter:unresolvedDoubtSearch">
                                <td>
                                    <div class="d-flex mb-2">
                                        <span class="badge rounded-pill bg-warning" data-bs-toggle="tooltip" title="Doubt Asked By">{{record.student.name}}</span>

                                        <span class="badge rounded-pill bg-info ms-2" data-bs-toggle="tooltip" title="Doubt Asked On">{{record.test.currentQuestion.feedback.createdDate | date: 'MMM dd'}}</span>
                                    </div>

                                    <div ng-if="record.test.currentQuestion.feedback.attachment != null">
                                        <a class="text-success" href="{{record.test.currentQuestion.feedback.attachment}}" target="_blank" data-bs-toggle='tooltip' title='Doubt attachment'>
                                            <img src='{{record.test.currentQuestion.feedback.attachment}}' class='img-fluid d-block m-auto doubt-image' alt='doubt attachment'>
                                        </a>
                                    </div>

                                    <p>{{record.test.currentQuestion.feedback.feedback}}</p>


                                    <div class="col-12 d-flex justify-content-center align-items-center">
                                        <button class="btn btn-light text-primary fw-bold text-uppercase" id="{{record.test.currentQuestion.feedback.id}}" onclick="show_edit_modal('modal_div','resolve_doubts_modal','doubts/resolve_doubts_modal/'+this.id+'/general/<?= $doubt_id; ?>')">
                                            Resolve
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div class='text-center border border-3 border-danger text-danger p-3 rounded' ng-if="!unresolved">No pending doubts</div>

                </div>


                <div class="tab-pane fade" id="pills-resolved-doubts" role="tabpanel" aria-labelledby="pills-resolved-doubts-tab">

                    <div class="text-end mt-2 mb-4">
                        <input type="text" placeholder="Search" ng-model="resolvedDoubtSearch">
                    </div>

                    <table id="resolved_doubt_details_tbody" class="table" ng-if="resolved.length > 0">
                        <tbody>
                            <tr class="card shadow" ng-repeat="record in resolved | filter:resolvedDoubtSearch">
                                <td>
                                    <div class="d-flex mb-2">
                                        <span class="badge rounded-pill bg-warning" data-bs-toggle="tooltip" title="Doubt Asked By">{{record.student.name}}</span>

                                        <span class="badge rounded-pill bg-info ms-2" data-bs-toggle="tooltip" title="Doubt Asked On">{{record.test.currentQuestion.feedback.createdDate | date: 'MMM dd'}}</span>
                                    </div>

                                    <div ng-if="record.test.currentQuestion.feedback.attachment != null"><a class="text-success" href="{{record.test.currentQuestion.feedback.attachment}}" target="_blank" data-bs-toggle='tooltip' title='Doubt attachment'> <img src='{{record.test.currentQuestion.feedback.attachment}}' class='img-fluid d-block m-auto doubt-image' alt='doubt attachment'> </a></div>

                                    <p>{{record.test.currentQuestion.feedback.feedback}}</p>


                                    <div class="card border border-2 border-success mb-3 bg-light m-auto" style="max-width: 90%;">
                                        <div class="card-body text-success">
                                            <h5 class="card-title">Resolution</h5>
                                            <p class="card-text"> <b class="text-dark">{{record.teacherName == null ? 'Admin' : record.teacherName}}</b>: {{record.test.currentQuestion.feedback.feedbackResolutionText}}</p>


                                            <div ng-if="record.test.currentQuestion.feedback.feedbackResolutionImageUrl != null && record.test.currentQuestion.feedback.feedbackResolutionImageUrl != '' && record.test.currentQuestion.feedback.feedbackResolutionImageUrl.indexOf('http') < 0">
                                                <a class="text-success" href="/{{record.test.currentQuestion.feedback.feedbackResolutionImageUrl}}" target="_blank" data-bs-toggle='tooltip' title='Doubt solution attachment'>
                                                    <img src="/{{record.test.currentQuestion.feedback.feedbackResolutionImageUrl}}" class='img-fluid d-block m-auto doubt-image' alt='Doubt solution attachment'>
                                                </a>
                                            </div>


                                            <div ng-if="record.test.currentQuestion.feedback.feedbackResolutionImageUrl != null && record.test.currentQuestion.feedback.feedbackResolutionImageUrl != '' && record.test.currentQuestion.feedback.feedbackResolutionImageUrl.indexOf('http') >= 0">
                                                <a class="text-success" href="{{record.test.currentQuestion.feedback.feedbackResolutionImageUrl}}" target="_blank" data-bs-toggle='tooltip' title='Doubt solution attachment'>
                                                    <img src='{{record.test.currentQuestion.feedback.feedbackResolutionImageUrl}}' class='img-fluid d-block m-auto doubt-image' alt='Doubt solution attachment'>
                                                </a>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="col-12 d-flex justify-content-center align-items-center">
                                        <button class="btn btn-light text-danger fw-bold text-uppercase" id="{{record.test.currentQuestion.feedback.questionId}}" onclick="show_edit_modal('modal_div','move_to_pending_doubt_modal','doubts/move_to_pending_doubt_modal/'+this.id)">Move to Pending</button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div class='text-center border border-3 border-danger text-danger p-3 rounded' ng-if="!resolved">No resolved doubts</div>
                </div>
            </div>


        </div>

    </div>
</div>


<!-- Include Footer -->
<?php include_once(APPPATH . "Views/footer.php"); ?>

<script type="text/javascript">
    //Init variables
    var doubtId = <?php echo $doubt_id; ?>;
    var doubtType = '<?php echo $doubtType; ?>';
</script>

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

        // console.log("Reset math jax ..");
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

    function loader(show) {
        if (show) {
            $("#loading-div").removeClass("d-none");
        } else {
            $("#loading-div").addClass("d-none");
        }
    }


    app.controller('doubtDetails', function($scope, userService, $location, $http) {

        console.log("Doubt details controller ...");


        $scope.loadDoubts = function(resolve) {


            $scope.errorMessage = "Loading ..";
            $scope.dataObj = {
                feedback: {
                    id: doubtId,
                    type: doubtType,
                    resolution: resolve
                }
            };
            console.log("get " + resolve + " request ==>", $scope.dataObj);
            loader(true);
            userService.callAdminService($scope, "getQuestionFeedbacks").then(function(response) {
                console.log(' ============= Got response ========== ', response);
                $scope.errorMessage = "";

                if (response.maps.length == 0) {
                    //$scope.errorMessage = "";
                } else {
                    if (resolve == 'Resolved') {
                        $scope.resolved = response.maps;
                        $scope.currentRecord = response.maps[0];
                        // applyMathJax($scope.currentRecord.test.currentQuestion.question, "questionText");
                    } else {
                        $scope.unresolved = response.maps;
                        $scope.currentRecord = response.maps[0];
                        // applyMathJax($scope.currentRecord.test.currentQuestion.question, "questionText");
                    }

                    $('#watchVideo').attr('src', response.maps[0].test.currentQuestion.feedback.sourceVideoUrl);
                    if (response.maps[0].test.currentQuestion.feedback.sourceVideoUrl == null) {
                        $('#watchVideo').attr('style', 'display:none');
                    } else {
                        $('#watchVideo').attr('style', '');
                    }


                    loader(false);
                    initializeTooltip();
                    //console.log("Source set as " + response.maps[0].test.currentQuestion.feedback.sourceVideoUrl);
                }
            }).catch(function(error) {
                console.log("Error!" + error);
                $scope.errorMessage = "Could not fetch doubts ..";
            });


        }

        $scope.loadDoubts('Resolved');
        $scope.loadDoubts('Unresolved');

    });
</script>