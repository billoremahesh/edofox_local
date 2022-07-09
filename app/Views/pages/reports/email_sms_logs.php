<!-- Include Header -->
<?php include_once(APPPATH . "Views/header.php"); ?>

<!-- Custom CSS -->
<link href="<?php echo base_url('assets/css/reports/email_sms_logs.css?v=20220707'); ?>" rel="stylesheet">

<div id="content">
    <div class="container-fluid mt-4">

        <div class="flex-container-column">
            <div>
                <label class="h5 text-gray-800 text-uppercase"> <?= $title; ?> </h5>
            </div>
            <div class="breadcrumb_div" aria-label="breadcrumb">
                <ol class="breadcrumb_custom">
                    <li class="breadcrumb_item"><a href="<?php echo base_url('/home'); ?>"> Home </a></li>
                    <li class="breadcrumb_item" aria-current="page"><a href="<?php echo base_url('reports'); ?>">Reports</a></li>
                    <li class="breadcrumb_item active" aria-current="page"> <?= $title; ?> </li>
                </ol>
            </div>
        </div>

        <div class="card shadow p-4">

            <div class="d-flex justify-content-between my-1">
                <div class="text-center">
                    <badge class="badge bg-info fw-bold fs-6" id="total_student_login_sessions"></badge>
                </div>



                <div class="d-flex flex-row-reverse">

                    <div class="mx-2">
                        <select class="form-select channel_filter" id="channel_filter" style="width:150px">
                            <option value=""> All </option>
                            <option value="sms"> SMS </option>
                            <option value="email"> Email </option>
                            <option value="whatsapp"> WhatsApp </option>
                        </select>
                    </div>

                    <div class="input-group input-group-sm flex-nowrap search_input_wrap">

                        <span class="input-group-text" id="addon-search">
                            <i onclick="resetDatatable()" data-bs-toggle="tooltip" title="Reset saved table settings like hidden columns, search, etc" class='material-icons' id="refresh_icon">refresh</i>
                        </span>

                        <input class="form-control text-black-50" id='searchbox' autocomplete="off" style='width: 130px; color: blue; font-weight: bold;' placeholder="Search" name="srch-term" type="text" value="<?= (isset($_SESSION['email_sms_logs_session_search']) && !empty($_SESSION['email_sms_logs_session_search'])) ? $_SESSION['email_sms_logs_session_search'] : ''; ?>" placeholder="Search" aria-label="Search" aria-describedby="addon-search">

                    </div>


                </div>
            </div>


            <div class="table-responsive table_custom">
                <table class="table table-bordered table-hover table-sm" id="emailSmsTble" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th> # </th>
                            <th> Student Name </th>
                            <th> Mobile Number </th>
                            <th> Classroom/ Exam </th>
                            <th> Message </th>
                            <th> Channel </th>
                            <th> Status </th>
                            <th> Date </th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>



        </div>
    </div>
</div>




<!-- Include Footer -->
<?php include_once(APPPATH . "Views/footer.php"); ?>

<script>
    var emailSmsTble;
    var institute_id = "<?= $instituteID; ?>";
    // To reset all the parameters in the datatable which are saved using stateSave
    function resetDatatable() {
        // Resetting the filter values
        $("#channel_filter").val("");
        $("#searchbox").val("");
        emailSmsTble.draw();


        // REF: https://datatables.net/forums/discussion/53726/table-state-clear-does-not-clear-saved-state-how-do-i-remove-specified-filters-sorts-here
        emailSmsTble.state.clear(); // 1a - Clear State
        emailSmsTble.destroy(); // 1b - Destroy

        setTimeout(function() {
            window.location.reload();
        }, 1000);
    }

    $(document).ready(function() {
        emailSmsTble = $('#emailSmsTble').DataTable({
            stateSave: true,
            "columnDefs": [{
                "targets": [1, 2, 3, 4, 5, 6, 7],
                "orderable": false,
            }, {
                "targets": -1,
                "class": 'btn_col'
            }],
            "order": [
                [0, "asc"]
            ],
            dom: 'Bflrtip',
            buttons: [{
                extend: 'colvis',
                //https://datatables.net/forums/discussion/50751/hide-several-columns-for-colvis-button-list
                columns: ':gt(0)',
                text: "Toggle Columns"
            }],
            "searching": true,
            "paging": true,
            "pageLength": 25,
            "bLengthChange": false,
            "bInfo": false,
            "processing": true,
            "serverSide": true,
            "ajax": {
                'url': base_url + "/reports/load_email_sms_logs",
                "type": "POST",
                "data": function(d) {
                    d.searchbox = $("#searchbox").val(),
                        d.institute_id = institute_id,
                        d.channel_filter = $('#channel_filter').val()
                }
            },
            language: {
                search: "",
                processing: '<i class="fas fa-atom fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>'
            },
            'drawCallback': function(data) {
                $('#total_student_login_sessions').html("Total Email/ SMS Sent: " + data.json.recordsFiltered);
                initializeTooltip();
            },
            "dataSrc": "Data",
            "fnRowCallback": function(nRow, aData, iDisplayIndex) {
                //console.log(userdatatable.page.info());
            }
        });

        // Onchange of custom filters
        $(".channel_filter").change(function() {
            emailSmsTble.draw();
        });

        $("#searchbox").keyup(function() {
            emailSmsTble.draw();
        });
    });



    function waitForElementToDisplay(selector, time, counter) {
        if (counter > 6) {
            return;
        }
        if (document.querySelector(selector) != null) {
            $("#emailSmsTble_filter").prepend($("#emailSmsTbleExportGroup"));
            $("#emailSmsTbleExportGroup").show();
            return;
        } else {
            setTimeout(function() {
                waitForElementToDisplay(selector, time, counter + 1);
            }, time);
        }
    }

    $(document).ready(function() {
        waitForElementToDisplay("#emailSmsTble_filter", 1000, 1);
    });
</script>