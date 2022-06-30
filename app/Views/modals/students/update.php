<!-- Update Student Details Modal -->
<div id="update_student_details_modal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-lg">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<h6 class="modal-title"><?= $title; ?></h6>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<?php
			$attributes = ['name' => 'update_student_form', 'class' => 'update_student_form', 'id' => 'update_student_form'];
			?>
			<?php echo form_open('students/update_student_details_submit', $attributes); ?>
			<div class="modal-body">

				<div class="row">
					<div class="col-md-6 mb-2">
						<label class="form-label" for="name">Name*</label>
						<input type="text" class="form-control" id="name" name="name" value="<?php echo $student_details['name']; ?>" pattern="[a-zA-Z ]+" title="Please enter alphabets with space only" required>
					</div>


					<div class="col-md-6 mb-2">
						<label class="form-label" for="mobile">Mobile Number*</label>
						<input type="text" class="form-control" id="mobile_no" name="mobile_no" value="<?php echo $student_details['mobile_no']; ?>" maxlength="10" size="10" pattern="\d{10}" title="Enter 10 digit mobile number" required>
					</div>

					<div class="col-md-6 mb-2">
						<label class="form-label" for="roll_no">Institute's Roll No</label>
						<input type="text" class="form-control" id="roll_no" name="roll_no" value="<?php echo $student_details['roll_no']; ?>" maxlength="15" title="Enter roll number">
					</div>

					<div class="col-md-6 mb-2">
						<label class="form-label" for="gender">Gender*</label>
						<select class="form-control" name="gender" id="gender" required>
							<option value=""></option>
							<option value="Male" <?php if ($student_details['gender'] == "Male") echo "selected"; ?>>Male</option>
							<option value="Female" <?php if ($student_details['gender'] == "Female") echo "selected"; ?>>Female</option>
						</select>
					</div>

					<div class="col-md-6 mb-2">
						<div class="mb-2">
							<label class="form-label" for="email">Email </label>
							<input type="email" class="form-control" id="email" name="email" value="<?php echo $student_details['email']; ?>">
						</div>
					</div>

					<div class="col-md-6 mb-2">
						<label class="form-label" for="parent_mobile_no">Parent Mobile Number </label>
						<input type="tel" class="form-control" id="parent_mobile_no" name="parent_mobile_no" value="<?php echo $student_details['parent_mobile_no']; ?>" pattern="[1-9]{1}[0-9]{9}" title="Enter 10 Digit Mobile No." maxlength="10">
					</div>


					<div class="col-md-6 mb-2">
						<label class="form-label" for="previous_marks">Previous Marks</label>
						<input type="text" class="form-control" id="previous_marks" name="previous_marks" value="<?= $student_details['previous_marks']; ?>" maxlength="6" title="Enter Previous Marks">
					</div>

					<div class="col-md-6 mb-2">
						<label class="form-label" for="caste_category">Category</label>
						<select class="form-control" name="caste_category" id="caste_category">
							<option value=""></option>
							<?php
							$category_array = array('OPEN', 'SC', 'ST', 'EWS', 'SBC', 'OBC', 'NTA-VJ', 'NTB-NT1', 'NTC-NT2', 'NTD-NT3');
							sort($category_array);
							?>

							<?php
							foreach ($category_array as $category) :
							?>
								<option value="<?= $category; ?>" <?= ($category == $student_details['caste_category']) ? 'selected' : ''; ?>><?= $category; ?></option>
							<?php endforeach; ?>
						</select>
					</div>

					<hr />

					<div class="row mb-2 extra_details_structure_append_div" id="extra_details_structure_div_0">
						<div class="col-10 mb-2">
							<h6> Extra Details</h6>
						</div>
						<div class="col-2 mb-2 text-right">
							<span class="add_extra_details_structure">
								<i class="fa fa-plus-circle" aria-hidden="true"></i>
							</span>
						</div>
					</div>

					<?php
					// Extra/Additional Details
					$additional_details_arr = explode(" | ", $student_details['extra_details']);

					if (!empty($additional_details_arr)) :
						$key = 1;
						foreach ($additional_details_arr as $additional_detail) :
							if (!empty($additional_detail) && $additional_detail != "") :
								$extra_details_arr =  explode(":", $additional_detail);


					?>

								<div class='extra_details_append_subdiv row mb-2'>
									<div class="row" id="extra_details_structure_div_<?= $key; ?>">
										<div class='col-md-4'>
											<input type='text' class='form-control extra_details_keys' max-length='40' name='extra_details_keys[]' pattern='[a-zA-Z ]+' value="<?= str_replace("|", "", $extra_details_arr[0]); ?>" id="key_<?= $key; ?>" readonly>
										</div>
										<div class='col-md-6'>
											<input type='text' class='form-control extra_details_value' pattern='[a-zA-Z ]+' name='extra_details_val[]' max-length='40' value="<?= str_replace("|", "", $extra_details_arr[1]); ?>" id="val_<?= $key; ?>">
										</div>
										<div class='col-md-2' onclick='remove_extra_structure_div(<?= $key; ?>)'>
											<span class='remove_ed_icon'>
												<i class='fas fa-trash'></i>
											</span>
										</div>
									</div>

								</div>
					<?php
								$key++;
							endif;
						endforeach;
					endif;
					?>


				</div>
			</div>
			<div class="modal-footer">
				<input type="hidden" name="student_id" value="<?= $student_id; ?>" required />
				<input type="hidden" name="redirect" value="<?= $redirect; ?>" required />
				<button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Cancel</button>
				<button type="submit" id="update_btn" class="btn btn-primary" name="update_btn">Update</button>
			</div>
			<?php echo form_close(); ?>
		</div>
	</div>
</div>

<script>
	// Add Multiple Extra Details Structure
	$(document).ready(function() {
		// Add Extra Details Structure
		$(".add_extra_details_structure").click(function() {
			// Finding total number of extra details divs
			var total_extra_details_append_divs = $(
				".extra_details_structure_append_div"
			).length;

			// last <div> with extra_details_structure_append_div class id
			var lastid = $(".extra_details_structure_append_div:last").attr("id");
			var split_id = lastid.split("_");
			var nextindex = Number(split_id[4]) + 1;

			var max = 15;
			// Check total number extra_details_structure_append_div
			if (total_extra_details_append_divs < max) {
				// Adding new div container after last occurance of extra_details_structure_append_div class
				$(".extra_details_structure_append_div:last").after(
					"<div class='extra_details_structure_append_div' id='extra_details_structure_div_" +
					nextindex +
					"'></div>"
				);

				$("#extra_details_structure_div_" + nextindex).append(
					"<div class='extra_details_append_subdiv row mb-2'><div class='col-md-4'><input type='text' class='form-control   extra_details_keys' max-length='40' name='extra_details_keys[]' pattern='[a-zA-Z ]+' value='' placeholder='Enter a label' id='key_" +
					nextindex +
					"'></div><div class='col-md-6'><input type='text' class='form-control extra_details_value' pattern='[a-zA-Z ]+' name='extra_details_val[]' value='' max-length='40' placeholder='Enter a value'  id='val_" +
					nextindex +
					"'></div><div class='col-md-2' onclick='remove_extra_structure_div(" +
					nextindex +
					")'><span class='remove_ed_icon'><i class='fas fa-trash'></i></span></div></div>"
				);
			} else {
				alert("Exceed max number of file structure elements.");
			}
		});


	});

	function remove_extra_structure_div(remove_id) {
		$("#extra_details_structure_div_" + remove_id).remove();
	}
</script>