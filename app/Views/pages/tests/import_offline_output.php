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
            <p>The following students' results were imported successfully:</p>
            <table class="table table-bordered table-success">
                <thead>
                    <tr>
                        <th>Student Username</th>
                        <th>Successful Questions Imported</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($successful_inserts_student_counts as $key_student_username => $no_of_successful_entries) : ?>
                        <tr>
                            <td><?= $key_student_username ?></td>
                            <td><?= $no_of_successful_entries ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <?php


            if (count($question_not_found_array) > 0) {
                // There are some question which were not found
                // Display the question numbers
                echo "<p>The following question numbers were not found in the database:</p>";
                foreach ($question_not_found_array as $question) {
                    echo "<div>$key</div>";
                }
            }

            if (count($students_not_found_array) > 0) {
                // There are some students who were not found
                // Display the student usernames
                echo "<p>The following username students were not found in the database:</p>";
                foreach ($students_not_found_array as $student) {
                    echo "<div>$student</div>";
                }
            }
            ?>


            <div class="text-center">
                <h4 class="text-success">Imported Offline Results Successfully</h4>

                <a class='btn btn-success' href="<?= base_url('tests/revaluate_result/' . $test_id); ?>" target='_blank'>Generate Result</a>
                <br>
                <br>
                <a class="btn btn-outline-success" href="<?= base_url('tests/import_offline_results/' . $test_id); ?>"> Go Back </a>

            </div>

        </div>

    </div>

    <!-- Include Footer -->
    <?php include_once(APPPATH . "Views/footer.php"); ?>