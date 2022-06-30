<div class="modal fade" id="delete_staff_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"> <?= $title; ?> </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <?php echo form_open('staff/delete_staff_submit'); ?>
            <div class="modal-body">
                <p> Are you sure, you want to delete this <?= $staff_details['name']; ?> staff?</p>
            </div>
            <div class="modal-footer">
                <input type="hidden" name="staff_id" value="<?= $staff_id; ?>" required />
                <input type="hidden" name="redirect" value="<?= $redirect; ?>" required />
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-danger"> Yes </button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>