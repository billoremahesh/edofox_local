<!-- Include Header -->
<?php include_once(APPPATH . "Views/header.php"); ?>

<!-- Custom CSS -->
<link href="<?php echo base_url('assets/css/tests/student_proctor_activity.css?v=20210915'); ?>" rel="stylesheet">
<?php

$proctorScore = "NA";
if (!empty($student_proctor_avg_score)) {
    $proctorScore = $student_proctor_avg_score['score'];
}

?>
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
                    <li class="breadcrumb_item"><a href="<?php echo base_url('tests/proctoring_analysis/' . $test_id); ?>"> Proctoring Analysis </a></li>
                    <li class="breadcrumb_item active" aria-current="page"> <?= $title; ?> </li>
                </ol>
            </div>
        </div>

        <div class="p-4">
            <div class="text-center" style="margin-bottom: 16px;">
                <div style="display: inline-block; margin:auto; background-color: white; padding: 16px; border-radius: 6px; box-shadow: 0px 2px 10px #ddd;">
                    <h3 class="text-center"> <span class="label label-success">Score : <?= number_format((float)$proctorScore, 2, '.', ''); ?></span> </h3>

                    <img class="img-responsive rounded-centered-image" style="max-height: 200px; max-width: 300px;" src="<?= $student_details['proctor_img']; ?>">
                    <p class="text-muted small"> Student reference image </p>
                </div>
            </div>
            <div class="row">

                <?php

                $sr_no = 1;

                foreach ($proctor_images as $row) :
                    $score = $row['score'];
                    $date = changeDateTimezone($row['created_date']);
                    $remarks = $row['remarks'];
                    $imgUrl = $row['img_path'];
                    $remarksText = "";
                    if (isset($remarks) && $remarks != '') {
                        $remarksText = "<span class='label label-danger'>" . $remarks . "</span>";
                    }

                    $score_label_class = "label-danger";
                    if ($score > 50) {
                        $score_label_class = "label-warning";
                    }
                    if ($score > 75) {
                        $score_label_class = "label-default";
                    }
                    if ($score > 90) {
                        $score_label_class = "label-success";
                    }
                ?>
                    <div class="col-sm-3 text-center">
                        <div style="display: inline-block; margin: 8px auto; background-color: white; border-radius: 6px; border: 2px solid #eee;">
                            <img src="<?= $imgUrl ?>" class="rounded mx-auto d-block lazy" style="max-height: 300px;" />
                            <span class="label <?= $score_label_class; ?>">Score: <?= $score ?> </span>
                            <div> <?= $remarksText ?> </div>
                            <p class="small text-info"> <?= changeDateTimezone(date("d M Y, h:i A", strtotime($date)),"d M Y, h:i A"); ?></p>
                        </div>
                    </div>

                    <?php if ($sr_no % 4 == 0) : ?>
                        <div class="clearfix visible-sm-block visible-md-block visible-lg-block"></div>
                    <?php endif; ?>

                <?php $sr_no++;
                endforeach;

                ?>

            </div>
        </div>

    </div>
</div>

<!-- Include Footer -->
<?php include_once(APPPATH . "Views/footer.php"); ?>


<script type="text/javascript" src="<?= base_url('assets/js/lazy.js'); ?>"></script>

<script type="text/javascript">
    function lazyLoad() {
        $('.lazy').Lazy({
            beforeLoad: function(element) {
                // called before an elements gets handled
                //console.log("Before loading");
            },
            afterLoad: function(element) {
                // called after an element was successfully handled
                //console.log("Image loaded properly");

            },
            onError: function(element) {
                // called whenever an element could not be handled
                console.log("ERROR in loading image");

            },
            onFinishedAll: function() {
                // called once all elements was handled
                //console.log("Images all loaded properly");
            }
        });
    }
</script>


<script>
    var table;
    $(document).ready(function() {
        table = $('#datatable').DataTable({
            dom: 'Blfrtip',
            buttons: [
                'excel'
            ]
        });

        $.fn.dataTable.ext.search.push(
            function(settings, data, dataIndex) {
                var activityType = $("#activity_type_dropdown").val();
                if (activityType == '') {
                    return true;
                }

                var value = data[1];
                if (activityType == 'Question') {
                    if (value.indexOf('Q.') >= 0) {
                        return true;
                    }
                    return false;
                } else if (activityType == 'Movement') {
                    if (value.indexOf('Q.') >= 0) {
                        return false;
                    }
                    return true;
                }
                return true;
            }
        );

        lazyLoad();

    });

    function filterData() {
        table.draw();
    }
</script>