    <!-- Add Classroom Modal -->
    <div id="add_syllabus_modal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title"><?= $title; ?></h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <?php echo form_open('syllabus/add_syllabus_submit'); ?>
                <div class="modal-body row g-3">
                    <div class="col-md-12">
                        <label class="form_label" for="subject_name">Subject Name</label> 
                        <select name="subject_name" id="subject_name" class="form-control">
                            <option value="">Select Subject</option> 
                            <?php foreach($subjectlist as $value){   ?>
                                 <option value="<?= $value['subject_id'] ?>" ><?= $value['subject'] ?></option>
                                <?php } ?>
                        </select> 
                     </div>
                    <div class="col-md-12">
                        <label class="form_label" for="syllabus_name">Syllabus Name</label>
                        <input type="text" class="form-control" name="syllabus_name" id="syllabus_name" maxlength="50" required>
                   
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
                                    <option value="<?= $package_id; ?>"> <?= $package_name; ?> </option>
                            <?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-12">
                        <label class="form_label" for="description">Description</label>
                        <textarea type="text" class="form-control" rows="4" name="description" id="description" required></textarea>
                    </div>
               

                </div>
                <div class="modal-footer">
                    <input type="hidden" name="institute_id" value="<?= $instituteID; ?>" required />
                    <input type="hidden" name="redirect" value="<?= $redirect; ?>" required />
                    <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success" name="add_package_form_submit">Add</button>
                </div>
                <?php echo form_close(); ?>
            </div>

        </div>
    </div>
    
    
<script>
      $('#session_classroom').multiselect({ 
        columns: 1,
        placeholder: 'Select Classroom',
        search: true,
        selectAll: true
    });
</script>