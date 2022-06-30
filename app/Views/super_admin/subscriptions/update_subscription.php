<!-- Include Header -->
<?php include_once(APPPATH . "Views/header.php"); ?>

<!-- Custom CSS -->
<link href="<?php echo base_url('assets/css/subscriptions/new_subscription.css?v=20220428'); ?>" rel="stylesheet">

<div class="container-fluid mt-4">


    <div class="flex-container-column">
        <div>
            <label class="h5 text-gray-800 text-uppercase"> <?= $title; ?> </h5>
        </div>
        <div class="breadcrumb_div" aria-label="breadcrumb">
            <ol class="breadcrumb_custom">
                <li class="breadcrumb_item"><a href="<?php echo base_url('/home'); ?>"> Home </a></li>
                <li class="breadcrumb_item"><a href="<?php echo base_url('/institutes'); ?>"> Institutes </a></li>
                <li class="breadcrumb_item"><a href="<?php echo base_url('/subscriptions/overview/' . encrypt_string($subscription_data['institute_id'])); ?>"> <?= $institute_data['institute_name']; ?> </a></li>
                <li class="breadcrumb_item active" aria-current="page"> <?= $title; ?> </li>
            </ol>
        </div>
    </div>

    <!-- Content Row -->
    <div class="card shadow mb-4" style="max-width: 900px;margin:auto;">

        <div class="card-body">

            <?php echo form_open('subscriptions/update_subscription_submit'); ?>

            <div class="row">
                <div class="col-4 my-2">
                    <label class="form-label" for="subscription_type"> Subscription Type </label>

                    <input type="text" name="subscription_type" id="subscription_type" value="<?= $subscription_data['plan_type']; ?>" readonly required />

                </div>


                <div class="col-4 my-2">
                    <?php
                    $max_students_readonly = "readonly";
                    if (in_array("all_super_admin_perms", session()->get('perms'))) {
                        $max_students_readonly = "";
                    }
                    ?>
                    <label class="form-label" for="max_students"> Max Students </label>
                    <input type="number" name="max_students" id="max_students" value="<?= $subscription_data['no_of_students']; ?>" <?= $max_students_readonly; ?> />
                </div>

                <?php if (in_array("all_super_admin_perms", session()->get('perms'))) :  ?>
                    <?php


                    $manual_checked = "";
                    if ($subscription_data['manual_plan'] == 1) {
                        $manual_checked = " Checked";
                    }
                    ?>
                    <div class="col-12 my-2">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" onchange="get_subcription_plans()" value="1" <?= $manual_checked; ?> name="add_amount_manual" id="add_amount_manual">
                            <label class="form-check-label" for="add_amount_manual">
                                Add Amount Manually
                            </label>
                        </div>
                    </div>
                <?php endif; ?>

            </div>

            <div class="row">
                <div class="col-6 p-4">
                    <?php
                    $plan_selected_total_amount = 0;
                    ?>
                    <!-- Loader Div -->
                    <div id="custom_loader"></div>



                    <div id="manual_subcription_plans" style="display: none;">

                        <div class='package_info_card y-1'>
                            <div class="mb-2">
                                <label for="manual_plan_name">Plan Name</label>
                                <input type='text' id="manual_plan_name" class="form-control mb-2" name='manual_plan_name' value="<?= $subscription_data['plan_name']; ?>" />
                            </div>
                            <div class="mb-2">
                                <label for="exam_amount">Exam Amount</label>
                                <input type='text' name='exam_amount' id="exam_amount" class="form-control mb-2 module_manual_amount" value="<?= $subscription_data['exam_amount']; ?>" />
                            </div>
                            <div class="mb-2">
                                <label for="omr_amount">OMR Amount</label>
                                <input type='text' name='omr_amount' id="omr_amount" class="form-control mb-2 module_manual_amount" value="<?= $subscription_data['omr_amount']; ?>" />
                            </div>
                            <div class="mb-2">
                                <label for="dlp_amount">DLP Amount</label>
                                <input type='text' name='dlp_amount' id="dlp_amount" class="form-control mb-2 module_manual_amount" value="<?= $subscription_data['dlp_amount']; ?>" />
                            </div>
                            <div class="mb-2">
                                <label for="live_amount">Live Amount</label>
                                <input type='text' name='live_amount' id="live_amount" class="form-control mb-2 module_manual_amount" value="<?= $subscription_data['live_amount']; ?>" />
                            </div>
                            <div class="mb-2">
                                <label for="support_amount">Support Amount</label>
                                <input type='text' name='support_amount' id="support_amount" class="form-control mb-2 module_manual_amount" value="<?= $subscription_data['support_amount']; ?>" />
                            </div>
                        </div>

                    </div>


                    <div id="subcription_plans">
                        <?php
                        if ($subscription_data['manual_plan'] == 0) {
                        ?>
                            <?php
                            if ($subscription_plans) {

                                foreach ($subscription_plans as $plan) {
                                    $plan_selected = "";

                                    if ($plan['module'] == "Exam") {
                                        if ($subscription_data['exam'] == 1 or $subscription_data['exam'] == 3) {
                                            $plan_selected = " Checked";
                                            $plan_selected_total_amount = $plan_selected_total_amount + ($subscription_data['no_of_students'] * $plan['price']);
                                        }
                                    }

                                    if ($plan['module'] == "OMR") {
                                        if ($subscription_data['exam'] == 2 or $subscription_data['exam'] == 3) {
                                            $plan_selected = " Checked";
                                            $plan_selected_total_amount = $plan_selected_total_amount + ($plan['price']);
                                        }
                                    }

                                    if ($plan['module'] == "Live") {
                                        if ($subscription_data['live'] == 1) {
                                            $plan_selected = " Checked";
                                            $plan_selected_total_amount = $plan_selected_total_amount + ($subscription_data['no_of_students'] * $plan['price']);
                                        }
                                    }

                                    if ($plan['module'] == "DLP") {
                                        if ($subscription_data['dlp'] == 1) {
                                            $plan_selected = " Checked";
                                            $plan_selected_total_amount = $plan_selected_total_amount + ($subscription_data['no_of_students'] * $plan['price']);
                                        }
                                    }

                                    if ($plan['module'] == "Support") {
                                        if ($subscription_data['support'] == 1) {
                                            $plan_selected = " Checked";
                                            $plan_selected_total_amount = $plan_selected_total_amount + ($plan['price']);
                                        }
                                    }

                            ?>
                                    <div class='col-12 package_info_card y-1'>
                                        <div class='form-check'>
                                            <input class='form-check-input' onclick='calculate_amount()' name='check_pkg[]' type='checkbox' value="<?= $plan['id']; ?>" id="<?= $plan['id']; ?>" <?= $plan_selected; ?>>
                                            <label class='form-check-label' for="<?= $plan['id']; ?>">
                                                <?= $plan['module'] . "( " . $plan['plan_name'] . " )"; ?>
                                            </label>
                                        </div>
                                    </div>
                            <?php
                                }
                            }

                            ?>

                        <?php
                        }
                        ?>
                    </div>
                </div>
                <div class="col-6 p-4">

                    <div class="d-flex flex-column">

                        <div class="d-flex flex-row-reverse my-2 package_total_amt_text">
                            <div class="mx-2">
                                <input type="hidden" id="packages_total_amt" name="packages_total_amt" value="<?= $plan_selected_total_amount; ?>" required />
                                <div id="packages_total_amt_div"> <?= $plan_selected_total_amount; ?> </div>
                            </div>
                            <div> Package Total Amount: &#8377;</div>
                        </div>


                        <div class="d-flex flex-row-reverse checkout_text my-2">
                            <div class="mx-2">

                                <select class="form-select" name="next_invoice_date" id="next_invoice_date" onchange="final_total_amount()" required>
                                    <?php
                                    $invoice_months = array(date('M', strtotime('+1 month')), date('M', strtotime('+2 month')), date('M', strtotime('+3 month')));
                                    if (!empty($invoice_months)) {

                                        foreach ($invoice_months as $invoice_month) {
                                            $invoice_month_selected = "";
                                            if (date("M", strtotime($subscription_data['next_invoice_date'])) == $invoice_month) {
                                                $invoice_month_selected = " selected";
                                            }
                                            echo "<option value='$invoice_month' $invoice_month_selected >$invoice_month</option>";
                                        }
                                    }
                                    ?>
                                </select>

                            </div>
                            <div> Next Invoice Date </div>
                        </div>

                        <div class="d-flex flex-row-reverse my-2">
                            <div class="form-floating">
                                <textarea class="form-control" placeholder="Leave a comment here" id="comments" name="comments" style="height: 100px"><?= $subscription_data['comments']; ?></textarea>
                                <label for="comments">Comments</label>
                            </div>
                        </div>

                        <div class="d-flex flex-row-reverse checkout_text my-2">
                            <div class="mx-2">
                                <input type="number" min='0' max='20' id="discount" name="discount" value="<?= $subscription_data['discount']; ?>" autocomplete="off" required /> %
                            </div>
                            <div> Discount </div>
                        </div>

                        <div class="d-flex flex-row-reverse total_saved_text my-2">
                            <div id="total_saved"> 0.00 </div>
                            <div> Total Saved: &#8377;</div>
                        </div>

                        <div class="d-flex flex-row-reverse footer_total_amt_text my-2">

                            <div class="mx-2">
                                <input type="hidden" id="plan_name" name="plan_name" value="<?= $subscription_data['plan_name']; ?>" required />
                                <input type="hidden" id="final_total_amt" name="final_total_amt" value="<?= $subscription_data['amount']; ?>" required />
                                <input type="hidden" id="subscription_id" name="subscription_id" value="<?= $subscription_id; ?>" required />
                                <input type="hidden" id="institute_id" name="institute_id" value="<?= $subscription_data['institute_id']; ?>" required />
                                <input type="hidden" id="redirect" name="redirect" value="/subscriptions/overview/<?= encrypt_string($subscription_data['institute_id']); ?>" required />

                                <div id="final_total_amt_div"> <?= $subscription_data['amount']; ?> </div>
                            </div>

                            <div> Final Total Amount: &#8377;</div>
                        </div>

                        <div class="d-flex flex-row-reverse my-2">
                            <div id="submit_btn_div">
                            </div>
                        </div>

                    </div>
                </div>

            </div>

            <?php echo form_close(); ?>

        </div>
    </div>



</div>

<!-- Include Footer -->
<?php include_once(APPPATH . "Views/footer.php"); ?>

<!-- Subscription Js -->
<script src="<?php echo base_url('assets/js/subscriptions.js?v=20220505'); ?>"></script>


<?php
if ($subscription_data['manual_plan'] == 1) {
?>
    <script>
        $("#subcription_plans").html("");
        $("#manual_subcription_plans").show();
    </script>
<?php
}
?>