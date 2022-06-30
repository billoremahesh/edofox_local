<!-- Include Header -->
<?php include_once(APPPATH . "Views/header.php"); ?>

<!-- Custom CSS -->
<link href="<?php echo base_url('assets/css/notices/overview.css?v=20210917'); ?>" rel="stylesheet">


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
            <div class="d-flex flex-row-reverse my-2">

                <div>
                    <button type="button" class="btn btn-sm text-black-50" onclick="resetDatatable()" data-bs-toggle="tooltip" title="Reset saved table settings like hidden columns, search, etc">
                        <i class='material-icons'>refresh</i>
                    </button>

                    <button class='btn btn-sm text-black-50' onclick='dtExport("custom_data_tble_wrapper","excel");' data-bs-toggle="tooltip" title="Export Notices List in Excel">
                        <i class='material-icons'>outbound</i>
                    </button>


                    <button class='btn btn-sm border-0' onclick="show_add_modal('modal_div','add_notice_modal','notices/add_notice_modal');" data-bs-toggle="tooltip" title="Add New Notice">
                        <i class='action_button_plus_icon material-icons' style="background-color: #ed4c05;">add</i>
                    </button>


                </div>



                <div class="mx-2">
                    <input type="date" id="start_date" class="form-input" />
                    <input type="date" id="end_date" class="form-input" />
                    <button class="btn btn-warning date_filter"> Apply Filter </button>
                </div>

            </div>

            <div class="d-flex justify-content-between my-2">

                <!-- Moved Datatable Page Length Menu -->
                <div id="dataTables_length_div"></div>

                <!-- Moved Datatable Search box -->
                <div id="dataTables_search_box_div"></div>
            </div>

            <div class="table-responsive table_custom">
                <table class="table table-bordered" id="notices_custom_data_tble" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th> # </th>
                            <th> Title </th>
                            <th> Start Date </th>
                            <th> End Date </th>
                            <th> Actions </th>
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
    var notices_custom_data_tble;
    $(document).ready(function() {
        $('[data-toggle="tooltip"]').tooltip();
        notices_custom_data_tble = $('#notices_custom_data_tble').DataTable({
            stateSave: true,
            "columnDefs": [{
                "targets": [4],
                "orderable": false,
            }, {
                "targets": -1,
                "class": 'btn_col'
            }],
            "order": [
                [0, "asc"]
            ],
            dom: 'Bflrtip',
            "lengthMenu": [
                [10, 25, 50, 100],
                [10, 25, 50, 100]
            ],
            buttons: [{
                extend: 'colvis',
                //https://datatables.net/forums/discussion/50751/hide-several-columns-for-colvis-button-list
                columns: ':gt(0)',
                text: "Toggle Columns"
            }, {
                extend: 'print',
                exportOptions: {
                    columns: ':visible:not(.not_export)'
                },
                title: "Classrooms ",
                customize: function(win) {
                    $(win.document.body).find('h1').css('text-align', 'center');
                    $(win.document.body).css('font-size', '9px');
                    $(win.document.body).find('td').css('padding', '0px');
                    $(win.document.body).find('td').css('padding-left', '2px');
                }
            }],
            "searching": true,
            "paging": true,
            "pageLength": 50,
            "bInfo": false,
            "processing": true,
            "serverSide": true,
            "ajax": {
                'url': base_url + "/notices/load_notices",
                "type": "POST",
                "data": function(d) {
                    d.start_date = $('#start_date').val(),
                        d.end_date = $('#end_date').val()
                }
            },
            language: {
                search: "",
                processing: '<i class="fas fa-atom fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>'
            },
            'drawCallback': function(data) {
                console.log(data.json);
                $('#total_classrooms').html("No of classrooms: " + data.json.recordsFiltered);
            },
            "dataSrc": "Data",
            "fnRowCallback": function(nRow, aData, iDisplayIndex) {
                //console.log(userdatatable.page.info());
            }
        });

        // Onchange of custom filters
        $(".date_filter").onclick(function() {
            notices_custom_data_tble.draw();
        });

    });


    // To reset all the parameters in the datatable which are saved using stateSave
    function resetDatatable() {
        notices_custom_data_tble.draw();
        // REF: https://datatables.net/forums/discussion/53726/table-state-clear-does-not-clear-saved-state-how-do-i-remove-specified-filters-sorts-here
        notices_custom_data_tble.state.clear(); // 1a - Clear State
        notices_custom_data_tble.destroy(); // 1b - Destroy
        setTimeout(function() {
            // Reload after few seconds
            window.location.reload();
        }, 1000);
    }
</script>