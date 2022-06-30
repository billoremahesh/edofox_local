<!-- Include Header -->
<?php include_once(APPPATH . "Views/header.php"); ?>

<!-- Custom CSS -->
<link href="<?php echo base_url('assets/css/dlp/manage_dlp.css?v=20210915'); ?>" rel="stylesheet">

<div id="content">
    <div class="container-fluid mt-4">

        <div class="flex-container-column">
            <div>
                <label class="h5 text-gray-800 text-uppercase"> <?= $title; ?> </h5>
            </div>
            <div class="breadcrumb_div" aria-label="breadcrumb">
                <ol class="breadcrumb_custom">
                    <li class="breadcrumb_item"><a href="<?php echo base_url('/home'); ?>"> Home </a></li>
                    <li class="breadcrumb_item"><a href="<?php echo base_url('dlp'); ?>"> DLP </a></li>
                    <li class="breadcrumb_item active" aria-current="page"> <?= $title; ?> </li>
                </ol>
            </div>
        </div>

        <div class="card shadow p-4">
            <div class="table_custom">
                <table class="table table-bordered" id="deleted_content_table">
                    <thead>
                        <tr>
                            <th> # </th>
                            <th> Content Name </th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>

    </div>

</div>

<!-- Include Footer -->
<?php include_once(APPPATH . "Views/footer.php"); ?>



<script>
    $(document).ready(function() {
        var institute_id = "<?= $instituteID ?>";

        var deleted_content_table = $('#deleted_content_table').DataTable({
            'processing': true,
            'serverSide': true,
            "stateSave": true,
            "pageLength": 25,
            "order": [
                [0, "asc"]
            ],
            'ajax': {
                'url': base_url + '/dlp/load_deleted_dlp_content',
                "data": {
                    "institute_id": institute_id
                }
            },
            'serverMethod': 'post',
            "stripeClasses": [],
            'columns': [{
                data: 'sr_no'
            }, {
                data: 'video_name'
            }],
            language: {
                search: ""
            }
        });


    });
</script>