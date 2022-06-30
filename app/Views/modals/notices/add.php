    <!-- Add Notice Modal -->
    <div id="add_notice_modal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title"><?= $title; ?></h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <?php echo form_open('notices/add_notice_submit'); ?>
                <div class="modal-body row g-3">
                    <div class="col-md-12">
                        <label class="form_label" for="title"> Title </label>
                        <input type="text" class="form-control" name="title" id="title" required>
                    </div>
                    <div class="col-md-12">
                        <label class="form_label" for="description"> Description </label>
                        <input type="text" class="form-control" name="description" id="description" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="redirect" value="<?= $redirect; ?>" required />
                    <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success" name="add_package_form_submit">Add</button>
                </div>
                <?php echo form_close(); ?>
            </div>

        </div>
    </div>