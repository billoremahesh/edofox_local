<!-- Include Header -->
<?php include_once(APPPATH . "Views/header.php"); ?>

<!-- Custom CSS -->
<link href="<?php echo base_url('assets/css/sales_team/overview.css?v=20220422'); ?>" rel="stylesheet">


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


        <div class="card shadow p-4">


            <div class="d-flex justify-content-between my-1">

                <div class="text-center">
                    <badge class="badge bg-info fw-bold fs-6" id="total_classrooms">
                        <?= "Total Team Members: " . count($sales_team); ?>
                    </badge>
                </div>

                <div class="d-flex flex-row-reverse">

                    <div class="input-group input-group-sm flex-nowrap search_input_wrap">

                        <span class="input-group-text" id="addon-search">
                            <i onclick="resetDatatable()" data-bs-toggle="tooltip" title="Reset saved table settings like hidden columns, search, etc" class='material-icons' id="refresh_icon">refresh</i>
                        </span>

                        <input class="form-control text-black-50" id='searchbox' autocomplete="off" style='width: 130px; color: blue; font-weight: bold;' placeholder="Search" name="srch-term" type="text" placeholder="Search" aria-label="Search" aria-describedby="addon-search">

                    </div>

                    <?php if (in_array("manage_sales_team", session()->get('perms')) or in_array("all_super_admin_perms", session()->get('perms'))) :  ?>
                        <div class="mx-2">
                            <a href="<?= base_url('/SuperAdmins/add'); ?>" data-toggle='tooltip' title='Add New Staff'>
                                <span class="material-icons action_button_plus_icon">
                                    add
                                </span>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="table-responsive table_custom">
                <table class="table table-bordered table-hover table-sm" id="SuperAdminsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th> # </th>
                            <th> Name </th>
                            <th> Email </th>
                            <th> Mobile Number </th>
                            <th> Username </th>
                            <th> Institues Account Manager </th>
                            <th> Role </th>
                            <th class="not_to_export not_to_print"> Actions </th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php
                        if (!empty($sales_team)) {
                            $i = 1;
                            foreach ($sales_team as $row) {
                                $staff_id = encrypt_string($row['id']);

                                echo "<tr>";
                                echo "<td>" . $i . "</td>";
                                echo "<td>" . $row['name'] . "</td>";
                                echo "<td>" . $row['email'] . "</td>";
                                echo "<td>" . $row['mobile_number'] . "</td>";
                                echo "<td>" . $row['username'] . "</td>";
                                echo "<td>" . $row['institute_names'] . "</td>";
                                echo "<td>" . $row['role'] . "</td>";

                        ?>
                                <td>

                                    <div class='dropdown'><button class='btn btn-default dropdown-toggle more_option_button' type='button' id='classroomDropdownMenu' data-bs-toggle='dropdown' data-bs-auto-close='outside' aria-expanded='false'><i class='fa fa-ellipsis-h' aria-hidden='true'></i>
                                        </button>
                                        <ul class='dropdown-menu dropdown-menu-end' aria-labelledby='classroomDropdownMenu'>

                                            <?php if (in_array("manage_sales_team", session()->get('perms')) or in_array("all_super_admin_perms", session()->get('perms'))) :  ?>
                                                <li>
                                                    <a class='btn btn-sm' href="<?= base_url('SuperAdmins/update/' . $staff_id); ?>">Update Details</a>
                                                </li>

                                                <li role='separator' class='dropdown-divider'></li>
                                                <li>
                                                    <button class='btn btn-sm' onclick="show_edit_modal('modal_div','delete_super_admin_details_modal','SuperAdmins/delete_super_admin_details_modal/<?php echo $staff_id; ?>');"> Delete Sales Team </button>
                                                </li>
                                            <?php endif; ?>

                                        </ul>
                                    </div>

                                </td>

                        <?php
                                echo "</tr>";
                                $i++;
                            }
                        }

                        ?>
                    </tbody>
                </table>
            </div>



            <div id="SuperAdminsTableExportGroup" class="export-icon-group" style="display: none">
                <img class="export-icon" onclick='dtExport("SuperAdminsTable_wrapper","excel");' src="<?php echo base_url('assets/img/icons/download-excel-512x512.png'); ?>" alt='Excel' height='16' width='16'>
            </div>

        </div>


    </div>
</div>

<!-- Include Footer -->
<?php include_once(APPPATH . "Views/footer.php"); ?>


<script>
    var SuperAdminsTable;

    // To reset all the parameters in the datatable which are saved using stateSave
    function resetDatatable() {
        // Resetting the filter values
        $("#searchbox").val("");
        localStorage.setItem('sales_team_datatable_search_value', "");
        SuperAdminsTable.draw();


        // REF: https://datatables.net/forums/discussion/53726/table-state-clear-does-not-clear-saved-state-how-do-i-remove-specified-filters-sorts-here
        SuperAdminsTable.state.clear(); // 1a - Clear State
        SuperAdminsTable.destroy(); // 1b - Destroy

        setTimeout(function() {
            window.location.reload();
        }, 1000);
    }

    $(document).ready(function() {

        var sales_team_datatable_search_value = localStorage.getItem('sales_team_datatable_search_value');
        $("#searchbox").val(sales_team_datatable_search_value);


        SuperAdminsTable = $('#SuperAdminsTable').DataTable({
            stateSave: true,
            "columnDefs": [{
                "targets": [6],
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
                // not_export class is used to hide excel columns. 
                "exportOptions": {
                    "columns": ':visible:not(.not_to_export)'
                },
                messageTop: "Sales Team"
            }, {
                extend: 'print',
                exportOptions: {
                    columns: ':visible:not(.not_to_print)'
                },
                title: "Sales Team",
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
            language: {
                search: "",
                processing: '<i class="fas fa-atom fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>'
            },
            stateSaveCallback: function(settings, data) {
                if (data != null && data.search != null && data.search.search != null) {
                    localStorage.setItem('sales_team_datatable_search_value', data.search.search);
                } else {
                    localStorage.setItem('sales_team_datatable_search_value', "");
                }
            }
        });

        if (sales_team_datatable_search_value != '' && sales_team_datatable_search_value != null) {
            SuperAdminsTable.search(sales_team_datatable_search_value).draw();
        }
        $("#searchbox").keyup(function() {
            SuperAdminsTable.search(this.value).draw();
        });

    });


    function waitForElementToDisplay(selector, time, counter) {
        if (counter > 6) {
            return;
        }
        if (document.querySelector(selector) != null) {
            $("#SuperAdminsTable_filter").prepend($("#SuperAdminsTableExportGroup"));
            $("#SuperAdminsTableExportGroup").show();
            return;
        } else {
            setTimeout(function() {
                waitForElementToDisplay(selector, time, counter + 1);
            }, time);
        }
    }

    $(document).ready(function() {
        waitForElementToDisplay("#SuperAdminsTable_filter", 1000, 1);
    });
</script>