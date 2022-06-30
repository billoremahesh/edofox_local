<?php

namespace App\Controllers;

use \App\Models\InvoiceModel;


class Invoices extends BaseController
{


    /*****************************************************************
     * #################### SUPER ADMIN CODE END HERE ###############
     *****************************************************************/
    public function index()
    {
        // Check permission to view Super Admin
        if (!isAuthorizedSuperAdmin()) {
            return redirect()->to(base_url('/home'));
        }
        $postData['super_admin_role'] = session()->get('super_admin_role');
        $postData['login_id'] = decrypt_cipher(session()->get('login_id'));
        $InvoiceModel = new InvoiceModel();
        $data['title'] = "Billing";
        $data['total_pending_invoices'] = $InvoiceModel->total_pending_invoices($postData);
        $data['overdue_invoices'] = $InvoiceModel->overdue_invoices($postData);
        $data['invoices_expired'] = $InvoiceModel->invoices_expired($postData);

        $data['total_invoices'] = $InvoiceModel->fetch_invoices_data($postData);
        $data['invoices_data'] = json_encode($data['total_invoices']);
        return view('super_admin/invoices/overview', $data);
    }
    /*******************************************************/



    /**
     * Institute Invoices 
     */
    public function institute_invoices(string $institute_id)
    {
        $data['title'] = "Institute Invoices";
        $data['institute_id'] = $institute_id;
        $InvoiceModel = new InvoiceModel();
        $postData['institute_id'] = decrypt_cipher($institute_id);
        $data['total_pending_invoices'] = $InvoiceModel->total_pending_invoices($postData);
        $data['overdue_invoices'] = $InvoiceModel->overdue_invoices($postData);
        $data['invoices_expired'] = $InvoiceModel->invoices_expired($postData);
        $data['institute_invoices'] = $InvoiceModel->fetch_institute_invoices(decrypt_cipher($institute_id));
        return view('/pages/invoices/overview', $data);
    }
    /*******************************************************/

    // Added temporary function to run invoice jobs 
    public function run_job(string $job_date = "2022-05-04", int $flag = 0)
    {

        include_once(APPPATH . "Views/service_urls.php");
        if ($flag == 0) {
            $invoice_job_array = array($generateInvoicesUrl);
        } elseif ($flag == 1) {
            $invoice_job_array = array($sendPaymentRemindersUrl);
        } else {
            $invoice_job_array = array($suspendAccountsUrl);
        }
        // $generateInvoicesUrl, $sendPaymentRemindersUrl, $suspendAccountsUrl

        foreach ($invoice_job_array as $job_url) {
            $data = array(
                "startTime" => $job_date
            );
            $data_string = json_encode($data);
            echo $data_string;

            // Initiate curl
            $ch = curl_init();
            // Disable SSL verification
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            // Will return the response, if false it print the response
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            // Username and Password
            // curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);
            // POST ROW data
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data_string),
                'AuthToken: ' . decrypt_cipher($_SESSION['admin_token'])
            ));

            curl_setopt($ch, CURLOPT_URL, $job_url);
            // Execute
            $objTestString = curl_exec($ch);
            // Closing
            curl_close($ch);
            // echo $objTestString;
            $objTest = json_decode($objTestString, true);

            print_r($objTest);
            echo "<br/>";
            echo "=================================";
            echo "<br/>";
        }
    }

    public function print_invoice(string $public_id)
    {
        $data['title'] = "Institute Invoice";
        $InvoiceModel = new InvoiceModel();
        $invoice_id = $InvoiceModel->fetch_invoice_public_id($public_id);
        $data['invoice_data'] = $InvoiceModel->fetch_invoice_data($invoice_id);
        return view('/pages/invoices/print_invoice', $data);
    }



    public function edit_invoice_modal(string $invoice_id)
    {
        $data['title'] = "Update Invoice Details";
        $data['invoice_id'] = $invoice_id;
        $invoice_id = decrypt_cipher($invoice_id);
        $InvoiceModel = new InvoiceModel();
        $data['invoice_deatils'] = $InvoiceModel->fetch_invoice_data($invoice_id);
        return view('modals/invoices/update', $data);
    }


    public function update_invoice_details_submit()
    {
        $session = session();
        $redirect = $this->request->getVar('redirect');
        $result = $this->validate([
            'invoice_id' => ['label' => 'id', 'rules' => 'required|string|min_length[1]']
        ]);

        if (!$result) {
            $session->setFlashdata('toastr_error', 'Validation failed.');
            return redirect()->to(base_url($redirect))->withInput();
        } else {
            $data = $this->request->getVar();
            $data['invoice_id'] = decrypt_cipher($data['invoice_id']);
            $InvoiceModel = new InvoiceModel();
            if ($InvoiceModel->update_invoice_details($data)) {
                $session->setFlashdata('toastr_success', 'Invoice details updated successfully.');
            } else {
                $session->setFlashdata('toastr_error', 'Error in processing.');
            }
            return redirect()->to(base_url($redirect));
        }
    }
}
