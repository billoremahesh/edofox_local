<div class="modal fade" id="delete_template_rule_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"> <?= $title; ?> </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <?php echo form_open('/testTemplates/delete_template_rule_submit'); ?>
            <div class="modal-body">
                <p> Are you sure, you want to delete this <?= $templates_rule_details['display_name']; ?> template rule?</p>
            </div>
            <div class="modal-footer">
                <input type="hidden" name="template_rule_id" value="<?= $template_rule_id; ?>" required />
                <input type="hidden" name="template_id" value="<?= $template_id; ?>" required />
                <input type="hidden" name="is_disabled" value="1" required />
                <input type="hidden" name="redirect" value="<?= $redirect; ?>" required />
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-danger"> Yes </button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>