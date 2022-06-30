<!-- Include Header -->
<?php include_once(APPPATH . "Views/header.php"); ?>

<!-- Custom CSS -->
<link href="<?php echo base_url('assets/css/subscriptions/overview.css?v=20220422'); ?>" rel="stylesheet">


<div id="content">
    <div class="container-fluid mt-4">

        <div class="flex-container-column">
            <div>
                <label class="h5 text-gray-800 text-uppercase"> <?= $title; ?> </h5>
            </div>
            <div class="breadcrumb_div" aria-label="breadcrumb">
                <ol class="breadcrumb_custom">
                    <li class="breadcrumb_item"><a href="<?php echo base_url('/home'); ?>"> Home </a></li>
                    <?php
                    $userType = session()->get('user_type');
                    if ($userType == "super_admin") :
                    ?>
                        <li class="breadcrumb_item"><a href="<?php echo base_url('/institutes'); ?>"> Institutes </a></li>
                    <?php endif; ?>
                    <li class="breadcrumb_item active" aria-current="page"> <?= $title; ?> </li>
                </ol>
            </div>
        </div>


        <div class="card shadow p-4">

            <div class="text-center">
                <h3>Institute Name: <?= $institute_data['institute_name']; ?></h3>
            </div>

            <div class="d-flex justify-content-between my-1">

                <div class="text-center">
                    <badge class="badge bg-info fw-bold fs-6" id="total_classrooms">
                        <?= "Total Subscriptions: " . count($institute_subscriptions); ?>
                    </badge>
                </div>

                <div class="d-flex flex-row-reverse">

                    <div class="input-group input-group-sm flex-nowrap search_input_wrap">

                        <span class="input-group-text" id="addon-search">
                            <i onclick="resetDatatable()" data-bs-toggle="tooltip" title="Reset saved table settings like hidden columns, search, etc" class='material-icons' id="refresh_icon">refresh</i>
                        </span>

                        <input class="form-control text-black-50" id='searchbox' autocomplete="off" style='width: 130px; color: blue; font-weight: bold;' placeholder="Search" name="srch-term" type="text" placeholder="Search" aria-label="Search" aria-describedby="addon-search">

                    </div>

                    <?php if (in_array("manage_subscriptions", session()->get('perms')) or in_array("all_super_admin_perms", session()->get('perms'))) :  ?>
                        <div class="mx-2">
                            <a href="<?= base_url('/subscriptions/new_subscription/' . $institute_id); ?>" data-toggle='tooltip' title='Add Subscription'>
                                <span class="material-icons action_button_plus_icon">
                                    add
                                </span>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="table-responsive table_custom">
                <table class="table table-bordered table-hover table-sm" id="subscriptionsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th> # </th>
                            <th> Plan Name </th>
                            <th> Plan Type </th>
                            <th> Next Invoice Date </th>
                            <th> Status </th>
                            <th> Created Date </th>
                            <th> Created By </th>
                            <th> Last Updated </th>
                            <th class='text-right'> Amount </th>
                            <th class='text-right'> Discount </th>
                            <th class='text-right'> Students </th>
                            <th class="not_to_export not_to_print"> Actions </th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php
                        if (!empty($institute_subscriptions)) {
                            $i = 1;
                            foreach ($institute_subscriptions as $row) {
                                $row_id = encrypt_string($row['id']);
                                $public_id = $row['public_id'];

                                echo "<tr>";
                                echo "<td>" . $i . "</td>";
                                echo "<td>" . $row['plan_name'] . "</td>";
                                echo "<td>" . $row['plan_type'] . "</td>";
                                echo "<td>" . $row['next_invoice_date'] . "</td>";
                                echo "<td>" . $row['status'] . "</td>";
                                echo "<td>" . $row['created_date'] . "</td>";
                                echo "<td>" . $row['created_date'] . "</td>";
                                echo "<td>" . $row['last_updated'] . "</td>";
                                echo "<td class='text-right'>" . $row['amount'] . "</td>";
                                echo "<td class='text-right'>" . $row['discount'] . "</td>";
                                echo "<td class='text-right'>" . $row['no_of_students'] . "</td>";
                        ?>
                                <td>
                                    <?php if ($row['status'] != "Cancelled") :  ?>
                                        <a class='btn btn-sm' target="_blank" href="<?= base_url('subscriptions/proposal/' . $public_id); ?>" data-toggle='tooltip' title='Proposal'>
                                            <span class="material-icons">
                                                receipt_long
                                            </span>
                                        </a>

                                        <?php

                                        $manual_subscription_check = 0;

                                        if ($row['manual_plan'] == 1) {
                                            $manual_subscription_check = 1;
                                        }

                                        if (in_array("all_super_admin_perms", session()->get('perms'))) {
                                            $manual_subscription_check = 0;
                                        }

                                        ?>

                                        <?php if (in_array("manage_subscriptions", session()->get('perms')) or in_array("all_super_admin_perms", session()->get('perms'))) :  ?>
                                            <?php if (check_active_invoice_subscription($row['id'])) : ?>
                                                <?php if ($manual_subscription_check == 0) : ?>
                                                    <a class='btn btn-sm' href="<?= base_url('Subscriptions/update_subscription/' . $row_id); ?>" data-toggle='tooltip' title='Update Subscription Details'>
                                                        <span class="material-icons">
                                                            edit
                                                        </span>
                                                    </a>
                                                <?php endif; ?>
                                            <?php else : ?>
                                                <a class='btn btn-sm' href="">
                                                    <span class="material-icons" data-toggle='tooltip' title="Yearly subscription already paid, so you not able update subscription till next invoice">
                                                        info
                                                    </span>
                                                </a>
                                            <?php endif; ?>
                                        <?php endif; ?>


                                        <button class='btn btn-sm' onclick="show_edit_modal('modal_div','cancel_subscription_modal','Subscriptions/cancel_subscription_modal/<?php echo $row_id; ?>');" data-toggle='tooltip' title='Cancel Subscription'>
                                            <span class="material-icons">
                                                delete
                                            </span>
                                        </button>
                                    <?php endif; ?>

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



            <div id="subscriptionsTableExportGroup" class="export-icon-group" style="display: none">
                <img class="export-icon" onclick='dtExport("subscriptionsTable_wrapper","excel");' src="<?php echo base_url('assets/img/icons/download-excel-512x512.png'); ?>" alt='Excel' height='16' width='16'>
            </div>

        </div>


    </div>
</div>

<!-- Include Footer -->
<?php include_once(APPPATH . "Views/footer.php"); ?>


<script>
    var subscriptionsTable;

    // To reset all the parameters in the datatable which are saved using stateSave
    function resetDatatable() {
        // Resetting the filter values
        $("#searchbox").val("");
        localStorage.setItem('subscriptions_datatable_search_value', "");
        subscriptionsTable.draw();


        // REF: https://datatables.net/forums/discussion/53726/table-state-clear-does-not-clear-saved-state-how-do-i-remove-specified-filters-sorts-here
        subscriptionsTable.state.clear(); // 1a - Clear State
        subscriptionsTable.destroy(); // 1b - Destroy

        setTimeout(function() {
            window.location.reload();
        }, 1000);
    }

    $(document).ready(function() {

        var subscriptions_datatable_search_value = localStorage.getItem('subscriptions_datatable_search_value');
        $("#searchbox").val(subscriptions_datatable_search_value);


        subscriptionsTable = $('#subscriptionsTable').DataTable({
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
                messageTop: "Subscriptions"
            }, {
                extend: 'print',
                exportOptions: {
                    columns: ':visible:not(.not_to_print)'
                },
                title: "Subscriptions",
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
                if (data != null && data.search != null && data.search.search != null && data.search.search != "null") {
                    localStorage.setItem('subscriptions_datatable_search_value', data.search.search);
                } else {
                    localStorage.setItem('subscriptions_datatable_search_value', "");
                }
            }
        });

        if (subscriptions_datatable_search_value != '' && subscriptions_datatable_search_value != null) {
            subscriptionsTable.search(subscriptions_datatable_search_value).draw();
        }
        $("#searchbox").keyup(function() {
            subscriptionsTable.search(this.value).draw();
        });

    });


    function waitForElementToDisplay(selector, time, counter) {
        if (counter > 6) {
            return;
        }
        if (document.querySelector(selector) != null) {
            $("#subscriptionsTable_filter").prepend($("#subscriptionsTableExportGroup"));
            $("#subscriptionsTableExportGroup").show();
            return;
        } else {
            setTimeout(function() {
                waitForElementToDisplay(selector, time, counter + 1);
            }, time);
        }
    }

    $(document).ready(function() {
        waitForElementToDisplay("#subscriptionsTable_filter", 1000, 1);
    });
</script>