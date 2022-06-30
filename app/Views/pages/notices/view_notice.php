<!-- Include Header -->
<?php include_once(APPPATH . "Views/header.php"); ?>

<!-- Custom CSS -->
<link href="<?php echo base_url('assets/css/notices/overview.css?v=20210917'); ?>" rel="stylesheet">


<div id="content">
    <div class="container-fluid mt-4">

        <div class="flex-container-column">
            <div>
                <label class="h5 text-gray-800 text-uppercase"> <?= $title; ?> </h5>
            </div>
            <div class="breadcrumb_div" aria-label="breadcrumb">
                <ol class="breadcrumb_custom">
                    <li class="breadcrumb_item"><a href="<?php echo base_url('/home'); ?>"> Home </a></li>
                    <li class="breadcrumb_item"><a href="<?php echo base_url('/notices'); ?>"> Notices </a></li>
                    <li class="breadcrumb_item active" aria-current="page"> <?= $title; ?> </li>
                </ol>
            </div>
        </div>

        <div class="card shadow p-4">

            <div>
                <span>Title:</span>
                <?= $notice_data['title']; ?>
            </div>


            <div>
                Description: <?= $notice_data['description']; ?>
            </div>

            <div>
                Start Date: <?= $notice_data['start_date']; ?><br />
                End Date: <?= $notice_data['end_date']; ?>
            </div>


            <div>
                Attached File:
            </div>

        </div>
    </div>
</div>
<!-- Include Footer -->
<?php include_once(APPPATH . "Views/footer.php"); ?>