<!-- Delete Solution Video Modal -->
<div id="delete_solution_video_modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title"><?= $title; ?></h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <?php echo form_open_multipart('tests/delete_solution_video_submit'); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <p>WARNING! Do you want to delete this video? This cannot be undone.</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <input type="hidden" name="video_id" value="<?= $video_id; ?>" required />
                <input type="hidden" name="institute_id" value="<?= $instituteID; ?>" required />
                <input type="hidden" name="redirect" value="<?= $redirect; ?>" required />
                <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-success">Delete</button>
            </div>
            <?php echo form_close(); ?>
        </div>

    </div>
</div>