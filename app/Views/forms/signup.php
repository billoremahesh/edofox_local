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
    <link href='https://fonts.googleapis.com/css?family=Roboto:400,700,300' rel='stylesheet' type='text/css'>

    <link href="<?php echo base_url('assets/plugins/snackbar-material/snackbar.min.css?v=20210829'); ?>" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?php echo base_url('assets/css/signup.css?v=20210829'); ?>" rel="stylesheet">

</head>

<body ng-app="registration" ng-controller="register">

    <div class="container">
        <?php

        // Reference: https://stackoverflow.com/questions/20203063/mysql-where-id-is-in-array    
        // $classroom_array = implode("','", $studentClassrooms);

        $instituteName = $institute_data['institute_name'];
        $app_url = urlencode($institute_data['app_url']);
        $prefix = "../";

        if (strpos($institute_data['logo_path'], 'http') !== false) {
            $prefix = "";
        }
        $logo_path = $prefix . $institute_data['logo_path'];

        ?>

        <div id="error_notify_div">
            <p class="top_note">For smooth experience, Please use updated Google Chrome browser to fill this form.</p>
            <!--        <p class="top_note">मी फॉर्म भरण्यापूर्वी माहित पुस्तकातील संपूर्ण माहिती वाचली आहे याची हमी घेतो </p>-->
        </div>

        <div id="appl_form">



            <div id="head_part" class="text-center">

                <?php if (isset($logo_path)) : ?>
                    <img src="<?= base_url($logo_path); ?>" class="img-fluid text-center" alt="no-image" id="banner_img" style="max-height: 75px; max-width:150px;display: block;margin:auto; float: left; padding-right: 10px;" />

                <?php endif; ?>
                <h3 class="text-uppercase fw-normal"><?= $instituteName ?></h3>

            </div>


            <form role="form" id="registerForm" name="registerForm">
                <div class="row">
                    <div class="col-md-12 mb-2">
                        <p class="text-muted">Online Registration:</p>
                    </div>

                    <div class="col-md-6 mb-2">
                        <label for="stu_surname" class="label1">1) Surname: <span id="notice_span">*</span></label>
                    </div>
                    <div class="col-md-6 mb-2">
                        <input type="text" ng-model="user.lastName" class="form-control col-md-6" id="stu_surname" name="stu_surname" pattern="[a-zA-Z]+" title="Only Enter Letters" required>
                        <p class="error small" ng-if="!registerForm.stu_surname.$valid && submitted">Please enter a valid surname</p>
                    </div>

                </div>

                <div class="row">
                    <div class="col-md-6 mb-2">
                        <label for="stu_name" class="label1">FIRST Name: <span id="notice_span">*</span></label>
                    </div>
                    <div class="col-md-6 mb-2">
                        <input type="text" ng-model="user.firstName" class="form-control" id="stu_name" name="stu_name" pattern="[a-zA-Z]+" title="Only Enter Letters" required>
                        <p class="error small" ng-if="!registerForm.stu_name.$valid && submitted">Please enter a valid first name</p>
                    </div>

                </div>

                <div class="row">
                    <div class="col-md-6 mb-2">
                        <label for="stu_fathername" class="label1">Father's Name: <span id="notice_span">*</span></label>
                    </div>
                    <div class="col-md-6 mb-2">
                        <input type="text" ng-model="user.middleName" class="form-control" id="stu_fathername" name="stu_fathername" pattern="[a-zA-Z]+" title="Only Enter Letters" required>
                        <p class="error small" ng-if="!registerForm.stu_fathername.$valid && submitted">Please enter a valid father name</p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-2">
                        <label for="stu_mobile1" class="label1">2) Mobile No: <span id="notice_span">*</span></label>
                        <p class="label_note">You may get a SMS on this number after signup</p>
                    </div>
                    <div class="col-md-6 mb-2">
                        <input type="tel" ng-model="user.phone" class="form-control" id="stu_mobile1" name="stu_mobile1" pattern="[1-9]{1}[0-9]{9}" title="Enter 10 Digit Mobile No." maxlength="10" required>
                        <p class="error small" ng-if="!registerForm.stu_mobile1.$valid && submitted">Please enter a valid mobile number</p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-2">
                        <label for="stu_email" class="label1">Email address: <span id="notice_span">*</span></label>
                    </div>
                    <div class="col-md-6 mb-2">
                        <input type="email" ng-model="user.email" class="form-control" id="stu_email" name="stu_email" title="Enter valid Email" required>
                        <p class="error small" ng-if="!registerForm.stu_email.$valid && submitted">Please enter a valid email id</p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-2">
                        <label for="stu_roll" class="label1">Enter username/roll number: <span id="notice_span">*</span></label>
                    </div>
                    <div class="col-md-6 mb-2">
                        <input type="text" ng-model="user.rollNo" class="form-control" id="stu_roll" name="stu_roll" title="Enter valid username/roll number" pattern="[0-9a-zA-Z@._-]+" maxlength="50" minlength="6" required>
                        <p class="error small" ng-if="!registerForm.stu_roll.$valid && submitted">Please enter a username or roll number provided by institute. This will be used for login. Minimum 6 characters required. Only letters, numbers and "@", ".", "_", "-" allowed</p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-2">
                        <label for="stu_password" class="label1">Enter a password: <span id="notice_span">*</span></label>
                    </div>
                    <div class="col-md-6 mb-2">
                        <input type="password" ng-model="user.password" class="form-control" id="stu_password" name="stu_password" title="Enter a password" maxlength="15" minlength="6" required>
                        <p class="error small" ng-if="!registerForm.stu_password.$valid && submitted">Please enter a password. Minimum 6 characters required</p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-2">
                        <label for="gender" class="label1">3) Gender: <span id="notice_span">*</span></label>
                    </div>
                    <div class="col-md-6 mb-2">

                        <label class="checkbox-inline"><input type="radio" name="gender" ng-model="user.gender" value="Male"> Male</label>
                        <label class="checkbox-inline"><input type="radio" name="gender" ng-model="user.gender" value="Female"> Female</label>
                        <p class="error small" ng-if="!user.gender && submitted">Please select a gender</p>
                    </div>
                </div>


                <div class="row">
                    <div class="col-md-6 mb-2">
                        <label for="choice_of_exam" class="label1">4) Courses available: <span id="notice_span">*</span></label>
                        <p class="small">(Choose any one)</p>
                    </div>
                    <div class="col-md-6 mb-2">

                        <div ng-repeat="package in packages">
                            <label class="checkbox-inline">
                                <input type="checkbox" ng-model="package.selected">{{package.name}}
                            </label>
                        </div>

                        <!--<label class="checkbox-inline"><input type="checkbox" name="choice_of_exam" value="NEET">NEET</label>
                    <label class="checkbox-inline"><input type="checkbox" name="choice_of_exam" value="MHT-CET">MHT-CET</label>-->
                        <p class="error small" ng-if="noPackage && submitted">Please select atleast one package</p>
                    </div>
                </div>

                <input type="hidden" ng-model="user.examMode" ng-init="user.examMode='Online'" value="Online">

                <div class="row">
                    <div class="col-md-6 mb-2">
                        <label for="stu_date_of_birth" class="label1">7) Date of Birth: <span id="notice_span">(DD-MM-YYYY) *</span></label>
                    </div>


                    <div class="col-md-2 mb-2">

                        <select id="stu_date_of_birth_date" name="stu_date_of_birth_date" class="form-control" ng-model="user.day" required>
                            <option value="01">01</option>
                            <option value="02">02</option>
                            <option value="03">03</option>
                            <option value="04">04</option>
                            <option value="05">05</option>
                            <option value="06">06</option>
                            <option value="07">07</option>
                            <option value="08">08</option>
                            <option value="09">09</option>
                            <option value="10">10</option>
                            <option value="11">11</option>
                            <option value="12">12</option>
                            <option value="13">13</option>
                            <option value="14">14</option>
                            <option value="15">15</option>
                            <option value="16">16</option>
                            <option value="17">17</option>
                            <option value="18">18</option>
                            <option value="19">19</option>
                            <option value="20">20</option>
                            <option value="21">21</option>
                            <option value="22">22</option>
                            <option value="23">23</option>
                            <option value="24">24</option>
                            <option value="25">25</option>
                            <option value="26">26</option>
                            <option value="27">27</option>
                            <option value="28">28</option>
                            <option value="29">29</option>
                            <option value="30">30</option>
                            <option value="31">31</option>
                        </select>

                    </div>

                    <div class="col-md-2 mb-2">

                        <select id="stu_date_of_birth_month" name="stu_date_of_birth_month" ng-model="user.month" class="form-control" required>
                            <option value="01">01</option>
                            <option value="02">02</option>
                            <option value="03">03</option>
                            <option value="04">04</option>
                            <option value="05">05</option>
                            <option value="06">06</option>
                            <option value="07">07</option>
                            <option value="08">08</option>
                            <option value="09">09</option>
                            <option value="10">10</option>
                            <option value="11">11</option>
                            <option value="12">12</option>
                        </select>
                    </div>

                    <div class="col-md-2 mb-2">

                        <select id="stu_date_of_birth_year" name="stu_date_of_birth_year" ng-model="user.year" class="form-control" required>

                            <?php
                            $current_year = intval(date("Y"));
                            $start_year = $current_year - 18;
                            $end_year = $current_year - 10;

                            for ($i = $start_year; $i <= $end_year; $i++) {
                                echo "<option value='$i'>$i</option>";
                            }
                            ?>
                        </select>
                        <p class="error small" ng-if="( (!registerForm.stu_date_of_birth_year.$valid || !registerForm.stu_date_of_birth_month.$valid || !registerForm.stu_date_of_birth_date.$valid)) && submitted">Please select a valid Date of birth</p>
                    </div>


                </div>

                <div class="row" ng-if="getPrice() > 0">
                    <div class="col-md-6 mb-2">
                        <h4> You need to pay : {{getPrice()}} /- </h4>
                    </div>

                </div>




                <p id="notice_para">* marked fields are mandatory/compulsory</p>
                <ul><strong>Terms and conditions:
                        <!--<li>You have read all the instructions given <a target="_blank" href="">HERE</a> before registering by filling this form.</li>-->

                        <li>Please make sure you are signing up to the desired institute.</li>
                        <li>Please make sure you have entered your correct personal information.</li>
                        <li>Payment terms (if any) are decided by the institute and Edofox is not accountable for any of it. Please contact your institute in case of such issues.</li>
                        <li>Approval of your profile is the responsibility of the institute you are signing up to. Edofox cannot be held liable for it.</li>
                    </strong>

                </ul>

                <div class="mb-2">
                    <input type="checkbox" id="terms-checkbox" name="acceptance" value="yes" ng-model="accepted" required> <label for="terms-checkbox">I accept the above instructions</label>
                    <p class="error small" ng-if="!registerForm.acceptance.$valid && submitted">Please accept the terms and conditions</p>
                </div>

                <input type="hidden" value="true" ng-init="user.payment.offline = true" ng-model="user.payment.offline">

                <p class="error small" ng-if="!registerForm.$valid && submitted">Please fill all the required fields</p>

                <button type="button" class="btn btn-lg" id="my_btn" name="iit_reg_submit" ng-click="submit()" ng-disabled="progress">{{progress ? 'Submitting ..' : 'Submit'}}</button>
                <p class="footer_terms">By clicking "Submit" you agree to the terms and conditions above.</p>
                <p class="footer_terms"><strong>For technical queries only regarding form, Call: 7378865408 (11am - 6pm)</strong></p>
            </form>

        </div>


        <div class="footer">
            <p>Powered by <a href="http://edofox.com" target="_blank">Edofox</a> </p>
            <p>Handcrafted by Team Matter Softwares</p>
        </div>

    </div>

    <script type="text/javascript">
        var instituteId = <?= $institute_data['id']; ?>;
    </script>

    <script src="https://www.gstatic.com/firebasejs/4.10.1/firebase.js"></script>
    <script src="<?php echo base_url('assets/jquery-3.6.0.min.js'); ?>"></script>
    <script src="<?php echo base_url('assets/bootstrap-5.0.2-dist/js/bootstrap.bundle.min.js'); ?>"></script>
    <script src="<?php echo base_url('assets/js/url.js'); ?>"></script>
    <script src="<?php echo base_url('assets/js/angular.js'); ?>"></script>
    <script src="<?php echo base_url('assets/js/main.js'); ?>"></script>
    <script src="<?php echo base_url('assets/js/registration/service.js'); ?>"></script>

    <script src="<?php echo base_url('assets/plugins/snackbar-material/snackbar.min.js'); ?>"></script>



    <script>
        localStorage.setItem("edofox_app_url", "<?= $app_url ?>");
        // console.log("appurl", decodeURIComponent(localStorage.getItem("edofox_app_url")));

        $("#form11").submit(function(e) {
            if (!$('input[type=checkbox]:checked').length) {
                alert("Please accept the instructions by selecting checkbox.");
                return false;
            }

            return true;
        });
    </script>



    <script type="text/javascript">
        <?php if (session()->getFlashdata('toastr_success')) { ?>
            toastr.success("<?php echo session()->getFlashdata('toastr_success'); ?>");
        <?php } else if (session()->getFlashdata('toastr_error')) {  ?>
            toastr.error("<?php echo session()->getFlashdata('toastr_error'); ?>");
        <?php } else if (session()->getFlashdata('toastr_warning')) {  ?>
            toastr.warning("<?php echo session()->getFlashdata('toastr_warning'); ?>");
        <?php } else if (session()->getFlashdata('toastr_info')) {  ?>
            toastr.info("<?php echo session()->getFlashdata('toastr_info'); ?>");
        <?php } ?>
    </script>
</body>

</html>