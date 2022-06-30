<!-- Include Header -->
<?php include_once(APPPATH . "Views/header.php"); ?>

<!-- Custom CSS -->
<link href="<?php echo base_url('assets/css/reports/live_lectures_analysis.css?v=20210918'); ?>" rel="stylesheet">

<div id="content" ng-app="app" ng-controller="liveAnalysis">
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

            <div ng-if="isLoading">
                <i class='fas fa-atom fa-spin fa-2x fa-fw'></i>
            </div>


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
                <table class="table table-bordered edo-table" id="live_lecture_details_tble" style="width:100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Lecture</th>
                            <th>Status</th>
                            <th>Created on</th>
                            <th>Created by</th>
                            <th>Recording Size (GB)</th>
                            <th>Classroom</th>
                            <th>Attended #</th>

                        </tr>
                    </thead>
                    <tbody>
                        <?php

                        $sr_no = 1;
                        if (!empty($live_sessions_data)) :
                            foreach ($live_sessions_data as $row) :

                                $video_url = $row['recording_url'];
                                $video_size = "";
                                $joined = $row['joined'];
                                $videoId = $row['videoId'];
                                if (isset($row['file_size'])) {
                                    $video_size = round($row['file_size'] / (1024 * 1024 * 1024), 3);
                                }

                                echo "<tr>";
                                echo "<td>$sr_no</td>";
                                echo "<td><a style='cursor:pointer' ng-click=\"joinLive('" . $videoId . "')\">" . strtoupper($row['session_name']) . "</a></td>";
                                echo "<td>" . $row['status'] . "</td>";
                                echo "<td>" . changeDateTimezone($row['created_date']) . "</td>";
                                echo "<td>" . $row['creator'] . "</td>";
                                echo "<td>$video_size</td>";
                                echo "<td>" . $row['package_name'] . "</td>";
                                echo "<td><a href='video-detailed-analysis.php?videoId=$videoId&requestType=Live'>$joined</a></td>";
                                echo "</tr>";

                                $sr_no++;
                            endforeach;
                        endif;
                        ?>
                    </tbody>
                </table>

            </div>

            <div id="live_lecture_details_tbleExportGroup" class="export-icon-group" style="display: none">
                <img class="export-icon" onclick='dtExport("live_lecture_details_tble_wrapper","excel");' src="<?php echo base_url('assets/img/icons/download-excel-512x512.png'); ?>" alt='Excel' height='16' width='16'>
            </div>

        </div>

    </div>


</div>


<div id="modalWatch" class="modal fade">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Watch Video</h6>
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

<!-- Include Footer -->
<?php include_once(APPPATH . "Views/footer.php"); ?>




<script>
    function showVideo(videoUrl) {
        $("#videoFrame").attr("src", videoUrl);
        $("#modalWatch").modal('show');
    }
</script>

<script>
    app.controller('liveAnalysis', function($scope, userService, $location, $http) {

        console.log("Live analysis controller ...");


        $scope.joinLive = function(sessionId) {

            $scope.isLoading = true;

            $scope.dataObj = {
                student: {
                    id: studentId,
                    accessType: 'Teacher',
                    currentPackage: {
                        id: sessionId
                    }
                }
            };


            userService.callService($scope, "joinSession").then(function(response) {
                console.log(' ============= Got response ========== ', response);
                //$scope.subjects = response.subjects;
                $scope.isLoading = false;
                if (response.status.statusCode > 0) {
                    $scope.errorMessage = "";
                    console.log("Session joining === > ", response);
                    var session = response.packages[0];
                    window.open(session.videoUrl, "_blank", "location=no,menubar=no,toolbar=no,titlebar=no,fullscreen=yes");
                    //session.name = $scope.classroom.name;
                    //$scope.continue(session);
                } else {
                    $scope.errorMessage = "Some error in joining classroom ..";
                }

            }).catch(function(error) {
                console.log("Error!" + error);
            });


        }

    });
</script>


<script>
    // Call the dataTables jQuery plugin
    $(document).ready(function() {

        $('#live_lecture_details_tble').DataTable({
            "order": [0, 'asc'],
            "stripeClasses": [],
            dom: 'Bfrtip',
            buttons: ['excel'],
        });

        // Moved Datatable Search box and Page length option
        $("#dataTables_search_box_div").html($("#live_lecture_details_tble_filter"));
        $("#dataTables_length_div").html($("#live_lecture_details_tble_length"));
    });
</script>

<script>
    function waitForElementToDisplay(selector, time, counter) {
        if (counter > 6) {
            return;
        }
        if (document.querySelector(selector) != null) {
            $("#live_lecture_details_tble_filter").prepend($("#live_lecture_details_tbleExportGroup"));
            $("#live_lecture_details_tbleExportGroup").show();
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
        waitForElementToDisplay("#live_lecture_details_tble_filter", 1000, 1);
    });
</script>