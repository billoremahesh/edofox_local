<div class="modal fade" id="delete_syllabus_topic_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"> <?= $title; ?> </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <?php echo form_open('syllabus/delete_syllabus_topic_submit'); ?>
            <div class="modal-body">
                <p> Are you sure, you want to delete this <b><?= $syllabus_details['syllabus_name'];?></b> syllabus of topics?</p>
            </div>
            <div class="modal-footer">
                <input type="hidden" name="syllabus_id" value="<?= $syllabus_id; ?>" required />
                <input type="hidden" name="redirect" value="<?= $redirect; ?>" required />
                <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-danger"> Yes </button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>