<?php

if (!empty($course_tests)) :
?>

    <label for="test_id">Tests</label>
    <i class="fa fa-refresh" aria-hidden="true" onclick="fetchCourseTests('<?= $course_id ?>')"></i>
    <select class="form-control course_tests_dropdown" name="test_id" id="test_id" onchange="toggleSubmitButton(this.value)">
        <option value=""></option>
        <?php
        foreach ($course_tests as $row) {
            $test_id = $row['test_id'];
            $test_name = $row['test_name'];
            $package_name = $row['package_name'];

            echo "<option value='$test_id'>$test_name ($package_name)</option>";
        }
        ?>
    </select>

    <p style="text-align: center;">
        <a href="<?= base_url('tests'); ?>" target="_blank">Create New Test</a>
    </p>


<?php else : ?>
    <a href="<?= base_url('tests'); ?>" target='_blank'>No tests for this course/classroom. Add now</a>


<?php
endif;


?>