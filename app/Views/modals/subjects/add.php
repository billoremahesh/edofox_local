<div id="add_subject_modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= $title; ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <?php echo form_open('subjects/add_subject_submit'); ?>
            <div class="modal-body">

                <div class="row">
                    <div class="col-md-12">
                        <label class="form_label" for="subject_name">Subject Name</label>
                        <input type="text" class="form-control" name="subject_name" id="subject_name" maxlength="120" required>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <input type="hidden" name="institute_id" value="<?= $instituteID; ?>" required />
                <input type="hidden" name="redirect" value="<?= $redirect; ?>" required />
                <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-success">Add</button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>