<div class="row">
    <?php
    $course_id = $classrooms_details['id'];
    ?>

    <div class="col-xs-12">
        <label class="text-muted"><?= $classrooms_details['package_name']; ?></label>
    </div>

    <?php
    if (!empty($active_subjects_list)) :
        foreach ($active_subjects_list as $subject_list) :
    ?>


            <div class="subjects-chapters-block">
                <h6>
                    <b><?= $subject_list['subject']; ?></b>
                    <i class="far fa-trash-alt deleteClassroom" aria-hidden="true" onclick="disableSubjectWithChapters(<?= $subject_list['subject_id']; ?>, <?= $classrooms_details['id']; ?>)"></i>
                    <label style="margin-left: 4px;font-size:14px;" for="<?= $subject_list['subject'] ?>"><input type="checkbox" class="selectAll" id="<?= $subject_list['subject'] ?>" /> Select All Chapters for Cloning</label>
                    <button class="btn btn-sm btn-outline-secondary text-uppercase float-end" id="add-dlp-chapters-button" onclick="show_add_modal('modal_div','add_dlp_chapter_modal','dlp/add_dlp_chapter_modal/<?= $classroom_id; ?>/<?= $subject_list['subject_id']; ?>');">+ New Chapters IN <?= $subject_list['subject']; ?></button>
                </h6>

                <div class="clearfix"></div>

                <div class="row">

                    <?php
                    $chapter_count = 1;
                    $dlp_chapters = dlp_chapters($classrooms_details['id'], $subject_list['subject_id'], 1);
                    if (!empty($dlp_chapters)) :
                        foreach ($dlp_chapters as $dlp_chapter_list) :
                            $mapping_id = $dlp_chapter_list['id'];
                            $chapter_id = $dlp_chapter_list['chapter_id'];
                            $chapter_name = $dlp_chapter_list['chapter_name'];
                            $chapter_no = $dlp_chapter_list['chapter_no'];

                    ?>

                            <div class="col-md-6 my-2">
                                <div class="chapter-item">
                                    <input class="chapter-order-input" type="text" style="max-width: 32px;" value="<?= $chapter_no ?>" onblur="updateChapterOrder('<?= $mapping_id ?>', this.value)" title="Chapter Order" />


                                    <a href="<?= base_url('dlp/chapter_content/' . encrypt_string($course_id) . '/' . encrypt_string($subject_list['subject_id']) . '/' . encrypt_string($chapter_id)); ?>" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" data-bs-toggle="tooltip" title="<?= $chapter_name ?>"><?= $chapter_name ?></a>

                                    <input style="margin-right: 4px;" type='checkbox' name='chapter_ids[]' class='bulk_chapter_select' value="<?= $chapter_id; ?>">

                                    <!-- Delete chapter  -->
                                    <i class="far fa-trash-alt" aria-hidden="true" data-bs-toggle="tooltip" title="Delete Chapter" onclick="disableChapterFromClassroom(<?= $mapping_id ?>)"></i>

                                </div>
                            </div>


                    <?php
                            $chapter_count++;
                        endforeach;
                    endif;
                    ?>
                </div>


                <?php
                // Showing Disabled Chapter Names here
                $dlp_disabled_chapters = dlp_chapters($classrooms_details['id'], $subject_list['subject_id'], 0);
                if (!empty($dlp_disabled_chapters)) :
                ?>
                    <div class="row" style="background-color: #ffebee; padding: 16px">

                        <div class="col-sm-12">
                            <label class="text-muted">Disabled Chapters:</label>
                        </div>

                        <?php

                        $chapter_count = 1;
                        foreach ($dlp_disabled_chapters as $disabled_chapter) :
                            $mapping_id = $disabled_chapter['id'];
                            $chapter_id = $disabled_chapter['chapter_id'];
                            $chapter_name = $disabled_chapter['chapter_name'];
                            $chapter_no = $disabled_chapter['chapter_no'];

                        ?>

                            <div class="col-sm-6">
                                <div class="chapter-item">


                                    <a href="#" onclick="showChapterContent('<?= $subject_list['subject_id'] ?>', '<?= $course_id ?>', '<?= $chapter_id ?>')" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: #ef9a9a" data-bs-toggle="tooltip" title="<?= $chapter_name ?>"><?= $chapter_name ?></a>

                                    <button class="btn btn-sm" onclick="enableChapterFromClassroom(<?= $mapping_id ?>)">Enable</button>

                                </div>
                            </div>


                        <?php
                            $chapter_count++;
                        endforeach;
                        ?>

                    </div>

                <?php endif; ?>
            </div>


        <?php endforeach; ?>
    <?php else :
        echo "<p class='text-center'>No subjects and chapters for this course</p>";
    endif;
    ?>

    <div class="col-xs-12 text-center">

        <button class="btn btn-outline-secondary btn-sm text-uppercase" id="add-dlp-subjects-button" style="margin: 16px auto 32px;" onclick="show_add_modal('modal_div','add_dlp_subject_modal','dlp/add_dlp_subject_modal/<?= $classroom_id; ?>');">
            <img src="<?= base_url('assets/img/icons/subject-book.png'); ?>" style="width: 32px;" /> <br>
            Add new subjects in this classroom
        </button>
    </div>


    <div class="col-xs-12" style="margin-bottom: 96px;">


        <?php

        if (!empty($disabled_subjects_list)) :
        ?>

            <div>
                <label class="text-muted">Deleted Subjects in this Classroom:</label>
            </div>


            <?php
            foreach ($disabled_subjects_list as $subject_list) :

            ?>

                <div>

                    <div class="subjects-chapters-block">
                        <h6><b><?= $subject_list['subject']; ?></b>

                            <button class="btn btn-outline-warning btn-sm text-uppercase float-end" onclick="enableSubjectWithChapters(<?= $subject_list['subject_id'] ?>, <?= $course_id ?>)">Enable Subject</button>
                        </h6>
                    </div>
                </div>


            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<script>
    // For selecting subjects all chapters
    $('.selectAll').click(function(e) {
        $(this).closest('.subjects-chapters-block').find('input:checkbox').prop('checked', this.checked);
    });
</script>