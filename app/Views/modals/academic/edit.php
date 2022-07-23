<style>
    .ms-options-wrap button{
        overflow: hidden;
    }
    
</style>
<div class="modal fade" id="update_academic_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
             <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title"><?= $title; ?></h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div> 
                <?php echo form_open('academic/update_academic_submit'); ?>
                <div class="modal-body row g-3">
                    <div class="col-md-12">
                        <label class="form_label" for="academic_plan">Academic plan</label> 
                        <input type="text" class="form-control" name="academic_plan" id="academic_plan" value="<?= $academic_details['academic_plan_name'] ?>" maxlength="50" required>
                   
                     </div>
                    <div class="col-md-12"> 
                    <label class="form_label" for="start_date">Start Date</label>
                    <input type="text" id="schedule_start_date" class="form-control" name="start_date" value="<?= $academic_details['start_date'] ?>" placeholder="Start Date" required />
                      </div>

                    <div class="col-md-12">
                        <label class="form_label" for="end_date">End Date</label>
                        <input type="text" id="schedule_end_date" name="end_date" class="form-control" value="<?= $academic_details['end_date'] ?>" placeholder="End Date" required />
                
                    </div>
                     
                    <div class="col-md-12">
                        <label class="form_label" for="syllabus_name">Syllabus Name</label> 
                        <select name="syllabus_name" id="syllabus_name" class="form-control" required>
                            <option value="">Select Syllabus</option> 
                            <?php foreach($syllabuslist as $value){   ?>
                                 <option value="<?= $value['id'] ?>" <?php echo $academic_details['syllabus_id']==$value['id'] ? 'selected':''; ?> ><?= $value['syllabus_name'] ?></option>
                                <?php } ?>
                        </select> 
                     </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="institute_id" value="<?= $instituteID; ?>" required />
                    <input type="hidden" name="redirect" value="<?= $redirect; ?>" required />
                    <input type="hidden" name="academic_id" value="<?= $academic_id; ?>" required />
                    
                    <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success" name="add_package_form_submit">Add</button>
                </div>
                <?php echo form_close(); ?>
            </div>

        </div>
    </div>

     
    
<script>
    $(document).ready(function() {

var classroom_filter_val = "";
var classroom_filter_text = "";

$("#schedule_start_date").flatpickr({
    dateFormat: "Y-m-d",
    onChange: function(selectedDates) {
        $("#schedule_end_date").flatpickr({
            dateFormat: "Y-m-d",
            minDate: new Date(selectedDates),
            maxDate: new Date(selectedDates).fp_incr(30), // add 7 days
        });
    }
});
    });
</script>