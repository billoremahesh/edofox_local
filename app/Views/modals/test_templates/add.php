<div class="modal fade" id="add_template_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"> <?= $title; ?> </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <?php echo form_open('/testTemplates/add_template_details_submit'); ?>
            <div class="modal-body">
                <div class="row">

                    <div class="col-12 mb-2">
                        <label class="form_label mb-2" for="template_name"> Template Name <span class="req_color">*</span></label>
                        <input class="form-control" name="template_name" id="template_div_input" placeholder="Template Name" minlength="1" maxlength="120" required />
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <input type="hidden" name="test_id" value="<?= $test_id; ?>" required />
                <input type="hidden" name="institute_id" value="<?= $institute_id; ?>" required />
                <input type="hidden" name="redirect" value="<?= $redirect; ?>" required />
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary"> Add </button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>