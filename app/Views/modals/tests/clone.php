<!-- Clone Test Modal -->
<div id="clone_test_modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title"><?= $title; ?></h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <?php echo form_open('tests/clone_test_submit'); ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12 mb-2">
                        <label class="form-label" for="cloned_test_name">New Cloned Test Name</label>
                        <input type="text" class="form-control" name="cloned_test_name" id="cloned_test_name" value="<?= $test_details['test_name']; ?>" required />
                    </div>

                    <div class="col-md-12 mb-2">
                        <label class="form-label" for="update_test_package">Classrooms</label> (You can select multiple classrooms)
                        <select class="form-control add_package_multiple" id="update_test_package" name="update_test_package[]" multiple="multiple" required>
                            <?php
                            foreach ($classroom_list as $row) {
                                $package_id = $row['id'];
                                $package_name = $row['package_name'];
                                echo "<option value='$package_id'>$package_name</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="col-md-4 mb-2">
                        <label for="start_date">Test Start Time</label>
                        <div class="input-append date form_datetime1">
                            <input size="16" type="text" id="start_date" name="start_date" required autocomplete="off" required>
                            <span class="add-on"><i class="icon-th"></i></span>
                        </div>
                    </div>

                    <div class="col-md-4 mb-2">
                        <label class="form_label" for="end_date">Test End Time</label>
                        <div class="input-append date form_datetime2">
                            <input size="16" type="text" id="end_date" name="end_date" required autocomplete="off" required>
                            <span class="add-on"><i class="icon-th"></i></span>
                        </div>
                    </div>

                </div>

            </div>
            <div class="modal-footer">
                <input type="hidden" name="original_test_id" value="<?= $test_id; ?>" required />
                <input type="hidden" name="institute_id" value="<?= $instituteID; ?>" required />
                <input type="hidden" name="redirect" value="<?= $redirect; ?>" required />
                <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-success" name="add_package_form_submit">Clone/ Copy</button>
            </div>
            <?php echo form_close(); ?>
        </div>

    </div>
</div>

<script>
    // bootstrap 5 select2 not working in modal bug
    // https://stackoverflow.com/questions/18487056/select2-doesnt-work-when-embedded-in-a-bootstrap-modal/33884094#33884094
    // Initializing select2
    $('.add_package_multiple').select2({
        width: "100%",
        dropdownParent: $("#clone_test_modal")
    });
</script>

<script type="text/javascript">
    $(".form_datetime1").datetimepicker({
        format: "yyyy-mm-dd hh:ii",
        autoclose: true,
        todayBtn: true,
        fontAwesome: 'font-awesome',
        pickerPosition: "bottom-left"
    });

    $(".form_datetime2").datetimepicker({
        format: "yyyy-mm-dd hh:ii",
        autoclose: true,
        todayBtn: true,
        fontAwesome: 'font-awesome',
        pickerPosition: "bottom-left"
    });
</script>