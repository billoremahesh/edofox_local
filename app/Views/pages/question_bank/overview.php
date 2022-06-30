<!-- Include Header -->
<?php include_once(APPPATH . "Views/header.php"); ?>

<!-- Custom CSS -->
<link href="<?php echo base_url('assets/css/question_bank/overview.css?v=20211028'); ?>" rel="stylesheet">

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


        <div class="p-4">
            <div class="row">
                <div class="col-3">
                    <?php if (!empty($subjects_list)) : ?>
                        <div class="list-group mb-4">
                            <?php foreach ($subjects_list as $subject_list) :
                                $encrypted_subject_id = encrypt_string($subject_list['subject_id']);
                            ?>
                                <button type="button" class="list-group-item list-group-item-action" onclick="load_chapters('<?= $subject_list['subject_id']; ?>','<?= $encrypted_subject_id; ?>')"><?= $subject_list['subject']; ?></button>
                            <?php endforeach; ?>
                        </div>
                    <?php else :
                        echo "<p class='text-center'>No subjects mapped, please add subject first.</p>";
                    endif;
                    ?>
                </div>
                <div class="col-9">
                    <!-- Loading Div -->
                    <div class="text-center my-2 d-none" id="loading-div">
                        <i class='fas fa-atom fa-spin fa-2x fa-fw'></i>
                    </div>
                    <div class="card p-4" id="chapters_div">
                        <p class="text-center"><b>Please select subject to display chapters list</b></p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Include Footer -->
<?php include_once(APPPATH . "Views/footer.php"); ?>
<script src="<?php echo base_url('assets/js/manage_questions.js'); ?>"></script>

<script>
    function load_chapters(subject_id, encrypted_subject_id) {
        loader(true);
        $("#chapters_div").addClass("d-none");
        var request = {
            subject_id: subject_id
        };
        $.ajax({
            url: base_url + "/subjects/load_subject_chapters",
            method: 'POST',
            dataType: 'json',
            contentType: 'application/json',
            data: JSON.stringify(request),
            success: function(result) {
                // console.log("Result ==>", result);
                $("#chapters_div").removeClass("d-none");
                $('#chapters_div').html(getFormattedChaptersList(result, encrypted_subject_id));
                $('[data-bs-toggle="tooltip"]').tooltip();
                loader(false);
            }
        });
    }
</script>

<script>
    function loader(show) {
        if (show) {
            $("#loading-div").removeClass("d-none");
        } else {
            $("#loading-div").addClass("d-none");
        }
    }
</script>