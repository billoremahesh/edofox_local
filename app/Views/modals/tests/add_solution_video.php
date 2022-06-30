<!-- Add Solution Video Modal -->
<div id="add_solution_video_modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title"><?= $title; ?></h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <?php echo form_open_multipart('tests/add_solution_video_submit'); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-2">
                            <label>Video Name:</label>
                            <input type="text" class="form-control" name="video_name" required />
                        </div>
                        <div class="mb-2">
                            <label>Embed Video URL (Youtube/Vimeo):</label>
                            <input type="text" class="form-control" name="video_url" required />
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <input type="hidden" name="test_id" value="<?= $test_id; ?>" required />
                <input type="hidden" name="institute_id" value="<?= $instituteID; ?>" required />
                <input type="hidden" name="redirect" value="<?= $redirect; ?>" required />
                <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-success">Add</button>
            </div>
            <?php echo form_close(); ?>
        </div>

    </div>
</div>