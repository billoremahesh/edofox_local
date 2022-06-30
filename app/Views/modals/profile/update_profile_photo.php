<div class="modal fade" id="update_profile_photo_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h6 class="modal-title"><?= $title;  ?></h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="" method="post" enctype="multipart/form-data" id="uploadProfilePicForm">

                <div class="modal-body">
                    <div>
                        <input type="file" name="profile_photo" class="form-control form-control-user" id="fileToUpload" required>
                        <p style="color: #858796;padding-top:8px;"> * The file should be an image ( png, jpeg, jpg ) with max size 500KB. </p>
                    </div>

                    <div class="error_msg_div mx-2 text-danger" style="display: none;">
                    </div>

                </div>

                <div class="modal-footer">
                    <input type="hidden" id="adminId" name="profile_id" value="<?= decrypt_cipher($profile_id); ?>" required />
                    <input type="hidden" id="institute_id" name="institute_id" value="<?= decrypt_cipher($institute_id); ?>" required />
                    <input type="hidden" name="redirect" value="<?= $redirect; ?>" required />
                    <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary submitBtn">Update</button>
                </div>

            </form>
        </div>
    </div>
</div>


<script>
    $('#uploadProfilePicForm').submit(function(evt) {
        console.log("submit prevented!");
        $('.error_msg_div').css('display', 'none');
        $(".submitBtn").attr("disabled", true);
        evt.preventDefault();
        var instituteId = $("#institute_id").val();
        var adminId = $("#adminId").val();
        var f = document.getElementById('fileToUpload').files[0];
        var fileSize = f.size;
        var fsize = Math.round((fileSize / 1024));
        if (fsize > 500) {
            $(".submitBtn").attr("disabled", false);
            $('.error_msg_div').css('display', 'block');
            $('.error_msg_div').html("File too Big, please select a file less than 500KB");
            return false;
        }
        var fd = new FormData();
        fd.append("file", f);
        var request = {
            institute: {
                id: instituteId,
                adminId: adminId
            },  
        };
        fd.append("request", JSON.stringify(request));

        $("#error").text("Saving .. Please wait ..");

        get_admin_token().then(function(result) {
            var resp = JSON.parse(result);
            if (resp != null && resp.status == 200 && resp.data != null && resp.data.admin_token != null) {

                token = resp.data.admin_token;
                $.ajax({
                    url: rootAdmin + "uploadLogo",
                    beforeSend: function(request) {
                        request.setRequestHeader("AuthToken", token);
                    },
                    type: "POST",
                    data: fd,
                    success: function(msg) {
                        // console.log("Response", msg);
                        if (msg != null && msg.status != null && msg.status.statusCode == 200) {
                            Snackbar.show({
                                pos: 'top-center',
                                text: 'Successfully updated profile picture'
                            });
                            window.location.reload();
                        } else {
                            $(".submitBtn").attr("disabled", false);
                            $("#error").text("Error in saving .. Please try again ..");
                        }

                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        $(".submitBtn").attr("disabled", false);
                        $("#error").text("Error in uploading .. Please try again ..");
                    },
                    cache: false,
                    contentType: false,
                    processData: false
                });
            }
        });

    });
</script>