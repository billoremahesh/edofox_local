	<!-- Disable student modal -->
	<div id="disable_student_modal" class="modal fade" role="dialog">
	    <div class="modal-dialog">
	        <!-- Modal content-->
	        <div class="modal-content">
	            <div class="modal-header">
	                <h6 class="modal-title"><?= $title; ?></h6>
	                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
	            </div>
	            <?php echo form_open('students/disable_student_submit'); ?>
	            <div class="modal-body">
	                <p>Are you sure you want to <?= $block_type; ?> this <b><?= $student_details['name']; ?></b> student data.</p>
	            </div>
	            <div class="modal-footer">
	                <input type="hidden" name="student_id" value="<?= $student_id; ?>" required />
                    <input type="hidden" name="block_type" value="<?= $block_type; ?>" required />
	                <input type="hidden" name="redirect" value="<?= $redirect; ?>" required />
	                <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Cancel</button>
	                <button type="submit" class="btn btn-secondary"> <?= ucfirst($block_type); ?> </button>
	            </div>
	            <?php echo form_close(); ?>
	        </div>
	    </div>
	</div>