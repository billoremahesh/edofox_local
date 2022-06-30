<!-- Include Header -->
<?php include_once(APPPATH . "Views/header.php"); ?>

<!-- Custom CSS -->
<link href="<?php echo base_url('assets/css/feedbacks/overview.css?v=202111171921'); ?>" rel="stylesheet">
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

        <div style="max-width: 800px;margin:auto;">
            <table id="studentListTable" class="table table-borderless" style="width: 100%;">
                <thead class="d-none">
                    <tr>
                        <th>#</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>


    </div>

</div>


<!-- Include Footer -->
<?php include_once(APPPATH . "Views/footer.php"); ?>



<script>
    var userdatatable;
    $(document).ready(function() {
        userdatatable = $('#studentListTable').DataTable({
            "processing": true,
            "stateSave": true,
            "serverSide": true,
            "pageLength": 10,
            "order": [0, "asc"],
            "ajax": {
                url: base_url + "/feedbacks/load_feedbacks",
                type: "post",
                "data": function(d) {}
            },
            "stripeClasses": [],
            language: {
                search: "",
                processing: '<i class="fas fa-atom fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>'
            },
            "dataSrc": "Data"
        });
    });
</script>