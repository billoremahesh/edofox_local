<!-- Migrate Bulk Classroom Students Modal -->
<div id="migrate_bulk_classroom_students_modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title"><?= $title; ?></h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <?php echo form_open('classrooms/migrate_classroom_students_submit'); ?>
            <div class="modal-body">

                <div class="mb-2">
                    <label class="form-label" for="new_package_id">Select Classroom: </label>
                    <select class="form-control new_package_id" name="new_package_id" id="new_package_id" required>
                        <option value=""></option>
                        <?php
                        if ($classroom_list) :
                            foreach ($classroom_list as $package) :
                        ?>
                                <option value="<?= encrypt_string($package['id']); ?>"><?= $package['package_name']; ?></option>
                        <?php
                            endforeach;
                        endif;

                        ?>

                    </select>
                </div>

                <div class="checkbox mt-4">
                    <label><input name="copy_bulk_students_check" type="checkbox" value="1"> Don't remove students from this classroom </label>
                </div>

            </div>
            <div class="modal-footer">
                <input type="hidden" name="migrate_student_ids" id="migrate_student_ids" required>
                <input type="hidden" name="classroom_id" value="<?= $classroom_id; ?>" required />
                <input type="hidden" name="institute_id" value="<?= $institute_id; ?>" required />
                <input type="hidden" name="redirect" value="<?= $redirect; ?>" required />
                <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-success"> Migrate </button>
            </div>
            <?php echo form_close(); ?>
        </div>

    </div>
</div>

<script>
    $('.new_package_id').select2({
        dropdownAutoWidth: true,
        width: '100%',
        dropdownParent: $("#migrate_bulk_classroom_students_modal")
    });
</script>