<!-- Include Header -->
<?php include_once(APPPATH . "Views/header.php"); ?>

<!-- Custom CSS -->
<link href="<?php echo base_url('assets/css/tests/test_result.css?v=20210915'); ?>" rel="stylesheet">

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

        <div class="card shadow p-4">

            <div class="d-flex flex-row-reverse">

                <div>
                    <!-- Moved Datatable Search box -->
                    <div id="dataTables_search_box_div"></div>
                </div>

                <div>
                    <!-- Moved Datatable Page Length Menu -->
                    <div style="margin-left: 16px;" id="dataTables_length_div"></div>
                </div>

                <div>
                    <select class="form-control" id="activity_type_dropdown" onchange="filterData()">
                        <option value="">Show all</option>
                        <option value="Question">Show question activity</option>
                        <option value="Movement">Show screen activity</option>
                    </select>
                </div>

            </div>



            <div class="table-responsive table_custom">
                <table class="table table-striped table-bordered edo_table table-sm" id="activity_table" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Activity Type</th>
                            <th>Date and Time</th>
                            <th>Device</th>
                            <th>Device details</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php

                        $sr_no = 1;
                        if ($student_test_activity_details) :
                            foreach ($student_test_activity_details as $row) {
                                $activity = $row['activity_type'];
                                $date = $row['activity_time'];
                                $deviceInfo = $row['device_info'];
                                $device = $row['device'];
                                $questionNumber = $row['question_number'];
                                $questionId = $row['question_id'];

                                $questionText = "";
                                if (isset($questionId) && $questionId != null) {
                                    $questionText = "Q." . $questionNumber . " (Ref - " . $questionId . ")";
                                }

                                echo "<tr>";

                                echo "<td>$sr_no</td>";
                                echo "<td>$activity $questionText</td>";
                                echo "<td>$date</td>";
                                echo "<td>$device</td>";
                                echo "<td>$deviceInfo</td>";
                                echo "</tr>";

                                $sr_no++;
                            }
                        endif;

                        ?>
                    </tbody>
                </table>
            </div>


            <div id="activity_tableExportGroup" class="export-icon-group" style="display: none">
                <img class="export-icon" onclick='dtExport("activity_table_wrapper","excel");' src="<?php echo base_url('assets/img/icons/download-excel-512x512.png'); ?>" alt='Excel' height='16' width='16'>
            </div>

        </div>

    </div>
</div>

<!-- Include Footer -->
<?php include_once(APPPATH . "Views/footer.php"); ?>

<script>
    var table;
    $(document).ready(function() {
        table = $('#activity_table').DataTable({
            order: [0, "asc"],
            dom: "Bfrtip",
            buttons: ["excel"],
        });

        // Moved Datatable Search box and Page length option
        $("#dataTables_search_box_div").html($("#activity_table_filter"));
        $("#dataTables_length_div").html($("#activity_table_length"));

        $.fn.dataTable.ext.search.push(
            function(settings, data, dataIndex) {
                var activityType = $("#activity_type_dropdown").val();
                if (activityType == '') {
                    return true;
                }

                var value = data[1];
                if (activityType == 'Question') {
                    if (value.indexOf('Q.') >= 0) {
                        return true;
                    }
                    return false;
                } else if (activityType == 'Movement') {
                    if (value.indexOf('Q.') >= 0) {
                        return false;
                    }
                    return true;
                }
                return true;
            }
        );


    });

    function filterData() {
        table.draw();
    }

    function waitForElementToDisplay(selector, time, counter) {
        if (counter > 6) {
            return;
        }
        if (document.querySelector(selector) != null) {
            $("#activity_table_filter").prepend($("#activity_tableExportGroup"));
            $("#activity_tableExportGroup").show();
            return;
        } else {
            setTimeout(function() {
                waitForElementToDisplay(selector, time, counter + 1);
            }, time);
        }
    }

    function dtExport(sContainerName, sType) {
        var sButtonName = "";
        switch (sType) {
            case "excel":
                sButtonName = "buttons-excel";
                break;
            case "pdf":
                sButtonName = "buttons-pdf";
                break;
        }

        $("#" + sContainerName + " ." + sButtonName).click();
    }

    $(document).ready(function() {
        waitForElementToDisplay("#activity_table_filter", 1000, 1);
    });
</script>