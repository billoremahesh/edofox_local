<?php

if (isset($post_data['dropdownValue'])) {
    //call the function or execute the code
    $serviceCallCounter = sanitize_input($post_data['serviceCallCounter']);

    // Saving value in session for on-reload state save of dropdowns
    $_SESSION['session_subject_filter'] = (isset($post_data['dropdownValue'])) ? sanitize_input($post_data['dropdownValue']) : "";
    $_SESSION['session_doubts_type_filter'] = (isset($post_data['doubts_type_filter'])) ? sanitize_input($post_data['doubts_type_filter']) : "";
    $_SESSION['session_time_filter'] = (isset($post_data['time_filter'])) ? sanitize_input($post_data['time_filter']) : "";


    if (isset($post_data['unResolvedObjectDataString'])) {
        renderUnResolvedDoubts(sanitize_input($post_data['dropdownValue']), sanitize_input($post_data['unResolvedObjectDataString']), $serviceCallCounter);

        $_SESSION['session_doubts_resolution_type_filter'] = "Unresolved";
    }
    if (isset($post_data['resolvedObjectDataString'])) {
        renderResolvedDoubts(sanitize_input($post_data['dropdownValue']), sanitize_input($post_data['resolvedObjectDataString']), $serviceCallCounter);

        $_SESSION['session_doubts_resolution_type_filter'] = "Resolved";
    }
}

/**
 * TO render UNRESOLVED DOUBTS in table 
 * @param  $selectedVal
 * @param  $objResolvedDoubts
 */
function renderUnResolvedDoubts($selectedVal, $objUnResolvedDoubts, $serviceCallCounter)
{
    /**
     * Fetching each question
     * For UNRESOLVED DOUBTS
     */


    /*
        highlight_string("<?php\n\$data =\n" . var_export($objUnResolvedDoubts, true) . ";\n?>");
        die;
    */

    if (isset($objUnResolvedDoubts["test"]["test"])) {
        $countOfQuestionsAvailable = 0;
        foreach ($objUnResolvedDoubts["test"]["test"] as $doubt) {
            if ((isset($doubt['subjectId']) && $doubt['subjectId'] == $selectedVal) || $selectedVal == -1) {

                $countOfQuestionsAvailable++;

                if (isset($doubt['question'])) {
                    $questionText = $doubt['question'];
                    $questionText = str_replace('$$', '$', $questionText);
                } else {
                    $questionText = "";
                }

                $feedbackQuestionId = $doubt['id'];

                if (isset($doubt['questionImageUrl'])) {
                    $question_imgUrl = $doubt['questionImageUrl'];
                    $question_imgUrl = str_replace("/var/www/reliancedlp.edofox.com/public_html", "https://reliancedlp.edofox.com", $question_imgUrl);
                    $question_imgUrl = str_replace("/var/www/edofoxlatur.com/public_html", "https://test.edofox.com", $question_imgUrl);
                    if (!empty($question_imgUrl)) {
                        $question_imgUrlTag = "<a href='$question_imgUrl' target='_blank'><img src='$question_imgUrl' class='img-fluid d-block m-auto doubt-image' alt='question-image'></a>";
                    } else {
                        $question_imgUrlTag = "";
                    }
                } else {
                    $question_imgUrlTag = "";
                }
                $feedbackType = null;
                $sourceVideoName = "";

                if (isset($doubt['feedback'])) {
                    if (isset($doubt['feedback']['frequency'])) {
                        $feedbackFrequency = $doubt['feedback']['frequency'];
                    }

                    if (isset($doubt['feedback']['sourceVideoUrl'])) {
                        $sourceVideoUrl = $doubt['feedback']['sourceVideoUrl'];
                    }
                    if (isset($doubt['feedback']['sourceVideoName'])) {
                        $sourceVideoName = $doubt['feedback']['sourceVideoName'];
                        $feedbackType = "video";
                    }
                    if (isset($doubt['feedback']['feedback'])) {
                        $feedbackText = $doubt['feedback']['feedback'];
                    }
                    if (isset($doubt['feedback']['id'])) {
                        $feedbackType = "general";
                        if (isset($doubt['feedback']['attachment'])) {
                            $attachment = $doubt['feedback']['attachment'];
                        }
                        if (isset($doubt['feedback']['askedBy'])) {
                            $askedBy = $doubt['feedback']['askedBy'];
                        }
                    }
                } else {
                    $feedbackFrequency = "-";
                }

?>
                <div class="card card-body shadow">
                    <div class="row">
                        <div class="col-12">

                            <div class="d-flex justify-content-between">
                                <div>
                                    <?php if (isset($doubt['subject'])) : ?>
                                        <span class="badge rounded-pill bg-secondary" data-bs-toggle="tooltip" title="Subject"><?= $doubt['subject'] ?></span>
                                    <?php endif; ?>

                                    <?php if (isset($askedBy) && !empty($askedBy)) : ?>
                                        <span class="badge rounded-pill bg-warning ms-2" data-bs-toggle="tooltip" title="Doubt Asked By"><?= $askedBy ?></span>
                                    <?php endif; ?>
                                </div>

                                <?php if (isset($feedbackFrequency)) : ?>

                                    <a class='position-relative btn btn-light bg-transparent text-warning btn-sm' href="<?= base_url('doubts/doubt_details/' . $feedbackQuestionId . '/' . $feedbackType); ?>" data-bs-toggle="tooltip" title="<?= $feedbackFrequency ?> students raised this doubt">
                                        <span class="material-icons">record_voice_over</span>
                                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning">
                                            <?= $feedbackFrequency ?>
                                            <span class="visually-hidden"><?= $feedbackFrequency ?> students raised this doubt</span>
                                        </span>
                                    </a>

                                <?php endif; ?>
                            </div>

                            <p><span class="questionText"><?= $questionText ?></span></p>
                            <p><?= $question_imgUrlTag ?></p>

                            <!-- Show feedback attachment in case of general feedback -->
                            <?php if ($feedbackType == 'general' && isset($attachment) && !empty($attachment)) : ?>
                                <div><a class="text-success" href="<?= $attachment ?>" target="_blank" data-bs-toggle='tooltip' title='Doubt attachment'> <img src='<?= $attachment ?>' class='img-fluid d-block m-auto doubt-image' alt='doubt attachment'> </a></div>
                            <?php endif; ?>



                            <?php if (isset($sourceVideoUrl) && !empty($sourceVideoUrl)) :
                                if (strpos($sourceVideoUrl, "m3u8") !== false || strpos($sourceVideoUrl, "mogiio.com") !== false) : ?>
                                    <div class="embed-responsive embed-responsive-16by9 text-center">
                                        <video id="my-video" class="video-js video-js-responsive-container text-center center-block embed-responsive-item" controls preload="auto" data-setup='{ "playbackRates": [0.5, 1, 1.25, 1.5, 2] }'>
                                            <source src="<?= $sourceVideoUrl ?>" type="application/x-mpegURL" />
                                            <p class="vjs-no-js">
                                                To view this video please use latest Google Chrome browser
                                                <a href="https://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a>
                                            </p>
                                        </video>
                                    </div>
                                <?php else : ?>
                                    <iframe class="video-frame" src="<?= $sourceVideoUrl ?>" frameborder="0" allow="fullscreen" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
                                <?php endif; ?>
                                <p class="fw-bold" data-bs-toggle='tooltip' title='Video Title'><?= $sourceVideoName ?></p>
                            <?php endif; ?>

                            <p data-bs-toggle='tooltip' title='Doubt Text'><?= $feedbackText ?></p>

                        </div>
                        <div class="col-12 d-flex justify-content-center align-items-center">
                            <?php
                            $feedbackType_string = "";
                            if ($feedbackType) :
                                $feedbackType_string = "/" . $feedbackType;
                            endif;
                            ?>
                            <button class="btn btn-light text-primary fw-bold text-uppercase" onclick="show_edit_modal('modal_div','resolve_doubts_modal','doubts/resolve_doubts_modal/<?= $feedbackQuestionId; ?><?= $feedbackType_string; ?>')">
                                Resolve
                            </button>

                        </div>
                    </div>
                </div>
            <?php
            }
        }
    } else {
        $more_word = "";
        if ($serviceCallCounter > 0) {
            $more_word = "more";
        }
        echo "<div class='text-center border border-3 border-danger text-danger p-3 rounded'>No $more_word pending doubts available for this subject.</div>";
    }
}

/**
 * TO render RESOLVED DOUBTS in table
 * @param  $selectedVal
 * @param  $objResolvedDoubts
 */
function renderResolvedDoubts($selectedVal, $objResolvedDoubts, $serviceCallCounter)
{
    /**
     * Fetching each question
     * For RESOLVED DOUBTS
     */

    if (isset($objResolvedDoubts["test"]["test"])) {
        $countOfQuestionsAvailable = 0;
        foreach ($objResolvedDoubts["test"]["test"] as $doubt) {

            if ((isset($doubt['subjectId']) && $doubt['subjectId'] == $selectedVal) || $selectedVal == -1) {

                $countOfQuestionsAvailable++;

                if (isset($doubt['question'])) {
                    $questionText = $doubt['question'];
                    $questionText = str_replace('$$', '$', $questionText);
                } else {
                    $questionText = "";
                }

                $feedbackQuestionId = $doubt['id'];

                if (isset($doubt['questionImageUrl'])) {
                    $question_imgUrl = $doubt['questionImageUrl'];
                    $question_imgUrl = str_replace("/var/www/reliancedlp.edofox.com/public_html", "https://reliancedlp.edofox.com", $question_imgUrl);
                    $question_imgUrl = str_replace("/var/www/edofoxlatur.com/public_html", "https://test.edofox.com", $question_imgUrl);
                    if (!empty($question_imgUrl)) {
                        $question_imgUrlTag = "<a href='$question_imgUrl' target='_blank'><img src='$question_imgUrl' class='img-fluid d-block m-auto doubt-image' alt='question-image'></a>";
                    } else {
                        $question_imgUrlTag = "";
                    }
                } else {
                    $question_imgUrlTag = "";
                }


                $sourceVideoUrl = "";
                $sourceVideoName = "";
                $feedbackText = "";

                $feedbackFrequency = "";
                if (isset($doubt['feedback'])) {
                    if (isset($doubt['feedback']['frequency'])) {
                        $feedbackFrequency = $doubt['feedback']['frequency'];
                    }


                    $feedbackVideoUrlHtmlTag = "";
                    if (isset($doubt['feedback']['feedbackVideoUrl']) && !empty($doubt['feedback']['feedbackVideoUrl'])) {
                        $feedbackVideoUrl = $doubt['feedback']['feedbackVideoUrl'];
                        $feedbackVideoUrlHtmlTag = "<a class='text-primary' href='$feedbackVideoUrl' target='_blank' data-bs-toggle='tooltip' title='Doubt Explanation Video'> <span class='material-icons'>play_circle_outline</span></a>";
                    }

                    $feedbackResolutionTextHtmlTag = "";
                    if (isset($doubt['feedback']['feedbackResolutionText']) && !empty($doubt['feedback']['feedbackResolutionText'])) {
                        $feedbackResolutionTextHtmlTag = $doubt['feedback']['feedbackResolutionText'];
                    }
                    $feedbackType = null;
                    $sourceVideoUrl = "";
                    if (isset($doubt['feedback']['sourceVideoUrl'])) {
                        $sourceVideoUrl = $doubt['feedback']['sourceVideoUrl'];
                    }
                    $sourceVideoName = "";
                    if (isset($doubt['feedback']['sourceVideoName'])) {
                        $sourceVideoName = $doubt['feedback']['sourceVideoName'];
                        $feedbackType = "video";
                    }
                    $feedbackText = "";
                    if (isset($doubt['feedback']['feedback'])) {
                        $feedbackText = $doubt['feedback']['feedback'];
                    }
                    $attachment = "";
                    if (isset($doubt['feedback']['id'])) {
                        $feedbackType = "general";
                        if (isset($doubt['feedback']['attachment'])) {
                            $attachment = $doubt['feedback']['attachment'];
                        }
                    }
                    $feedbackResolutionImg = "";
                    if (isset($doubt['feedback']['feedbackResolutionImageUrl'])) {
                        $feedbackResolutionImg = $doubt['feedback']['feedbackResolutionImageUrl'];
                    }
                } else {
                    $feedbackFrequency = "-";
                }

            ?>
                <div class="card card-body shadow">
                    <div class="row">
                        <div class="col-12">

                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <?php if (isset($doubt['subject'])) : ?>
                                        <span class="badge rounded-pill bg-secondary" data-bs-toggle="tooltip" title="Subject"><?= $doubt['subject'] ?></span>
                                    <?php endif; ?>
                                </div>


                                <?php if (isset($feedbackFrequency)) : ?>

                                    <a class='position-relative btn btn-light bg-transparent text-warning btn-sm' href="<?= base_url('doubts/doubt_details/' . $feedbackQuestionId . '/' . $feedbackType); ?>" data-bs-toggle="tooltip" title="<?= $feedbackFrequency ?> students raised this doubt">
                                        <span class="material-icons">record_voice_over</span>
                                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning">
                                            <?= $feedbackFrequency ?>
                                            <span class="visually-hidden"><?= $feedbackFrequency ?> students raised this doubt</span>
                                        </span>
                                    </a>

                                <?php endif; ?>
                            </div>

                            <p><span class="questionText"><?= $questionText ?></span></p>
                            <p><?= $question_imgUrlTag ?></p>

                            <?php if ($feedbackType == 'general' && isset($attachment) && !empty($attachment)) : ?>
                                <div><a class="text-success" href="<?= $attachment ?>" target="_blank" data-bs-toggle='tooltip' title='Doubt attachment'> <img src='<?= $attachment ?>' class='img-fluid d-block m-auto doubt-image' alt='doubt attachment'></a></div>
                            <?php endif; ?>



                            <?php if (isset($sourceVideoUrl) && !empty($sourceVideoUrl)) :
                                if (strpos($sourceVideoUrl, "m3u8") !== false || strpos($sourceVideoUrl, "mogiio.com") !== false) : ?>
                                    <div class="embed-responsive embed-responsive-16by9 text-center">
                                        <video id="my-video" class="video-js video-js-responsive-container text-center center-block embed-responsive-item" controls preload="auto" data-setup='{ "playbackRates": [0.5, 1, 1.25, 1.5, 2] }'>
                                            <source src="<?= $sourceVideoUrl ?>" type="application/x-mpegURL" />
                                            <p class="vjs-no-js">
                                                To view this video please use latest Google Chrome browser
                                                <a href="https://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a>
                                            </p>
                                        </video>
                                    </div>
                                <?php else : ?>
                                    <iframe class="video-frame" src="<?= $sourceVideoUrl ?>" frameborder="0" allow="fullscreen" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
                                <?php endif; ?>
                                <p class="fw-bold" data-bs-toggle='tooltip' title='Video Title'><?= $sourceVideoName ?></p>
                            <?php endif; ?>

                            <p data-bs-toggle='tooltip' title='Doubt Text'><?= $feedbackText ?></p>



                            <div class="card border border-2 border-success mb-3 bg-light m-auto" style="max-width: 90%;">
                                <div class="card-body text-success">
                                    <h5 class="card-title">Resolution</h5>
                                    <p class="card-text"><?= $feedbackResolutionTextHtmlTag ?></p>

                                    <div class="d-flex">
                                        <?= $feedbackVideoUrlHtmlTag ?>

                                        <?php if (isset($feedbackResolutionImg) && !empty($feedbackResolutionImg)) : ?>
                                            <?php
                                            $prefix = '/';
                                            if (strpos($feedbackResolutionImg, 'http') >= 0) {
                                                $prefix = '';
                                            }
                                            ?>
                                            <a class="text-primary" href="<?= $prefix . $feedbackResolutionImg ?>" target="_blank" data-bs-toggle='tooltip' title='View resolution'><span class="material-icons">image</span></a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>


                        </div>

                        <?php
                        $feedbackType_string = "";
                        if ($feedbackType) :
                            $feedbackType_string = "/" . $feedbackType;
                        endif;
                        ?>


                        <div class="col-12 d-flex justify-content-center align-items-center">
                            <button class="btn btn-light text-danger fw-bold text-uppercase" onclick="show_edit_modal('modal_div','move_to_pending_doubt_modal','doubts/move_to_pending_doubt_modal/<?= $feedbackQuestionId; ?><?= $feedbackType_string; ?>')">Move to Pending</button>
                        </div>

                    </div>
                </div>

<?php
            }
        }
    } else {
        $more_word = "";
        if ($serviceCallCounter > 0) {
            $more_word = "more";
        }
        echo "<div class='text-center border border-3 border-danger text-danger p-3 rounded'>No $more_word resolved doubts available for this subject.</div>";
    }
}

?>