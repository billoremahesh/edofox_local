<!-- Include Header -->
<?php include_once(APPPATH . "Views/header.php"); ?>

<!-- Custom CSS -->
<link href="<?php echo base_url('assets/css/sales_team/add.css?v=20210915'); ?>" rel="stylesheet">

<div id="content">
    <div class="container-fluid mt-4">

        <div class="flex-container-column">
            <div>
                <label class="h5 text-gray-800 text-uppercase"> <?= $title; ?> </h5>
            </div>
            <div class="breadcrumb_div" aria-label="breadcrumb">
                <ol class="breadcrumb_custom">
                    <li class="breadcrumb_item"><a href="<?php echo base_url('/home'); ?>"> Home </a></li>
                    <li class="breadcrumb_item"><a href="<?php echo base_url('SuperAdmins'); ?>"> Team Members </a></li>
                    <li class="breadcrumb_item active" aria-current="page"> <?= $title; ?> </li>
                </ol>
            </div>
        </div>


        <div class="card shadow p-4">
            <form action="<?= base_url('SuperAdmins/update_super_admin_details_submit'); ?>" class="new_staff_form" name="new_staff_form" method="post" enctype="multipart/form-data">
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
                        <input type="text" class="form-control" name="mobile_number" id="mobile" value="<?= $staff_details['mobile_number']; ?>" minlength="10" maxlength="10" pattern="\d{10}" required>
                    </div>

                    <div class="col-md-3">
                        <label class="form_label" for="role"> Role </label>
                        <select class="form-select" name="role" id="role" required>
                            <option value=""></option>
                            <option value="Super Admin" <?php if($staff_details['role']=="Super Admin"){ echo "selected"; } ?> >Super Admin</option>
                            <option value="Sales Team" <?php if($staff_details['role']=="Sales Team"){ echo "selected"; } ?> >Sales Team</option>
                            <option value="Accounting" <?php if($staff_details['role']=="Accounting"){ echo "selected"; } ?> >Accounting</option>
                            <option value="Tech Support" <?php if($staff_details['role']=="Tech Support"){ echo "selected"; } ?> >Tech Support</option>
                        </select>
                    </div>

                </div>

                <div class="row display-flex mt-4">

                    <div class="col-md-12 mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="perms_all" type="checkbox" value="all_super_admin_perms" id="superAdminPermsCheck" <?php if (in_array("all_super_admin_perms", $roleperms)) : echo "checked";
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

                    </div>

                    <div class="row display-flex" id="checkbox_list_sscf">

                        <div class="col-md-3">
                            <label> Institutes </label>
                            <div class="checkbox"><label><input type="checkbox" name="perms[]" class="sscf_checkbox" value="manage_institutes" <?php if (in_array("manage_institutes", $roleperms)) : echo "checked";
                                                                                                                                                endif; ?>> Manage </label></div>
                        </div>

                        <div class="col-md-3">
                            <label> Sales Team </label>
                            <div class="checkbox"><label><input type="checkbox" name="perms[]" class="sscf_checkbox" value="manage_sales_team" <?php if (in_array("manage_sales_team", $roleperms)) : echo "checked";
                                                                                                                                                endif; ?>> Manage </label></div>
                        </div>

                        <div class="col-md-3">
                            <label> Billing </label>
                            <div class="checkbox"><label><input type="checkbox" name="perms[]" class="sscf_checkbox" value="manage_billing" <?php if (in_array("manage_billing", $roleperms)) : echo "checked";
                                                                                                                                            endif; ?>> Manage </label></div>
                        </div>


                        <div class="col-md-3">
                            <label> Subscriptions </label>
                            <div class="checkbox"><label><input type="checkbox" name="perms[]" class="sscf_checkbox" value="manage_subscriptions" <?php if (in_array("manage_subscriptions", $roleperms)) : echo "checked";
                                                                                                                                            endif; ?>> Manage </label></div>
                        </div>

                        <div class="col-md-3">
                            <label> Help Desk </label>
                            <div class="checkbox"><label><input type="checkbox" name="perms[]" class="sscf_checkbox" value="manage_help_Desk" <?php if (in_array("manage_help_Desk", $roleperms)) : echo "checked";
                                                                                                                                                endif; ?>> Manage </label></div>
                        </div>


                        <div class="col-md-3">
                            <label> Emails </label>
                            <div class="checkbox"><label><input type="checkbox" name="perms[]" class="sscf_checkbox" value="manage_emails" <?php if (in_array("manage_emails", $roleperms)) : echo "checked";
                                                                                                                                            endif; ?>> Manage </label></div>
                        </div>

                        <div class="col-md-3">
                            <label> Feedbacks </label>
                            <div class="checkbox"><label><input type="checkbox" name="perms[]" class="sscf_checkbox" value="manage_feedbacks" <?php if (in_array("manage_feedbacks", $roleperms)) : echo "checked";
                                                                                                                                                endif; ?>> Manage </label></div>
                        </div>


                        <div class="col-md-3">
                            <label> Notices </label>
                            <div class="checkbox"><label><input type="checkbox" name="perms[]" class="sscf_checkbox" value="manage_notices" <?php if (in_array("manage_notices", $roleperms)) : echo "checked";
                                                                                                                                            endif; ?>> Manage </label></div>
                        </div>


                        <div class="col-md-3">
                            <label> Routes </label>
                            <div class="checkbox"><label><input type="checkbox" name="perms[]" class="sscf_checkbox" value="manage_routes" <?php if (in_array("manage_routes", $roleperms)) : echo "checked";
                                                                                                                                            endif; ?>> Manage </label></div>
                        </div>

                    </div>
                </div>

                <div class="row display-flex mt-4">
                    <input type="hidden" name="staff_id" value="<?= $staff_id; ?>" required />
                    <input type="hidden" name="redirect" value="<?= $redirect; ?>" required />
                    <input type="hidden" name="perms[]" id="perms" value="Null">
                    <div class="col-md-4">
                        <button type="reset" class="btn btn-outline-warning">Reset</button>
                        <button type="submit" id="add_new_staff_btn" class="btn btn-primary" name="add_new_staff_btn">Update</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- Include Footer -->
<?php include_once(APPPATH . "Views/footer.php"); ?>


<script>
    $(document).ready(function() {
        check_staff_perms();
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