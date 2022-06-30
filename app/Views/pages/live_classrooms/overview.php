<!-- Include Header -->
<?php include_once(APPPATH . "Views/header.php"); ?>

<!-- Custom CSS -->
<link href="<?php echo base_url('assets/css/live_classrooms/overview.css?v=20220407'); ?>" rel="stylesheet">

<div id="content">
    <div class="container-fluid mt-4">

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

        <div class="d-flex flex-row mb-4">

            <div class="flex-nowrap search_input_wrap">
                <input class="form-control text-black-50" id='searchbox' autocomplete="off" style='width: 200px; color: blue; font-weight: bold;' placeholder="Search" name="srch-term" type="text" placeholder="Search" aria-label="Search" aria-describedby="addon-search">
            </div>

            <div class="mx-2">
                <select class="form-select lecture_status_filter" id="lecture_status_filter">
                    <option value="Todays"> Todays Sessions </option>
                    <option value="Scheduled"> Scheduled </option>
                    <option value="Completed"> Completed </option>
                </select>
            </div>

            <a href="#" onclick="show_add_modal('modal_div','start_new_live_lecture_modal','liveClassrooms/start_new_live_lecture');" data-toggle='tooltip' title='Start new lecture'>
                <span class="material-icons action_button_plus_icon">
                    add
                </span>
            </a>

        </div>

        <!-- live lectures list  -->
        <div class="row" id="live_classrooms_list">

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

<!-- Websocket dependancies -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/sockjs-client/1.3.0/sockjs.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/stomp.js/2.3.3/stomp.js"></script>
<script src="https://cdn.jsdelivr.net/npm/js-cookie@2/src/js.cookie.min.js"></script>

<script src="<?php echo base_url('assets/js/websocket.js?v=20220409'); ?>"></script>
<script src="<?php echo base_url('assets/js/video/adapter-latest.js?v=20220409'); ?>"></script>


<script type="module" src="<?php echo base_url('assets/js/antmedia.js?v=20220409'); ?>"></script>
<script type="module" src="<?php echo base_url('assets/js/video_handlers.js?v=20220409'); ?>"></script>

<!-- Video JS -->
<link href="https://vjs.zencdn.net/7.8.4/video-js.css" rel="stylesheet" />
<script src="https://vjs.zencdn.net/7.8.4/video.js"></script>
<!-- If you'd like to support IE8 (for Video.js versions prior to v7) -->
<script src="https://vjs.zencdn.net/ie8/1.1.2/videojs-ie8.min.js"></script>


<script>
    $(document).ready(function() {
        load_live_classrooms();
        // Onchange of custom filters
        $(".lecture_status_filter").change(function() {
            load_live_classrooms();
        });

        $("#searchbox").keyup(function() {
            load_live_classrooms();
        });


        var videojsPlayer;
        $("#recordedPreviewModal").on("shown.bs.modal", function() {
            videojsPlayer = videojs("my-video");
            console.log(videojsPlayer);
            videojsPlayer.responsive(true);

            videojsPlayer.hlsQualitySelector({
                displayCurrentQuality: true,
            });
        });

        // To pause the playing video, when the modal is closed
        $("#recordedPreviewModal").on("hidden.bs.modal", function() {
            $(this).find("iframe").attr("src", "");
            if (videojsPlayer != null) {
                videojsPlayer.dispose();
            }
        });

        // To pause the playing video, when the modal is closed
        $("#recordedPreviewModal").on("hidden.bs.modal", function() {
            $(this).find("iframe").attr("src", "");
        });

    });
</script>


<script>
    function load_live_classrooms() {
        $("#live_classrooms_list").html(show_loading());
        $.ajax({
            type: 'post',
            url: base_url + '/liveClassrooms/load_live_lectures',
            data: {
                status: $("#lecture_status_filter").val(),
                search_string: $("#searchbox").val()
            },
            success: function(response) {
                $("#live_classrooms_list").html(format_live_lecture_data(response));
            }
        });
    }
</script>

<script>
    function format_live_lecture_data(data) {
        var session_status = $("#lecture_status_filter").val();
        var html = "";
        if (data != null) {
            data = JSON.parse(data);
            if (data.length == 0) {
                html = "<div class='col-sm-12 col-md-12'> No live lecture available</div>";
            }
            $.each(data, function(objIndex, obj) {
                var lecture_start_time = "";
                var lecture_end_time = "";
                var card_button = "";
                if (session_status == 'Scheduled') {
                    lecture_start_time = "Starts at " + obj.start_date;
                    lecture_end_time = "Ends at " + obj.end_date;
                }
                if (session_status == 'Todays') {
                    lecture_start_time = "Started at " + obj.start_date;
                    lecture_end_time = "Ends at " + obj.end_date;
                    var join_link = apachehost + "/liveClassrooms/join/" + obj.schedule_id + "/" + obj.stream_id;
                    card_button = "<a href=' " + join_link + " '  class='card_action_btn'>Join</a>";
                }
                if (session_status == 'Completed') {
                    lecture_start_time = "Started at " + obj.start_date;
                    if (obj.end_date != null) {
                        lecture_end_time = "Ended at " + obj.end_date;
                    }

                    if (obj.recording_url != null && obj.recording_url != '') {
                        card_button = "<a onclick=\"viewRecordingModal('" + obj.session_name + "','" + obj.recording_url + "');\" class='card_action_btn'>Watch</a>";
                    }

                }
                html = html + "<div class='col-sm-12 col-md-3'><div class='live_lecture_card'>";
                html = html + "<div class='card_head'>";
                html = html + "<div class='card_title'>" + obj.session_name.toUpperCase() + "</div>";
                html = html + "<div class='card_subtitle'>Classroom: " + obj.package_name + "</div>";
                html = html + "</div>";
                html = html + "<div class='card_body'>";
                html = html + "<div class='card_supporting_text'>" + lecture_start_time + "</div>";
                html = html + "<div class='card_supporting_text'>" + lecture_end_time + "</div>";
                html = html + "</div>";
                html = html + "<div class='card_actions'>";
                html = html + card_button;
                html = html + "</div>";
                html = html + "</div></div>";
            });
        } else {
            html = "<div class='col-sm-12 col-md-12'> No live lecture available</div>";
        }
        return html;
    }
</script>

<script>
    function show_loading() {
        var html = "<div class='d-flex align-items-center'><strong>Loading data, please wait...</strong><div class='spinner-border ms-auto' role='status' aria-hidden='true'></div></div>";
        return html;
    }
</script>

<script>
    function viewRecordingModal(lecture_name, lecture_videoUrl) {
        document.getElementById("recordedPreviewModalBody").innerHTML = "";
        var dataString =
            "subtopic=" +
            lecture_name +
            "&videoUrl=" +
            lecture_videoUrl +
            "&testId=&status=&progress=";
        $.ajax({
            type: "POST",
            data: dataString,
            url: base_url + "/dlp/display_course_videos",
            success: function(data) {
                document.getElementById("recordedPreviewModalBody").innerHTML = data;
                $("#recordedPreviewModal").modal("show");
            },
        });
    }
</script>