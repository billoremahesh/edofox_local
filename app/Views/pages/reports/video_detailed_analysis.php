<!-- Include Header -->
<?php include_once(APPPATH . "Views/header.php"); ?>

<!-- Custom CSS -->
<link href="<?php echo base_url('assets/css/reports/video_lecture_analysis.css?v=20210902'); ?>" rel="stylesheet">

<div id="content">
    <div class="container-fluid mt-4">
        <div class="flex-container-column">
            <div>
                <label class="h5 text-gray-800 text-uppercase"> <?= $title; ?> </h5>
            </div>
            <div class="breadcrumb_div" aria-label="breadcrumb">
                <ol class="breadcrumb_custom">
                    <li class="breadcrumb_item"><a href="<?php echo base_url('/home'); ?>"> Home </a></li>
                    <li class="breadcrumb_item" aria-current="page"><a href="<?php echo base_url('reports'); ?>">Reports</a></li>
                    <li class="breadcrumb_item" aria-current="page"><a href="<?php echo base_url('Lectures/video_analysis'); ?>">Video Analysis</a></li>
                    <li class="breadcrumb_item active" aria-current="page"> <?= $title; ?> </li>
                </ol>
            </div>
        </div>

        <div class="card shadow p-4">
            <div class="d-flex flex-row-reverse ">
                <div>
                    <!-- Moved Datatable Page Length Menu -->
                    <div style="margin-left: 16px;" id="dataTables_length_div"></div>
                </div>

                <div>
                    <!-- Moved Datatable Search box -->
                    <div id="dataTables_search_box_div"></div>
                </div>

            </div>

            <div>

                <table class="table table-striped table-bordered edo-table" id="video_analysis_table" style="width:100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Student Name</th>
                            <th>Contact</th>
                            <th>Roll number</th>
                            <th>Registered date</th>
                            <th>Interruptions</th>
                            <th>Duration watched (mins)</th>
                            <th>Last watched at</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($detailed_analysis)) :
                            $sr_no = 1;
                            foreach ($detailed_analysis as $row) :
                                $name = $row['name'];
                                $roll = $row['roll_no'];
                                $mobile = $row['mobile_no'];
                                $registeredDate = changeDateTimezone($row['created_date']);
                                $lastWatched = $row['lastWatched'];
                                $durationWatched = secToHR($row['duration']);
                                $frequency = $row['watched_activity'];
                                echo "<tr>";
                                echo "<td>$sr_no</td>";
                                echo "<td>$name</td>";
                                echo "<td>$mobile</td>";
                                echo "<td>$roll</td>";
                                echo "<td>$registeredDate</td>";
                                echo "<td>$frequency</td>";
                                echo "<td>$durationWatched</td>";
                                echo "<td>$lastWatched</td>";
                                echo "</tr>";
                                $sr_no++;
                            endforeach;
                        endif; ?>
                    </tbody>
                </table>
            </div>

            <div id="video_analysis_tableExportGroup" class="export-icon-group" style="display: none">
                <img class="export-icon" onclick='dtExport("video_analysis_table_wrapper","excel");' src="<?php echo base_url('assets/img/icons/download-excel-512x512.png'); ?>" alt='Excel' height='16' width='16'>
            </div>


        </div>
    </div>
</div>

<!-- Include Footer -->
<?php include_once(APPPATH . "Views/footer.php"); ?>

<script>
    // Call the dataTables jQuery plugin
    $(document).ready(function() {

        $('#video_analysis_table').DataTable({
            "order": [0, 'asc'],
            // "columnDefs": [{
            //     "targets": 5,
            //     "orderable": false,
            // }],
            dom: 'Bfrtip',
            buttons: ['excel'],
        });

        // Moved Datatable Search box and Page length option
        $("#dataTables_search_box_div").html($("#video_analysis_table_filter"));
        $("#dataTables_length_div").html($("#video_analysis_table_length"));
    });
</script>

<script>
    function waitForElementToDisplay(selector, time, counter) {
        if (counter > 6) {
            return;
        }
        if (document.querySelector(selector) != null) {
            $("#video_analysis_table_filter").prepend($("#video_analysis_tableExportGroup"));
            $("#video_analysis_tableExportGroup").show();
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
            case "pdf":
                sButtonName = "buttons-pdf";
                break;
        }

        $("#" + sContainerName + " ." + sButtonName).click();
    }

    $(document).ready(function() {
        waitForElementToDisplay("#video_analysis_table_filter", 1000, 1);
    });
</script>