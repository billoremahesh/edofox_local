    <!-- Update DLP Resource Modal -->
    <div id="update_dlp_resource_modal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><?= $title; ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <?php
                $video_name = $resource_details['video_name'];
                $video_url = $resource_details['video_url'];
                $activation_date = changeDateTimezone($resource_details['activation_date']);
                $expiry_date = changeDateTimezone($resource_details['expiry_date']);
                ?>
                <?php echo form_open_multipart('dlp/update_dlp_resource_submit'); ?>
                <div class="modal-body" id="updateResourceModalBody">
                    <div class="row">

                        <div class="col-xs-12">
                            <div class="mb-2">
                                <label for="resource_name">Title</label>
                                <input type="text" class="form-control" id="resource_name" name="resource_name" value="<?php echo $video_name; ?>" required>
                            </div>
                        </div>


                        <div class="col-md-12">
                            <div class="mb-2">
                                <label for="activation_date">Activation Date</label>
                                <div class="input-append date activation_datetime_update">
                                    <input type="text" id="activation_date" name="activation_date" value="<?php echo $activation_date; ?>" autocomplete="off">
                                    <span class="add-on"><i class="icon-th"></i></span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="mb-2">
                                <label for="expiry_date"> Expiry Date </label>
                                <div class="input-append date expiry_datetime">
                                    <input type="text" id="expiry_date" name="expiry_date" value="<?php echo $expiry_date; ?>" autocomplete="off">
                                    <span class="add-on"><i class="icon-th"></i></span>
                                </div>
                            </div>
                        </div>

                        <div class="col-xs-12">
                            <?php if ($resource_type === "VIDEO" && strpos($video_url, "vimeo")  == false) : ?>
                                <div class="mb-2">
                                    <label for="edit_video_url">Update URL</label>
                                    <input type="text" class="form-control" id="edit_video_url" name="video_url" value="<?php echo $video_url; ?>">
                                </div>
                            <?php else : ?>
                                <div class="mb-2">
                                    <label for="edit_video_url">Update URL</label>
                                    <input type="text" class="form-control" id="edit_video_url" name="video_url" value="<?php echo $video_url; ?>">
                                </div>
                            <?php endif; ?>



                            <?php if ($resource_type === "DOC") : ?>
                                <div class="mb-2">
                                    <label for="edit_document_file">Update PDF</label>
                                    <input type="file" class="form-control" name="document" id="edit_document_file" />
                                    <input type="hidden" name="document_url" value="<?php echo $video_url; ?>">
                                </div>
                            <?php endif; ?>

                        </div>

                        <div class="col-xs-12 col-md-12">
                            <div class="mb-2">
                                <label for="edit_chapter_id">Chapter</label>
                                <select class="chapters-dropdown" name="chapter_id" id="edit_chapter_id" required>
                                    <option value=""></option>

                                    <?php
                                    foreach ($dlp_resource_mapping_details as $row_resource_chapter_data) :
                                        $chapter_id = $row_resource_chapter_data['chapter_id'];
                                        $chapter_name = $row_resource_chapter_data['chapter_name'];

                                        $selected = "";
                                        if ($chapter_id == $edit_chapter_id) {
                                            $selected = "selected";
                                        }
                                        echo "<option value='$chapter_id' $selected>$chapter_name</option>";
                                    endforeach;
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <input type="hidden" name="resource_id" value="<?= $resource_id ?>" />
                    <input type="hidden" name="resource_mapping_id" value="<?= $resource_mapping_id ?>" />
                    <input type="hidden" name="institute_id" value="<?= $instituteID; ?>" required />
                    <input type="hidden" name="redirect" value="<?= $redirect; ?>" required />
                    <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success" name="add_package_form_submit">Update</button>
                </div>
                <?php echo form_close(); ?>
            </div>

        </div>
    </div>


    <script>
        $(".activation_datetime_update").datetimepicker({
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

        $('.chapters-dropdown').select2({
            dropdownAutoWidth: true,
            width: '100%',
            dropdownParent: $("#update_dlp_resource_modal")
        });
    </script>