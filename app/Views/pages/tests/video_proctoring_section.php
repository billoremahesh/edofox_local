<!-- Include Header -->
<?php include_once(APPPATH . "Views/header.php"); ?>

<!-- Custom CSS -->
<link href="<?php echo base_url('assets/css/tests/video_proctoring.css?v=20220331'); ?>" rel="stylesheet">

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

            <div class="text-center my-1">
                <h4><?= $test_details['test_name']; ?></h4>
            </div>

            <div class="my-1">
                <button class="btn btn-primary" onclick="create_session();"> Create New Session </button>
            </div>

            <div class="my-1" id="create_session_progress">

            </div>

            <div class="text-center my-2">
                <?php
                if (!empty($video_proctoring_data)) {

                    $test_start_time = $test_details['start_date'];
                    $session_start_date = $video_proctoring_data[0]['start_date'];

                    if ($test_start_time != $session_start_date) {
                        echo "<div class='text-center'><h5>Test time changed, please recreate new session </h5></div>";
                    }
                ?>
                    <table class="table table-bordered table-condensed my-2">
                        <thead>
                            <tr>
                                <th> # </th>
                                <th> Session </th>
                                <th> Classroom</th>
                                <th> Start Time </th>
                                <th> End Time </th>
                                <th> Students </th>
                                <th> Join </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 1;
                            foreach ($video_proctoring_data as $video_proctoring_session) {

                                $video_session_id = encrypt_string($video_proctoring_session['id']);
                                $meeting_id = encrypt_string($video_proctoring_session['schedule_id']);
                                $meeting_password = encrypt_string($video_proctoring_session['meeting_password']);

                                $datetime1 = strtotime(date('Y-m-d H:i:s'));
                                if (isset($video_proctoring_session['end_date']) && !empty($video_proctoring_session['end_date'])) {
                                    $datetime2 = strtotime($video_proctoring_session['end_date']);
                                } else {
                                    $datetime2 = strtotime(date('Y-m-d H:i:s'));
                                }

                                $session_interval = $datetime2 - $datetime1;

                                echo "<tr>";
                                echo "<td>" . $i . "</td>";
                                echo "<td>" . $video_proctoring_session['session_name'] . "</td>";
                                echo "<td>" . $video_proctoring_session['package_name'] . "</td>";
                                echo "<td>" . $video_proctoring_session['start_date'] . "</td>";
                                echo "<td>" . $video_proctoring_session['end_date'] . "</td>";
                                echo "<td>" . $video_proctoring_session['cnt'] . "</td>";
                            ?>
                                <td>
                                    <?php
                                    if ($session_interval <= 0) {
                                    ?>
                                        <a class="btn btn-primary" href="<?= base_url('tests/video_proctoring_recording/' . $video_session_id); ?>"> View Recording </a>
                                    <?php
                                    } else {
                                    ?>
                                        <a class="btn btn-primary" href="<?= base_url('tests/video_proctoring_session/' . $meeting_id . '/' . $meeting_password . '/' . encrypt_string($test_id)); ?>"> Join </a>
                                    <?php
                                    }
                                    ?>
                                </td>

                            <?php
                                echo "</tr>";
                            }
                            $i++;
                            ?>
                        </tbody>
                    </table>

                <?php
                } else {
                    echo "<h6>No Proctoring sessions created for this test.</h6>";
                }
                ?>
            </div>
        </div>

    </div>
</div>

<!-- Include Footer -->
<?php include_once(APPPATH . "Views/footer.php"); ?>

<script>
    function create_session() {
        var institute_id = <?= $institute_id; ?>;
        var test_id = <?= $test_id; ?>;

        var request = {
            test: {
                id: test_id
            },
            institute: {
                id: institute_id
            }
        };
        get_admin_token().then(function(result) {
                var resp = JSON.parse(result);
                if (resp != null && resp.status == 200 && resp.data != null && resp.data.admin_token != null) {
                    $.ajax({
                        type: "POST",
                        url: rootAdmin + "createProctoringRooms",
                        beforeSend: function(request) {
                            request.setRequestHeader("AuthToken", resp.data.admin_token);
                        },
                        data: JSON.stringify(request),
                        dataType: 'json',
                        contentType: 'application/json',
                        success: function(data) {
                            console.log(data);
                            if (data != null) {
                                if (data.status.statusCode == 200) {
                                    Snackbar.show({
                                        pos: 'top-center',
                                        text: 'New video session created successfully'
                                    });
                                    window.location.reload();
                                } else {
                                    $("#create_session_progress").text(data.status.responseText);
                                }
                            } else {
                                $("#create_session_progress").text("Some error while connecting to the server ..");
                            }
                        }
                    });
                } else {
                    $("#create_session_progress").text("Some error authenticating your request. Please clear your browser cache and try again.");
                }
            })
            .catch(function(error) {
                // An error occurred
                console.log("Error: ", error);
            });
    }
</script>