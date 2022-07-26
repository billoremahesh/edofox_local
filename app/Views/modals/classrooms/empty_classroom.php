<div class="modal fade" id="empty_classroom_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"> <?= $title; ?> </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <?php echo form_open('classrooms/empty_classroom_submit'); ?>
            <div class="modal-body">
                <p> Are you sure, you want to empty this <b><?= $classroom_details['package_name'];?></b> classroom? All the students from the classroom will be deleted and it cannot be undone.</p>

            </div>
            <div class="modal-footer">
                <input type="hidden" name="classroom_id" value="<?= $classroom_id; ?>" required />
                <input type="hidden" name="redirect" value="<?= $redirect; ?>" required />
                <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-danger"> Yes </button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>