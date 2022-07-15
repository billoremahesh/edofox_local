<!-- Delete Bulk Classroom Students Modal -->
<div id="delete_bulk_classroom_students_modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title"><?= $title; ?></h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <?php echo form_open('classrooms/delete_classroom_students_submit'); ?>
            <div class="modal-body">


                <p>
                    Are you sure, you want to delete selected students from classroom?
                </p>

            </div>
            <div class="modal-footer">
                <input type="hidden" name="delete_student_ids" id="delete_student_ids" required>
                <input type="hidden" name="classroom_id" value="<?= $classroom_id; ?>" required />
                <input type="hidden" name="redirect" value="<?= $redirect; ?>" required />
                <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-danger"> Yes </button>
            </div>
            <?php echo form_close(); ?>
        </div>

    </div>
</div>