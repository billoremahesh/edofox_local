<div class="modal fade" id="add_dlp_subject_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title"><?= $title; ?></h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <?php echo form_open('dlp/add_dlp_subject_submit'); ?>
            <div class="modal-body">
                <div class="mb-2">
                    <select class="subjects-dropdown" name="dlp_subjects[]" id="dlp_subjects" multiple required>
                        <option value=""></option>

                        <?php
                        foreach ($dlp_not_mapped_subjects as $row) :
                            $subject_id = $row['subject_id'];
                            $subject_name = $row['subject'];
                            echo "<option value='$subject_id'>$subject_name</option>";
                        endforeach;
                        ?>

                    </select>
                </div>
                <div class="text-center">
                    <button class="btn btn-secondary" data-bs-toggle="modal" data-bs-dismiss="modal" data-bs-target="#addNewSubjectsBulk">Do you want to add a new subject?</button>
                </div>
            </div>
            <div class="modal-footer">
                <input type="hidden" name="institute_id" value="<?= $instituteID; ?>" required />
                <input type="hidden" name="classroom_id" value="<?= $classroom_id; ?>" required />
                <input type="hidden" name="redirect" value="<?= $redirect; ?>" required />
                <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-success" name="add_package_form_submit">Add</button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

    <!-- Modal to add new subjects in bulk in the institute -->
    <div class="modal fade" id="addNewSubjectsBulk" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title">Add New Subjects</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <?php echo form_open('dlp/add_new_subjects_bulk_submit'); ?>
                    <div class="modal-body">

                        <input type="hidden" id="institute_id" name="institute_id" value="<?= $instituteID ?>" />
                        <input type="hidden" name="redirect" value="<?= $redirect; ?>" required />

                        <div class="form-group">
                            <label for="subject_names">Add comma separated Subject names</label>
                            <textarea class="form-control" name="subject_names" id="subject_names" placeholder="English, Sanskrit, Geography" required></textarea>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" name="add_new_subjects_submit">Add Subjects</button>
                    </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>

<script>
    // bootstrap 5 select2 not working in modal bug
    // https://stackoverflow.com/questions/18487056/select2-doesnt-work-when-embedded-in-a-bootstrap-modal/33884094#33884094
    // Initializing select2
    $('#dlp_subjects').select2({
        width: "100%",
        dropdownParent: $("#add_dlp_subject_modal")
    });
</script>