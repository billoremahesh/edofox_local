<!-- Include Header -->
<?php include_once(APPPATH . "Views/header.php"); ?>

<!-- Custom CSS -->
<link href="<?php echo base_url('assets/css/tests/add_questions_options.css?v=20220524'); ?>" rel="stylesheet">

<div id="content">
    <div class="container-fluid mt-4">

        <div class="flex-container-column">
            <div>
                <label class="h5 text-gray-800 text-uppercase"> <?= $test_details['test_name']; ?> </h5>
            </div>
            <div class="breadcrumb_div" aria-label="breadcrumb">
                <ol class="breadcrumb_custom">
                    <li class="breadcrumb_item"><a href="<?php echo base_url('/home'); ?>"> Home </a></li>
                    <li class="breadcrumb_item"><a href="<?php echo base_url('tests'); ?>"> Tests </a></li>
                    <li class="breadcrumb_item active" aria-current="page"> <?= $title; ?> </li>
                </ol>
            </div>
        </div>

        <div class="text-center">

            <div class="mb-4">
                <label class="text-muted">Well done! You added a new test.</label><br>
                <label class="text-muted">Now let's add questions in the test.</label><br>
                <label class="text-muted">Choose one option below for adding questions in the test: "<?= $test_details['test_name']; ?>"</label>
            </div>

            <div class="row">
                <?php
                $dyamic_columns = "col-4";
                if ($test_details['exam_conduction'] == "Offline") {
                    $dyamic_columns = "col-3";

                ?>
                    <div class="<?= $dyamic_columns; ?>">
                        <a class="add_questions_options_button" href="<?= base_url('tests/upload_exam_pdf_paper/' . $test_id); ?>">
                            <div class="d-flex flex-column justify-content-center align-content-center add_question_flex_card" style="position: relative;">
                                <div class="text-center">
                                    <img class="img-fluid" src="https://edofox-s3.s3.ap-south-1.amazonaws.com/public/statics/pdf_icon.png" style="width: 64px;" />
                                </div>
                                <div class="text-center">
                                    Upload PDF question paper
                                </div>
                                <div style="position: absolute;top:0;right:0;">
                                    <img class="img-fluid" src="<?= base_url('/assets/img/icons/new.png'); ?>" style="width: 45px;" />
                                </div>
                            </div>
                        </a>
                    </div>
                <?php
                }
                ?>
                <div class="<?= $dyamic_columns; ?>">
                    <a class="add_questions_options_button" href="<?= base_url('tests/add_test_img_questions/' . $test_id); ?>">
                        <div class="d-flex flex-column justify-content-center align-content-center add_question_flex_card">
                            <div>
                                <img class="img-fluid" src="<?= base_url('assets/img/icons/jpg.png'); ?>" style="width: 64px;" />
                            </div>
                            <div class="text-center">
                                Add Questions from Images <br />
                                <span class="text-muted">(JPEG, PNG)
                                </span>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="<?= $dyamic_columns; ?>">
                    <a class="add_questions_options_button" href="<?= base_url('tests/generate_chapter_wise_test/' . $test_id); ?>">
                        <div class="d-flex flex-column justify-content-center align-content-center add_question_flex_card">
                            <div>
                                <img class="img-fluid" src="<?= base_url('assets/img/icons/servers.png'); ?>" style="width: 64px;" />
                            </div>
                            <div class="text-center">
                                Add Questions from EDOFOX database <br />
                                <span class="text-muted">(auto-generate test)</span>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="<?= $dyamic_columns; ?>">
                    <a class="add_questions_options_button" href="<?= base_url('tests/parse_pdf/' . $test_id); ?>">
                        <div class="d-flex flex-column justify-content-center align-content-center add_question_flex_card">
                            <div class="text-center">
                                <img class="img-fluid" src="https://edofox-s3.s3.ap-south-1.amazonaws.com/public/statics/pdf_icon.png" style="width: 64px;" />
                            </div>
                            <div class="text-center">
                                Import PDF question paper
                            </div>
                        </div>
                    </a>
                </div>

            </div>

            <div class="my-4 text-center">
                <a class="skip_button" href="<?= base_url('tests'); ?>">Skip adding questions for now <i class="fas fa-chevron-right"></i><i class="fas fa-chevron-right"></i></a>
            </div>
        </div>

    </div>

</div>
</div>

<!-- Include Footer -->
<?php include_once(APPPATH . "Views/footer.php"); ?>