<!-- Include Header -->
<?php include_once(APPPATH . "Views/header.php"); ?>

<!-- Custom CSS -->
<link href="<?php echo base_url('assets/css/tests/realtime_overview.css?v=20210915'); ?>" rel="stylesheet">

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
                    <li class="breadcrumb_item"><a href="<?php echo base_url('tests/proctoring_analysis/' . $test_id); ?>"> Proctoring Analysis </a></li>
                    <li class="breadcrumb_item active" aria-current="page"> <?= $title; ?> </li>
                </ol>
            </div>
        </div>



        <div class="card shadow p-4">

            <div class="table-responsive">


                <table id="datatable" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Session name</th>
                            <th>Created on</th>
                            <th>Classroom</th>
                            <th>Schedule ID </th>
                            <th>Action</th>

                        </tr>
                    </thead>
                    <tbody>
                        <?php

                        $sr_no = 1;



                        foreach ($proctoring_sessions as $row) {

                            $video_title = $row['session_name'];
                            $created_date = changeDateTimezone($row['created_date']);
                            $classroom = $row['package_name'];
                            $scheduleId = $row['schedule_id'];

                            echo "<tr>";

                            echo "<td>$sr_no</td>";
                            echo "<td>$video_title</td>";
                            echo "<td>$created_date</td>";
                            echo "<td>$classroom</td>";
                            echo "<td>$scheduleId</td>";
                            echo "<td><a href='proctoring_videos.php?schedule_id=$scheduleId' >View</a></td>";

                            echo "</tr>";

                            $sr_no++;
                        }

                        ?>
                    </tbody>
                </table>
            </div>

        </div>
        <div id="modalWatch" class="modal fade">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title">Watch Video</h1>
                    </div>
                    <div class="modal-body">
                        <iframe class="video-frame" id="videoFrame" src="" frameborder="0" allow="fullscreen" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>


                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>

                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div>

    </div>
</div>

<!-- Include Footer -->
<?php include_once(APPPATH . "Views/footer.php"); ?>

<script>
    $(document).ready(function() {
        $('#datatable').DataTable({
            dom: 'Blfrtip',
            buttons: [
                'excel'
            ],
        });
    });

    function showVideo(videoUrl) {
        $("#videoFrame").attr("src", videoUrl);
        $("#modalWatch").modal('show');
    }
</script>

<script>
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
</script>