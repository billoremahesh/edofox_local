<!-- Include Header -->
<?php include_once(APPPATH . "Views/header.php"); ?>

<!-- Custom CSS -->
<link href="<?php echo base_url('assets/css/dlp/manage_dlp.css?v=20211030'); ?>" rel="stylesheet">

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
        <div class="container">
            <div class="row">
                <div class="col-sm-3">
                    <div class="top-counts-block">
                        <div>
                            <img src="https://edofox-s3.s3.ap-south-1.amazonaws.com/public/statics/blackboard.png" style="width: 32px;" />
                        </div>

                        <div style="margin-left: 16px;">
                            <label class="counts-subtitle">Total DLP Classrooms</label>
                            <h4 class="count-number" id="total_dlp_classrooms_text"><?= $dlp_classroom_count; ?></h4>
                        </div>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="top-counts-block">
                        <div>
                            <img src="https://edofox-s3.s3.ap-south-1.amazonaws.com/public/statics/video_icon.png" style="width: 32px;" />
                        </div>

                        <div style="margin-left: 16px;">
                            <label class="counts-subtitle">Total Videos</label>
                            <h4 class="count-number" id="total_videos_count_text"><?= $dlp_video_count; ?></h4>
                        </div>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="top-counts-block">
                        <div>
                            <img src="https://edofox-s3.s3.ap-south-1.amazonaws.com/public/statics/pdf_icon.png" style="width: 32px;" />
                        </div>

                        <div style="margin-left: 16px;">
                            <label class="counts-subtitle">Total Documents</label>
                            <h4 class="count-number" id="total_docs_count_text"><?= $dlp_doc_count; ?></h4>
                        </div>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="top-counts-block">
                        <div>
                            <img src="https://edofox-s3.s3.ap-south-1.amazonaws.com/subjectIcons/subject-exam.png" style="width: 32px;" />
                        </div>

                        <div style="margin-left: 16px;">
                            <label class="counts-subtitle">Total DLP Assignments</label>
                            <h4 class="count-number" id="total_assignments_count_text"><?= $dlp_assignments_count; ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div>

            <div style="text-align: right;">
                <button class="btn primary_add_button" onclick="show_clone_content_modal();"> Clone DLP Content </button>
                <button class="btn primary_add_button" onclick="show_add_modal('modal_div','addNewDlpData','dlp/add_dlp_content_modal');">Add New DLP PDF/Video/Chapter-Test</button>
            </div>

            <!-- Document Progress Bar -->
            <div id="progressList">

            </div>

            <div class="row">
                <div class="col-sm-3">
                    <label class="text-muted">DLP Courses/Classrooms:</label>
                    <?php if (!empty($classrooms_data)) : ?>


                        <div class="list-group mb-4">

                            <?php
                            foreach ($classrooms_data as $classroom_data) :
                            ?>

                                <button type="button" class="list-group-item list-group-item-action" onclick="load_subject_chapters('<?= $classroom_data['id']; ?>', this)"><?= $classroom_data['package_name']; ?></button>

                            <?php endforeach; ?>

                        </div>
                    <?php else : ?>
                        <h4>No DLP classrooms found. Start by adding some DLP classrooms.</h4>
                    <?php endif; ?>

                    <a class="btn btn-block text-uppercase bg-white" href="<?= base_url('classrooms'); ?>" style="color: #5b51d8"> <img src="<?= base_url('assets/img/icons/blackboard.png'); ?>" style="width: 64px;" /> <br>Add New DLP Classroom</a>

                    <div class="mt-2">
                        <a class="btn btn-block text-uppercase" href="<?= base_url('dlp/deleted_dlp_content'); ?>">Show Deleted Content</a>
                    </div>
                </div>

                <div class="col-sm-9">

                    <div class="text-center" id="course-subject-chapters-loading-div" style="display: none;">
                        <i class='fas fa-atom fa-spin fa-2x fa-fw'></i>
                    </div>
                    <div id="course-subject-chapters-div"></div>

                </div>
            </div>


        </div>


    </div>
</div>


<!-- Clone Chapter Content Modal -->
<div id="clone_chapter_content_modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"> Clone Chapter Content </h4>
            </div>
            <?php echo form_open_multipart('dlp/clone_dlp_chapter_content_submit'); ?>
            <div class="modal-body">

                <input type="hidden" name="chapter_ids" id="chapter_ids" required>
                <input type="hidden" name="package_id" id="package_id" required>

                <label class="text-muted">DLP Courses/Classrooms:</label>
                <?php if (!empty($classrooms_data)) : ?>
                    <select class="classrooms-dropdown" style="width: 100%;" name="classroom_ids[]" id="classroom_ids" multiple>
                        <?php
                        foreach ($classrooms_data as $classroom) {
                            if (isset($_SESSION['student_list_classroom_filter']) && is_array($_SESSION['student_list_classroom_filter']) && !empty($_SESSION['student_list_classroom_filter']) && in_array($classroom['id'], $_SESSION['student_list_classroom_filter'])) {
                                // Showing selected filter values from session
                                echo "<option value='" . $classroom['id'] . "' selected>" . $classroom['package_name'] . "</option>";
                            } else {
                                echo "<option value='" . $classroom['id'] . "'>" . $classroom['package_name'] . "</option>";
                            }
                        }
                        ?>
                    </select>
                <?php endif; ?>

            </div>
            <div class="modal-footer">
                <input type="hidden" name="redirect" value="dlp" required />
                <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-success"> Clone </button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>


<!-- Include Footer -->
<?php include_once(APPPATH . "Views/footer.php"); ?>
<script src="<?php echo base_url('assets/js/manage_dlp.js'); ?>"></script>
<!-- Needed for Mogii upload -->
<script src="https://sdk.amazonaws.com/js/aws-sdk-2.706.0.min.js"></script>

<script>
    var classroomId = sessionStorage.getItem("classroom_id");
    var instituteId = <?= decrypt_cipher($instituteID); ?>;
    $(document).ready(function() {
        if (classroomId) {
            load_subject_chapters(classroomId);
        }
    });
</script>

<script>
    // To call asynchronously Validate Login
    function load_subject_chapters(classroom_id, element) {
        sessionStorage.setItem("classroom_id", classroom_id);

        // Loading
        $("#course-subject-chapters-div").html("");
        $("#course-subject-chapters-loading-div").show();

        $('.list-group-item-action').removeClass('active');
        if (element) {
            $(element).addClass('active');
        }


        // console.log("Inside fetchTotalSubjectChaptersList." + classroom_id);

        $('.error_msg_div').css('display', 'none');
        $('.loading_div').css('display', 'block');


        fetchTotalSubjectChaptersList(classroom_id)
            .then(function(result) {
                var response = JSON.parse(result);
                // console.log("response", result);

                // document.getElementById("chapters-list-content").innerHTML = this.responseText;
                $("#course-subject-chapters-div").html(response);
                $("#course-subject-chapters-loading-div").hide();
                $('[data-bs-toggle="tooltip"]').tooltip();

                //Saving json object in string form in cookie
                var classroomData = {
                    classroom_id: classroom_id,
                };

                //saving cookie of which classroom tab was last clicked to fetch when returning later
                createCookie("active_dlp_classroom", JSON.stringify(classroomData), 1);


            })
            .catch(function(error) {
                // An error occurred
                // alert("Exception: " + error);
                $(".error_msg_div_text").html("There is some error connecting with the server .. Please try again ..");
                $('.error_msg_div').css('display', 'block');
                $('.loading_div').css('display', 'none');
            });
        return false;
    }
</script>

<script>
    // Show Clone Content Modal
    function show_clone_content_modal() {
        var checkboxes = document.getElementsByClassName('bulk_chapter_select');
        var checkedOne = Array.prototype.slice.call(checkboxes).some(x => x.checked);
        if (!checkedOne) {
            alert("Select aleast one test");
        } else {

            // Check selected checkboxes
            var checkedValue = [];
            var inputElements = document.getElementsByClassName('bulk_chapter_select');

            for (var i = 0; inputElements[i]; ++i) {
                if (inputElements[i].checked) {
                    checkedValue.push(inputElements[i].value);
                }
            }
            if (sessionStorage.getItem("classroom_id")) {} else {
                alert("Select aleast one classroom");
            }
            var selected_pkg_value = sessionStorage.getItem("classroom_id");
            $('#chapter_ids').val(checkedValue.toString());
            $('#package_id').val(selected_pkg_value);
            // Open Modal
            $("#clone_chapter_content_modal").modal('show');
        }
    }
</script>

<script>
    $('.classrooms-dropdown').select2({
        dropdownAutoWidth: true,
        width: '100%',
        dropdownParent: $("#clone_chapter_content_modal")
    });
</script>