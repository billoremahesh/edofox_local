<!-- Include Header -->
<?php include_once(APPPATH . "Views/header.php"); ?>

<!-- Custom CSS -->
<link href="<?php echo base_url('assets/css/attendance/overview.css?v=20220609'); ?>" rel="stylesheet">


<div id="content">
    <div class="container-fluid my-2">

        <div class="flex-container-column">
            <div>
                <label class="h5 text-gray-800 text-uppercase"> <?= $title; ?> </h5>
            </div>
            <div class="breadcrumb_div" aria-label="breadcrumb">
                <ol class="breadcrumb_custom">
                    <li class="breadcrumb_item"><a href="<?php echo base_url('/home'); ?>"> Home </a></li>
                    <li class="breadcrumb_item"><a href="<?php echo base_url('/attendance'); ?>"> Attendance </a></li>
                    <li class="breadcrumb_item active" aria-current="page"> <?= $title; ?> </li>
                </ol>
            </div>
        </div>

        <div class="bg-white rounded shadow p-2 my-4" style="max-width: 600px;margin:auto;">
            <div class="text-center"><b>Session : <?= strtoupper($schedule_details['title']); ?></b></div>
            <?php
            $formatted_attendance_date = date_create($attendance_date);
            $formatted_attendance_date = date_format($formatted_attendance_date, "d-M-Y");
            ?>
            <div><b>Date: <?= $formatted_attendance_date; ?></b></div>
            <div>Classroom: <?= $schedule_details['package_name']; ?></div>
            <div>Subject: <?= $schedule_details['subject']; ?></div>
            <div><?= $schedule_details['starts_at']; ?> - <?= $schedule_details['ends_at']; ?></div>
            <div>Duration : <?= secToHR($schedule_details['duration']); ?> Minutes</div>

            <div class="row row-cols-auto justify-content-center my-2">
                <div class="col">
                    <div class="border-primary h-5 px-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase">
                            Total Students: <span class="font-weight-bold text-gray-800" id="total_students_count"> 0 </span>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="border-success h-5 px-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase">
                            Present: <span class="font-weight-bold text-gray-800" id="present_students_cnt"> 0 </span>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="border-warning h-5 px-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase">
                            Absent: <span class="font-weight-bold text-gray-800" id="absent_students_cnt"> 0 </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row row-cols-auto justify-content-center my-2">
                <div class="col">
                    <a href="<?= base_url('attendance/take_attendance/' . $encrypt_session_id . '/' . $attendance_date); ?>" class="btn btn-primary"> Update Attendance </a>
                </div>
                <div class="col">
                    <a class="btn btn-outline-primary" onclick="sendAbsentNotification()" style="cursor:pointer"><i class="fa fa-lg fa-envelope" aria-hidden="true"></i> SEND ABSENT SMS/EMAIL </a>
                </div>
            </div>
        </div>

        <!-- Loader Div -->
        <div id="custom_loader"></div>
    </div>
</div>

<!-- Include Footer -->
<?php include_once(APPPATH . "Views/footer.php"); ?>

<script>
    var scheduleId = "<?= $session_id; ?>";
    var attendance_date = "<?= $attendance_date; ?>";
    var institute_id = "<?= $institute_id; ?>";
    $(document).ready(function() {
        get_attendance_data();
    });
</script>

<script>
    var scheduleDataId = null;

    function get_attendance_data() {
        var request = {
            schedule: {
                id: scheduleId,
                date: attendance_date
            },
            sortFilter: $("#order_by_filter").val()
        };
        toggle_custom_loader(true, "custom_loader");
        callAdminServiceJSONPost("getAttendance", request).then(function(response) {
                if (response.status.statusCode > 0) {
                    // console.log(response.students);
                    var attendance_data = format_attendance_data(response.students);
                    $("#attendance_student_data").html(attendance_data);
                    toggle_custom_loader(false, "custom_loader");

                    if (response.student != null && response.student.schedule != null) {
                        scheduleDataId = response.student.schedule[0].scheduleId;
                    }

                } else {
                    Snackbar.show({
                        pos: 'top-center',
                        text: 'Some error occured in fetching student data'
                    });
                    toggle_custom_loader(false, "custom_loader");
                }
            })
            .catch(function(error) {
                Snackbar.show({
                    pos: 'top-center',
                    text: 'Error in service call'
                });
                toggle_custom_loader(false, "custom_loader");
            });
    }
</script>

<script>
    function format_attendance_data(data) {
        var html = "";
        if (data != null) {
            var absent_students = 0;
            var present_students = 0;
            $.each(data, function(objIndex, obj) {
                if (obj.present != null && obj.present == true) {
                    present_students = present_students + 1;
                } else {
                    absent_students = absent_students + 1;
                }
            });
            total_students = data.length;
            $("#total_students_count").html(total_students);
            $("#present_students_cnt").html(present_students);
            $("#absent_students_cnt").html(absent_students);
        }
        return html;
    }
</script>

<script>
    function sendAbsentNotification() {
        if (scheduleDataId == null) {
            alert("Please take attendance first in order to send notifications");
            return;
        }
        var request = {
            schedule: {
                id: scheduleDataId
            },
            requestType: 'AbsentStudent'
        };

        callAdminServiceJSONPost("sendNotification", request).then(function(response) {
                console.log("Response", response);
                if (response != null && response.status.statusCode > 0) {
                    Snackbar.show({
                        pos: 'top-center',
                        text: 'SMS/Email process started successfully'
                    });
                    //window.location.reload();
                } else {
                    Snackbar.show({
                        pos: 'top-center',
                        text: 'Some error sending SMS/Email. Please try again'
                    });
                }
            })
            .catch(function(error) {
                Snackbar.show({
                    pos: 'top-center',
                    text: 'Could not connect with server.. Please try again'
                });
            });
    }
</script>