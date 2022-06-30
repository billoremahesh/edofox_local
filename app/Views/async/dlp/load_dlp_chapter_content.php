<div style="text-align: right;">
    <button class="btn btn-primary" onclick="show_add_modal('modal_div','addNewCourseResource','dlp/add_dlp_chapter_content_modal/<?= $classroom_id; ?>/<?= $subject_id; ?>/<?= $chapter_id; ?>');">Add New DLP Resource </button>
</div>

<div class="row">
    <?php


    echo "<h4><b>" . $chapter_details['chapter_name'] . "</b></h4>";

    ?>
    <div class="col-sm-6">
        <div>
            <div id="video-content-div">
                <?php
                if (!empty($chapter_video_content)) :
                ?>


                    <?php
                    foreach ($chapter_video_content as $row) :
                        $resource_mapping_id = $row['id'];
                        $resource_id = $row['resource_id'];
                        $video_name = $row['video_name'];
                        $video_url = $row['video_url'];
                        $test_id = $row['test_id'];
                        $content_order = $row['content_order'];
                        $created_date = changeDateTimezone($row['created_date']);
                        $status = $row['status'];
                        $progress = $row['progress'];

                        $todays_date = changeDateTimezone(date('d-m-Y H:i:s'));
                        // Changing style for upcoming content
                        $upcoming_style = "";
                        $upcoming_icon = "";
                        $activation_date = changeDateTimezone($row['activation_date']);
                        if (isset($row['activation_date']) && $row['activation_date'] != "" && strtotime($todays_date) < strtotime(changeDateTimezone($row['activation_date']))) {
                            $upcoming_style = "background-color: #e1f5fe";
                            $upcoming_icon = "<i class='far fa-clock text-primary' aria-hidden='true' data-toggle='tooltip' title='This content is not yet active'></i>";
                        }

                    ?>

                        <div style="display: flex;<?= $upcoming_style; ?>">

                            <input class="content-order-input" type="text" style="max-width: 32px; align-self: center; background-color: transparent;" value="<?= $content_order ?>" onblur="updateContentOrder('<?= $resource_mapping_id ?>', this.value)" />

                            <button class="video-content-button" onclick='displayCourseData("<?= $video_name ?>", "<?= $video_url ?>", "<?= $test_id ?>", "<?= $resource_id ?>","<?= $status ?>", "<?= $progress ?>")' title="<?= $video_name ?>" style="flex-grow: 8"><span class="text-muted small" style="margin-left: 28px;" data-toggle="tooltip" title="Added on"><?= changeDateTimezone(date("d M Y, h:i A", strtotime($created_date)), "d M Y, h:i A"); ?></span>
                            <span class="text-muted small" style="font-weight: bold;" data-toggle="tooltip" title="Available to students on (Activation date)"><?= (!empty($activation_date)) ? " | " . date("d M Y, h:i A", strtotime($activation_date)) : ""; ?></span>
                            <?= $upcoming_icon ?><br><i class="fas fa-play-circle" aria-hidden="true"></i><?= $video_name ?></button>

                            <div>
                                <button class="video-content-button" onclick="show_edit_modal('modal_div','update_dlp_resource_modal','dlp/update_dlp_resource_modal/<?= $chapter_id ?>/<?= $resource_mapping_id ?>/<?= $resource_id ?>/<?= $classroom_id; ?>/VIDEO')" data-bs-toggle="tooltip" title="Edit"><i class="fas fa-pencil-alt" aria-hidden="true"></i></button>
                                <button class="video-content-button" onclick="deleteCourseResource('<?= $resource_mapping_id ?>', 'VIDEO')" data-bs-toggle="tooltip" title="Remove from this chapter"><i class="fas fa-trash-alt" aria-hidden="true"></i></button>
                            </div>
                        </div>
                        <hr style="margin: 0;" />
                    <?php
                    endforeach;
                else : ?>

                    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
                    <lottie-player src="https://assets9.lottiefiles.com/packages/lf20_kvNZPL.json" background="transparent" speed="1" style="width: 200px; height: 200px;" loop autoplay></lottie-player>

                    <p>No videos available yet in this chapter.</p>
                <?php endif; ?>



                <?php

                if (!empty($chapter_disabled_video_content)) :
                ?>

                    <label class="text-muted">Deleted Videos:</label>

                    <?php
                    foreach ($chapter_disabled_video_content as $row) :
                        $resource_mapping_id = $row['id'];
                        $resource_id = $row['resource_id'];
                        $video_name = $row['video_name'];
                        $video_url = $row['video_url'];
                        $test_id = $row['test_id'];
                        $content_order = $row['content_order'];
                        $created_date = changeDateTimezone($row['created_date']);
                        $status = $row['status'];
                        $progress = $row['progress'];
                    ?>

                        <div style="display: flex;align-items: center;background-color: #ffebee;">

                            <button class="video-content-button" onclick='displayCourseData("<?= $video_name ?>", "<?= $video_url ?>", "<?= $test_id ?>", "<?= $resource_id ?>","<?= $status ?>", "<?= $progress ?>")' title="<?= $video_name ?>" style="flex-grow: 8"><span class="text-muted small" style="margin-left: 28px;"><?= changeDateTimezone(date("d M Y, h:i A", strtotime($created_date)), "d M Y, h:i A"); ?></span><br><i class="fas fa-play-circle" aria-hidden="true"></i><?= $video_name ?></button>

                            <div>
                                <button class="btn" onclick="enableCourseResource('<?= $resource_mapping_id ?>')">Enable</button>
                            </div>
                        </div>
                        <hr style="margin: 0;" />
                <?php
                    endforeach;
                endif;
                ?>

            </div>
        </div>
    </div>

    <div class="col-sm-6">
        <div>
            <div id="doc-content-div">


                <?php

                if (!empty($chapter_doc_content)) :
                ?>

                    <div class="resources-block">
                        <label>Resources</label>
                        <?php
                        foreach ($chapter_doc_content as $row) :
                            $resource_mapping_id = $row['id'];
                            $resource_id = $row['resource_id'];
                            $video_name = $row['video_name'];
                            $video_url = $row['video_url'];
                            $content_order = $row['content_order'];
                            $created_date = changeDateTimezone($row['created_date']);

                            $prefix = "../../../";
                            if (strpos($video_url, "http") >= 0) {
                                $prefix = "";
                            }
                            $docUrl = $prefix . $video_url;

                            $todays_date = changeDateTimezone(date('d-m-Y H:i:s'));
                            // Changing style for upcoming content
                            $upcoming_style = "";
                            $upcoming_icon = "";
                            $activation_date = changeDateTimezone($row['activation_date']);
                            if (isset($row['activation_date']) && $row['activation_date'] != "" && strtotime($todays_date) < strtotime(changeDateTimezone($row['activation_date']))) {
                                $upcoming_style = "background-color: #e1f5fe";
                                $upcoming_icon = "<i class='far fa-clock text-primary' aria-hidden='true' data-toggle='tooltip' title='This content is not yet active'></i>";
                            }
                        ?>

                            <div style="display: flex;<?= $upcoming_style; ?>">

                                <input class="content-order-input" type="text" style="max-width: 32px; align-self: center; background-color: transparent;" value="<?= $content_order ?>" onblur="updateContentOrder('<?= $resource_mapping_id ?>', this.value)" />

                                <a class="video-content-button" href="<?= $docUrl ?>" target="_blank" style="flex-grow: 8"><span class="text-muted small" style="margin-left: 28px;" data-toggle="tooltip" title="Added on"><?= changeDateTimezone(date("d M Y, h:i A", strtotime($created_date)), "d M Y, h:i A"); ?></span>
                                <span class="text-muted small" style="font-weight: bold;" data-toggle="tooltip" title="Available to students on (Activation date)"><?= (!empty($activation_date)) ? " | " . date("d M Y, h:i A", strtotime($activation_date)) : ""; ?></span>
                                <?= $upcoming_icon ?><br><i class="fas fa-file-alt" aria-hidden="true"></i><?= $video_name ?></a>


                                <div>
                                    <button class="video-content-button" onclick="show_edit_modal('modal_div','update_dlp_resource_modal','dlp/update_dlp_resource_modal/<?= $chapter_id ?>/<?= $resource_mapping_id ?>/<?= $resource_id ?>/<?= $classroom_id; ?>/DOC')" data-bs-toggle="tooltip" title="Edit"><i class="fas fa-pencil-alt" aria-hidden="true"></i></button>

                                    <button class="video-content-button" onclick="deleteCourseResource('<?= $resource_mapping_id ?>', 'DOC')" data-bs-toggle="tooltip" title="Remove from this chapter"><i class="fas fa-trash-alt" aria-hidden="true"></i></button>

                                </div>
                            </div>
                            <hr style="margin: 0;" />
                        <?php endforeach; ?>
                    </div>
                <?php
                endif;



                if (!empty($chapter_disabled_doc_content)) :
                ?>

                    <label class="text-muted">Deleted Documents:</label>

                    <?php
                    foreach ($chapter_disabled_doc_content as $row) :
                        $resource_mapping_id = $row['id'];
                        $resource_id = $row['resource_id'];
                        $video_name = $row['video_name'];
                        $video_url = $row['video_url'];
                        $content_order = $row['content_order'];
                        $created_date = changeDateTimezone($row['created_date']);

                        $docUrl = "../../../" . $video_url;

                    ?>

                        <div style="display: flex; align-items: center; background-color: #ffebee;">

                            <a class="video-content-button" href="<?= $docUrl ?>" target="_blank" style="flex-grow: 8"><span class="text-muted small" style="margin-left: 28px;"><?= changeDateTimezone(date("d M Y, h:i A", strtotime($created_date)), "d M Y, h:i A"); ?></span><br><i class="fas fa-file-alt" aria-hidden="true"></i><?= $video_name ?></a>

                            <div>
                                <button class="btn" onclick="enableCourseResource('<?= $resource_mapping_id ?>')">Enable</button>
                            </div>
                        </div>
                        <hr style="margin: 0;" />
                <?php endforeach;
                endif;
                ?>

            </div>
        </div>

        <!-- Tests DIV -->
        <div>
            <div id="tests-content-div">

                <?php
                if (!empty($chapter_test_content)) :
                ?>

                    <div class="tests-block">
                        <label>Tests</label>
                        <?php
                        foreach ($chapter_test_content as $row) :
                            $resource_mapping_id = $row['id'];
                            $test_name = $row['test_name'];
                            $content_order = $row['content_order'];
                            $start_date = $row['start_date'];
                        ?>

                            <div style="display: flex;">
                                <input class="content-order-input" type="text" style="max-width: 32px; align-self: center" value="<?= $content_order ?>" onblur="updateContentOrder('<?= $resource_mapping_id ?>', this.value)" />

                                <button class="video-content-button" style="flex-grow: 8"><span class="text-muted small" style="margin-left: 28px;"><?= changeDateTimezone(date("d M Y, h:i A", strtotime($start_date)), "d M Y, h:i A"); ?></span><br><i class="fas fa-list-ol" aria-hidden="true"></i><?= $test_name ?></button>

                                <div>
                                    <a href="<?= base_url('/tests'); ?>" target="_blank"><i class="fas fa-pencil-alt" aria-hidden="true"></i></a>

                                    <button class="video-content-button" onclick="deleteCourseResource('<?= $resource_mapping_id ?>', 'TEST')" data-bs-toggle="tooltip" title="Remove"><i class="fas fa-trash-alt" aria-hidden="true"></i></button>
                                </div>
                            </div>
                            <hr style="margin: 0;" />

                        <?php endforeach; ?>
                    </div>
                <?php
                endif;
                ?>


            </div>
        </div>

    </div>
</div>