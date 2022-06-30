<div class="modal fade" id="update_notice_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= $title; ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <?php echo form_open('notices/update_notice_submit'); ?>
            <div class="modal-body row g-3">

                <div class="col-md-12">
                    <label class="form_label" for="title"> Title </label>
                    <input type="text" class="form-control" name="title" id="title" value="<?php echo $notice_details['title']; ?>" required>
                </div>

                <div class="col-md-12">
                    <label class="form_label" for="description"> Description </label>
                    <input type="text" class="form-control" name="description" id="description" value="<?php echo $notice_details['description']; ?>" required>
                </div>
            </div>
            <div class="modal-footer">
                <input type="hidden" name="notice_id" value="<?= $notice_id; ?>" required />
                <input type="hidden" name="redirect" value="<?= $redirect; ?>" required />
                <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-success" name="update_package_submit">Update</button>
            </div>
            <?php echo form_close(); ?>
        </div>

    </div>
</div>