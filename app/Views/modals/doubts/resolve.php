<div id="resolve_doubts_modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title"><?= $title; ?></h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <?php
            $attributes = ['class' => 'cmxform', 'id' => 'myform'];
            ?>
            <form action="" method="post" enctype="multipart/form-data" id="resolve_doubt_form">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xs-12 mb-2">
                            <label for="add_test_name">Solution Video URL</label>
                            <input type="text" class="form-control" name="video_url" id="video_url">
                        </div>
                        <div class="col-xs-12 mb-2">
                            <label for="solution-image-input">Solution photo</label> <span>(optional) (Only 1 photo)</span>
                            <input type="file" class="form-control" name="solution-image" id="solution_img" onchange="validateFile(this);" accept="image/*">
                        </div>

                        <div class="col-xs-12 mb-2">
                            <label for="add_test_name">Details</label>
                            <textarea rows="7" cols="" class="form-control" name="doubt_resolution_text" id="doubt_resolution_textarea" required="required"></textarea>
                        </div>

                        <p class="text-center" id="addProgress"></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" id="doubt_question_id" value="<?= $doubt_question_id; ?>" required>
                    <input type="hidden" id="doubt_question_type" name="doubt_question_type" value="<?= $doubt_question_type; ?>">

                    <input type="hidden" name="resource_id" value="<?= $feedback_id ?>">
                    <input type="hidden" id="instituteId" name="instituteId" value="<?= $instituteId ?>">



                    <input type="hidden" id="doubt_answered_by" name="doubt_answered_by" value="<?= decrypt_cipher(session()->get('login_id')); ?>">
                    <input type="hidden" class="" name="session_type" value="Admin">
                    <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success" name="add_package_form_submit">Resolve</button>
                </div>
            </form>
        </div>

    </div>
</div>



<script>
    //Resolve doubt using service/AJAX
    $('#resolve_doubt_form').submit(function(evt) {

        evt.preventDefault();
        console.log("Form submit blocked ..");

        var video_url = $("#video_url").val();
        var image = document.getElementById('solution_img').files[0];
        var solution = $("#doubt_resolution_textarea").val();
        var doubtId = $("#doubt_question_id").val();
        var instituteId = $("#instituteId").val();
        var request = {
            feedback: {
                feedbackVideoUrl: video_url,
                feedbackResolutionText: solution,
                resolution: 'Resolved'
            },
            institute: {
                id: instituteId
            }
        }

        //Decide type of doubt
        var type = $("#doubt_question_type").val();
        console.log("Type found is " + type);
        if (type == 'video') {
            request.feedback.videoId = doubtId;
        } else if (type == 'general') {
            request.feedback.id = doubtId;
        } else {
            request.feedback.questionId = doubtId;
        }


        var fd = new FormData();
        fd.append('file', image);
        fd.append('request', JSON.stringify(request));

        $("#doubt_progress").text("Saving ..");

        console.log("Resolving", request);

        get_admin_token().then(function(result) {
            var resp = JSON.parse(result);
            if (resp != null && resp.status == 200 && resp.data != null && resp.data.admin_token != null) {
                token = resp.data.admin_token;
                $.ajax({
                    url: rootAdmin + "resolveDoubtWithFile",
                    beforeSend: function(request) {
                        request.setRequestHeader("AuthToken", token);
                    },
                    type: "POST",
                    data: fd,
                    success: function(msg) {
                        // console.log("Response", msg);
                        if (msg != null && msg.status != null && msg.status.statusCode == 200) {

                            // Send resolved doubt notification
                            send_doubt_notification(doubtId, type)
                                .then(function(result) {
                                    var response = JSON.parse(result);
                                    console.log("response", response);
                                    if (response.status.statusCode == 200) {
                                        console.log("Notification sent successfully");
                                    } else {
                                        console.log("Notification not sent");
                                    }
                                    Snackbar.show({
                                        pos: 'top-center',
                                        text: 'Successfully resolved doubt'
                                    });
                                    window.location.reload();
                                })
                                .catch(function(error) {
                                    // An error occurred
                                    alert("Exception: " + error);
                                });
                        } else {
                            $("#doubt_progress").text("Error in resolving doubt. Please try again later.");
                        }

                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        $("#doubt_progress").text("Error in resolving doubt.. Please try again ");
                    },
                    cache: false,
                    contentType: false,
                    processData: false
                });
            }
        });
    });
</script>


<script>
    function send_doubt_notification(doubtId, doubt_content_type) {
        return new Promise(function(resolve, reject) {
            var feedbackObj = {
                id: doubtId,
            };
            if (doubt_content_type == "video") {
                feedbackObj = {
                    videoId: doubtId,
                };
            } else if (doubt_content_type == "question") {
                feedbackObj = {
                    questionId: doubtId,
                };
            }
            var postdata = {
                feedback: feedbackObj,
                requestType: "DoubtResolved",
            };

            var xhr = $.ajax({
                    type: "POST",
                    data: JSON.stringify(postdata),
                    dataType: "json",
                    url: rootAdmin + "sendDoubtNotification",
                    contentType: "application/json",
                })
                .done(function(response) {
                    // success logic here
                    resolve(JSON.stringify(response));
                    // console.log(response);
                })
                .fail(function(jqXHR) {
                    // Our error logic here
                    reject(jqXHR.responseText);
                });
        });
    }
</script>