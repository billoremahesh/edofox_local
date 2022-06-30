<!-- Include Header -->
<?php include_once(APPPATH . "Views/header.php"); ?>

<!-- Custom CSS -->
<link href="<?php echo base_url('assets/css/tickets/overview.css'); ?>" rel="stylesheet">

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

    <!-- Content Row -->
    <div class="card shadow mb-4">
        <div class="card-header">

            <div class="d-flex flex-row mb-2">
                <div>
                    <label for="priority" class="form-label"> Priority </label>
                    <select class="form-select form-select-sm custom_drp_single priority_dropdown mr-2" name="priority" id="priority">
                        <option value="All"> All </option>
                        <option value="Low"> Low </option>
                        <option value="Medium"> Medium </option>
                        <option value="High"> High </option>
                        <option value="Critical"> Critical </option>
                    </select>
                </div>

                <div>
                    <label for="assign" class="form-label"> Assign </label>
                    <select class="form-select form-select-sm custom_drp_single assign_dropdown" name="assign" id="assign" style="max-width: 150px;">
                        <option value="">Select Staff</option>
                        <?php
                        if (!empty($staff_assign_data)) {
                            foreach ($staff_assign_data as $staff_data) { ?>
                                <option value="<?php echo $staff_data['id']; ?>"><?php echo $staff_data['username']; ?></option>
                        <?php   }
                        }
                        ?>
                    </select>
                </div>

                <div>
                    <!-- Moved Datatable Page Length Menu -->
                    <div style="margin-left: 16px;" id="dataTables_length_div"></div>
                </div>

                <div class="d-flex" style="flex-grow: 2;justify-content: flex-end;">
                    <div>
                        <!-- Moved Datatable Search box -->
                        <div id="dataTables_search_box_div"></div>
                    </div>
                </div>
            </div>

        </div>

        <div class="card-body">
            <div class="table-responsive table_custom">
                <?php $table =  "<table class='table table-bordered table-sm' id='ticket_overview_table' width='100%' cellspacing='0'>
                    <thead>
                        <tr>
                            <th> #Ticket </th>
                            <th> Raised By </th>
                            <th> Institute </th>
                            <th> Assign To </th>
                            <th> Reason </th>
                            <th> Created at </th>
                            <th> Updated at </th>
                            <th> Priority </th>
                            <th> Status </th>
                            <th> Actions </th>
                        </tr>
                    </thead>";
                $table .= "<tbody>";
                if (!empty($tickets_data)) {
                    $i = 1;
                    foreach ($tickets_data as $ticket_data) {
                        if (ucwords($ticket_data['priority']) == "Low") {
                            $priority_class = "class='round_box_cutom_sucess_bg'";
                        } else if (ucwords($ticket_data['priority']) == "Medium") {
                            $priority_class = "class='round_box_cutom_medium_bg'";
                        } else if (ucwords($ticket_data['priority']) == "High") {
                            $priority_class = "class='round_box_cutom_danger_bg'";
                        } else {
                            $priority_class = "class='round_box_cutom_warning_bg'";
                        }
                        $table .= "
                                <tr>
                                <td>{$ticket_data['ticket_number']}</td>
                                <td>{$ticket_data['student_name']}</td>
                                <td>{$ticket_data['institute_name']}</td>
                                <td>{$ticket_data['username']}</td>
                                <td>{$ticket_data['reason_name']}</td>
                                <td>{$ticket_data['created_at']}</td>
                                <td>{$ticket_data['updated_at']}</td>
                                <td><label " . $priority_class . "> " . ucwords($ticket_data['priority']) . "</label></td>";

                        if ($ticket_data['status'] == "Resolved") :
                            $table .= "<td> <label class='round_box_cutom_sucess_bg'>" . ucwords($ticket_data['status']) . "</label> </td>";
                        elseif ($ticket_data['status'] == "pending") :
                            $table .= "<td> <label class='round_box_cutom_danger_bg'>" . ucwords($ticket_data['status']) . "</label> </td>";
                        else :
                            $table .= "<td> <label class='round_box_cutom_process_bg'>" . ucwords($ticket_data['status']) . "</label> </td>";
                        endif;

                        $table .= "<td>
                                    <a class='material_icon_custom_div' href='Tickets/edit_ticket/{$ticket_data['id']}'>
                                        <i class='material_button_edit_icon material-icons'>edit</i>
                                    </a>
                                    <a class='material_icon_custom_div' href='javascript:void(0)' onclick='delete_ticket({$ticket_data['id']})'>
                                        <i class='material_button_delete_icon material-icons'>delete</i>
                                    </a>";
                        if ($ticket_data['local_test_data'] != '' && $ticket_data['local_test_data'] != 'NULL') :
                            $table .= "<a class='material_icon_custom_div' href='javascript:void(0)' onclick='download_data({$ticket_data['local_test_data']},{$ticket_data['student_id']})' id='downloadAnchorElem'>
                                        <i class='material_button_delete_icon material-icons'>download</i>
                                    </a>";
                        endif;
                        $table .= "</td></tr>";
                        $i++;

                        $table .= "<script type=\"text/javascript\">
                                    function delete_ticket(ticket_id){
                                        $.ajax({
                                            url : 'models/delete_ticket.php',
                                            type : 'POST',
                                            data : {ticket_id : ticket_id},
                                            success:function(result){
                                                if(result == 1){
                                                    window.location = 'tickets.php?success=1&q_message= Ticket updated Successfully.';
                                                }else{
                                                    window.location = 'tickets.php?success=0&q_message= Error in updating Ticket.';
                                                }
                                            }
                                        });
                                    }
                                    </script>";
                    }
                }

                $table .= "</tbody>
                </table>";
                echo $table;
                ?>

            </div>
            <div id="ticket_overview_tableExportGroup" class="export-icon-group" style="display: none">
                <img class="export-icon" onclick='dtExport("ticket_overview_table_wrapper","excel");' src="img/icons/download-excel-512x512.png" alt='Excel' height='16' width='16'>
                <img class="export-icon" onclick='dtExport("ticket_overview_table_wrapper","pdf");' src="img/icons/download-pdf-512x512.png" alt='PDF' height='16' width='16'>
            </div>
        </div>
    </div>

</div>
<!-- /.container-fluid -->

<!-- Include Footer -->
<?php include_once(APPPATH . "Views/footer.php"); ?>

<script>
    $(document).ready(function() {
        var ticket_overview_table = $('#ticket_overview_table').DataTable({
            "pageLength": 25,
            fixedHeader: true,
            responsive: true,
            "order": [3, 'desc'],
            "columnDefs": [{
                "targets": 6,
                "orderable": false,
            }],
            dom: 'Bfrtip',
            buttons: [{
                    extend: 'pdf',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5],
                        modifier: {
                            page: 'all',
                            search: 'none'
                        }
                    }
                },
                {
                    extend: 'excel',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5],
                        modifier: {
                            page: 'all',
                            search: 'none'
                        }
                    }

                }
            ],
        });

        // Moved Datatable Search box and Page length option
        $("#dataTables_search_box_div").html($("#ticket_overview_table_filter"));
        $("#dataTables_length_div").html($("#ticket_overview_table_length"));


        // Priority dropdown datatable
        $('.priority_dropdown').on('change', function() {
            var priority_dropdown = document.getElementById("priority").value;
            if (priority_dropdown == "All") {
                ticket_overview_table.column(5).search("").draw();
            } else {
                ticket_overview_table.column(5).search(priority_dropdown).draw();
            }
        });

        // Staff dropdown datatable
        $('.assign_dropdown').on('change', function() {
            var assign_dropdown = document.getElementById("assign");
            var assign_dropdown_value = assign_dropdown.options[assign_dropdown.selectedIndex].value;
            var assign_dropdown_text = assign_dropdown.options[assign_dropdown.selectedIndex].text;
            if (assign_dropdown_value != "") {
                ticket_overview_table.column(1).search(assign_dropdown_text).draw();
            } else {
                ticket_overview_table.column(1).search("").draw();
            }
        });
    });
</script>