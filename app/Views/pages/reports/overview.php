<!-- Include Header -->
<?php include_once(APPPATH . "Views/header.php"); ?>

<!-- Custom CSS -->
<link href="<?php echo base_url('assets/css/reports/overview.css?v=20210918'); ?>" rel="stylesheet">

<div id="content">
    <div class="container-fluid mt-4">

        <div class="flex-container-column">
            <div>
                <label class="h5 text-gray-800 text-uppercase"> <?= $title; ?> </h5>
            </div>
            <div class="breadcrumb_div" aria-label="breadcrumb">
                <ol class="breadcrumb_custom">
                    <li class="breadcrumb_item"><a href="<?php echo base_url('/home'); ?>"> Home </a></li>
                    <li class="breadcrumb_item active" aria-current="page"> <?= $title; ?> </li>
                </ol>
            </div>
        </div>




        <div class="row">
            <div class="col-sm-12 col-md-3 mb-3">
                <a class="organization_card_links" href="<?php echo base_url('/lectures/video_analysis'); ?>">
                    <div class="md_card">
                        <div class="md_card_media text-center">
                            <img class="img-fluid" src="<?= base_url('assets/img/statics/video_analysis_report.jpg'); ?>" style="width:100%;width:300px;margin: auto;" />
                        </div>
                        <div class="md_card_body">
                            <div class="md_card_title text-center text-uppercase">
                                Videos analysis
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-sm-12 col-md-3 mb-3">
                <a class="organization_card_links" href="<?php echo base_url('/reports/live_lectures_analysis'); ?>">
                    <div class="md_card">
                        <div class="md_card_media text-center">
                            <img class="img-fluid" src="<?= base_url('assets/img/statics/live-lecture-analysis.jpg'); ?>" style="width:100%; max-width:200px; margin: auto;" />
                        </div>
                        <div class="md_card_body">
                            <div class="md_card_title text-center text-uppercase">
                                LIVE LECTURE ANALYSIS
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <?php if (in_array("all_perms", session()->get('perms'))) :  ?>
                <div class="col-sm-12 col-md-3 mb-3">
                    <a class="organization_card_links" href="<?php echo base_url('/activityLogs'); ?>">
                        <div class="md_card">
                            <div class="md_card_media text-center">
                                <img class="img-fluid" src="<?= base_url('assets/img/statics/activity.jpg'); ?>" style="width:100%; max-width:200px; margin: auto;" />
                            </div>
                            <div class="md_card_body">
                                <div class="md_card_title text-center text-uppercase">
                                    Admin Activity
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endif; ?>

            <!-- Average Marks Report -->
            <div class="col-sm-12 col-md-3 mb-3">
                <a class="organization_card_links" href="<?php echo base_url('/tests/avg_marks_report'); ?>">
                    <div class="md_card">
                        <div class="md_card_media text-center">
                            <img class="img-fluid" src="<?= base_url('assets/img/statics/avg_test_report.jpg'); ?>" style="width:100%; max-width:200px; margin: auto;" />
                        </div>
                        <div class="md_card_body">
                            <div class="md_card_title text-center text-uppercase">
                                Average Marks Report
                            </div>
                        </div>
                    </div>
                </a>
            </div>


            <!-- Student Login Access Report -->
            <?php
            $max_student_tokens = $_SESSION['max_student_tokens'];
            $max_dlp_tokens = $_SESSION['max_dlp_tokens'];
            if (in_array("all_perms", session()->get('perms')) or !empty($max_student_tokens) or !empty($max_dlp_tokens)) :
            ?>
                <div class="col-sm-12 col-md-3 mb-3">
                    <a class="organization_card_links" href="<?php echo base_url('/reports/student_device_tracker'); ?>">
                        <div class="md_card">
                            <div class="md_card_media text-center">
                                <img class="img-fluid" src="<?= base_url('assets/img/statics/location_track.jpg'); ?>" style="width:100%; max-width:200px; margin: auto;" />
                            </div>
                            <div class="md_card_body">
                                <div class="md_card_title text-center text-uppercase">
                                    Student Device Tracker
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-sm-12 col-md-3 mb-3">
                    <a class="organization_card_links" href="<?php echo base_url('/reports/student_login_sessions'); ?>">
                        <div class="md_card">
                            <div class="md_card_media text-center">
                                <img class="img-fluid" src="<?= base_url('assets/img/statics/live_session.jpg'); ?>" style="width:100%; max-width:200px; margin: auto;" />
                            </div>
                            <div class="md_card_body">
                                <div class="md_card_title text-center text-uppercase">
                                    Active student sessions
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-sm-12 col-md-3 mb-3">
                    <a class="organization_card_links" href="<?php echo base_url('/reports/view_student_attendance'); ?>">
                        <div class="md_card">
                            <div class="md_card_media text-center">
                                <img class="img-fluid" src="<?= base_url('assets/img/statics/attendance_report.jpg'); ?>" style="width:100%; max-width:200px; margin: auto;" />
                            </div>
                            <div class="md_card_body">
                                <div class="md_card_title text-center text-uppercase">
                                 Attendance Report
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

              

            <?php endif; ?>


        </div>

    </div>

    <!-- Include Footer -->
    <?php include_once(APPPATH . "Views/footer.php"); ?>