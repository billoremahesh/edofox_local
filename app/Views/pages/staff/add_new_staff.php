<!-- Include Header -->
<?php include_once(APPPATH . "Views/header.php"); ?>

<!-- Custom CSS -->
<link href="<?php echo base_url('assets/css/staff/new_staff.css?v=20210915'); ?>" rel="stylesheet">

<div id="content">
    <div class="container-fluid mt-4">

        <div class="flex-container-column">
            <div>
                <label class="h5 text-gray-800 text-uppercase"> <?= $title; ?> </h5>
            </div>
            <div class="breadcrumb_div" aria-label="breadcrumb">
                <ol class="breadcrumb_custom">
                    <li class="breadcrumb_item"><a href="<?php echo base_url('/home'); ?>"> Home </a></li>
                    <li class="breadcrumb_item"><a href="<?php echo base_url('staff'); ?>"> Staff </a></li>
                    <li class="breadcrumb_item active" aria-current="page"> <?= $title; ?> </li>
                </ol>
            </div>
        </div>


        <div class="card shadow p-4">
            <form action="" class="new_staff_form" onsubmit="return add_new_staff_submit();" name="new_staff_form" method="post" enctype="multipart/form-data">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form_label" for="name"> Name </label>
                        <input type="text" class="form-control" name="name" id="name" required>
                    </div>

                    <div class="col-md-3">
                        <label class="form_label" for="email"> Email </label>
                        <input type="email" class="form-control" name="email" id="email" required>
                    </div>

                    <div class="col-md-3">
                        <label class="form_label" for="mobile"> Mobile </label>
                        <input type="text" class="form-control" name="mobile" id="mobile" minlength="10" maxlength="10" pattern="\d{10}" required>
                    </div>

                    <div class="col-md-3">
                        <label class="form_label" for="username"> Username </label>
                        <input type="text" class="form-control" name="username" id="username" autocomplete="off" minlength="5" required>
                    </div>

                    <div class="col-md-3">
                        <label class="form_label" for="password"> Password </label>
                        <input type="password" class="form-control" name="password" id="password" autocomplete="off" minlength="8" required>
                    </div>

                    <div class="col-md-3">
                        <label for="retype_password">Re-type Password *</label>
                        <input type="password" class="form-control" id="retype_password" name="retype_password" minlength="8" autocomplete="off" required>
                    </div>


                </div>

                <div class="row display-flex mt-4">

                    <div class="col-md-12 mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="perms_all" type="checkbox" value="all_perms" id="superAdminPermsCheck">
                            <label class="form-check-label" for="superAdminPermsCheck">
                                Super Admin Permissions (Perpetual permission to all modules and classrooms)
                            </label>
                        </div>
                    </div>

                </div>

                <div id="staff_perms_div">

                    <div class="row display-flex">
                        <div class="col-md-3 mb-2">
                            <div class="form-check btn btn-outline-primary">
                                <label class="select_sscf" class="form-check-label">
                                    Check All
                                </label>
                            </div>

                            <div class="form-check btn btn-outline-primary">
                                <label class="unselect_sscf" class="form-check-label">
                                    Uncheck All
                                </label>
                            </div>

                        </div>

                        <div class="col-md-12">
                            <div class="mb-2">
                                <label class="form-label" for="classrooms">Classrooms</label> (You can select multiple classrooms)
                                <select class="form-control" id="classrooms" name="classrooms[]" multiple="multiple">
                                    <?php
                                    foreach ($classroom_list as $row) {
                                        $package_id = $row['id'];
                                        $package_name = $row['package_name'];
                                        echo "<option value='$package_id'>$package_name</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row display-flex" id="checkbox_list_sscf">
                        <div class="col-md-3">
                            <label> Test </label>
                            <div class="checkbox"><label><input type="checkbox" name="perms[]" class="sscf_checkbox" value="view_tests"> View </label></div>
                            <div class="checkbox"><label><input type="checkbox" name="perms[]" class="sscf_checkbox" value="manage_tests"> Manage </label></div>
                        </div>
                        <div class="col-md-3">
                            <label> Students </label>
                            <div class="checkbox"><label><input type="checkbox" name="perms[]" class="sscf_checkbox" value="view_students"> View </label></div>
                            <div class="checkbox"><label><input type="checkbox" name="perms[]" class="sscf_checkbox" value="manage_students"> Manage </label></div>
                        </div>
                        <div class="col-md-3">
                            <label> Staff </label>
                            <div class="checkbox"><label><input type="checkbox" name="perms[]" class="sscf_checkbox" value="view_staff"> View </label></div>
                            <div class="checkbox"><label><input type="checkbox" name="perms[]" class="sscf_checkbox" value="manage_staff"> Manage </label></div>
                        </div>
                        <div class="col-md-3">
                            <label> DLP </label>
                            <div class="checkbox"><label><input type="checkbox" name="perms[]" class="sscf_checkbox" value="view_dlp"> View </label></div>
                            <div class="checkbox"><label><input type="checkbox" name="perms[]" class="sscf_checkbox" value="manage_dlp"> Manage </label></div>
                        </div>
                        <div class="col-md-3">
                            <label> Classrooms </label>
                            <div class="checkbox"><label><input type="checkbox" name="perms[]" class="sscf_checkbox" value="view_classrooms"> View </label></div>
                            <div class="checkbox"><label><input type="checkbox" name="perms[]" class="sscf_checkbox" value="manage_classrooms"> Manage </label></div>
                        </div>
                        <div class="col-md-3">
                            <label> Doubts </label>
                            <div class="checkbox"><label><input type="checkbox" name="perms[]" class="sscf_checkbox" value="view_doubts"> View </label></div>
                            <div class="checkbox"><label><input type="checkbox" name="perms[]" class="sscf_checkbox" value="manage_doubts"> Manage </label></div>
                        </div>

                        <div class="col-md-3">
                            <label> Reports </label>
                            <div class="checkbox"><label><input type="checkbox" name="perms[]" class="sscf_checkbox" value="view_reports"> View </label></div>
                            <div class="checkbox"><label><input type="checkbox" name="perms[]" class="sscf_checkbox" value="manage_reports"> Manage </label></div>
                        </div>
                        <div class="col-md-3">
                            <label> Question Bank Management </label>
                            <div class="checkbox"><label><input type="checkbox" name="perms[]" class="sscf_checkbox" value="view_question_bank"> View </label></div>
                            <div class="checkbox"><label><input type="checkbox" name="perms[]" class="sscf_checkbox" value="manage_question_bank"> Manage </label></div>
                            <div class="checkbox"><label><input type="checkbox" name="perms[]" class="sscf_checkbox" value="question_bank_supervisor"> Supervisor </label></div>
                        </div>

                        <div class="col-md-3">
                            <label> Schedule Management </label>
                            <div class="checkbox"><label><input type="checkbox" name="perms[]" class="sscf_checkbox" value="view_schedule"> View </label></div>
                            <div class="checkbox"><label><input type="checkbox" name="perms[]" class="sscf_checkbox" value="manage_schedule"> Manage </label></div>
                        </div>

                        <div class="col-md-3">
                            <label> Attendance Management </label>
                            <div class="checkbox"><label><input type="checkbox" name="perms[]" class="sscf_checkbox" value="view_attendance"> View </label></div>
                            <div class="checkbox"><label><input type="checkbox" name="perms[]" class="sscf_checkbox" value="manage_attendance"> Manage </label></div>
                        </div>

                    </div>
                </div>

                <!-- Error Message -->
                <div class="error_msg_div" style="display: none;">
                    <p class="error_msg_div_text">Error in processing. Try again.</p>
                </div>

                <!-- Loading Message -->
                <div class="loading_div" style="display: none;">
                    <span class="spinner-border spinner-border-sm"></span> Updating. Please Wait...
                </div>

                <div class="row display-flex mt-4">
                    <input type="hidden" name="instituteId" id="instituteId" value="<?= decrypt_cipher($instituteID); ?>" required />
                    <input type="hidden" name="redirect" value="<?= $redirect; ?>" required />
                    <input type="hidden" name="perms[]" id="perms" value="Null">
                    <div class="col-md-4">
                        <button type="reset" class="btn btn-outline-warning">Reset</button>
                        <button type="submit" id="add_new_staff_btn" class="btn btn-primary" name="add_new_staff_btn">Add New Staff</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- Include Footer -->
<?php include_once(APPPATH . "Views/footer.php"); ?>

<script src="<?php echo base_url('assets/js/staff.js'); ?>"></script>

<script>
    const perms_arr = [];
    // To call asynchronously Add New Staff
    function add_new_staff_submit() {
        // Disable Submit Button
        $("#add_new_staff_btn").attr("disabled", true);
        $('.error_msg_div').css('display', 'none');
        $('.loading_div').css('display', 'block');
        var adminName = $("#name").val();
        var instituteId = $("#instituteId").val();
        var email = $("#email").val();
        var mobile = $("#mobile").val();
        var perms = $("#perms").val();

        if ($("#superAdminPermsCheck").is(":checked")) {
            perms = "all_perms";
            perms_arr.push(perms);
        } else {
            $.each($('.sscf_checkbox'), function(index, field) {
                if (this.checked) {
                    perms = field.value;
                    perms_arr.push(perms);
                }
            });
        }
        perms = JSON.stringify(perms_arr);
        console.log("perms: " + perms);
        var classrooms = $("#classrooms").val();
        var username = $("#username").val();
        var password = $("#password").val();
        var retype_password = $("#retype_password").val();
        if (password != retype_password) {
            alert("Password and retype password should be same");
            $("#add_new_staff_btn").removeAttr('disabled');
            $('.loading_div').css('display', 'none');
            return false;
        }
        // Add new staff using promise
        add_new_staff(adminName, instituteId, email, mobile, perms, classrooms, username, password)
            .then(function(result) {
                var response = JSON.parse(result);
                console.log("response", result);
                if (response.statusCode == 200) {

                    // Snakbar Message
                    Snackbar.show({
                        pos: 'top-center',
                        text: 'New staff added successfully'
                    });

                    window.location = base_url + "/staff";
                } else {
                    console.log(response.responseText);
                    $('.error_msg_div').css('display', 'block');
                    $('.error_msg_div').html(response.responseText);
                    $("#add_new_staff_btn").removeAttr('disabled');
                    $('.loading_div').css('display', 'none');
                    return false;
                }
            })
            .catch(function(error) {
                // An error occurred
                alert("Exception: " + error);
                $('.error_msg_div').css('display', 'block');
                $("#add_new_staff_btn").removeAttr('disabled');
                $('.loading_div').css('display', 'none');
            });
        return false;
    }
</script>

<script>
    $(document).ready(function() {
        //Initializing select2
        $('#classrooms').select2({
            width: "100%"
        });
    });
</script>

<script>
    $(document).ready(function() {

        $(".select_sscf").click(function() {
            $(".sscf_checkbox").each(function() {
                this.checked = true;
            })
        });

        $(".unselect_sscf").click(function() {
            $(".sscf_checkbox").each(function() {
                this.checked = false;
            })
        });


        $("#superAdminPermsCheck").click(function() {
            if (this.checked) {
                $(".sscf_checkbox").each(function() {
                    this.checked = false;
                })

                $("#staff_perms_div").addClass("d-none");
            } else {
                $("#staff_perms_div").removeClass("d-none");
            }
        });

    });
</script>