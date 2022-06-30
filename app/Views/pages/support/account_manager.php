<!-- Include Header -->
<?php include_once(APPPATH . "Views/header.php"); ?>

<!-- Custom CSS -->
<link href="<?php echo base_url('assets/css/support/account_manager.css?v=20220422'); ?>" rel="stylesheet">


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

        <?php
        if (!empty($account_manager_data)) {
        ?>
            <div class="md_card am_card">
                <div class="md_card_media text-center">

                    <?php
                    $acc_manager_img_url = base_url('assets/img/profile-static.png');
                    $acc_manager_img = $account_manager_data['profile_img_url'];
                    if (!empty($acc_manager_img)) {
                        $acc_manager_img_url = $acc_manager_img;
                    }
                    ?>

                    <img class="img-fluid" src="<?= $acc_manager_img_url; ?>" style="width:100%;width:200px;margin: auto;" alt="Account Manager Image" />
                </div>
                <div class="md_card_body">
                    <div class="md_card_title text-center text-uppercase">
                        <?= $account_manager_data['name']; ?>
                    </div>

                    <div class="md_card_wrapper">
                        <div class="md_card_supporting_text">
                            <span class="align-middle me-2 material-icons">
                                call
                            </span> : <?= $account_manager_data['mobile_number']; ?>
                        </div>
                        <div class="md_card_supporting_text">
                            <span class="align-middle me-2 material-icons">
                                mail
                            </span> : <?= $account_manager_data['email']; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php
        }
        ?>

    </div>
</div>

<!-- Include Footer -->
<?php include_once(APPPATH . "Views/footer.php"); ?>