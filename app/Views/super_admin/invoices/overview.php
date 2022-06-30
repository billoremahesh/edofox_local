<!-- Include Header -->
<?php include_once(APPPATH . "Views/header.php"); ?>

<!-- Custom CSS -->
<link href="<?php echo base_url('assets/css/invoices.css?v=20220525'); ?>" rel="stylesheet">

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
            <div class="card border-left-success shadow py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Invoices</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= count($total_invoices) ?></div>
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


    <!-- Content Row -->
    <div class="card shadow p-4 mb-4">

        <div class="card-body">

            <div class="d-flex justify-content-between my-1">

                <div class="text-center">
                    <select class="form-select" id="invoice_status_filter">
                        <option value="">All</option>
                        <option value="Pending"> Pending </option>
                        <option value="Paid"> Paid </option>
                        <option value="OnHold"> OnHold </option>
                        <option value="Rejected"> Rejected </option>
                    </select>
                </div>

                <div class="d-flex flex-row-reverse">

                    <div class="input-group input-group-sm flex-nowrap search_input_wrap">

                        <span class="input-group-text" id="addon-search">
                            <i onclick="resetDatatable()" data-bs-toggle="tooltip" title="Reset saved table settings like hidden columns, search, etc" class='material-icons' id="refresh_icon">refresh</i>
                        </span>

                        <input class="form-control text-black-50" id='searchbox' autocomplete="off" style='width: 130px; color: blue; font-weight: bold;' placeholder="Search" name="srch-term" type="text" placeholder="Search" aria-label="Search" aria-describedby="addon-search">

                    </div>
                </div>
            </div>


            <table id='invoice_overview_table' class='table table-bordered table-hover table-sm' cellspacing='0' width='100%'>
                <thead>
                    <tr>
                        <th> # </th>
                        <th> Invoice Ref </th>
                        <th> Institute Name </th>
                        <th style="text-align: right;"> Amount Payable </th>
                        <th style="text-align: right;"> Discount Amount </th>
                        <th> Payment Type </th>
                        <th> Due Date </th>
                        <th> Students </th>
                        <th> Status </th>
                        <th class="not_to_export not_to_print"> Actions </th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

</div>


<!-- Include Footer -->
<?php include_once(APPPATH . "Views/footer.php"); ?>
<script>
    var invoice_overview_table;

    // To reset all the parameters in the datatable which are saved using stateSave
    function resetDatatable() {
        // Resetting the filter values
        $("#institute_status").val("");
        $("#searchbox").val("");
        localStorage.setItem('invoices_datatable_search_value', "");
        invoice_overview_table.draw();
        // REF: https://datatables.net/forums/discussion/53726/table-state-clear-does-not-clear-saved-state-how-do-i-remove-specified-filters-sorts-here
        invoice_overview_table.state.clear(); // 1a - Clear State
        invoice_overview_table.destroy(); // 1b - Destroy

        setTimeout(function() {
            window.location.reload();
        }, 1000);
    }

    $(document).ready(function() {
        $('[data-toggle="tooltip"]').tooltip();

        var invoices_datatable_search_value = localStorage.getItem('invoices_datatable_search_value');
        $("#searchbox").val(invoices_datatable_search_value);

        invoice_overview_table = $('#invoice_overview_table').DataTable({
            stateSave: true,
            "columnDefs": [{
                    "targets": [9],
                    "orderable": false,
                }, {
                    "targets": -1,
                    "class": 'btn_col'
                },
                {
                    "sClass": "numericCol",
                    "aTargets": [3, 4, 7]
                }
            ],
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
                // "action": newexportaction,
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
            "pageLength": 10,
            "bLengthChange": false,
            "bInfo": false,
            "processing": true,
            language: {
                search: "",
                processing: '<i class="fas fa-atom fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>'
            },
            "data": <?= $invoices_data; ?>
        });

        // Onchange of custom filters
        $("#invoice_status_filter").change(function() {
            invoice_overview_table.search(this.value).draw();
        });

        $("#searchbox").keyup(function() {
            invoice_overview_table.search(this.value).draw();
            localStorage.setItem('invoices_datatable_search_value', this.value);
        });

    });
</script>