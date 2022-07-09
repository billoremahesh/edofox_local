    <!-- Add Session Schedule Modal -->
    <div id="add_schedule_modal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title"><?= $title; ?></h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <?php echo form_open('schedule/add_schedule_submit'); ?>
                <div class="modal-body">
                    <div class="row g-3">


                        <div class="col-12">
                            <label class="form_label" for="session_title">Session Title</label>
                            <input type="text" class="form-control" name="session_title" id="session_title" maxlength="240" required>
                        </div>


                        <?php
                        $days_of_week_array = array(
                            '1' => 'Monday',
                            '2' => 'Tuesday',
                            '3' => 'Wednesday',
                            '4' => 'Thursday',
                            '5' => 'Friday',
                            '6' => 'Saturday',
                            '7' => 'Sunday'
                        );
                        ?>

                        <div class="col-12">
                            <div>Classroom: <b><?= $classroom_details['package_name']; ?></b></div>
                            <div>Date: <b><?= date_format(date_create($schedule_date), 'd/m/y'); ?></b></div>
                            <div>Day of the week: <b><?= $days_of_week_array[$day]; ?></b></div>
                        </div>
                        <div class="col-6">
                            <div><b>Session Frequency: </b><select name="session_frequency" id="session_frequency" class="form-select select2_dropdown" required >
                                <option value="Weekly" >Weekly</option>
                                <option value="Date" >Once</option> 
                                <option value="Monthly" >Monthly</option>
                            </select></div>
                        </div>
                        <div class="col-6">
                        <b>Session Date: </b>
                           <input type="date" id="schedule_date" name="schedule_date" class="form-select" value="<?= $schedule_date ?>" required />
                        </div>

                        <div class="col-4">
                            <label class="form_label" for="session_subject">Which subject?</label>

                            <select name="session_subject" id="session_subject" class="form-select select2_dropdown" required>
                                <option value="">Select Subject</option>
                                <?php
                                if (!empty($subjects_list)) :
                                    foreach ($subjects_list as $row) :
                                        $subject_id = $row['subject_id'];
                                        $subject_name = $row['subject'];
                                        echo "<option value='$subject_id'>$subject_name</option>";
                                    endforeach;
                                endif;
                                ?>
                            </select>
                        </div>




                        <div class="col-4">
                            <label class="form_label" for="session_start_time"> Session starts at</label>
                            <input type="text" class="form-control session_time" name="session_start_time" id="session_start_time" required />
                        </div>


                        <div class="col-4">
                            <label class="form_label" for="session_end_time">Session ends at</label>
                            <input type="text" class="form-control session_time" name="session_end_time" id="session_end_time" required />
                        </div>


                        <div class="col-12">
                            <div class="text-success" id="session_duration"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="session_week_day" value="<?= $day; ?>" />
                    <input type="hidden" name="session_classroom" value="<?= $classroom_id; ?>" /> 
                    <input type="hidden" name="institute_id" value="<?= $instituteID; ?>" required />
                    <input type="hidden" name="redirect" value="<?= $redirect; ?>" required />
                      <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Add</button>
                </div>
                <?php echo form_close(); ?>
            </div>

        </div>
    </div>

    <script>
        $(document).ready(function() {
            $(".session_time").change(calculateTime);
        });

        $('.select2_dropdown').select2({
            width: "100%",
            dropdownParent: $("#add_schedule_modal")
        });

        $(document).on('select2:open', () => {
            document.querySelector('.select2-search__field').focus();
        });


        $("#session_start_time").flatpickr({
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i",
            defaultDate: new Date(),
            onChange: function(selectedDates, dateStr, instance) {
                if (dateStr)
                    instance.close();
                $("#session_end_time").flatpickr({
                    enableTime: true,
                    noCalendar: true,
                    dateFormat: "H:i",
                    minDate: new Date(selectedDates)
                });
            },

        });


        function calculateTime() {
            var time_start = new Date();
            var time_end = new Date();

            //get values
            var valuestart = $("input[name='session_start_time']").val();
            var valuestop = $("input[name='session_end_time']").val();

            if (valuestart != '' && valuestop != '') {
                var valuestart = valuestart.split(':');
                var valuestop = valuestop.split(':');

                time_start.setHours(valuestart[0], valuestart[1])
                time_end.setHours(valuestop[0], valuestop[1])

                time_diff = msToTime(time_end - time_start);

                $("#session_duration").html("<b>Session Duration:</b> " + time_diff);
            }

        };
    </script>