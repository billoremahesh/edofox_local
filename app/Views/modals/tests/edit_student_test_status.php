	<!-- Modal -->
	<div class="modal fade" id="edit_student_test_status" tabindex="-1" role="dialog" aria-labelledby="edit-student-test-statu-label">
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
				<?php echo form_open_multipart(base_url('tests/student_test_status_submit'), $attributes); ?>
				<?php

				$test_status = $realtime_student_details['status'];
				$time_left = $realtime_student_details['time_left'];
				$exam_started_count = $realtime_student_details['exam_started_count'];
				?>

				<div class="modal-body">
					<div class="row">
						<div class="col-md-6">
							<div class="mb-2">
								<label class="form-label" for="test_status">Student test status*</label>
								<select class="form-control" name="test_status_value" id="test_status" required>
									<option value=""></option>
									<option value="COMPLETED" <?php if ($test_status == "COMPLETED") echo "selected"; ?>>COMPLETED</option>
									<option value="STARTED" <?php if ($test_status == "STARTED") echo "selected"; ?>>STARTED</option>
								</select>
							</div>
						</div>

						<div class="col-md-6">
							<div class="mb-2">
								<label class="form-label" for="test_status">Student time left in seconds </label>
								<input text="text" class="form-control" name="time_left_value" value="<?= $time_left; ?>" pattern="[0-9]+" maxlength="5" title="Enter only number. No text allowed" />
							</div>
						</div>
					</div>


					<div class="row">
						<div class="col-md-6">
							<div class="mb-2">
								<label class="form-label" for="exam_started_count">Test Starts count?</label>
								<input text="text" class="form-control" name="exam_started_count" id="exam_started_count" value="<?= $exam_started_count; ?>" pattern="[0-9]+" maxlength="2" title="Enter only number. No text allowed" />
							</div>
						</div>
					</div>
				</div>

				<div class="modal-footer">
					<input type="hidden" name="test_id" id="test_id" value="<?= $test_id; ?>" required>
					<input type="hidden" name="student_id" value="<?= $student_id; ?>" required />
					<input type="hidden" name="redirect" value="<?= $redirect; ?>" required />
					<button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Cancel</button>
					<button type="submit" class="btn btn-success" name="add_package_form_submit">Update</button>
				</div>
				<?php echo form_close(); ?>
			</div>
		</div>
	</div>