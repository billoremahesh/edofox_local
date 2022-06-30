    <!-- Add Classroom Modal -->
    <div id="add_classroom_modal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title"><?= $title; ?></h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <?php echo form_open('classrooms/add_classroom_submit'); ?>
                <div class="modal-body row g-3">
                    <div class="col-md-12">
                        <label class="form_label" for="package_name">Classroom Name</label>
                        <input type="text" class="form-control" name="package_name" id="package_name" required>
                    </div>
                    <div class="col-md-12">
                        <label class="form_label" for="package_type">Classroom type</label>
                        <select name="package_type" id="package_type" class="form-control">
                            <option value="">Regular</option>
                            <option value="DLP">Distance Learning Classroom</option>
                            <option value="Proctoring">Proctoring classroom</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form_label" for="package_price">Price</label>
                        <input type="text" class="form-control" name="package_price" id="package_price" required>
                    </div>
                    <div class="col-6">
                        <label class="form_label" for="package_offline_price">Offline Price</label>
                        <input type="text" class="form-control" name="package_offline_price" id="add_package_offline_price" required>
                    </div>
                    <div class="col-md-12">
                        <div class="form-check form-switch form-check-custom">
                            <input class="form-check-input cashbook_filter" type="checkbox" name="is_public_check" id="is_public_check">
                            <label class="form-check-label" for="is_public_check"> Is Public </label>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <input type="hidden" name="institute_id" value="<?= $instituteID; ?>" required />
                    <input type="hidden" name="redirect" value="<?= $redirect; ?>" required />
                    <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success" name="add_package_form_submit">Add</button>
                </div>
                <?php echo form_close(); ?>
            </div>

        </div>
    </div>