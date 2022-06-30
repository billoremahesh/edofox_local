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
    <div class="card shadow mb-4" style="max-width: 900px;margin:auto;">

        <div class="card-body">

            <form action="" onsubmit="return update_institute();" method="post">
                <input type="hidden" value="<?php echo $institute_id; ?>" id="institute_id" name="institute_id">

                <div class="row display-flex">


                    <div class="col-md-4">
                        <div class="mb-2">
                            <label class="form-label" for="total_students"> Max Students </label>
                            <input type="text" name="total_students" class="form-control form-control-user" id="total_students" placeholder="Total Students" value="<?= $institute_data['max_students'] ?>">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-2">
                            <label class="form-label" for="storage_quota"> Storage Quota (GB)</label>
                            <input type="text" name="storage_quota" class="form-control form-control-user" id="storage_quota" placeholder="Storage Quota" value="<?= $institute_data['storage_quota'] ?>">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-2">
                            <label class="form-label" for="expiry_date"> Expiry Date </label>
                            <input type="text" name="expiry_date" class="form-control form-control-user expiry_date" id="expiry_date" placeholder="Expiry Date" autocomplete="off" value="<?php echo date_format_custom($institute_data['expiry_date'], 'Y-m-d'); ?>">
                        </div>
                    </div>

                </div>




                <!-- Error Message -->
                <div class="error_msg_div" style="display: none;">
                    <p class="error_msg_div_text">There is an error while updating insitute. Try again.</p>
                </div>


                <!-- Loading Message -->
                <div class="loading_div" style="display: none;">
                    <span class="spinner-border spinner-border-sm"></span> Updating institute. Please Wait...
                </div>


                <div class="text-right">
                    <button type="submit" name="edit_institute_submit" class="btn btn-primary" id="submit_button"> Update </button>
                </div>
            </form>



        </div>
    </div>



</div>

<!-- Include Footer -->
<?php include_once(APPPATH . "Views/footer.php"); ?>
<script src="<?php echo base_url('assets/js/institutes.js'); ?>"></script>


<script>
    $(document).ready(function() {

        //  Exam date
        $('.expiry_date').datetimepicker({
            fontAwesome: 'font-awesome',
            autoclose: true
        });

    });
</script>


<script src="../js/angular/angular.js"></script>
<script src="../js/main.js"></script>
<script src="../js/institutes.js"></script>

<script>
    // To call asynchronously Validate Login
    function update_institute() {
        // Disable Submit Button
        $("#login_validate_btn").attr("disabled", true);
        $('.error_msg_div').css('display', 'none');
        $('.loading_div').css('display', 'block');

        var institute_id = $("#institute_id").val();
        // var purchase = $("#purchase").val();
        var total_students = $("#total_students").val();
        var storage_quota = $("#storage_quota").val();
        var expiry_date = $("#expiry_date").val();

        // Add new institute using promise
        update_institute_features(institute_id, purchase = 'basic', total_students, storage_quota, expiry_date)
            .then(function(result) {
                var response = JSON.parse(result);
                // console.log("response", result);
                if (response.status.statusCode == 200) {
                    // Reevaluating result using a async promise
                    Snackbar.show({
                        pos: 'top-center',
                        text: "Institute upgraded successfully"
                    });
                    window.location = base_url + "/institutes";
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