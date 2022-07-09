<!-- Include Header -->
<?php include_once(APPPATH . "Views/header.php"); ?>

<!-- Custom CSS -->
<link href="<?php echo base_url('assets/css/attendance/take_attendance.css?v=20220602'); ?>" rel="stylesheet">


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

        <div class="bg-white rounded shadow p-2 my-4" style="max-width: 800px;margin:auto;">
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
        </div>

        <!-- Loader Div -->
        <div id="custom_loader"></div>


        <div style="max-width: 800px;display:block;margin:auto;">
            <div class="row row-cols-auto justify-content-center my-1">
                <div class="col">
                    <div class="card border-left-primary shadow h-5 py-2 px-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase">
                            Total Students: <span class="font-weight-bold text-gray-800" id="total_students_count"> 0 </span>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card border-left-success shadow h-5 py-2 px-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase">
                            Present: <span class="font-weight-bold text-gray-800" id="present_students_cnt"> 0 </span>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card border-left-warning shadow h-5 py-2 px-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase">
                            Absent: <span class="font-weight-bold text-gray-800" id="absent_students_cnt"> 0 </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row row-cols-auto my-1">
                <div class="col">
                    <div class="h-5 py-2 px-2">
                        <button class="btn btn-outline-primary select_all_students"> Select All </button>
                    </div>
                </div>
                <div class="col">
                    <div class="h-5 py-2 px-2">
                        <button class="btn btn-outline-secondary unselect_all_students"> UnSelect All</button>
                    </div>
                </div>
                <div style="float: right; margin: 8px;">
                    <a class="btn btn-outline-primary" onclick="sendAbsentNotification()" style="cursor:pointer"><i class="fa fa-lg fa-envelope" aria-hidden="true"></i> SEND ABSENT SMS/EMAIL </a>
                </div>
                <div class="col">
                    <div class="h-5 py-2 px-2">
                        <select class="form-select" id="order_by_filter" onchange="get_attendance_data();">
                            <option value="Name">Sort by Name</option>
                            <option value="RollNo">Sort by Roll No</option>
                        </select>
                    </div>
                </div>
                <div class="col">
                    <div class="h-5 py-2 px-2">
                        <input type="text" id="student_search_filter" placeholder="Search Student" class="form-control" onkeyup="search_students();" />
                    </div>
                </div>
            </div>
        </div>

        <div class="my-2" id="attendance_student_data">

        </div>



    </div>
</div>

<!-- Include Footer -->
<?php include_once(APPPATH . "Views/footer.php"); ?>

<script>
    var scheduleId = "<?= $session_id; ?>";
    var attendance_date = "<?= $attendance_date; ?>";
    var institute_id = "<?= $institute_id; ?>";
    console.log(attendance_date);

    $(document).ready(function() {


        $(".select_all_students").click(function() {
            $(".stu_atten_checkbox").each(function() {
                this.checked = true;
            });
            calculate_stu_cnt();
        });

        $(".unselect_all_students").click(function() {
            $(".stu_atten_checkbox").each(function() {
                this.checked = false;
            });
            calculate_stu_cnt();
        });



    });


    $(document).ready(function() {
        get_attendance_data();
    });

    function search_students() {
        var search_val = $("#student_search_filter").val();
        if (search_val != '') {
            $(".attendance_ckboxs").removeClass("d-block");
            $(".attendance_ckboxs").addClass("d-none");
            $('.attendance_ckboxs:contains(' + search_val + ')').removeClass("d-none");
            $('.attendance_ckboxs:contains(' + search_val + ')').addClass("d-block");
        } else {
            $(".attendance_ckboxs").removeClass("d-none");
            $(".attendance_ckboxs").addClass("d-block");
        }
    }


    function calculate_stu_cnt() {
        console.log("call func");
        // Count of all checkboxes with class my_class
        total_count = $('input.stu_atten_checkbox').length;
        // Count of checkboxes which are checked
        total_checked_count = $('input.stu_atten_checkbox:checked').length;
        $('#present_students_cnt').html(total_checked_count);
        $('#absent_students_cnt').html((total_count - total_checked_count));
        console.log(total_checked_count);
    }
</script>

<script>
    var scheduleDataId = null;
    var encrypt_session_id = "<?= $encrypt_session_id; ?>";

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
                html = html + "<div class='card border-left-primary shadow' style='max-width: 800px;display:block;margin:4px auto;'>";
                html = html + "<label class='attendance_ckboxs ' ><div class='align-items-center text-muted pt-3 studnt_attend_card' style='position: relative;'>";


                if (obj.profilePic != null) {
                    var profile_pic_url = obj.profilePic;
                } else {
                    var profile_pic_url = base_url + "/assets/img/blank-profile-picture.png";
                }
                html = html + "<div class='row row-cols-auto'>";
                html = html + "<div class='col'>";
                html = html + '<div class="img-fluid rounded mx-auto d-block" style="background-repeat: no-repeat;background-size: cover;border-radius: 50%;background-position: center;height:50px;width:50px;background-image: url(' + profile_pic_url + ');"></div>';

                html = html + "</div>";
                html = html + "<div class='col'>";
                html = html + "<div class='ms-3 pb-3 mb-0 small lh-125 border-bottom border-gray'>";
                html = html + "<div class='d-flex justify-content-between align-items-center w-100'>";
                html = html + "<strong class='text-gray-dark'>" + obj.name + "</strong>";
                html = html + "<input type='hidden' name='stu_ids[]' class='stu_present_ids' value='" + obj.id + "'>";
                if (obj.present != null && obj.present == true) {
                    present_students = present_students + 1;
                    html = html + "<input type='checkbox' class='stu_atten_checkbox' checked='checked'   id='stu_present_check_" + obj.id + "' name='stu_present_ids[]' value='" + obj.id + "' onclick='calculate_stu_cnt()'>";
                } else {
                    absent_students = absent_students + 1;
                    html = html + "<input type='checkbox' class='stu_atten_checkbox' id='stu_present_check_" + obj.id + "' name='stu_present_ids[]' value='" + obj.id + "'  onclick='calculate_stu_cnt()' >";
                }

                html = html + "<span class='checkmark'></span>";

                html = html + "</div>";
                html = html + "<span class='d-block'> Roll Number: " + obj.username + " </span>";
                html = html + "</div>";
                html = html + "</div>";
                html = html + "</div>";
                html = html + "</div>";

                html = html + "</label>";
                if (obj.present != null && obj.present == false) {
                    html = html + "<div class='d-flex justify-content-center'>";
                    html = html + "<i style='padding: 10px;  cursor:pointer' onclick=\"sendAbsentNotification('" + obj.rollNo + "')\" class=\"fa fa-lg fa-envelope\" aria-hidden=\"true\"></i>"
                    html = html + "</div>";
                }
                html = html + "</div>";
            });
            html = html + "<div class='d-flex justify-content-center' style='position: fixed; bottom: 16px; left: 50%; transform: translateX(-50%); display: flex; justify-content: center;'><button class='btn btn-primary submit_btn' onclick='upload_attendance_submit();' type='button'>Submit</button></div>";

            total_students = data.length;
            $("#total_students_count").html(total_students);
            $("#present_students_cnt").html(present_students);
            $("#absent_students_cnt").html(absent_students);
        }
        return html;
    }
</script>

<script>
    function upload_attendance_submit() {

        var student_attendance_data_arr = [];
        var outArray = $('.stu_present_ids').toArray();
        outArray.forEach(function(i, key) {
            key = key + 1;
            obj = {};
            var student_id = $(i).val();
            if ($("#stu_present_check_" + student_id).is(":checked")) {
                var present = true;
            } else {
                var present = false;
            }
            obj['id'] = student_id;
            obj['present'] = present;
            student_attendance_data_arr.push(obj);
        })

        // console.log(student_attendance_data_arr);

        var request = {
            institute: {
                id: institute_id
            },
            schedule: {
                id: scheduleId,
                date: attendance_date
            },
            students: student_attendance_data_arr
        };

        callAdminServiceJSONPost("updateAttendance", request).then(function(response) {
                if (response.statusCode > 0) {
                    Snackbar.show({
                        pos: 'top-center',
                        text: 'Attendance added successfully'
                    });

                    window.location = base_url + "/attendance/overview/" + encrypt_session_id + '/' + attendance_date;
                    // window.location.reload();

                } else {
                    Snackbar.show({
                        pos: 'top-center',
                        text: 'Some error occured in fetching student data'
                    });
                }
            })
            .catch(function(error) {
                Snackbar.show({
                    pos: 'top-center',
                    text: 'Error in service call'
                });
            });
    }

    function sendAbsentNotification(username) {
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

        if (username != null) {
            //Notification is for individual student
            request.student = {
                rollNo: username
            };
        }

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