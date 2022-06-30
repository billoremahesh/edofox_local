<!-- Include Header -->
<?php include_once(APPPATH . "Views/header.php"); ?>

<!-- Custom CSS -->
<link href="<?php echo base_url('assets/css/institutes.css?v=20220411'); ?>" rel="stylesheet">

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


        <div class="card-body">

            <div class="d-flex justify-content-between my-1">

                <div class="text-center">
                    <badge class="badge bg-info fw-bold fs-6" id="total_institutes">
                        Total Institutes <?= count(json_decode($institutes_data)); ?>
                    </badge>
                </div>

                <div class="d-flex flex-row-reverse">

                    <div class="input-group input-group-sm flex-nowrap search_input_wrap">

                        <span class="input-group-text" id="addon-search">
                            <i onclick="resetDatatable()" data-bs-toggle="tooltip" title="Reset saved table settings like hidden columns, search, etc" class='material-icons' id="refresh_icon">refresh</i>
                        </span>

                        <input class="form-control text-black-50" id='searchbox' autocomplete="off" style='width: 130px; color: blue; font-weight: bold;' placeholder="Search" name="srch-term" type="text"  placeholder="Search" aria-label="Search" aria-describedby="addon-search">

                    </div>

                    <div class="mx-2">
                        <select class="form-select institute_status_filter" id="institute_status" style="width: 150px;">
                            <option value="active"> Active </option>
                            <option value="disabled"> Disabled </option>
                        </select>
                    </div>


                    <div>
                        <a class='action_button_plus_custom custom_btn ripple-effect' href="<?= base_url('institutes/add_institute'); ?>">
                            <i class='action_button_plus_icon material-icons' data-bs-toggle="tooltip" title="Add Institue">add</i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="table-responsive table_custom">
                <table id='institute_overview_table' class='table table-bordered table-hover table-sm' cellspacing='0' width='100%'>
                    <thead>
                        <tr>
                            <th> # </th>
                            <th> Institute Name </th>
                            <th> Total Active Students </th>
                            <th> Alias Name </th>
                            <th> Contact Number </th>
                            <th> Storage Quota </th>
                            <th> Expiry Date </th>
                            <th> Status </th>
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
    var institute_overview_table;
    
    // To reset all the parameters in the datatable which are saved using stateSave
    function resetDatatable() {
        // Resetting the filter values
        $("#institute_status").val("");
        $("#searchbox").val("");
        localStorage.setItem('institutes_datatable_search_value', "");
        institute_overview_table.draw();
        // REF: https://datatables.net/forums/discussion/53726/table-state-clear-does-not-clear-saved-state-how-do-i-remove-specified-filters-sorts-here
        institute_overview_table.state.clear(); // 1a - Clear State
        institute_overview_table.destroy(); // 1b - Destroy

        setTimeout(function() {
            window.location.reload();
        }, 1000);
    }

    $(document).ready(function() {
        $('[data-toggle="tooltip"]').tooltip();

        var institutes_datatable_search_value = localStorage.getItem('institutes_datatable_search_value');
        $("#searchbox").val(institutes_datatable_search_value);

        institute_overview_table = $('#institute_overview_table').DataTable({
            stateSave: true,
            "columnDefs": [{
                "targets": [8],
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
                messageTop: "Institutes"
            }, {
                extend: 'print',
                exportOptions: {
                    columns: ':visible:not(.not_to_print)'
                },
                title: "Institutes ",
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
            "data": <?= $institutes_data;?>
        });

        // Onchange of custom filters
        $(".institute_status_filter").change(function() {
            institute_overview_table.search(this.value).draw();
        });

        $("#searchbox").keyup(function() {
            institute_overview_table.search(this.value).draw();
            localStorage.setItem('institutes_datatable_search_value', this.value);
        });

    });
</script>