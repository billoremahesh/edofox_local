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
                            <label class="form_label" for="session_title">Session Title<span style="color:red;">*</span></label>
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
                            <!-- <div id="frequency_date" >Date: <b><?= date_format(date_create($schedule_date), 'd/m/y'); ?></b></div>
                            <div id="frequency_day" >Day of the week: <b><?= $days_of_week_array[$day]; ?></b></div> -->
                        </div>
                        <div class="col-6">
                            <div>
                                <label class="form_label" for="schedule_date">Session Frequency</label>
                                <select name="session_frequency" id="session_frequency" class="form-control form-select select2_dropdown" required>
                                    <option value="Weekly">Weekly</option>
                                    <option value="Date">Once</option>
                                    <option value="Monthly">Monthly</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-6" id="day_of_week">
                            <div>
                                <label class="form_label" for="session_week">Day of the week</label>
                                <br> <b><?= $days_of_week_array[$day]; ?></b>
                            </div>
                        </div>
                        <div class="col-6" style="display:none;" id="date_of_month">
                            <label class="form_label" for="schedule_date">Day of the month</label>
                            <br> <b><?= $dayofmonth; ?></b>
                        </div>
                        <div class="col-6" style="display:none;" id="once_date">
                            <div id="frequency_date">Date: <br> <b><?= date_format(date_create($schedule_date), 'd/m/y'); ?></b></div>
                        </div>

                        <div class="col-4">
                            <label class="form_label" for="session_subject">Which subject?<span style="color:red;">*</span></label>

                            <select name="session_subject" id="session_subject" class="form-control form-select select2_dropdown" required>
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
                            <label class="form_label" for="session_start_time"> Session starts at<span style="color:red;" >*</span></label>
                            <input type="text" class="form-control timepicker" name="session_start_time" id="session_start_time"  min="00:00" max="12:00" placeholder="hh:mm" readonly required />
                        </div>


                        <div class="col-4">
                            <label class="form_label" for="session_end_time">Session ends at<span style="color:red;" >*</span></label>
                            <input type="text" class="form-control timepicker" name="session_end_time" id="session_end_time"  min="00:00" max="12:00" placeholder="hh:mm" readonly required />
                        </div>


                        <div class="col-12">
                            <div class="text-success" id="session_duration"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="session_week_day" value="<?= $day; ?>" />
                    <input type="hidden" name="session_month_day" value="<?= $month_day_value; ?>" />
                    <input type="hidden" name="session_classroom" value="<?= $classroom_id; ?>" />
                    <input type="hidden" name="schedule_date" value="<?= $schedule_date ?>" />
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
    var endsAt = "";
    <?php if( isset($last_end_time) && $last_end_time != ""):?>
        endsAt = '<?= $last_end_time ?>';
    <?php endif;?>

        
    </script>

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


        // $(".timepicker").timepicker({
        //     enableTime: true,
        //     noCalendar: true,
        //     dateFormat: "HH:mm:ss", 
        //     defaultDate: new Date(),
        //     // onChange: function(selectedDates, dateStr, instance) {
        //     //     alert(selectedDates);
        //     //     // if (dateStr)
        //     //     //     instance.close();
        //     //     // $("#session_end_time").flatpickr({
        //     //     //     enableTime: true,
        //     //     //     noCalendar: true,
        //     //     //     dateFormat: "H:i",
        //     //     //     minDate: new Date(selectedDates)
        //     //     // });
        //     // },

        // });


        function calculateTime() {
            var time_start = new Date();
            var time_end = new Date();

            //get values
            var valuestart = $("input[name='session_start_time']").val();
            var valuestop = $("input[name='session_end_time']").val();

            if (valuestart != '' && valuestop != '') {
                var valuestart = valuestart.split(':');
                var valuestop = valuestop.split(':');

                var valuestart1 = valuestart[1].split(' ');
                var valuestop1 = valuestop[1].split(' ');

                console.log(valuestart, 'valuestart');
                console.log(valuestop, 'valuestart');

                time_start.setHours(valuestart[0], valuestart1[0])
                time_end.setHours(valuestop[0], valuestop1[0])

                time_diff = msToTime(time_end - time_start);

                if (time_diff == 'NaN:NaN:NaN') {
                    $("#session_duration").html("");
                    $("#session_end_time").val('');
                    $("#session_duration").html("<b style='color:red' >Invalid Time format</b>");

                } else if (time_diff == '00:00:00') {
                    $("#session_duration").html("");
                    $("#session_end_time").val('');
                    $("#session_duration").html("<b style='color:red' >End Time Should be greater than the start time</b>");

                } else {
                    let check_time = time_diff.includes("-");
                    if (check_time == false) {
                        $("#session_duration").html("<b>Session Duration:</b> " + time_diff);

                    } else {
                        $("#session_end_time").val('');
                        $("#session_duration").html("<b style='color:red' >End Time Should be greater than the start time</b>");
                    }
                }


            }

        };


        $("#session_frequency").change(function() {
            var getValue = $(this).val();
            if (getValue == 'Date') {
                $("#once_date").show();
                $("#day_of_week").hide();
                $("#date_of_month").hide();
            } else if (getValue == 'Weekly') {
                // let day_week=`Day of the week: <b></b>`;
                // $("#frequency_day").html(day_week);
                $("#day_of_week").show();
                $("#date_of_month").hide();
                $("#once_date").hide();
            } else if (getValue == 'Monthly') {
                // let get_date = $("#schedule_date").val(); 
                // let day_month=`Date of the Month: <b>`+get_date+`</b>`;
                // $("#frequency_day").html(day_month);
                $("#day_of_week").hide();
                $("#date_of_month").show();
                $("#once_date").hide();
            }
        });


        $(function() {
            $(".timepicker").timepicker({
                timeFormat: "HH:mm",
                interval: 15,
                minTime: "06",
                maxTime: "23:55",
                defaultTime: "00",
                startTime: "01:00",
                dynamic: true,
                dropdown: true,
                scrollbar: false,
                change: function(time) {
                    let start = $("#session_start_time").val();
                    let end = $("#session_end_time").val();
                    if ((start != '') && (end != '')) {
                        calculateTime();
                    } else if (start != '') {
                        $("#session_end_time").val(get_Update_time(time, 30));
                    }
                }
            });
        });

        function get_Update_time(time, add_time) {
            var d1 = new Date(time);
            d2 = new Date(d1);
            d2.setMinutes(d1.getMinutes() + add_time);
            console.log("Updated D2", d2);
            let hour = d2.getHours() < 10 ? '0' + d2.getHours() : d2.getHours();
            let minutes = d2.getMinutes() < 10 ? '0' + d2.getMinutes() : d2.getMinutes();
            const hoursAndMinutes = hour + ':' + minutes;
            return hoursAndMinutes;
        }

        //Setup time
        if(endsAt != "") {
            $("#session_start_time").val(endsAt);
            //Set end time which is 30 minutes + this time
            var values = endsAt.split(":");
            var newMinutes = parseInt(values[1]) + 30;
            var newHour = parseInt(values[0]);
            if(newMinutes >= 60) {
                newHour = newHour + 1;
                newMinutes = newMinutes % 60;
            }
            if(newMinutes < 10) {
                newMinutes = "0" + newMinutes;
            }
            if(newHour < 10) {
                newHour = "0" + newHour;
            }
            $("#session_end_time").val(newHour + ":" + newMinutes);       
        }
    </script>