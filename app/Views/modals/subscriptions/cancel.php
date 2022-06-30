<div class="modal fade" id="cancel_subscription_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"> <?= $title; ?> </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <?php echo form_open('Subscriptions/cancel_subscription_submit'); ?>
            <div class="modal-body">
                <p> Are you sure, you want to delete this <?= $subscription_data['plan_name']; ?> subscription?</p>
            </div>
            <div class="modal-footer">
                <input type="hidden" id="subscription_id" name="subscription_id" value="<?= $subscription_id; ?>" required />
                <input type="hidden" id="institute_id" name="institute_id" value="<?= $subscription_data['institute_id']; ?>" required />
                <input type="hidden" id="redirect" name="redirect" value="/subscriptions/overview/<?= encrypt_string($subscription_data['institute_id']); ?>" required />
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-danger"> Yes </button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>