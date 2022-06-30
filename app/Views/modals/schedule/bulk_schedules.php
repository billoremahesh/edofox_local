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



                        <div class="col-12 col-md-6">
                            <label class="form_label" for="session_classroom">Which Classroom?</label>

                            <select name="session_classroom" id="session_classroom" class="form-select select2_dropdown" required>
                                <option value="">Select Classroom</option>
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

                        <div class="col-12 col-md-6">
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

                        <div class="col-12"><b>Session Frequency: </b><span class="badge bg-success">Weekly</span></div>

                        <div class="col-12">
                            <h6> Schedule Configurations </h6>
                            <button type="button" class="add_section_btn" onclick="add_exam_section_structure()">
                                Add
                            </button>
                        </div>



                        <div class="col-12">
                            <table class="table table-bordered table-condensed my-2" id="exam_section_structure_tbl">
                                <thead>
                                    <tr>
                                        <th>Day of the week?</th>
                                        <th>Session starts at</th>
                                        <th>Session ends at</th>
                                        <th>Duration</th>
                                        <th>Delete</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    <tr class="exam_section_structure_tr" id="exam_section_structure_tr_1">
                                        <td>
                                            <select name="session_week_days[]" id="session_week_days_1" class="form-select" required>
                                                <option value="">Select Day</option>
                                                <?php
                                                foreach ($days_of_week_array as $key => $day) :
                                                ?>
                                                    <option value="<?= $key; ?>"><?= $day; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control session_start_time" name="session_start_time[]" id="session_start_time_1" onchange="calculateTime(1)" required />
                                        </td>
                                        <td>
                                            <input type="text" class="form-control session_end_time" name="session_end_time[]" id="session_end_time_1" onchange="calculateTime(1)" required />
                                        </td>
                                        <td>
                                            <div class="text-success" id="session_duration_1"></div>
                                        </td>
                                        <td>

                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="session_frequency" value="Weekly" required />
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
        $('.select2_dropdown').select2({
            width: "100%",
            dropdownParent: $("#add_schedule_modal")
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

        function calculateTime(id) {
            var time_start = new Date();
            var time_end = new Date();

            //get values
            var valuestart = $("#session_start_time_" + id).val();
            var valuestop = $("#session_end_time_" + id).val();

            if (valuestart != '' && valuestop != '') {
                var valuestart = valuestart.split(':');
                var valuestop = valuestop.split(':');

                time_start.setHours(valuestart[0], valuestart[1])
                time_end.setHours(valuestop[0], valuestop[1])

                time_diff = msToTime(time_end - time_start);

                $("#session_duration_" + id).html(time_diff);
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