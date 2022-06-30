<!-- Include Header -->
<?php include_once(APPPATH . "Views/header.php"); ?>

<!-- Custom CSS -->
<link href="<?php echo base_url('assets/css/release_updates.css?v=20220527'); ?>" rel="stylesheet">

<div id="content">
    <div class="container-fluid mt-4">

        <div class="flex-container-column">
            <div>
                <label class="h5 text-gray-800"> <?= $title; ?> </h5>
            </div>
            <div class="breadcrumb_div" aria-label="breadcrumb">
                <ol class="breadcrumb_custom">
                    <li class="breadcrumb_item"><a href="<?php echo base_url('/home'); ?>"> Home </a></li>
                    <li class="breadcrumb_item active" aria-current="page"> <?= $title; ?> </li>
                </ol>
            </div>
        </div>

        <div class="change_log_timeline">
            <div class="row align-items-center">
                <div class="col">
                    <ul class="timeline">
                    <li class="timeline-item">
                            <div class="timeline-info my-1">
                                <span>May 27, 2022</span>
                            </div>
                            <div class="timeline-content">
                                <ol class="timeline-content-list">
                                    <li>Exam flow changed for Offline exams. Now admin can directly upload a offline question paper PDF and scan OMR sheets</li>
                                    <li>WhatsApp Integration Beta launched! Now students can get WhatsApp messages for exam schedule and result</li>
                                    <li>Whatsapp Invitation can be sent to students with valid WhatsApp numbers</li>
                                </ol>
                            </div>
                        </li>
                        <li class="timeline-item">
                            <div class="timeline-info my-1">
                                <span>April 25, 2022</span>
                            </div>
                            <div class="timeline-content">
                                <ol class="timeline-content-list">
                                    <li>Option of sending username/password over Email/SMS to students immediately after excel import</li>
                                    <li>Sending account invitation link to individual student</li>
                                    <li>Progress of student excel import now being shown for better understanding</li>
                                    <li>Chart of weekly student login activity on admin dashboard</li>
                                </ol>
                            </div>
                        </li>
                        <li class="timeline-item">
                            <div class="timeline-info my-1">
                                <span>April 18, 2022</span>
                            </div>
                            <div class="timeline-content">
                                <ol class="timeline-content-list">
                                    <li> Enhancements for live classes module </li>
                                    <li> Minor enhancements and fixes for video proctoring and image proctoring </li>
                                    <li>Progress bar added for Import offline results and generate result to show progress of the overall process</li>
                                </ol>
                            </div>
                        </li>
                        <li class="timeline-item">
                            <div class="timeline-info my-1">
                                <span>April 06, 2022</span>
                            </div>
                            <div class="timeline-content">
                                <ol class="timeline-content-list">
                                    <li> Video Proctoring Feature Update </li>
                                    <li> Block parallel student logins for test </li>
                                    <li> Limit concurrent active users for DLP </li>
                                </ol>
                            </div>
                        </li>
                        <li class="timeline-item">
                            <div class="timeline-info my-1">
                                <span>March 28, 2022</span>
                            </div>
                            <div class="timeline-content">
                                <ol class="timeline-content-list">
                                    <li> OMR Feature </li>
                                </ol>
                            </div>
                        </li>
                        <li class="timeline-item">
                            <div class="timeline-info my-1">
                                <span>March 22, 2022</span>
                            </div>
                            <div class="timeline-content">
                                <ol class="timeline-content-list">
                                    <li> Test Template Feature (You can select existing template to fill up all entities) </li>
                                    <li> Real time exam overview - reset option added for the student exam session which will allow blocked students to login to the exam. </li>
                                </ol>
                            </div>
                        </li>
                        <li class="timeline-item">
                            <div class="timeline-info my-2">
                                <span>March 03, 2022</span>
                            </div>
                            <div class="timeline-content">
                                <h5 class="timeline-content-subheading my-2"> Features </h5>
                                <ol class="timeline-content-list">
                                    <li> Student List printing requirements, in excel as well - show institute name, logo and classroom selected</li>
                                </ol>

                                <h5 class="timeline-content-subheading my-2"> Bug Fixes </h5>
                                <ol class="timeline-content-list">
                                    <li> Admin Panel - Student performance clicking on any test goes to analysis section instead of student result </li>
                                    <li> Admin Panel - Offline result download template excel bug resolved </li>
                                </ol>
                            </div>
                        </li>
                        <li class="timeline-item">
                            <div class="timeline-info my-2">
                                <span>Dec 2021</span>
                            </div>
                            <div class="timeline-content">
                                <p> Version 2.0 - New Admin UI </p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Include Footer -->
<?php include_once(APPPATH . "Views/footer.php"); ?>