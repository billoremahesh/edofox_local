<?php
if (!empty($course_chapters)) :
?>

    <label for="chapter_id">Chapter</label>
    <select class="form-control" name="chapter_id" id="chapter_id" required>
        <option value=""></option>
        <?php
        foreach ($course_chapters as $row) :
            $id = $row['id'];
            $chapter_name = $row['chapter_name'];
            echo "<option value='$id'>$chapter_name</option>";
        endforeach;
        ?>
    </select>

<?php else :
    echo "No chapters for selected course(s)";
endif;
?>