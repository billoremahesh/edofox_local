    <!-- Add Session Schedule Modal -->
    <div id="holiday_list" class="modal fade" role="dialog">
        <div class="modal-dialog modal-lg">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title"><?= $title; ?></h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div> 
                <div class="modal-body">
                <div class="table-responsive table_custom">
                <table class="table table-bordered table-hover table-sm" id="staffListTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr> 
                                        <th>Title</th>
                                        <th>Classrooms (s)</th>
                                        <th>Start Date</th>
                                        <th>End Date</th> 
                                        <th>Duration</th> 
                                        <th>Action</th> 
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach($holiday_list as $value){ ?>
                                    <tr class="exam_section_structure_tr" id="exam_section_structure_tr_1">
                                        
                                        <td>
                                            <?= $value['title'] ?>
                                        </td>
                                        <td>
                                            <?= isset($value['package_name']) ? $value['package_name'] : 'ALL'  ?>
                                         </td>
                                        <td style="text-align:center;" >
                                        <?= isset($value['date']) ? $value['date'] : "NA" ?>
                                        </td> 
                                        <td style="text-align:center;" >
                                        <?= isset($value['to_date']) ? $value['date'] : "NA" ?>
                                        </td> 
                                        <td style="text-align:center;" >
                                        <?= $value['duration'] ?>
                                        </td> 
                                        <td>
                                        <button type="button" class='btn btn-primary btn-sm text-uppercase' onclick="show_edit_modal('modal_div','delete_holiday_modal','schedule/delete_holiday_modal/<?= $value['id'] ?>');"> Delete</button>
                                              

                                </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                </div>  
                </div>
            </div>

        </div>
    </div>
 
    

<script>
    var staffListTable;

    // To reset all the parameters in the datatable which are saved using stateSave
    function resetDatatable() {
        // Resetting the filter values
        $("#searchbox").val("");
        localStorage.setItem('staff_datatable_search_value', "");
        staffListTable.draw();


        // REF: https://datatables.net/forums/discussion/53726/table-state-clear-does-not-clear-saved-state-how-do-i-remove-specified-filters-sorts-here
        staffListTable.state.clear(); // 1a - Clear State
        staffListTable.destroy(); // 1b - Destroy

        setTimeout(function() {
            window.location.reload();
        }, 1000);
    }

    $(document).ready(function() {

        var staff_datatable_search_value = localStorage.getItem('staff_datatable_search_value');
        $("#searchbox").val(staff_datatable_search_value);


        staffListTable = $('#staffListTable').DataTable({
            stateSave: true,
            "columnDefs": [{
                "targets": [5],
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
                messageTop: "Holiday"
            }, {
                extend: 'print',
                exportOptions: {
                    columns: ':visible:not(.not_to_print)'
                },
                title: "Holiday",
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
            language: {
                search: "",
                processing: '<i class="fas fa-atom fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>'
            },
            stateSaveCallback: function(settings, data) {
                if (data != null && data.search != null && data.search.search != null) {
                    localStorage.setItem('staff_datatable_search_value', data.search.search);
                }else{
                    localStorage.setItem('staff_datatable_search_value', "");
                }
            }
        });

        if (staff_datatable_search_value != '' && staff_datatable_search_value != null) {
            staffListTable.search(staff_datatable_search_value).draw();
        }
        $("#searchbox").keyup(function() {
            staffListTable.search(this.value).draw();
        });

    });


    function waitForElementToDisplay(selector, time, counter) {
        if (counter > 6) {
            return;
        }
        if (document.querySelector(selector) != null) {
            $("#staffListTable_filter").prepend($("#staffListTableExportGroup"));
            $("#staffListTableExportGroup").show();
            return;
        } else {
            setTimeout(function() {
                waitForElementToDisplay(selector, time, counter + 1);
            }, time);
        }
    }

    $(document).ready(function() {
        waitForElementToDisplay("#staffListTable_filter", 1000, 1);
    });
</script>

    