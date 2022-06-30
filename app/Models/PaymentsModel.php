<?php

namespace App\Models;

use CodeIgniter\Model;

class PaymentsModel extends Model
{

    /*******************************************************/
    /**
     * To check if payment request id is found 
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function check_invoice_payment_request($payment_request_id)
    {
        $db = \Config\Database::connect();

        $query = $db->query("SELECT id FROM institute_payments WHERE payment_request_id = '$payment_request_id' ");
        return $query->getNumRows();
    }
    /*******************************************************/


    /*******************************************************/
    /**
     * To check if payment ID is alloted or not
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function check_payment_alloted(array $data)
    {
        $db = \Config\Database::connect();
        $payment_id = $data['payment_id'];
        $query = $db->query("SELECT id FROM institute_payments WHERE  payment_id='$payment_id' AND payment_id!='' ");
        return $query->getNumRows();
    }
    /*******************************************************/




    public function update_payment_info(array $data)
    {

        $db = \Config\Database::connect();
        $db->transStart();
        // array_map should walk through $array
        // used to replace empty values with NULL values
        $data = array_map(function ($value) {
            return $value === "" ? NULL : $value;
        }, $data);


        $update_data['payment_id'] = $data['payment_id'];
        $update_data['status'] = $data['status'];
        $update_data['payment_medium'] = $data['payment_medium'];

        $db->table('institute_payments')->update($update_data, ['payment_request_id' => $data['payment_request_id']]);

        // Update Invoice
        $update_invoice_data['payment_medium'] = "ONLINE PAYMENT";
        $update_invoice_data['status'] = "Credit";
        $update_invoice_data['payment_request_id'] = $data['payment_request_id'];
        $InvoiceModel = new InvoiceModel();
        if (!$InvoiceModel->update_invoice_details($update_invoice_data)) {
            // Log Message
            $log_info = [
                'payment_request_id' => $data['payment_request_id']
            ];
            log_message('error', 'Payment ID updation failed for payment request ID {payment_request_id}', $log_info);
            return false;
        }
        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            // Log Message
            $log_info = [
                'payment_request_id' => $data['payment_request_id']
            ];
            log_message('error', 'Payment ID updation failed for payment request ID {payment_request_id}', $log_info);


            return false;
        } else {
            return true;
        }
    }


    /*******************************************************/
    /**
     * Add New  Payment Entry For  OFFLINE Transaction
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function add_payment_info(array $data)
    {

        $db = \Config\Database::connect();
        $db->transStart();

        if (isset($data['institute_id'])) {
            $add_data['institute_id'] = sanitize_input($data['institute_id']);
        }

        if (isset($data['status'])) {
            $add_data['status'] = sanitize_input($data['status']);
        }

        if (isset($data['payment_medium'])) {
            $add_data['payment_medium'] = sanitize_input($data['payment_medium']);
        }

        if (isset($data['amount'])) {
            $add_data['amount'] = sanitize_input($data['amount']);
        }

        if (isset($data['payment_request_id'])) {
            $add_data['payment_request_id'] = sanitize_input($data['payment_request_id']);
        }

        $db->table('institute_payments')->insert($add_data);

        // Get inserted  ID
        $payment_id = $db->insertID();

        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            // Log Message
            $log_info =  [
                'username' =>  $this->session->get('username')
            ];
            log_message('error', 'User {username} tried to add payment offline but failed', $log_info);
            return false;
        } else {
            return $payment_id;
        }
    }
    /*******************************************************/
}
