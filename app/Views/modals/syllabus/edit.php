<style>
    .ms-options-wrap button{
        overflow: hidden;
    }
    
</style>
<div class="modal fade" id="update_syllabus_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <?php $selected_class=[];  foreach($syllabus_classroom as $sy_val){ $selected_class[]= $sy_val['classroom_id']; } ?>
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title"><?= $title; ?></h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <?php echo form_open('syllabus/update_syllabus_submit'); ?>
                <div class="modal-body row g-3">   
                <div class="col-md-12">
                        <label class="form_label" for="subject_name">Subject Name</label> 
                        <select name="subject_name" id="subject_name" class="form-control">
                            <option value="">Select Subject</option> 
                            <?php foreach($subjectlist as $value){    ?> 
                                 <option value="<?= $value['subject_id'] ?>" id="tree1" <?php echo $syllabus_details['subject_id'] == $value['subject_id']  ? "selected" : "" ?> ><?= $value['subject'] ?></option>
                                <?php } ?>
                        </select> 
                     </div>
                    <div class="col-md-12">
                        <label class="form_label" for="syllabus_name">Syllabus Name</label>
                        <input type="text" class="form-control" name="syllabus_name" id="syllabus_name" maxlength="50" value="<?= $syllabus_details['syllabus_name'] ?>" required>
                   
                    </div>
                    <div class="col-md-12">
                        <label class="form_label" for="session_classroom">Applicable to classrooms</label>
                        <select name="session_classroom[]" class="form-control" multiple id="session_classroom" style="broder:1px solid #ced4da;" >
                        <?php
                            if (!empty($classroom_list)) {
                                foreach ($classroom_list as $row) {
                                    $package_id = $row['id'];
                                    $package_name = $row['package_name'];
                            ?>
                                    <option value="<?= $package_id; ?>" > <?= $package_name; ?> </option>
                            <?php 
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-12">
                        <label class="form_label" for="description">Description</label>
                        <textarea type="text" class="form-control" rows="4" name="description" id="description" required><?= $syllabus_details['description'] ?></textarea>
                    </div>
                    


                </div>
                <div class="modal-footer">
                    <input type="hidden" name="syllabus_id" value="<?= $classroom_id; ?>" required />
                    <input type="hidden" name="redirect" value="<?= $redirect; ?>" required />
                    <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success" name="update_package_submit">Update</button>
                </div>
                <?php echo form_close(); ?>
            </div>

        </div>
    </div>

    
<script>

    var selectedClassroom =<?php echo json_encode($selected_class);?>; 
       $("#session_classroom").val(selectedClassroom);

      $('#session_classroom').multiselect({ 
        columns: 1,
        placeholder: 'Select Classroom',
        search: true,
        selectAll: true, 

    });
  
</script>