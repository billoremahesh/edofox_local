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

<!-- Custom CSS -->
<link href="<?php echo base_url('assets/css/invoice/print_invoice.css?v=20220518'); ?>" rel="stylesheet">

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
                    <div style="display: flex;justify-content:space-between;color: black;" id="header_print">
                        <img class="img-fluid" src="<?= base_url('assets/img/mattersoft_logo_with_name.png'); ?>" style="width:200px" alt='Edofox Logo' />
                        <div>
                            <div>
                                <?php
                                if ($invoice_data["status"] == 'Paid') {
                                    echo "TAX INVOICE";
                                } else {
                                    echo "Pro forma";
                                }
                                ?>
                            </div>
                            <div><?= $invoice_data['invoice_ref']; ?></div>
                        </div>
                    </div>
                    <div style="margin-top: 8px;margin-bottom:8px;border-top:1px solid black;color: black;"></div>
                    <div class="text-center">
                        <div style="color: black;">Original for Recipient</div>
                    </div>
                    <div style="display:flex;justify-content:space-between;">
                        <div style="display: flex;justify-content:space-between;flex-direction: column;color: black;">
                            <div> <b>Company:</b> Matter Softwares Pvt. Ltd. </div>
                            <div> <b>Company Address:</b> S.NO. 60/3, Shiv Nagari,Bijali NGR<br />
                                Chinchwad,Pune<br />
                                Maharashtra, State Code: 27, India<br />
                                Phone: 7350182285,Email: contact@edofox.com<br />
                                <b>GSTIN:</b> 27AAHCR0352E1ZN </div>
                            <div> <b>CIN No:</b> U74999PN2014PTC152228 </div>
                        </div>
                        <div style="color: black;">
                            <div><b>Date:</b> <?= date_format_custom($invoice_data['created_date'], "d/m/Y"); ?></div>
                            <div><b>Payment Due Date</b> : <?= date_format_custom($invoice_data['due_date'], "d/m/Y"); ?></div>
                        </div>
                    </div>

                    <hr />

                    <div style="display: flex;justify-content:space-between;flex-direction: column;color: black;">
                        <div>
                            <b>Customer Name: <?= $invoice_data['institute_name']; ?></b>
                        </div>
                        <div><b>Address:</b> <?= $invoice_data['institute_address']; ?>
                            <div>
                                <b>Customer GSTIN:</b>
                                <?= $invoice_data['institute_gst_no']; ?>
                            </div>
                        </div>

                        <p><b>Number of students:</b> <?= $invoice_data['no_of_students']; ?></p>

                        <table class="table table-bordered table-condensed" style="font-size: 12px;font-weight:600;">
                            <thead>
                                <tr>
                                    <th>Sr</th>
                                    <th>Item</th>
                                    <th>Descripton</th>
                                    <th>HSN/SAC</th>
                                    <th style='text-align:right;'>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $i = 1;
                                $total_amount = 0;
                                $plan_name = "";
                                if (!empty($invoice_data['plan_name'])) {
                                    $plan_name = "(" . $invoice_data['plan_name'] . ")";
                                }
                                if ($invoice_data['exam_amount'] != 0) {
                                    echo "<tr>";
                                    echo "<td>$i</td>";
                                    echo "<td> Exam Module $plan_name </td>";
                                    echo "<td> Module Cost </td>";
                                    echo "<td> 998313 </td>";
                                    echo "<td style='text-align:right;'>&#8377; " . number_format($invoice_data['exam_amount'], 2) . "</td>";
                                    echo "</tr>";
                                    $total_amount = $total_amount + $invoice_data['exam_amount'];
                                    $i++;
                                }

                                if ($invoice_data['omr_amount'] != 0) {
                                    echo "<tr>";
                                    echo "<td>$i</td>";
                                    echo "<td> OMR (Offline) Module $plan_name </td>";
                                    echo "<td> Module Cost </td>";
                                    echo "<td> 998313 </td>";
                                    echo "<td style='text-align:right;'>&#8377; " . number_format($invoice_data['omr_amount'], 2) . "</td>";
                                    echo "</tr>";
                                    $total_amount = $total_amount + $invoice_data['omr_amount'];
                                    $i++;
                                }


                                if ($invoice_data['dlp_amount'] != 0) {
                                    echo "<tr>";
                                    echo "<td>$i</td>";
                                    echo "<td> DLP Module $plan_name </td>";
                                    echo "<td> Module Cost </td>";
                                    echo "<td> 998313 </td>";
                                    echo "<td style='text-align:right;'>&#8377; " . number_format($invoice_data['dlp_amount'], 2) . "</td>";
                                    echo "</tr>";
                                    $total_amount = $total_amount + $invoice_data['dlp_amount'];
                                    $i++;
                                }


                                if ($invoice_data['live_amount'] != 0) {
                                    echo "<tr>";
                                    echo "<td>$i</td>";
                                    echo "<td> Live Module $plan_name </td>";
                                    echo "<td> Module Cost </td>";
                                    echo "<td> 998313 </td>";
                                    echo "<td style='text-align:right;'>&#8377; " . number_format($invoice_data['live_amount'], 2) . "</td>";
                                    echo "</tr>";
                                    $total_amount = $total_amount + $invoice_data['live_amount'];
                                    $i++;
                                }

                                if ($invoice_data['support_amount'] != 0) {
                                    echo "<tr>";
                                    echo "<td>$i</td>";
                                    echo "<td> Dedicated Support $plan_name </td>";
                                    echo "<td> Module Cost </td>";
                                    echo "<td> 998313 </td>";
                                    echo "<td style='text-align:right;'>&#8377; " . number_format($invoice_data['support_amount'], 2) . "</td>";
                                    echo "</tr>";
                                    $total_amount = $total_amount + $invoice_data['support_amount'];
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

                        <div id="footer_print">
                            <div style="display: flex;justify-content:space-between;flex-direction: row-reverse;color: black;">
                                <div>
                                    <div style="text-align:right;">Total: &#8377; <?= number_format($invoice_data['invoice_amount'], 2); ?></div>
                                    <div style="text-align:right;">Output Tax SGST @ 9.0 : &#8377; <?= number_format($invoice_data['sgst_tax_amount'], 2); ?> </div>
                                    <div style="text-align:right;">Output Tax CGST @ 9.0: &#8377; <?= number_format($invoice_data['cgst_tax_amount'], 2); ?></div>
                                    <div style="text-align:right;">Final Total: &#8377; <?= number_format($invoice_data['amount_payable'], 2); ?></div>
                                    <div style="text-align:right;">Rounded Total: &#8377; <?= number_format($invoice_data['amount_payable'], 2); ?> </div>
                                    <div style="text-align:right;">In Words: INR <?= getIndianCurrencyInWords($invoice_data['amount_payable']); ?></div>
                                </div>
                            </div>

                            <div style="display: flex;justify-content:space-between; ">
                                <div style="display: flex;flex-direction: column; margin-top:4px;">
                                    <div>
                                        <img class="img-fluid" src="<?= base_url('assets/img/sign_ceo.png'); ?>" style="width:150px" alt='Signature' />
                                    </div>
                                    <div><b>Matter Softwares Private Limited</b></div>
                                    <div>CEO Anand Kore</div>
                                </div>
                                <?php if ($invoice_data["status"] == 'Paid') { ?>
                                    <div style="margin-top:4px;">
                                            <img class="img-fluid" src="<?= base_url('assets/img/paid_invoice.jpg'); ?>" style="width:150px" alt='Signature' />
                                    </div>
                                <?php } ?>
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