    <!-- Add DLP Content Modal -->
    <div id="addNewDlpData" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title"><?= $title; ?></h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <?php echo form_open_multipart('dlp/add_dlp_content_submit'); ?>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-2">
                                <label class="form-label" for="add_classroom_id">Course</label>

                                <select class="form-control classrooms-dropdown" name="classrooms[]" id="add_classroom_id" style="width:200px !important;" onchange="fetchCourseSubjects(this)" multiple="multiple" required>
                                    <option value=""></option>
                                    <?php
                                    foreach ($classrooms_list as $classroom) :
                                    ?>
                                        <option value="<?= $classroom['id']; ?>"><?= $classroom['package_name']; ?></option>
                                    <?php
                                    endforeach;
                                    ?>
                                </select>
                            </div>
                        </div>


                        <div class="col-md-4">
                            <div class="mb-2" id="subject-dropdown-div"></div>
                        </div>


                        <div class="col-md-4">
                            <div class="mb-2" id="chapters-dropdown-div"></div>
                        </div>
                    </div>

                    <div class="row" id="resource-type-div" style="display: none;">
                        <div class="col-md-4">
                            <div class="mb-2">
                                <label class="form-label" for="resource_type">Resource Type</label>
                                <select class="form-control" name="resource_type" id="resource_type" onchange="toggleResourceType(this)" required>
                                    <option value=""></option>
                                    <option value="VIDEO">Video</option>
                                    <option value="DOC">PDF Document</option>
                                    <option value="TEST">Test</option>
                                </select>
                            </div>
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-2" id="tests-dropdown-div"></div>
                        </div>
                    </div>


                    <div class="row" id="video-data-div" style="display: none;">
                        <div class="col-md-4">
                            <div class="mb-2">
                                <label class="form-label" for="video_name">Video Name</label>
                                <input type="text" class="form-control" name="video_name" id="video_name" onkeyup="toggleSubmitButton(this.value)">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-2">
                                <label class="form-label" for="activation_date">Activation Date</label>
                                <div class="input-append date activation_datetime">
                                    <input type="text" id="activation_date" name="activation_date" autocomplete="off">
                                    <span class="add-on"><i class="icon-th"></i></span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4" style="padding-right: 4px;">
                            <div class="mb-2">
                                <label for="expiry_date"> Expiry Date </label>
                                <div class="input-append date expiry_datetime">
                                    <input type="text" id="expiry_date" name="expiry_date" autocomplete="off">
                                    <span class="add-on"><i class="icon-th"></i></span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-8">
                            <div class="mb-2">
                                <label class="form-label" for="video_url">Upload Video File</label>
                                <input type="file" name="video_file" id="video_file" />
                            </div>
                        </div>



                        <div class="col-md-8">
                            OR
                            <div class="mb-2">
                                <label class="form-label" for="video_url">Video URL</label>
                                <input type="text" class="form-control" name="video_url" id="video_url">
                            </div>
                        </div>
                    </div>


                    <div id="doc-data-div" style="display: none;">
                        <div class="row file_structure_append_div" id="file_structure_div_0">
                            <div class="col-md-4">
                                <div class="mb-2">
                                    <label class="form-label" for="doc_name">Document Title</label>
                                    <input type="text" class="form-control" name="upload_doc_names[]" id="doc_name" onkeyup="toggleSubmitButton(this.value)">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label class="form-label" for="document">Upload PDF</label>
                                    <input type="file" name="upload_documents[]" id="document" />
                                </div>
                            </div>

                            <div class="col-md-2">
                                <span class="add_file_structure"><i class="fa fa-plus-circle" aria-hidden="true"></i></span>
                            </div>
                        </div>
                    </div>


                    <p id="error" style="color:red"></p>
                    <p id="vprogress" style="color: green"></p>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="institute_id" id="instituteId" value="<?= decrypt_cipher($instituteID); ?>" required />
                    <input type="hidden" name="redirect" value="<?= $redirect; ?>" required />
                    <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success" name="add_package_form_submit">Add</button>
                </div>
                <?php echo form_close(); ?>
            </div>

        </div>
    </div>

    <script src="<?php echo base_url('assets/js/manage_dlp.js'); ?>"></script>
    <script>
        $(".activation_datetime").datetimepicker({
            format: "yyyy-mm-dd hh:ii",
            autoclose: true,
            todayBtn: true,
            fontAwesome: 'font-awesome',
            pickerPosition: "bottom-left"
        });

        $(".expiry_datetime").datetimepicker({
            format: "yyyy-mm-dd hh:ii",
            autoclose: true,
            todayBtn: true,
            fontAwesome: 'font-awesome',
            pickerPosition: "bottom-left"
        });

        $('.classrooms-dropdown').select2({
            dropdownAutoWidth: true,
            width: '100%',
            dropdownParent: $("#addNewDlpData")
        });
        $('.subjects-dropdown').select2({
            dropdownAutoWidth: true,
            width: '100%',
            dropdownParent: $("#addNewDlpData")
        });
        $('.chapters-dropdown').select2({
            dropdownAutoWidth: true,
            width: '100%',
            dropdownParent: $("#addNewDlpData")
        });

        $('.course_tests_dropdown').select2({
            dropdownAutoWidth: true,
            width: '100%',
            dropdownParent: $("#addNewDlpData")
        });

        // Add Multiple file input with remove option

        $(document).ready(function() {
            // Add new file structure file_structure_append_div
            $(".add_file_structure").click(function() {
                // Finding total number of file_structure_append_divs added
                var total_file_structure_append_div = $(
                    ".file_structure_append_div"
                ).length;

                // last <div> with file_structure_append_div class id
                var lastid = $(".file_structure_append_div:last").attr("id");
                var split_id = lastid.split("_");
                var nextindex = Number(split_id[3]) + 1;

                var max = 10;
                // Check total number file_structure_append_divs
                if (total_file_structure_append_div < max) {
                    // Adding new div container after last occurance of file_structure_append_div class
                    $(".file_structure_append_div:last").after(
                        "<div class='file_structure_append_div' id='file_structure_div_" +
                        nextindex +
                        "'></div>"
                    );

                    // Adding file_structure_append_div to <div>
                    $("#file_structure_div_" + nextindex).append(
                        "<div class='file_structure_append_subdiv row'><div class='col-md-4'><input type='text' class='form-control' placeholder='Enter a file name' name='upload_doc_names[]' id='txt_" +
                        nextindex +
                        "'></div><div class='col-md-6'><input type='file'  name='upload_documents[]' id='txt_" +
                        nextindex +
                        "'></div><div class='col-md-2' onclick='remove_structure(" +
                        nextindex +
                        ")'><span class='action_button_plus_custom'><i id='remove_" +
                        nextindex +
                        "' class='fas fa-trash remove_file_structure'></i></span></div></div>"
                    );
                } else {
                    alert("Exceed max number of file structure elements.");
                }
            });

            // Remove file_structure_append_div
            $(".container").on("click", ".remove_file_structure", function() {
                var id = this.id;
                var split_id = id.split("_");
                var deleteindex = split_id[1];
                // Remove <div> with id
                $("#div_" + deleteindex).remove();
            });
        });

        // Remove file_structure_append_div
        function remove_structure(remove_id) {
            $("#file_structure_div_" + remove_id).remove();
        }
    </script>