<style>
    .ms-options-wrap button{
        overflow: hidden;
    }
    
</style>
<!-- Add Classroom Modal -->
    <div id="add_syllabus_modal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title"><?= $title; ?></h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <?php echo form_open('syllabus/update_syllabus_chapter_submit'); ?>
                <div class="modal-body row g-3">
                    <div class="col-md-12">
                        <label class="form_label" for="subject_name">Syllabus Name</label> 
                         <p><b><?= $syllabusDetails['syllabus_name'] ?></b></p>
                     </div>
                  
                    <div class="col-md-12">
                        <label class="form_label" for="chapter">Chapters Name</label>
                        <select name="chapter[]" class="form-control" multiple id="chapter" style="broder:1px solid #ced4da;" required >
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
                        <?php $difficultys=[1,2,3,4,5]; ?>
                        <label class="form_label" for="difficulty">Difficulty</label>
                        <select name="difficulty" id="difficulty" class="form-control" required>
                            <option value="" >-Select Difficulty-</option>
                           <?php foreach($difficultys as $value){ ?>
                             <option value="<?= $value ?>" <?php echo $difficulty==$value?'selected':''; ?>  ><?= $value ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="col-md-12">
                        <?php $importances=['Low','Medium','High','Very High']; ?>
                        <label class="form_label" for="importance">important</label>
                        <select name="importance" id="importance" class="form-control" required> 
                            <option value="" >-Select Importance-</option> 
                            <?php foreach($importances as $value){ ?>
                            <option value="<?= $value ?>" <?php echo $importance==$value?'selected':''; ?> ><?= $value ?></option>
                                <?php } ?>
                        </select>
                    </div>
               

                </div>
                <div class="modal-footer">
                    <input type="hidden" name="institute_id" value="<?= $instituteID; ?>" required />
                    <input type="hidden" name="redirect" value="<?= $redirect; ?>" required />
                    <input type="hidden" name="syllabus_id" value="<?= $syllabusDetails['id'] ?>" />
                    <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success" name="add_package_form_submit">Add</button>
                </div>
                <?php echo form_close(); ?>
            </div>

        </div>
    </div>
    
    
<script>

    
var selectedClassroom =<?php echo json_encode($s_chapter_id);?>;  
       $("#chapter").val(selectedClassroom);
      $('#chapter').multiselect({ 
        columns: 1,
        placeholder: 'Select Chapter', 
        search: true,
        selectAll: true
    });
</script>