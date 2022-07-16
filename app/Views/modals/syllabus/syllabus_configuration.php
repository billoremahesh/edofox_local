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
                        <label class="form_label" for="subject_name">Syllabus Name</label> 
                        <select name="subject_name" id="subject_name" class="form-control">
                            <option value="">Select Syllabus</option> 
                            <?php foreach($syllabuslist as $value){   ?>
                                 <option value="<?= $value['id'] ?>" ><?= $value['syllabus_name'] ?></option>
                                <?php } ?>
                        </select> 
                     </div>
                  
                    <div class="col-md-12">
                        <label class="form_label" for="session_classroom">Chapters Name</label>
                        <select name="session_classroom[]" class="form-control" multiple id="session_classroom" style="broder:1px solid #ced4da;" >
                        <?php
                            if (!empty($chapter_list)) {
                                foreach ($chapter_list as $row) {
                                    $chapter_id = $row['id'];
                                    $chapter_name = $row['chapter_name'];
                            ?>
                                    <option value="<?= $chapter_id; ?>"> <?= $chapter_name; ?> </option>
                            <?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-12">
                        <label class="form_label" for="difficulty">Difficulty</label>
                        <select name="difficulty" id="difficulty" class="form-control">
                            <option value="1" >1</option>
                            <option value="2" >2</option>
                            <option value="3" >3</option>
                            <option value="4" >4</option>
                            <option value="5" >5</option>
                        </select>
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
        placeholder: 'Select Chapter',
        search: true,
        selectAll: true
    });
</script>