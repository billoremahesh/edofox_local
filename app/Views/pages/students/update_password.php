<!-- Include Header -->
<?php include_once(APPPATH . "Views/header.php"); ?>

<!-- Custom CSS -->
<link href="<?php echo base_url('assets/css/tests/realtime_overview.css?v=20210915'); ?>" rel="stylesheet">

<div id="content">
    <div class="container-fluid mt-4">

        <div class="flex-container-column">
            <div>
                <label class="h5 text-gray-800 text-uppercase"> <?= $title; ?> </h5>
            </div>
            <div class="breadcrumb_div" aria-label="breadcrumb">
                <ol class="breadcrumb_custom">
                    <li class="breadcrumb_item"><a href="<?php echo base_url('/home'); ?>"> Home </a></li>
                    <li class="breadcrumb_item"><a href="<?php echo base_url('students'); ?>"> Students </a></li>
                    <li class="breadcrumb_item active" aria-current="page"> <?= $title; ?> </li>
                </ol>
            </div>
        </div>

        <div class="card p-4" style="max-width: 500px;margin:auto;">
            <form action="" class="update_password_form" onsubmit="return update_password();" method="post" enctype="multipart/form-data">
                <div class="row">

                    <div class="col-md-12 mb-2">
                        Student Name : <b><?= $student_details['name']; ?></b><br />
                        Username : <b><?= $student_details['username']; ?></b>
                    </div>

                    <div class="col-md-12 mb-2">
                        <label class="form-label" for="stu_new_password">Password *</label>
                        <input type="password" class="form-control" id="stu_new_password" name="stu_new_password" minlength="6" required>
                    </div>
                    <div class="col-md-12 mb-2">
                        <label class="form-label" for="stu_retype_password">Re-type Password *</label>
                        <input type="password" class="form-control" id="stu_retype_password" name="stu_retype_password" minlength="6" required>
                    </div>

                    <!-- Error Message -->
                    <div class="error_msg_div" style="display: none;">
                        <p class="error_msg_div_text">Error in processing. Try again.</p>
                    </div>

                    <!-- Loading Message -->
                    <div class="loading_div" style="display: none;">
                        <span class="spinner-border spinner-border-sm"></span> Updating. Please Wait...
                    </div>

                    <div class="col-md-12 mt-2">
                        <input type="hidden" id="student_token" value="<?= $student_details['universal_token']; ?>" required />
                        <input type="hidden" name="redirect" value="<?= $redirect; ?>" required />
                        <button type="reset" class="btn btn-outline-warning">Reset</button>
                        <button type="submit" id="update_password_btn" class="btn btn-primary" name="update_password_btn">Update</button>
                    </div>
                </div>
            </form>
        </div>

    </div>
</div>

<!-- Include Footer -->
<?php include_once(APPPATH . "Views/footer.php"); ?>

<script src="<?php echo base_url('assets/js/login.js'); ?>"></script>

<script>
    // To call asynchronously Update Password
    function update_password() {
        // Disable Submit Button
        $("#update_password_btn").attr("disabled", true);
        $('.error_msg_div').css('display', 'none');
        $('.loading_div').css('display', 'block');
        var student_token = $("#student_token").val();
        var password = $("#stu_new_password").val();
        var retype_password = $("#stu_retype_password").val();
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
                set_new_password(student_token, password, resp.data.admin_token)
                    .then(function(result) {
                        var response = JSON.parse(result);
                        // console.log("response", result);
                        if (response.status.statusCode == 200) {
                            // Snakbar Message
                            Snackbar.show({
                                pos: 'top-center',
                                text: 'Updated password successfully'
                            });
                            // Reevaluating result using a async promise
                            window.location = base_url + "/students";
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