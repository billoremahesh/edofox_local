<!-- Include Header -->
<?php include_once(APPPATH . "Views/header.php"); ?>

<!-- Custom CSS -->
<link href="<?php echo base_url('assets/css/feedbacks/add.css?v=202111171921'); ?>" rel="stylesheet">
<div id="content">
    <div class="container-fluid">

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

        <div class="card shadow p-4" style="max-width: 400px;margin:auto;">
            <?php echo form_open('/feedbacks/add_feedback_submit'); ?>
            <div class="row g-3">
                <div>
                    <div class="rateyo" data-rateyo-rating="1" data-rateyo-num-stars="5"></div>
                    <span class='result'></span>
                </div>
                <input type="hidden" name="rating" id="rating_given" required />
                <div class="mb-3">
                    <div class="input-group">
                        <label class="input-group-text" for="inputGroupSelectModule">Modules</label>
                        <select class="form-select" name="module" id="inputGroupSelectModule">
                            <!-- <option >Choose...</option> -->
                            <option value="Test">Test</option>
                            <option value="DLP">DLP</option>
                            <option value="Classrooms">Classrooms</option>
                            <option value="Question Bank">Question Bank</option>
                            <option value="Reports">Reports</option>
                            <option value="Overall" selected>Overall</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="feedback_text" class="form-label">Please add your valuable feedback below:</label>
                    <textarea class="form-control" name="feedback_text" id="feedback_text" rows="5"></textarea>
                </div>
            </div>

            <input type="hidden" name="admin_id" value="<?= $admin_id; ?>" required />
            <input type="hidden" name="redirect" value="<?= $redirect; ?>" required />
            <button type="submit" class="btn btn-success">Submit</button>

            <?php echo form_close(); ?>
        </div>

    </div>

</div>


<!-- Include Footer -->
<?php include_once(APPPATH . "Views/footer.php"); ?>

<!-- RateYo Star Rating lib -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/rateYo/2.3.2/jquery.rateyo.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/rateYo/2.3.2/jquery.rateyo.min.css" rel="stylesheet" />

<script>
    $(function() {
        $(".rateyo").rateYo().on("rateyo.change", function(e, data) {
            var rating = data.rating;
            $(this).parent().find('.result').text('Rating : ' + rating);
            $("#rating_given").val(rating);
        });
    });
</script>