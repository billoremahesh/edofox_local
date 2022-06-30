<!-- Include Header -->
<?php include_once(APPPATH . "Views/header.php"); ?>

<!-- Custom CSS -->
<link href="<?php echo base_url('assets/css/tests/overview.css?v=20210915'); ?>" rel="stylesheet">
<div id="content">
    <div class="container-fluid mt-4">

        <div class="flex-container-column">
            <div>
                <label class="h5 text-gray-800 text-uppercase"> <?= $title; ?> </h5>
            </div>
            <div class="breadcrumb_div" aria-label="breadcrumb">
                <ol class="breadcrumb_custom">
                    <li class="breadcrumb_item"><a href="<?php echo base_url('/home'); ?>"> Home </a></li>
                    <li class="breadcrumb_item active" aria-current="page"> <?= $title; ?> </li>
                </ol>
            </div>
        </div>


        <div class="row">
            <div class="col-md-3 col-xl-2">

                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Profile Settings</h5>
                    </div>

                    <div class="list-group list-group-flush" role="tablist">
                        <a class="list-group-item list-group-item-action active" data-bs-toggle="list" href="#account" role="tab">
                            Account
                        </a>
                        <a class="list-group-item list-group-item-action" data-bs-toggle="list" href="#password" role="tab">
                            Update Password
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-9 col-xl-10">

                <div class="tab-content">
                    <div class="tab-pane fade show active" id="account" role="tabpanel">

                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Public info</h5>
                            </div>
                            <div class="card-body">
                                <?php
                                $attributes = ['class' => 'cmxform', 'id' => 'update_profile_form'];
                                ?>
                                <?php echo form_open('profile/update_profile_info', $attributes); ?>
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="mb-3">
                                            <label class="form_label" for="name"> Name </label>
                                            <input type="text" class="form-control" name="name" id="name" value="<?= $profile_details['name']; ?>" required>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form_label" for="email"> Email </label>
                                            <input type="email" class="form-control" name="email" id="email" value="<?= $profile_details['email']; ?>" required>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form_label" for="mobile"> Mobile </label>
                                            <input type="text" class="form-control" name="mobile" id="mobile" value="<?= $profile_details['mobile']; ?>" required>
                                        </div>

                                        <div class="mb-3">
                                            <label for="username">Username</label>
                                            <input type="text" class="form-control" id="username" name="username" value="<?php echo $profile_details['username']; ?>">
                                        </div>
                                        <input type="hidden" name="profile_id" value="<?= $profile_id; ?>" required />
                                        <input type="hidden" name="redirect" value="<?= $redirect; ?>" required />
                                        <button type="submit" id="update_profile_btn" class="btn btn-primary">Save changes</button>
                                        <?php echo form_close(); ?>

                                    </div>
                                    <div class="col-md-4">
                                        <div class="text-center">

                                            <div class="d-flex justify-content-center">
                                                <?php
                                                $profile_image['profile_picture_url'] = session()->get('profile_img_url');
                                                $profile_image['size'] = "50px";
                                                echo view('pages/profile/profile_image_circle.php', $profile_image);
                                                ?>
                                            </div>

                                            <div class="fw-bold"><?php echo $_SESSION['username']; ?></div>
                                            <div class="mt-2">
                                                <a class="btn btn-primary" onclick="show_edit_modal('modal_div','update_profile_photo_modal','profile/update_profile_photo/<?php echo $profile_id; ?>');"><i class="fas fa-upload"></i> Update Profile Photo</a>
                                            </div>

                                        </div>
                                    </div>
                                </div>


                            </div>
                        </div>


                    </div>
                    <div class="tab-pane fade" id="password" role="tabpanel">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Update Password</h5>
                            </div>
                            <div class="card-body">
                                <form action="" class="update_password_form" onsubmit="return update_password();" method="post" enctype="multipart/form-data">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="mb-2">
                                                <label class="form-label" for="password">Password *</label>
                                                <input type="password" class="form-control" id="new_password" name="new_password" minlength="8" required>
                                            </div>
                                        </div>
                                        <div class="col-md-12 mt-2">
                                            <div class="mb-2">
                                                <label class="form-label" for="retype_password">Re-type Password *</label>
                                                <input type="password" class="form-control" id="retype_password" name="retype_password" minlength="8" required>
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

                                        <div class="col-md-12 mt-4">
                                            <input type="hidden" id="token" name="token" value="<?php echo $admin_token; ?>" />
                                            <button type="reset" class="btn btn-outline-warning">Reset</button>
                                            <button type="submit" id="update_password_btn" class="btn btn-primary" name="update_password_btn">Update</button>
                                        </div>
                                    </div>
                                </form>

                            </div>
                        </div>
                    </div>


                </div>

            </div>
        </div>

    </div>
</div>

<!-- Include Footer -->
<?php include_once(APPPATH . "Views/footer.php"); ?>
<script src="<?php echo base_url('assets/js/jquery.validate.js'); ?>"></script>
<script src="<?php echo base_url('assets/js/institutes.js?v=20210829'); ?>"></script>


<script>
    // To call asynchronously Update Password
    function update_password() {
        // Disable Submit Button
        $("#update_password_btn").attr("disabled", true);
        $('.error_msg_div').css('display', 'none');
        $('.loading_div').css('display', 'block');
        var token = $("#token").val();
        var password = $("#new_password").val();
        var retype_password = $("#retype_password").val();
        if (password != retype_password) {
            alert("password and retype password should be same");
            $("#update_password_btn").removeAttr('disabled');
            $('.loading_div').css('display', 'none');
            return false;
        }

        //Authenticate for token
        get_admin_token().then(function(result) {
            var resp = JSON.parse(result);
            if (
                resp != null &&
                resp.status == 200 &&
                resp.data != null &&
                resp.data.admin_token != null
            ) {
                // Update Password using promise
                set_new_password(token, password)
                    .then(function(result) {
                        var response = JSON.parse(result);
                        // console.log("response", result);
                        if (response.status.statusCode == 200) {
                            // Snakbar Message
                            Snackbar.show({
                                pos: 'top-center',
                                text: 'Password changed successfully'
                            });
                            // Reevaluating result using a async promise
                            window.location = base_url + "/profile";
                        } else {
                            console.log(response.status.responseText);
                            $('.error_msg_div').css('display', 'block');
                            $("#update_password_btn").removeAttr('disabled');
                            $('.loading_div').css('display', 'none');
                            return false;
                        }
                    })
                    .catch(function(error) {
                        // An error occurred
                        alert("Exception: " + error);
                    });

            } else {
                $scope.responseText =
                    "Error authenticating the request .. Logout and try again";
            }
        });
        return false;
    }
</script>


<script>
    $().ready(function() {
        // validate form
        $("#update_profile_form").validate({
            rules: {
                name: {
                    required: true,
                    letterspaceonly: true
                },
                email: {
                    required: true,
                    email: true
                },
                mobile_no: {
                    required: true,
                    digits: true
                }
            },
            messages: {
                name: "Please enter name, only accepts alphabetical characters and spaces",
                email: "Please enter the mail id",
                mobile_no: "Please enter 10 digits mobile number"
            },
            submitHandler: function(form) {
                $('#update_profile_btn').prop('disabled', true);
                form.submit();
            }
        });

        jQuery.validator.addMethod("letterspaceonly", function(value, element) {
            return this.optional(element) || /^[a-zA-Z-,]+(\s{0,1}[a-zA-Z-, ])*$/.test(value);
        }, "Letters only please");

    });
</script>