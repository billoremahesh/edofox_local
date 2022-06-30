<?php
$userType = session()->get('user_type');
?>
<!-- Topbar -->
<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #ed4c05;color:white;">
    <div class="container-fluid">
        <div class="d-flex align-items-center">
            <a data-bs-toggle="offcanvas" href="#edofox-offcanvas" role="button" aria-controls="edofox-offcanvas" style="padding: 0px 8px;">
                <span class="material-icons text-white-50" style="margin-top: 6px;" data-bs-toggle="tooltip" title="Open Navigation Bar">keyboard_double_arrow_right</span>
            </a>


            <a href="<?= base_url('/home'); ?>" class="navbar-brand text-white">
                <?= $_SESSION['instituteName']; ?>
            </a>

        </div>


        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="material-icons text-white-50">
                menu
            </span>
        </button>

        <div class="collapse navbar-collapse justify-content-end" id="navbarSupportedContent">
            <?php
            if ($userType != "super_admin") :
            ?>
                <!-- Top Search Box -->
                <div class="top-search-box">
                    <!-- Search Box -->
                    <div class="search-box-input-group">
                        <input class="search_input awesomplete" type="search" data-list="#list_route_links" id="search_route_links" placeholder="Search..." aria-label="Search" autocomplete="off" spellcheck="false" role="combobox" aria-expanded="false">
                    </div>
                    <span class="clear_search_routes" style="display: none;"></span>
                    <!-- Search Results List -->
                    <ul class="suggestion-search-menu" id="list_route_links" aria-labelledby="search_route_links">
                        <li></li>
                    </ul>

                </div>
            <?php
            endif;
            ?>
            <ul class="navbar-nav">

                <?php
                if ($userType != "super_admin") :
                ?>

                    <li class="nav-item">
                        <a class="nav-link position-relative" data-bs-toggle="offcanvas" data-bs-target="#offcanvasActivityList" aria-controls="offcanvasActivityList" aria-current="page">
                            <span class="material-icons text-white-50 d-none d-sm-none d-md-none d-lg-inline">
                                notifications_none
                            </span>
                            <span class="d-inline d-sm-inline d-md-inline d-lg-none">Activity Logs</span>
                            <?php
                            if ($_SESSION['unread_activity_count'] != 0) :
                            ?>
                                <span class="d-none d-sm-none d-md-none d-lg-inline position-absolute translate-middle badge rounded-pill bg-danger" style="left: 70%; top: 20%;">
                                    <?= $_SESSION['unread_activity_count']; ?>
                                </span>
                            <?php
                            endif;
                            ?>
                        </a>
                    </li>



                    <li class="nav-item dropdown">
                        <a class="nav-link" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="material-icons text-white-50 d-none d-sm-none d-md-none d-lg-inline">
                                help_outline
                            </span>
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown" style="margin-right: 150px;">
                            <!-- Old Version Link -->
                            <li>
                                <a class="dropdown-item" href="https://<?= $_SERVER['HTTP_HOST']; ?>/test-adminPanel/sql_operations/login_validate.php?universal_token=<?= decrypt_cipher(session()->get('admin_token')); ?>">
                                    Switch to edofox old version
                                </a>
                            </li>
                            <!-- Feedback Form -->
                            <li>
                                <a class="dropdown-item" href="<?= base_url('/feedbacks/add') ?>" class="nav-link y-2"> Give Feedback </a>
                            </li>

                            <?php
                            if (session()->get('support_feature') == 1) {
                            ?>
                                <li>
                                    <a class="dropdown-item" href="<?= base_url('/support/account_manager') ?>" class="nav-link y-2"> Account Manager </a>
                                </li>
                            <?php
                            }
                            ?>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <!-- Release Updates -->
                            <li>
                                <a class="dropdown-item" href="<?= base_url('/home/release_updates') ?>" class="nav-link y-2"> What's new </a>
                            </li>

                            <li class="d-none">
                                <div class="dropdown-item">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" onchange="toggleDarkMode();" id="darkModeToggle">
                                        <label class="form-check-label" for="darkModeToggle">Dark Mode</label>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>

                <li class="nav-item">
                    <a class="nav-link" href="<?php echo base_url('login/user_logout'); ?>">
                        <span class="material-icons text-white-50 d-none d-sm-none d-md-none d-lg-inline">
                            logout
                        </span>
                        <span class="d-inline d-sm-inline d-md-inline d-lg-none">Logout</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasActivityList" aria-labelledby="offcanvasActivityListLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title m-2" id="offcanvasActivityListLabel">Activity Logs</h5>
        <button type="button" class="btn text-white" data-bs-dismiss="offcanvas" aria-label="Close">
            <span class="material-icons text-white-50">close</span>
        </button>
    </div>

    <div class="offcanvas-body p-0">
        <?php
        $user_activity_data = unread_activity_logs(decrypt_cipher(session()->get('login_id')), session()->get('last_login'));
        if (!empty($user_activity_data)) :
        ?>
            <div class="list-group list-group-flush mb-4">

                <?php
                foreach ($user_activity_data as $user_activity) :
                ?>
                    <a href="#" class="list-group-item list-group-item-action"><?= $user_activity['activity_log']; ?></a>
                <?php endforeach; ?>

                <a class="list-group-item list-group-item-action" href="<?= base_url('/activityLogs'); ?>"> <b>See all activities -></b></a>

            </div>
        <?php else : ?>
            <h4 class="text-center">No activity found.</h4>
        <?php endif; ?>

    </div>
</div>