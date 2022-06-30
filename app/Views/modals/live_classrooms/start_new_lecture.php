<div class="modal fade" id="start_new_live_lecture_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"> <?= $title; ?> </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="post" enctype="multipart/form-data" id="create_new_lecture_form">

                <div class="modal-body">

                    <div class="row">
                        <div class="col-12 mb-3">
                            <label> Classroom </label>
                            <select class="form-select classroom_dropdown" name="classroom" id="lecture_classroom" required>
                                <option></option>
                                <?php
                                if (!empty($classroom_list)) {
                                    foreach ($classroom_list as $classroom) {
                                ?>
                                        <option value="<?= $classroom['id']; ?>"> <?= $classroom['package_name']; ?></option>
                                <?php
                                    }
                                }
                                ?>
                            </select>
                        </div>

                        <div class="col-12 mb-3">
                            <input type="text" class="form-control" name="lecture_name" id="lecture_name" placeholder="Lecture name" required>
                        </div>

                        <div class="col-12 mb-3">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="lecture_time_check" id="lecture_time_now" value="Now" onclick="set_dates(this.value)" checked>
                                <label class="form-check-label" for="lecture_time_now">
                                    Now
                                </label>
                            </div>


                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="lecture_time_check" id="lecture_time_schedule" value="Schedule" onclick="set_dates(this.value)">
                                <label class="form-check-label" for="lecture_time_schedule">
                                    Schedule
                                </label>
                            </div>
                        </div>

                        <div class="col-6 mb-2">
                            <label class="form-label" for="start_date">Start date</label>
                            <input type="text" class="form-control form_datetime1" id="start_date" name="start_date" autocomplete="off" required>
                        </div>

                        <div class="col-6 mb-2">
                            <label class="form-label" for="end_date">End date</label>
                            <input type="text" class="form-control form_datetime2" id="end_date" name="end_date" autocomplete="off" required>
                        </div>

                        <div class="col-12 mb-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1" id="recording_session">
                                <label class="form-check-label" for="recording_session">
                                    Record the session
                                </label>
                            </div>
                        </div>

                    </div>

                </div>

                <div class="p-2" id="error_msg"></div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success submitBtn"> Start </button>
                </div>

            </form>

        </div>

    </div>
</div>


<script type="text/javascript">
    $(document).ready(function() {
        set_dates("Now");
    });


    var date = new Date();
    var current_year = date.getFullYear();
    var current_month = ("0" + (date.getMonth() + 1)).slice(-2);
    var current_day = ("0" + date.getDate()).slice(-2);
    var current_hour = (date.getHours() < 10 ? '0' : '') + date.getHours();
    var post_one_hour = (date.getHours() < 10 ? '0' : '') + (date.getHours() + 1);
    var current_minutes = (date.getMinutes() < 10 ? '0' : '') + date.getMinutes();
    var today_current_time = current_year + "-" + current_month + "-" + current_day + " " + current_hour + ":" + current_minutes;

    $(".form_datetime1").datetimepicker({
        format: "yyyy-mm-dd hh:ii",
        autoclose: true,
        startDate: today_current_time,
        todayBtn: true,
        fontAwesome: 'font-awesome',
        pickerPosition: "bottom"
    }).on('changeDate', function(selected) {
        var minDate = $(".form_datetime1").val();
        var maxDate = $(".form_datetime1").val() + 4;
        $(".form_datetime2").datetimepicker('setStartDate', minDate);
        $(".form_datetime2").datetimepicker('setEndDate', maxDate);
    });

    $(".form_datetime2").datetimepicker({
        format: "yyyy-mm-dd hh:ii",
        autoclose: true,
        fontAwesome: 'font-awesome',
        pickerPosition: "bottom"
    }).on('changeDate', function() {
        var maxDate = $(".form_datetime2").val();
        $(".form_datetime1").datetimepicker('setEndDate', maxDate);
    });
</script>

<script>
    function set_dates(val) {
        var date = new Date();
        var current_year = date.getFullYear();
        var current_month = ("0" + (date.getMonth() + 1)).slice(-2);
        var current_day = ("0" + date.getDate()).slice(-2);
        var current_hour = (date.getHours() < 10 ? '0' : '') + date.getHours();
        var post_one_hour =  (date.getHours() + 1);
        post_one_hour = (post_one_hour < 10 ? '0' : '') + post_one_hour;
        var current_minutes = (date.getMinutes() < 10 ? '0' : '') + date.getMinutes();
        var today_current_time = current_year + "-" + current_month + "-" + current_day + " " + current_hour + ":" + current_minutes;
        var today_one_hour = current_year + "-" + current_month + "-" + current_day + " " + post_one_hour + ":" + current_minutes;
        if (val == "Now") {
            $(".form_datetime1").val(today_current_time);
            $(".form_datetime2").val(today_one_hour);
        } else {
            $(".form_datetime1").val("");
            $(".form_datetime2").val("");
        }
        $(".form_datetime1").trigger("changeDate");
    }
</script>


<script>
    $('#create_new_lecture_form').submit(function(evt) {
        $(".submitBtn").attr("disabled", true);
        evt.preventDefault();
        var session_time = $('input[name="lecture_time_check"]:checked').val();
        var recording_session = document.getElementById('recording_session');
        if (recording_session.checked) {
            var classroom = {
                id: $("#lecture_classroom").val(),
                name: $("#lecture_name").val(),
                status: 'Recording'
            };
        } else {
            var classroom = {
                id: $("#lecture_classroom").val(),
                name: $("#lecture_name").val()
            };
        }
        var request = {
            student: {
                currentPackage: classroom
            },
            startTime: $("#start_date").val(),
            endTime: $("#end_date").val(),
            requestType: "",
            institute: {
                adminId: <?= $admin_id; ?>,
            },
        };

        callAdminServiceJSONPost("createLiveLecture", request).then(function(response) {
                if (response.status.statusCode > 0) {
                    Snackbar.show({
                        pos: 'top-center',
                        text: 'Live lecture created successfully'
                    });
                    // console.log(response);
                    var live_lecture_session = response.packages[0];
                    // console.log(live_lecture_session);
                    if (session_time == "Now") {
                        window.location = base_url + "/liveClassrooms/join/" + live_lecture_session.sessionId + "/" + live_lecture_session.streamId;
                    } else {
                        window.location = base_url + "/liveClassrooms";
                    }

                } else {
                    $("error_msg").html("Some error occured in starting live lecture");
                    $(".submitBtn").attr("disabled", false);
                }
            })
            .catch(function(error) {
                $(".submitBtn").attr("disabled", false);
                console.log("Error in service call " + error);
                $("error_msg").html("Error in service call");
            });
    })
</script>


<script>
    // bootstrap 5 select2 not working in modal bug
    // https://stackoverflow.com/questions/18487056/select2-doesnt-work-when-embedded-in-a-bootstrap-modal/33884094#33884094
    // Initializing select2
    $('.classroom_dropdown').select2({
        width: "100%",
        dropdownParent: $("#start_new_live_lecture_modal")
    });
</script>