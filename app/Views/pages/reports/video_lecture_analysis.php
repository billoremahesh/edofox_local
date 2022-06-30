<!-- Include Header -->
<?php include_once(APPPATH . "Views/header.php"); ?>
<!-- Video Lecture Analysis -->

<!-- Custom CSS -->
<link href="<?php echo base_url('assets/css/reports/video_lecture_analysis.css?v=20210902'); ?>" rel="stylesheet">

<div class="container-fluid mt-4">

    <div class="flex-container-column">
        <div>
            <label class="h5 text-gray-800 text-uppercase"> <?= $title; ?> </h5>
        </div>
        <div class="breadcrumb_div" aria-label="breadcrumb">
            <ol class="breadcrumb_custom">
                <li class="breadcrumb_item"><a href="<?php echo base_url('/home'); ?>"> Home </a></li>
                <li class="breadcrumb_item" aria-current="page"><a href="<?php echo base_url('reports'); ?>">Reports</a></li>
                <li class="breadcrumb_item active" aria-current="page"> <?= $title; ?> </li>
            </ol>
        </div>
    </div>


    <div class="card shadow p-4">
        <p><?= $quotaUsedValue ?> GBs used</p>

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
                        <th>Title</th>
                        <th>Created on</th>
                        <th>Size (GB)</th>
                        <th>Subject</th>
                        <th>Watched #</th>
                        <th>Completed #</th>
                        <th>Average watched %</th>

                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (!empty($video_lectures)) :
                        $sr_no = 0;
                        foreach ($video_lectures as $row) :
                            $video_url = $row['video_url'];
                            $video_title = $row['video_name'];
                            $created_date = changeDateTimezone($row['created_date']);
                            $video_size = $row['size'];
                            if (isset($video_size)) {
                                $video_size = round($video_size / (1024 * 1024 * 1024), 3);
                            }
                            $subject_name = $row['subject'];
                            $videoId = $row['videoId'];
                            $started = 0;
                            $completed = 0;
                            $average = 0;

                            if (!empty($startedResult)) :
                                foreach ($startedResult as $startedRow) {
                                    if ($startedRow['video_id'] == $videoId) {
                                        $started = $startedRow['startedCount'];
                                        break;
                                    }
                                }
                            endif;

                            if (!empty($completedResult)) :
                                foreach ($completedResult as $completedRow) {
                                    if ($completedRow['video_id'] == $videoId) {
                                        $completed = $completedRow['completedCount'];
                                        break;
                                    }
                                }
                            endif;

                            if (!empty($avgResult)) :
                                foreach ($avgResult as $avgRow) {
                                    if ($avgRow['video_id'] == $videoId) {
                                        $average = $avgRow['avg'];
                                        $average = round(($average * 100), 2);
                                        break;
                                    }
                                }
                            endif;

                            echo "<tr>";
                            echo "<td>" . ++$sr_no . "</td>";
                            echo "<td><a style='cursor:pointer' onclick=\"showVideo('" . $video_url . "')\">" . strtoupper($video_title) . "</a></td>";
                            echo "<td>" . $created_date . "</td>";
                            echo "<td>" . $video_size . "</td>";
                            echo "<td>" . $subject_name . "</td>";
                    ?>
                            <td>
                                <a href="<?= base_url('Lectures/video_detailed_analysis/' . encrypt_string($videoId)); ?>"><?= $started; ?></a>
                            </td>
                            <td>
                                <a href="<?= base_url('Lectures/video_detailed_analysis/' . encrypt_string($videoId)); ?>"><?= $completed; ?></a>
                            </td>
                    <?php
                            echo "<td>" . $average . "</td>";
                            echo "</tr>";
                        endforeach;
                    endif;
                    ?>
                </tbody>
            </table>

        </div>

        <div id="video_analysis_tableExportGroup" class="export-icon-group" style="display: none">
            <img class="export-icon" onclick='dtExport("video_analysis_table_wrapper","excel");' src="<?php echo base_url('assets/img/icons/download-excel-512x512.png'); ?>" alt='Excel' height='16' width='16'>
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
            "stripeClasses": [],
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