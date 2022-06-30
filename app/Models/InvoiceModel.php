<?php

namespace App\Models;

use CodeIgniter\Model;

class InvoiceModel extends Model
{


    /**
     * Get total pending invoices
     *
     * @return integer
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function total_pending_invoices(array $requestData)
    {

        $db = \Config\Database::connect();
        $institute_check_condn = "";
        $institute_id = "";
        if (isset($requestData['institute_id']) && !empty($requestData['institute_id'])) {
            $institute_id = sanitize_input($requestData['institute_id']);
        }

        if ($institute_id != "") {
            $institute_check_condn = " AND edofox_invoices.institute_id = '$institute_id' ";
        }

        // Check Role
        $role_condition = "";
        if (isset($requestData['super_admin_role']) && isset($requestData['login_id'])) {
            $role = sanitize_input($requestData['super_admin_role']);
            $login_id = sanitize_input($requestData['login_id']);
            if ($role != 'Super Admin') {
                $role_condition = " AND institute.account_manager = '$login_id' ";
            }
        }

        $sql_total_invoices = "SELECT count(edofox_invoices.id) cnt
        FROM edofox_invoices 
        JOIN institute
        ON edofox_invoices.institute_id = institute.id  
        WHERE edofox_invoices.status = 'Pending'
        $institute_check_condn $role_condition";

        $query = $db->query($sql_total_invoices);
        $result = $query->getRowArray();
        return $result['cnt'];
    }
    /*******************************************************/



    /**
     * Get total due invoices
     *
     * @return integer
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function overdue_invoices(array $requestData)
    {

        $db = \Config\Database::connect();
        $institute_check_condn = "";
        $institute_id = "";
        if (isset($requestData['institute_id']) && !empty($requestData['institute_id'])) {
            $institute_id = sanitize_input($requestData['institute_id']);
        }

        if ($institute_id != "") {
            $institute_check_condn = " AND edofox_invoices.institute_id = '$institute_id' ";
        }

        // Check Role
        $role_condition = "";
        if (isset($requestData['super_admin_role']) && isset($requestData['login_id'])) {
            $role = sanitize_input($requestData['super_admin_role']);
            $login_id = sanitize_input($requestData['login_id']);
            if ($role != 'Super Admin') {
                $role_condition = " AND institute.account_manager = '$login_id' ";
            }
        }

        $sql_overdue_invoices = "SELECT count(edofox_invoices.id) cnt
        FROM edofox_invoices 
        JOIN institute
        ON edofox_invoices.institute_id = institute.id 
        WHERE edofox_invoices.status = 'Pending' 
        AND MONTH(due_date) = month(curdate())
        $institute_check_condn $role_condition";

        $query = $db->query($sql_overdue_invoices);
        $result = $query->getRowArray();
        return $result['cnt'];
    }
    /*******************************************************/


    /**
     * Get total invoice expired
     *
     * @return integer
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function invoices_expired($institute_id = "")
    {

        $db = \Config\Database::connect();
        $institute_check_condn = "";
        $institute_id = "";
        if (isset($requestData['institute_id']) && !empty($requestData['institute_id'])) {
            $institute_id = sanitize_input($requestData['institute_id']);
        }

        if ($institute_id != "") {
            $institute_check_condn = " AND edofox_invoices.institute_id = '$institute_id' ";
        }

        // Check Role
        $role_condition = "";
        if (isset($requestData['super_admin_role']) && isset($requestData['login_id'])) {
            $role = sanitize_input($requestData['super_admin_role']);
            $login_id = sanitize_input($requestData['login_id']);
            if ($role != 'Super Admin') {
                $role_condition = " AND institute.account_manager = '$login_id' ";
            }
        }

        $sql_expired_invoices = "SELECT count(edofox_invoices.id) cnt
        FROM edofox_invoices 
        JOIN institute
        ON edofox_invoices.institute_id = institute.id 
        WHERE edofox_invoices.expiry_date < curdate()
        AND edofox_invoices.status = 'Pending' 
        $institute_check_condn $role_condition";

        $query = $db->query($sql_expired_invoices);
        $result = $query->getRowArray();
        return $result['cnt'];
    }
    /*******************************************************/




    public function fetch_invoice_public_id($public_id)
    {
        $db = \Config\Database::connect();
        $sql = "SELECT id 
        FROM edofox_invoices where public_id = '$public_id' ";
        $query = $db->query($sql);
        $result = $query->getRowArray();
        return $result['id'];
    }




    /**
     * Get all invoices details - Json
     *
     * @return array
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function fetch_invoices_data(array $requestData)
    {
        // Check Role
        $role_condition = "";
        if (isset($requestData['super_admin_role']) && isset($requestData['login_id'])) {
            $role = sanitize_input($requestData['super_admin_role']);
            $login_id = sanitize_input($requestData['login_id']);
            if ($role != 'Super Admin') {
                $role_condition = " AND institute.account_manager = '$login_id' ";
            }
        }

        $sql = "SELECT edofox_invoices.*, institute.institute_name
        FROM edofox_invoices
        JOIN institute
        ON edofox_invoices.institute_id = institute.id  
        $role_condition ORDER BY institute.institute_name ASC";
        $total_filtered_limit_query = $this->db->query($sql);
        $filter_result = $total_filtered_limit_query->getResultArray();

        $data = array();
        $i = 1;
        foreach ($filter_result as $row) {

            $nestedData = array();
            $nestedData[] = $i;
            $nestedData[] = $row["invoice_ref"];
            $nestedData[] = $row["institute_name"];
            $nestedData[] = $row["amount_payable"];
            $nestedData[] = $row["discount_amount"];
            $nestedData[] = $row["payment_type"];
            $nestedData[] = $row["due_date"];
            $nestedData[] = $row["no_of_students"];


            if ($row["status"] == 'Paid') {
                $invoice_status = "bg-success text-white";
            }
            if ($row["status"] == 'Pending') {
                $invoice_status = "bg-warning text-dark";
            }
            if ($row["status"] == 'OnHold') {
                $invoice_status = "bg-secondary";
            }

            if ($row["status"] == 'Rejected') {
                $invoice_status = "bg-danger";
            }

            $invoice_status_data = htmlspecialchars("<label class='badge $invoice_status'>" . $row["status"] . "</label>");
            $nestedData[] = htmlspecialchars_decode("<div class='d-flex'>$invoice_status_data</div>");


            $update_invoice_details = "";
            $share_invoice_paynow_link = "";

            if (in_array("manage_invoices", session()->get('perms')) or in_array("all_super_admin_perms", session()->get('perms'))) {
                if ($row["status"] != 'Paid') {



                    $update_invoice_details =  htmlspecialchars("<button class='btn btn-sm' data-bs-toggle='tooltip' title='Edit Invoice' class='material_icon_custom_div' onclick=" . "show_edit_modal('modal_div','edit_invoice_modal','/invoices/edit_invoice_modal/" . encrypt_string($row['id']) . "');" . "><i class='material-icons'>edit</i></button>");


                    $share_invoice_paynow_link =  htmlspecialchars("<button class='btn btn-sm' data-bs-toggle='tooltip' title='Share Payment Link' class='material_icon_custom_div' onclick=" . "show_edit_modal('modal_div','share_payment_link','/payments/share_payment_link/" . $row['public_id'] . "');" . "><i class='material-icons'>share</i></button>");
                }
            }

            $print_invoice_link = base_url('/invoices/print_invoice/' . $row['public_id']);
            $print_invoice_btn = htmlspecialchars("<a class='btn btn-sm' data-bs-toggle='tooltip' title='Print Invoice'  target='_blank' href='$print_invoice_link'><span class='material-icons'>receipt_long</span></a>");

            $nestedData[] = htmlspecialchars_decode("<div class='d-flex'>$print_invoice_btn $share_invoice_paynow_link $update_invoice_details</div>");
            $data[] = $nestedData;
            $i++;
        }

        $json_data = $data;
        return $json_data;
    }
    /*******************************************************/


    public function fetch_invoice_data($invoice_id)
    {
        $db = \Config\Database::connect();
        $sql = "SELECT edofox_invoices.*,institute.institute_name,institute.max_students,institute.address as institute_address,IF(institute.gst_no IS NULL,'NA', institute.gst_no) as institute_gst_no
        FROM edofox_invoices
        LEFT JOIN institute
        ON edofox_invoices.institute_id = institute.id
        WHERE edofox_invoices.id = '$invoice_id' ";
        $query = $db->query($sql);
        $result = $query->getRowArray();
        return $result;
    }


    public function fetch_invoice_id($public_id)
    {
        $db = \Config\Database::connect();
        $sql = "SELECT edofox_invoices.id
        FROM edofox_invoices
        WHERE edofox_invoices.public_id = '$public_id' ";
        $query = $db->query($sql);
        $result = $query->getRowArray();
        return $result['id'];
    }


    /**
     * Get Institute Invoices
     *
     * @return array
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function fetch_institute_invoices(int $institute_id)
    {
        $db = \Config\Database::connect();
        $sql = "SELECT edofox_invoices.* 
        FROM edofox_invoices 
        WHERE edofox_invoices.institute_id = :institute_id: ";
        $query = $db->query($sql, [
            'institute_id' => sanitize_input($institute_id)
        ]);
        return $query->getResultArray();
    }
    /*******************************************************/



    /**
     * Check Invoice Payment Status 
     *
     * @return boolean
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function check_invoice_payment(int $invoice_id)
    {

        $db = \Config\Database::connect();
        $sql_query = "SELECT id
        FROM edofox_invoices 
        WHERE edofox_invoices.status = 'Paid' AND id  = '$invoice_id ' ";

        $query = $db->query($sql_query);
        $result = $query->getResultArray();
        if (!empty($result)) {
            return true;
        } else {
            return false;
        }
    }
    /*******************************************************/



    /*******************************************************/
    /**
     * Update Invoice Details 
     * @return void
     * @author Rushi B <rushikesh.badadale@mattersoft.xyz>
     */
    public function update_invoice_details(array $data)
    {

        $db = \Config\Database::connect();
        $db->transStart();

        // It is required to pass mode of payment in case of online 
        if (isset($data['payment_medium']) && $data['payment_medium'] == "ONLINE PAYMENT" && isset($data['status']) && $data['status'] == "Credit") {
            $payment_request_id = $data['payment_request_id'];
            $sql_query = "select id from institute_payments where payment_request_id = '$payment_request_id' ";
            $query = $this->db->query($sql_query);
            $result = $query->getRowArray();

            $update_data['last_updated'] = date('Y-m-d H:i:s');
            $update_data['status'] = "Paid";
            $db->table('edofox_invoices')->update($update_data, ['payment_trx_id' => $result['id']]);

            $payment_trx_id = $result['id'];
            $sql_query2 = "select id from edofox_invoices where payment_trx_id = '$payment_trx_id' ";
            $query2 = $this->db->query($sql_query2);
            $result2 = $query2->getRowArray();
            $invoice_id = $result2['id'];
            $invoice_result = $this->fetch_invoice_data($invoice_id);
            $subscription_id = $invoice_result['subscription_id'];
            // Update Institute Table 
            $InstituteSubscriptionsModel = new InstituteSubscriptionsModel();
            $InstituteSubscriptionsModel->enable_subscription($subscription_id);
            $subscription_data = $InstituteSubscriptionsModel->subscription_details($subscription_id);
            $institute_data['institute_id'] = $subscription_data['institute_id'];
            $institute_data['exam'] = $subscription_data['exam'];
            $institute_data['live'] = $subscription_data['live'];
            $institute_data['dlp'] = $subscription_data['dlp'];
            $institute_data['support'] = $subscription_data['support'];
            $InstituteModel = new InstituteModel();
            $InstituteModel->update_institute_module_feature($institute_data);
        } else {

            $date_now = date("Y-m-d"); // this format is string comparable
            $check_due_date_greater_than_today = 0;


            if (isset($data['due_date'])) {
                $update_data['due_date'] = sanitize_input($data['due_date']);
                if ($date_now < $update_data['due_date']) {
                    $check_due_date_greater_than_today = 1;
                }
            }


            if (isset($data['invoice_status'])) {
                $update_data['status'] = sanitize_input($data['invoice_status']);
            }

            if (isset($data['payment_trx_id'])) {
                $update_data['payment_trx_id'] = sanitize_input($data['payment_trx_id']);
            }

            // Check Offline Payment 
            if (isset($data['invoice_status']) && $data['invoice_status'] == 'Paid') {
                $invoice_data = $this->fetch_invoice_data($data['invoice_id']);
                $payment_data['institute_id'] =  $invoice_data['institute_id'];
                $payment_data['status'] =  'Credit';
                $payment_data['payment_medium'] = "OFFLINE PAYMENT";
                $payment_data['amount'] = $invoice_data['invoice_amount'];
                $PaymentsModel = new PaymentsModel();
                $payment_trx_id  = $PaymentsModel->add_payment_info($payment_data);
                if ($payment_trx_id) {
                    $update_data['payment_trx_id'] = $payment_trx_id;
                }
            }


            if ((isset($data['invoice_status']) && $data['invoice_status'] == 'Paid') or (isset($data['invoice_status']) && $data['invoice_status'] == 'Pending' && isset($data['due_date']) && $check_due_date_greater_than_today == 1)) {
                $invoice_result = $this->fetch_invoice_data($data['invoice_id']);
                $subscription_id = $invoice_result['subscription_id'];
                // Update Institute Table 
                $InstituteSubscriptionsModel = new InstituteSubscriptionsModel();
                $InstituteSubscriptionsModel->enable_subscription($subscription_id);
                $subscription_data = $InstituteSubscriptionsModel->subscription_details($subscription_id);
                $institute_data['institute_id'] = $subscription_data['institute_id'];
                $institute_data['exam'] = $subscription_data['exam'];
                $institute_data['live'] = $subscription_data['live'];
                $institute_data['dlp'] = $subscription_data['dlp'];
                $institute_data['support'] = $subscription_data['support'];
                $InstituteModel = new InstituteModel();
                $InstituteModel->update_institute_module_feature($institute_data);
            }


            $update_data['last_updated'] = date('Y-m-d H:i:s');
            $userType = session()->get('user_type');
            if ($userType == "super_admin") {
                $update_data['updated_by'] = decrypt_cipher(session()->get('login_id'));
            }
            $db->table('edofox_invoices')->update($update_data, ['id' => $data['invoice_id']]);
        }

        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            // generate an error... or use the log_message() function to log your error
            // Log Message
            $log_info = [
                'invoice_id' => $data['invoice_id']
            ];
            log_message('error', 'Invoice updation failed for ID {invoice_id}', $log_info);
            return false;
        } else {
            return true;
        }
    }
    /*******************************************************/
}
