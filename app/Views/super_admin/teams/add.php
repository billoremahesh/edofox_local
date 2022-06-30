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
            <form action="<?= base_url('SuperAdmins/add_super_admin_submit'); ?>" class="new_staff_form" name="new_staff_form" method="post" enctype="multipart/form-data">
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
                        <input type="text" class="form-control" name="mobile_number" id="mobile" minlength="10" maxlength="10" pattern="\d{10}" required>
                    </div>

                    <div class="col-md-3">
                        <label class="form_label" for="role"> Role </label>
                        <select class="form-select" name="role" id="role" required>
                            <option value=""></option>
                            <option value="Super Admin">Super Admin</option>
                            <option value="Sales Team">Sales Team</option>
                            <option value="Accounting">Accounting</option>
                            <option value="Tech Support">Tech Support</option>
                        </select>
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
                            <input class="form-check-input" type="checkbox" name="perms_all" type="checkbox" value="all_super_admin_perms" id="superAdminPermsCheck">
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
                            <div class="checkbox"><label><input type="checkbox" name="perms[]" class="sscf_checkbox" value="manage_institutes"> Manage </label></div>
                        </div>

                        <div class="col-md-3">
                            <label> Sales Team </label>
                            <div class="checkbox"><label><input type="checkbox" name="perms[]" class="sscf_checkbox" value="manage_sales_team"> Manage </label></div>
                        </div>

                        <div class="col-md-3">
                            <label> Billing </label>
                            <div class="checkbox"><label><input type="checkbox" name="perms[]" class="sscf_checkbox" value="manage_billing"> Manage </label></div>
                        </div>


                        <div class="col-md-3">
                            <label> Subscriptions </label>
                            <div class="checkbox"><label><input type="checkbox" name="perms[]" class="sscf_checkbox" value="manage_subscriptions"> Manage </label></div>
                        </div>

                        <div class="col-md-3">
                            <label> Help Desk </label>
                            <div class="checkbox"><label><input type="checkbox" name="perms[]" class="sscf_checkbox" value="manage_help_Desk"> Manage </label></div>
                        </div>


                        <div class="col-md-3">
                            <label> Emails </label>
                            <div class="checkbox"><label><input type="checkbox" name="perms[]" class="sscf_checkbox" value="manage_emails"> Manage </label></div>
                        </div>

                        <div class="col-md-3">
                            <label> Feedbacks </label>
                            <div class="checkbox"><label><input type="checkbox" name="perms[]" class="sscf_checkbox" value="manage_feedbacks"> Manage </label></div>
                        </div>


                        <div class="col-md-3">
                            <label> Notices </label>
                            <div class="checkbox"><label><input type="checkbox" name="perms[]" class="sscf_checkbox" value="manage_notices"> Manage </label></div>
                        </div>


                        <div class="col-md-3">
                            <label> Routes </label>
                            <div class="checkbox"><label><input type="checkbox" name="perms[]" class="sscf_checkbox" value="manage_routes"> Manage </label></div>
                        </div>

                    </div>
                </div>

                <div class="row display-flex mt-4">
                    <input type="hidden" name="redirect" value="<?= $redirect; ?>" required />
                    <input type="hidden" name="perms[]" id="perms" value="Null">
                    <div class="col-md-4">
                        <button type="reset" class="btn btn-outline-warning">Reset</button>
                        <button type="submit" id="add_new_staff_btn" class="btn btn-primary" name="add_new_staff_btn">Add</button>
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