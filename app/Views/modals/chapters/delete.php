<div id="delete_chapter_modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= $title; ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <?php echo form_open('chapters/delete_chapter_submit'); ?>
            <div class="modal-body">

                <div class="row">
                    <div class="col-md-12">
                        <p> Are you sure, you want to delete this <?= $chapter_details['chapter_name']; ?> chapter, this action can not be undone?</p>
                    </div>

                </div>
                <div class="modal-footer">
                    <input type="hidden" name="chapter_id" value="<?= $chapter_id; ?>" required />
                    <input type="hidden" name="redirect" value="<?= $redirect; ?>" required />
                    <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Yes</button>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>