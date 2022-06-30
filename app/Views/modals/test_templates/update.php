<div class="modal fade" id="update_template_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"> <?= $title; ?> </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <?php echo form_open('/testTemplates/update_template_details_submit'); ?>
            <div class="modal-body">
                <div class="row">

                    <div class="col-12 mb-2">
                        <label class="form_label" for="template_name"> Template Name <span class="req_color">*</span></label>
                        <input type="text" class="form-control" name="template_name" id="template_name" minlength="2" maxlength="120" value="<?= $templates_details['template_name']; ?>" required>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <input type="hidden" name="template_id" value="<?= $template_id; ?>" required />
                <input type="hidden" name="redirect" value="<?= $redirect; ?>" required />
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary"> Update </button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>