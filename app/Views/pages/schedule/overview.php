<!-- Include Header -->
<?php include_once(APPPATH . "Views/header.php"); ?>

<!-- Custom CSS -->
<link href="<?php echo base_url('assets/css/schedule/overview.css?v=20220609'); ?>" rel="stylesheet">


<div id="content">
    <div class="container-fluid mt-4">

        <div class="flex-container-column">
            <div>
                <label class="h5 text-gray-800 text-uppercase"> <?= $title; ?> </h5>
            </div>
            <div class="breadcrumb_div" aria-label="breadcrumb">
                <ol class="breadcrumb_custom">
                    <li class="breadcrumb_item"><a href="<?php echo base_url('/home'); ?>"> Home </a></li>
                    <li class="breadcrumb_item active" aria-current="page"> <?= $title; ?> </li>
                </ol>
            </div>
        </div>

        <div>

            <div class="row row-cols-auto">

                <div class="col my-1">
                    <select id="classroom_filter" class="form-select classroom_select2_dropdown" onchange="get_schedule_data()">
                        <option value=""> Select Classroom</option>
                    </select>
                </div>


                <div class="col my-1">
                    <input type="text" id="schedule_start_date" placeholder="Start Date" onchange="get_schedule_data()" />
                    <input type="text" id="schedule_end_date" placeholder="End Date" onchange="get_schedule_data()" />
                </div>


                <div class="col my-1">
                    <button class="btn btn-secondary btn-sm text-uppercase" onclick="resetFilterData();" data-toggle='tooltip' title='Reset Filters'>
                        Reset
                    </button>
                </div>

                <div class="col-6 my-1">
                    <div class="float-end">
                        <?php if ($module == 'Schedule') :  ?>
                            <?php if (in_array("manage_schedule", session()->get('perms')) or in_array("all_perms", session()->get('perms'))) :  ?>
                                <a href="#" class="btn btn-primary btn-sm text-uppercase" onclick="show_add_modal('modal_div','add_schedule_modal','schedule/add_bulk_schedules_modal');" data-toggle='tooltip' title='Add Schedule'>
                                    Add Schedule
                                </a>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                    <div class="float-end">
                        <?php  
                        if ($module_holiday == 'Add-Holiday') :  ?>
                         <?php if (in_array("manage_schedule", session()->get('perms')) or in_array("all_perms", session()->get('perms'))) :  ?>
                             

                                <div class="btn-group">
                            <button type="button" class="btn btn-outline-success btn-sm d-flex align-items-center justify-content-center me-1 dropdown-toggle" data-bs-toggle="dropdown" style="margin-right:5px;color:#fff;background-color:#ec5034 !important" aria-expanded="false">
                                HOLIDAY
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" onclick="show_add_modal('modal_div','add_holiday_modal','schedule/add_holiday_modal');" >Add Holiday</a></li>

                                <li><a class="dropdown-item" href="#" onclick="show_edit_modal('modal_div','holiday_list','schedule/holiday_list');" >List Holiday</a></li>
                            </ul>
                        </div>



                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>


            <!-- Loader Div -->
            <div id="custom_loader"></div>
            <div id="error_message" class="text-center my-2"></div>

            <!-- Display Schedule Data -->
            <div id="schedule_cards_div">

            </div>

        </div>
    </div>
</div>

<!-- Include Footer -->
<?php include_once(APPPATH . "Views/footer.php"); ?>



<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>


<script>
    var ui_module = "<?= $module; ?>";

    function resetFilterData() {
        localStorage.setItem('schedule_classroom_filter_val', "");
        localStorage.setItem('schedule_classroom_filter_text', "");
        localStorage.setItem('schedule_start_date_filter_val', "");
        localStorage.setItem('schedule_end_date_filter_val', "");
        setTimeout(function() {
            window.location.reload();
        }, 1000);
    }

    $(document).ready(function() {

        var classroom_filter_val = "";
        var classroom_filter_text = "";

        $("#schedule_start_date").flatpickr({
            dateFormat: "Y-m-d",
            onChange: function(selectedDates) {
                $("#schedule_end_date").flatpickr({
                    dateFormat: "Y-m-d",
                    minDate: new Date(selectedDates),
                    maxDate: new Date(selectedDates).fp_incr(6), // add 7 days
                });
            }
        });


        // Optimized classroom dropdown list
        $(".classroom_select2_dropdown").select2({
            ajax: {
                theme: "bootstrap5",
                type: "POST",
                url: base_url + "/classrooms/optimized_classrooms_list",
                // @returns Data to be directly passed into the request.
                data: function(params) {
                    var queryParameters = {
                        search: params.term, // search term
                        page: params.page
                    };
                    return queryParameters;
                },
                processResults: function(data, params) {
                    console.log(data, params);
                    params.page = params.page || 1;
                    return {
                        results: $.map(data.items, function(item) {
                            return {
                                text: item.name,
                                id: item.id,
                            };
                        }),
                        pagination: {
                            more: params.page * 30 < data.total_count,
                        },
                    };
                },
                // The number of milliseconds to wait for the user to stop typing before
                // issuing the ajax request.
                delay: 250,
                dataType: "json",
            }
        });

        if (localStorage.getItem('schedule_end_date_filter_val') != '') {
            $("#schedule_end_date").val(localStorage.getItem('schedule_end_date_filter_val'));
        }

        if (localStorage.getItem('schedule_start_date_filter_val') != '') {
            $("#schedule_start_date").val(localStorage.getItem('schedule_start_date_filter_val'));
        }


        if (localStorage.getItem('schedule_classroom_filter_val') != '') {
            classroom_filter_val = localStorage.getItem('schedule_classroom_filter_val');
            classroom_filter_text = localStorage.getItem('schedule_classroom_filter_text');
            $(".classroom_select2_dropdown").select2("trigger", "select", {
                data: {
                    id: classroom_filter_val,
                    text: classroom_filter_text
                }
            });
        }

        get_schedule_data();
    });



    function get_schedule_data() {
        $("#error_message").html("");
        toggle_custom_loader(true, "custom_loader");
        var classroom = $("#classroom_filter").val();
        var classroom_name = $("#classroom_filter").select2('data')[0]['text'];
        var schedule_start_date = $("#schedule_start_date").val();
        var schedule_end_date = $("#schedule_end_date").val();


        if (classroom == '' || schedule_start_date == '' || schedule_end_date == '') {
            toggle_custom_loader(false, "custom_loader");
            $("#error_message").html("<div class='default_card'><h4>Please select the classroom</h4></div>");
        } else {

            localStorage.setItem('schedule_classroom_filter_val', classroom);
            localStorage.setItem('schedule_classroom_filter_text', classroom_name);
            localStorage.setItem('schedule_start_date_filter_val', schedule_start_date);
            localStorage.setItem('schedule_end_date_filter_val', schedule_end_date);
            jQuery.ajax({
                url: base_url + '/schedule/fetch_schedule_events',
                type: 'POST',
                dataType: 'json',
                data: {
                    schedule_start_date: schedule_start_date,
                    schedule_end_date: schedule_end_date,
                    classroom: classroom
                },
                success: function(result) {
                    $('#schedule_cards_div').html(format_schedule(result));
                    toggle_custom_loader(false, "custom_loader");
                    $("#error_message").html("");
                }
            });
        }
    }

    function format_schedule(data) {
        var html = "";
        var date = new Date();
        var current_year = date.getFullYear();
        var current_month = ("0" + (date.getMonth() + 1)).slice(-2);
        var current_day = ("0" + date.getDate()).slice(-2);
        var CurrentDate = current_year + "-" + current_month + "-" + current_day;

        var options = {
            weekday: 'long',
            month: 'long',
            day: 'numeric'
        };

        console.log(CurrentDate);
        if (data != null) {
            console.log(data);
            if (data.length == 0) {
                html = "<div class='default_card'><h4>No schedule available</h4></div>";
            } else {
                var week_date = "";
                var classroom = $("#classroom_filter").val();
                var schedule_date = $("#schedule_date").val();

                html = html + "<div class='kanban'>";
                html = html + "<div class='kanban-container'>";
                $.each(data, function(objIndex, obj) {
                    if (week_date != obj.date) {
                        if (objIndex != 0) {
                            if (ui_module == 'Schedule') {
                                html = html + "<li>";
                                html = html + "<div>";
                                html = html + "<button class='btn btn-outline-primary' onclick=" +
                                    "show_edit_modal('modal_div','add_schedule_modal','/schedule/add_schedule_modal/" +
                                    classroom + "/" + week_date + "');" +
                                    "> Add Schedule</button>";
                                html = html + "</div>";
                                html = html + "</li>";
                            }
                            html = html + "</ul></div></div>";
                        }

                        var formatted_date = new Date(obj.date);
                        formatted_date = formatted_date.toLocaleDateString("en-US", options)
                        html = html + "<div class='kanban-column'>";
                        html = html + "<div class='kanban-column-header'>" + formatted_date + "</div>";
                        html = html + "<div class='kanban-column'><ul class='kanban-column-list'>";
                        week_date = obj.date;
                    }

                    if (obj.title != null) {
                        html = html + "<li class='schedule_card position-relative'>";
                        html = html + "<div class='badge subject_badge'>" + obj.subject_name.toUpperCase() + "</div>";
                        html = html + "<div class='card_head'>";
                        html = html + "<div class='card_title'>" + obj.title.toUpperCase() + "</div>";
                        html = html + "</div>";
                        html = html + "<div class='card_body'>";
                        html = html + "<div class='card_supporting_text'>Classroom: " + obj.package_name.toUpperCase() + "</div>";


                        html = html + "<div class='card_supporting_text'>" + formatAMPM(obj.starts_at) + "-" + formatAMPM(obj.ends_at) + "</div>";


                        html = html + "<div class='card_supporting_text'>Duration: ";
                        html = html + secondsToHms(obj.duration);
                        html = html + "</div>";

                        if (obj.total_students != null) {
                            html = html + "<div class='card_supporting_text'>Attendance: ";
                            if (obj.present_students != null) {
                                html = html + obj.present_students + "/" + obj.total_students;
                            } else {
                                html = html + obj.total_students;
                            }

                            html = html + "</div>";
                        }

                        if (ui_module == 'Schedule') {
                            html = html + "<div class='d-flex justify-content-between my-2'><span class='material-icons schedule_btns schedule_edit_btn' onclick=" +
                                "show_edit_modal('modal_div','update_schedule_modal','/schedule/update_schedule_modal/" +
                                obj.id +
                                "');" +
                                ">edit</span><span class='material-icons schedule_btns schedule_delete_btn  mx-2' onclick=" +
                                "show_edit_modal('modal_div','delete_schedule_modal','/schedule/delete_schedule_modal/" +
                                obj.id +
                                "');" +
                                ">delete</span></div>";
                        } else {
                            if (obj.date <= CurrentDate) {
                                if (obj.total_students == null) {
                                    html = html + "<div class='d-flex justify-content-between my-2'><a class='btn btn-sm btn-primary mx-2' href='" +
                                        base_url + "/attendance/take_attendance/" +
                                        obj.id + "/" + obj.date + "'>Take attendance</a></div>";
                                } else {
                                    html = html + "<div class='d-flex justify-content-between my-2'><a class='material-icons schedule_btns schedule_view_btn' target='_blank' href='" +
                                        base_url + "/attendance/overview/" +
                                        obj.id + "/" + obj.date + "'>visibility</a></div>";
                                }
                            }
                        }

                        html = html + "</div>";
                        html = html + "</li>";
                    }

                });
                if (ui_module == 'Schedule') {
                    html = html + "<li>";
                    html = html + "<div>";
                    html = html + "<button class='btn btn-outline-primary' onclick=" +
                        "show_edit_modal('modal_div','add_schedule_modal','/schedule/add_schedule_modal/" +
                        classroom + "/" + week_date + "');" +
                        "> Add Schedule</button>";
                    html = html + "</div>";
                    html = html + "</li>"
                }
                html = html + "</ul></div></div>";
                html = html + "</div></div>";
            }
        } else {
            html = "<div class='default_card'><h4>No schedule available</h4></div>";
        }
        return html;
    }
</script>