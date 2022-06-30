    <!-- Add Session Schedule Modal -->
    <div id="add_holiday_modal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title"><?= $title; ?></h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <?php echo form_open('schedule/add_holiday_submit'); ?>
                <div class="modal-body">
                    <div class="row g-3">


                        <div class="col-12">
                            <label class="form_label" for="session_title">Holiday Title</label>
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



                        <div class="col-12 col-md-12">
                            <label class="form_label" for="session_classroom">Which Classroom?</label>

                            <select name="session_classroom[]" id="session_classroom" class="form-select select2_dropdown" multiple required>
                                <option value="ALL">Select All</option>
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

                       



                        <div class="col-12">
                            <table class="table table-bordered table-condensed my-2" id="exam_section_structure_tbl">
                                <thead>
                                    <tr> 
                                        <th>Holiday starts at</th>
                                        <th>Holiday ends at</th>
                                        <th>Duration</th> 
                                    </tr>
                                </thead>
                                <tbody>

                                    <tr class="exam_section_structure_tr" id="exam_section_structure_tr_1">
                                        
                                        <td>
                                            <input type="date" class="form-control session_start_time" name="session_start_time" id="session_start_time" onchange="calculateTime(1)" required />
                                        </td>
                                        <td>
                                            <input type="date" class="form-control session_end_time" name="session_end_time" id="session_end_time"  onchange="calculateTime(1)" required />
                                        </td>
                                        <td style="text-align:center;" >
                                            <div class="text-success" id="session_duration_1"></div>
                                        </td> 
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="session_frequency" value="Date" required />
                    <input type="hidden" name="institute_id" value="<?= $instituteID; ?>" required />
                    <input type="hidden" name="duration" id="duration" value="" required />
                    <input type="hidden" name="redirect" value="<?= $redirect; ?>" required />
                    <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Add</button>
                </div>
                <?php echo form_close(); ?>
            </div>

        </div>
    </div>

    <script>
        $('.select2_dropdown').select2({
            width: "100%",
            dropdownParent: $("#add_holiday_modal")
        });

        $(document).on('select2:open', () => {
            document.querySelector('.select2-search__field').focus();
        });


        $("#session_start_time_1").flatpickr({
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i",
            defaultDate: new Date(),
            onChange: function(selectedDates, dateStr, instance) {
                if (dateStr)
                    instance.close();
                $("#session_end_time_1").flatpickr({
                    enableTime: true,
                    noCalendar: true,
                    dateFormat: "H:i",
                    minDate: new Date(selectedDates)
                });
            },
        });

        function daysBetween(startDate, endDate) {
    var millisecondsPerDay = 24 * 60 * 60 * 1000;
    return (treatAsUTC(endDate) - treatAsUTC(startDate)) / millisecondsPerDay;
}

function treatAsUTC(date) {
    var result = new Date(date);
    result.setMinutes(result.getMinutes() - result.getTimezoneOffset());
    return result;
}

        function calculateTime(id) {
            var time_start = new Date();
            var time_end = new Date();
            //get values
            var valuestart = $("#session_start_time").val();
            var valuestop = $("#session_end_time").val();
            if(valuestart!='' && valuestop!=''){
            var day =daysBetween(valuestart,valuestop);
            if(parseInt(day)<0){
             alert('Selected Date is invalid');
             $("#session_end_time").val(valuestart); 
           $("#session_duration_1").html(1);
           $("#duration").val(1);
            }else{ 
                day = parseInt(day)+1;
           $("#session_duration_1").html(day);
           $("#duration").val(day);
            }
          
        }
           

        };
    </script>

    <script>
        var week_arr_str = "<?php foreach ($days_of_week_array as $key => $day) {
                                echo "<option value=" . $key . ">" . $day . "</option>";
                            } ?>";

        function add_exam_section_structure() {
            var lastid = $(".exam_section_structure_tr:last").attr("id");
            var split_id = lastid.split("_");
            var nextindex = Number(split_id[4]) + 1;
            var html = "<tr class='exam_section_structure_tr' id='exam_section_structure_tr_" + nextindex + "'>";

            html = html + "<td> <select class='form-select' name='session_week_days[]' id='session_week_days_" + nextindex + "'><option value=''>Select Day</option>" + week_arr_str + "</select></td>";

            html = html + "<td><input type='text' class='form-control session_start_time' name='session_start_time[]' id='session_start_time_" + nextindex + "' onchange='calculateTime(" + nextindex + ")' required /></td>";

            html = html + "<td><input type='text' class='form-control session_end_time' name='session_end_time[]' id='session_end_time_" + nextindex + "' onchange='calculateTime(" + nextindex + ")' required /></td>";

            html = html + "<td><div class='text-success' id='session_duration_" + nextindex + "'></div></td>";

            html = html + "<td><span onclick='remove_extra_structure_div(" + nextindex + ")' class='remove_ed_icon'><i class='fas fa-trash'></i></span></td>";
            html = html + "</tr>";

            $("#exam_section_structure_tbl").append(html);


            $("#session_start_time_"+ nextindex).flatpickr({
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                defaultDate: new Date(),
                onChange: function(selectedDates, dateStr, instance) {
                    if (dateStr)
                        instance.close();
                    $("#session_end_time_"+ nextindex).flatpickr({
                        enableTime: true,
                        noCalendar: true,
                        dateFormat: "H:i",
                        minDate: new Date(selectedDates)
                    });
                },
            });
        }

        function remove_extra_structure_div(remove_id) {
            $("#exam_section_structure_tr_" + remove_id).remove();
        }
    </script>


    <script>
        $(document).ready(function() {
            if (localStorage.getItem('schedule_classroom_filter_val') != '') {
                classroom_filter_val = localStorage.getItem('schedule_classroom_filter_val');
                classroom_filter_text = localStorage.getItem('schedule_classroom_filter_text');
                console.log(classroom_filter_val);
                $(".select2_dropdown").val(classroom_filter_val).trigger('change');
            }
        });
    </script>