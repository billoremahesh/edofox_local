    <!-- Update Session Schedule Modal -->
    <div id="update_schedule_modal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title"><?= $title; ?></h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <?php echo form_open('schedule/update_schedule_submit'); ?>
                <div class="modal-body">
                    <div class="row g-3">


                        <div class="col-12">
                            <label class="form_label" for="session_title">Session Title</label>
                            <input type="text" class="form-control" name="session_title" id="session_title" value="<?= $schedule_details['title']; ?>" maxlength="240" required>
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

                        // $session_frequency_array = array(
                        //     'Weekly' => 'Weekly',
                        //     'Monthly' => 'Monthly',
                        //     'Date' => 'Once on a specific Date',
                        // );
                        ?>



                        <div class="col-12 col-md-6">
                            <label class="form_label" for="session_classroom">Which Classroom?</label>

                            <select name="session_classroom" id="session_classroom" class="form-select select2_dropdown" required>
                                <option value="">Select Classroom</option>
                                <?php
                                if (!empty($classroom_list)) {
                                    foreach ($classroom_list as $row) {
                                        $package_id = $row['id'];
                                        $package_name = $row['package_name'];

                                        $classroom_selected = "";
                                        if ($package_id == $schedule_details['classroom_id']) {
                                            $classroom_selected = " selected";
                                        }

                                ?>
                                        <option value="<?= $package_id; ?>" <?= $classroom_selected; ?>> <?= $package_name; ?> </option>
                                <?php
                                    }
                                }
                                ?>
                            </select>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form_label" for="session_subject">Which subject?</label>

                            <select name="session_subject" id="session_subject" class="form-select select2_dropdown" required>
                                <option value="">Select Subject</option>
                                <?php
                                if (!empty($subjects_list)) :
                                    foreach ($subjects_list as $row) :

                                        $subject_id = $row['subject_id'];
                                        $subject_name = $row['subject'];

                                        $subject_selected = "";
                                        if ($subject_id == $schedule_details['subject_id']) {
                                            $subject_selected = " selected";
                                        }

                                        echo "<option value='$subject_id' $subject_selected >$subject_name</option>";
                                    endforeach;
                                endif;
                                ?>
                            </select>
                        </div>

                        <div class="col-12"><b>Session Frequency: </b><span class="badge bg-success">Weekly</span></div>

                        <div class="col-4">
                            <label class="form_label" for="session_week_day">Which day of the week?</label>
                            <select name="session_week_day" id="session_week_day" class="form-select" required>
                                <option value="">Select Day</option>
                                <?php
                                foreach ($days_of_week_array as $key => $day) :
                                    $day_selected = "";
                                    if ($key == $schedule_details['day']) {
                                        $day_selected = " selected";
                                    }
                                ?>
                                    <option value="<?= $key; ?>" <?= $day_selected; ?>><?= $day; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>



                        <div class="col-4">
                            <label class="form_label" for="session_start_time"> Session starts at</label>
                            <input type="text" class="form-control session_time" name="session_start_time" id="session_start_time" autocomplete="off" value="<?= $schedule_details['starts_at']; ?>" required />
                        </div>


                        <div class="col-4">
                            <label class="form_label" for="session_end_time">Session ends at</label>
                            <input type="text" class="form-control session_time" name="session_end_time" id="session_end_time" autocomplete="off" value="<?= $schedule_details['ends_at']; ?>" required />
                        </div>


                        <div class="col-12">
                            <div class="text-success" id="session_duration"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="schedule_id" value="<?= $schedule_id; ?>" required />
                    <input type="hidden" name="redirect" value="<?= $redirect; ?>" required />
                    <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Update</button>
                </div>
                <?php echo form_close(); ?>
            </div>

        </div>
    </div>

    <script>
        var selected_start_time = "<?= $schedule_details['starts_at']; ?>";
        $(document).ready(function() {
            $(".session_time").change(calculateTime);
            calculateTime();
        });

        $('.select2_dropdown').select2({
            width: "100%",
            dropdownParent: $("#update_schedule_modal")
        });

        $(document).on('select2:open', () => {
            document.querySelector('.select2-search__field').focus();
        });

        $("#session_start_time").flatpickr({
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i",
            defaultDate: selected_start_time,
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

        $('.start_time').datetimepicker({
            pickDate: false,
            pickTime: true,
            use24hours: true,
            format: 'hh:ii',
            autoclose: true,
            fontAwesome: 'font-awesome',
            pickerPosition: "bottom-left"
        });

        $('.end_time').datetimepicker({
            pickDate: false,
            pickTime: true,
            use24hours: true,
            format: 'hh:ii',
            autoclose: true,
            fontAwesome: 'font-awesome',
            pickerPosition: "bottom-left"
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