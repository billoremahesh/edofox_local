	<!-- Modal -->
	<div class="modal fade" id="reset_student_exam_session" tabindex="-1" role="dialog" aria-labelledby="edit-student-test-statu-label">
		<div class="modal-dialog" role="document">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header">
					<h6 class="modal-title"><?= $title; ?></h6>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>

				<?php
				$attributes = ['class' => 'cmxform', 'id' => 'myform'];
				?>
				<?php echo form_open_multipart(base_url('tests/reset_student_exam_session_submit'), $attributes); ?>

				<div class="modal-body">
					<div class="row">
						<p> This will reset the student exam session which will allow blocked students to login to the exam. Are you sure? </p>
					</div>
				</div>

				<div class="modal-footer">
					<input type="hidden" name="test_id" id="test_id" value="<?= $test_id; ?>" required>
					<input type="hidden" name="student_id" value="<?= $student_id; ?>" required />
					<input type="hidden" name="redirect" value="<?= $redirect; ?>" required />
					<button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Cancel</button>
					<button type="submit" class="btn btn-success" name="add_package_form_submit">Yes</button>
				</div>

				<?php echo form_close(); ?>
			</div>
		</div>
	</div>