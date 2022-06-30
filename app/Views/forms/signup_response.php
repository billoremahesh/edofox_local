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

    <!-- Datatable CSS For Bootstrap 5  -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs5/dt-1.11.2/af-2.3.7/b-2.0.0/cr-1.5.4/date-1.1.1/fc-3.3.3/fh-3.1.9/r-2.2.9/sc-2.0.5/sb-1.2.1/sp-1.4.0/datatables.min.css" />

    <!-- Google Fonts -->
    <link href='http://fonts.googleapis.com/css?family=Roboto:400,700,300' rel='stylesheet' type='text/css'>

    <link href="<?php echo base_url('assets/plugins/snackbar-material/snackbar.min.css?v=20210829'); ?>" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?php echo base_url('assets/css/signup.css?v=20210829'); ?>" rel="stylesheet">

    <!-- lottiefiles JS -->
    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js" async></script>

</head>

<body>

    <div class="container" id="appl_form">
        <lottie-player src="<?php echo base_url('assets/img/animations/9917-success.json'); ?>" background="transparent" speed="1" style="width: 100px; margin:auto;height:auto" autoplay></lottie-player>

        <h3 class="text-center" style="color:#8bc34a">You have registered successfully!</h3>

        <?php if (!empty($token) && !empty($student_data)) : ?>
            <hr />
            <div>
                <table style="margin: auto;" class="">
                    <tbody>
                        <tr>
                            <td>Name</td>
                            <td><?= $student_data['name']; ?></td>
                        </tr>
                        <tr>
                            <td>Username</td>
                            <td><?= $student_data['username'] ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
        <hr />

        <div class="text-center">
            <a href="<?= HTTPHOST; ?>"><button class="btn btn-success">Go to Login</button></a>
        </div>

        <div id="app_button_div" class="text-center">
            <hr />
            <p>Or download our app: </p>
            <a href="#" target="_blank" id="app-download-button">
                <img src="<?php echo base_url('assets/img/google-play-badge.png'); ?>" style="width: 150px;" />
            </a>
        </div>

    </div>

    <div class="footer">
        <p>Handcrafted by Team Matter Softwares</p>
    </div>

    <script src="<?php echo base_url('assets/jquery-3.6.0.min.js'); ?>"></script>
    <script src="<?php echo base_url('assets/bootstrap-5.0.2-dist/js/bootstrap.bundle.min.js'); ?>"></script>

    <script>
        var redirect = localStorage.getItem("completePayment")
        var app_url = localStorage.getItem("edofox_app_url");

        if (redirect == 'Y') {
            window.location.href = "../test_instructions.html"
        }
    </script>


    <script>
        //Showing the android download button
        if (app_url) {
            // console.log("App url found");
            app_url = decodeURIComponent(app_url);
            // console.log("decoded app url", app_url);
            $("#app-download-button").attr("href", app_url);
        } else {
            $("#app-download-button").attr("href", "https://play.google.com/store/apps/details?id=com.mattersoft.edofoxapp");

            // Hiding the app download image button
            $("#app_button_div").hide();
        }
    </script>
</body>

</html>