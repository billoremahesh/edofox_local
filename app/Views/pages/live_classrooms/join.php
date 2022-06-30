<!-- Include Header -->
<?php include_once(APPPATH . "Views/header.php"); ?>
<link rel="stylesheet" href="<?php echo base_url('assets/js/video/video.css?v=20220413'); ?>" />
<!-- Custom CSS -->
<link href="<?php echo base_url('assets/css/live_classrooms/join.css?v=20220413'); ?>" rel="stylesheet">
<link href="<?php echo base_url('assets/css/live_classrooms/video_media_query.css?v=20220413'); ?>" rel="stylesheet">

<?php
$lectureSaved = 'false';
if (isset($lecture_saved) && !empty($lecture_saved)) {
    $lectureSaved = $lecture_saved;
}
?>

<div id="content">
    <div class="container-fluid mt-2">

        <div class="flex-container-column">
            <div>
                <h5 class="h5 text-gray-800 text-uppercase"> <?= strtoupper($live_lecture_details['session_name']); ?> </h5>
            </div>
            <div class="breadcrumb_div" aria-label="breadcrumb">
                <ol class="breadcrumb_custom">
                    <li class="breadcrumb_item"><a href="<?php echo base_url('/home'); ?>"> Home </a></li>
                    <li class="breadcrumb_item"><a href="<?php echo base_url('/liveClassrooms'); ?>"> Live Lectures </a></li>
                    <li class="breadcrumb_item active" aria-current="page"> <?= strtoupper($live_lecture_details['session_name']); ?> </li>
                </ol>
            </div>
        </div>

        <div id="video_msg_content">
            <div class="row">
                <div class="col-sm-12 col-md-6 video_window_full_screen">

                    <div class="d-flex">
                        <input type="hidden" id="live_session_id" value="<?= $live_lecture_details['id'] ?>">

                        <div class="mx-2">
                            <button id="join_publish_button" class="btn btn-primary">Connect</button>
                        </div>

                        <div class="mx-2">
                            <button id="view_recording_btn" class="btn btn-primary" style="display: none;">View Recording</button>
                        </div>
                    </div>

                    <div class="d-flex my-2">
                        <div class="alert alert-warning alert-dismissible fade show" id="message-alert" role="alert" style="display: none;">
                        </div>
                    </div>

                    <div class="video_div" id="video_preview" style="display:none;">
                        <div class="ratio ratio-1x1">
                            <canvas id="canvas" style="display: none;"></canvas>
                            <video id="localVideo" autoplay muted playsinline></video>
                            <div id="canvas-designer"></div>
                            <div style="margin-top:10px;display: none;" class="col-sm-8 offset-sm-2">Microphone Gain: <input type=range id=volume_change_input min=0 max=1 value=1 step=0.01></div>
                        </div>
                    </div>

                    <!-- Video controls -->
                    <div class="desktop_content_display">
                        <div class="buttons-container" style="display: none;">
                            <div class="d-flex justify-content-center mt-3">
                                <div id="audio-controls" class="btn-group mx-1" role="group">
                                    <button type="button" class="fab-buttons mic-btn" title="Mute/Unmute">
                                        <i class="fas fa-microphone"></i>
                                    </button>
                                </div>

                                <div id="video-controls" class="btn-group mx-1" role="group">
                                    <button type="button" class="fab-buttons video-btn" title="Show/Hide Video">
                                        <i class="fas fa-video"></i>
                                    </button>
                                </div>


                                <div id="video-controls" class="btn-group mx-1" role="group">
                                    <button id="draw-btn" type="button" class="fab-buttons" title="Toggle Annotation">
                                        <i id="draw-icon" class="far fa-edit"></i>
                                    </button>
                                </div>

                                <div class="btn-group mx-1" role="group">
                                    <button id="btnShare" class="fab-buttons" title="Share Screen">
                                        <i class="fas fa-desktop"></i>
                                    </button>
                                </div>

                                <div class="btn-group mx-1" role="group">
                                    <button id="device_list" type="button" class="fab-buttons device_list" title="Choose Camera device from list">
                                        <i class="fas fa-sliders-h"></i>
                                    </button>
                                </div>

                                <div class="btn-group mx-1" role="group">
                                    <button type="button" class="fab-buttons-exit stop_publish_button" title="End Lecture">
                                        <i class="fas fa-window-close"></i>
                                    </button>
                                </div>

                            </div>

                        </div>

                    </div>

                </div>
                <div class="col-6 desktop_content_display">

                    <div id="messages_main_div" style="display: none;">
                        <div class="d-flex">

                            <div class="text-center btn-group">
                                <button id="mute_all_students" type="button" class="btn mute_all_students" title="Mute All Students">
                                    Mute All Students
                                </button>
                            </div>


                            <div class="form-check form-switch mx-2">
                                <input class="form-check-input toggle_comments" type="checkbox" id="toggle_comments">
                                <label class="form-check-label" for="toggle_comments">Comments</label>
                            </div>
                        </div>

                        <div class="d-flex my-2" id="messages_panel">
                            <div class="discussions">
                                <div class="discussion_header">
                                    Students
                                </div>
                                <!-- Student list appears here -->
                                <div class="student_list" id="student_list">
                                </div>
                            </div>
                            <div class="chat">
                                <div class="chat_header">
                                    <div>
                                        Messages
                                    </div>
                                </div>

                                <div class="chat_messages">
                                    <!-- Messages appears here -->
                                    <div class="msg_history" id="msg_history">

                                    </div>
                                </div>

                                <div class="chat_footer">
                                    <div class="write_message_div input_msg_write">
                                        <input type="text" id="text_message" class="write-message write_msg text_message" placeholder="Type your message here" />
                                        <span class="material-icons send_message msg_send_btn" id="msg_send_btn">
                                            send
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>



            <div class="mobile_chat_settings">
                <!-- show only in mobile devices -->
                <div class="mobile_content_display">
                    <div class="buttons-container" style="display: none;">
                        <div class="d-flex justify-content-center align-items-center mt-3">

                            <div id="audio-controls" class="btn-group mx-1" role="group">
                                <button type="button" class="fab-buttons mic-btn" title="Mute/Unmute">
                                    <i class="fas fa-microphone"></i>
                                </button>
                            </div>

                            <div id="video-controls" class="btn-group mx-1" role="group">
                                <button type="button" class="fab-buttons video-btn" title="Show/Hide Video">
                                    <i class="fas fa-video"></i>
                                </button>
                            </div>

                            <button class="fab-buttons" tooltip="Students" data-bs-toggle="offcanvas" data-bs-target="#student_list_window" aria-controls="offcanvasRight">
                                <i class="fas fa-users"></i>
                            </button>

                            <div class="msg_btn_grp">
                                <button class="fab-buttons" data-bs-toggle="offcanvas" data-bs-target="#messages_chat_window" aria-controls="offcanvasRight" id="show_messages_btn">
                                    <i class="far fa-comment-alt"></i>
                                </button>
                                <span id="msg_count_badge" class="msg_count_badge" style="display:none">
                                    0
                                </span>
                            </div>


                            <div class="fab-container d-flex flex-column justify-content-center align-items-center" style="margin-bottom: 60px;">

                                <button type="button" class="fab-pop-up-buttons stop_publish_button" title="End Lecture">
                                    <i class="fas fa-window-close"></i>
                                </button>

                                <button type="button" class="fab-pop-up-buttons level device_list" title="Choose Camera device from list">
                                    <i class="fas fa-sliders-h"></i>
                                </button>

                                <button class="fab-pop-up-buttons" tooltip="Settings">
                                    <span class="fab-rotate level">
                                        <i class="fa fab-icon level-item" aria-hidden="true"></i>
                                    </span>
                                </button>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Mobile Chat Window -->
        <!-- Messages appears here in mobile devices -->
        <!-- Student List -->
        <div class="offcanvas offcanvas-end" tabindex="-1" id="student_list_window" aria-labelledby="offcanvasRightLabel">
            <div class="offcanvas-header">
                <h5 id="offcanvasRightLabel">
                    <div class="d-flex">

                        <div class="text-center btn-group">
                            <button id="mute_all_students" type="button" class="btn mute_all_students" title="Mute All Students">
                                Mute All Students
                            </button>
                        </div>

                        <div class="form-check form-switch mx-2">
                            <input class="form-check-input toggle_comments" type="checkbox" id="toggle_comments">
                            <label class="form-check-label" for="toggle_comments">Comments</label>
                        </div>
                    </div>
                </h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <div class="student_list" id="offcanvas_student_list">

                </div>
            </div>
        </div>
        <!-- Chat messages -->
        <div class="offcanvas offcanvas-end" tabindex="-1" id="messages_chat_window" aria-labelledby="offcanvasRightLabel">
            <div class="offcanvas-header">
                <h5 id="offcanvasRightLabel">Messages</h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <div class="offcanvas_chat_messages">
                    <!-- Messages appears here -->
                    <div class="msg_history" id="offcanvas_msg_history">
                    </div>
                    <div class="offcanvas_chat_footer">
                        <div class="write_message_div input_msg_write">
                            <input type="text" id="text_message" class="write-message write_msg text_message" placeholder="Type your message here" />
                            <span class="material-icons send_message msg_send_btn" id="msg_send_btn">
                                send
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </div>
</div>

</div>
</div>



</div>
</div>




<!-- Student Template for single student -->
<div class="chat_people" style="display: none;" id="student_template">
    <div class="chat_img_div text-center">
        <img class="chat_img" src="https://ptetutorials.com/images/user-profile.png" alt="user">
        <a class="remote_mic_action" style="cursor:pointer" >
            <span id="remoteMicStatus">
                <i class="fas fa-microphone"></i>
            </span>
        </a>
    </div>
    <div class="chat_ib">
        <div class="contact_card_user_details">
            <div>
                <span class="student_name"></span>
            </div>
            <div class="chat_date signal-bars mt1 sizing-box {{networkQuality()}}">
                <div class="first-bar bar"></div>
                <div class="second-bar bar"></div>
                <div class="third-bar bar"></div>
                <div class="fourth-bar bar"></div>
                <div class="fifth-bar bar"></div>
            </div>
        </div>
    </div>
    <video height="0" width="0" autoplay playsinline></video>
</div>


<!-- Chat Message Template for incoming/outgoing chats -->
<div class="incoming_msg" style="display:none;" id="chat_template_incoming">
    <div class="incoming_msg_img">
        <img src="https://ptetutorials.com/images/user-profile.png">
    </div>
    <div class="received_msg">
        <div class="received_withd_msg">
            <p></p>
        </div>
        <span class="time_date incoming_msg_time"> </span>
    </div>
</div>

<div class="outgoing_msg" style="display:none;" id="chat_template_outgoing">
    <div class="sent_msg_div">
        <div class="sent_msg">
            <p></p>
        </div>
        <span class="time_date outgoing_msg_time"></span>
    </div>
</div>


<!-- Pop Modals -->
<div id="fileUploadModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">File Details</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <input type="file" ng-model="attachment" id="attachment"> <br>

                <textarea class="form-control" ng-model="fileMessage" placeholder="Your comment..."></textarea>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-default" ng-click="uploadFile()">Upload</button>
            </div>

        </div>

    </div>
</div>



<div id="shareModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Invite others</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <input type="text" readonly="true" id="publicLink" value="{{shareUrl}}" class="form-control">

                <p id="shareStatus"></p>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" ng-click="copyToClipboard()">Copy to clipboard</button>
            </div>

        </div>

    </div>
</div>

<div id="devicesModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Devices List</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body" id="show_device_list">

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="change_video_source_btn">Change Video Source</button>
            </div>

        </div>

    </div>
</div>

<div id="leaveConfirmModal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Confirm End lecture</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                Are you sure you want to end this lecture?
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="end_lecture_confirm_btn">Yes, End it</button>
            </div>

        </div>

    </div>
</div>


<div id="recordedPreviewModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" id="recordedPreviewModalBody">

        </div>
    </div>
</div>


<!-- Include Footer -->
<?php include_once(APPPATH . "Views/footer.php"); ?>


<script>
    var streamId = "<?= $stream_id; ?>";
    var roomName = "<?= $session_id; ?>";
    var roomId = 'Live Lecture';
    var type = 'Admin';
    var username = "<?= $username ?>";
    var lectureSaved = "<?= $lectureSaved; ?>";
    var userId = <?= $admin_id ?>;
    console.log("Stream " + streamId + " and session " + roomName + " by user " + username);

    var recording = 0;
    var comments = 1;
    <?php if (isset($live_lecture_details['is_recording']) && !empty($live_lecture_details['is_recording'])) : ?>
        recording = <?= $live_lecture_details['is_recording'] ?>;
    <?php endif; ?>
    <?php if (isset($live_lecture_details['is_comments']) && !empty($live_lecture_details['is_comments'])) : ?>
        comments = <?= $live_lecture_details['is_comments'] ?>;
    <?php endif; ?>
</script>

<!-- Websocket dependancies -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/sockjs-client/1.3.0/sockjs.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/stomp.js/2.3.3/stomp.js"></script>
<script src="https://cdn.jsdelivr.net/npm/js-cookie@2/src/js.cookie.min.js"></script>

<script src="<?php echo base_url('assets/js/websocket.js?v=20220409'); ?>"></script>
<script src="<?php echo base_url('assets/js/video/adapter-latest.js?v=20220409'); ?>"></script>



<script type="module" src="<?php echo base_url('assets/js/antmedia.js?v=20220409'); ?>"></script>
<script src="<?php echo base_url('assets/js/common.js?v=20220409'); ?>"></script>
<script type="module" src="<?php echo base_url('assets/js/video_handlers.js?v=20220409'); ?>"></script>

<!-- Video JS -->
<link href="https://vjs.zencdn.net/7.8.4/video-js.css" rel="stylesheet" />
<script src="https://vjs.zencdn.net/7.8.4/video.js"></script>
<!-- If you'd like to support IE8 (for Video.js versions prior to v7) -->
<script src="https://vjs.zencdn.net/ie8/1.1.2/videojs-ie8.min.js"></script>

<script src="<?php echo base_url('assets/js/live_classrooms.js?v=20220409'); ?>"></script>


<script>
    const body = document.querySelector("body");
    const fab_nav = document.querySelector(".fab-container");
    const fab_rotate = document.querySelector(".fab-rotate");
    const fab_icon = document.querySelector(".fab-icon");

    if (fab_nav) {
        fab_nav.addEventListener("click", function() {
            fab_nav.classList.toggle("fab-opened");
        });

        body.addEventListener("click", function(e) {
            if (e.target != fab_nav && e.target != fab_rotate && e.target != fab_icon) {
                fab_nav.classList.remove("fab-opened");
            }
        });
    }
</script>