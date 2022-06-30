<div class="modal fade" id="delete_test_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"> <?= $title; ?> </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <?php echo form_open('tests/delete_test_submit'); ?>
            <div class="modal-body">
                <p> Are you sure, you want to delete this test?</p>
            </div>
            <div class="modal-footer">
                <input type="hidden" name="test_id" value="<?= $test_id; ?>" required />
                <input type="hidden" name="redirect" value="<?= $redirect; ?>" required />
                <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary"> Yes </button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>