<?php
if (!empty($course_subjects)) :
?>
    <label for="subject_id">Subject
        <img class="loading-div" id="subject-loading-div" src="<?= base_url('assets/img/loading.gif'); ?>" />
    </label>
    <select class="form-control" name="subject_id" id="subject_id" onchange="fetchCourseChapters(this, '<?= $course_id ?>')" required>
        <option value=""></option>
        <?php
        foreach ($course_subjects as $row) :
            $subject_id = $row['subject_id'];
            $subject_name = $row['subject'];

            echo "<option value='$subject_id'>$subject_name</option>";
        endforeach;
        ?>
    </select>

<?php
else :
    echo "No subjects for selected course(s)";
endif;
?>