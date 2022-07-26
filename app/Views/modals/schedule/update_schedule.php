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
                        <label class="form_label" for="session_title">Session Title<span style="color:red;" >*</span></label>
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



                    <div class="col-12 col-md-12">
                        <label class="form_label" for="session_classroom">Classroom :</label>
                         <b><?= $schedule_details['package_name']; ?></b>
                    </div> 

                    <div class="col-6"> <label class="form_label" for="session_classroom">Session Frequency:</label><br><span class="badge bg-success"><?= $schedule_details['frequency']; ?></span></div>

                    <div class="col-6" >
                    <div> 
                        <?php if($schedule_details['frequency']=='Date'){ ?> 
                    <label class="form_label" for="session_week">Date</label>
                    <br> <b><?= date_format(date_create($schedule_details['date']), 'd/m/y'); ?></b>
                        <?php }else if($schedule_details['frequency']=='Monthly'){  ?>
                            <label class="form_label" for="session_week">Day of the Month</label>
                            <br>  <b><?= $dayofmonth; ?></b>
                        <?php }else if($schedule_details['frequency']=='Weekly'){ ?>
                            <label class="form_label" for="session_week">Day of the week</label>
                            <br>  <b><?php
                              $day_week=$schedule_details['day'];
                             echo  $days_of_week_array[$day_week];  ?></b>
                         <?php } ?>  
                    </div>
                    </div> 
                    <br>
                    <div class="row">  
                    <div class="col-4">
                        <label class="form_label" for="session_subject">Which subject?<span style="color:red;" >*</span></label>

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



                    <div class="col-4">
                        <label class="form_label" for="session_start_time"> Session starts at<span style="color:red;" >*</span></label>
                        <input type="text" class="form-control timepicker" name="session_start_time" id="session_start_time" autocomplete="off" value="<?= $schedule_details['starts_at']; ?>" readonly required />
                    </div>


                    <div class="col-4">
                        <label class="form_label" for="session_end_time">Session ends at<span style="color:red;" >*</span></label>
                        <input type="text" class="form-control timepicker" name="session_end_time" id="session_end_time" autocomplete="off" value="<?= $schedule_details['ends_at']; ?>" readonly required />
                    </div>
                    </div>



                    <div class="col-12">
                        <div class="text-success" id="session_duration"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <input type="hidden" name="session_classroom" value="<?= $schedule_details['classroom_id'] ?>" required />
                <input type="hidden" name="schedule_id" value="<?= $schedule_id; ?>" required />
                <input type="hidden" name="redirect" value="<?= $redirect; ?>" required />
                <input type="hidden" name="session_week_day" id="session_week_day" value="<?= $schedule_details['day'] ?>" />
                <input type="hidden" name="session_month_day" id="session_week_day" value="<?= $schedule_details['day'] ?>" />
                <input type="hidden" name="frequency" id="session_frequency" value="<?= $schedule_details['frequency'] ?>" />
                <input type="hidden" name="classroom_id" id="classroom_id" value="<?= $schedule_details['classroom_id'] ?>" />
                <input type="hidden" name="date" id="date" value="<?= $schedule_details['date'] ?>" />
                <input type="hidden" name="day" id="day" value="<?= $schedule_details['day'] ?>" />
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

    // $("#session_start_time").flatpickr({
    //     enableTime: true,
    //     noCalendar: true,
    //     dateFormat: "H:i",
    //     defaultDate: selected_start_time,
    //     onChange: function(selectedDates, dateStr, instance) {
    //         if (dateStr)
    //             instance.close();
    //         $("#session_end_time").flatpickr({
    //             enableTime: true,
    //             noCalendar: true,
    //             dateFormat: "H:i",
    //             minDate: new Date(selectedDates)
    //         });
    //     },
    // });

    // $('.start_time').datetimepicker({
    //     pickDate: false,
    //     pickTime: true,
    //     use24hours: true,
    //     format: 'hh:ii',
    //     autoclose: true,
    //     fontAwesome: 'font-awesome',
    //     pickerPosition: "bottom-left"
    // });

    // $('.end_time').datetimepicker({
    //     pickDate: false,
    //     pickTime: true,
    //     use24hours: true,
    //     format: 'hh:ii',
    //     autoclose: true,
    //     fontAwesome: 'font-awesome',
    //     pickerPosition: "bottom-left"
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

            time_start.setHours(valuestart[0], valuestart[1])
            time_end.setHours(valuestop[0], valuestop[1])

            time_diff = msToTime(time_end - time_start);

            if (time_diff == 'NaN:NaN:NaN') {
                    $("#session_duration").html("");
                    // Snackbar.show({
                    //     pos: 'top-center',
                    //     text: 'Invalid Time format'
                    // });
                    $("#session_end_time").val('');
                    $("#session_duration").html("<b style='color:red' >Invalid Time format</b>");
                
                }else if(time_diff=='00:00:00'){
                    $("#session_duration").html("");
                    // Snackbar.show({
                    //     pos: 'top-center',
                    //     text: 'End Time Should be greater than the start time', 
                    // });
                    $("#session_end_time").val('');
                    $("#session_duration").html("<b style='color:red' >End Time Should be greater than the start time</b>");
                  
                }else{
                    let check_time = time_diff.includes("-");
                    if (check_time == false) {
                        $("#session_duration").html("<b>Session Duration:</b> " + time_diff);

                    } else {
                        // $("#session_duration").html("");
                        // Snackbar.show({
                        //     pos: 'top-center',
                        //     text: 'End Time Should be greater than the start time'
                        // }); 
                    $("#session_end_time").val('');
                    $("#session_duration").html("<b style='color:red' >End Time Should be greater than the start time</b>");
                    }
                }
        }

    };


    
    $(function() {
            $(".timepicker").timepicker({
                timeFormat: "HH:mm",
                interval: 15,
                minTime: "06",
                maxTime: "23:55pm",
                defaultTime: "00",
                startTime: "01:00",
                dynamic: true,
                dropdown: true,
                scrollbar: false,change: function(time) {

                let start = $("#session_start_time").val();
                let end = $("#session_end_time").val();
                console.log('hello');
                if ((start != '') && (end != '')) {
                calculateTime();
                }
                }
            });
        });
</script>