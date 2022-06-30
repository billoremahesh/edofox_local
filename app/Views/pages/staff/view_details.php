<!-- Include Header -->
<?php include_once(APPPATH . "Views/header.php"); ?>

<!-- Custom CSS -->
<link href="<?php echo base_url('assets/css/classrooms/overview.css?v=20210917'); ?>" rel="stylesheet">


<div id="content">
    <div class="container-fluid mt-4">

        <div class="flex-container-column">
            <div>
                <label class="h5 text-gray-800 text-uppercase"> <?= $title; ?> </h5>
            </div>
            <div class="breadcrumb_div" aria-label="breadcrumb">
                <ol class="breadcrumb_custom">
                    <li class="breadcrumb_item"><a href="<?php echo base_url('/home'); ?>"> Home </a></li>
                    <li class="breadcrumb_item"><a href="<?php echo base_url('staff'); ?>"> Staff </a></li>
                    <li class="breadcrumb_item active" aria-current="page"> <?= $title; ?> </li>
                </ol>
            </div>
        </div>


        <div class="card shadow p-4">
            <div class="row">
                <div class="col-md-8">
                    <table class="table table-borderless mb-3">
                        <tr>
                            <td>Name</td>
                            <td><?= $staff_details['name']; ?></td>
                        </tr>

                        <tr>
                            <td>Email</td>
                            <td><?= $staff_details['email']; ?></td>
                        </tr>

                        <tr>
                            <td>Mobile</td>
                            <td><?= $staff_details['mobile']; ?></td>
                        </tr>

                        <tr>
                            <td>Username</td>
                            <td><?= $staff_details['username']; ?></td>
                        </tr>

                    </table>
                </div>
                <div class="col-md-4">
                    <div class="text-center">
                        <div class="d-flex justify-content-center">
                            <?php
                            $profile['profile_picture_url'] = $staff_details['profile_img_url'];
                            $profile['size'] = "50px";
                            echo view('pages/profile/profile_image_circle.php', $profile);
                            ?>
                        </div>
                        
                        <div class="fw-bold"><?php echo $_SESSION['username']; ?></div>
                    </div>
                </div>
            </div>
        </div>


    </div>
</div>

<!-- Include Footer -->
<?php include_once(APPPATH . "Views/footer.php"); ?>