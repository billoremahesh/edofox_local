<!-- Include Header -->
<?php include_once(APPPATH . "Views/header.php"); ?>

<!-- Custom CSS -->
<link href="<?php echo base_url('assets/css/institutes.css?v=20210829'); ?>" rel="stylesheet">

<div class="container-fluid mt-4">


    <div class="flex-container-column">
        <div>
            <label class="h5 text-gray-800 text-uppercase"> <?= $title; ?> </h5>
        </div>
        <div class="breadcrumb_div" aria-label="breadcrumb">
            <ol class="breadcrumb_custom">
                <li class="breadcrumb_item"><a href="<?php echo base_url('/home'); ?>"> Home </a></li>
                <li class="breadcrumb_item"><a href="<?php echo base_url('institutes'); ?>"> Institutes </a></li>
                <li class="breadcrumb_item active" aria-current="page"> <?= $title; ?> </li>
            </ol>
        </div>
    </div>

    <!-- Content Row -->
    <div class="card shadow mb-4" style='max-width:900px;margin:auto;'>

        <div class="card-body">


            <?php echo form_open('institutes/update_institute_details_submit'); ?>


            <input type="hidden" value="<?php echo $institute_id; ?>" id="institute_id" name="institute_id" required>


            <input type="hidden" value="<?php echo $redirect; ?>" id="redirect" name="redirect">

            <div class="row">
                <div class="col-8 my-2">
                    <label class="form-label" for="institute_name"> Institute Name <span class="req_color">*</span></label>
                    <input type="text" name="institute_name" class="form-control form-control-user" id="institute_name" placeholder="Institute Name" value="<?= $institute_data['institute_name'] ?>" maxlength="120">
                </div>

                <div class="col-4 my-2">
                    <label class="form-label" for="alias_name"> Alias Name <span class="req_color">*</span></label>
                    <input type="text" name="alias_name" class="form-control form-control-user" id="alias_name" placeholder="Alias Name" value="<?= $institute_data['alias_name'] ?>" maxlength="90">
                </div>


                <div class="col-6 my-2">
                    <label class="form-label" for="contact">Billing Contact Number <span class="req_color">*</span></label>
                    <input type="text" name="contact" class="form-control form-control-user" id="contact"  value="<?= $institute_data['contact_number'] ?>" required>
                </div>

                <div class="col-6 my-2">
                    <label class="form-label" for="email">Billing Email <span class="req_color">*</span></label>
                    <input type="text" name="email" class="form-control form-control-user" id="email"  value="<?= $institute_data['email'] ?>" required>
                </div>


                <div class="col-12 my-2">
                    <label for="institute_address" class="form-label">Address</label>
                    <textarea class="form-control" id="institute_address" name="institute_address"><?= $institute_data['address']; ?></textarea>
                </div>

                <div class="col-4 my-2">
                    <label class="form-label" for="storage_quota"> Storage Quota (GB)</label>
                    <input type="text" name="storage_quota" class="form-control form-control-user" id="storage_quota" value="<?= $institute_data['storage_quota'] ?>">
                </div>


                <div class="col-4 my-2">
                    <label for="institute_gst_no" class="form-label">GST No</label> (For Invoices)
                    <input type="text" class="form-control" id="institute_gst_no" name="institute_gst_no" value="<?php echo $institute_data['gst_no']; ?>">
                </div>


                <div class="col-4 my-2">
                    <label class="form-label" for="account_manager"> Account Manager <span class="req_color">*</span></label>
                    <select class="form-select" id="account_manager" name="account_manager" required>
                        <option></option>
                        <?php
                        if (!empty($sales_team)) {
                            foreach ($sales_team as $account_manager) {
                                $acc_manager_selected = "";
                                if($account_manager['id'] == $institute_data['account_manager']){
                                    $acc_manager_selected = " selected";
                                }
                        ?>
                                <option value="<?= $account_manager['id']; ?>" <?= $acc_manager_selected; ?> ><?= $account_manager['name']; ?></option>
                        <?php
                            }
                        }
                        ?>
                    </select>

                </div>

                <div class="col-12 my-2">
                    <label for="web_url" class="form-label">Web URL</label>
                    <input type="text" class="form-control" id="web_url" name="web_url" value="<?php echo $institute_data['web_url']; ?>">
                </div>


                <div class="col-12 my-2">
                    <label for="app_url" class="form-label">APP URL</label>
                    <input type="text" class="form-control" id="app_url" name="app_url" value="<?php echo $institute_data['app_url']; ?>">
                </div>

                <div class="col-4 my-2">
                    <label for="app_version" class="form-label">APP Version</label>
                    <input type="text" class="form-control" id="app_version" name="app_version" value="<?php echo $institute_data['app_version']; ?>">
                </div>

                <div class="col-4 my-2">
                    <label for="latitude" class="form-label">Latitude</label>
                    <input type="text" class="form-control" id="latitude" name="latitude" value="<?php echo $institute_data['latitude']; ?>">
                </div>

                <div class="col-4 my-2">
                    <label for="longitude" class="form-label">Longitude</label>
                    <input type="text" class="form-control" id="longitude" name="longitude" value="<?php echo $institute_data['longitude']; ?>">
                </div>


                <div class="col-md-6 mb-3">
                    <label for="video_streaming_condition" class="form-label">Allow Videos Streaming In: *</label>
                    <select class="form-control" id="video_streaming_condition" name="video_streaming_condition">
                        <option value="">Everywhere</option>
                        <option value="APP" <?php if ($institute_data['video_constraint'] == "APP") echo "selected"; ?>>APP Only</option>
                    </select>
                </div>


                <div class="col-md-6 mb-3">
                    <label for="institute_address" class="form-label">Timezone</label>
                    <select class="timezone_dropdown" name="timezone" id="timezone" >
                        <option value=""></option>
                        <?php
                        if (!empty($timezones)) :
                            foreach ($timezones as $timezone) :
                                $select_check = "";
                                if ($timezone['zone_name'] == $institute_data['timezone']) :
                                    $select_check = "selected";
                                endif;
                        ?>
                                <option value="<?= $timezone['zone_name']; ?>" <?= $select_check; ?>>
                                    <?= $timezone['zone_name']; ?>
                                </option>
                        <?php
                            endforeach;
                        endif;
                        ?>
                    </select>
                </div>

                <div class="col-12 my-2">
                    <button type="submit" name="edit_institute_submit" class="btn btn-primary" id="submit_button"> Update </button>
                </div>

            </div>

            <?php echo form_close(); ?>
        </div>
    </div>




</div>

<!-- Include Footer -->
<?php include_once(APPPATH . "Views/footer.php"); ?>

<script>
    // Initializing select2
    $('.timezone_dropdown').select2({
        width: "100%",
    });
</script>