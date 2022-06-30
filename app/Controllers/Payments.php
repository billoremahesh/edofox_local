<?php

namespace App\Controllers;

use \App\Models\InvoiceModel;
use \App\Models\PaymentsModel;

class Payments extends BaseController
{

    public function index()
    {
        // Card Credentials
        // Number: 4242 4242 4242 4242
        // Date: Any valid future date
        // CVV: 111
        // Name: Any Name
        // 3D-secure password: 1221
        return redirect()->to(base_url('/home'));
    }

    public function pay_invoice(string $public_id)
    {
        $session = session();
        //Check if payment done, then redirect to profile dashboard
        $InvoiceModel = new InvoiceModel();
        $invoice_id = $InvoiceModel->fetch_invoice_id($public_id);
        $invoice_data = $InvoiceModel->fetch_invoice_data($invoice_id);
        $checkPaymentresult = $InvoiceModel->check_invoice_payment($invoice_id);
        if ($checkPaymentresult) {
            $session->setFlashdata('toastr_error', 'You have already made the payment');
            return redirect()->to(base_url('/payments'));
        }

        // Set User Session Manually
        $usr_phone = session()->get('mobile');
        $usr_name = session()->get('name');
        $usr_email = session()->get('email');
        $pack_name = $invoice_data['invoice_ref'];
        $pack_amt = $invoice_data['amount_payable'];

        // CURL Payment Request
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, env('PAYMENT_CURLOPT_URL'));
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                "X-Api-Key:" . env('PAYMENT_API_KEY'),
                "X-Auth-Token:" . env('PAYMENT_AUTH_TOKEN')
            )
        );

        $payload = array(
            'purpose' => "$pack_name",
            'amount' => "$pack_amt",
            'phone' => "$usr_phone",
            'buyer_name' => "$usr_name",
            'redirect_url' => env('PAYMENT_REDIRECT_URL'),
            'send_email' => true,
            'webhook' => env('PAYMENT_WEBHOOK_URL'),
            'send_sms' => true,
            'email' => "$usr_email",
            'allow_repeated_payments' => false
        );
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));
        $response = curl_exec($ch);
        curl_close($ch);
        $json_decode = json_decode($response, true);
        // print_r($json_decode);
        $payment_request_id = $json_decode['payment_request']['id'];
        $long_url = $json_decode['payment_request']['longurl'];
        echo "URL: $long_url. REQUEST ID: $payment_request_id";


        $payment_data['institute_id'] =  $invoice_data['institute_id'];
        $payment_data['amount'] = $invoice_data['invoice_amount'];
        $payment_data['payment_request_id'] = $payment_request_id;
        $PaymentsModel = new PaymentsModel();

        $payment_trx_id  = $PaymentsModel->add_payment_info($payment_data);
        if ($payment_trx_id) {
            $update_data['payment_trx_id'] = $payment_trx_id;
            $update_data['invoice_id'] = $invoice_id;
            $InvoiceModel->update_invoice_details($update_data);
            // Then redirect to the payment page
            header('Location:' . $long_url);
            exit(); // This is important. if exit not added, CI4 does not redirect
        } else {
            $session->setFlashdata('toastr_error', 'Error in saving payment request.');
            return redirect()->to(base_url('/login'));
        }
    }



    /*******************************************************/
    /**
     * Redirect method after payment
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function payment_redirect()
    {
        $session = session();

        // Get data from the URL
        $data = $this->request->getVar();

        if (isset($data['payment_request_id']) && !empty($data['payment_request_id'])) {

            // Checking payment ID
            if (isset($data['payment_id']) && !empty($data['payment_id'])) {

                // Checking whether student exists with payment request ID
                $PaymentsModel = new PaymentsModel();
                if ($PaymentsModel->check_invoice_payment_request($data['payment_request_id']) == 1) {
                    $session->set("payment_id", $data['payment_id']);
                } else {
                    // Student not found
                    $session->setFlashdata('toastr_error', 'Record not found. Please try again.');
                    return redirect()->to(base_url('/login'));
                }
            } else {
                // No payment request id in URL
                $session->setFlashdata('toastr_error', 'There was some error retriving payment ID. Please contact support team regarding confirmation of your payment.');
                return redirect()->to(base_url('/home'));
            }
        } else {
            // No payment request id in URL
            $session->setFlashdata('toastr_error', 'There was some error retriving payment data. Please do not make payment again if your account has been debited.');
            return redirect()->to(base_url('/home'));
        }


        return view('payments/payment_redirect');
    }
    /*******************************************************/



    /*******************************************************/
    /**
     * Webhook method after payment
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function webhook()
    {

        $data['payment_id'] = $_POST['payment_id'];
        $data['payment_request_id'] = $_POST['payment_request_id'];
        $data['status'] = $_POST['status'];
        $data['payment_medium'] = "ONLINE PAYMENT";

        $status = $_POST['status'];
        $buyer_phone = $_POST['buyer_phone'];
        $buyer_name = $_POST['buyer_name'];

        if ($status == 'Credit') {
            // Update payment ID and status
            $PaymentsModel = new PaymentsModel();
            if ($PaymentsModel->update_payment_info($data)) {

                // Then send SMS for success

            } else {
                // Failed

                // Send SMS for failure

            }
        } else {
            // Credit failed or transaction failed

            // Send SMS for failure

        }
    }
    /*******************************************************/



    /*******************************************************/
    /**
     * To check whether the payment is alloted or not from webhook
     * Added By @Hemant
     */
    public function ajax_check_payment_alloted()
    {

        $data = $this->request->getVar();
        if (isset($data['action']) && $data['action'] === "check") {
            $data['payment_id'] = session()->get('payment_id');
            $institute_name = session()->get('institute_name');
            // Trimming name to 30 characters for SMS {#var} restriction
            $institute_name = (strlen($institute_name) > 30) ? substr($institute_name, 0, 28) : $institute_name;


            $buyer_phone = "+91" . session()->get('mobile_no');


            $PaymentsModel = new PaymentsModel();
            if ($PaymentsModel->check_payment_alloted($data) == 1) {
                echo "SUCCESS";
            } else {
                echo "CHECK AGAIN";
            }
        }
    }
    /*******************************************************/


    /**
     * Payment Response - Success Message
     */
    public function response()
    {
        $data['title'] = 'Success';
        return view('payments/response', $data);
    }
    /*******************************************************/



    public function share_payment_link(string $public_id)
    {
        $data['title'] = "Share Payment Link";
        $data['public_id'] = $public_id;
        echo view('modals/payments/share_link', $data);
    }
}
