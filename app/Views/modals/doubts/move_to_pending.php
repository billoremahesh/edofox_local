<div id="move_to_pending_doubt_modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title"><?= $title; ?></h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <?php
            $attributes = ['class' => 'cmxform', 'id' => 'myform'];
            ?>
            <?= form_open_multipart('doubts/move_doubt_to_pending_submit', $attributes); ?>
            <div class="modal-body">
                <div class="mb-2">
                    <p>Are you sure you want to move this doubt question back to Pending?</p>
                    <p>You will have to resolve the question again!</p>
                </div>
            </div>
            <div class="modal-footer">
                <input type="hidden" name="doubt_question_id" value="<?= $doubt_id; ?>" required />
                <input type="hidden" name="institute_id" value="<?= $instituteID; ?>" required />
                <input type="hidden" name="redirect" value="<?= $redirect; ?>" required />
                <input type="hidden" name="doubt_question_type" value="<?= $type; ?>" required />
                <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-secondary" name="move_to_pending_submit">Move to Pending</button>
            </div>
            <?php echo form_close(); ?>
        </div>

    </div>
</div>