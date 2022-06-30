    <div class="modal fade" id="update_classroom_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title"><?= $title; ?></h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <?php echo form_open('classrooms/update_classroom_submit'); ?>
                <div class="modal-body row g-3">

                    <div class="col-12">
                        <label class="form_label" for="update_package_name">Classroom Name</label>
                        <input type="text" class="form-control" name="package_name" id="update_package_name" value="<?php echo $classroom_details['package_name']; ?>" required>
                    </div>
                    <div class="col-12">
                        <label class="form_label" for="update_package_type">Classroom type</label>
                        <select name="package_type" id="update_package_type" class="form-control">
                            <option value="">Regular</option>
                            <option value="DLP" <?php echo $classroom_details['type'] == "DLP" ? "selected" : "" ?>>Distance Learning Classroom</option>
                            <option value="Proctoring" <?php echo $classroom_details['type'] == "Proctoring" ? "selected" : "" ?>>Proctoring classroom</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form_label" for="update_package_price">Price</label>
                        <input type="text" class="form-control" name="package_price" id="update_package_price" value="<?php echo $classroom_details['price']; ?>" required>
                    </div>

                    <div class="col-6">
                        <label class="form_label" for="update_package_offline_price">Offline Price</label>
                        <input type="text" class="form-control" name="package_offline_price" id="update_package_offline_price" value="<?php echo $classroom_details['offline_price']; ?>" required>
                    </div>
                    <div class="col-12">
                        <div class="form-check form-switch form-check-custom">
                            <input class="form-check-input cashbook_filter" type="checkbox" name="is_public_check" id="is_public_check" value="<?php echo $classroom_details['is_public']; ?>" <?php if ($classroom_details['is_public'] == 1) {
                                                                                                                                                                                                    echo "checked";
                                                                                                                                                                                                } ?>>
                            <label class="form-check-label" for="is_public_check"> Is Public </label>
                        </div>
                    </div>


                </div>
                <div class="modal-footer">
                    <input type="hidden" name="classroom_id" value="<?= $classroom_id; ?>" required />
                    <input type="hidden" name="redirect" value="<?= $redirect; ?>" required />
                    <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success" name="update_package_submit">Update</button>
                </div>
                <?php echo form_close(); ?>
            </div>

        </div>
    </div>