    <!-- Delete Session Schedule Modal -->
    <div id="delete_holiday_modal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title"><?= $title; ?></h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <?php echo form_open('schedule/delete_holiday_submit'); ?>
                <div class="modal-body">
                    <div class="row g-3"> 
                        <div class="col-12">
                            Are you sure, you want to delete this <?= $holiday_details['title']; ?> holiday.
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="schedule_id" value="<?= $holiday_id; ?>" required />
                    <input type="hidden" name="is_disabled" value="1" required /> 
                    <input type="hidden" name="redirect" value="<?= $redirect; ?>" required />
                    <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Yes</button>
                </div>
                <?php echo form_close(); ?>
            </div>

        </div>
    </div>