<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-60995650-2"></script>
    <script src="<?php echo base_url('assets/gtag.js'); ?>"></script>

    <meta name="robots" content="noindex,nofollow" />

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="<?php echo base_url('assets/img/favicon.png'); ?>" />
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->

    <title><?= $title; ?></title>


    <!-- Font-Awesome 5.15.4-web -->
    <link rel="stylesheet" href="<?php echo base_url('assets/fontawesome-5.15.4-web/css/all.min.css'); ?>">

    <!-- Bootstrap-5.0.2 -->
    <link rel="stylesheet" href="<?php echo base_url('assets/bootstrap-5.0.2-dist/css/bootstrap.min.css'); ?>">

    <link rel="stylesheet" href="<?php echo base_url('assets/plugins/jquery-ui-1.12.1.custom/jquery-ui.css'); ?>">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Karla:400,700&display=swap" rel="stylesheet">

    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <!-- Snackbar -->
    <link href="<?php echo base_url('assets/plugins/snackbar-material/snackbar.min.css'); ?>" rel="stylesheet" />

    <!-- Toastr CSS -->
    <link href="<?php echo base_url('assets/plugins/toastr/toastr.css'); ?>" rel="stylesheet" />
    <!-- Custom CSS -->
    <link href="<?php echo base_url('assets/css/common.css?v=20220428'); ?>" rel="stylesheet">

    <link href="<?php echo base_url('assets/css/subscriptions/proposal.css?v=20220518'); ?>" rel="stylesheet">

</head>

<body>
    <?php $session = \Config\Services::session(); ?>



    <div id="content">
        <div class="container-fluid mt-4">


            <div class="text-center">
                <label class="h5 text-gray-800 text-uppercase"> <?= $title; ?> </h5>
            </div>


            <div class="text-center">
                <div class="w-100 mt-2">
                    <button class="btn btn-primary" onclick="printDiv();"> Print </button>
                </div>
            </div>

            <div class="card shadow p-4 my-2" style="max-width: 900px;margin:auto;">

                <div id="printableArea">
                    <div style="padding-bottom: 150px;">
                        <div style="display: flex;justify-content:space-between;color: black;" id="header_print">
                            <img class="img-fluid" src="<?= base_url('assets/img/mattersoft_logo_with_name.png'); ?>" style="width:200px" alt='Edofox Logo' />
                            <div>
                                <div>QUOTATION</div>
                                <div><?= $proposal_data['quotation_ref']; ?></div>
                            </div>
                        </div>

                        <div style="margin-top: 8px;margin-bottom:8px;border-top:1px solid black;color: black;"></div>
                        <div style="display: flex;justify-content:space-between;color: black;">
                            <div>
                                <div>
                                    <b>Customer Name: <?= $proposal_data['institute_name']; ?></b>
                                </div>
                                <div>
                                    <b>Proposal for Edofox: <?= $proposal_data['plan_name']." Plan - ".$proposal_data['plan_type']. " Billing"; ?> </b>
                                </div>
                                <div>
                                    <b>Number of students: </b> <?= $proposal_data['no_of_students']; ?>
                                </div>
                            </div>
                            <div>
                                <div><b>Date:</b> <?= date_format_custom($proposal_data['created_date'], "d/m/Y"); ?></div>
                                <div><b>Valid Till</b> : <?= date_format_custom($proposal_data['next_invoice_date'], "d/m/Y"); ?></div>
                            </div>
                        </div>

                        <div style="margin-top: 8px;">
                            <table class="table table-bordered table-condensed">
                                <thead>
                                    <tr>
                                        <th>Sr</th>
                                        <th>Item Code</th>
                                        <th>Description</th>
                                        <th style='text-align:right;'>Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 1;
                                    $total_amount = 0;
                                    if ($proposal_data['exam'] == "2") {
                                        echo "<tr>";
                                        echo "<td>$i</td>";
                                        echo "<td> OMR (Offline Module) </td>";
                                        echo "<td> Module Cost </td>";
                                        echo "<td style='text-align:right;'>&#8377; " . number_format($proposal_data['omr_amount'], 2) . "</td>";
                                        echo "</tr>";
                                        $total_amount = $total_amount + $proposal_data['omr_amount'];
                                        $i++;
                                    }

                                    if ($proposal_data['exam'] == "1") {

                                        echo "<tr>";
                                        echo "<td>$i</td>";
                                        echo "<td> Exam Module </td>";
                                        echo "<td> Module Cost </td>";
                                        echo "<td style='text-align:right;'>&#8377; " . number_format($proposal_data['exam_amount'], 2) . "</td>";
                                        echo "</tr>";
                                        $total_amount = $total_amount + $proposal_data['exam_amount'];
                                        $i++;
                                    }


                                    if ($proposal_data['exam'] == "3") {
                                        echo "<tr>";
                                        echo "<td>$i</td>";
                                        echo "<td> Exam Module </td>";
                                        echo "<td> Module Cost </td>";
                                        echo "<td style='text-align:right;'>&#8377; " . number_format($proposal_data['exam_amount'], 2) . "</td>";
                                        echo "</tr>";
                                        $total_amount = $total_amount + $proposal_data['exam_amount'];
                                        $i++;

                                        echo "<tr>";
                                        echo "<td>$i</td>";
                                        echo "<td> OMR (Offline Module) </td>";
                                        echo "<td> Module Cost </td>";
                                        echo "<td style='text-align:right;'>&#8377; " . number_format($proposal_data['omr_amount'], 2) . "</td>";
                                        echo "</tr>";
                                        $total_amount = $total_amount + $proposal_data['omr_amount'];
                                        $i++;
                                    }


                                    if ($proposal_data['dlp'] == "1") {
                                        echo "<tr>";
                                        echo "<td>$i</td>";
                                        echo "<td> Learning Management System </td>";
                                        echo "<td> Module Cost </td>";
                                        echo "<td style='text-align:right;'>&#8377; " . number_format($proposal_data['dlp_amount'], 2) . "</td>";
                                        echo "</tr>";
                                        $total_amount = $total_amount + $proposal_data['dlp_amount'];
                                        $i++;
                                    }

                                    if ($proposal_data['live'] == "1") {
                                        echo "<tr>";
                                        echo "<td>$i</td>";
                                        echo "<td> Live Lectures </td>";
                                        echo "<td> Module Cost </td>";
                                        echo "<td style='text-align:right;'>&#8377; " . number_format($proposal_data['live_amount'], 2) . "</td>";
                                        echo "</tr>";
                                        $total_amount = $total_amount + $proposal_data['live_amount'];
                                        $i++;
                                    }

                                    if ($proposal_data['support'] == "1") {
                                        echo "<tr>";
                                        echo "<td>$i</td>";
                                        echo "<td> Dedicated Support </td>";
                                        echo "<td> Module Cost </td>";
                                        echo "<td style='text-align:right;'>&#8377; " . number_format($proposal_data['support_amount'], 2) . "</td>";
                                        echo "</tr>";
                                        $total_amount = $total_amount + $proposal_data['support_amount'];
                                        $i++;
                                    }
                                    ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="3"><b>Total</b></th>
                                        <th colspan="2" style='text-align:right;'>&#8377; <?= number_format($total_amount, 2); ?></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div id="footer_print" style="position: absolute;bottom:40px;width:95%;display: flex;flex-direction: row-reverse;justify-content:space-between;color: black;">
                            <div>
                                <div style="text-align:right;">Final Total: &#8377; <?= number_format($total_amount, 2); ?></div>

                                <?php if ($proposal_data['discount'] != "0.00") : ?>
                                    <div style="text-align:right;">Discount : <?= $proposal_data['discount']; ?> %</div>
                                <?php endif; ?>

                                <div style="text-align:right;">Final Amount : &#8377; <?= number_format($proposal_data['amount'], 2); ?></div>
                                <div style="text-align:right;">In Words: INR <?= getIndianCurrencyInWords($proposal_data['amount']); ?></div>
                            </div>
                            <div>
                                18% GST is applicable on the total amount.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>




    <script src="<?php echo base_url('assets/jquery-3.6.0.min.js'); ?>"></script>
    <script src="<?php echo base_url('assets/bootstrap-5.0.2-dist/js/bootstrap.bundle.min.js'); ?>"></script>
    <script src="<?php echo base_url('assets/plugins/jquery-ui-1.12.1.custom/jquery-ui.js'); ?>"></script>
    <!-- Toastr JS -->
    <script src="<?php echo base_url('assets/plugins/toastr/toastr.min.js'); ?>"></script>

    <script src="<?php echo base_url('assets/js/url.js'); ?>"></script>
    <script src="<?php echo base_url('assets/js/angular.js?v=20210829'); ?>"></script>
    <script src="<?php echo base_url('assets/js/main.js?v=20210829'); ?>"></script>
    <script src="<?php echo base_url('assets/js/admin_login.js?v=20210829'); ?>"></script>

    <!-- Snackbar JS -->
    <!-- Ref: https://www.polonel.com/snackbar/ -->
    <script src="<?php echo base_url('assets/plugins/snackbar-material/snackbar.min.js'); ?>"></script>


    <script>
        function printDiv() {
            var printContents = document.getElementById('printableArea').innerHTML;
            var originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
            window.location.reload(true);
        }
    </script>

</body>

</html>