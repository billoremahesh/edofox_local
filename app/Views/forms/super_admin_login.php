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


  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Karla:400,700&display=swap" rel="stylesheet">

  <!-- Material Icons -->
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

  <!-- Snackbar -->
  <link href="<?php echo base_url('assets/plugins/snackbar-material/snackbar.min.css'); ?>" rel="stylesheet" />


  <!-- Custom CSS -->
  <link href="<?php echo base_url('assets/css/login.css?v=20211031'); ?>" rel="stylesheet">

</head>

<body>

  <div class="container pt-4" id="main_div">

    <div class="text-center text-uppercase">
      <img src="<?php echo base_url('assets/img/edofox-name-logo-black.png'); ?>" style="height:60px; margin-top: 0px" />
      <p><b>SuperAdmin</b></p>
    </div>

    <div class="row">
      <div class="col-md-12">
        <div class='card_box'>
          <h5 id="title">Login</h5>
          <hr>
          <form action="<?= base_url('/login/super_admin_login_validate'); ?>" method="post">


            <div class="input-group mb-3">
              <input type="text" name="username" id="username" class="form-control" placeholder="Username" value="<?= old('username') ?>" aria-label="Username" aria-describedby="username-addon" minlength="4" maxlength="40" autocomplete="on" required>
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

            <button type="submit" id="login_validate_btn" name="login_validate" class="login_button">Login</button>
            <button type="reset" class="reset_button">Reset </button>
          </form>

        </div>
      </div>

    </div>

  </div>


  <script src="<?php echo base_url('assets/jquery-3.6.0.min.js'); ?>"></script>
  <script src="<?php echo base_url('assets/bootstrap-5.0.2-dist/js/bootstrap.bundle.min.js'); ?>"></script>

  <!-- Toastr JS -->
  <script src="<?php echo base_url('assets/plugins/toastr/toastr.min.js'); ?>"></script>

  <!-- Snackbar JS -->
  <!-- Ref: https://www.polonel.com/snackbar/ -->
  <script src="<?php echo base_url('assets/plugins/snackbar-material/snackbar.min.js'); ?>"></script>


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


</body>

</html>