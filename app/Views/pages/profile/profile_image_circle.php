<style>
    .img_profile {
        background-repeat: no-repeat;
        background-size: cover;
        border-radius: 50%;
        margin-right: 8px;
        background-position: center;
        height: <?= $size ?>;
        width: <?= $size ?>;
        background-image: url(<?php if ($profile_picture_url) {
                                    $profile_img_prefix = "../";
                                    if (strpos($profile_picture_url, 'http') >= 0) {
                                        $profile_img_prefix = "";
                                    }
                                    echo $profile_img_prefix . $profile_picture_url;
                                } else {
                                    echo base_url('assets/img/edofox-logo-round-bg.svg');
                                } ?>
);
    }
</style>

<div class="img_profile"></div>