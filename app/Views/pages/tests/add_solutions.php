<!-- Include Header -->
<?php include_once(APPPATH . "Views/header.php"); ?>

<!-- Custom CSS -->
<link href="<?php echo base_url('assets/css/tests/add_solutions.css?v=20211122'); ?>" rel="stylesheet">

<div id="content">
    <div class="container-fluid mt-4">

        <div class="flex-container-column">
            <div>
                <label class="h5 text-gray-800 text-uppercase"> <?= $title; ?> </h5>
            </div>
            <div class="breadcrumb_div" aria-label="breadcrumb">
                <ol class="breadcrumb_custom">
                    <li class="breadcrumb_item"><a href="<?php echo base_url('/home'); ?>"> Home </a></li>
                    <li class="breadcrumb_item"><a href="<?php echo base_url('tests'); ?>"> Tests </a></li>
                    <li class="breadcrumb_item active" aria-current="page"> <?= $title; ?> </li>
                </ol>
            </div>
        </div>

        <div class="add_solutions_options_container">

            <ul class="list-group">

                <a href="<?= base_url('/tests/add_solutions_images/' . $test_id); ?>" class="list-group-item list-group-item-action my-2" aria-current="true">

                    <div class="ms-2 me-auto">
                        <div class="fw-bold">Add Solutions Images</div>
                        <ul>
                            <li>Add bulk solution images for each question of test</li>
                            <li>Visible below every question on <b>result page</b></li>
                        </ul>
                    </div>
                </a>





                <?php if (isset($solution_pdf_data['solutions_pdf_url'])) : ?>
                    <div class="text-center m-2 fw-bolder">
                        <a target="_blank" class="link-info" href="<?= $solution_pdf_data['solutions_pdf_url'] ?>">Previously Added Solutions PDF File <i class="fas fa-external-link-alt"></i> </a>
                    </div>
                <?php endif; ?>


                <button type="button" class="list-group-item list-group-item-action my-2" onclick="show_add_modal('modal_div','uploadSolutionsPdfModal','/tests/add_solution_pdf_modal/<?= $test_id; ?>');" aria-current="true">

                    <div class="ms-2 me-auto">
                        <div class="fw-bold">Add Solutions PDF

                            <?php if (!empty($solution_pdf_data) && $solution_pdf_data['solutions_pdf_url'] != '') : ?>
                                <span class="material-icons text-primary fs-5" data-bs-toggle="tooltip" title="Solution PDF added">verified</span>
                            <?php endif; ?>

                        </div>
                        <ul>
                            <li>Add single PDF with all questions' solutions</li>
                            <li>Visible as a single file on <b>result page</b></li>
                        </ul>
                    </div>


                </button>



                <button type="button" class="list-group-item list-group-item-action my-2" onclick="show_add_modal('modal_div','add_solution_video_modal','tests/add_solution_video_modal/<?= $test_id; ?>');" aria-current="true">

                    <div class="ms-2 me-auto">
                        <div class="fw-bold">Add Solution Video URL
                            <?php if (!empty($solution_video_data)) : ?>
                                <span class="material-icons text-primary fs-5" data-bs-toggle="tooltip" title="<?= count($solution_video_data); ?> Solution Videos added">verified</span>
                            <?php endif; ?>
                        </div>
                        <ul>
                            <li>Add solution <b>embed</b> video URL from YouTube/Vimeo</li>
                            <li>Visible as multiple videos on <b>result page</b></li>
                        </ul>
                    </div>

                </button>
            </ul>


            <?php if (!empty($solution_video_data)) : ?>
                <div class="card shadow p-2">
                    <div class="fw-bold text-muted">Solution Videos</div>
                    <table class="table table-borderless align-middle table-sm ">
                        <tbody>
                            <?php
                            foreach ($solution_video_data as $key => $row) :
                                $video_id = encrypt_string($row['id']);
                                $video_name = $row['video_name'];
                                $video_url = $row['video_url'];
                            ?>
                                <tr>
                                    <td><?= $key + 1 ?></td>
                                    <td><a href="<?= $video_url ?>" target="_blank"><?= $video_name ?></a></td>
                                    <td><button class="btn text-danger" onclick="show_add_modal('modal_div','delete_solution_video_modal','tests/delete_solution_video_modal/<?= $video_id; ?>/<?= $test_id; ?>');" data-bs-toggle="tooltip" title="Delete Video from solutions"><span class="material-icons fs-5">delete_outline</span></button></td>
                                </tr>
                            <?php endforeach; ?>

                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>


    </div>

</div>

<!-- Include Footer -->
<?php include_once(APPPATH . "Views/footer.php"); ?>