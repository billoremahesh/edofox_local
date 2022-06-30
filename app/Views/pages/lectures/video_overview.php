<!-- Include Header -->
<?php include_once(APPPATH . "Views/header.php"); ?>
<!-- Video Lecture View -->


<!-- Custom CSS -->
<link href="<?php echo base_url('assets/css/lectures/video_overview.css?v=20210902'); ?>" rel="stylesheet">


<div class="container-fluid mt-4" ng-app="app" ng-controller="viewLectures">
<?php 
    if( $live_count == 1) :
?>
        <div class="flex-container-column">
        <div>
                <label class="h5 text-gray-800 text-uppercase"> <?= $title; ?> </h5>
            </div>
            <div class="breadcrumb_div" aria-label="breadcrumb">
                <ol class="breadcrumb_custom">
                    <li class="breadcrumb_item"><a href="<?php echo base_url('/home'); ?>"> Home </a></li>
                    <li class="breadcrumb_item active" aria-current="page"> <?= $title; ?> </li>
                </ol>
            </div>
        </div>


        <div class="list-group">
                <a href="#" class="list-group-item" ng-repeat="upload in uploads">
                    <h4 class="list-group-item-heading">{{upload.title}}</h4>
                    <div class="list-group-item-text">
                        <!-- <progress value="{{upload.value}}" max="{{upload.max}}"></progress> -->

                        <p class="text-center small">{{(upload.value / 1048576).toFixed(2)}}/{{(upload.max/ 1048576).toFixed(2)}}MB <span [ngStyle]="{{upload.status == 'COMPLETED' ? 'color: green; font-weight: bold' : ''}}"> {{upload.status}}</span></p>

                        <div class="progress">
                            <div class="progress-bar" role="progressbar" aria-valuenow="{{upload.valuePercent}}" aria-valuemin="0" aria-valuemax="{{upload.max}}" [ngStyle]="min-width: 2em; width: {{upload.valuePercent}}%">
                                {{upload.valuePercent}} %
                            </div>
                        </div>
                    </div>
                </a>
        </div>

        <?php if ($userType === 'Teacher') { ?>
                <div class="text-center text-uppercase">
                    <button type="button" class="btn btn-primary btn-lg" ng-click="saveModal()">
                        Add new lecture
                    </button>
                </div>
        <?php } ?>

        <br><br>
        <div class="alert alert-info alert-dismissable" id="message-alert" ng-if="errorMessage">
            {{errorMessage}}
        </div>

        <div class="row">
                <div class="col-12">
                    <div class="input-group search-lectures-input">
                        <input type="text" class="form-control" ng-keyup="$event.keyCode == 13 ? filter() : null;" placeholder="Search in lectures" id="search" ng-model="search" ng-when="lectures.length > 0">
                        <span class="input-group-btn">
                            <button class="btn btn-secondary" type="button" ng-click="filter()"><i class="fa fa-search" aria-hidden="true"></i></button>
                        </span>
                    </div>
                </div>
        </div>
        <br>

        <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
            <div class="panel panel-default" ng-repeat="record in lectures">
                <div class="panel-heading" role="tab" id="heading{{record.lecture.id}}">
                        <?php if ($userType === 'Teacher') { ?>
                            <span ng-click="deleteModal(record)"><i style="cursor: pointer" class="fa fa-trash fa-2x pull-right" aria-hidden="true"></i></span>&nbsp;
                            <span ng-click="updateModal(record)"><i style="cursor: pointer" class="fa fa-pencil fa-2x pull-right" aria-hidden="true"></i></span>

                        <?php } ?>

                        <h4 class="panel-title">
                            <a style="display: block;" role="button" data-bs-toggle="collapse" data-parent="#accordion" href="#collapse{{record.lecture.id}}" aria-expanded="true" aria-controls="collapse{{record.lecture.id}}" ng-click="addPlayer(record.lecture.id)">
                                <p class="subject_tag" ng-if="record.subject.subjectName">{{record.subject.subjectName}}</p>

                                <h4>{{$index + 1}} . {{record.lecture.videoName}} </h4>

                            </a>
                        </h4>
                        <?php if ($userType === 'Teacher') { ?>
                            <p style="color: grey">{{record.lecture.video_url}}</p>
                        <?php } ?>
                </div>

                <div id="collapse{{record.lecture.id}}" class="panel-collapse collapse {{$index == 0 ? 'in': ''}} " role="tabpanel" aria-labelledby="heading{{record.lecture.id}}" ng-click="addPlayer(record.lecture.id)">
                        <div class="panel-body text-center">

                            <?php if ($userType !== 'Teacher') { ?>
                                <div>
                                    <button class="raise_doubt_button" ng-click="raiseDoubt(record.lecture.id)">Ask Lecture doubt</button>
                                </div>

                            <?php } ?>


                            <?php if ($video_constraint == "APP") : ?>
                                <div class="text-center download-app-message">
                                    <img class="img-fluid" src="dist/img/statics/app-download-notice-static.jpg" style="margin:auto;width:100%; max-width:600px" />
                                    <p>Please download Our Official Android App to enjoy new features like download and watch video anytime and save internet data. Please download the app and login before Friday. After Friday you will not be able to stream videos in web browser. </p>
                                    <a href="<?= $app_url ?>" target="_blank"><img src="./dist/img/google-play-badge.png" style="width: 150px;" /></a>

                                    <iframe class="video-frame" id="iframe{{record.lecture.id}}" src="{{record.lecture.video_url}}" frameborder="0" allow="fullscreen" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
                                </div>

                                <iframe class="video-frame video-player-iframe" id="iframe{{record.lecture.id}}" src="{{record.lecture.video_url}}" frameborder="0" allow="fullscreen" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>

                            <?php else : ?>

                                <div class="text-center download-app-message">
                                    <img class="img-fluid" src="dist/img/statics/app-download-notice-static.jpg" style="margin:auto;width:100%; max-width:600px" />
                                    <p>You can no longer watch videos in mobile browsers. In an effort to provide better experience for you, we have created an Android app where you can download and watch the videos any time. Please download Our Official Android App to watch the video. Click below</p>
                                    <a href="https://play.google.com/store/apps/details?id=com.mattersoft.edofoxapp" target="_blank"><img src="./dist/img/google-play-badge.png" style="width: 150px;" /></a>
                                </div>

                                <iframe class="video-frame video-player-iframe" id="iframe{{record.lecture.id}}" src="{{record.lecture.video_url}}" frameborder="0" allow="fullscreen" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
                            <?php endif; ?>


                        </div>
                </div>
            </div>
        </div>
        <div ng-if="isLoading"><i class='fas fa-atom fa-spin fa-2x fa-fw'></i></div>

        <div id="modalDelete" class="modal fade">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h6 class="modal-title">Delete lecture</h6>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to permenantly delete {{lecture.videoName}}?

                    </div>
                    <p style="color:red">{{error}}</p>
                    <p style="color:green">{{success}}</p>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
                        <button type="submit" ng-if="!error && !success" class="btn btn-default" name="delete_video" ng-click="update(true)">Yes, Delete</button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div>


        <div id="modalUpdate" class="modal fade">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h6 class="modal-title">Update lecture</h6>
                    </div>
                    <div class="modal-body">
                        <form role="form">
                            <input type="hidden" name="_token" value="">
                            <div class="mb-2">
                                <label class="form-label">Video title</label>
                                <div>
                                    <input type="text" ng-model="lecture.videoName" class="form-control" name="title" placeholder="Enter video title">
                                </div>
                            </div>

                            <div class="mb-2">
                                <label class="form-label">Choose subject</label>
                                <div>
                                    <select ng-model="subject" ng-options="subject.subjectName for subject in subjects">

                                    </select>
                                </div>
                            </div>

                            <div class="mb-2">
                                <label class="form-label">Choose classroom</label>
                                <div>
                                    <select ng-model="classroom" ng-options="package.name for package in packages">

                                    </select>
                                </div>
                            </div>


                            <div class="mb-2">
                                <div>
                                    <p style="color:red">{{error}}</p>
                                    <p style="color:green">{{success}}</p>
                                    <button type="button" class="btn btn-success" ng-click="update()">Save</button>
                                    <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </form>

                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div>

        <div id="modalSave" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="addLectureModal">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addLectureModal">Add lecture</h5>
                    </div>
                    <div class="modal-body">
                        <form role="form">
                            <input type="hidden" name="_token" value="">
                            <div class="mb-2">
                                <label class="form-label">Video title</label>
                                <div>
                                    <input type="text" ng-model="videoTitle" class="form-control" name="title" placeholder="Enter video title">
                                </div>
                            </div>
                            <div class="mb-2" ng-if="selectedVideo == null">
                                <label class="form-label">Select video file</label>
                                <div>
                                    <input type="file" id="videoFile" class="form-control" name="file">
                                </div>
                            </div>

                            <div class="mb-2">
                                <label class="form-label">Choose subject</label>
                                <div>
                                    <select ng-model="selectedSubject" ng-options="subject.subjectName for subject in subjects">

                                    </select>
                                </div>
                            </div>

                            <div class="mb-2">
                                <label class="form-label">Choose classroom</label>
                                <div>
                                    <select ng-model="selectedPackage" ng-options="package.name for package in packages">


                                    </select>
                                </div>
                            </div>


                            <div class="mb-2">
                                <div>
                                    <p style="color:red">{{error}}</p>
                                    <p style="color:green">{{success}}</p>
                                    <button type="button" class="btn btn-success" ng-click="save()">Save</button>
                                    <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </form>

                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div>

        <div id="raiseDoubtModal" class="modal fade" role="dialog">
            <div class="modal-dialog">

                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
                        <h6 class="modal-title">Raise Doubt</h6>
                    </div>
                    <form name="doubt_form" ng-submit="sendDoubt(questionData)">
                        <div class="modal-body">
                            <div class="row">

                                <div class="col-xs-12">
                                    <div class="mb-2">
                                        <label class="form-label" for="doubt_text">What is your doubt? Explain</label>
                                        <textarea class="form-control" name="doubt_text" ng-model="questionData.feedback" id="doubt_text" required></textarea>
                                    </div>
                                </div>

                                <div class="col-xs-12">
                                    <div class="mb-2">
                                        <label class="form-label" for="doubt_text">Attach a picture (Optional):</label>
                                        <input name="doubtFile" id="doubtFile" type="file" accept="image/*">
                                    </div>
                                </div>

                            </div>
                        </div>
                        <p style="color:red">{{doubtError}}</p>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-default" name="add_test_form_submit">Send Doubt</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>

        <?php if ($screeningTestStudent) : ?>
            <div class="modal fade" tabindex="-1" role="dialog" id="screening-test-notice-modal">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h6 class="modal-title">Important Notice</h6>
                        </div>
                        <div class="modal-body">
                            <div>
                                <a class="text-uppercase" target="_blank" href="https://junior-shahucollegelatur.org.in/download/2020/Screen-Test-Extra-Exam.pdf">NEW! Foundation Batch Full Syllabus PRACTICE EXAM Notice</a>
                            </div>

                            <p>Please join our official RSML Screening Test Telegram channel for all instant updates. Use link: <a href="https://t.me/joinchat/AAAAAFd7Qy6oQFY0UFIFwQ" target="_blank">https://t.me/joinchat/AAAAAFd7Qy6oQFY0UFIFwQ</a></p>


                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
<?php 
else :
    echo "<p style='padding: 15px; text-align: center;'><b>This feature is not enabled for your institute. Please contact your admin</b></p>";
endif;
?>
</div>



<!-- Include Footer -->
<?php include_once(APPPATH . "Views/footer.php"); ?>
