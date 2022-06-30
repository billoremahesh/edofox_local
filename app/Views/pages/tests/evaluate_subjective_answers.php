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
                    <li class="breadcrumb_item active" aria-current="page"> <?= $title; ?> </li>
                </ol>
            </div>
        </div>


        <div>
            <div class="container-fluid" id="main_div">
                <?php
                $pendingSubmits = false;
                ?>

                <div>
                    <?php
                    $test_name = $testDetails['test_name'];
                    echo "<h4>" . $test_name . "</h4>";
                    $formatted_start_date = changeDateTimezone(date("d M Y", strtotime($testDetails['start_date'])),"d M Y");
                    echo "Exam date: " . $formatted_start_date . "<br/>";
                    ?>
                </div>


                <div class="d-flex justify-content-between my-2">

                    <!-- Moved Datatable Page Length Menu -->
                    <div id="dataTables_length_div"></div>

                    <!-- Moved Datatable Search box -->
                    <div id="dataTables_search_box_div"></div>
                </div>

                <div class="table-responsive">

                    <table id="custom_data_tble" class="table table-bordered" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>Sr No</th>
                                <th>Roll No.</th>
                                <th>Name</th>
                                <th>Mobile</th>
                                <th>Uploaded files</th>
                                <th>Correct</th>
                                <th>Total Solved</th>
                                <th>Score</th>
                                <th>Exam Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $row_count = 1;
                            if (!empty($examAnswersResult)) {
                                foreach ($examAnswersResult as $row) {

                                    $studentId = $row['student_id'];
                                    $testId = $testDetails['test_id'];

                                    $buttonClass = "";
                                    $status = "<i class=\"fa fa-exclamation-circle\" aria-hidden=\"true\"></i>";
                                    if (isset($row['status']) && $row['status'] == 'COMPLETED') {
                                        $status = "<i class=\"fa fa-check\" aria-hidden=\"true\" style='color:green' title='Completed'></i>";
                                    } else {
                                        $pendingSubmits = true;
                                        $buttonClass = "display:none";
                                    }

                                    echo "<tr>";
                                    echo "<td>" . $row_count. "</td>";
                                    echo "<td>" . $row['roll_no'] . "</td>";
                                    echo "<td style='white-space: nowrap;'>" . $row['name'] . "</td>";
                                    echo "<td>" . $row['mobile_no'] . "</td>";
                                    echo "<td>" . $row['files'] . "</td>";
                                    echo "<td>" . $row['correct'] . "</td>";
                                    echo "<td>" . $row['solved'] . "</td>";
                                    echo "<td>" . $row['score'] . "</td>";
                                    echo "<td> " . $status . "  </td>";
                                    echo "<td style='white-space: nowrap;'>";
                            ?>
                                    <a href="<?= base_url('tests/evaluate_students_subjective_answers/' . $testId . '/' . $studentId); ?>" class='btn btn-primary' style="<?= $buttonClass; ?>"> Evaluate</a>
                            <?php
                                    echo " </td>";
                                    echo "</tr>";
                                    $row_count++;
                                }
                            }
                            ?>
                        </tbody>
                    </table>

                    <?php if ($pendingSubmits) : ?>
                        <div class="text-center">
                            <label class="text-danger">If the test is ended, please submit everyone's test from Real time overview screen to access evaluation for all.</label>
                        </div>
                    <?php endif; ?>

                </div>


            </div>

        </div>

    </div>
</div>

<!-- Include Footer -->
<?php include_once(APPPATH . "Views/footer.php"); ?>