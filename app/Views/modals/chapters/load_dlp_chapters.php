<label for="chapter_id">Chapter</label>
<select class="form-control" name="chapter_id" id="chapter_id" required>
    <option value=""></option>
    <?php
       if(!empty($chapters_list)):
    foreach ($chapters_list as $chapter_data) :
    ?>
        <option value="<?= $chapter_data['id']; ?>"><?= $chapter_data['chapter_name']; ?></option>
    <?php
    endforeach;
endif;
    ?>
</select>