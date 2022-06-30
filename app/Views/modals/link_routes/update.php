<div class="modal fade" id="update_route_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"> <?= $title; ?> </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <?php echo form_open('/linkRoutes/update_route_submit'); ?>
            <div class="modal-body">
                <div class="row">

                    <div class="col-12 mb-2">
                        <label class="form_label" for="route_name"> Route Name <span class="req_color">*</span></label>
                        <input type="text" class="form-control" name="route_name" id="route_name" minlength="2" maxlength="200" value="<?= $route_details['route_name']; ?>" required>
                    </div>

                    <div class="col-12 mb-2">
                        <label class="form_label" for="route"> Route Link <span class="req_color">*</span></label>
                        <input type="text" class="form-control" name="route" id="route" minlength="2" maxlength="200" value="<?= $route_details['route']; ?>" required>
                    </div>


                    <div class="col-12 mb-2">
                        <label class="form_label" for="tags"> Tags <span class="req_color"> * Add comma seperated values </span></label>
                        <input type="text" class="form-control" name="tags" id="tags" minlength="2" maxlength="200" value="<?= $route_details['tags']; ?>" required>
                    </div>

                    <div class="col-12 mb-2">
                        <label class="form_label" for="shortcut_key"> Shortcut Key </label>
                        <input type="text" class="form-control" name="shortcut_key" id="shortcut_key" minlength="2" maxlength="40" value="<?= $route_details['shortcut_key']; ?>">
                    </div>

                    <div class="col-12 mb-2">
                        <label class="form_label" for="perm_key"> Permision Key </label>
                        <input type="text" class="form-control" name="perm_key" id="perm_key" minlength="2" maxlength="40" value="<?= $route_details['perm_key']; ?>">
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <input type="hidden" name="route_id" value="<?= $route_id; ?>" required />
                <input type="hidden" name="redirect" value="<?= $redirect; ?>" required />
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-success"> Update </button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>