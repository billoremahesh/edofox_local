<!-- Include Header -->
<?php include_once(APPPATH . "Views/header.php"); ?>

<!-- Custom CSS -->
<link href="<?php echo base_url('assets/css/classrooms/overview.css?v=20220331'); ?>" rel="stylesheet">


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





        <div class="bg-white rounded shadow p-4">

            <div class="d-flex justify-content-between my-1">

                <div class="text-center">
                    <badge class="badge bg-info fw-bold fs-6" id="total_classrooms"></badge>
                </div>

                <div class="d-flex flex-row-reverse">

                    <div class="input-group input-group-sm flex-nowrap search_input_wrap">

                        <span class="input-group-text" id="addon-search">
                            <i onclick="resetDatatable()" data-bs-toggle="tooltip" title="Reset saved table settings like hidden columns, search, etc" class='material-icons' id="refresh_icon">refresh</i>
                        </span>

                        <input class="form-control text-black-50" id='searchbox' autocomplete="off" style='width: 130px; color: blue; font-weight: bold;' placeholder="Search" name="srch-term" type="text" value="<?= (isset($_SESSION['classroom_search_string']) && !empty($_SESSION['classroom_search_string'])) ? $_SESSION['classroom_search_string'] : ''; ?>" placeholder="Search" aria-label="Search" aria-describedby="addon-search">

                    </div>

                    <div class="mx-2">
                        <select class="form-select classroom_status_filter" id="classroom_status" style="width:150px">
                            <option value="active"> Active </option>
                            <option value="disabled"> Disabled </option>
                        </select>
                    </div>


                    <?php if (in_array("manage_classrooms", session()->get('perms')) or in_array("all_perms", session()->get('perms'))) :  ?>
                        <a href="#" onclick="show_add_modal('modal_div','add_syllabus_modal','syllabus/add_syllabus_modal/syllabus');" data-toggle='tooltip' title='Add New Syllabus'>
                            <span class="material-icons action_button_plus_icon">
                                add
                            </span>
                        </a>
                    <?php endif; ?>
 
                </div>
            </div>


            <div class="table-responsive table_custom">
                <table class="table table-bordered" id="classroomsListTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th> # </th>
                            <th> Syllabus </th>
                            <th> Subject Name </th> 
                            <th> Description </th> 
                            <th class="not_to_export not_to_print"> Actions </th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Include Footer -->
<?php include_once(APPPATH . "Views/footer.php"); ?>


<script>
    var classroomsListTable;

    // To reset all the parameters in the datatable which are saved using stateSave
    function resetDatatable() {
        // Resetting the filter values
        $("#classroom_status").val("");
        $("#searchbox").val("");
        classroomsListTable.draw();


        // REF: https://datatables.net/forums/discussion/53726/table-state-clear-does-not-clear-saved-state-how-do-i-remove-specified-filters-sorts-here
        classroomsListTable.state.clear(); // 1a - Clear State
        classroomsListTable.destroy(); // 1b - Destroy

        setTimeout(function() {
            window.location.reload();
        }, 1000);
    }

    $(document).ready(function() {
        classroomsListTable = $('#classroomsListTable').DataTable({
            stateSave: true,
            "columnDefs": [{
                "targets": [3, 4],
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
            }, {
                "extend": 'excel',
                "titleAttr": 'Excel',
                "action": newexportaction,
                // not_export class is used to hide excel columns. 
                "exportOptions": {
                    "columns": ':visible:not(.not_to_export)'
                },
                messageTop: "Syllabus"
            }, {
                extend: 'print',
                exportOptions: {
                    columns: ':visible:not(.not_to_print)'
                },
                title: "Syllabus ",
                customize: function(win) {
                    $(win.document.body).find('h1').css('text-align', 'center');
                    $(win.document.body).css('font-size', '9px');
                    $(win.document.body).find('td').css('padding', '0px');
                    $(win.document.body).find('td').css('padding-left', '2px');
                }
            }],
            "searching": true,
            "paging": true,
            "pageLength": 25,
            "bLengthChange": false,
            "bInfo": false,
            "processing": true,
            "serverSide": true,
            "ajax": {
                'url': base_url + "/syllabus/load_classrooms",
                "type": "POST",
                "data": function(d) {
                    d.classroom_status = $('#classroom_status').val(),
                        d.searchbox = $("#searchbox").val()
                }
            },
            language: {
                search: "",
                processing: '<i class="fas fa-atom fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>'
            },
            'drawCallback': function(data) {
                console.log(data.json);
                $('#total_classrooms').html("Total Syllabus: " + data.json.recordsFiltered);
                initializeTooltip();
            },
            "dataSrc": "Data",
            "fnRowCallback": function(nRow, aData, iDisplayIndex) {
                //console.log(userdatatable.page.info());
            }
        });

        // Onchange of custom filters
        $(".classroom_status_filter").change(function() {
            classroomsListTable.draw();
        });

        $("#searchbox").keyup(function() {
            classroomsListTable.draw();
        });


    });



    function waitForElementToDisplay(selector, time, counter) {
        if (counter > 6) {
            return;
        }
        if (document.querySelector(selector) != null) {
            $("#classroomsListTable_filter").prepend($("#classroomsListTableExportGroup"));
            $("#classroomsListTableExportGroup").show();
            return;
        } else {
            setTimeout(function() {
                waitForElementToDisplay(selector, time, counter + 1);
            }, time);
        }
    }

    $(document).ready(function() {
        waitForElementToDisplay("#classroomsListTable_filter", 1000, 1);
    });
</script>