<!-- Add Student Classroom Modal -->
<div id="add_classroom" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title"><?= $title; ?></h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('Students/add_student_classroom_submit') ?>" method="post" id="add_student_form">
            <div class="modal-body">
                <div class="row">
                    <div class="mb-2">
                    <label class="form-label" for="add_package_id">Select Classroom: </label>
                    <select class="form-control" name="add_package_id" id="add_package_id" required>
                        <option value="">Select</option>
                        <?php if(!empty($classroom_list)):
                                foreach($classroom_list as $row):
                                $package_id = $row['id'];
                                $package_name = $row['package_name'];
                        ?>
                                <option value="<?php echo $package_id ?>"><?= $package_name; ?></option>
                        <?php
                                endforeach;
                              endif;
                        ?>
                    </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                 <input type="hidden" name="student_id" value="<?= $student_id; ?>" />
                 <input type="hidden" name="institute_id" value="<?= $institute_id; ?>" />
                <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-success" >Add</button>
            </div>
            </form>
        </div>
    </div>
</div>