<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-60995650-2"></script>
    <script src="<?php echo base_url('assets/gtag.js'); ?>"></script>

    <meta name="robots" content="noindex,nofollow" />

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="<?php echo base_url('assets/img/favicon.png'); ?>" />
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->

    <title><?= $title; ?></title>


    <!-- Font-Awesome 5.15.4-web -->
    <link rel="stylesheet" href="<?php echo base_url('assets/fontawesome-5.15.4-web/css/all.min.css'); ?>">

    <!-- Bootstrap-5.0.2 -->
    <link rel="stylesheet" href="<?php echo base_url('assets/bootstrap-5.0.2-dist/css/bootstrap.min.css'); ?>">

    <link rel="stylesheet" href="<?php echo base_url('assets/plugins/jquery-ui-1.12.1.custom/jquery-ui.css'); ?>">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Karla:400,700&display=swap" rel="stylesheet">

    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <!-- Snackbar -->
    <link href="<?php echo base_url('assets/plugins/snackbar-material/snackbar.min.css'); ?>" rel="stylesheet" />

    <!-- Toastr CSS -->
    <link href="<?php echo base_url('assets/plugins/toastr/toastr.css'); ?>" rel="stylesheet" />

    <!-- Custom CSS -->
    <link href="<?php echo base_url('assets/css/login.css?v=20211031'); ?>" rel="stylesheet">

</head>

<body>
    <?php $session = \Config\Services::session(); ?>
    <div class="container pt-4" id="main_div">

        <div class="text-center text-uppercase">
            <img src="<?php echo base_url('assets/img/edofox-name-logo-black.png'); ?>" style="height:60px; margin-top: 0px" />
            <p><b>Admin</b></p>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="d-none d-md-block">
                    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js" async></script>
                    <lottie-player src="https://assets4.lottiefiles.com/packages/lf20_ZmsQVB.json" background="transparent" speed="1" style="width: 500px; height: 350px; margin:auto;" loop autoplay></lottie-player>
                </div>
            </div>
            <div class="col-md-6">
                <div class='card_box'>
                    <h5 id="title">Admin Login</h5>
                    <hr>
                    <form action="" onsubmit="return validate_login();" method="post">


                        <div class="input-group mb-3">
                            <input type="text" name="username" id="username" class="form-control" placeholder="Username" value="<?= old('username') ?>" aria-label="Username" aria-describedby="username-addon" minlength="4" maxlength="45" autocomplete="on" required>
                            <span class="input-group-text" id="username-addon">
                                <i class="fas fa-user"></i>
                            </span>
                        </div>


                        <div class="input-group mb-3">
                            <input type="password" name="password" id="password" class="form-control" placeholder="Password" aria-label="Password" aria-describedby="password-addon" minlength="4" maxlength="40" autocomplete="on" required>
                            <span class="input-group-text" id="password-addon">
                                <i class="fas fa-eye" id="togglePassword" onclick="togglePassword()"></i>
                            </span>
                        </div>


                        <!-- Error Message -->
                        <div class="error_msg_div" style="display: none;">
                            <p class="error_msg_div_text">Your username or password does not match our records. Try again.</p>
                        </div>


                        <!-- Loading Message -->
                        <div class="loading_div" style="display: none;">
                            <span class="spinner-border spinner-border-sm"></span> Authenticating. Please Wait...
                        </div>

                        <button type="submit" id="login_validate_btn" name="login_validate" class="login_button">Login</button>
                        <button type="reset" class="reset_button">Reset </button>
                    </form>

                </div>
            </div>

        </div>

    </div>


    <script src="<?php echo base_url('assets/jquery-3.6.0.min.js'); ?>"></script>
    <script src="<?php echo base_url('assets/bootstrap-5.0.2-dist/js/bootstrap.bundle.min.js'); ?>"></script>
    <script src="<?php echo base_url('assets/plugins/jquery-ui-1.12.1.custom/jquery-ui.js'); ?>"></script>
    <!-- Toastr JS -->
    <script src="<?php echo base_url('assets/plugins/toastr/toastr.min.js'); ?>"></script>

    <script src="<?php echo base_url('assets/js/url.js'); ?>"></script>
    <script src="<?php echo base_url('assets/js/angular.js?v=20210829'); ?>"></script>
    <script src="<?php echo base_url('assets/js/main.js?v=20210829'); ?>"></script>
    <script src="<?php echo base_url('assets/js/admin_login.js?v=20210829'); ?>"></script>

    <!-- Snackbar JS -->
    <!-- Ref: https://www.polonel.com/snackbar/ -->
    <script src="<?php echo base_url('assets/plugins/snackbar-material/snackbar.min.js'); ?>"></script>

    <script>
        // To call asynchronously Validate Login
        function validate_login() {
            // Disable Submit Button
            $("#login_validate_btn").attr("disabled", true);
            $('.error_msg_div').css('display', 'none');
            $('.loading_div').css('display', 'block');
            var username = $("#username").val();
            var password = $("#password").val();

            // Validating login credentails using promise
            check_valid_login_user(username, password)
                .then(function(result) {
                    var response = JSON.parse(result);
                    // console.log("response", result);
                    if (response.status.statusCode == 200) {
                        // Reevaluating result using a async promise
                        var token = response.institute.token;
                        // console.log(token); 
                        // Snakbar Message
                        Snackbar.show({
                            pos: 'top-center',
                            text: 'Login successful ... Please wait, redirecting to dashboard.'
                        });
                        window.location = base_url + "/login/admin_validate_login/" + token;
                        // Usage!
                        sleepInSeconds(4000).then(() => {
                            // Do something after the sleep!
                            $('.error_msg_div').css('display', 'block');
                            $(".error_msg_div_text").text("taking time for redirection, please wait");
                            $('.loading_div').css('display', 'none');
                        });
                    } else {
                        if (response.status.responseText != null && response.status.responseText != '') {
                            $(".error_msg_div_text").text(response.status.responseText);
                        }
                        $('.error_msg_div').css('display', 'block');
                        $("#login_validate_btn").removeAttr('disabled');
                        $('.loading_div').css('display', 'none');
                        return false;
                    }
                })
                .catch(function(error) {
                    // An error occurred
                    // alert("Exception: " + error);
                    $(".error_msg_div_text").html("There is some error connecting with the server .. Please try again ..");
                    $('.error_msg_div').css('display', 'block');
                    $("#login_validate_btn").removeAttr('disabled');
                    $('.loading_div').css('display', 'none');
                });
            return false;
        }
    </script>



    <script>
        // To show or hide password in the UI
        function togglePassword() {
            var x = document.getElementById("password");
            if (x.type === "password") {
                x.type = "text";

                $("#password-addon i").removeClass("fa-eye");
                $("#password-addon i").addClass("fa-eye-slash");
            } else {
                x.type = "password";

                $("#password-addon i").removeClass("fa-eye-slash");
                $("#password-addon i").addClass("fa-eye");
            }
        }
    </script>

    <script>
        // sleep time expects milliseconds
        function sleepInSeconds(time) {
            return new Promise((resolve) => setTimeout(resolve, time));
        }
    </script>

    <script type="text/javascript">
        <?php if ($session->getFlashdata('toastr_success')) { ?>
            toastr.success("<?php echo $session->getFlashdata('toastr_success'); ?>");
        <?php } else if ($session->getFlashdata('toastr_error')) {  ?>
            toastr.error("<?php echo $session->getFlashdata('toastr_error'); ?>");
        <?php } else if ($session->getFlashdata('toastr_warning')) {  ?>
            toastr.warning("<?php echo $session->getFlashdata('toastr_warning'); ?>");
        <?php } else if ($session->getFlashdata('toastr_info')) {  ?>
            toastr.info("<?php echo $session->getFlashdata('toastr_info'); ?>");
        <?php } ?>
    </script>
    <script>
        // To initialize tooltip again using method call
        function initializeTooltip() {
            // Ref: https://www.w3schools.com/bootstrap5/bootstrap_tooltip.php
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            })
        }
        initializeTooltip();
    </script>

    <script>
        toastr.options = {
            "positionClass": "toast-top-center"
        }
    </script>
</body>

</html>