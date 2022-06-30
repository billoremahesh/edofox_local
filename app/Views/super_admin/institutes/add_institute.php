<!-- Include Header -->
<?php include_once(APPPATH . "Views/header.php"); ?>

<!-- Custom CSS -->
<link href="<?php echo base_url('assets/css/institutes.css?v=20210829'); ?>" rel="stylesheet">

<div class="container-fluid mt-4">


    <div class="flex-container-column">
        <div>
            <label class="h5 text-gray-800 text-uppercase"> <?= $title; ?> </h5>
        </div>
        <div class="breadcrumb_div" aria-label="breadcrumb">
            <ol class="breadcrumb_custom">
                <li class="breadcrumb_item"><a href="<?php echo base_url('/home'); ?>"> Home </a></li>
                <li class="breadcrumb_item"><a href="<?php echo base_url('institutes'); ?>"> Institutes </a></li>
                <li class="breadcrumb_item active" aria-current="page"> <?= $title; ?> </li>
            </ol>
        </div>
    </div>

    <!-- Content Row -->
    <div class="card shadow my-2" style="max-width: 900px;margin:auto;">


        <div class="card-body">
            <form action="" onsubmit="return add_institute();" method="post">

                <div class="row">

                    <div class="col-12 my-2">
                        <label class="form-label" for="institute_name"> Institute Name <span class="req_color">*</span></label>
                        <input type="text" name="institute_name" class="form-control form-control-user" id="institute_name" placeholder="Institute Name" maxlength="40">
                    </div>

                    <div class="col-6 my-2">
                        <label class="form-label" for="email">Billing Email <span class="req_color">*</span></label>
                        <input type="text" name="email" class="form-control form-control-user" id="email"  required>
                    </div>

                    <div class="col-6 my-2">
                        <label class="form-label" for="contact">Billing Contact Number <span class="req_color">*</span></label>
                        <input type="text" name="contact" class="form-control form-control-user" id="contact" required>
                    </div>

                    <div class="col-4 my-2">
                        <label class="form-label" for="username"> User Name <span class="req_color">*</span></label>
                        <input type="text" name="username" class="form-control form-control-user" id="username" placeholder="Username">
                    </div>


                    <div class="col-4 my-2">
                        <label class="form-label" for="password">Password <span class="req_color">*</span></label>
                        <input type="password" name="password" class="form-control form-control-user" id="password" placeholder="Password">
                    </div>

                    <div class="col-4 my-2">
                        <label class="form-label" for="password">Retype Password <span class="req_color">*</span></label>
                        <input type="password" name="retype_password" class="form-control form-control-user" id="retype_password" placeholder="Retype Password">
                    </div>


                    <div class="col-4 my-2">
                        <label class="form-label" for="total_students"> Max Students <span class="req_color">*</span></label>
                        <input type="text" name="total_students" class="form-control form-control-user" id="total_students" placeholder="Total Students" required>
                    </div>

                    <div class="col-4 my-2">
                        <label class="form-label" for="storage_quota"> Storage Quota (GB)</label>
                        <input type="text" name="storage_quota" class="form-control form-control-user" id="storage_quota" placeholder="Storage Quota">
                    </div>

                    <div class="col-4 my-2">
                        <label class="form-label" for="expiry_date"> Expiry Date </label>
                        <input type="text" name="expiry_date" class="form-control form-control-user expiry_date" autocomplete="off" id="expiry_date" placeholder="Expiry Date">
                    </div>


                    <div class="col-4 my-2">
                        <label class="form-label" for="account_manager"> Account Manager <span class="req_color">*</span></label>
                        <select class="form-select" id="account_manager" name="account_manager" required>
                            <option></option>
                            <?php
                            if (!empty($sales_team)) {
                                foreach ($sales_team as $account_manager) {
                            ?>
                                    <option value="<?= $account_manager['id']; ?>"><?= $account_manager['name']; ?></option>
                            <?php
                                }
                            }
                            ?>
                        </select>

                    </div>

                    <!-- Error Message -->
                    <div class="col-12 my-2 error_msg_div" style="display: none;">
                        <p class="error_msg_div_text">There is an error while adding insitute. Try again.</p>
                    </div>


                    <!-- Loading Message -->
                    <div class="col-12 my-2 loading_div" style="display: none;">
                        <span class="spinner-border spinner-border-sm"></span> Adding new institute. Please Wait...
                    </div>

                    <div class="col-12 my-2">
                        <button type="submit" name="add_institute_submit" class="btn btn-primary" id="login_validate_btn"> Create </button>
                    </div>

                </div>

            </form>
        </div>
    </div>

</div>

<!-- Include Footer -->
<?php include_once(APPPATH . "Views/footer.php"); ?>


<script src="<?php echo base_url('assets/js/institutes.js?v=20220425'); ?>"></script>
<script>
    $(document).ready(function() {

        //  Exam date
        $('.expiry_date').datetimepicker({
            fontAwesome: 'font-awesome',
            autoclose: true
        });

    });
</script>

<script>
    // To call asynchronously Validate Login
    function add_institute() {
        // Disable Submit Button
        $("#login_validate_btn").attr("disabled", true);
        $('.error_msg_div').css('display', 'none');
        $('.loading_div').css('display', 'block');

        var institute_name = $("#institute_name").val();
        var contact = $("#contact").val();
        var username = $("#username").val();
        var password = $("#password").val();
        var retype_password = $("#retype_password").val();
        var email = $("#email").val();
        // var purchase = $("#purchase").val();
        var total_students = $("#total_students").val();
        var storage_quota = $("#storage_quota").val();
        var expiry_date = $("#expiry_date").val();
        var account_manager = $("#account_manager").val();


        if (password != retype_password) {
            alert("password and retype password should be same");
            $("#login_validate_btn").removeAttr('disabled');
            $('.loading_div').css('display', 'none');
            return false;
        }

        // Add new institute using promise
        add_new_institute(username, password, institute_name, contact, email, purchase = 'basic', total_students, storage_quota, expiry_date, account_manager)
            .then(function(result) {
                var response = JSON.parse(result);
                // console.log("response", result);
                if (response.status.statusCode == 200) {
                    // Reevaluating result using a async promise
                    Snackbar.show({
                        pos: 'top-center',
                        text: "New Institute created successfully"
                    });
                    redirect_to_add_subscriptions();
                    // window.location = base_url + "/institutes";
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

    function redirect_to_add_subscriptions(){
        $.ajax({
            url: base_url + "/institutes/get_newly_created_institute_id",
            type: "POST",
            data: {},
            success: function(result) {
                window.location = base_url + "/subscriptions/new_subscription/"+result;
            }
        });
    }
</script>