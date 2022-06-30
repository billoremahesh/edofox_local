<div id="update_chapter_modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= $title; ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <?php echo form_open('chapters/update_chapter_submit'); ?>
            <div class="modal-body">

                <div class="row">
                    <div class="col-md-12">
                        <label class="form_label" for="subject_id">Subject</label>
                        <select class="subject_dropdown" name="subject_id" id="subject_id" required>
                            <option value=""></option>
                            <?php
                            if (!empty($subjects_list)) :
                                foreach ($subjects_list as $row) :
                                    $subject_id = $row['subject_id'];
                                    $subject_name = $row['subject'];
                                    echo "<option value='$subject_id'>$subject_name</option>";
                                endforeach;
                            endif;
                            ?>
                        </select>
                    </div>

                    <div class="col-md-12">
                        <label class="form_label" for="chapter_name">Chapter Name</label>
                        <input type="text" class="form-control" name="chapter_name" id="chapter_name" value="<?= $chapter_details['chapter_name']; ?>" maxlength="120" required>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <input type="hidden" name="chapter_id" value="<?= $chapter_id; ?>" required />
                <input type="hidden" name="redirect" value="<?= $redirect; ?>" required />
                <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-success">Update</button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>



<script>
    // bootstrap 5 select2 not working in modal bug
    // https://stackoverflow.com/questions/18487056/select2-doesnt-work-when-embedded-in-a-bootstrap-modal/33884094#33884094
    // Initializing select2
    $('.subject_dropdown').select2({
        width: "100%",
        dropdownParent: $("#update_chapter_modal")
    });
</script>