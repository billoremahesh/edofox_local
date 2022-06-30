<!-- Include Header -->
<?php include_once(APPPATH . "Views/header.php"); ?>

<!-- Custom CSS -->
<link href="<?php echo base_url('assets/css/admin_dashboard.css?v=20220406'); ?>" rel="stylesheet">


<?php
/**
 * Fetching expiry data for battery type display
 */
//Ref: https://stackoverflow.com/questions/2040560/finding-the-number-of-days-between-two-dates
//To calculate numbers of days to expire
$expiry_date = session()->get('expiry_date');
$expiry_days = strtotime($expiry_date) - time();
$expiry_days = round($expiry_days / (60 * 60 * 24));

//To show conditional color to progress bar
if ($expiry_days > 10) {
    $expiry_progress_bar_class = "bg-info";
} else {
    $expiry_progress_bar_class = "bg-danger";
}
?>



<?php

if ($institute_details) {
    $maxStudents = 0;
    $storageQuota = 0;
    if (isset($institute_details['max_students'])) {
        $maxStudents = $institute_details['max_students'];
    } else {
        $maxStudents = -1;
    }

    if (isset($institute_details['storage_quota'])) {
        $storageQuota = $institute_details['storage_quota'];
    }
    $registered_students_percent = round($registeredCount * 100 / $maxStudents);
    //To show conditional color to progress bar
    if ($registered_students_percent < 90) {
        $stu_progress_bar_class = "bg-success";
    } else {
        $stu_progress_bar_class = "bg-danger";
    }
}

?>

<div id="content">
    <div class="container-fluid mt-4">

        <div class="row">

            <div class="col-12 text-center">
                <h1 id="dashboard_heading">Admin Dashboard</h1>
            </div>

            <div class="col-12 text-center">

                <!-- Pending Invoice Alert -->
                <?php if ($total_pending_invoices > 0) { ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert" style="max-width: 800px;margin:auto;">
                        You have <?= $total_pending_invoices; ?> payments due. Please complete the payments soon in order to avoid account suspension. <br />
                        <a href="<?= base_url('/invoices/institute_invoices/' . session()->get('instituteID')); ?>" class="alert-link"> You can click here to make the payment online </a>.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php } ?>
            </div>


            <div class="col-xs-12 my-3">
                <?php if ($expiry_days < 30 && $expiry_days > 0) : ?>
                    <div class="more-info-card card p-3 rounded shadow" style="max-width:400px;display:block;margin:auto;">
                        <div class="progress" style="height: 3px;">
                            <div class="progress-bar <?= $expiry_progress_bar_class ?>" role="progressbar" aria-valuenow="<?= $expiry_days ?>" aria-valuemin="0" aria-valuemax="30" style="width: <?= $expiry_days * 100 / 30 ?>%; ">
                                <span class="sr-only"><?= $expiry_days ?>%</span>
                            </div>
                        </div>
                        <div class="text-center">Your account will expire in <b><?= $expiry_days ?></b> days</div>
                    </div>
                    <?php else: ?>
                        <div class="more-info-card card p-3 rounded shadow" style="max-width:400px;display:block;margin:auto;">
                        <div class="progress" style="height: 3px;">
                            <div class="progress-bar <?= $expiry_progress_bar_class ?>" role="progressbar" aria-valuenow="<?= $expiry_days ?>" aria-valuemin="0" aria-valuemax="30" style="width: 100%; ">
                                <span class="sr-only"><?= $expiry_days ?>%</span>
                            </div>
                        </div>
                        <div class="text-center">Your account expired <b><?= abs($expiry_days) ?></b> days ago</div>
                    </div>
                <?php endif; ?>

            </div>
        </div>

        <div class="row">

            <div class="col-lg-4 mb-4 d-flex flex-column">
                <div class="chart chart-sm shadow bg-white rounded w-100" id="weekly_tests_chart_div">

                </div>

                <div class="chart chart-sm shadow bg-white rounded w-100 my-2" id="weekly_student_logins_chart_div">

                </div>
            </div>

            <div class="col-lg-8">
                <div class="w-100">
                    <div class="row">
                        <div class="col-sm-4 mb-4">
                            <a class="card_box_link" href="<?= base_url('tests'); ?>">
                                <div class="p-3 bg-white rounded shadow d-flex align-items-center h-100">
                                    <div>
                                        <img src="<?= base_url('assets/img/icons/calendar.png'); ?>" style="width: 48px;" />
                                    </div>

                                    <div style="margin-left: 16px;">
                                        <label class="counts-subtitle">Tests Today</label>
                                        <h4 class="count-number" id="total_videos_count_text"><?= indian_number_format($todays_test_cnt); ?></h4>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <div class="col-sm-4 mb-4">
                            <a class="card_box_link" href="<?= base_url('tests'); ?>">
                                <div class="p-3 bg-white rounded shadow d-flex align-items-center h-100">
                                    <div>
                                        <img src="https://edofox-s3.s3.ap-south-1.amazonaws.com/subjectIcons/subject-exam.png" style="width: 48px;" />
                                    </div>

                                    <div style="margin-left: 16px;">
                                        <label class="counts-subtitle">Total Tests</label>
                                        <h4 class="count-number" id="total_videos_count_text"><?= indian_number_format($total_test_cnt); ?></h4>
                                    </div>
                                </div>
                            </a>
                        </div>


                        <div class="col-sm-4 mb-4">
                            <a class="card_box_link" href="<?= base_url('students'); ?>">
                                <div class="p-3 bg-white rounded shadow d-flex align-items-center h-100">
                                    <div>
                                        <img src="<?= base_url('assets/img/icons/teamwork.png'); ?>" style="width: 48px;" />
                                    </div>

                                    <div style="margin-left: 16px;">
                                        <label class="counts-subtitle">Total Active Students</label>
                                        <h4 class="count-number" id="total_videos_count_text"><?= indian_number_format($student_cnt); ?></h4>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-sm-4 mb-4">
                            <a class="card_box_link" href="<?= base_url('classrooms'); ?>">
                                <div class="p-3 bg-white rounded shadow d-flex align-items-center h-100">
                                    <div>
                                        <img src="https://edofox-s3.s3.ap-south-1.amazonaws.com/public/statics/blackboard.png" style="width: 48px;" />
                                    </div>

                                    <div style="margin-left: 16px;">
                                        <label class="counts-subtitle">Classrooms</label>
                                        <h4 class="count-number" id="total_videos_count_text"><?= indian_number_format($total_classrooms); ?></h4>
                                    </div>
                                </div>
                            </a>
                        </div>


                        <div class="col-sm-4 mb-4">
                            <?php
                            if (session()->get('dlp_count') == 1) {

                                $video_icon_link = "https://edofox-s3.s3.ap-south-1.amazonaws.com/public/statics/video_icon.png";
                            ?>
                                <a class="card_box_link" href="<?= base_url('/dlp'); ?>">


                                <?php
                            } else {
                                $video_icon_link = base_url('assets/img/statics/video.png');
                                ?>
                                    <a class="card_box_link card_box_disabled_link" href="<?= base_url('/home/feature_blocked'); ?>">

                                    <?php
                                }
                                    ?>
                                    <div class="p-3 bg-white rounded shadow d-flex align-items-center h-100">
                                        <div>
                                            <img src="<?= $video_icon_link; ?>" style="width: 48px;" />
                                        </div>

                                        <div style="margin-left: 16px;">
                                            <label class="counts-subtitle">Total Videos</label>
                                            <h4 class="count-number" id="total_videos_count_text"><?= indian_number_format($dlp_video_count); ?></h4>
                                        </div>
                                    </div>
                                    </a>
                        </div>


                        <div class="col-sm-4 mb-4">
                            <?php
                            if (session()->get('dlp_count') == 1) {

                                $pdf_icon_link = "https://edofox-s3.s3.ap-south-1.amazonaws.com/public/statics/pdf_icon.png";
                            ?>

                                <a class="card_box_link" href="<?= base_url('dlp'); ?>">
                                <?php
                            } else {
                                $pdf_icon_link = base_url('assets/img/statics/pdf.png');
                                ?>
                                    <a class="card_box_link card_box_disabled_link" href="<?= base_url('/home/feature_blocked'); ?>">

                                    <?php
                                }
                                    ?>

                                    <div class="p-3 bg-white rounded shadow d-flex align-items-center h-100">
                                        <div>
                                            <img src="<?= $pdf_icon_link; ?>" style="width: 48px;" />
                                        </div>

                                        <div style="margin-left: 16px;">
                                            <label class="counts-subtitle">Total Documents</label>
                                            <h4 class="count-number" id="total_videos_count_text"><?= indian_number_format($dlp_doc_count); ?></h4>
                                        </div>
                                    </div>
                                    </a>
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-sm-4 mb-4">
                            <div class="p-3 bg-white rounded shadow d-flex align-items-center h-100">
                                <div>
                                    <img src="<?= base_url('assets/img/icons/calendar_expiry.png'); ?>" style="width: 48px;" />
                                </div>

                                <div style="margin-left: 16px;">
                                    <label class="counts-subtitle">Account Expiry Date</label>
                                    <h4 class="count-number" id="total_videos_count_text"><?= changeDateTimezone(date("d M Y", strtotime($expiry_date)), "d M Y"); ?></h4>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-4 mb-4">
                            <div class="p-3 bg-white rounded shadow d-flex align-items-center h-100">
                                <div>
                                    <img src="<?= base_url('assets/img/icons/queue.png'); ?>" style="width: 48px;" />
                                </div>

                                <div class="flex-grow-1" style="margin-left: 16px;">
                                    <label class="counts-subtitle">Students' quota</label>
                                    <h4 class="count-number" id="total_videos_count_text">
                                        <?php
                                        if ($maxStudents < 0) {
                                            echo "NA";
                                        } else if ($registeredCount >= 0) {
                                            echo "<span>$registeredCount/$maxStudents</span>";
                                        } else {
                                            echo "<span style='color:red'>Exceeded</span>";
                                        }
                                        ?>
                                    </h4>
                                    <div class="progress" style="height: 2px;">
                                        <div class="progress-bar <?= $stu_progress_bar_class ?>" role="progressbar" aria-valuenow="<?= $registered_students_percent ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?= $registered_students_percent ?>%">
                                            <span class="sr-only"><?= $registered_students_percent ?>%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-4 mb-4">
                            <div class="p-3 bg-white rounded shadow d-flex align-items-center h-100">
                                <div>
                                    <img src="<?= base_url('assets/img/icons/pendrive.png'); ?>" style="width: 48px;" />
                                </div>

                                <div style="margin-left: 16px;">
                                    <label class="counts-subtitle">Storage Quota </label>
                                    <h4 class="count-number" id="total_videos_count_text"><?= $storageQuota ?> GB</h4>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>



        <div class="row">
            <div class="col-lg-12">

                <div class="card rounded shadow">
                    <div class="card-body">
                        <div class="row">
                            <div class="col mt-0">
                                <h5 class="card-title">Useful Links</h5>
                            </div>
                        </div>

                        <div class="d-flex mb-0">
                            <div>
                                <!-- WhatsApp credits -->
                                <?php
                                if (empty(session()->get('whatsapp_credits'))) {
                                ?>
                                    <p class="text-success fw-bold">Claim your FREE 500 WhatsApp credits today and try our Whatsapp Integration. Contact your <b> <a href="<?= base_url('/support/account_manager'); ?>" target="_blank">Account Manager</a> </b> to know more!
                                        <button class="btn btn-sm btn-success whatsapp_credits_btn" onclick="claim_whatsapp_credits();"><i class='fab fa-whatsapp'></i> Claim WhatsApp Credits </button>
                                    </p>
                                <?php
                                } else {
                                    if (session()->get('whatsapp_credits') > 0) {
                                        echo "<p class='text-success fw-bold'><b> <i class='fab fa-whatsapp'></i> WhatsApp Credits: " . session()->get('whatsapp_credits') . "</b></p>";
                                    } else {
                                        echo "<p class='text-danger fw-bold'><b> <i class='fab fa-whatsapp'></i> Your WhatsApp Credits have been expired, please recharge </b></p>";
                                    }
                                }
                                ?>

                                <!-- https://www.freecodecamp.org/news/how-to-use-html-to-open-link-in-new-tab/ -->
                                <p>Link where Admin/Teachers can login: <b><a href="<?= base_url('/home'); ?>" target="_blank" rel="noopener noreferrer"><?= base_url('/home'); ?></a></b></p>
                                <p>Link where Student can login:
                                    <b><a href="<?= HTTPHOST; ?>" target="_blank" rel="noopener noreferrer">
                                            <?= HTTPHOST; ?></a></b>
                                    <b> OR </b>
                                    <a href="<?= base_url('/home/qr_code'); ?>" target="_blank">
                                        <span class="material-icons">
                                            qr_code
                                        </span>
                                    </a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>




        <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasChapterList" aria-labelledby="offcanvasChapterListLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title m-2" id="offcanvasChapterListLabel">Shortcut keys</h5>
                <button type="button" class="btn text-white" data-bs-dismiss="offcanvas" aria-label="Close">
                    <span class="material-icons text-white-50">close</span>
                </button>
            </div>

            <div class="offcanvas-body p-2">
                <div class="list-group list-group-flush mb-4">
                    <p><b>A Shortcut key is a key or a combination of keys on a computer keyboard that, when pressed at one time, performs a task (such as starting an application) more quickly than by using a mouse or other input device.</b></p>
                    <div class="list-group-item list-group-item-action">
                        Add New Test - ctrl+alt+t
                    </div>
                    <div class="list-group-item list-group-item-action">
                        Add New Student - ctrl+alt+s
                    </div>
                    <div class="list-group-item list-group-item-action">
                        Add New Classroom - ctrl+alt+c
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>



<!-- The Modal FOR Notice-->
<div class="modal" id="notice_modal">
    <div class="modal-dialog">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <span class="modal-title"><b> We need your feedback! </b></span>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Modal body -->
            <div class="modal-body">

                <div style="color: black;">
                    <p>Hello <b><?= $_SESSION['username']; ?></b>.</p>

                    <p>We are collecting feedback based on your experience! Please click the below link now to give feedback.</p>

                    <p>If you want to give feedback later, please find the "Give Feedback" menu in our sidebar to the left.</p>

                    <p>Thanks.</p>
                </div>
                <div style="text-align: center;">
                    <a href="<?= base_url('/feedbacks/add') ?>" class="btn btn-success" target="_blank"> Give Feedback </a>
                </div>
            </div>

            <!-- Modal footer -->
            <div class="modal-footer">
                <button type="button" onclick="skip_notice_modal()" class="btn btn-light" data-bs-dismiss="modal">Skip</button>
            </div>

        </div>
    </div>
</div>

<!-- Include Footer -->
<?php include_once(APPPATH . "Views/footer.php"); ?>
<script src="<?php echo base_url('assets/js/admin_dashboard.js?v=20220421'); ?>"></script>



<script>
    // Show Notice
    $(function() {
        show_notice();
    });

    function show_notice() {
        var currentDate = new Date();
        var notice_start_date = new Date('03/23/2022');
        var notice_end_date = new Date('04/28/2022');
        if (localStorage.getItem('notice_modal_show') !== null) {
            var notice_modal_check = window.localStorage.getItem('notice_modal_show');
        } else {
            var notice_modal_check = 0;
        }
        if (currentDate > notice_start_date && currentDate < notice_end_date && notice_modal_check != 1) {
            // Show Modal
            $('#notice_modal').modal('show');
        }
    }

    function skip_notice_modal() {
        window.localStorage.setItem("notice_modal_show", "1");
    }
</script>

<script>
    function claim_whatsapp_credits() {
        var institute_id = "<?= decrypt_cipher(session()->get('instituteID')); ?>";
        var institute_name = "<?= session()->get('instituteName'); ?>";
        $(".whatsapp_credits_btn").attr("disabled", true);
        $.ajax({
            url: base_url + "/institutes/claim_whatsapp_credits",
            type: "POST",
            data: {
                institute_id: institute_id,
                institute_name: institute_name,
                whatsapp_credits: 500
            },
            success: function(result) {
                Snackbar.show({
                    pos: 'top-center',
                    text: 'Successfully claimed WhatsApp Credits'
                });
                window.location.reload();
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                $(".whatsapp_credits_btn").attr("disabled", false);
            }
        });
    }
</script>