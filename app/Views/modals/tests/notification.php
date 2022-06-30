<div id="sendNotificationModal" class="modal fade" role="dialog">
		<div class="modal-dialog">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-bs-dismiss="modal">&times;</button>
					<h6 class="modal-title">Send SMS/Email notification</h6>
				</div>

				<div class="modal-body">

					<div class="mb-2">
						<label class="form-label" for="student_import_package">Classroom *</label>
						<!--                     <input type="text" class="form-control" name="add_test_ui" id="add_test_ui"> -->
						<select class="form-control" name="notification_package" id="notification_package" required>
							<option value="">All students</option>
							<?php

							foreach ($all_classrooms_array as $row) {
								$package_id = $row['id'];
								$package_name = $row['package_name'];

								echo "<option value='$package_id'>$package_name</option>";
							}
							?>
						</select>
					</div>

					<div class="mb-2">
						<label class="form-label" for="notification_type">Notification type *</label>
						<!--                     <input type="text" class="form-control" name="add_test_ui" id="add_test_ui"> -->
						<select class="form-control" name="notification_type" id="notification_type" required>
							<option value="InviteActivate">Student Invitation with Activation Link</option>
							<option value="StudentInvite">Student invitation with username and password</option>
						</select>
					</div>

					<div class="mb-2" id="invite_pwd_div" style="display: none;">
						<label class="form-label" for="password">Password is </label>
						<input type="text" class="form-control" maxlength="15" id="mail_password" placeholder="Password pattern e.g. 123456 or your mobile number etc">
					</div>

					<div class="mb-2" id="invite_exam_starts_div" style="display: none;">
						<label class="form-label" for="notification_type">Exam starts at</label>
						<!--                     <input type="text" class="form-control" name="add_test_ui" id="add_test_ui"> -->
						<select class="form-control" name="exam_starts" id="additional_msg">
							<option value="Exam will start at Today ">Today</option>
							<option value="Exam will start at Tomorrow">Tomorrow</option>
							<option value="Exam will start at NextWeek">Next Week</option>
							<option value="Exam will start at Later">Later</option>
						</select>
					</div>

					<p id="sendProgress" class="text-danger"></p>

				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
					<button type="submit" class="btn btn-primary" name="import_excel" onclick="sendNotification()">Send</button>
				</div>

			</div>
		</div>
	</div>