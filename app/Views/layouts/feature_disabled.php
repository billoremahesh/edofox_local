<!-- Include Header -->
<?php include_once(APPPATH . "Views/header.php"); ?>

<!-- Custom CSS -->
<link href="<?php echo base_url('assets/css/dlp/manage_dlp.css?v=20211030'); ?>" rel="stylesheet">

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


        <div class="text-center fs-4 fw-bold" style="max-width: 900px;margin:auto;">
            <h5>
                Access to this feature is currently blocked. Please make sure this feature is part of your subscription and you have paid all the invoices. Contact your account manager for more info. You can check your pending invoices below
            </h5>

            <div class="d-flex justify-content-center my-2">

            <a href="<?= base_url('/invoices/institute_invoices/' . session()->get('instituteID')); ?>" class="btn btn-success mx-2"> Check Invoices </a>

            <a href="<?= base_url('/support/account_manager'); ?>" class="btn btn-secondary mx-2"> Account Manager Info </a>

            </div>

        </div>
        <div class="text-center">
            <img src="<?= base_url('assets/img/feature_locked.jpg') ?>" class="img-fluid" alt="Feature" style="max-height:500px;" />
        </div>
    </div>


</div>
</div>




<!-- Include Footer -->
<?php include_once(APPPATH . "Views/footer.php"); ?>