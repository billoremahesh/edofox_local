<!-- Add Student Modal -->
<div id="add_student_modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title"><?= $title; ?></h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="#" method="post" id="add_student_form">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-4 mb-2">
                            <label class="form_label" for="last_name">Last Name <span class="req_color">*</span></label>
                            <input type="text" class="form-control" name="last_name" id="last_name" pattern="[a-zA-Z]+" title="Please enter alphabets only" required>
                        </div>
                        <div class="col-4 mb-2">
                            <label class="form_label" for="first_name">First Name <span class="req_color">*</span></label>
                            <input type="text" class="form-control" name="first_name" id="first_name" pattern="[a-zA-Z]+" title="Please enter alphabets only" required>
                        </div>
                        <div class="col-4 mb-2">
                            <label class="form_label" for="middle_name">Middle Name <span class="req_color">*</span></label>
                            <input type="text" class="form-control" name="middle_name" id="middle_name" pattern="[a-zA-Z]+" title="Please enter alphabets only" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-4 mb-2">
                            <label class="form_label" for="mobile_no">Mobile Number <span class="req_color">*</span></label>
                            <input type="text" class="form-control" name="mobile_no" id="mobile_no" minlength="10" maxlength="10" pattern="\d{10}" title="Enter 10 digit mobile number" required>
                        </div>
                        <div class="col-4 mb-2">
                            <label class="form_label" for="roll_no">Roll Number <span class="req_color">*</span></label>
                            <input type="text" class="form-control" name="roll_no" id="roll_no" maxlength="25" title="Enter roll number" required>
                        </div>
                        <div class="col-4 mb-2">
                            <label class="form_label" for="password">Password <span class="req_color">*</span></label>
                            <input type="text" class="form-control" name="password" id="password" value="123456" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-2">
                            <label class="form_label" for="gender">Gender <span class="req_color">*</span></label>
                            <select name="gender" id="gender" class="form-control" required>
                                <option value=""></option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>
                        <div class="col-6 mb-2">
                            <label class="form_label" for="package_id">Classroom Enrolled <span class="req_color">*</span></label>
                            <select name="package_id" id="package_id" class="form-control" required>
                                <option value=""></option>
                                <?php
                                if (!empty($all_classrooms_array)) :
                                    foreach ($all_classrooms_array as $row) :
                                        $package_id = $row['id'];
                                        $package_name = $row['package_name'];
                                        echo "<option value='$package_id'>$package_name</option>";
                                    endforeach;
                                endif;
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-2">
                            <label class="form_label" for="email">Email</label>
                            <input type="email" class="form-control" name="email" id="email">
                        </div>
                        <div class="col-6 mb-2">
                            <label class="form_label" for="parent_mobile_no">Parent Mobile Number</label>
                            <input type="text" class="form-control" name="parent_mobile_no" id="parent_mobile_no" pattern="[1-9]{1}[0-9]{9}" title="Enter 10 Digit Mobile No." maxlength="10">
                        </div>
                    </div>

                    <hr />

                    <div class="row mb-2 extra_details_structure_append_div" id="extra_details_structure_div_0">
                        <div class="col-10 mb-2">
                            <h6> Extra Details</h6>
                        </div>
                        <div class="col-2 mb-2 text-right">
                            <span class="add_extra_details_structure">
                                <i class="fa fa-plus-circle" aria-hidden="true"></i>
                            </span>
                        </div>
                    </div>


                    <p class="text-center" id="addProgress"></p>

                </div>
                <div class="modal-footer">
                    <input type="hidden" name="institute_id" value="<?= $instituteID; ?>" required />
                    <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success" id="add_new_student_button" name="add_new_student_submit">Add</button>
                </div>
            </form>
        </div>
    </div>
</div>


<script src="<?php echo base_url('assets/js/jquery.validate.js'); ?>"></script>

<script>
    $("#add_student_form").submit(function(e) {

        e.preventDefault(); // avoid to execute the actual submit of the form.

        var form = $(this);

        // Hiding the button to prevent double submits
        $("#add_new_student_button").hide();

        var student_last_name = $('#last_name').val().trim();
        var student_first_name = $('#first_name').val().trim();
        var student_middle_name = $('#middle_name').val().trim();
        var additional_details = "";
        var outArray = $('.extra_details_keys').toArray();
        outArray.forEach(function(keys,i) {
            additional_details += keys.value + ":";
            var indArray = $('.extra_details_value').toArray();
            additional_details += indArray[i].value + " | ";
        })
        // console.log(additional_details);

        var request = {
            student: {
                name: student_last_name + " " + student_first_name + " " + student_middle_name,
                phone: $('#mobile_no').val(),
                email: $('#email').val().trim(),
                rollNo: $('#roll_no').val(),
                gender: $('#gender').val(),
                examMode: "Online",
                parentMobileNo: $('#parent_mobile_no').val(),
                password: $('#password').val().trim(),
                packages: [{
                    id: $("#package_id").val(),
                    institute: {
                        id: <?php echo $instituteID; ?>
                    },
                    status: "Completed"
                }],
                payment: {
                    mode: "Offline"
                },
                additionalDetails: additional_details
            },
            institute: {
                id: <?php echo $instituteID; ?>
            }
        }

        //Load tokens first
        get_admin_token().then(function(result) {
                var resp = JSON.parse(result);
                if (resp != null && resp.status == 200 && resp.data != null && resp.data.admin_token != null) {


                    $("#addProgress").text("Saving ... Please wait ..");

                    $.ajax({
                        type: "POST",
                        url: rootAdmin + "registerStudent",
                        beforeSend: function(request) {
                            request.setRequestHeader("AuthToken", resp.data.admin_token);
                        },
                        data: JSON.stringify(request), // serializes the form's elements.
                        dataType: 'json',
                        contentType: 'application/json',
                        success: function(data) {
                            // show response from the php script.
                            // console.log(data);
                            if (data != null) {
                                if (data.status.statusCode == 200) {
                                    Snackbar.show({
                                        pos: 'top-center',
                                        text: 'Added new student successfully'
                                    });
                                    window.location.reload();
                                } else {
                                    $("#addProgress").text(data.status.responseText);
                                    $("#add_new_student_button").show();
                                }
                            } else {
                                $("#addProgress").text("Some error while connecting to the server ..");
                                $("#add_new_student_button").show();
                            }

                        }
                    });

                } else {
                    $("#add_new_student_button").show();
                    alert("Some error authenticating your request. Please clear your browser cache and try again.");
                }
            })
            .catch(function(error) {
                // An error occurred
                alert("Exception: " + error);
            });

    });
</script>


<script>
    // Add Multiple Extra Details Structure
    $(document).ready(function() {
        // Add Extra Details Structure
        $(".add_extra_details_structure").click(function() {
            // Finding total number of extra details divs
            var total_extra_details_append_divs = $(
                ".extra_details_structure_append_div"
            ).length;

            // last <div> with extra_details_structure_append_div class id
            var lastid = $(".extra_details_structure_append_div:last").attr("id");
            var split_id = lastid.split("_");
            var nextindex = Number(split_id[4]) + 1;

            var max = 15;
            // Check total number extra_details_structure_append_div
            if (total_extra_details_append_divs < max) {
                // Adding new div container after last occurance of extra_details_structure_append_div class
                $(".extra_details_structure_append_div:last").after(
                    "<div class='extra_details_structure_append_div' id='extra_details_structure_div_" +
                    nextindex +
                    "'></div>"
                );

                $("#extra_details_structure_div_" + nextindex).append(
                    "<div class='extra_details_append_subdiv row mb-2'><div class='col-md-4'><input type='text' class='form-control extra_details_keys' pattern='[a-zA-Z ]+' max-length='40' value='' placeholder='Enter a label' id='key_" +
                    nextindex +
                    "'></div><div class='col-md-6'><input type='text' pattern='[a-zA-Z ]+' class='form-control extra_details_value' value='' max-length='40' placeholder='Enter a value'  id='val_" +
                    nextindex +
                    "'></div><div class='col-md-2' onclick='remove_extra_structure_div(" +
                    nextindex +
                    ")'><span class='remove_ed_icon'><i class='fas fa-trash'></i></span></div></div>"
                );
            } else {
                alert("Exceed max number of file structure elements.");
            }
        });


    });

    function remove_extra_structure_div(remove_id) {
        $("#extra_details_structure_div_" + remove_id).remove();
    }
</script>