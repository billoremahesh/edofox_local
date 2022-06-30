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
                    <li class="breadcrumb_item active" aria-current="page"> <?= $title; ?> </li>
                </ol>
            </div>
        </div>


        <div class="row">

            <div class="col-3 mb-2">
                <div class="card border-left-success shadow  py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Total Invoices</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    <?= count($institute_invoices); ?>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-file-invoice fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-3 mb-2">
                <div class="card border-left-primary shadow py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Total Pending Invoices</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $total_pending_invoices ?></div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-file-invoice fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-3 mb-2">
                <div class="card border-left-warning shadow py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Invoices due this month</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $overdue_invoices ?></div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-file-invoice fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-3 mb-2">
                <div class="card border-left-danger shadow py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                    Invoices expired</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $invoices_expired ?></div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-file-invoice fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>


        <div class="card shadow p-4">

            <div class="d-flex justify-content-between my-1">

                <div class="d-flex flex-row-reverse">

                    <div class="input-group input-group-sm flex-nowrap search_input_wrap">

                        <span class="input-group-text" id="addon-search">
                            <i onclick="resetDatatable()" data-bs-toggle="tooltip" title="Reset saved table settings like hidden columns, search, etc" class='material-icons' id="refresh_icon">refresh</i>
                        </span>

                        <input class="form-control text-black-50" id='searchbox' autocomplete="off" style='width: 130px; color: blue; font-weight: bold;' placeholder="Search" name="srch-term" type="text" placeholder="Search" aria-label="Search" aria-describedby="addon-search">

                    </div>


                </div>
            </div>

            <div class="table-responsive table_custom">
                <table class="table table-bordered table-hover table-sm" id="instituteInvoicesTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th> # </th>
                            <th> Invoice Ref </th>
                            <th> Due Date </th>
                            <th> Amount Payable </th>
                            <th> Discount Amount </th>
                            <th> Status </th>
                            <th> Payment Type</th>
                            <th> Students </th>
                            <th class="not_to_export not_to_print"> Actions </th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php
                        if (!empty($institute_invoices)) {
                            $i = 1;
                            foreach ($institute_invoices as $row) {
                                $row_id = encrypt_string($row['id']);
                                $public_id = $row['public_id'];

                                echo "<tr>";
                                echo "<td>" . $i . "</td>";
                                echo "<td>" . $row['invoice_ref'] . "</td>";
                                echo "<td>" . $row['expiry_date'] . "</td>";
                                echo "<td class='text-right'>" . $row['amount_payable'] . "</td>";
                                echo "<td class='text-right'>" . $row['discount_amount'] . "</td>";
                                echo "<td class='text-right'>" . $row['status'] . "</td>";
                                echo "<td>" . $row['payment_type'] . "</td>";
                                echo "<td class='text-right'>" . $row['no_of_students'] . "</td>";

                        ?>
                                <td>

                                    <div class="d-flex">
                                        <a class='btn btn-sm' data-bs-toggle='tooltip' title='Print Invoice' target="_blank" href="<?= base_url('/invoices/print_invoice/' . $public_id); ?>">
                                            <span class="material-icons">
                                                receipt_long
                                            </span>
                                        </a>


                                        <?php
                                        if ($row['status'] == "Pending") {
                                        ?>
                                            <a class='btn btn-primary' href="<?= base_url('/Payments/pay_invoice/' . $public_id); ?>">
                                                Pay Now
                                            </a>

                                            <button type="button" class='btn btn-sm' onclick="show_edit_modal('modal_div','share_payment_link','payments/share_payment_link/<?= $public_id; ?>');" data-bs-toggle="tooltip" title="Share Payment Link">
                                                <i class="material-icons">share</i>
                                            </button>

                                        <?php
                                        }
                                        ?>
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



            <div id="instituteInvoicesTableExportGroup" class="export-icon-group" style="display: none">
                <img class="export-icon" onclick='dtExport("instituteInvoicesTable_wrapper","excel");' src="<?php echo base_url('assets/img/icons/download-excel-512x512.png'); ?>" alt='Excel' height='16' width='16'>
            </div>

        </div>


    </div>
</div>

<!-- Include Footer -->
<?php include_once(APPPATH . "Views/footer.php"); ?>


<script>
    var instituteInvoicesTable;

    // To reset all the parameters in the datatable which are saved using stateSave
    function resetDatatable() {
        // Resetting the filter values
        $("#searchbox").val("");
        localStorage.setItem('invoices_datatable_search_value', "");
        instituteInvoicesTable.draw();


        // REF: https://datatables.net/forums/discussion/53726/table-state-clear-does-not-clear-saved-state-how-do-i-remove-specified-filters-sorts-here
        instituteInvoicesTable.state.clear(); // 1a - Clear State
        instituteInvoicesTable.destroy(); // 1b - Destroy

        setTimeout(function() {
            window.location.reload();
        }, 1000);
    }

    $(document).ready(function() {

        var invoices_datatable_search_value = localStorage.getItem('invoices_datatable_search_value');
        $("#searchbox").val(invoices_datatable_search_value);


        instituteInvoicesTable = $('#instituteInvoicesTable').DataTable({
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
                messageTop: "Invoices"
            }, {
                extend: 'print',
                exportOptions: {
                    columns: ':visible:not(.not_to_print)'
                },
                title: "Invoices",
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
                    localStorage.setItem('invoices_datatable_search_value', data.search.search);
                } else {
                    localStorage.setItem('invoices_datatable_search_value', "");
                }
            }
        });

        if (invoices_datatable_search_value != '' && invoices_datatable_search_value != null) {
            instituteInvoicesTable.search(invoices_datatable_search_value).draw();
        }
        $("#searchbox").keyup(function() {
            instituteInvoicesTable.search(this.value).draw();
        });

    });


    function waitForElementToDisplay(selector, time, counter) {
        if (counter > 6) {
            return;
        }
        if (document.querySelector(selector) != null) {
            $("#instituteInvoicesTable_filter").prepend($("#instituteInvoicesTableExportGroup"));
            $("#instituteInvoicesTableExportGroup").show();
            return;
        } else {
            setTimeout(function() {
                waitForElementToDisplay(selector, time, counter + 1);
            }, time);
        }
    }

    $(document).ready(function() {
        waitForElementToDisplay("#instituteInvoicesTable_filter", 1000, 1);
    });
</script>