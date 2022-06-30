<!-- Include Header -->
<?php include_once(APPPATH . "Views/header.php"); ?>

<!-- Custom CSS -->
<link href="<?php echo base_url('assets/css/dlp/manage_dlp.css?v=20211208'); ?>" rel="stylesheet">
<link href="<?php echo base_url('assets/css/dlp/dlp_content.css?v=20211123'); ?>" rel="stylesheet">

<!-- Video JS -->
<link href="https://vjs.zencdn.net/7.8.4/video-js.css" rel="stylesheet" />

<script src="https://vjs.zencdn.net/7.8.4/video.js"></script>

<!-- If you'd like to support IE8 (for Video.js versions prior to v7) -->
<script src="https://vjs.zencdn.net/ie8/1.1.2/videojs-ie8.min.js"></script>

<div id="content">
    <div class="container-fluid mt-4">

        <div class="flex-container-column">
            <div>
                <label class="h5 text-gray-800 text-uppercase"> <?= $subject_details['subject']; ?> </label>
            </div>
            <div class="breadcrumb_div" aria-label="breadcrumb">
                <ol class="breadcrumb_custom">
                    <li class="breadcrumb_item"><a href="<?php echo base_url('/home'); ?>"> Home </a></li>
                    <li class="breadcrumb_item"><a href="<?php echo base_url('dlp'); ?>"> DLP </a></li>
                    <li class="breadcrumb_item active" aria-current="page"> <?= $title; ?> </li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3">
                <div>
                    <div id="chapters-list-content">

                        <?php if (!empty($chapters_data)) : ?>
                            <div class="list-group list-group-flush mb-4">

                                <?php
                                foreach ($chapters_data as $chapter_data) :
                                ?>

                                    <button type="button" class="list-group-item list-group-item-action" onclick="fetchTotalContentList('<?= $chapter_data['id']; ?>','<?= $classroom_id; ?>')"><?= $chapter_data['chapter_name']; ?></button>

                                <?php endforeach; ?>

                            </div>
                        <?php else : ?>
                            <h4>No DLP Chapters found. Start by adding some DLP Chapters.</h4>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
            <div class="col-md-9">
                <div class="card p-4">



                    <!-- Document Progress Bar -->
                    <div id="progressList">

                    </div>

                    <div id="chapters-content-div">

                    </div>

                    <!-- Loading Div -->
                    <div class="text-center my-2 d-none" id="loading-div">
                        <i class='fas fa-atom fa-spin fa-2x fa-fw'></i>
                    </div>
                </div>

                <!-- Error Message -->
                <div class="error_msg_div" style="display: none;">
                    <p class="error_msg_div_text">Error in processing. Try again.</p>
                </div>

            </div>
        </div>
        <div id="progressList">

        </div>
    </div>
</div>



<div id="displayCourseModal" class="modal fade bs-example-modal-lg" data-backdrop="static" role="dialog" aria-labelledby="courseDataModalLabel">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content" id="displayCourseModalBody">

        </div>
    </div>
</div>





<!-- Include Footer -->
<?php include_once(APPPATH . "Views/footer.php"); ?>
<script src="<?php echo base_url('assets/js/manage_dlp.js'); ?>"></script>

<script src="https://sdk.amazonaws.com/js/aws-sdk-2.706.0.min.js"></script>

<script>
    function loader(show) {
        if (show) {
            $("#loading-div").removeClass("d-none");
        } else {
            $("#loading-div").addClass("d-none");
        }
    }
</script>

<script>
    var instituteId = <?= $instituteID ?>;
    sessionStorage.setItem("chapter_id", <?= $chapter_id ?>);
    sessionStorage.setItem("classroom_id", <?= $classroom_id ?>);
    sessionStorage.setItem("subject_id", <?= $subject_id ?>);
    var subjectId = sessionStorage.getItem("subject_id");
    var subjectName = sessionStorage.getItem("subject_name");
    var classroomId = sessionStorage.getItem("classroom_id");
    var chapterId = sessionStorage.getItem("chapter_id");
</script>


<script>
    var videoId;

    function displayCourseData(subtopic, videoUrl, testId, foundationId, status, progress) {
        // console.log("Foundation ID", foundationId);
        videoId = foundationId;
        subtopic = encodeURI(subtopic);
        videoUrl = encodeURI(videoUrl);
        testId = encodeURI(testId);
        document.getElementById("displayCourseModalBody").innerHTML = "";
        // if (videoUrl.length == 0) {
        //     document.getElementById("displayCourseModalBody").innerHTML = "";
        //     return;
        // } else {
            var dataString =
                "subtopic=" + subtopic + "&videoUrl=" + videoUrl + "&testId=" + testId + "&status=" + status + "&progress=" + progress;
            $.ajax({
                type: "POST",
                data: dataString,
                url: base_url + "/dlp/display_course_videos",
                success: function(data) {
                    document.getElementById("displayCourseModalBody").innerHTML = data;
                    $("#displayCourseModal").modal('show');
                },
            });
        // }
    }

    var videojsPlayer;
    $('#displayCourseModal').on('shown.bs.modal', function() {
        videojsPlayer = videojs('my-video');
        console.log(videojsPlayer);
        videojsPlayer.responsive(true);

        videojsPlayer.hlsQualitySelector({
            displayCurrentQuality: true
        });


    });

    // To pause the playing video, when the modal is closed
    $('#displayCourseModal').on('hidden.bs.modal', function() {
        $(this).find('iframe').attr("src", "");
        if (videojsPlayer != null) {
            videojsPlayer.dispose();
        }

    });


    // To pause the playing video, when the modal is closed
    $('#displayCourseModal').on('hidden.bs.modal', function() {
        $(this).find('iframe').attr("src", "");
    });
</script>

<script>
    $(document).ready(function() {
        if (subjectId && chapterId && classroomId) {
            fetchTotalContentList(chapterId, classroomId);
        }
    });
</script>


<script>
    //To change the video and resources content list on click via ajax
    function fetchTotalContentList(chapterId, classroomId) {

        // console.log("Inside fetchTotalContentList." + chapterId + " - " + classroomId);

        // Saving the ids in session for fetching clicked chapters content
        sessionStorage.setItem("chapter_id", chapterId);
        sessionStorage.setItem("classroom_id", classroomId);

        //Setting the chapter id field in add new resource modal for dynamic addition
        $("#chapter_id_input").val(chapterId);

        $('.error_msg_div').css('display', 'none');
        $("#chapters-content-div").html("");
        loader(true);
        load_chapter_content(chapterId, classroomId)
            .then(function(result) {
                var response = JSON.parse(result);
                loader(false);
                $("#chapters-content-div").html(response);
                $("#chapters-content-loading-div").hide();

                initializeTooltip();
            })
            .catch(function(error) {
                // An error occurred
                $(".error_msg_div_text").html("There is some error connecting with the server .. Please try again ..");
                $('.error_msg_div').css('display', 'block');
            });
        return false;
    }
</script>