<!-- Include Header -->
<?php include_once(APPPATH . "Views/header.php"); ?>
<!-- Custom CSS -->
<link href="<?php echo base_url('assets/css/tests/video_proctoring.css?v=20220331'); ?>" rel="stylesheet">

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

        <div class="card shadow p-4">
            <div class="text-center my-2">
                <h4><?= $video_session_data['session_name']; ?></h4>
            </div>

            <div class="my-1" id="video_download_progress">

            </div>

            <?php
            if (isset($video_session_data['recording_url']) && !empty($video_session_data['recording_url'])) {
            ?>
                <div class="text-center my-2">
                    <div class="ratio ratio-4x3">
                        <iframe src="<?= $video_session_data['recording_url']; ?>" title="<?= $video_session_data['session_name']; ?>" allowfullscreen></iframe>
                    </div>
                </div>
            <?php
            } else {
            ?>

                <div class="text-center my-2">
                    <div class="ratio ratio-4x3">
                        <iframe src="" title="<?= $video_session_data['session_name']; ?>" id="video_recording" allowfullscreen></iframe>
                    </div>
                </div>

            <?php
            }
            ?>
        </div>

    </div>
</div>

<!-- Include Footer -->
<?php include_once(APPPATH . "Views/footer.php"); ?>

<script>
    $(document).ready(function() {
        // Check video url present else call zoom recording API
        var recording_url = "<?= $video_session_data['recording_url']; ?>";
        if (recording_url == "") {
            view_zoom_recording();
        }
    });

    function view_zoom_recording() {
        var institute_id = <?= $institute_id; ?>;
        var live_session_id = <?= $video_session_id; ?>;
        $("#video_download_progress").text("Please wait, loading video...");
        var request = {
            lecture: {
                id: live_session_id
            },
            institute: {
                id: institute_id
            }
        };
        get_admin_token().then(function(result) {
                var resp = JSON.parse(result);
                if (resp != null && resp.status == 200 && resp.data != null && resp.data.admin_token != null) {
                    $.ajax({
                        type: "POST",
                        url: rootAdmin + "downloadProctoringRecording",
                        beforeSend: function(request) {
                            request.setRequestHeader("AuthToken", resp.data.admin_token);
                        },
                        data: JSON.stringify(request),
                        dataType: 'json',
                        contentType: 'application/json',
                        success: function(data) {
                            console.log(data);
                            if (data != null) {
                                if (data.status.statusCode == 200) {
                                    var response_url = "";
                                    data.lectures.forEach(function(item) {
                                        response_url = item.lecture.video_url;
                                    });
                                    console.log(response_url);
                                    $("#video_recording").attr("src",response_url);
                                    $("#video_download_progress").text("");
                                } else {
                                    $("#video_download_progress").text(data.status.responseText);
                                }
                            } else {
                                $("#video_download_progress").text("Some error while connecting to the server ..");
                            }
                        }
                    });
                } else {
                    $("#video_download_progress").text("Some error authenticating your request. Please clear your browser cache and try again.");
                }
            })
            .catch(function(error) {
                // An error occurred
                console.log("Error: ", error);
            });
    }
</script>