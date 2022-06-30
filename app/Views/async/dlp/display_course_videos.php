<?php

if (isset($videoUrl)) {

    $videoUrl = str_replace("https://vimeo.com", "https://player.vimeo.com/video", $videoUrl);
    $videoUrl = str_replace("youtube.com/watch?v=", "youtube.com/embed/", $videoUrl);

    $studentID = 0;
    $studentName = "";

    // To show or block video on WEB 
    $video_constraint = isset($_SESSION["videoConstraint"]) ? $_SESSION["videoConstraint"] : "";
    $app_url = isset($_SESSION["appUrl"]) ? $_SESSION["appUrl"] : "";
?>

    <style>
        /* For mobile screens */
        .video-player-frame {
            display: block;
            margin: 16px auto;
            width: 100%;
        }

        @media (min-width: 768px) {
            .video-player-frame {
                min-width: 480px;
                min-height: 360px;
            }
        }

        @media (min-width: 992px) {
            .video-player-frame {
                min-width: 640px;
                min-height: 480px;
            }
        }
    </style>


    <div class="modal-header">
        <h6 class="modal-title"><?= $title; ?></h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <?php if ($video_constraint == "APP") : ?>

            <?php if (isset($_COOKIE['isPureAndroidApp']) || isset($_COOKIE['isApplication'])) : ?>


                <?php if (strpos($videoUrl, "m3u8") !== false || strpos($videoUrl, "mogiio.com") !== false) : ?>
                    <?php if (isset($status) && $status == 'Transcoding') : ?>
                        <p> Please wait as we are preparing this video for you </p>
                    <?php elseif (isset($status) && $status == 'Uploading') : ?>
                        <p> This video was not uploaded properly. Please upload again. </p>
                    <?php else : ?>
                        <div class="ratio ratio-16x9">
                            <div>
                                <video id="my-video" class="video-js video-js-responsive-container text-center center-block w-100 h-100" controls preload="auto" data-setup='{ "playbackRates": [0.5, 1, 1.25, 1.5, 2] }'>
                                    <source src="<?= $videoUrl ?>" type="application/x-mpegURL" />
                                    <p class="vjs-no-js">
                                        To view this video please use latest Google Chrome browser
                                        <a href="https://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a>
                                    </p>
                                </video>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php else : ?>
                    <iframe id="watchVideo" class="video-player-frame" src="<?= $videoUrl ?>" frameborder="0" allow="fullscreen" allowfullscreen></iframe>
                <?php endif; ?>

            <?php else : ?>
                <div class="text-center">
                    <img class="img-responsive" src="<?= base_url('assets/img/statics/app-download-notice-static.jpg'); ?>" style="margin:auto;width:100%; max-width:600px" />
                    <p>Please download Our Official Android App to watch the video. Click below</p>
                    <a href="<?= $app_url ?>" target="_blank"><img src="<?= base_url('assets/img/google-play-badge.png'); ?>" style="width: 150px;" /></a>
                </div>

            <?php endif; ?>

        <?php else : ?>

 


            <!-- This below video part is hidden on MOBILE on student side using JS -->
            <?php if (strpos($videoUrl, "m3u8") !== false || strpos($videoUrl, "mogiio.com") !== false) : ?>
                <?php if (isset($status) && $status == 'Transcoding') : ?>
                    <p> Please wait as we are preparing this video for you </p>
                <?php elseif (isset($status) && $status == 'Failed') : ?>
                    <p> ERROR! Failed to convert the video. Please check video format and upload again. </p>
                <?php elseif (isset($status) && $status == 'Uploading') : ?>
                    <p> This video was not uploaded properly. Please upload again. </p>
                <?php else : ?>
                    <div class="ratio ratio-16x9" id="watchVideo">
                        <div>
                            <video id="my-video" class="video-js video-js-responsive-container text-center center-block w-100 h-100" controls preload="auto" data-setup='{ "playbackRates": [0.5, 1, 1.25, 1.5, 2] }'>
                                <source src="<?= $videoUrl ?>" type="application/x-mpegURL" />
                                <p class="vjs-no-js">
                                    To view this video please use latest Google Chrome browser
                                    <a href="https://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a>
                                </p>
                            </video>
                        </div>
                    </div>
                <?php endif; ?>
            <?php elseif (empty($videoUrl)) : ?>
                <p> Please wait as we are preparing this video for you </p>
            <?php else : ?>
                <iframe id="watchVideo" class="video-player-frame" src="<?= $videoUrl ?>" frameborder="0" allow="fullscreen" allowfullscreen></iframe>
            <?php endif; ?>

        <?php endif; ?>

        <?php if (isset($testId) && $testId != 0) :
        ?>
            <p>Assignment:
                <button type="button" class="btn btn-secondary start_test_button" name="test_frm" onclick="loadTestData('<?php echo $studentID; ?>','<?php echo $studentName; ?>','<?php echo $testId; ?>','<?php echo $testName; ?>','--');"><?= $testName ?></button>
            </p>
        <?php endif; ?>
    </div>

<?php
} else {
    echo "<p>Video URL not found. Please try again later.</p>";
}
?>