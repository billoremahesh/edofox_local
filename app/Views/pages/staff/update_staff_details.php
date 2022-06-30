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
            <?php
            $attributes = ['name' => 'update_staff_form', 'class' => 'update_staff_form', 'id' => 'update_staff_form'];
            ?>
            <?php echo form_open('staff/update_staff_submit', $attributes); ?>
            <div class="row g-3">

                <div class="col-md-3">
                    <label class="form_label" for="name"> Name </label>
                    <input type="text" class="form-control" name="name" id="name" value="<?= $staff_details['name']; ?>" required>
                </div>

                <div class="col-md-3">
                    <label class="form_label" for="email"> Email </label>
                    <input type="email" class="form-control" name="email" id="email" value="<?= $staff_details['email']; ?>" required>
                </div>

                <div class="col-md-3">
                    <label class="form_label" for="mobile"> Mobile </label>
                    <input type="text" class="form-control" name="mobile" id="mobile" value="<?= $staff_details['mobile']; ?>" minlength="10" maxlength="10" pattern="\d{10}" required>
                </div>

                <div class="col-md-3">
                    <label class="form_label" for="username"> Username </label>
                    <input type="text" class="form-control" name="username" id="username" value="<?= $staff_details['username']; ?>" required>
                </div>



            </div>

            <div class="row display-flex mt-4">

                <div class="col-md-12 mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="perms_all" type="checkbox" value="all_perms" id="superAdminPermsCheck" <?php if (in_array("all_perms", $roleperms)) : echo "checked";
                                                                                                                                                        endif; ?>>
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
                                if (!empty($classroom_list)) {
                                    foreach ($classroom_list as $row) {
                                        $package_id = $row['id'];
                                        $package_name = $row['package_name'];
                                ?>
                                        <option value="<?= $package_id; ?>" <?php if (!empty($staff_mapped_packages)) :  if (in_array($package_id, $staff_mapped_packages)) : echo "selected";
                                                                                endif;
                                                                            endif; ?>> <?= $package_name; ?> </option>
                                <?php
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row display-flex" id="checkbox_list_sscf">
                    <div class="col-md-3">
                        <label> Test </label>
                        <div class="checkbox"><label><input type="checkbox" name="perms[]" class="sscf_checkbox" value="view_tests" <?php if (in_array("view_tests", $roleperms)) : echo "checked";
                                                                                                                                    endif; ?>> View </label></div>
                        <div class="checkbox"><label><input type="checkbox" name="perms[]" class="sscf_checkbox" value="manage_tests" <?php if (in_array("manage_tests", $roleperms)) : echo "checked";
                                                                                                                                        endif; ?>> Manage </label></div>
                    </div>
                    <div class="col-md-3">
                        <label> Students </label>
                        <div class="checkbox"><label><input type="checkbox" name="perms[]" class="sscf_checkbox" value="view_students" <?php if (in_array("view_students", $roleperms)) : echo "checked";
                                                                                                                                        endif; ?>> View </label></div>
                        <div class="checkbox"><label><input type="checkbox" name="perms[]" class="sscf_checkbox" value="manage_students" <?php if (in_array("manage_students", $roleperms)) : echo "checked";
                                                                                                                                            endif; ?>> Manage </label></div>
                    </div>
                    <div class="col-md-3">
                        <label> Staff </label>
                        <div class="checkbox"><label><input type="checkbox" name="perms[]" class="sscf_checkbox" value="view_staff" <?php if (in_array("view_staff", $roleperms)) : echo "checked";
                                                                                                                                    endif; ?>> View </label></div>
                        <div class="checkbox"><label><input type="checkbox" name="perms[]" class="sscf_checkbox" value="manage_staff" <?php if (in_array("manage_staff", $roleperms)) : echo "checked";
                                                                                                                                        endif; ?>> Manage </label></div>
                    </div>
                    <div class="col-md-3">
                        <label> DLP </label>
                        <div class="checkbox"><label><input type="checkbox" name="perms[]" class="sscf_checkbox" value="view_dlp" <?php if (in_array("view_dlp", $roleperms)) : echo "checked";
                                                                                                                                    endif; ?>> View </label></div>
                        <div class="checkbox"><label><input type="checkbox" name="perms[]" class="sscf_checkbox" value="manage_dlp" <?php if (in_array("manage_dlp", $roleperms)) : echo "checked";
                                                                                                                                    endif; ?>> Manage </label></div>
                    </div>
                    <div class="col-md-3">
                        <label> Classrooms </label>
                        <div class="checkbox"><label><input type="checkbox" name="perms[]" class="sscf_checkbox" value="view_classrooms" <?php if (in_array("view_classrooms", $roleperms)) : echo "checked";
                                                                                                                                            endif; ?>> View </label></div>
                        <div class="checkbox"><label><input type="checkbox" name="perms[]" class="sscf_checkbox" value="manage_classrooms" <?php if (in_array("manage_classrooms", $roleperms)) : echo "checked";
                                                                                                                                            endif; ?>> Manage </label></div>
                    </div>
                    <div class="col-md-3">
                        <label> Doubts </label>
                        <div class="checkbox"><label><input type="checkbox" name="perms[]" class="sscf_checkbox" value="view_doubts" <?php if (in_array("view_doubts", $roleperms)) : echo "checked";
                                                                                                                                        endif; ?>> View </label></div>
                        <div class="checkbox"><label><input type="checkbox" name="perms[]" class="sscf_checkbox" value="manage_doubts" <?php if (in_array("manage_doubts", $roleperms)) : echo "checked";
                                                                                                                                        endif; ?>> Manage </label></div>
                    </div>

                    <div class="col-md-3">
                        <label> Reports </label>
                        <div class="checkbox"><label><input type="checkbox" name="perms[]" class="sscf_checkbox" value="view_reports" <?php if (in_array("view_reports", $roleperms)) : echo "checked";
                                                                                                                                        endif; ?>> View </label></div>
                        <div class="checkbox"><label><input type="checkbox" name="perms[]" class="sscf_checkbox" value="manage_reports" <?php if (in_array("manage_reports", $roleperms)) : echo "checked";
                                                                                                                                        endif; ?>> Manage </label></div>
                    </div>

                    <div class="col-md-3">
                        <label> Question Bank Management </label>
                        <div class="checkbox"><label><input type="checkbox" name="perms[]" class="sscf_checkbox" value="view_question_bank" <?php if (in_array("view_question_bank", $roleperms)) : echo "checked";
                                                                                                                                            endif; ?>> View </label></div>
                        <div class="checkbox"><label><input type="checkbox" name="perms[]" class="sscf_checkbox" value="manage_question_bank" <?php if (in_array("manage_question_bank", $roleperms)) : echo "checked";
                                                                                                                                                endif; ?>> Manage </label></div>
                        <div class="checkbox"><label><input type="checkbox" name="perms[]" class="sscf_checkbox" value="question_bank_supervisor" <?php if (in_array("question_bank_supervisor", $roleperms)) : echo "checked";
                                                                                                                                                    endif; ?>> Supervisor </label></div>
                    </div>

                    <div class="col-md-3">
                        <label> Schedule Management </label>
                        <div class="checkbox"><label><input type="checkbox" name="perms[]" class="sscf_checkbox" value="view_schedule" <?php if (in_array("view_schedule", $roleperms)) : echo "checked";
                                                                                                                                        endif; ?>> View </label></div>
                        <div class="checkbox"><label><input type="checkbox" name="perms[]" class="sscf_checkbox" value="manage_schedule" <?php if (in_array("manage_schedule", $roleperms)) : echo "checked";
                                                                                                                                            endif; ?>> Manage </label></div>
                    </div>


                    <div class="col-md-3">
                        <label> Attendance Management </label>
                        <div class="checkbox"><label><input type="checkbox" name="perms[]" class="sscf_checkbox" value="view_attendance" <?php if (in_array("view_attendance", $roleperms)) : echo "checked";
                                                                                                                                            endif; ?>> View </label></div>
                        <div class="checkbox"><label><input type="checkbox" name="perms[]" class="sscf_checkbox" value="manage_attendance" <?php if (in_array("manage_attendance", $roleperms)) : echo "checked";
                                                                                                                                            endif; ?>> Manage </label></div>
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
                <input type="hidden" name="staff_id" value="<?= $staff_id; ?>" required />
                <input type="hidden" name="redirect" value="<?= $redirect; ?>" required />
                <input type="hidden" name="perms[]" id="perms" value="Null">
                <div class="col-md-4">
                    <button type="submit" id="update_btn" class="btn btn-primary" name="update_btn"> Update </button>
                </div>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<!-- Include Footer -->
<?php include_once(APPPATH . "Views/footer.php"); ?>

<script src="<?php echo base_url('assets/js/jquery.validate.js'); ?>"></script>

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

<script>
    // Onload check super admin perms
    function check_staff_perms() {
        var super_admin_perms_check = $("#superAdminPermsCheck");
        if (super_admin_perms_check.is(':checked')) {
            $(".sscf_checkbox").each(function() {
                this.checked = false;
            })
            $("#staff_perms_div").addClass("d-none");
        } else {
            $("#staff_perms_div").removeClass("d-none");
        }
    }
</script>

<script>
    $().ready(function() {
        check_staff_perms();
        // validate form
        $("#update_staff_form").validate({
            rules: {
                name: {
                    required: true,
                    letterspaceonly: true
                },
                password: "required",
                mobile: {
                    required: true,
                    digits: true
                },
                email: {
                    required: true,
                    email: true
                },
                username: "required"
            },
            messages: {
                name: "Please enter staff name, only accepts alphabetical characters & space allowed",
                password: "Please enter the password",
                mobile: "Please enter 10 digits mobile number",
                email: "Please select email",
                username: "Please select username"
            },
            submitHandler: function(form) {
                $('#update_btn').prop('disabled', true);
                form.submit();
            }
        });

        jQuery.validator.addMethod("letterspaceonly", function(value, element) {
            return this.optional(element) || /^[a-zA-Z-,]+(\s{0,1}[a-zA-Z-, ])*$/.test(value);
        }, "Letters only please");

    });
</script>