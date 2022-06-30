<!-- Delete student classroom modal -->
<div id="delete_student_classroom" class="modal fade" role="dialog">
	<div class="modal-dialog">
        <!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header">
					<h6 class="modal-title"><?= $title; ?></h6>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<?php echo form_open('students/delete_student_classroom_submit'); ?>
				<div class="modal-body">
					<p>Are you sure you want to delete this classroom ?</b> This action cannot be undone.</p>
				</div>
				<div class="modal-footer">
                    <input type="hidden" name="student_id" value="<?= $student_id; ?>" required />
					<input type="hidden" name="stu_pkg_id" value="<?= $stu_pkg_id; ?>" required />
					<input type="hidden" name="institute_id" value="<?= $institute_id; ?>" required />
					<button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Cancel</button>
					<button type="submit" class="btn btn-danger"> Delete </button>
				</div>
				<?php echo form_close(); ?>
			</div>
    </div>
</div>