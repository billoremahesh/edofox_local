	<!-- Delete student token modal -->
	<div id="delete_student_token_modal" class="modal fade" role="dialog">
	    <div class="modal-dialog">
	        <!-- Modal content-->
	        <div class="modal-content">
	            <div class="modal-header">
	                <h6 class="modal-title"><?= $title; ?></h6>
	                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
	            </div>
	            <?php echo form_open('students/delete_student_token_submit'); ?>
	            <div class="modal-body">
	                <p>Are you sure you want to delete this student token?</p>
	            </div>
	            <div class="modal-footer">
	                <input type="hidden" name="token" value="<?= $token; ?>" required />
	                <input type="hidden" name="redirect" value="<?= $redirect; ?>" required />
	                <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Cancel</button>
	                <button type="submit" class="btn btn-danger"> Delete </button>
	            </div>
	            <?php echo form_close(); ?>
	        </div>
	    </div>
	</div>