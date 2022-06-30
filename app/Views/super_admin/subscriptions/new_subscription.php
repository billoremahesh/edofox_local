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
                <li class="breadcrumb_item"><a href="<?php echo base_url('institutes'); ?>"> Institutes </a></li>
                <li class="breadcrumb_item"><a href="<?php echo base_url("/subscriptions/overview/".encrypt_string($institute_data['id'])); ?>"> <?= $institute_data['institute_name'];?> </a></li>
                <li class="breadcrumb_item active" aria-current="page"> <?= $title; ?> </li>
            </ol>
        </div>
    </div>

    <!-- Content Row -->
    <div class="card shadow mb-4" style="max-width: 900px;margin:auto;">

        <div class="card-body">

            <?php echo form_open('subscriptions/new_subscription_submit'); ?>

            <div class="row">
                <div class="col-4 my-2">
                    <label class="form-label" for="subscription_type"> Subscription Type </label>
                    <select class="form-control" name="subscription_type" onchange="get_subcription_plans();get_unbilled_entitites();" id="subscription_type" required>
                        <option></option>
                        <option value="Monthly"> Monthly </option>
                        <option value="Yearly"> Yearly </option>
                    </select>
                </div>


                <div class="col-4 my-2">
                    <label class="form-label" for="max_students"> Max Students </label>
                    <input type="number" name="max_students" id="max_students" value="<?= $institute_data['max_students']; ?>" readonly />
                </div>


                <?php if (in_array("all_super_admin_perms", session()->get('perms'))) :  ?>
                    <div class="col-12 my-2">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" onchange="get_subcription_plans()" value="1" name="add_amount_manual" id="add_amount_manual">
                            <label class="form-check-label" for="add_amount_manual">
                                Add Amount Manually
                            </label>
                        </div>
                    </div>
                <?php endif; ?>

            </div>

            <div class="row">

                <div class="col-6 p-4">

                    <!-- Loader Div -->
                    <div id="custom_loader"></div>

                    <div id="subcription_plans">

                    </div>

                    <div id="manual_subcription_plans" style="display: none;">
                        <div class='package_info_card y-1'>
                            <div class="mb-2">
                                <label for="manual_plan_name">Plan Name</label>
                                <input type='text' id="manual_plan_name" class="form-control mb-2" name='manual_plan_name' />
                            </div>
                            <div class="mb-2">
                                <label for="exam_amount">Exam Amount</label>
                                <input type='text' name='exam_amount' id="exam_amount" class="form-control mb-2 module_manual_amount" value="0" />
                            </div>
                            <div class="mb-2">
                                <label for="omr_amount">OMR Amount</label>
                                <input type='text' name='omr_amount' id="omr_amount" class="form-control mb-2 module_manual_amount" value="0" />
                            </div>
                            <div class="mb-2">
                                <label for="dlp_amount">DLP Amount</label>
                                <input type='text' name='dlp_amount' id="dlp_amount" class="form-control mb-2 module_manual_amount" value="0" />
                            </div>
                            <div class="mb-2">
                                <label for="live_amount">Live Amount</label>
                                <input type='text' name='live_amount' id="live_amount" class="form-control mb-2 module_manual_amount" value="0" />
                            </div>
                            <div class="mb-2">
                                <label for="support_amount">Support Amount</label>
                                <input type='text' name='support_amount' id="support_amount" class="form-control mb-2 module_manual_amount" value="0" />
                            </div>
                        </div>
                    </div>

                    <div id="unbilled_entitites">

                    </div>
                </div>

                <div class="col-6 p-4">

                    <div class="d-flex flex-column">

                        <div class="d-flex flex-row-reverse my-2 package_total_amt_text">
                            <div class="mx-2">
                                <input type="hidden" id="packages_total_amt" name="packages_total_amt" value="0" required />
                                <div id="packages_total_amt_div"> 0.00 </div>
                            </div>
                            <div> Package Total Amount: &#8377;</div>
                        </div>


                        <div class="d-flex flex-row-reverse checkout_text my-2">
                            <div class="mx-2">
                                <select class="form-select" name="next_invoice_date" id="next_invoice_date" required>
                                    <?php
                                    $invoice_months = array(date('M', strtotime('+1 month')), date('M', strtotime('+2 month')), date('M', strtotime('+3 month')));
                                    if (!empty($invoice_months)) {
                                        foreach ($invoice_months as $invoice_month) {
                                            echo "<option value='$invoice_month'>$invoice_month</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div> Next Invoice Date </div>
                        </div>

                        <div class="d-flex flex-row-reverse my-2">
                            <div class="form-floating">
                                <textarea class="form-control" placeholder="Leave a comment here" id="comments" name="comments" style="height: 100px"></textarea>
                                <label for="comments">Comments</label>
                            </div>
                        </div>

                        <div class="d-flex flex-row-reverse checkout_text my-2">
                            <div class="mx-2">
                                <input type="number" min='0' max='20' id="discount" name="discount" value="0" autocomplete="off" required /> %
                            </div>
                            <div> Discount </div>
                        </div>

                        <div class="d-flex flex-row-reverse total_saved_text my-2">
                            <div id="total_saved"> 0.00 </div>
                            <div> Total Saved: &#8377;</div>
                        </div>

                        <div class="d-flex flex-row-reverse footer_total_amt_text my-2">

                            <div class="mx-2">
                                <input type="hidden" id="final_total_amt" name="final_total_amt" value="0" required />
                                <input type="hidden" id="institute_name" name="institute_name" value="<?= $institute_data['institute_name']; ?>" required />
                                <input type="hidden" id="institute_id" name="institute_id" value="<?= $institute_data['id']; ?>" required />
                                <input type="hidden" id="redirect" name="redirect" value="/subscriptions/overview/<?= encrypt_string($institute_data['id']); ?>" required />

                                <div id="final_total_amt_div"> 0.00 </div>
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
<script src="<?php echo base_url('assets/js/subscriptions.js?v=20220512'); ?>"></script>

<script>
    function get_unbilled_entitites() {
        var subscription_type = $("#subscription_type").val();
        var max_students = $("#max_students").val();
        if (subscription_type != "") {
            $.ajax({
                url: base_url + "/subscriptions/get_unbilled_entitites",
                type: "POST",
                data: {
                    plan_type: subscription_type,
                    max_students: max_students,
                },
                success: function(result) {
                    $("#unbilled_entitites").html(format_unbilled_entitites(result));
                },
            });
        } else {
            $("#unbilled_entitites").html("");
        }
    }
</script>