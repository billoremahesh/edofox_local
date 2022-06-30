<div class="modal fade" id="add_dlp_chapter_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title"><?= $title; ?></h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <?php echo form_open('dlp/add_dlp_chapter_submit'); ?>
            <div class="modal-body">
                <?php 
                if (!empty($dlp_chapters_list)) : ?>
                    <div>
                        <label for="add_chapters_dropdown">Select chapters to add</label>
                        <select class="chapters-dropdown" name="dlp_chapters[]" id="add_chapters_dropdown" multiple required>
                            <option value=""></option>
                            <?php
                            foreach ($dlp_chapters_list as $row) :
                                $chapter_id = $row['id'];
                                $chapter_name = $row['chapter_name'];
                                echo "<option value='$chapter_id'>$chapter_name</option>";
                            endforeach;
                            ?>
                        </select>
                    </div>
                    <div>
                        <button type="button" class="btn btn-default" data-bs-toggle="modal" data-bs-dismiss="modal" data-bs-target="#addNewChaptersBulk">Chapters missing above? You need to first add chapters in the subject</button>
                    </div>

                <?php
                else :
                    echo "No chapters for selected subject";

                ?>
                    <div>
                        <button type="button" class="btn btn-default" data-bs-toggle="modal" data-bs-dismiss="modal" data-bs-target="#addNewChaptersBulk">Chapters missing above? You need to first add chapters in the subject</button>
                    </div>
                <?php
                endif;
                ?>
            </div>
            <div class="modal-footer">
                <input type="hidden" name="institute_id" value="<?= $instituteID; ?>" required />
                <input type="hidden" name="classroom_id" value="<?= $classroom_id; ?>" required />
                <input type="hidden" name="redirect" value="<?= $redirect; ?>" required />
                <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-success" name="add_package_form_submit">Add</button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

    <!-- Modal to add new chapters in bulk in the subjects -->
    <div class="modal fade" id="addNewChaptersBulk" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title">Add Chapters in a subject</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <?php echo form_open('dlp/add_new_chapters_in_subject_submit'); ?>
                    <div class="modal-body">

                        <input type="hidden" id="institute_id" name="institute_id" value="<?= $instituteID ?>" />
                        <input type="hidden" name="redirect" value="<?= $redirect; ?>" required />

                        <div class="form-group">
                            <label for="dlp_subject">Select a subject</label>
                            <select class="form-control" name="dlp_subject" id="dlp_subject" required>
                                <option value=""></option>

                                <?php
                                if(!empty($dlp_subjects_list)){
                                    foreach ($dlp_subjects_list as $row){
                                        $subject_id = $row['subject_id'];
                                        $subject_name = $row['subject'];

                                        echo "<option value='$subject_id'>$subject_name</option>";
                                    }
                                }
                                ?>

                            </select>
                        </div>



                        <div class="form-group">
                            <label for="chapter_names">Add comma separated chapter names</label>
                            <textarea class="form-control" name="chapter_names" id="chapter_names" placeholder="Evolution, Rotational Motion, p-block elements" required></textarea>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" name="add_new_chapters_submit">Add Chapters</button>
                    </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>


<script>
    // bootstrap 5 select2 not working in modal bug
    // https://stackoverflow.com/questions/18487056/select2-doesnt-work-when-embedded-in-a-bootstrap-modal/33884094#33884094
    // Initializing select2
    $('#add_chapters_dropdown').select2({
        width: "100%",
        dropdownParent: $("#add_dlp_chapter_modal")
    });
</script>