<style>
    .offcanvas-start {
        width: 280px;
    }
</style>
<?php
// Get Service URI
$uri = service('uri');
$userType = session()->get('user_type');
?>
<div class="offcanvas offcanvas-start" tabindex="-1" id="edofox-offcanvas" aria-labelledby="offcanvasExampleLabel">
    <div class="offcanvas-header">
        <!-- Brand -->
        <a class="sidebar-brand text-center" href="<?= base_url('/home'); ?>">
            <?php
            if ($userType == "super_admin") {
            ?>
                <img class="img-fluid" alt="logo-img" src="<?= base_url('/assets/img/edofox-new-logo.png'); ?>" style="max-height: 32px; max-width: 100%;">
            <?php
            } elseif (isset($_SESSION['logo_path'])) {
                $logoUrl = $_SESSION['logo_path'];
                $logo_prefix = "../";
                if (strpos($logoUrl, 'http') >= 0) {
                    $logo_prefix = "";
                }
            ?>

                <img class="img-fluid" alt="logo-img" src="<?= $logo_prefix . $logoUrl; ?>" style="max-height: 32px; max-width: 100%;">

            <?php
            } ?>
        </a>


        <a class="btn text-white" data-bs-dismiss="offcanvas" aria-label="Close">
            <span class="material-icons text-white-50">keyboard_double_arrow_left</span>
        </a>

    </div>
    <div class="offcanvas-body">

        <div>

            <ul class="nav nav-pills flex-column mb-auto">

                <li>
                    <a class="nav-link <?= ($uri->getTotalSegments() >= 0 && ($uri->getSegment(1) == '' || $uri->getSegment(1) == 'home')) ? 'active' : 'link-dark' ?>" href="<?= base_url('/home'); ?>">

                        <span class="align-middle me-2 material-icons">
                            dashboard
                        </span>
                        <span class="align-middle"> Dashboard </span>
                    </a>
                </li>

                <?php if (in_array("view_tests", session()->get('perms')) or in_array("all_perms", session()->get('perms'))) :  ?>
                    <?php if (session()->get('exam_feature') != 0) { ?>
                        <li>
                            <a class="nav-link <?= ($uri->getTotalSegments() >= 1 && $uri->getSegment(1) == 'tests') ? 'active' : 'link-dark'; ?>" href="<?= base_url('tests'); ?>">
                                <span class="align-middle me-2 material-icons">
                                    summarize
                                </span>
                                <span class="align-middle">Manage Tests </span>
                            </a>
                        </li>
                    <?php } else { ?>
                        <li>
                            <a class="nav-link nav-disabled-link" href="<?= base_url('/home/feature_blocked'); ?>">
                                <span class="align-middle me-2 material-icons">
                                    summarize
                                </span>
                                <span class="align-middle">Manage Tests </span>
                            </a>
                        </li>

                <?php }
                endif; ?>

                <?php if (in_array("view_students", session()->get('perms')) or in_array("all_perms", session()->get('perms'))) :  ?>
                    <li>
                        <a class="nav-link <?= ($uri->getTotalSegments() >= 1 && $uri->getSegment(1) == 'students') ? 'active' : 'link-dark'; ?>" href="<?= base_url('students'); ?>">
                            <span class="align-middle me-2 material-icons">
                                people
                            </span>
                            <span class="align-middle">Manage Students </span>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if (in_array("view_staff", session()->get('perms')) or in_array("all_perms", session()->get('perms'))) :  ?>
                    <li>
                        <a class="nav-link <?= ($uri->getTotalSegments() >= 1 && $uri->getSegment(1) == 'staff') ? 'active' : 'link-dark'; ?>" href="<?= base_url('staff'); ?>">
                            <span class="align-middle me-2 material-icons">
                                groups
                            </span>
                            <span class="align-middle">Manage Staff </span>
                        </a>
                    </li>
                <?php endif; ?>

                <!--  Schedule Management -->
                <?php if (in_array("view_schedule", session()->get('perms')) or in_array("all_perms", session()->get('perms'))) :  ?>
                    <li>
                        <a class="nav-link <?= ($uri->getTotalSegments() >= 1 && $uri->getSegment(1) == 'schedule') ? 'active' : 'link-dark'; ?>" href="<?= base_url('schedule'); ?>">
                            <span class="align-middle me-2 material-icons">
                            calendar_month
                            </span>
                            <span class="align-middle"> Schedule Management </span>
                        </a>
                    </li>
                <?php endif; ?>

                <!-- Attendance Management -->
                <?php if (in_array("view_attendance", session()->get('perms')) or in_array("all_perms", session()->get('perms'))) :  ?>
                    <li>
                        <a class="nav-link <?= ($uri->getTotalSegments() >= 1 && $uri->getSegment(1) == 'attendance') ? 'active' : 'link-dark'; ?>" href="<?= base_url('attendance'); ?>">
                            <span class="align-middle me-2 material-icons">
                                rule
                            </span>
                            <span class="align-middle"> Attendance Management </span>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if (in_array("view_dlp", session()->get('perms')) or in_array("all_perms", session()->get('perms'))) :  ?>
                    <?php
                    if (session()->get('dlp_count') == 1) {
                    ?>
                        <li>
                            <a class="nav-link <?= ($uri->getTotalSegments() >= 1 && $uri->getSegment(1) == 'dlp') ? 'active' : 'link-dark'; ?>" href="<?= base_url('dlp'); ?>">
                                <span class="align-middle me-2 material-icons">
                                    cast_for_education
                                </span>
                                <span class="align-middle"> Learning Content </span>
                            </a>
                        </li>
                    <?php
                    } else {
                    ?>
                        <li>
                            <a class="nav-link nav-disabled-link" href="<?= base_url('/home/feature_blocked'); ?>">
                                <span class="align-middle me-2 material-icons">
                                    cast_for_education
                                </span>
                                <span class="align-middle"> DLP </span>
                            </a>
                        </li>

                    <?php
                    }
                    ?>
                <?php endif; ?>

                <?php if (in_array("view_classrooms", session()->get('perms')) or in_array("all_perms", session()->get('perms'))) :  ?>
                    <li>
                        <a class="nav-link <?= ($uri->getTotalSegments() == 1 && $uri->getSegment(1) == 'classrooms') ? 'active' : 'link-dark'; ?>" href="<?= base_url('classrooms'); ?>">
                            <span class="align-middle me-2 material-icons">
                                class
                            </span>
                            <span class="align-middle"> Classrooms </span>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if (in_array("view_syllabus", session()->get('perms')) or in_array("all_perms", session()->get('perms'))) :  ?>
                    <li>
                        <a class="nav-link <?= ($uri->getTotalSegments() == 1 && $uri->getSegment(1) == 'syllabus') ? 'active' : 'link-dark'; ?>" href="<?= base_url('syllabus'); ?>">
                            <span class="align-middle me-2 material-icons">
                                class
                            </span>
                            <span class="align-middle"> Syllabus <span class="badge rounded-pill bg-warning" data-bs-toggle="tooltip" title="Beta Release for early adopters">Beta</span>
</span>
                        </a>
                    </li>
                <?php endif; ?> 

                <?php if (in_array("view_academic_plan", session()->get('perms')) or in_array("all_perms", session()->get('perms'))) :  ?>
                    <li>
                        <a class="nav-link <?= ($uri->getTotalSegments() == 1 && $uri->getSegment(1) == 'academic') ? 'active' : 'link-dark'; ?>" href="<?= base_url('academic'); ?>">
                            <span class="align-middle me-2 material-icons">
                                class
                            </span>
                            <span class="align-middle"> Academic Plan</span>
                        </a>
                    </li>
                <?php endif; ?> 


                <?php if (in_array("view_classrooms", session()->get('perms')) or in_array("all_perms", session()->get('perms'))) :  ?>

                    <?php
                    if (session()->get('live_count') == 1) {
                    ?>

                        <li>
                            <a class="nav-link <?= ($uri->getTotalSegments() >= 1 && $uri->getSegment(1) == 'liveClassrooms') ? 'active' : 'link-dark'; ?>" href="<?= base_url('/liveClassrooms'); ?>">
                                <span class="align-middle me-2 material-icons">
                                    video_camera_front
                                </span>
                                <span class="align-middle"> Start Live Classroom </span>
                            </a>
                        </li>
                    <?php
                    } else {
                    ?>
                        <li>
                            <a class="nav-link nav-disabled-link" href="<?= base_url('/home/feature_blocked'); ?>">
                                <span class="align-middle me-2 material-icons">
                                    video_camera_front
                                </span>
                                <span class="align-middle"> Start Live Classroom </span>
                            </a>
                        </li>
                    <?php
                    }
                    ?>

                <?php endif; ?>


                <?php if (in_array("view_doubts", session()->get('perms')) or in_array("all_perms", session()->get('perms'))) :  ?>
                    <li>
                        <a class="nav-link <?= ($uri->getTotalSegments() >= 1 && $uri->getSegment(1) == 'doubts') ? 'active' : 'link-dark'; ?>" href="<?= base_url('doubts'); ?>">
                            <span class="align-middle me-2 material-icons">
                                help_center
                            </span>
                            <span class="align-middle"> Doubts </span>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if (in_array("view_lectures", session()->get('perms')) or in_array("all_perms", session()->get('perms'))) :  ?>
                    <!-- <li>
                        <a class="nav-link <?= ($uri->getTotalSegments() >= 1 && $uri->getSegment(1) == 'lectures') ? 'active' : 'link-dark'; ?>" href="<?= base_url('lectures'); ?>">
                            <i class="align-middle me-2 fas fa-chalkboard-teacher"></i> <span class="align-middle"> Lectures </span>
                        </a>
                    </li> -->
                <?php endif; ?>

                <?php if (in_array("view_question_bank", session()->get('perms')) or in_array("all_perms", session()->get('perms'))) :  ?>
                    <?php if (session()->get('exam_feature') != 0) { ?>
                        <li>
                            <a class="nav-link <?= ($uri->getTotalSegments() >= 1 && $uri->getSegment(1) == 'questionBank') ? 'active' : 'link-dark'; ?>" href="<?= base_url('questionBank'); ?>">
                                <span class="align-middle me-2 material-icons">
                                    question_answer
                                </span>
                                <span class="align-middle"> Question Bank </span>
                            </a>
                        </li>

                    <?php } else { ?>
                        <li>
                            <a class="nav-link nav-disabled-link" href="<?= base_url('/home/feature_blocked'); ?>">
                                <span class="align-middle me-2 material-icons">
                                    question_answer
                                </span>
                                <span class="align-middle"> Question Bank </span>
                            </a>
                        </li>

                <?php }
                endif; ?>



                <?php if (in_array("view_reports", session()->get('perms')) or in_array("all_perms", session()->get('perms'))) :  ?>
                    <li><a class="nav-link <?= ($uri->getTotalSegments() >= 1 && $uri->getSegment(1) == 'reports') ? 'active' : 'link-dark'; ?>" href="<?= base_url('reports'); ?>">
                            <span class="align-middle me-2 material-icons">
                                analytics
                            </span>
                            <span class="align-middle"> Reports </span>
                        </a>
                    </li>
                <?php endif; ?>

                <?php
                $userType = session()->get('user_type');
                if ($userType == "super_admin") :
                ?>

                    <?php if (in_array("manage_institutes", session()->get('perms')) or in_array("all_super_admin_perms", session()->get('perms'))) :  ?>

                        <li>
                            <a class="nav-link <?= ($uri->getTotalSegments() >= 0 && ($uri->getSegment(1) == 'institutes')) ? 'active' : 'link-dark' ?>" href="<?= base_url('/institutes'); ?>">
                                <span class="align-middle me-2 material-icons">
                                    location_city
                                </span>
                                <span class="align-middle"> Institutes </span>
                            </a>
                        </li>
                    <?php endif; ?>



                    <?php if (in_array("manage_sales_team", session()->get('perms')) or in_array("all_super_admin_perms", session()->get('perms'))) :  ?>

                        <li>
                            <a class="nav-link <?= ($uri->getTotalSegments() >= 0 && ($uri->getSegment(1) == 'SuperAdmins')) ? 'active' : 'link-dark' ?>" href="<?= base_url('/SuperAdmins'); ?>">
                                <span class="align-middle me-2 material-icons">
                                    people
                                </span>
                                <span class="align-middle"> Team Members </span>
                            </a>
                        </li>

                    <?php endif; ?>

                    <?php if (in_array("manage_billing", session()->get('perms')) or in_array("all_super_admin_perms", session()->get('perms'))) :  ?>

                        <li>
                            <a class="nav-link <?= ($uri->getTotalSegments() >= 0 && ($uri->getSegment(1) == 'invoices')) ? 'active' : 'link-dark' ?>" href="<?= base_url('/invoices'); ?>">
                                <span class="align-middle me-2 material-icons">
                                    receipt_long
                                </span>
                                <span class="align-middle"> Billing </span>
                            </a>
                        </li>
                    <?php endif; ?>


                    <?php if (in_array("manage_help_Desk", session()->get('perms')) or in_array("all_super_admin_perms", session()->get('perms'))) :  ?>

                        <li>
                            <a class="nav-link <?= ($uri->getTotalSegments() >= 0 && ($uri->getSegment(1) == 'Tickets')) ? 'active' : 'link-dark' ?>" href="<?= base_url('/tickets'); ?>">
                                <span class="align-middle me-2 material-icons">
                                    support_agent
                                </span>
                                <span class="align-middle"> Help Desk </span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if (in_array("manage_emails", session()->get('perms')) or in_array("all_super_admin_perms", session()->get('perms'))) :  ?>

                        <li>
                            <a class="nav-link <?= ($uri->getTotalSegments() >= 1 && $uri->getSegment(1) == 'emails') ? 'active' : 'link-dark'; ?>" href="<?= base_url('/emails'); ?>">
                                <span class="align-middle me-2 material-icons">
                                    email
                                </span>
                                <span class="align-middle"> Emails </span>
                            </a>
                        </li>

                    <?php endif; ?>

                    <?php if (in_array("manage_feedbacks", session()->get('perms')) or in_array("all_super_admin_perms", session()->get('perms'))) :  ?>

                        <li>
                            <a class="nav-link <?= ($uri->getTotalSegments() >= 1 && $uri->getSegment(1) == 'feedbacks') ? 'active' : 'link-dark'; ?>" href="<?= base_url('/feedbacks'); ?>">
                                <span class="align-middle me-2 material-icons">
                                    reviews
                                </span>
                                <span class="align-middle"> Feedbacks </span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if (in_array("manage_notices", session()->get('perms')) or in_array("all_super_admin_perms", session()->get('perms'))) :  ?>
                        <li>
                            <a class="nav-link <?= ($uri->getTotalSegments() >= 1 && $uri->getSegment(1) == 'notices') ? 'active' : 'link-dark'; ?>" href="<?= base_url('/notices'); ?>">
                                <span class="align-middle me-2 material-icons">
                                    notifications_none
                                </span>
                                <span class="align-middle"> Notices </span>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if (in_array("manage_routes", session()->get('perms')) or in_array("all_super_admin_perms", session()->get('perms'))) :  ?>
                        <li>
                            <a class="nav-link <?= ($uri->getTotalSegments() >= 1 && $uri->getSegment(1) == 'linkRoutes') ? 'active' : 'link-dark'; ?>" href="<?= base_url('/linkRoutes'); ?>">
                                <span class="align-middle me-2 material-icons">
                                    call_missed_outgoing
                                </span>
                                <span class="align-middle"> Routes </span>
                            </a>
                        </li>
                    <?php endif; ?>
                <?php
                endif;
                ?>

            </ul>
            <hr>


            <div class="dropdown">
                <a href="#" class="d-flex align-items-center link-dark text-decoration-none dropdown-toggle" id="dropdownUser2" data-bs-toggle="dropdown" aria-expanded="false">
                    <?php
                    $profile_image['profile_picture_url'] = session()->get('profile_img_url');
                    $profile_image['size'] = "32px";
                    echo view('pages/profile/profile_image_circle.php', $profile_image);
                    ?>
                    <strong><?= $_SESSION['username']; ?></strong>
                </a>
                <ul class="dropdown-menu text-small shadow" aria-labelledby="dropdownUser2">
                    <?php
                    if ($userType != "super_admin") :
                    ?>
                        <li><a class="dropdown-item" href="<?php echo base_url('profile'); ?>"> Profile</a></li>

                        <?php if (in_array("all_perms", session()->get('perms'))) :  ?>
                            <li><a class="dropdown-item" href="<?php echo base_url('settings'); ?>">Settings</a></li>
                            <li><a class="dropdown-item" href="<?php echo base_url('/subscriptions/overview/' . session()->get('instituteID')); ?>">Subscriptions</a></li>
                            <li><a class="dropdown-item" href="<?php echo base_url('/invoices/institute_invoices/' . session()->get('instituteID')); ?>">Invoices</a></li>
                        <?php endif; ?>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                    <?php endif; ?>
                    <li><a class="dropdown-item" href="<?php echo base_url('login/user_logout'); ?>"> Sign out</a></li>
                </ul>
            </div>
        </div>

    </div>
</div>