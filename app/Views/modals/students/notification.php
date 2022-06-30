<div id="student_notification_modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title"><?= $title; ?></h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <div class="mb-2">
                    <label class="form-label" for="student_import_package">Classroom *</label>
                    <select class="form-control" name="notification_package" id="notification_package" required>
                        <option value="">All students</option>
                        <?php

                        foreach ($all_classrooms_array as $row) {
                            $package_id = $row['id'];
                            $package_name = $row['package_name'];
                            echo "<option value='$package_id'>$package_name</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="mb-2">
                    <label class="form-label" for="notification_type">Notification type *</label>
                    <select class="form-control" name="notification_type" id="notification_type" required>
                        <option value="InviteActivate">Student Invitation with Activation Link</option>
                        <!-- <option value="StudentInvite">Student invitation with username and password</option> -->
                    </select>
                </div>

                <div class="mb-2" id="invite_pwd_div" style="display: none;">
                    <label class="form-label" for="password">Password is </label>
                    <input type="text" class="form-control" maxlength="15" id="mail_password" placeholder="Password pattern e.g. 123456 or your mobile number etc">
                </div>

                <div class="mb-2" id="invite_exam_starts_div" style="display: none;">
                    <label class="form-label" for="notification_type">Exam starts at</label>
                    <select class="form-control" name="exam_starts" id="additional_msg">
                        <option value="Exam will start at Today ">Today</option>
                        <option value="Exam will start at Tomorrow">Tomorrow</option>
                        <option value="Exam will start at NextWeek">Next Week</option>
                        <option value="Exam will start at Later">Later</option>
                    </select>
                </div>

                <p id="sendProgress" class="text-danger"></p>

            </div>
            <div class="modal-footer">
                <input type="hidden" name="institute_id" value="<?= $instituteID; ?>" required />
                <input type="hidden" name="redirect" value="<?= $redirect; ?>" required />
                <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-success" onclick="sendNotification()">Send</button>
            </div>

        </div>
    </div>
</div>

<script>
    //Notify students with email/SMS
    function sendNotification() {

        var classroomSelected = $("#notification_package").val();

        var password = $("#mail_password").val();

        var studentObj = null;
        if (classroomSelected != null && classroomSelected != "") {
            studentObj = {
                currentPackage: {
                    id: classroomSelected
                },
                password: password
            };
        }

        // if (!classroomIdsArray || !classroomIdsArray.length) {
        // 	$("#sendProgress").text("Please select a classroom to import students into");
        // 	return;
        // }

        // Looping through the classrooms array
        // Then importing the excel data for each classroom
        // classroomIdsArray.forEach(function(classroomId) {
        // console.log("Importing students for classroomid:", classroomId);


        var requestType = $("#notification_type").val();
        var additionalMessage = $("#additional_msg").val();
        var mailer = null;
        if (additionalMessage != null && additionalMessage != "") {
            mailer = {
                additionalMessage: additionalMessage
            };
        }

        var request = {
            institute: {
                id: "<?php echo $decryptedinstituteID; ?>"
            },
            student: studentObj,
            mailer: mailer,
            requestType: requestType
        };
        $("#sendProgress").text("Starting SMS/Email process ..");

        // console.log("Uploading ..", fd);
        //Load tokens first
        get_admin_token().then(function(result) {
                var resp = JSON.parse(result);
                if (resp != null && resp.status == 200 && resp.data != null && resp.data.admin_token != null) {
                    $.ajax({
                        type: "POST",
                        url: rootAdmin + "sendNotification",
                        beforeSend: function(request) {
                            request.setRequestHeader("AuthToken", resp.data.admin_token);
                        },
                        data: JSON.stringify(request), // serializes the form's elements.
                        dataType: 'json',
                        contentType: 'application/json',
                        success: function(data) {
                            // show response from the php script.
                            // console.log(data);
                            if (data != null) {
                                if (data.status.statusCode == 200) {
                                    // Snakbar Message
                                    Snackbar.show({
                                        pos: 'top-center',
                                        text: 'SMS/Email process started successfully. Please check after sometime'
                                    });

                                    window.location.reload();
                                } else {
                                    $("#sendProgress").text(data.status.responseText);
                                }
                            } else {
                                $("#sendProgress").text("Some error while connecting to the server ..");
                            }

                            //alert(data);
                        },
                        error: function(err) {
                            $("#sendProgress").text("Some error while connecting to the server ..");
                        }
                    });
                } else {
                    alert("Some error authenticating your request. Please clear your browser cache and try again.");
                }
            })
            .catch(function(error) {
                // An error occurred
                // alert("Exception: " + error);
            });

        // });

    }
</script>