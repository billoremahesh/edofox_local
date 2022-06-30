<!-- Include Header -->
<?php include_once(APPPATH . "Views/header.php"); ?>

<!-- Custom CSS -->
<link href="<?php echo base_url('assets/css/tests/overview.css?v=20210915'); ?>" rel="stylesheet">

<div id="content">
    <div class="container-fluid mt-4">

        <div class="flex-container-column">
            <div>
                <label class="h5 text-gray-800 text-uppercase"> <?= $title; ?> </h5>
            </div>
            <div class="breadcrumb_div" aria-label="breadcrumb">
                <ol class="breadcrumb_custom">
                    <li class="breadcrumb_item"><a href="<?php echo base_url('/home'); ?>"> Home </a></li>
                    <li class="breadcrumb_item"><a href="<?php echo base_url('tests'); ?>"> Tests </a></li>
                    <li class="breadcrumb_item active" aria-current="page"> <?= $title; ?> </li>
                </ol>
            </div>
        </div>

        <div class="card p-4">
            <div class="container-fluid" id="main_div">


                <?php

if(empty($revaluated_result)):
    echo "result not found";exit();
endif;
                $groupSubjects = explode(",", $revaluated_result['subjects_group']);

                //Ordering the groups for easy of data overview FOR MATH
                if (count($groupSubjects) == 4) {
                    $subjectsOrder = array(3, 1, 2, 0);
                } else {
                    $subjectsOrder = array(2, 0, 1);
                }

                $orderedSubjects = array();
                foreach ($subjectsOrder as $index) {
                    $orderedSubjects[$index] = $groupSubjects[$index];
                }

                if (in_array("Biology", $orderedSubjects)) {
                    $revaluateResult2 =  $revaluated_result1;

                    // Check PCMB
                    if (in_array("Math", $orderedSubjects)) {
                        //Changing order of subjects for BIO subjects
                        $subjectsOrder = array(3, 1, 2, 0);
                    } else {
                        //Changing order of subjects for BIO subjects
                        $subjectsOrder = array(2, 1, 0);
                    }


                    $orderedSubjects = array();
                    foreach ($subjectsOrder as $index) {
                        $orderedSubjects[$index] = $groupSubjects[$index];
                    }
                } else {
                    $revaluateResult2 =  $revaluated_result2;
                }


                $test_name = $revaluated_result['test_name'];

                ?>

                <div>

                    <button class="btn btn-success pull-right" onclick="testAnalysisFunc('<?= $testidFsms ?>', '<?= rawurlencode($test_name) ?>')"> See Detailed Test Analysis</button>

                    <?php
                    echo "<h4>" . $test_name . "</h4>";
                    $formatted_start_date = changeDateTimezone(date("d M Y", strtotime($revaluateResultAssoc['start_date'])),"d M Y");
                    echo "Exam date: " . $formatted_start_date . "<br/>";
                    echo "Top Score: <b>" . round($revaluateResultAssoc['top_score'], 0) . "/" . $revaluateResultAssoc['total_marks'] . "</b><br/>";
                    ?>
                </div>

                <form id="frm-fire-sms" action="" method="POST">
                    <div class="table-responsive">
                        <button class="btn btn-secondary" id="send_sms_btn">Send SMS</button>

                        <table id="fire-sms" class="table table-bordered" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th class="notForPrint"><input name="select_all" value="1" type="checkbox"></th>
                                    <th>Rank</th>
                                    <th>Roll No.</th>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <?php
                                    foreach ($orderedSubjects as $sub_data) {
                                        echo "<th>" . $sub_data . "</th>";
                                    }
                                    ?>
                                    <th>Total</th>
                                    <th>%</th>
                                    <th>Correct</th>
                                    <th>Wrong</th>
                                    <th>NA</th>
                                    <th>Mobile</th>
                                    <th>Parent Mob</th>
                                    <th class="notForPrint">SMS</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $row_count = 1;
                                while ($row = mysqli_fetch_assoc($revaluateResult2)) {

                                    echo "<tr>";
                                    echo "<td>" . $row['stu_id'] . "</td>";
                                    echo "<td>" . $row_count . "</td>";
                                    echo "<td>" . $row['stu_roll_no'] . "</td>";
                                    echo "<td style='white-space: nowrap;'>" . $row['stu_name'] . "</td>";
                                    echo "<td>" . $row['category'] . "</td>";

                                    foreach ($orderedSubjects as $sub_data) {
                                        $marks_scored = $row[$sub_data];
                                        if ($marks_scored === "-") {
                                            $marks_scored = 0;
                                        }
                                        echo "<td>" . $marks_scored . "</td>";
                                    }

                                    $percent_scored = round($row['score'] * 100 / $revaluateResultAssoc['total_marks'], 2);
                                    echo "<td><b>" . $row['score'] . "</b>/" . $revaluateResultAssoc['total_marks'] . "</td>";
                                    echo "<td>" . $percent_scored . "</td>";
                                    echo "<td>" . $row['correct_ans'] . "</td>";
                                    echo "<td>" . $row['wrong_ans'] . "</td>";
                                    echo "<td>" . $row['not_attempted'] . "</td>";
                                    echo "<td>" . $row['stu_mobile_no'] . "</td>";
                                    echo "<td>" . $row['parent_mobile_no'] . "</td>";
                                    echo "<td style='white-space: nowrap;'>" . $row['message'] . "</td>";
                                    echo "</tr>";

                                    $row_count++;
                                }

                                ?>
                            </tbody>
                        </table>
                    </div>


                </form>

                <div id="fire-smsExportGroup" class="export-icon-group" style="display: none;margin-right:15px;">
                    <img class="export-icon" onclick='dtExport("fire-sms_wrapper","excel");' src='../dist/img/download-excel-512x512.png' alt='Excel' height='16' width='16' data-bs-toggle="tooltip" title="Export Excel">
                    <img class="export-icon" onclick='dtExport("fire-sms_wrapper","print");' src='../dist/img/outline_print_black_48dp.png' alt='Excel' height='16' width='16' data-bs-toggle="tooltip" title="Print">
                </div>

                <form id="frm_fire_sms_submit" action="sql_operations/fire_bulk_sms_submit.php" method="post">
                    <input class="hidden" id="fire_sms_test_id" name="fire_sms_test_id" value="<?php echo $testidFsms; ?>" />
                    <input class="hidden" id="fire_sms_instituteID" name="fire_sms_instituteID" value="<?php echo $instituteID; ?>" />
                    <textarea class="hidden" id="fire_sms_studentids" name="fire_sms_studentids"></textarea>
                    <input class="hidden" type="submit" id="frm_fire_sms_submit_btn" name="frm_fire_sms_submit_btn" value="Submit">
                </form>


                <!-- To display absent students list using ajax -->
                <div id="absent-students-div"></div>
            </div>






        </div>
    </div>

    <!-- Include Footer -->
    <?php include_once(APPPATH . "Views/footer.php"); ?>

    <script>
        function updateDataTableSelectAllCtrl(table) {
            var $table = table.table().node();
            var $chkbox_all = $('tbody input[type="checkbox"]', $table);
            var $chkbox_checked = $('tbody input[type="checkbox"]:checked', $table);
            var chkbox_select_all = $('thead input[name="select_all"]', $table).get(0);

            // If none of the checkboxes are checked
            if ($chkbox_checked.length === 0) {
                chkbox_select_all.checked = false;
                if ('indeterminate' in chkbox_select_all) {
                    chkbox_select_all.indeterminate = false;
                }

                // If all of the checkboxes are checked
            } else if ($chkbox_checked.length === $chkbox_all.length) {
                chkbox_select_all.checked = true;
                if ('indeterminate' in chkbox_select_all) {
                    chkbox_select_all.indeterminate = false;
                }

                // If some of the checkboxes are checked
            } else {
                chkbox_select_all.checked = true;
                if ('indeterminate' in chkbox_select_all) {
                    chkbox_select_all.indeterminate = true;
                }
            }
        }

        $(document).ready(function() {
            // Array holding selected row IDs
            var rows_selected = [];
            var editedPrintTitle = '<?= $test_name ?>' + ' Result (<?= $formatted_start_date ?>)';
            var table = $('#fire-sms').DataTable({
                'columnDefs': [{
                    'targets': 0,
                    'searchable': false,
                    'orderable': false,
                    'width': '1%',
                    'className': 'dt-body-center',
                    'render': function(data, type, full, meta) {
                        return '<input type="checkbox">';
                    }
                }],
                dom: 'Bfrtip',
                buttons: [{
                    extend: 'excel',
                    exportOptions: {
                        //REF: https://datatables.net/forums/discussion/35224/exporting-specific-columns-dynamically
                        columns: ':not(.notForPrint)'
                        // columns: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14]
                    }
                }, {
                    extend: 'print',
                    title: editedPrintTitle,
                    exportOptions: {
                        columns: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12]
                    },
                    customize: function(win) {
                        $(win.document.body).find('h1').css('text-align', 'center');
                        $(win.document.body).css('font-size', '9px');
                        $(win.document.body).find('td').css('padding', '0px');
                        $(win.document.body).find('td').css('padding-left', '2px');
                    }
                }],
                'paging': false,
                'order': [1, 'asc'],
                'rowCallback': function(row, data, dataIndex) {
                    // Get row ID
                    var rowId = data[0];

                    // If row ID is in the list of selected row IDs
                    if ($.inArray(rowId, rows_selected) !== -1) {
                        $(row).find('input[type="checkbox"]').prop('checked', true);
                        $(row).addClass('selected');
                    }
                }
            });
            $("#fire-sms_filter").prepend($("#send_sms_btn"));
            // Handle click on checkbox
            $('#fire-sms tbody').on('click', 'input[type="checkbox"]', function(e) {
                var $row = $(this).closest('tr');

                // Get row data
                var data = table.row($row).data();

                // Get row ID
                var rowId = data[0];

                // Determine whether row ID is in the list of selected row IDs 
                var index = $.inArray(rowId, rows_selected);

                // If checkbox is checked and row ID is not in list of selected row IDs
                if (this.checked && index === -1) {
                    rows_selected.push(rowId);

                    // Otherwise, if checkbox is not checked and row ID is in list of selected row IDs
                } else if (!this.checked && index !== -1) {
                    rows_selected.splice(index, 1);
                }

                if (this.checked) {
                    $row.addClass('selected');
                } else {
                    $row.removeClass('selected');
                }

                // Update state of "Select all" control
                updateDataTableSelectAllCtrl(table);

                // Prevent click event from propagating to parent
                e.stopPropagation();
            });

            // Handle click on table cells with checkboxes
            $('#fire-sms').on('click', 'tbody td, thead th:first-child', function(e) {
                $(this).parent().find('input[type="checkbox"]').trigger('click');
            });

            // Handle click on "Select all" control
            $('thead input[name="select_all"]', table.table().container()).on('click', function(e) {
                if (this.checked) {
                    $('#fire-sms tbody input[type="checkbox"]:not(:checked)').trigger('click');
                } else {
                    $('#fire-sms tbody input[type="checkbox"]:checked').trigger('click');
                }

                // Prevent click event from propagating to parent
                e.stopPropagation();
            });

            // Handle table draw event
            table.on('draw', function() {
                // Update state of "Select all" control
                updateDataTableSelectAllCtrl(table);
            });

            // Handle form submission event 
            $('#frm-fire-sms').on('submit', function(e) {
                var form = this;

                // Iterate over all selected checkboxes
                $.each(rows_selected, function(index, rowId) {
                    // Create a hidden element 
                    $(form).append(
                        $('<input>')
                        .attr('type', 'hidden')
                        .attr('name', 'id[]')
                        .val(rowId)
                    );
                });

                // FOR DEMONSTRATION ONLY     

                // Output form data to a console     
                // Output form data to a console     
                $('#fire_sms_studentids').text(rows_selected.join(","));

                // Output form data to a console     
                //   $('#fire-sms-console').text($(form).serialize());
                console.log("Form submission", $(form).serialize());

                // Remove added elements
                $('input[name="id\[\]"]', form).remove();

                // Prevent actual form submission
                e.preventDefault();

                // Submit Fire SMS Form
                var submitBtnClick = document.getElementById("frm_fire_sms_submit_btn");
                submitBtnClick.click();
            });






            //To fetch absent students from a test
            $.get("./sql_operations/ajax_fetch_test_absent_students.php", {
                    test_id: "<?= $testidFsms ?>"
                },
                function(data, status) {
                    // alert("Data: " + data + "\nStatus: " + status);
                    // console.log(data);

                    $("#absent-students-div").html(data);


                    //Adding datatable functionality to the table
                    $('#absent-students-table').DataTable({
                        'columnDefs': [{
                            'targets': 0,
                            'searchable': true,
                            'orderable': true,
                        }],
                        dom: 'Blfrtip',
                        buttons: [
                            'excel'
                        ],
                        'paging': false,
                    });
                });


            //Chaning the page <title></title> dynamically from here 
            //To Give this file name to exported excel as well
            document.title = "<?= $test_name ?>";



            //Setting tooltip
            $('[data-bs-toggle="tooltip"]').tooltip();
        });
    </script>


    <script>
        function waitForElementToDisplay(selector, time, counter) {
            if (counter > 6) {
                return;
            }
            if (document.querySelector(selector) != null) {
                $("#fire-sms_filter").prepend($("#fire-smsExportGroup"));
                $("#fire-smsExportGroup").show();
                return;
            } else {
                setTimeout(function() {
                    waitForElementToDisplay(selector, time, counter + 1);
                }, time);
            }
        }

        function dtExport(sContainerName, sType) {
            var sButtonName = '';
            switch (sType) {
                case "excel":
                    sButtonName = "buttons-excel";
                    break;
                case "print":
                    sButtonName = "buttons-print";
                    break;
            }

            $("#" + sContainerName + " ." + sButtonName).click();
        }

        $(document).ready(function() {

            waitForElementToDisplay("#fire-sms_filter", 1000, 1);

        });
    </script>


    <script>
        function testAnalysisFunc(test_id, test_name) {
            localStorage.setItem("testid_result", test_id);
            window.location = 'results.php?test_id=' + test_id;
        }
    </script>